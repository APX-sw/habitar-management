<?php

namespace App\Http\Controllers;

use App\Models\FixedCharge;
use Illuminate\Http\Request;

class FixedChargeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'name' => 'required|string|max:255',
            'transaction_category_id' => 'nullable|exists:transaction_categories,id',
            'is_paid_by_agency' => 'nullable|boolean',
        ]);

        FixedCharge::create([
            'lease_id' => $request->lease_id,
            'name' => $request->name,
            'amount' => 0, // Siempre 0 por defecto como pidió el usuario
            'transaction_category_id' => $request->transaction_category_id,
            'is_paid_by_agency' => $request->boolean('is_paid_by_agency'),
        ]);

        return back()->with('success', 'Concepto mensual añadido.');
    }

    public function destroy(FixedCharge $fixedCharge)
    {
        $fixedCharge->delete();
        return back()->with('success', 'Concepto mensual eliminado.');
    }
}
