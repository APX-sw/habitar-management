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
        $query = Expense::with(['property', 'account', 'transactionCategory'])->orderBy('date', 'desc');

        if ($request->filled('property_id')) {
            if ($request->property_id === 'none') {
                $query->whereNull('property_id');
            } else {
                $query->where('property_id', $request->property_id);
            }
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('category_id')) {
            $query->where('transaction_category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $expenses = $query->paginate(20)->withQueryString();

        $properties = Property::orderBy('location')->get();
        $accounts = Account::orderBy('name')->get();
        $categories = \App\Models\TransactionCategory::where('type', 'expense')->orWhere('type', 'both')->orderBy('name')->get();

        if ($request->ajax()) {
            return view('expenses.partials.table', compact('expenses'))->render();
        }

        return view('expenses.index', compact('expenses', 'properties', 'accounts', 'categories'));
    }

    public function create()
    {
        $properties = Property::all();
        $accounts = Account::where('is_active', true)->get();
        $categories = \App\Models\TransactionCategory::where('type', 'expense')->orWhere('type', 'both')->get();
        return view('expenses.create', compact('properties', 'accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'account_id' => 'required|exists:accounts,id',
            'transaction_category_id' => 'required|exists:transaction_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('expenses', 'public');
        }

        $expense = Expense::create([
            'property_id' => $request->property_id,
            'account_id' => $request->account_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'description' => $request->description,
            'attachment_path' => $attachmentPath,
            'transaction_category_id' => $request->transaction_category_id,
            'is_paid' => true,
        ]);

        // Generate movement
        $expense->movement()->create([
            'account_id' => $request->account_id,
            'type' => 'expense',
            'amount' => $request->amount,
            'description' => 'Gasto: ' . ($request->description ?? $expense->transactionCategory->name),
            'movement_date' => \Carbon\Carbon::parse($request->date)->setTimeFrom(now()),
            'transaction_category_id' => $request->transaction_category_id
        ]);

        return redirect()->route('expenses.index')->with('success', 'Gasto registrado correctamente.');
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only('description');

        if ($request->hasFile('attachment')) {
            if ($expense->attachment_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($expense->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->update($data);

        return back()->with('success', 'Gasto actualizado correctamente.');
    }
}
