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
        ]);

        FixedCharge::create([
            'lease_id' => $request->lease_id,
            'name' => $request->name,
            'amount' => 0, // Siempre 0 por defecto como pidió el usuario
        ]);

        return back()->with('success', 'Concepto mensual añadido.');
    }

    public function destroy(FixedCharge $fixedCharge)
    {
        $fixedCharge->delete();
        return back()->with('success', 'Concepto mensual eliminado.');
    }
}
