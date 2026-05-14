@extends('layouts.app')

@section('title', '| Configuración')

@section('content')
<div style="margin-bottom: 3rem;">
    <h1 style="color: var(--primary-color); font-size: 2.5rem; margin: 0;">Configuración del Sistema</h1>
    <p style="color: var(--text-light); font-size: 1.1rem; margin-top: 0.5rem;">Selecciona una categoría para administrar los parámetros maestros.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
    
    <!-- UBICACIÓN -->
    <a href="{{ route('settings.locations') }}" class="config-card" style="text-decoration: none; color: inherit;">
        <div class="card" style="height: 100%; transition: all 0.3s ease; border-top: 4px solid #4299E1; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 2.5rem;">
            <div style="background: #EBF8FF; color: #3182CE; width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            </div>
            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color);">Ubicación</h3>
            <p style="font-size: 0.9rem; color: var(--text-light); margin: 0;">Provincias y localidades disponibles para las propiedades.</p>
        </div>
    </a>

    <!-- INMUEBLES -->
    <a href="{{ route('settings.property_types') }}" class="config-card" style="text-decoration: none; color: inherit;">
        <div class="card" style="height: 100%; transition: all 0.3s ease; border-top: 4px solid #48BB78; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 2.5rem;">
            <div style="background: #F0FFF4; color: #38A169; width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color);">Inmuebles</h3>
            <p style="font-size: 0.9rem; color: var(--text-light); margin: 0;">Tipos de inmuebles (Casa, Depto, Local, etc.)</p>
        </div>
    </a>

    <!-- ÍNDICES -->
    <a href="{{ route('settings.indices') }}" class="config-card" style="text-decoration: none; color: inherit;">
        <div class="card" style="height: 100%; transition: all 0.3s ease; border-top: 4px solid #ED8936; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 2.5rem;">
            <div style="background: #FFFAF0; color: #DD6B20; width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color);">Actualización</h3>
            <p style="font-size: 0.9rem; color: var(--text-light); margin: 0;">Índices de indexación mensual (IPC, ICL, etc.)</p>
        </div>
    </a>

    <!-- TESORERÍA -->
    <a href="{{ route('settings.accounts') }}" class="config-card" style="text-decoration: none; color: inherit;">
        <div class="card" style="height: 100%; transition: all 0.3s ease; border-top: 4px solid var(--primary-color); display: flex; flex-direction: column; align-items: center; text-align: center; padding: 2.5rem;">
            <div style="background: #F7FAFC; color: var(--primary-color); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
            </div>
            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color);">Tesorería</h3>
            <p style="font-size: 0.9rem; color: var(--text-light); margin: 0;">Gestión de métodos de pago y cuentas.</p>
        </div>
    </a>

    <!-- CATEGORÍAS -->
    <a href="{{ route('settings.categories') }}" class="config-card" style="text-decoration: none; color: inherit;">
        <div class="card" style="height: 100%; transition: all 0.3s ease; border-top: 4px solid #9F7AEA; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 2.5rem;">
            <div style="background: #FAF5FF; color: #805AD5; width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
            </div>
            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color);">Plan de Cuentas</h3>
            <p style="font-size: 0.9rem; color: var(--text-light); margin: 0;">Categorías de ingresos y gastos para reportes.</p>
        </div>
    </a>

</div>

<style>
    .config-card:hover .card {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: transparent;
    }
</style>
@endsection
