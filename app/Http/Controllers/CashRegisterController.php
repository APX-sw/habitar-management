<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CashRegisterMovement;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::with(['movements' => function($q) {
            $q->latest('movement_date')->limit(5);
        }])->get();

        $query = CashRegisterMovement::with(['account', 'transactionCategory'])->orderBy('movement_date', 'desc')->orderBy('id', 'desc');

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('transaction_category_id')) {
            $query->where('transaction_category_id', $request->transaction_category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        $movements = $query->paginate(15);
        $categories = \App\Models\TransactionCategory::orderBy('name')->get();

        $totalBalance = $accounts->where('type', '!=', 'habitar_fund')->sum(function($account) {
            return $account->current_balance;
        });

        $employees = \App\Models\Employee::all();

        $deletedMovements = \App\Models\DeletedMovement::with(['account', 'user'])->latest()->get();
        $deletedMovementsFormatted = $deletedMovements->map(function($del) {
            return [
                'id' => $del->id,
                'created_at_raw' => $del->created_at->toIso8601String(),
                'created_at_formatted' => \Carbon\Carbon::parse($del->created_at)->format('d/m/Y H:i') . ' hs',
                'movement_date_formatted' => \Carbon\Carbon::parse($del->movement_date)->format('d/m/Y'),
                'account_name' => $del->account->name,
                'description' => $del->description,
                'amount' => (float)$del->amount,
                'user_name' => $del->user ? $del->user->name : 'Administrador',
                'reason' => $del->reason
            ];
        });

        if ($request->ajax()) {
            return view('cash_register._movements_table', compact('movements'))->render();
        }

        return view('cash_register.index', compact('accounts', 'movements', 'totalBalance', 'categories', 'deletedMovements', 'deletedMovementsFormatted', 'employees'));
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'source_account_id' => 'required|exists:accounts,id',
            'destination_account_id' => 'required|exists:accounts,id|different:source_account_id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        \DB::transaction(function() use ($request) {
            $source = Account::find($request->source_account_id);
            $dest = Account::find($request->destination_account_id);

            // Salida de la cuenta origen
            CashRegisterMovement::create([
                'account_id' => $request->source_account_id,
                'type' => 'expense',
                'amount' => $request->amount,
                'movement_date' => now(),
                'description' => "Transferencia enviada a " . $dest->name . ". " . ($request->notes ?? ''),
                'transaction_category_id' => 9 // Transferencia
            ]);

            // Ingreso en la cuenta destino
            CashRegisterMovement::create([
                'account_id' => $request->destination_account_id,
                'type' => 'income',
                'amount' => $request->amount,
                'movement_date' => now(),
                'description' => "Transferencia recibida de " . $source->name . ". " . ($request->notes ?? ''),
                'transaction_category_id' => 9 // Transferencia
            ]);
        });

        return back()->with('success', 'Transferencia realizada con éxito.');
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'adjustment_type' => 'required|in:new_balance,delta_income,delta_expense',
            'amount' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $account = Account::find($request->account_id);
        $amount = $request->amount;
        $type = 'income';
        $movementAmount = $amount;

        if ($request->adjustment_type === 'new_balance') {
            $currentBalance = $account->current_balance;
            $diff = $amount - $currentBalance;
            if (abs($diff) < 0.01) return back()->with('info', 'El saldo ya es el indicado.');
            
            $type = $diff > 0 ? 'income' : 'expense';
            $movementAmount = abs($diff);
        } elseif ($request->adjustment_type === 'delta_expense') {
            $type = 'expense';
        }

        CashRegisterMovement::create([
            'account_id' => $request->account_id,
            'type' => $type,
            'amount' => $movementAmount,
            'movement_date' => now(),
            'description' => "Ajuste de Saldo: " . ($request->notes ?? 'Ajuste manual'),
            'transaction_category_id' => 10 // Ajuste
        ]);

        return back()->with('success', 'Saldo ajustado correctamente.');
    }
}
