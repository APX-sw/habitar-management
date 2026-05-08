@extends('layouts.app')

@section('title', '| Ficha de Propiedad')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--secondary-color); padding-bottom: 1rem;">
        <div>
            <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;">Ficha Técnica de Propiedad</h1>
            <div style="display: flex; align-items: center; gap: 0.5rem; background: #f0f4f8; padding: 0.5rem 1rem; border-radius: 8px; width: fit-content;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2b6cb0" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span style="font-weight: 700; color: #2d3748;">{{ $property->city->name ?? 'N/A' }}</span>
                <span style="color: #718096;">• {{ $property->province->name ?? 'N/A' }}</span>
                <span style="margin-left: 1rem; color: #a0aec0;">|</span>
                <span style="margin-left: 1rem; color: #4a5568; font-weight: 500;">{{ $property->location }}</span>
            </div>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('properties.edit', $property) }}" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-weight: 600;">Editar</a>
            <a href="{{ route('properties.index') }}" class="btn" style="background: #edf2f7; color: #4a5568;">Volver al Listado</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Columna Izquierda: Datos Técnicos -->
        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Detalles del Inmueble</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase;">Tipo</p>
                        <p style="font-size: 1.1rem; font-weight: 600;">{{ $property->type->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase;">Superficie</p>
                        <p style="font-size: 1.1rem; font-weight: 600;">{{ $property->square_meters ?? '0' }} m²</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase;">Ambientes</p>
                        <p style="font-size: 1.1rem; font-weight: 600;">{{ $property->rooms }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase;">Baños</p>
                        <p style="font-size: 1.1rem; font-weight: 600;">{{ $property->bathrooms }}</p>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                    @if($property->has_garage)
                        <span style="background: #e6fffa; color: #2c7a7b; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">✓ Cochera</span>
                    @endif
                    @if($property->has_patio)
                        <span style="background: #e6fffa; color: #2c7a7b; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">✓ Patio</span>
                    @endif
                    @if($property->has_balcony)
                        <span style="background: #e6fffa; color: #2c7a7b; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">✓ Balcón</span>
                    @endif
                    @if($property->pets_allowed)
                        <span style="background: #fffaf0; color: #9c4221; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">🐾 Mascotas</span>
                    @endif
                </div>

                <div style="margin-top: 2rem;">
                    <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Descripción / Notas</p>
                    <p style="line-height: 1.6; color: #4a5568;">{{ $property->description ?: 'Sin descripción adicional.' }}</p>
                </div>
            </div>

            <!-- Historial de Alquileres -->
            <div class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Historial de Contratos</h3>
                @forelse($property->leases as $lease)
                    <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-weight: 700; color: var(--primary-color);">{{ $lease->tenant->name }}</p>
                            <p style="font-size: 0.8rem; color: var(--text-light);">Desde: {{ $lease->start_date }}</p>
                        </div>
                        <span style="padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.7rem; font-weight: 700; {{ $lease->is_active ? 'background: #C6F6D5; color: #22543D;' : 'background: #E2E8F0; color: #4A5568;' }}">
                            {{ $lease->is_active ? 'VIGENTE' : 'FINALIZADO' }}
                        </span>
                    </div>
                @empty
                    <p style="text-align: center; color: var(--text-light); padding: 2rem;">No hay contratos registrados para esta propiedad.</p>
                @endforelse
            </div>
        </div>

        <!-- Columna Derecha: Propietario -->
        <div>
            <div class="card" style="border-top: 4px solid var(--accent-color);">
                <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; margin-bottom: 1rem;">Dueño de la Propiedad</p>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="width: 50px; height: 50px; background: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color); font-size: 1.2rem;">
                        {{ substr($property->owner->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 style="color: var(--primary-color); margin: 0;">{{ $property->owner->name }}</h4>
                        <p style="font-size: 0.85rem; color: var(--text-light); margin: 0;">{{ $property->owner->dni_cuit }}</p>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <div style="font-size: 0.9rem;"><span style="color: var(--text-light);">📞</span> {{ $property->owner->phone ?: 'Sin teléfono' }}</div>
                    <div style="font-size: 0.9rem;"><span style="color: var(--text-light);">✉️</span> {{ $property->owner->email ?: 'Sin email' }}</div>
                </div>
                <a href="{{ route('owners.show', $property->owner) }}" class="btn" style="width: 100%; background: var(--primary-color); color: white; margin-top: 1.5rem; text-align: center; font-size: 0.85rem;">Ver Perfil Completo</a>
            </div>

            @if(!$property->activeLease)
                <div style="margin-top: 2rem; background: #ebf8ff; padding: 2rem; border-radius: 15px; border: 1px dashed #3182ce; text-align: center;">
                    <p style="color: #2b6cb0; font-weight: 700; margin-bottom: 1rem;">¡Esta propiedad está disponible!</p>
                    <a href="{{ route('leases.create', ['property_id' => $property->id]) }}" class="btn" style="background: #3182ce; color: white;">Crear Nuevo Contrato</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
