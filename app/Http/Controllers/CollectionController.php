<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\CollectionDetail;
use App\Models\Lease;
use App\Models\ExtraCharge;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Collection::query();

        if ($request->filled('filter_year')) {
            $query->where('year', $request->filter_year);
        }

        if ($request->filled('filter_month')) {
            $query->where('month', $request->filter_month);
        }

        if ($request->filled('search')) {
            $query->whereHas('lease.tenant', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('lease.property', function($q) use ($request) {
                $q->where('location', 'like', '%' . $request->search . '%');
            });
        }

        $periods = $query->selectRaw('month, year, count(*) as total_collections, sum(total_amount) as sum_total, 
                                     sum(case when status = "paid" then 1 else 0 end) as paid_count,
                                     sum(case when status != "paid" then 1 else 0 end) as pending_count')
            ->groupBy('month', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $availableYears = Collection::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('collections.index', compact('periods', 'availableYears'));
    }

    public function create(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        // Buscamos contratos que estén activos en este periodo
        $leases = Lease::with(['property', 'tenant', 'fixedCharges'])
            ->where('is_active', true)
            ->where('start_date', '<=', Carbon::create($year, $month, 1)->endOfMonth())
            ->where('end_date', '>=', Carbon::create($year, $month, 1)->startOfMonth())
            ->get();

        // Pre-calculamos los montos y manejamos errores de índices faltantes
        foreach ($leases as $lease) {
            try {
                $lease->projected_rent = $lease->calculateRentForDate($month, $year);
                $lease->has_error = false;
            } catch (\Exception $e) {
                $lease->projected_rent = null;
                $lease->has_error = true;
                $lease->error_msg = $e->getMessage();
            }
        }

        return view('collections.create', compact('leases', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
            'lease_ids' => 'required|array',
        ]);

        try {
            foreach ($request->lease_ids as $leaseId) {
                $lease = Lease::find($leaseId);
                
                // Calculamos el alquiler para este mes (esto disparará la excepción si falta data)
                $rentAmount = $lease->calculateRentForDate($request->month, $request->year);

                $collection = Collection::firstOrCreate(
                    ['lease_id' => $leaseId, 'month' => $request->month, 'year' => $request->year],
                    ['rent_amount' => $rentAmount, 'status' => 'draft']
                );

                // Sincronizar detalles (tanto si es nuevo como si es borrador existente)
                if ($collection->status === 'draft') {
                    // 1. Asegurar Alquiler
                    $collection->details()->updateOrCreate(
                        ['type' => 'rent'],
                        ['name' => 'Alquiler Mensual', 'amount' => $rentAmount, 'destination' => 'owner']
                    );
                    $collection->update(['rent_amount' => $rentAmount]);

                    // 2. Cargos Extra (Cuotas de Honorarios, etc.)
                    $extras = ExtraCharge::where('lease_id', $leaseId)
                        ->whereMonth('billing_date', $request->month)
                        ->whereYear('billing_date', $request->year)
                        ->get();

                    foreach ($extras as $extra) {
                        $dest = 'owner';
                        $lowerName = strtolower($extra->description);
                        if (str_contains($lowerName, 'honorario') || str_contains($lowerName, 'comision') || str_contains($lowerName, 'comisión') || str_contains($lowerName, 'deposito') || str_contains($lowerName, 'depósito')) {
                            $dest = 'agency';
                        }
                        $collection->details()->updateOrCreate(
                            ['type' => 'extra_charge', 'related_id' => $extra->id],
                            ['name' => $extra->description, 'amount' => $extra->amount, 'destination' => $dest]
                        );
                    }

                    // 3. Conceptos Mensuales Recurrentes
                    foreach ($lease->fixedCharges as $fc) {
                        $dest = 'owner';
                        $lowerName = strtolower($fc->name);
                        if (str_contains($lowerName, 'honorario') || str_contains($lowerName, 'comision') || str_contains($lowerName, 'comisión') || str_contains($lowerName, 'deposito') || str_contains($lowerName, 'depósito')) {
                            $dest = 'agency';
                        }
                        $collection->details()->firstOrCreate(
                            ['type' => 'fixed_charge', 'related_id' => $fc->id],
                            ['name' => $fc->name, 'amount' => 0, 'destination' => $dest]
                        );
                    }
                    
                    // Recalcular total inicial
                    $collection->update(['total_amount' => $collection->details()->sum('amount')]);
                }
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('collections.show_period', ['month' => $request->month, 'year' => $request->year])
            ->with('success', 'Cobros generados como borrador.');
    }

    public function showPeriod(Request $request, $month, $year)
    {
        $query = Collection::with(['lease.property', 'lease.tenant', 'details'])
            ->where('month', $month)
            ->where('year', $year);

        // Búsqueda por Inquilino o Propiedad
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('lease.tenant', function($sq) use ($search) {
                    $sq->where('name', 'like', "%$search%");
                })->orWhereHas('lease.property', function($sq) use ($search) {
                    $sq->where('location', 'like', "%$search%");
                });
            });
        }

        // Filtro por Estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $collections = $query->latest()->get();

        return view('collections.period', compact('collections', 'month', 'year'));
    }

    public function show(Collection $collection)
    {
        $collection->load(['lease.property', 'lease.tenant', 'details', 'lease.fixedCharges', 'lease.extraCharges', 'payments.account']);
        $accounts = \App\Models\Account::where('is_active', true)->get();
        return view('collections.show', compact('collection', 'accounts'));
    }

    public function update(Request $request, Collection $collection)
    {
        // Actualizar montos manuales de cargos fijos
        if ($request->has('details')) {
            foreach ($request->details as $detailId => $amount) {
                CollectionDetail::where('id', $detailId)->update(['amount' => $amount]);
            }
        }

        // Recalcular total
        $total = $collection->details()->sum('amount');
        $collection->update([
            'total_amount' => $total,
            'status' => 'ready' // Marcar como listo una vez guardado
        ]);

        return back()->with('success', 'Cobro actualizado y listo.');
    }

    public function sendToTenant(Collection $collection)
    {
        if ($collection->status === 'paid') {
            return back()->with('error', 'No se puede enviar un cobro que ya ha sido pagado.');
        }

        $collection->load(['lease.property', 'lease.tenant', 'details']);

        $payload = $this->prepareWebhookPayload($collection);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(15)->post('https://n8n.dev.jfsdevs.com.ar/webhook/8dc83dd3-b602-47b1-a425-3842a3357159', $payload);
            
            if (!$response->successful()) {
                throw new \Exception("Error " . $response->status());
            }

            // Actualizar estado a "sent" si estaba en "ready"
            if ($collection->status === 'ready') {
                $collection->update(['status' => 'sent']);
            }

            return back()->with('success', 'Información enviada correctamente al inquilino.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error Webhook Collection: " . $e->getMessage());
            return back()->with('error', 'Error al enviar al webhook: ' . $e->getMessage());
        }
    }

    public function bulkSend(Request $request)
    {
        $request->validate([
            'collection_ids' => 'required|array',
        ]);

        $count = 0;
        foreach ($request->collection_ids as $id) {
            $collection = Collection::find($id);
            if ($collection && $collection->status === 'ready') {
                $payload = $this->prepareWebhookPayload($collection);
                try {
                        \Illuminate\Support\Facades\Http::post('https://n8n.dev.jfsdevs.com.ar/webhook/8dc83dd3-b602-47b1-a425-3842a3357159', $payload);
                    $collection->update(['status' => 'sent']);
                    $count++;
                } catch (\Exception $e) {
                    // Log error or continue
                }
            }
        }

        return back()->with('success', "$count cobros enviados a procesar correctamente.");
    }

    public function pay(Request $request, Collection $collection)
    {
        $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.account_id' => 'required|exists:accounts,id',
            'payments.*.payment_date' => 'required|date',
            'payments.*.notes' => 'nullable|string'
        ]);

        foreach ($request->payments as $pData) {
            $payment = $collection->payments()->create($pData);

            // Generar movimiento de caja (Ingreso)
            $payment->movement()->create([
                'account_id' => $pData['account_id'],
                'type' => 'income',
                'amount' => $pData['amount'],
                'description' => 'Cobro Alquiler: ' . $collection->lease->property->location . ' (' . $collection->month . '/' . $collection->year . ')',
                'movement_date' => $pData['payment_date'],
            ]);
        }

        // Recalcular estado basado en el total pagado
        $totalPaid = $collection->total_paid;
        if ($totalPaid >= $collection->total_amount) {
            $collection->update([
                'status' => 'paid',
                'payment_date' => now()
            ]);
        } else {
            $collection->update(['status' => 'partial']);
        }

        return back()->with('success', 'Pago(s) registrado(s) correctamente.');
    }

    private function prepareWebhookPayload(Collection $collection)
    {
        return [
            'collection_id' => $collection->id,
            'period' => \Carbon\Carbon::createFromDate($collection->year, $collection->month, 1)->translatedFormat('F Y'),
            'property' => $collection->lease->property->location,
            'tenant' => [
                'name' => $collection->lease->tenant->name,
                'email' => $collection->lease->tenant->email,
            ],
            'total_amount' => $collection->total_amount,
            'details' => $collection->details->map(function($detail) {
                return [
                    'description' => $detail->name,
                    'amount' => $detail->amount,
                    'type' => $detail->type
                ];
            })->toArray()
        ];
    }
}
