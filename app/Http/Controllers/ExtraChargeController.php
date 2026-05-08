<?php

namespace App\Http\Controllers;

use App\Models\ExtraCharge;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExtraChargeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'billing_date' => 'required|date',
            'total_installments' => 'required|integer|min:1',
        ]);

        $totalInstallments = $validated['total_installments'];
        $amountPerInstallment = $validated['amount'] / $totalInstallments;
        $baseDate = Carbon::parse($validated['billing_date']);

        for ($i = 1; $i <= $totalInstallments; $i++) {
            $billingDate = $baseDate->copy()->addMonths($i - 1);
            
            $extra = ExtraCharge::create([
                'lease_id' => $validated['lease_id'],
                'description' => $validated['description'] . ($totalInstallments > 1 ? " (Cuota $i/$totalInstallments)" : ""),
                'amount' => $amountPerInstallment,
                'billing_date' => $billingDate,
                'installment_number' => $i,
                'total_installments' => $totalInstallments,
            ]);

            // Si ya existe un cobro generado para ese mes y ese contrato, se lo adosamos
            $collection = \App\Models\Collection::where('lease_id', $validated['lease_id'])
                ->where('month', $billingDate->month)
                ->where('year', $billingDate->year)
                ->first();

            if ($collection && $collection->status !== 'paid') {
                $collection->details()->create([
                    'type' => 'extra_charge',
                    'related_id' => $extra->id,
                    'name' => $extra->description,
                    'amount' => $extra->amount
                ]);
                $collection->update(['total_amount' => $collection->details()->sum('amount')]);
            }
        }

        return redirect()->back()->with('success', 'Cargo(s) generado(s) y adosados a los cobros correspondientes.');
    }
}
