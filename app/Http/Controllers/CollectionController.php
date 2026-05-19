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
                        ['name' => 'Alquiler Mensual', 'amount' => $rentAmount, 'destination' => 'owner', 'transaction_category_id' => 1] // 1: Alquileres
                    );
                    $collection->update(['rent_amount' => $rentAmount]);

                    // 2. Cargos Extra (Cuotas de Honorarios, etc.)
                    $extras = ExtraCharge::where('lease_id', $leaseId)
                        ->whereMonth('billing_date', $request->month)
                        ->whereYear('billing_date', $request->year)
                        ->get();

                    foreach ($extras as $extra) {
                        $dest = 'owner';
                        $catId = $extra->transaction_category_id;
                        $lowerName = strtolower($extra->description);
                        
                        if (!$catId) {
                            if (str_contains($lowerName, 'honorario') || str_contains($lowerName, 'comision') || str_contains($lowerName, 'comisión')) {
                                $dest = 'agency';
                                $catId = 2; // Honorarios Inmobiliarios
                            } elseif (str_contains($lowerName, 'deposito') || str_contains($lowerName, 'depósito')) {
                                $dest = 'agency';
                                $catId = 3; // Depósitos en Garantía
                            } elseif (str_contains($lowerName, 'multa') || str_contains($lowerName, 'interes') || str_contains($lowerName, 'interés')) {
                                $dest = 'agency';
                                $catId = 4; // Intereses y Recargos
                            } else {
                                $recurrentCat = \App\Models\TransactionCategory::where('name', 'Gastos Recurrentes')->first();
                                $catId = $recurrentCat ? $recurrentCat->id : 8; // Default to Gastos Recurrentes
                            }
                        }
                        
                        $collection->details()->updateOrCreate(
                            ['type' => 'extra_charge', 'related_id' => $extra->id],
                            ['name' => $extra->description, 'amount' => $extra->amount, 'destination' => $dest, 'transaction_category_id' => $catId]
                        );
                    }

                    // 3. Conceptos Mensuales Recurrentes
                    foreach ($lease->fixedCharges as $fc) {
                        $dest = $fc->is_paid_by_agency ? 'agency' : 'owner';
                        $catId = $fc->transaction_category_id;
                        $lowerName = strtolower($fc->name);
                        
                        if (!$catId) {
                            if (str_contains($lowerName, 'honorario') || str_contains($lowerName, 'comision') || str_contains($lowerName, 'comisión')) {
                                $dest = 'agency';
                                $catId = 2; // Honorarios Inmobiliarios
                            } elseif (str_contains($lowerName, 'deposito') || str_contains($lowerName, 'depósito')) {
                                $dest = 'agency';
                                $catId = 3; // Depósitos en Garantía
                            } else {
                                $recurrentCat = \App\Models\TransactionCategory::where('name', 'Gastos Recurrentes')->first();
                                $catId = $recurrentCat ? $recurrentCat->id : 8; // Default to Gastos Recurrentes
                            }
                        }
                        
                        $collection->details()->firstOrCreate(
                            ['type' => 'fixed_charge', 'related_id' => $fc->id],
                            ['name' => $fc->name, 'amount' => 0, 'destination' => $dest, 'transaction_category_id' => $catId]
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
        $categories = \App\Models\TransactionCategory::all();
        return view('collections.show', compact('collection', 'accounts', 'categories'));
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
        if ($collection->status === 'draft' || $collection->status === 'incompleto') {
            return back()->with('error', 'No se puede enviar un cobro incompleto. Debes cargar y guardar los montos para marcarlo como listo para cobrar.');
        }

        if ($collection->status === 'paid') {
            return back()->with('error', 'No se puede enviar un cobro que ya ha sido pagado.');
        }

        $collection->load(['lease.property', 'lease.tenant', 'details']);

        $payload = $this->prepareWebhookPayload($collection);

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(15)->post('https://n8n.dev.jfsdevs.com.ar/webhook/8dc83dd3-b602-47b1-a425-3842a3357159', $payload);
            
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
                        \Illuminate\Support\Facades\Http::withoutVerifying()->post('https://n8n.dev.jfsdevs.com.ar/webhook/8dc83dd3-b602-47b1-a425-3842a3357159', $payload);
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
            'payments.*.notes' => 'nullable|string',
            'payments.*.transferred_to_owner' => 'nullable|boolean'
        ]);

        foreach ($request->payments as $pData) {
            $pData['transferred_to_owner'] = !empty($pData['transferred_to_owner']);
            $payment = $collection->payments()->create($pData);
            
            $paymentAmount = $pData['amount'];
            $totalDebt = $collection->total_amount;
            
            // Unificamos el cobro de caja en un único movimiento (sea directo al propietario o normal)
            if (isset($pData['transferred_to_owner']) && $pData['transferred_to_owner']) {
                // Pago directo al propietario: Generar UN SOLO INGRESO TRANSITORIO y UN SOLO EGRESO
                $transferCategory = \App\Models\TransactionCategory::where('name', 'Transferencia al Propietario (Directa)')->first();
                if (!$transferCategory) {
                    $transferCategory = \App\Models\TransactionCategory::create(['name' => 'Transferencia al Propietario (Directa)', 'type' => 'expense', 'description' => 'Transferencia directa al propietario desde el recibo de cobro']);
                }
                
                // INGRESO TRANSITORIO
                $payment->movement()->create([
                    'account_id' => $pData['account_id'],
                    'type' => 'income',
                    'amount' => $paymentAmount,
                    'description' => "Ingreso por Pago Directo a Propietario - {$collection->lease->property->location}",
                    'movement_date' => Carbon::parse($pData['payment_date'])->setTimeFrom(now()),
                    'transaction_category_id' => $transferCategory->id
                ]);

                // EGRESO AUTOMÁTICO
                \App\Models\CashRegisterMovement::create([
                    'type' => 'expense',
                    'amount' => $paymentAmount,
                    'movement_date' => Carbon::parse($pData['payment_date'])->setTimeFrom(now()),
                    'description' => "Egreso por Pago Directo a Propietario - {$collection->lease->property->location}",
                    'account_id' => $pData['account_id'],
                    'transaction_category_id' => $transferCategory->id,
                    'user_id' => auth()->id(),
                    'related_id' => $payment->id,
                    'related_type' => \App\Models\CollectionPayment::class,
                ]);

            } else {
                // INGRESO ORDINARIO (Unificado)
                $payment->movement()->create([
                    'account_id' => $pData['account_id'],
                    'type' => 'income',
                    'amount' => $paymentAmount,
                    'description' => "Cobro Alquiler e Impuestos - {$collection->lease->property->location} (Cobro #{$collection->id})",
                    'movement_date' => Carbon::parse($pData['payment_date'])->setTimeFrom(now()),
                    'transaction_category_id' => 1 // Categoría: Alquileres
                ]);
            }
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

    public function paymentReceipt(Collection $collection, \App\Models\CollectionPayment $payment)
    {
        // Verificar que el pago pertenezca a la colección
        if ($payment->collection_id !== $collection->id) {
            abort(404);
        }

        return view('collections.receipt', compact('collection', 'payment'));
    }

    public function sendReceiptToTenant(Collection $collection, \App\Models\CollectionPayment $payment)
    {
        if ($payment->collection_id !== $collection->id) {
            abort(404);
        }

        $collection->load(['lease.property', 'lease.tenant', 'details']);

        $whatsapp = \App\Models\AgencySetting::get('whatsapp_number');
        $agencyEmail = \App\Models\AgencySetting::get('agency_email', 'contacto@habitar.com.ar');
        $agencyAddress = \App\Models\AgencySetting::get('agency_address', 'Av. Belgrano (N) 450, Santiago del Estero');
        $defaultAccount = \App\Models\AgencyBankAccount::where('is_active', true)->first();

        $payload = [
            'payment_id' => $payment->id,
            'receipt_number' => 'REC-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
            'collection_id' => $collection->id,
            'period' => \Carbon\Carbon::createFromDate($collection->year, $collection->month, 1)->translatedFormat('F Y'),
            'payment_date' => \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y'),
            'property' => $collection->lease->property->location,
            'tenant' => [
                'name' => $collection->lease->tenant->name,
                'email' => $collection->lease->tenant->email,
            ],
            'amount_paid' => $payment->amount,
            'payment_method' => ($payment->account->type ?? '') === 'cash' 
                ? 'Efectivo' 
                : (($payment->account->type ?? '') === 'bank' ? 'Transferencia Bancaria' : ($payment->account->name ?? 'N/A')),
            'notes' => $payment->notes,
            'details' => $collection->details->map(function($detail) {
                return [
                    'description' => $detail->name,
                    'amount' => $detail->amount,
                    'type' => $detail->type
                ];
            })->toArray(),
            'agency_bank_account' => $defaultAccount ? [
                'holder_name' => $defaultAccount->holder_name,
                'bank_entity' => $defaultAccount->bank_entity,
                'cbu' => $defaultAccount->cbu,
                'alias' => $defaultAccount->alias,
            ] : null,
            'contact' => [
                'whatsapp' => $whatsapp,
                'whatsapp_url' => $whatsapp ? "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsapp) : null,
                'agency_email' => $agencyEmail,
                'agency_address' => $agencyAddress
            ],
            'n8n_code' => \App\Services\N8nCodeService::getTenantReceiptCode(),
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->timeout(15)
                ->post('https://n8n.dev.jfsdevs.com.ar/webhook/5619b6ed-baa5-4ee8-83b0-d72c57deadd6', $payload);
            
            if (!$response->successful()) {
                throw new \Exception("Error " . $response->status());
            }

            return back()->with('success', 'Recibo de cobro enviado correctamente al inquilino por email.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error Webhook Payment Receipt: " . $e->getMessage());
            return back()->with('error', 'Error al enviar el recibo al webhook: ' . $e->getMessage());
        }
    }

    public function transferPaymentToOwner(Collection $collection, \App\Models\CollectionPayment $payment)
    {
        if ($payment->transferred_to_owner) {
            return back()->with('error', 'Este pago ya fue transferido al propietario.');
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $collection) {
                // 1. Marcar el pago como transferido
                $payment->update(['transferred_to_owner' => true]);

                // 2. Generar movimiento de salida (Egreso) en Caja
                $transferCategory = \App\Models\TransactionCategory::where('name', 'Transferencia al Propietario (Directa)')->first();
                if (!$transferCategory) {
                    $transferCategory = \App\Models\TransactionCategory::create(['name' => 'Transferencia al Propietario (Directa)', 'type' => 'expense', 'description' => 'Transferencia directa al propietario desde el recibo de cobro']);
                }

                \App\Models\CashRegisterMovement::create([
                    'type' => 'expense',
                    'amount' => $payment->amount, // Sale todo el monto del pago
                    'date' => now(),
                    'description' => "Pago Directo a Propietario - {$collection->lease->property->location} (Cobro #{$collection->id})",
                    'account_id' => $payment->account_id, // Sale de la misma cuenta donde entró
                    'transaction_category_id' => $transferCategory->id,
                    'user_id' => auth()->id(),
                    'related_id' => $payment->id,
                    'related_type' => \App\Models\CollectionPayment::class,
                ]);
            });

            return back()->with('success', 'El pago ha sido marcado como transferido y se generó el movimiento de salida en caja automáticamente.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error transferring payment: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al procesar la transferencia: ' . $e->getMessage());
        }
    }

    private function prepareWebhookPayload(Collection $collection)
    {
        $defaultAccount = \App\Models\AgencyBankAccount::where('is_active', true)->first();
        $whatsapp = \App\Models\AgencySetting::get('whatsapp_number');

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
            })->toArray(),
            'agency_bank_account' => $defaultAccount ? [
                'holder_name' => $defaultAccount->holder_name,
                'bank_entity' => $defaultAccount->bank_entity,
                'cbu' => $defaultAccount->cbu,
                'alias' => $defaultAccount->alias,
            ] : null,
            'contact' => [
                'whatsapp' => $whatsapp,
                'whatsapp_url' => $whatsapp ? "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsapp) : null
            ],
            'n8n_code' => \App\Services\N8nCodeService::getTenantCollectionNotificationCode(),
        ];
    }
}
