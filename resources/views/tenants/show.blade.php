@extends('layouts.app')

@section('title', '| Perfil de Inquilino')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--secondary-color); padding-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="width: 70px; height: 70px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700;">
                {{ substr($tenant->name, 0, 1) }}
            </div>
            <div>
                <h1 style="color: var(--primary-color); margin: 0;">{{ $tenant->name }}</h1>
                <p style="color: var(--text-light); margin: 0;">Inquilino registrado desde {{ $tenant->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        <div style="display: flex; gap: 0.8rem;">
            <a href="{{ route('tenants.edit', $tenant) }}" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-weight: 600;">Editar</a>
            <a href="{{ route('tenants.index') }}" class="btn" style="background: #edf2f7; color: #4a5568;">Volver</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
        <!-- Info Personal -->
        <div>
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.1rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">Información de Contacto</h3>
                <div style="display: flex; flex-direction: column; gap: 1.2rem;">
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; margin: 0;">DNI / CUIT</p>
                        <p style="font-weight: 600; margin: 0.2rem 0 0 0;">{{ $tenant->dni_cuit }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; margin: 0;">Teléfono</p>
                        <p style="font-weight: 600; margin: 0.2rem 0 0 0;">{{ $tenant->phone ?: 'No registrado' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; margin: 0;">Email</p>
                        <p style="font-weight: 600; margin: 0.2rem 0 0 0;">{{ $tenant->email ?: 'No registrado' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; margin: 0;">Contacto Emergencia</p>
                        <p style="font-weight: 600; margin: 0.2rem 0 0 0;">{{ $tenant->emergency_contact ?: 'No registrado' }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 style="font-size: 1.1rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">Referencias</h3>
                <p style="font-size: 0.9rem; line-height: 1.6; color: #4a5568;">{{ $tenant->references ?: 'Sin referencias adicionales cargadas.' }}</p>
            </div>
        </div>

        <!-- Historial de Alquileres -->
        <div>
            <div class="card">
                <h3 style="font-size: 1.2rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">Historial de Contratos</h3>
                
                @forelse($tenant->leases as $lease)
                    <div style="background: #f8fafc; border: 1px solid var(--secondary-color); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.2rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color); text-transform: uppercase; margin-bottom: 0.4rem;">
                                {{ $lease->is_active ? 'Contrato Actual' : 'Contrato Finalizado' }}
                            </p>
                            <h4 style="margin: 0; color: var(--primary-color);">{{ $lease->property->location }}</h4>
                            <p style="font-size: 0.85rem; color: var(--text-light); margin-top: 0.3rem;">{{ $lease->start_date }} → {{ $lease->end_date ?: 'Indefinido' }}</p>
                        </div>
                        <a href="{{ route('leases.show', $lease) }}" class="btn" style="background: white; border: 1px solid var(--secondary-color); color: var(--primary-color); font-size: 0.8rem; font-weight: 600;">Ver Contrato</a>
                    </div>
                @empty
                    <div style="text-align: center; padding: 3rem; color: var(--text-light);">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 1rem;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <p>Este inquilino no tiene contratos registrados aún.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
