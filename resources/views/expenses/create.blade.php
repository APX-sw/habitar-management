@extends('layouts.app')

@section('title', '| Registrar Gasto')

@section('content')
<div style="margin-bottom: 2.5rem;">
    <a href="{{ route('expenses.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
        <span style="font-weight: 600;">Volver a Gastos</span>
    </a>
    <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Registrar Gasto</h1>
    <p style="color: var(--text-light); margin-top: 0.5rem;">El gasto generará un egreso inmediato en la Caja seleccionada.</p>
</div>

<div class="card" style="max-width: 600px; padding: 2rem;">
    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Cuenta de Origen (De dónde sale la plata)</label>
            <select name="account_id" required style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px;">
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} (Saldo: ${{ number_format($account->current_balance, 2) }})</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Propiedad (Opcional)</label>
            <select name="property_id" style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px;">
                <option value="">Gasto General de Inmobiliaria</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->location }}</option>
                @endforeach
            </select>
            <small style="color: var(--text-light); display: block; margin-top: 0.3rem;">Si seleccionas una propiedad, el gasto se descontará en la próxima Rendición.</small>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Fecha del Gasto</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Monto</label>
                <input type="number" step="0.01" name="amount" required style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px; font-weight: 700; color: #E53E3E;">
            </div>
        </div>

        <div style="margin-bottom: 2.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Descripción</label>
            <input type="text" name="description" placeholder="Ej: Arreglo termotanque, Resma papel..." required style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px;">
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 1rem 2.5rem;">Guardar Gasto</button>
        </div>
    </form>
</div>
@endsection
