<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Account;
use App\Models\Property;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['property', 'account'])->orderBy('date', 'desc');

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $expenses = $query->paginate(20);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $properties = Property::all();
        $accounts = Account::where('is_active', true)->get();
        return view('expenses.create', compact('properties', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $expense = Expense::create([
            'property_id' => $request->property_id,
            'account_id' => $request->account_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'description' => $request->description,
            'is_paid' => true, // Assuming it's paid immediately from the account
        ]);

        // Generate movement
        $expense->movement()->create([
            'account_id' => $request->account_id,
            'type' => 'expense',
            'amount' => $request->amount,
            'description' => 'Gasto: ' . $request->description,
            'movement_date' => $request->date,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Gasto registrado correctamente.');
    }
}
