<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\IndexType;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function locations()
    {
        $provinces = Province::withCount('cities')->with('cities')->get();
        return view('settings.locations', compact('provinces'));
    }

    public function propertyTypes()
    {
        $propertyTypes = PropertyType::all();
        return view('settings.property_types', compact('propertyTypes'));
    }

    public function indices()
    {
        $indexTypes = IndexType::all();
        return view('settings.indices', compact('indexTypes'));
    }

    public function storeProvince(Request $request)
    {
        $request->validate(['name' => 'required|unique:provinces']);
        $province = Province::create($request->all());
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => 'Provincia creada.',
                'province' => $province->loadCount('cities')
            ]);
        }

        return back()->with('success', 'Provincia creada.');
    }

    public function storeCity(Request $request)
    {
        $request->validate(['name' => 'required', 'province_id' => 'required|exists:provinces,id']);
        $city = City::create($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => 'Localidad creada.',
                'city' => $city
            ]);
        }

        return back()->with('success', 'Localidad creada.');
    }

    public function storePropertyType(Request $request)
    {
        $request->validate(['name' => 'required|unique:property_types']);
        $type = PropertyType::create($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => 'Tipo de inmueble creado.',
                'type' => $type
            ]);
        }

        return back()->with('success', 'Tipo de inmueble creado.');
    }

    public function getCities(Province $province)
    {
        return response()->json($province->cities);
    }

    public function destroyProvince(Province $province, Request $request)
    {
        // Check if properties are using any city of this province
        $cityIds = $province->cities()->pluck('id');
        if (\App\Models\Property::whereIn('city_id', $cityIds)->exists()) {
            $msg = 'No se puede eliminar la provincia: hay propiedades registradas en sus localidades.';
            return $request->wantsJson() ? response()->json(['error' => $msg], 422) : back()->with('error', $msg);
        }

        // Cascade delete cities
        $province->cities()->delete();
        $province->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => 'Provincia eliminada correctamente.']);
        }

        return back()->with('success', 'Provincia eliminada.');
    }

    public function destroyCity(City $city, Request $request)
    {
        if (\App\Models\Property::where('city_id', $city->id)->exists()) {
            $msg = 'No se puede eliminar: hay propiedades registradas en esta localidad.';
            return $request->wantsJson() ? response()->json(['error' => $msg], 422) : back()->with('error', $msg);
        }

        $city->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => 'Localidad eliminada.']);
        }

        return back()->with('success', 'Localidad eliminada.');
    }

    public function destroyPropertyType(PropertyType $type, Request $request)
    {
        if (\App\Models\Property::where('property_type_id', $type->id)->exists()) {
            $msg = 'No se puede eliminar: hay propiedades con este tipo asignado.';
            return $request->wantsJson() ? response()->json(['error' => $msg], 422) : back()->with('error', $msg);
        }
        
        $type->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => 'Tipo de inmueble eliminado.']);
        }

        return back()->with('success', 'Tipo de inmueble eliminado.');
    }

    public function storeIndexType(Request $request)
    {
        $request->validate(['name' => 'required|unique:index_types']);
        $index = IndexType::create($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => 'Índice de actualización creado.',
                'index' => $index
            ]);
        }

        return back()->with('success', 'Índice creado.');
    }

    public function destroyIndexType(IndexType $index, Request $request)
    {
        if (\App\Models\Lease::where('index_type_id', $index->id)->exists()) {
            $msg = 'No se puede eliminar: hay contratos vinculados a este índice.';
            return $request->wantsJson() ? response()->json(['error' => $msg], 422) : back()->with('error', $msg);
        }
        
        $index->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => 'Índice eliminado.']);
        }

        return back()->with('success', 'Índice eliminado.');
    }

    public function storeIndexValue(Request $request)
    {
        $validated = $request->validate([
            'index_type_id' => 'required|exists:index_types,id',
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'percentage' => 'required|numeric'
        ]);

        $value = \App\Models\IndexValue::updateOrCreate(
            [
                'index_type_id' => $validated['index_type_id'],
                'year' => $validated['year'],
                'month' => $validated['month'],
            ],
            ['percentage' => $validated['percentage']]
        );

        return response()->json([
            'success' => 'Valor cargado correctamente para ' . $validated['month'] . '/' . $validated['year'],
            'value' => $value
        ]);
    }

    public function getIndexValues(IndexType $index)
    {
        $values = $index->values()->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        // Convertimos percentage a float para evitar ceros innecesarios en el JSON
        $values->transform(function($val) {
            $val->percentage = (float) $val->percentage;
            return $val;
        });
        return response()->json($values);
    }

    public function destroyIndexValue(\App\Models\IndexValue $value)
    {
        $value->delete();
        return response()->json(['success' => 'Registro eliminado.']);
    }

    public function agencyBankAccounts()
    {
        $accounts = \App\Models\AgencyBankAccount::all();
        return view('settings.agency_bank_accounts', compact('accounts'));
    }

    public function storeAgencyBankAccount(Request $request)
    {
        $request->validate([
            'holder_name' => 'required',
            'bank_entity' => 'required',
            'cbu' => 'required',
            'alias' => 'required',
        ]);

        \App\Models\AgencyBankAccount::create($request->all());

        return back()->with('success', 'Cuenta bancaria agregada.');
    }

    public function setDefaultAgencyBankAccount(\App\Models\AgencyBankAccount $account)
    {
        \App\Models\AgencyBankAccount::where('id', '!=', $account->id)->update(['is_active' => false]);
        $account->update(['is_active' => true]);

        return back()->with('success', 'Cuenta marcada como predeterminada para cobros.');
    }

    public function destroyAgencyBankAccount(\App\Models\AgencyBankAccount $account)
    {
        $account->delete();
        return back()->with('success', 'Cuenta eliminada.');
    }

    public function contact()
    {
        $whatsapp = \App\Models\AgencySetting::get('whatsapp_number');
        $agencyEmail = \App\Models\AgencySetting::get('agency_email', 'contacto@habitar.com.ar');
        $agencyAddress = \App\Models\AgencySetting::get('agency_address', 'Av. Belgrano (N) 450, Santiago del Estero');
        return view('settings.contact', compact('whatsapp', 'agencyEmail', 'agencyAddress'));
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'required',
            'agency_email' => 'required|email',
            'agency_address' => 'required'
        ]);

        \App\Models\AgencySetting::set('whatsapp_number', $request->whatsapp_number);
        \App\Models\AgencySetting::set('agency_email', $request->agency_email);
        \App\Models\AgencySetting::set('agency_address', $request->agency_address);

        return back()->with('success', 'Información de contacto de la inmobiliaria actualizada.');
    }

    public function fetchIcl()
    {
        \Illuminate\Support\Facades\Artisan::call('icl:fetch');
        return back()->with('success', 'ICL actualizado desde BCRA.');
    }
}
