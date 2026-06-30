<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::orderBy('name')->get();
        return view('settings.accounts', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,other',
        ]);

        Account::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_active' => true
        ]);

        return back()->with('success', 'Cuenta creada correctamente.');
    }

    public function toggleActive(Account $account)
    {
        $account->update(['is_active' => !$account->is_active]);
        return back()->with('success', 'Estado de la cuenta actualizado.');
    }
}
