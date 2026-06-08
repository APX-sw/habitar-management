@extends('layouts.app')

@section('title', '| Resumen de Arqueo')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('cash-register-closures.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver al historial</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Resumen del Cierre de Caja</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Cierre realizado el {{ \Carbon\Carbon::parse($closure->closure_date)->format('d/m/Y H:i') }} por {{ $closure->user->name ?? 'Sistema' }}</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="padding: 1.5rem; border-left: 5px solid #4A5568;">
        <h3 style="margin: 0 0 0.5rem; color: var(--text-light); font-size: 0.85rem; text-transform: uppercase;">Apertura</h3>
        <div style="font-size: 1.5rem; font-weight: 800; color: #4A5568;">
            ${{ number_format($closure->initial_balance, 2) }}
        </div>
        <div style="font-size: 0.75rem; color: #a0aec0; margin-top: 0.2rem;">
            {{ \Carbon\Carbon::parse($closure->opened_at)->format('d/m/Y H:i') }}
        </div>
    </div>
    <div class="card" style="padding: 1.5rem; border-left: 5px solid #3182CE;">
        <h3 style="margin: 0 0 0.5rem; color: var(--text-light); font-size: 0.85rem; text-transform: uppercase;">Saldo Sistema (Teórico)</h3>
        <div style="font-size: 1.5rem; font-weight: 800; color: #3182CE;">
            ${{ number_format($closure->system_balance, 2) }}
        </div>
    </div>
    <div class="card" style="padding: 1.5rem; border-left: 5px solid {{ $closure->difference == 0 ? '#48BB78' : ($closure->difference > 0 ? '#4299E1' : '#E53E3E') }};">
        <h3 style="margin: 0 0 0.5rem; color: var(--text-light); font-size: 0.85rem; text-transform: uppercase;">Saldo Físico (Real)</h3>
        <div style="font-size: 1.5rem; font-weight: 800; color: {{ $closure->difference == 0 ? '#48BB78' : ($closure->difference > 0 ? '#4299E1' : '#E53E3E') }};">
            ${{ number_format($closure->physical_balance, 2) }}
        </div>
        <div style="font-size: 0.85rem; font-weight: 700; color: {{ $closure->difference == 0 ? '#48BB78' : ($closure->difference > 0 ? '#4299E1' : '#E53E3E') }}; margin-top: 0.2rem;">
            Diferencia: ${{ number_format($closure->difference, 2) }}
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Billetes -->
    <div class="card" style="padding: 2rem; align-self: start;">
        <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">Resumen de Billetes</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #edf2f7; text-align: left;">
                    <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Billete</th>
                    <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; text-align: center;">Cantidad</th>
                    <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($closure->bills as $bill)
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 1rem 0.5rem; font-weight: 700; color: #4A5568;">
                            ${{ number_format($bill->bill_value, 0) }}
                        </td>
                        <td style="padding: 1rem 0.5rem; text-align: center; font-weight: 600;">
                            {{ $bill->quantity }}
                        </td>
                        <td style="padding: 1rem 0.5rem; text-align: right; font-weight: 700; color: #2D3748;">
                            ${{ number_format($bill->bill_value * $bill->quantity, 2) }}
                        </td>
                    </tr>
                @endforeach
                @if($closure->bills->isEmpty())
                    <tr>
                        <td colspan="3" style="padding: 1.5rem; text-align: center; color: var(--text-light);">No se detallaron billetes.</td>
                    </tr>
                @endif
            </tbody>
            @if($closure->bills->isNotEmpty())
            <tfoot>
                <tr style="border-top: 2px solid #e2e8f0;">
                    <td colspan="2" style="padding: 1rem 0.5rem; font-weight: 800; color: #2D3748; text-align: right;">Total Físico:</td>
                    <td style="padding: 1rem 0.5rem; font-weight: 800; color: var(--primary-color); text-align: right;">${{ number_format($closure->physical_balance, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
        
        @if($closure->notes)
            <div style="margin-top: 2rem; padding: 1rem; background: #fffaf0; border-left: 4px solid #ed8936; border-radius: 4px;">
                <h4 style="margin: 0 0 0.5rem; color: #c05621; font-size: 0.85rem; text-transform: uppercase;">Notas / Observaciones</h4>
                <p style="margin: 0; font-size: 0.9rem; color: #7b341e;">{{ $closure->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Movimientos -->
    <div class="card" style="padding: 2rem;">
        <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">Movimientos Registrados</h3>
        <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1.5rem;">Todos los movimientos que ocurrieron mientras esta sesión estuvo abierta.</p>
        
        <div style="overflow-y: auto; max-height: 600px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="position: sticky; top: 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <tr style="border-bottom: 2px solid #edf2f7; text-align: left;">
                        <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Fecha</th>
                        <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Concepto / Descripción</th>
                        <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Tipo</th>
                        <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; text-align: right;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalIngresos = 0;
                        $totalEgresos = 0;
                    @endphp
                    @forelse($movements as $mov)
                        @php
                            if($mov->type == 'income') $totalIngresos += $mov->amount;
                            else $totalEgresos += $mov->amount;
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem 0.5rem; font-weight: 600; font-size: 0.85rem; color: #4A5568; white-space: nowrap;">
                                {{ \Carbon\Carbon::parse($mov->created_at)->format('H:i d/m') }}
                            </td>
                            <td style="padding: 1rem 0.5rem;">
                                <div style="font-weight: 700; color: var(--primary-color); font-size: 0.9rem;">{{ $mov->transactionCategory->name ?? 'Sin Categoría' }}</div>
                                <div style="font-size: 0.8rem; color: #a0aec0; margin-top: 0.2rem; max-width: 300px;">{{ $mov->description }}</div>
                            </td>
                            <td style="padding: 1rem 0.5rem;">
                                @if($mov->type === 'income')
                                    <span style="background: #E6FFFA; color: #319795; padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 800;">INGRESO</span>
                                @else
                                    <span style="background: #FFF5F5; color: #C53030; padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 800;">EGRESO</span>
                                @endif
                            </td>
                            <td style="padding: 1rem 0.5rem; text-align: right; font-weight: 800; color: {{ $mov->type === 'income' ? '#48BB78' : '#E53E3E' }};">
                                {{ $mov->type === 'income' ? '+' : '-' }}${{ number_format($mov->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-light);">No se registraron movimientos en caja durante esta sesión.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #edf2f7;">
            <div>
                <span style="display: block; font-size: 0.75rem; color: #718096; text-transform: uppercase; font-weight: 700;">Total Ingresos</span>
                <span style="font-size: 1.2rem; font-weight: 800; color: #48BB78;">+${{ number_format($totalIngresos, 2) }}</span>
            </div>
            <div>
                <span style="display: block; font-size: 0.75rem; color: #718096; text-transform: uppercase; font-weight: 700;">Total Egresos</span>
                <span style="font-size: 1.2rem; font-weight: 800; color: #E53E3E;">-${{ number_format($totalEgresos, 2) }}</span>
            </div>
            <div style="text-align: right;">
                <span style="display: block; font-size: 0.75rem; color: #718096; text-transform: uppercase; font-weight: 700;">Balance del Turno</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color);">${{ number_format($totalIngresos - $totalEgresos, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
