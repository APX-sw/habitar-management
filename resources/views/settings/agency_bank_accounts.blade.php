@extends('layouts.app')

@section('title', '| Cuentas de Cobro')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="{{ route('settings.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver a Configuración
            </a>
            <h1 style="color: var(--primary-color);">Cuentas Bancarias de Cobro</h1>
            <p style="color: var(--text-light);">Administra las cuentas que se enviarán a los inquilinos para realizar los pagos.</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem;">
        <!-- Formulario de Alta -->
        <div>
            <div class="card">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Nueva Cuenta</h3>
                <form action="{{ route('settings.agency_bank_accounts.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label>Titular de la Cuenta</label>
                        <input type="text" name="holder_name" required placeholder="Ej: Habitar Propiedades SRL">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label>Entidad Bancaria</label>
                        <input type="text" name="bank_entity" required placeholder="Ej: Banco Galicia">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label>CBU</label>
                        <input type="text" name="cbu" required placeholder="22 dígitos">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label>Alias</label>
                        <input type="text" name="alias" required placeholder="Ej: HABITAR.COBROS">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Agregar Cuenta</button>
                </form>
            </div>
        </div>

        <!-- Listado -->
        <div>
            <div class="card" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 1rem; text-align: left; font-size: 0.8rem; color: #718096; text-transform: uppercase;">Cuenta / Banco</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.8rem; color: #718096; text-transform: uppercase;">Datos</th>
                            <th style="padding: 1rem; text-align: center; font-size: 0.8rem; color: #718096; text-transform: uppercase;">Estado</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.8rem; color: #718096; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr style="border-bottom: 1px solid var(--secondary-color);">
                            <td style="padding: 1.2rem 1rem;">
                                <div style="font-weight: 700; color: var(--primary-color);">{{ $account->holder_name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-light);">{{ $account->bank_entity }}</div>
                            </td>
                            <td style="padding: 1.2rem 1rem;">
                                <div style="font-size: 0.85rem; font-family: monospace;">CBU: {{ $account->cbu }}</div>
                                <div style="font-size: 0.85rem; font-weight: 600; color: var(--accent-color);">ALIAS: {{ $account->alias }}</div>
                            </td>
                            <td style="padding: 1.2rem 1rem; text-align: center;">
                                @if($account->is_active)
                                    <span class="badge" style="background: #C6F6D5; color: #22543D;">PREDETERMINADA</span>
                                @else
                                    <form action="{{ route('settings.agency_bank_accounts.default', $account) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn" style="background: #edf2f7; font-size: 0.7rem; padding: 0.4rem 0.8rem;">Activar</button>
                                    </form>
                                @endif
                            </td>
                            <td style="padding: 1.2rem 1rem; text-align: right;">
                                <form action="{{ route('settings.agency_bank_accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('¿Eliminar esta cuenta?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #C53030; cursor: pointer;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($accounts->isEmpty())
                        <tr>
                            <td colspan="4" style="padding: 3rem; text-align: center; color: var(--text-light);">No hay cuentas registradas.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem; background: #FFF5F5; color: #C53030; padding: 1rem; border-radius: 8px; font-size: 0.85rem; display: flex; gap: 0.8rem; align-items: flex-start;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span>La cuenta marcada como <strong>PREDETERMINADA</strong> será la que se incluya automáticamente en los correos de cobro enviados a los inquilinos.</span>
            </div>
        </div>
    </div>
</div>
@endsection
