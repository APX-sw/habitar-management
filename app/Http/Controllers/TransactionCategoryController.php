<?php

namespace App\Http\Controllers;

use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        $categories = TransactionCategory::orderBy('name')->get();
        return view('settings.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense,both',
        ]);

        TransactionCategory::create(array_merge($validated, [
            'is_system' => false,
            'group' => 'other'
        ]));

        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function destroy(TransactionCategory $category)
    {
        if ($category->is_system) {
            return back()->with('error', 'No se pueden eliminar categorías del sistema.');
        }

        if (\App\Models\CashRegisterMovement::where('transaction_category_id', $category->id)->exists()) {
            return back()->with('error', 'No se puede eliminar la categoría: tiene movimientos asociados.');
        }

        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}
