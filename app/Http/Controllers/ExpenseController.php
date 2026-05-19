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
        $query = Expense::with(['property', 'account', 'transactionCategory', 'documents'])->orderBy('date', 'desc');

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
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $expense = Expense::create([
            'property_id' => $request->property_id,
            'account_id' => $request->account_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_category_id' => $request->transaction_category_id,
            'is_paid' => true,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('expenses/' . $expense->id, 'public');
                $expense->documents()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

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
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $expense->update($request->only('description'));

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('expenses/' . $expense->id, 'public');
                $expense->documents()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        return back()->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(Expense $expense)
    {
        \DB::transaction(function() use ($expense) {
            // Find related cash movement
            $movement = $expense->movement;
            if ($movement) {
                // Log deleted movement to audit trail
                \App\Models\DeletedMovement::create([
                    'original_movement_id' => $movement->id,
                    'account_id' => $movement->account_id,
                    'type' => $movement->type,
                    'amount' => $movement->amount,
                    'description' => $movement->description . ' (Gasto eliminado permanentemente)',
                    'movement_date' => $movement->movement_date,
                    'transaction_category_id' => $movement->transaction_category_id,
                    'deleted_by_user_id' => auth()->id(),
                    'reason' => 'Eliminación voluntaria de gasto por error de carga.',
                ]);

                // Delete cash movement (this automatically restores the account balance)
                $movement->delete();
            }

            // Delete associated file attachments physically from storage
            foreach ($expense->documents as $doc) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->path);
                $doc->delete();
            }

            // Delete the expense itself
            $expense->delete();
        });

        return redirect()->route('expenses.index')->with('success', 'Gasto eliminado correctamente y saldo de caja restablecido.');
    }
}
