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

        $query = CashRegisterMovement::with('account')->orderBy('movement_date', 'desc')->orderBy('id', 'desc');

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->paginate(20);

        return view('cash_register.index', compact('accounts', 'movements'));
    }
}
