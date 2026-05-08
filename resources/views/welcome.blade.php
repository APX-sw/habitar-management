@extends('layouts.app')

@section('title', '| Tablero')

@section('content')
<div style="margin-bottom: 2.5rem;">
    <h1 style="color: var(--primary-color); font-size: 2.2rem; margin-bottom: 0.5rem;">Tablero de Control</h1>
    <p style="color: var(--text-light);">Bienvenido al sistema de gestión de Habitar Inmobiliaria.</p>
</div>

<!-- KPI Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 3rem;">
    <div class="card" style="display: flex; align-items: center; gap: 1.2rem;">
        <div style="background: #ebf4ff; color: #2b6cb0; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary-color);">{{ \App\Models\Property::count() }}</div>
            <div style="font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Propiedades</div>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; gap: 1.2rem;">
        <div style="background: #faf5ff; color: #805ad5; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary-color);">{{ \App\Models\Lease::where('is_active', true)->count() }}</div>
            <div style="font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Contratos Activos</div>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; gap: 1.2rem;">
        <div style="background: #fffaf0; color: #dd6b20; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary-color);">{{ \App\Models\Lease::whereMonth('end_date', now()->month)->whereYear('end_date', now()->year)->count() }}</div>
            <div style="font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Contratos que vencen este mes</div>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; gap: 1.2rem;">
        <div style="background: #fff5f5; color: #e53e3e; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary-color);">{{ \App\Models\Collection::where('status', '!=', 'paid')->count() }}</div>
            <div style="font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Cobros Pendientes</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem;">
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Próximos Vencimientos</h3>
        <p style="color: var(--text-light); font-size: 0.9rem;">No hay vencimientos cercanos en los próximos 30 días.</p>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Actividad Reciente</h3>
        <p style="color: var(--text-light); font-size: 0.9rem;">El historial de actividad aparecerá aquí.</p>
    </div>
</div>
@endsection
