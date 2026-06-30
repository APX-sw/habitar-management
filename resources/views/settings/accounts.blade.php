@extends('layouts.app')

@section('title', '| Gestión de Cuentas')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('settings.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver a Configuración</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Cuentas y Tesorería</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Administra las cuentas bancarias y cajas de efectivo de la agencia.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; align-items: start;">
    
    <!-- Lista de Cuentas -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #edf2f7; background: #f8fafc;">
            <h3 style="margin: 0; color: var(--primary-color); font-size: 1.1rem;">Cuentas Configuradas</h3>
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #edf2f7; background: #fcfcfc;">
                    <th style="padding: 1rem 1.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Nombre de la Cuenta</th>
                    <th style="padding: 1rem 1.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Tipo</th>
                    <th style="padding: 1rem 1.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Estado</th>
                    <th style="padding: 1rem 1.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.2rem 1.5rem;">
                        <div style="font-weight: 700; color: var(--primary-color);">{{ $account->name }}</div>
                    </td>
                    <td style="padding: 1.2rem 1.5rem;">
                        <span class="badge" style="background: #edf2f7; color: #4a5568;">
                            {{ $account->type === 'cash' ? 'Efectivo' : ($account->type === 'bank' ? 'Banco / Transferencia' : 'Otro') }}
                        </span>
                    </td>
                    <td style="padding: 1.2rem 1.5rem;">
                        @if($account->is_active)
                            <span class="badge" style="background: #c6f6d5; color: #22543d;">Activa</span>
                        @else
                            <span class="badge" style="background: #fed7d7; color: #822727;">Inactiva</span>
                        @endif
                    </td>
                    <td style="padding: 1.2rem 1.5rem; text-align: right;">
                        <form action="{{ route('settings.accounts.toggle', $account) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn" style="background: {{ $account->is_active ? '#fff5f5' : '#f0fff4' }}; color: {{ $account->is_active ? '#c53030' : '#276749' }}; font-size: 0.8rem; padding: 0.4rem 0.8rem; border: 1px solid {{ $account->is_active ? '#feb2b2' : '#9ae6b4' }};">
                                {{ $account->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Formulario de Creación -->
    <div class="card" style="padding: 2rem; position: sticky; top: 2rem;">
        <h3 style="margin: 0 0 1.5rem; color: var(--primary-color); font-size: 1.1rem;">Nueva Cuenta</h3>
        <form action="{{ route('settings.accounts.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Nombre de la Cuenta</label>
                <input type="text" name="name" required placeholder="Ej: Galicia Suc. Centro" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Tipo de Cuenta</label>
                <select name="type" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; background: white;">
                    <option value="bank">Banco / Transferencia</option>
                    <option value="cash">Efectivo / Caja</option>
                    <option value="other">Otro</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Crear Cuenta</button>
        </form>

        <div style="margin-top: 2rem; padding: 1rem; background: #fffaf0; border-radius: 10px; border: 1px solid #feebc8;">
            <p style="font-size: 0.8rem; color: #9c4221; margin: 0; line-height: 1.5;">
                <strong>Nota:</strong> Las cuentas activas aparecerán como opciones al registrar cobros, pagos y gastos.
            </p>
        </div>
    </div>

</div>
@endsection
