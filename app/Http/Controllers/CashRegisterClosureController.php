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
        // Get physical cash balance (type = 'cash')
        $accounts = Account::where('type', 'cash')->where('is_active', true)->get();
        $systemBalance = $accounts->sum('current_balance');

        return view('cash_register_closures.create', compact('systemBalance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'closure_date' => 'required|date',
            'system_balance' => 'required|numeric',
            'bills' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $physicalBalance = 0;
        foreach ($request->bills as $value => $quantity) {
            $physicalBalance += ((float)$value * (int)$quantity);
        }

        $difference = $physicalBalance - $request->system_balance;

        $closure = CashRegisterClosure::create([
            'closure_date' => $request->closure_date,
            'system_balance' => $request->system_balance,
            'physical_balance' => $physicalBalance,
            'difference' => $difference,
            'notes' => $request->notes,
            'user_id' => auth()->id()
        ]);

        foreach ($request->bills as $value => $quantity) {
            if ($quantity > 0) {
                CashRegisterClosureBill::create([
                    'cash_register_closure_id' => $closure->id,
                    'bill_value' => $value,
                    'quantity' => $quantity
                ]);
            }
        }

        return redirect()->route('cash-register-closures.index')->with('success', 'Arqueo de caja guardado exitosamente.');
    }
}
