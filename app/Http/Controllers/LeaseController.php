<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Lease::with(['property', 'tenant']);

        // Búsqueda por Propiedad o Inquilino
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('property', function($sq) use ($search) {
                    $sq->where('location', 'like', "%$search%");
                })->orWhereHas('tenant', function($sq) use ($search) {
                    $sq->where('name', 'like', "%$search%");
                });
            });
        }

        // Filtro por Mes de Vencimiento
        if ($request->filled('expiry_month')) {
            $query->whereRaw('MONTH(end_date) = ?', [$request->expiry_month]);
        }

        // Filtro por Precio Base (Min/Max)
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // Filtro por Estado (Lógica de Fechas)
        if ($request->filled('status')) {
            $today = now();
            $nearThreshold = now()->addMonths(2);

            if ($request->status === 'expired') {
                $query->where('end_date', '<', $today)->where('renewal_status', '!=', 'terminated');
            } elseif ($request->status === 'near') {
                $query->whereBetween('end_date', [$today, $nearThreshold])->where('renewal_status', '!=', 'terminated');
            } elseif ($request->status === 'active') {
                $query->where('end_date', '>', $nearThreshold)->where('renewal_status', '!=', 'terminated');
            } elseif ($request->status === 'terminated') {
                $query->where('renewal_status', 'terminated');
            }
        }

        $leases = $query->latest()->paginate(15)->withQueryString();
        
        return view('leases.index', compact('leases'));
    }

    public function create()
    {
        $properties = Property::with(['type', 'city'])->whereDoesntHave('activeLease')->get();
        $tenants = Tenant::all();
        $indexTypes = \App\Models\IndexType::all();
        return view('leases.create', compact('properties', 'tenants', 'indexTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'tenant_id' => 'required|exists:tenants,id',
            'guarantor_name' => 'nullable|string',
            'guarantor_id_number' => 'nullable|string',
            'guarantor_email' => 'nullable|email',
            'guarantor_address' => 'nullable|string',
            'guarantor_phone' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'base_price' => 'required|numeric|min:0',
            'security_deposit_amount' => 'nullable|numeric|min:0',
            'agency_fee_amount' => 'nullable|numeric|min:0',
            'update_type' => 'required|in:fixed,indexed',
            'update_frequency_months' => 'required|integer|min:1',
            'update_value' => 'nullable|numeric',
            'index_type_id' => 'nullable|exists:index_types,id',
            'fixed_charges' => 'nullable|array',
            'fixed_charges.*.name' => 'nullable|string',
            'fixed_charges.*.amount' => 'nullable|numeric|min:0',
            'initial_fee_installments' => 'nullable|integer|min:1',
        ]);

        $lease = Lease::create(array_merge($validated, [
            'security_deposit_amount' => $validated['security_deposit_amount'] ?? 0,
            'agency_fee_amount' => $validated['agency_fee_amount'] ?? 0,
        ]));

        // Generar Cargos Fijos (Conceptos) - Solo si tienen nombre
        if ($request->has('fixed_charges')) {
            foreach ($request->fixed_charges as $charge) {
                if (!empty($charge['name'])) {
                    $lease->fixedCharges()->create([
                        'name' => $charge['name'],
                        'amount' => $charge['amount'] ?? 0,
                        'is_paid_by_agency' => isset($charge['is_paid_by_agency']) ? (bool)$charge['is_paid_by_agency'] : false,
                    ]);
                }
            }
        }

        // Generar Depósito en Garantía (Cargo Inicial único)
        if ($lease->security_deposit_amount > 0) {
            $lease->extraCharges()->create([
                'description' => "Depósito en Garantía",
                'amount' => $lease->security_deposit_amount,
                'billing_date' => Carbon::parse($lease->start_date),
                'installment_number' => 1,
                'total_installments' => 1,
                'is_paid' => false,
            ]);
        }

        // Generar Honorarios Inmobiliaria (Cargo Inicial en cuotas)
        if ($lease->agency_fee_amount > 0) {
            $total = $lease->agency_fee_amount;
            $installments = $request->initial_fee_installments ?? 1;
            $amountPerInstallment = round($total / $installments, 2);
            $startDate = Carbon::parse($lease->start_date);

            for ($i = 1; $i <= $installments; $i++) {
                $lease->extraCharges()->create([
                    'description' => "Honorarios Inmobiliaria (Cuota $i/$installments)",
                    'amount' => ($i == $installments) ? ($total - ($amountPerInstallment * ($installments - 1))) : $amountPerInstallment,
                    'billing_date' => $startDate->copy()->addMonths($i - 1),
                    'installment_number' => $i,
                    'total_installments' => $installments,
                    'is_paid' => false,
                ]);
            }
        }

        return redirect()->route('leases.index')->with('success', 'Contrato generado correctamente con su plan de pagos inicial.');
    }

    public function showRenewalForm(Lease $lease)
    {
        $lease->load(['property', 'tenant', 'fixedCharges']);
        $indexTypes = \App\Models\IndexType::all();
        
        // El nuevo depósito sugerido es el nuevo precio base (que se editará en el form)
        // Pero ya calculamos la base para la vista
        return view('leases.renew', compact('lease', 'indexTypes'));
    }

    public function storeRenewal(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'base_price' => 'required|numeric|min:0',
            'update_type' => 'required|in:fixed,indexed',
            'update_frequency_months' => 'required|integer|min:1',
            'update_value' => 'nullable|numeric',
            'index_type_id' => 'nullable|exists:index_types,id',
            'property_review_status' => 'required|boolean',
            'property_review_notes' => 'nullable|string',
            'agency_fee_amount' => 'nullable|numeric|min:0',
            'initial_fee_installments' => 'nullable|integer|min:1',
            'security_deposit_diff' => 'nullable|numeric|min:0',
        ]);

        // 1. Calcular nuevo depósito total guardado
        $oldDeposit = $lease->security_deposit_amount ?? 0;
        $depositDiff = $validated['security_deposit_diff'] ?? 0;
        $newTotalDeposit = $oldDeposit + $depositDiff;

        // 2. Crear el nuevo contrato
        $newLease = Lease::create(array_merge($validated, [
            'property_id' => $lease->property_id,
            'tenant_id' => $lease->tenant_id,
            'guarantor_name' => $lease->guarantor_name,
            'guarantor_id_number' => $lease->guarantor_id_number,
            'guarantor_email' => $lease->guarantor_email,
            'guarantor_address' => $lease->guarantor_address,
            'guarantor_phone' => $lease->guarantor_phone,
            'security_deposit_amount' => $newTotalDeposit,
            'agency_fee_amount' => $validated['agency_fee_amount'] ?? 0,
            'parent_lease_id' => $lease->id,
            'renewal_status' => 'renewed',
            'is_active' => true
        ]));

        // 3. Heredar cargos fijos
        foreach ($lease->fixedCharges as $charge) {
            $newLease->fixedCharges()->create([
                'name' => $charge->name,
                'amount' => $charge->amount,
                'is_paid_by_agency' => $charge->is_paid_by_agency,
            ]);
        }

        // 4. Generar cargo por la DIFERENCIA de depósito
        if ($depositDiff > 0) {
            $newLease->extraCharges()->create([
                'description' => "Diferencia Depósito en Garantía (Renovación)",
                'amount' => $depositDiff,
                'billing_date' => Carbon::parse($newLease->start_date),
                'installment_number' => 1,
                'total_installments' => 1,
                'is_paid' => false,
            ]);
        }

        // 5. Generar Honorarios Inmobiliaria si existen
        if ($newLease->agency_fee_amount > 0) {
            $total = $newLease->agency_fee_amount;
            $installments = $request->initial_fee_installments ?? 1;
            $amountPerInstallment = round($total / $installments, 2);
            $startDate = Carbon::parse($newLease->start_date);

            for ($i = 1; $i <= $installments; $i++) {
                $newLease->extraCharges()->create([
                    'description' => "Honorarios Inmobiliaria (Renovación - Cuota $i/$installments)",
                    'amount' => ($i == $installments) ? ($total - ($amountPerInstallment * ($installments - 1))) : $amountPerInstallment,
                    'billing_date' => $startDate->copy()->addMonths($i - 1),
                    'installment_number' => $i,
                    'total_installments' => $installments,
                    'is_paid' => false,
                ]);
            }
        }

        // 5. Desactivar contrato viejo
        $lease->update([
            'is_active' => false,
            'renewal_status' => 'past_version'
        ]);

        return redirect()->route('leases.show', $newLease)->with('success', 'Contrato renovado exitosamente. Se ha generado un cargo por la diferencia de depósito de $' . number_format($depositDiff, 2));
    }

    public function showRenegotiationForm(Lease $lease)
    {
        $lease->load(['property', 'tenant', 'fixedCharges']);
        $indexTypes = \App\Models\IndexType::all();
        return view('leases.renegotiate', compact('lease', 'indexTypes'));
    }

    public function storeRenegotiation(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'base_price' => 'required|numeric|min:0',
            'update_type' => 'required|in:fixed,indexed',
            'update_frequency_months' => 'required|integer|min:1',
            'update_value' => 'nullable|numeric',
            'index_type_id' => 'nullable|exists:index_types,id',
            'agency_fee_amount' => 'nullable|numeric|min:0',
            'initial_fee_installments' => 'nullable|integer|min:1',
            'security_deposit_diff' => 'nullable|numeric|min:0',
            'start_date' => 'required|date', // Fecha desde que rige el nuevo precio
            'end_date' => 'required|date|after:start_date',
        ]);

        $oldDeposit = $lease->security_deposit_amount ?? 0;
        $depositDiff = $validated['security_deposit_diff'] ?? 0;
        $newTotalDeposit = $oldDeposit + $depositDiff;

        // Crear nueva versión por renegociación
        $newLease = Lease::create(array_merge($validated, [
            'property_id' => $lease->property_id,
            'tenant_id' => $lease->tenant_id,
            'guarantor_name' => $lease->guarantor_name,
            'guarantor_id_number' => $lease->guarantor_id_number,
            'guarantor_email' => $lease->guarantor_email,
            'guarantor_address' => $lease->guarantor_address,
            'guarantor_phone' => $lease->guarantor_phone,
            'security_deposit_amount' => $newTotalDeposit,
            'agency_fee_amount' => $validated['agency_fee_amount'] ?? 0,
            'parent_lease_id' => $lease->id,
            'renewal_status' => 'renegotiated',
            'is_active' => true
        ]));

        // Heredar cargos fijos
        foreach ($lease->fixedCharges as $charge) {
            $newLease->fixedCharges()->create([
                'name' => $charge->name,
                'amount' => $charge->amount,
                'is_paid_by_agency' => $charge->is_paid_by_agency,
            ]);
        }

        // Honorarios por Renegociación
        if ($newLease->agency_fee_amount > 0) {
            $total = $newLease->agency_fee_amount;
            $installments = $request->initial_fee_installments ?? 1;
            $amountPerInstallment = round($total / $installments, 2);
            $billingDate = Carbon::parse($newLease->start_date);

            for ($i = 1; $i <= $installments; $i++) {
                $newLease->extraCharges()->create([
                    'description' => "Honorarios por Renegociación (Cuota $i/$installments)",
                    'amount' => ($i == $installments) ? ($total - ($amountPerInstallment * ($installments - 1))) : $amountPerInstallment,
                    'billing_date' => $billingDate->copy()->addMonths($i - 1),
                    'installment_number' => $i,
                    'total_installments' => $installments,
                    'is_paid' => false,
                ]);
            }
        }

        // Diferencia de depósito si hubiera
        if ($depositDiff > 0) {
            $newLease->extraCharges()->create([
                'description' => "Ajuste Depósito en Garantía (Renegociación)",
                'amount' => $depositDiff,
                'billing_date' => Carbon::parse($newLease->start_date),
                'installment_number' => 1,
                'total_installments' => 1,
                'is_paid' => false,
            ]);
        }

        // Desactivar versión anterior
        $lease->update([
            'is_active' => false,
            'renewal_status' => 'past_version'
        ]);

        return redirect()->route('leases.show', $newLease)->with('success', 'Contrato renegociado exitosamente. Las nuevas condiciones rigen desde el ' . Carbon::parse($newLease->start_date)->format('d/m/Y'));
    }

    public function terminate(Request $request, Lease $lease)
    {
        $request->validate([
            'penalty_amount' => 'required|numeric|min:0',
            'termination_date' => 'required|date',
            'reason' => 'nullable|string'
        ]);

        // 1. Generar cargo por multa de rescisión
        if ($request->penalty_amount > 0) {
            $lease->extraCharges()->create([
                'description' => "Multa por Rescisión Anticipada",
                'amount' => $request->penalty_amount,
                'billing_date' => $request->termination_date,
                'installment_number' => 1,
                'total_installments' => 1,
                'is_paid' => false,
                'notes' => $request->reason
            ]);
        }

        // 2. Desactivar contrato
        $lease->update([
            'is_active' => false,
            'end_date' => $request->termination_date,
            'renewal_status' => 'terminated',
            'termination_reason' => $request->reason
        ]);

        return redirect()->route('leases.index')->with('success', 'Contrato finalizado correctamente. Se ha generado la multa correspondiente.');
    }

    public function show(Lease $lease)
    {
        $lease->load(['property', 'tenant', 'fixedCharges', 'extraCharges']);
        $categories = \App\Models\TransactionCategory::all();
        return view('leases.show', compact('lease', 'categories'));
    }

    public function monthlySummary(Lease $lease, Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        $billingDate = Carbon::parse($month . '-01');

        $basePrice = $lease->base_price;
        $fixedChargesSum = $lease->fixedCharges->sum('amount');
        
        $extraCharges = $lease->extraCharges()
            ->whereYear('billing_date', $billingDate->year)
            ->whereMonth('billing_date', $billingDate->month)
            ->get();

        $extraChargesSum = $extraCharges->sum('amount');
        
        $total = $basePrice + $fixedChargesSum + $extraChargesSum;

        return view('leases.summary', compact('lease', 'month', 'basePrice', 'fixedChargesSum', 'extraCharges', 'extraChargesSum', 'total'));
    }
}
