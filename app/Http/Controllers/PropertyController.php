<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Owner;
use App\Models\Province;
use App\Models\City;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['owner', 'city', 'province', 'type', 'activeLease.tenant']);

        // Búsquedas
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('location', 'like', "%$search%")
                  ->orWhereHas('city', function($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('owner', function($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
            });
        }

        // Filtros
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }
        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }
        if ($request->filled('rooms')) {
            $query->where('rooms', $request->rooms);
        }
        if ($request->filled('status')) {
            if ($request->status === 'rented') {
                $query->whereHas('activeLease');
            } elseif ($request->status === 'available') {
                $query->whereDoesntHave('activeLease');
            }
        }

        $properties = $query->latest()->paginate(15)->withQueryString();
        
        $owners = Owner::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $propertyTypes = PropertyType::orderBy('name')->get();
            
        return view('properties.index', compact('properties', 'owners', 'cities', 'propertyTypes'));
    }

    public function create()
    {
        $owners = Owner::all();
        $provinces = Province::orderBy('name')->get();
        $propertyTypes = PropertyType::all();
        $allRecurrentConcepts = \App\Models\RecurrentConcept::orderBy('name')->get();
        return view('properties.create', compact('owners', 'provinces', 'propertyTypes', 'allRecurrentConcepts'));
    }

    public function store(Request $request)
    {
        if ($request->filled('new_owner_name')) {
            $owner = Owner::create([
                'name' => $request->new_owner_name,
                'dni_cuit' => $request->new_owner_dni,
                'contact' => $request->new_owner_contact,
            ]);
            $request->merge(['owner_id' => $owner->id]);
        }

        $validated = $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'property_type_id' => 'required|exists:property_types,id',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'rooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:0',
            'has_garage' => 'boolean',
            'has_patio' => 'boolean',
            'has_balcony' => 'boolean',
            'pets_allowed' => 'boolean',
            'has_expenses' => 'boolean',
            'square_meters' => 'nullable|numeric|min:0',
            'expenses_payment_address' => 'nullable|string|max:255',
            'expenses_payment_number' => 'nullable|string|max:255',
            'recurrent_concepts' => 'nullable|array',
            'recurrent_concepts.*.id' => 'nullable|exists:recurrent_concepts,id',
            'recurrent_concepts.*.payment_code' => 'nullable|string|max:255',
        ]);

        // Default values for booleans if not present
        $data = $validated;
        unset($data['recurrent_concepts']); // Quitamos del array principal
        $data['has_garage'] = $request->has('has_garage');
        $data['has_patio'] = $request->has('has_patio');
        $data['has_balcony'] = $request->has('has_balcony');
        $data['pets_allowed'] = $request->has('pets_allowed');
        $data['has_expenses'] = $request->has('has_expenses');

        // Si no paga expensas, limpiar campos
        if (!$data['has_expenses']) {
            $data['expenses_payment_address'] = null;
            $data['expenses_payment_number'] = null;
        }

        $property = Property::create($data);

        // Atar los conceptos recurrentes si vinieron en el request
        if ($request->has('recurrent_concepts')) {
            foreach ($request->recurrent_concepts as $rc) {
                if (!empty($rc['id'])) {
                    $property->recurrentConcepts()->attach($rc['id'], [
                        'payment_code' => $rc['payment_code'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('properties.index')->with('success', 'Propiedad creada correctamente.');
    }

    public function show(Property $property)
    {
        $property->load(['owner', 'city', 'type', 'leases.tenant', 'recurrentConcepts']);
        $allRecurrentConcepts = \App\Models\RecurrentConcept::orderBy('name')->get();
        return view('properties.show', compact('property', 'allRecurrentConcepts'));
    }

    public function showApi(Property $property)
    {
        $property->load(['owner', 'province', 'city', 'type']);
        return response()->json($property);
    }

    public function edit(Property $property)
    {
        $owners = Owner::all();
        $provinces = Province::orderBy('name')->get();
        $propertyTypes = PropertyType::all();
        return view('properties.edit', compact('property', 'owners', 'provinces', 'propertyTypes'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'property_type_id' => 'required|exists:property_types,id',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'rooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:0',
            'has_garage' => 'boolean',
            'has_patio' => 'boolean',
            'has_balcony' => 'boolean',
            'pets_allowed' => 'boolean',
            'square_meters' => 'nullable|numeric|min:0',
        ]);

        $data = $validated;
        $data['has_garage'] = $request->has('has_garage');
        $data['has_patio'] = $request->has('has_patio');
        $data['has_balcony'] = $request->has('has_balcony');
        $data['pets_allowed'] = $request->has('pets_allowed');

        $property->update($data);

        return redirect()->route('properties.index')->with('success', 'Propiedad actualizada.');
    }

    public function destroy(Property $property)
    {
        if ($property->leases()->where('is_active', true)->exists()) {
            return back()->with('error', 'No se puede eliminar la propiedad porque tiene un contrato de alquiler vigente.');
        }

        $property->delete();

        return redirect()->route('properties.index')->with('success', 'Propiedad eliminada correctamente.');
    }

    public function addConcept(Request $request, Property $property)
    {
        $request->validate([
            'recurrent_concept_id' => 'required|exists:recurrent_concepts,id',
            'payment_code' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $property->recurrentConcepts()->attach($request->recurrent_concept_id, [
            'payment_code' => $request->payment_code,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Concepto adherido correctamente.');
    }

    public function removeConcept(Property $property, \App\Models\RecurrentConcept $concept)
    {
        $property->recurrentConcepts()->detach($concept->id);
        return back()->with('success', 'Concepto desvinculado correctamente.');
    }
}
