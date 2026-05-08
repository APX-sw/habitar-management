@extends('layouts.app')

@section('title', '| Ficha de Propietario')

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('owners.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver al listado
    </a>
    <h1 style="color: var(--primary-color);">{{ $owner->name }}</h1>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Sidebar: Owner Info -->
    <div>
        <div class="card" style="position: sticky; top: 100px;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Datos del Propietario</h3>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; color: var(--text-light); font-size: 0.8rem; text-transform: uppercase; font-weight: 600;">DNI / CUIT</label>
                <p style="font-weight: 500;">{{ $owner->dni_cuit }}</p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; color: var(--text-light); font-size: 0.8rem; text-transform: uppercase; font-weight: 600;">Email</label>
                <p style="font-weight: 500;">{{ $owner->email ?? 'Sin email' }}</p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; color: var(--text-light); font-size: 0.8rem; text-transform: uppercase; font-weight: 600;">Teléfono</label>
                <p style="font-weight: 500;">{{ $owner->phone ?? 'Sin teléfono' }}</p>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: var(--text-light); font-size: 0.8rem; text-transform: uppercase; font-weight: 600;">Notas</label>
                <p style="font-size: 0.9rem; color: var(--text-main);">{{ $owner->contact ?? 'Sin notas' }}</p>
            </div>

            <h3 style="margin-top: 2rem; margin-bottom: 1rem; font-size: 1rem; border-top: 1px solid var(--secondary-color); padding-top: 1rem;">Cuentas Bancarias</h3>
            @forelse($owner->bankAccounts as $account)
                <div style="background: var(--bg-body); padding: 0.8rem; border-radius: 6px; margin-bottom: 0.8rem; font-size: 0.85rem;">
                    <p><strong>CBU/Alias:</strong> {{ $account->cbu_alias }}</p>
                    <p><strong>Titular:</strong> {{ $account->holder_name }}</p>
                    <p><strong>CUIT:</strong> {{ $account->holder_cuit }}</p>
                </div>
            @empty
                <p style="font-size: 0.85rem; color: var(--text-light);">No hay cuentas registradas.</p>
            @endforelse
            
            <a href="#" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 1.5rem;">Editar Propietario</a>
        </div>
    </div>

    <!-- Main: Properties -->
    <div>
        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
            Propiedades Asociadas 
            <span style="background: var(--accent-color); color: white; padding: 0.1rem 0.6rem; border-radius: 20px; font-size: 0.9rem;">{{ $owner->properties->count() }}</span>
        </h3>

        @forelse($owner->properties as $property)
            <div class="card" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="color: var(--primary-color); margin-bottom: 0.3rem;">{{ $property->location }}</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">{{ $property->city }} • {{ ucfirst($property->type) }}</p>
                </div>
                <div style="text-align: right;">
                    @if($property->activeLease)
                        <div style="margin-bottom: 0.5rem;">
                            <span style="background: #E6FFFA; color: #2C7A7B; padding: 0.3rem 0.8rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; border: 1px solid #B2F5EA;">ALQUILADA</span>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-main);">Inquilino: <strong>{{ $property->activeLease->tenant->name }}</strong></p>
                    @else
                        <span style="background: #FFF5F5; color: #C53030; padding: 0.3rem 0.8rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; border: 1px solid #FEB2B2;">DISPONIBLE</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="card" style="text-align: center; padding: 3rem; border: 2px dashed var(--secondary-color); box-shadow: none; background: transparent;">
                <p style="color: var(--text-light);">Este propietario no tiene propiedades registradas.</p>
                <a href="{{ route('properties.create', ['owner_id' => $owner->id]) }}" style="color: var(--accent-color); font-weight: 600; text-decoration: none; display: block; margin-top: 1rem;">+ Cargar Propiedad</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
