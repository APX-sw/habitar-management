@extends('layouts.app')

@section('title', '| Caja General')

@section('content')
<div style="margin-bottom: 2.5rem;">
    <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Caja General</h1>
    <p style="color: var(--text-light); margin-top: 0.5rem;">Visualiza los saldos y el historial de todas las cuentas de la inmobiliaria.</p>
</div>

<!-- Saldos -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    @foreach($accounts as $account)
        <div class="card" style="padding: 1.5rem; border-left: 5px solid {{ $account->type === 'cash' ? '#48BB78' : '#4299E1' }};">
            <h3 style="margin: 0 0 0.5rem; color: var(--text-light); font-size: 0.9rem; text-transform: uppercase;">{{ $account->name }}</h3>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary-color);">
                ${{ number_format($account->current_balance, 2) }}
            </div>
        </div>
    @endforeach
</div>

<!-- Historial -->
<div class="card" style="padding: 2rem;">
    <h3 style="margin: 0 0 1.5rem; color: var(--primary-color);">Últimos Movimientos</h3>
    
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #edf2f7;">
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Fecha</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Cuenta</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Descripción</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-light);">Ingreso</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-light);">Egreso</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 1rem;">{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}</td>
                        <td style="padding: 1rem; font-weight: 600;">{{ $movement->account->name }}</td>
                        <td style="padding: 1rem; color: var(--text-light);">{{ $movement->description }}</td>
                        <td style="padding: 1rem; text-align: right; font-weight: 700; color: #48BB78;">
                            {{ $movement->type === 'income' ? '$' . number_format($movement->amount, 2) : '-' }}
                        </td>
                        <td style="padding: 1rem; text-align: right; font-weight: 700; color: #E53E3E;">
                            {{ $movement->type === 'expense' ? '$' . number_format($movement->amount, 2) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay movimientos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $movements->links() }}
    </div>
</div>
@endsection
