@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-header {
        margin-bottom: 2rem;
    }
    
    .dashboard-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .dashboard-header p {
        color: var(--text-muted);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        padding: 1.5rem;
        transition: var(--transition);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-title {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .welcome-panel {
        padding: 2rem;
        text-align: center;
        margin-top: 2rem;
        border-left: 4px solid var(--primary-color);
    }
</style>
@endpush

@section('content')
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <p>Resumen general del sistema Habitar.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card glass-panel">
            <div class="stat-title">Propiedades Activas</div>
            <div class="stat-value">124</div>
        </div>
        
        <div class="stat-card glass-panel">
            <div class="stat-title">Contratos Vigentes</div>
            <div class="stat-value">89</div>
        </div>
        
        <div class="stat-card glass-panel">
            <div class="stat-title">Ingresos del Mes</div>
            <div class="stat-value">$1.2M</div>
        </div>
    </div>

    <div class="welcome-panel glass-panel">
        <h2>Bienvenido, {{ Auth::user()->name }}</h2>
        <p style="color: var(--text-muted); margin-top: 1rem;">
            Has iniciado sesión exitosamente con el rol de 
            <strong style="color: var(--primary-color);">{{ Auth::user()->roles->pluck('name')->first() ?? 'Usuario' }}</strong>. 
            El menú de navegación superior incluye el botón para probar el Cierre de Sesión.
        </p>
    </div>
@endsection
