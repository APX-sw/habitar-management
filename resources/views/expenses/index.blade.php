@extends('layouts.app')

@section('title', '| Gastos')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Gastos</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Registro de egresos generales o de propiedades.</p>
    </div>
    <a href="{{ route('expenses.create') }}" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700;">➕ Registrar Gasto</a>
</div>

<div class="card" style="padding: 2rem;">
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #edf2f7;">
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Fecha</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Propiedad (Opcional)</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Descripción</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-light);">Cuenta (Origen)</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-light);">Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 1rem;">{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                        <td style="padding: 1rem;">
                            @if($expense->property)
                                <a href="{{ route('properties.show', $expense->property) }}" style="color: var(--accent-color); text-decoration: none; font-weight: 600;">{{ $expense->property->location }}</a>
                            @else
                                <span style="color: var(--text-light); font-style: italic;">Gasto Inmobiliaria</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; font-weight: 500;">{{ $expense->description }}</td>
                        <td style="padding: 1rem;">{{ $expense->account->name }}</td>
                        <td style="padding: 1rem; text-align: right; font-weight: 800; color: #E53E3E;">
                            ${{ number_format($expense->amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay gastos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $expenses->links() }}
    </div>
</div>
@endsection
