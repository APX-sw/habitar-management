<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegisterClosure;
use App\Models\CashRegisterClosureBill;
use App\Models\Account;

class CashRegisterClosureController extends Controller
{
    public function index()
    {
        $closures = CashRegisterClosure::with('user')->orderBy('closure_date', 'desc')->orderBy('id', 'desc')->get();
        return view('cash_register_closures.index', compact('closures'));
    }

    public function create()
    {
        $currentSession = CashRegisterClosure::where('status', 'open')->first();

        if (!$currentSession) {
            $lastClosure = CashRegisterClosure::where('status', 'closed')->latest('id')->first();
            $recommendedBalance = $lastClosure ? $lastClosure->physical_balance : 0;
            return view('cash_register_closures.open', compact('recommendedBalance'));
        }

        // Si hay sesión abierta, mostrar la vista de Cierre (Arqueo)
        // El balance esperado es: initial_balance + sum(ingresos) - sum(egresos) desde opened_at
        
        $accounts = Account::where('type', 'cash')->where('is_active', true)->pluck('id');
        
        $incomes = \App\Models\CashRegisterMovement::whereIn('account_id', $accounts)
            ->where('type', 'income')
            ->where('created_at', '>=', $currentSession->opened_at)
            ->sum('amount');
            
        $expenses = \App\Models\CashRegisterMovement::whereIn('account_id', $accounts)
            ->where('type', 'expense')
            ->where('created_at', '>=', $currentSession->opened_at)
            ->sum('amount');

        $systemBalance = $currentSession->initial_balance + $incomes - $expenses;

        return view('cash_register_closures.create', compact('systemBalance', 'currentSession'));
    }

    public function openSession(Request $request)
    {
        $request->validate([
            'initial_balance' => 'required|numeric|min:0'
        ]);

        $openSession = CashRegisterClosure::where('status', 'open')->first();
        if ($openSession) {
            return back()->with('error', 'Ya existe una sesión de caja abierta.');
        }

        CashRegisterClosure::create([
            'initial_balance' => $request->initial_balance,
            'opened_at' => now(),
            'status' => 'open',
            'user_id' => auth()->id()
        ]);

        return redirect()->route('cash-register-closures.create')->with('success', 'Sesión de caja iniciada.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'closure_date' => 'required|date',
            'system_balance' => 'required|numeric',
            'bills' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $currentSession = CashRegisterClosure::where('status', 'open')->firstOrFail();

        $physicalBalance = 0;
        foreach ($request->bills as $value => $quantity) {
            $physicalBalance += ((float)$value * (int)$quantity);
        }

        $difference = $physicalBalance - $request->system_balance;

        $currentSession->update([
            'closure_date' => $request->closure_date,
            'system_balance' => $request->system_balance,
            'physical_balance' => $physicalBalance,
            'difference' => $difference,
            'notes' => $request->notes,
            'status' => 'closed'
        ]);

        foreach ($request->bills as $value => $quantity) {
            if ($quantity > 0) {
                CashRegisterClosureBill::create([
                    'cash_register_closure_id' => $currentSession->id,
                    'bill_value' => $value,
                    'quantity' => $quantity
                ]);
            }
        }

        return redirect()->route('cash-register-closures.index')->with('success', 'Arqueo de caja y cierre de sesión guardado exitosamente.');
    }

    public function show(CashRegisterClosure $closure)
    {
        $closure->load(['user', 'bills']);

        $accounts = Account::where('type', 'cash')->where('is_active', true)->pluck('id');

        $movements = \App\Models\CashRegisterMovement::with('transactionCategory')
            ->whereIn('account_id', $accounts)
            ->where('created_at', '>=', $closure->opened_at)
            ->where('created_at', '<=', $closure->updated_at)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cash_register_closures.show', compact('closure', 'movements'));
    }
}
