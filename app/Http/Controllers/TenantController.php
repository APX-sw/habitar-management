<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::withCount('leases');

        // Búsqueda por nombre o DNI
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('dni_cuit', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filtro de Deuda (Refinado)
        if ($request->filled('debt_status')) {
            if ($request->debt_status === 'with_debt') {
                // Inquilinos con al menos un contrato que tenga una cobranza no pagada
                $query->whereHas('leases.collections', function($q) {
                    $q->where('status', '!=', 'paid');
                });
            } elseif ($request->debt_status === 'up_to_date') {
                // Inquilinos que NO tienen ningún contrato con cobranzas pendientes
                // Es decir: o no tienen cobranzas, o todas están pagas.
                $query->whereDoesntHave('leases.collections', function($q) {
                    $q->where('status', '!=', 'paid');
                });
            }
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();
        $accounts = \App\Models\Account::where('is_active', true)->get();
        
        return view('tenants.index', compact('tenants', 'accounts'));
    }

    public function pendingCollections(Tenant $tenant)
    {
        return response()->json([
            'tenant' => $tenant->name,
            'debt' => $tenant->total_debt,
            'collections' => $tenant->getPendingCollections()->values()
        ]);
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'dni_cuit' => 'required|string|unique:tenants',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'contact' => 'nullable|string',
            'references' => 'nullable|string',
        ]);

        Tenant::create($validated);

        return redirect()->route('tenants.index')->with('success', 'Inquilino creado correctamente.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('leases.property');
        $pendingCollections = $tenant->getPendingCollections();
        $totalDebt = $pendingCollections->sum('pending_amount');
        
        return view('tenants.show', compact('tenant', 'pendingCollections', 'totalDebt'));
    }

    public function showApi(Tenant $tenant)
    {
        return response()->json($tenant);
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'dni_cuit' => 'required|string|unique:tenants,dni_cuit,' . $tenant->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'contact' => 'nullable|string',
            'references' => 'nullable|string',
        ]);

        $tenant->update($validated);

        return redirect()->route('tenants.index')->with('success', 'Inquilino actualizado.');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->leases()->where('is_active', true)->exists()) {
            return back()->with('error', 'No se puede eliminar el inquilino porque tiene un contrato vigente.');
        }

        $tenant->delete();

        return redirect()->route('tenants.index')->with('success', 'Inquilino eliminado correctamente.');
    }
}
