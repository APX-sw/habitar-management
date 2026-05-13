<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Owner::withCount('properties');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('dni_cuit', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $owners = $query->latest()->paginate(15)->withQueryString();
            
        return view('owners.index', compact('owners'));
    }

    public function create()
    {
        return view('owners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'dni_cuit' => 'required|string|unique:owners',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'contact' => 'nullable|string',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.cbu_alias' => 'required|string',
            'bank_accounts.*.holder_name' => 'required|string',
            'bank_accounts.*.holder_cuit' => 'required|string',
        ]);

        $owner = Owner::create($validated);

        if ($request->has('bank_accounts')) {
            foreach ($request->bank_accounts as $account) {
                $owner->bankAccounts()->create($account);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'owner' => $owner
            ]);
        }

        return redirect()->route('owners.index')->with('success', 'Propietario creado correctamente.');
    }

    public function show(Owner $owner)
    {
        $owner->load('properties.activeLease.tenant');
        return view('owners.show', compact('owner'));
    }

    public function edit(Owner $owner)
    {
        return view('owners.edit', compact('owner'));
    }

    public function update(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'dni_cuit' => 'required|string|unique:owners,dni_cuit,' . $owner->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'contact' => 'nullable|string',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.cbu_alias' => 'required|string',
            'bank_accounts.*.holder_name' => 'required|string',
            'bank_accounts.*.holder_cuit' => 'required|string',
        ]);

        $owner->update($validated);

        // Sincronizar cuentas bancarias
        $owner->bankAccounts()->delete();
        if ($request->has('bank_accounts')) {
            foreach ($request->bank_accounts as $account) {
                $owner->bankAccounts()->create($account);
            }
        }

        return redirect()->route('owners.index')->with('success', 'Propietario actualizado.');
    }

    public function destroy(Owner $owner)
    {
        // Verificar si tiene propiedades con contratos activos
        $hasActiveLeases = $owner->properties()->whereHas('leases', function($q) {
            $q->where('is_active', true);
        })->exists();

        if ($hasActiveLeases) {
            return back()->with('error', 'No se puede eliminar al propietario porque tiene propiedades con contratos de alquiler activos. Primero debe finalizar o rescindir los contratos.');
        }

        // Si no tiene contratos activos, procedemos a eliminar (esto eliminará sus propiedades también si lo hacemos manual o por DB)
        // Eliminamos propiedades manualmente para asegurar el cascade si no está en la DB
        $owner->properties()->delete();
        $owner->delete();

        return redirect()->route('owners.index')->with('success', 'Propietario y sus propiedades vacantes eliminados correctamente.');
    }
}
