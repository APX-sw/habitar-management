@extends('layouts.app')

@section('title', '| Perfil de Inquilino')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; border-bottom: 2px solid var(--secondary-color); padding-bottom: 1.2rem;">
        <!-- Fila superior: Link de volver sutil -->
        <div style="margin-bottom: 0.8rem;">
            <a href="{{ route('tenants.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
                <span style="font-weight: 600;">Volver al listado de inquilinos</span>
            </a>
        </div>

        <!-- Fila de perfil y botón de editar -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="width: 70px; height: 70px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    {{ substr($tenant->name, 0, 1) }}
                </div>
                <div>
                    <h1 style="color: var(--primary-color); margin: 0; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em;">{{ $tenant->name }}</h1>
                    <p style="color: var(--text-light); margin: 0; margin-top: 0.2rem; font-weight: 500;">Inquilino registrado desde {{ $tenant->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            <div style="display: flex; gap: 0.8rem; align-items: center;">
                <a href="{{ route('tenants.edit', $tenant) }}" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 1.5rem; border: 1px solid #cbd5e0; border-radius: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Editar Perfil
                </a>
            </div>
        </div>
    </div>

    @if($totalDebt > 0)
        <!-- Saldo Deudor Consolidado Box -->
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: #fff5f5; border: 1px solid #fed7d7; border-left: 5px solid #e53e3e; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(229, 62, 62, 0.05); display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="background: #fed7d7; color: #e53e3e; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div>
                        <h3 style="margin: 0; color: #9b2c2c; font-size: 1.1rem; font-weight: 800;">Saldo Deudor Consolidado</h3>
                        <p style="margin: 0.2rem 0 0 0; color: #c53030; font-size: 0.85rem; font-weight: 600;">Este inquilino posee cobros impagos o con saldos pendientes.</p>
                    </div>
                </div>
                <div style="font-size: 1.8rem; font-weight: 900; color: #e53e3e; font-family: monospace;">
                    ${{ number_format($totalDebt, 2, ',', '.') }}
                </div>
            </div>
            
            <div style="background: white; border: 1px solid #feb2b2; border-radius: 12px; overflow: hidden; margin-top: 0.25rem;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                    <thead>
                        <tr style="background: #fff5f5; border-bottom: 1px solid #fed7d7; color: #9b2c2c; font-weight: 800; text-transform: uppercase; font-size: 0.75rem;">
                            <th style="padding: 0.75rem 1rem;">Propiedad</th>
                            <th style="padding: 0.75rem 1rem;">Periodo</th>
                            <th style="padding: 0.75rem 1rem; text-align: right;">Saldo Pendiente</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; width: 100px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingCollections as $pendingCol)
                            <tr style="border-bottom: 1px solid #f7fafc; color: #4a5568; font-weight: 500;">
                                <td style="padding: 0.75rem 1rem; font-weight: 700;">{{ $pendingCol->lease->property->location }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    {{ ucfirst(\Carbon\Carbon::createFromDate(null, $pendingCol->month, 1)->locale('es')->translatedFormat('F')) }} {{ $pendingCol->year }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right; color: #e53e3e; font-weight: 800; font-family: monospace;">
                                    ${{ number_format($pendingCol->pending_amount, 2, ',', '.') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <a href="{{ route('collections.show', $pendingCol) }}" style="color: #3182ce; text-decoration: none; font-weight: 700;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        Ver Cobro
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

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
                        <a href="{{ route('leases.show', $lease) }}" class="btn" style="background: white; border: 1px solid var(--secondary-color); color: var(--primary-color); font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 32px; padding: 0 1rem;">Ver Contrato</a>
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

    <!-- Sección de Garante (Sutil y al final) -->
    @php
        $activeLease = $tenant->leases->firstWhere('is_active', true) ?? $tenant->leases->sortByDesc('created_at')->first();
    @endphp

    @if($activeLease && $activeLease->guarantor_name)
        <div style="margin-top: 3rem; border-top: 2px solid var(--secondary-color); padding-top: 2rem; padding-bottom: 2rem;">
            <h3 style="font-size: 1.05rem; color: #718096; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Información del Garante (Contrato: {{ $activeLease->property->location }})
            </h3>
            <div style="background: #f8fafc; border: 1px solid var(--secondary-color); border-radius: 16px; padding: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Nombre Completo</span>
                    <span style="font-weight: 700; color: var(--primary-color); font-size: 1rem;">{{ $activeLease->guarantor_name }}</span>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">DNI / CUIT</span>
                    <span style="font-weight: 700; color: var(--primary-color); font-size: 1rem;">{{ $activeLease->guarantor_id_number ?: 'No registrado' }}</span>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Teléfono</span>
                    <span style="font-weight: 700; color: var(--primary-color); font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        @if($activeLease->guarantor_phone)
                            {{ $activeLease->guarantor_phone }}
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activeLease->guarantor_phone) }}" target="_blank" style="text-decoration: none; display: inline-flex;" title="Enviar WhatsApp">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#25D366" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            </a>
                        @else
                            <span style="color: var(--text-light); font-weight: 500;">No registrado</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Email</span>
                    <span style="font-weight: 700; color: var(--primary-color); font-size: 1rem;">
                        @if($activeLease->guarantor_email)
                            <a href="mailto:{{ $activeLease->guarantor_email }}" style="color: var(--primary-color); text-decoration: none; hover: underline;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">{{ $activeLease->guarantor_email }}</a>
                        @else
                            <span style="color: var(--text-light); font-weight: 500;">No registrado</span>
                        @endif
                    </span>
                </div>
                <div style="grid-column: 1 / -1; border-top: 1px dashed var(--secondary-color); padding-top: 1rem;">
                    <span style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Dirección de Garantía</span>
                    <span style="font-weight: 600; color: var(--text-main); font-size: 0.95rem;">{{ $activeLease->guarantor_address ?: 'No registrada' }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
