@extends('layouts.app')

@section('title', '| Editar Propiedad')

@section('styles')
<style>
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .full-width { grid-column: 1 / -1; }
</style>
@endsection

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--secondary-color); padding-bottom: 1rem;">
        <div>
            <h1 style="color: var(--primary-color); font-size: 2rem;">Editar Propiedad</h1>
            <p style="color: var(--text-light); font-size: 0.95rem;">Ubicada en {{ $property->location }}</p>
        </div>
        <a href="{{ route('properties.index') }}" class="btn" style="background: var(--secondary-color);">Volver</a>
    </div>

    <form action="{{ route('properties.update', $property) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card">
            <div class="form-grid">
                <!-- Propietario -->
                <div class="full-width" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; font-weight: 600; font-size: 1.1rem; color: var(--primary-color);">Propietario</label>
                    <select name="owner_id" required style="width: 100%; padding: 1rem; border-radius: var(--border-radius); border: 2px solid var(--secondary-color);">
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ $property->owner_id == $owner->id ? 'selected' : '' }}>{{ $owner->name }} ({{ $owner->dni_cuit }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ubicación -->
                <div class="full-width" style="margin-top: 1rem;">
                    <label style="display: block; margin-bottom: 1.5rem; font-weight: 600; font-size: 1.1rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Detalles de la Propiedad</label>
                </div>

                <div class="full-width">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Dirección / Calle y Número</label>
                    <input type="text" name="location" value="{{ $property->location }}" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <!-- Comodidades -->
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Ambientes</label>
                    <input type="number" name="rooms" value="{{ $property->rooms }}" min="1" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Baños</label>
                    <input type="number" name="bathrooms" value="{{ $property->bathrooms }}" min="0" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <div class="full-width">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Metros Cuadrados Totales (m²)</label>
                    <input type="number" step="0.01" name="square_meters" value="{{ $property->square_meters }}" style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <div class="full-width" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin: 1rem 0;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        <input type="checkbox" name="has_garage" value="1" {{ $property->has_garage ? 'checked' : '' }}>
                        <span style="font-weight: 500;">Cochera</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        <input type="checkbox" name="has_patio" value="1" {{ $property->has_patio ? 'checked' : '' }}>
                        <span style="font-weight: 500;">Patio</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        <input type="checkbox" name="has_balcony" value="1" {{ $property->has_balcony ? 'checked' : '' }}>
                        <span style="font-weight: 500;">Balcón</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        <input type="checkbox" name="pets_allowed" value="1" {{ $property->pets_allowed ? 'checked' : '' }}>
                        <span style="font-weight: 500;">Mascotas</span>
                    </label>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Provincia</label>
                    <select name="province_id" id="province-select" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ $property->province_id == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Localidad</label>
                    <select name="city_id" id="city-select" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                        <option value="{{ $property->city_id }}">{{ $property->city->name ?? 'Seleccionar Localidad' }}</option>
                    </select>
                </div>

                <div class="full-width">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tipo de Inmueble</label>
                    <select name="property_type_id" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type->id }}" {{ $property->property_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="full-width">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">{{ $property->description }}</textarea>
                </div>

                <div class="full-width" style="display: flex; justify-content: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 1.2rem 5rem;">Actualizar Propiedad</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const provinceSelect = document.getElementById('province-select');
    const citySelect = document.getElementById('city-select');

    async function updateCities(provinceId, selectedCityId = null) {
        citySelect.innerHTML = '<option value="">-- Cargando... --</option>';
        if (!provinceId) {
            citySelect.innerHTML = '<option value="">-- Seleccionar Localidad --</option>';
            return;
        }
        try {
            const response = await fetch(`/api/provinces/${provinceId}/cities`);
            const cities = await response.json();
            citySelect.innerHTML = '<option value="">-- Seleccionar Localidad --</option>';
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.name;
                if (selectedCityId && city.id == selectedCityId) option.selected = true;
                citySelect.appendChild(option);
            });
        } catch (error) { console.error(error); }
    }

    provinceSelect.addEventListener('change', function() {
        updateCities(this.value);
    });

    // Populate on load to show all cities of the current province
    window.addEventListener('load', () => {
        const currentProvince = provinceSelect.value;
        const currentCity = "{{ $property->city_id }}";
        if (currentProvince) {
            updateCities(currentProvince, currentCity);
        }
    });
</script>
@endsection
