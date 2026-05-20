@extends('layouts.app')

@section('title', '| Perfil de Propietario')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Top Navigation & Title -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
        <div>
            <a href="{{ route('owners.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.8rem; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='var(--accent-color)'" onmouseout="this.style.color='var(--text-light)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Volver al listado
            </a>
            <h1 style="color: var(--primary-color); font-size: 2.6rem; font-weight: 800; letter-spacing: -0.03em; margin: 0;">{{ $owner->name }}</h1>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('reports.create', ['owner_id' => $owner->id]) }}" class="btn-edit-header" style="color: #319795; border-color: #b2f5ea; background: #e6fffa;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Generar Dossier
            </a>
            <a href="{{ route('owners.edit', $owner) }}" class="btn-edit-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Editar Perfil
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2.5rem; align-items: start;">
        <!-- Left Column: Owner Information -->
        <aside style="display: grid; gap: 2rem;">


            <!-- Profile Info Card -->
            <div class="card-premium" style="padding: 2rem;">
                <h3 class="section-title-small">Datos de Contacto</h3>
                <div style="display: grid; gap: 1.5rem;">
                    <div class="info-item">
                        <span class="info-label">DNI / CUIT</span>
                        <span class="info-value">{{ $owner->dni_cuit }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $owner->email ?? 'No especificado' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Teléfono</span>
                        <span class="info-value">{{ $owner->phone ?? 'No especificado' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Notas Internas</span>
                        <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-main); background: #f8fafc; padding: 1rem; border-radius: 8px; margin: 0;">
                            {{ $owner->contact ?? 'Sin observaciones registradas.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bank Accounts Card -->
            <div class="card-premium" style="padding: 2rem; border-top: 4px solid #4299E1;">
                <h3 class="section-title-small" style="color: #2b6cb0;">Cuentas Bancarias</h3>
                <div style="display: grid; gap: 1rem;">
                    @forelse($owner->bankAccounts as $account)
                        <div style="background: #ebf8ff; border: 1px solid #bee3f8; padding: 1.25rem; border-radius: 12px;">
                            <div style="font-weight: 700; color: #2c5282; font-size: 1rem; margin-bottom: 0.5rem;">{{ $account->holder_name }}</div>
                            <div style="font-size: 0.85rem; color: #4a5568; font-family: monospace; letter-spacing: 0.05em; background: rgba(255,255,255,0.65); padding: 0.5rem 0.75rem; border-radius: 6px; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; border: 1px solid rgba(190, 227, 248, 0.5);">
                                <span style="user-select: all;">{{ $account->cbu_alias }}</span>
                                <button onclick="navigator.clipboard.writeText('{{ $account->cbu_alias }}'); this.innerText='✅'; setTimeout(() => this.innerText='📋', 1500);" style="background: none; border: none; cursor: pointer; font-size: 0.95rem; padding: 0; display: flex; align-items: center;" title="Copiar CBU/Alias">
                                    📋
                                </button>
                            </div>
                            <div style="font-size: 0.75rem; color: #718096; font-weight: 600;">CUIT: {{ $account->holder_cuit }}</div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 1.5rem; color: var(--text-light); font-size: 0.9rem;">
                            No tiene cuentas registradas.
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>

        <!-- Right Column: Properties -->
        <main>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color); display: flex; align-items: center; gap: 0.75rem;">
                    Cartera de Propiedades
                    <span style="background: var(--accent-color); color: white; padding: 0.2rem 0.8rem; border-radius: 30px; font-size: 0.85rem;">{{ $owner->properties->count() }}</span>
                </h2>
                <a href="{{ route('properties.create', ['owner_id' => $owner->id]) }}" class="btn-add-property">
                    + Cargar Propiedad
                </a>
            </div>

            <div style="display: grid; gap: 1.5rem;">
                @forelse($owner->properties as $property)
                    <div class="property-card-dashboard">
                        <div style="display: flex; gap: 1.5rem; align-items: center;">
                            <div class="property-icon-box">
                                @if($property->type->name == 'Departamento')
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="2"></line><line x1="15" y1="22" x2="15" y2="2"></line><line x1="12" y1="22" x2="12" y2="2"></line><line x1="9" y1="6" x2="15" y2="6"></line><line x1="9" y1="10" x2="15" y2="10"></line><line x1="9" y1="14" x2="15" y2="14"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
                                @else
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--primary-color); margin: 0 0 0.25rem 0;">{{ $property->location }}</h4>
                                <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-light); font-size: 0.95rem;">
                                    <span>{{ $property->city->name }}</span>
                                    <span style="width: 4px; height: 4px; background: #cbd5e0; border-radius: 50%;"></span>
                                    <span>{{ $property->type->name }}</span>
                                </div>
                            </div>
                            <div style="text-align: right; min-width: 180px;">
                                @if($property->activeLease)
                                    <div class="status-badge-active">ALQUILADA</div>
                                    <div style="margin-top: 0.5rem;">
                                        <span style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Inquilino</span>
                                        <div style="font-weight: 600; color: var(--text-main);">{{ $property->activeLease->tenant->name }}</div>
                                    </div>
                                @else
                                    <div class="status-badge-available">DISPONIBLE</div>
                                    <div style="margin-top: 0.5rem; color: var(--text-light); font-size: 0.85rem;">Lista para alquilar</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 4rem 2rem; background: white; border: 2px dashed #e2e8f0; border-radius: 16px;">
                        <div style="color: #cbd5e0; margin-bottom: 1rem;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                        </div>
                        <p style="color: var(--text-light); font-size: 1.1rem; margin-bottom: 1.5rem;">Este propietario aún no tiene propiedades cargadas.</p>
                        <a href="{{ route('properties.create', ['owner_id' => $owner->id]) }}" class="btn-add-property" style="display: inline-flex;">
                            Empezar a Cargar
                        </a>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>

<style>
    .card-premium {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }

    .section-title-small {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 800;
        color: var(--text-light);
        margin: 0 0 1.5rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-light);
        text-transform: uppercase;
    }

    .info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .btn-edit-header {
        background: white;
        color: var(--primary-color);
        text-decoration: none;
        height: 42px;
        padding: 0 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    .btn-edit-header:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .btn-add-property {
        background: var(--accent-gradient);
        color: white;
        text-decoration: none;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(56, 178, 172, 0.3);
    }
    .btn-add-property:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(56, 178, 172, 0.4);
    }

    .property-card-dashboard {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .property-card-dashboard:hover {
        transform: scale(1.01);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border-color: var(--accent-color);
    }

    .property-icon-box {
        width: 54px;
        height: 54px;
        background: #f8fafc;
        color: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
    }

    .status-badge-active {
        display: inline-block;
        padding: 0.4rem 1rem;
        background: #C6F6D5;
        color: #22543D;
        font-weight: 800;
        font-size: 0.75rem;
        border-radius: 6px;
        letter-spacing: 0.02em;
    }

    .status-badge-available {
        display: inline-block;
        padding: 0.4rem 1rem;
        background: #E2E8F0;
        color: #4A5568;
        font-weight: 800;
        font-size: 0.75rem;
        border-radius: 6px;
        letter-spacing: 0.02em;
    }
</style>
@endsection
