@extends('layouts.app')

@section('title', '| Rendiciones a Clientes')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Rendiciones</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Liquida cobros y gastos mensuales a los propietarios.</p>
    </div>
</div>

<div class="card" style="padding: 2rem; margin-bottom: 2rem;">
    <h3 style="margin: 0 0 1.5rem; color: var(--primary-color);">Generar Nueva Rendición</h3>
    <form action="{{ route('settlements.create') }}" method="GET" style="display: grid; grid-template-columns: 1fr 120px 120px auto; gap: 1rem; align-items: end;">
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Propietario (Opcional para Masivo)</label>
            <select name="owner_id" style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px;">
                <option value="">TODOS LOS PROPIETARIOS</option>
                @foreach($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Mes</label>
            <input type="number" name="month" min="1" max="12" value="{{ date('n') }}" required style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Año</label>
            <input type="number" name="year" value="{{ date('Y') }}" required style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px;">
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 0.8rem 1.5rem;">Calcular</button>
        </div>
    </form>
</div>

<div class="card" style="padding: 2rem;">
    <h3 style="margin: 0 0 1.5rem; color: var(--primary-color);">Historial de Rendiciones</h3>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #edf2f7;">
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Periodo</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Propietario</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-light);">Cobros</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-light);">Gastos</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-light);">Neto a Pagar</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-light);">Estado</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-light);">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settlements as $settlement)
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 1rem; font-weight: 600;">{{ str_pad($settlement->month, 2, '0', STR_PAD_LEFT) }}/{{ $settlement->year }}</td>
                        <td style="padding: 1rem;">
                            <div style="font-weight: 600;">{{ $settlement->owner->name }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-light);">DNI/CUIT: {{ $settlement->owner->dni_cuit ?? 'N/A' }}</div>
                        </td>
                        <td style="padding: 1rem; text-align: right; color: #48BB78;">${{ number_format($settlement->total_income, 2) }}</td>
                        <td style="padding: 1rem; text-align: right; color: #E53E3E;">${{ number_format($settlement->total_expense, 2) }}</td>
                        <td style="padding: 1rem; text-align: right; font-weight: 800;">${{ number_format($settlement->net_amount, 2) }}</td>
                        <td style="padding: 1rem; text-align: center;">
                            @if($settlement->status === 'paid')
                                <span class="badge" style="background: #C6F6D5; color: #22543D;">PAGADO</span>
                            @else
                                <span class="badge" style="background: #FEFCBF; color: #744210;">PENDIENTE PAGO</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <a href="{{ route('settlements.show', $settlement) }}" class="btn" style="background: #edf2f7; padding: 0.4rem 1rem; font-size: 0.8rem;">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay rendiciones registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $settlements->links() }}
    </div>
</div>
@endsection
