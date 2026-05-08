@extends('layouts.app')

@section('title', '| Resumen Mensual')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="color: var(--primary-color);">Resumen de Cobros</h1>
            <p style="color: var(--text-light);">Contrato: {{ $lease->property->location }} • Inquilino: {{ $lease->tenant->name }}</p>
        </div>
        <form action="{{ route('leases.summary', $lease) }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="month" name="month" value="{{ $month }}" style="padding: 0.5rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Ver Mes</button>
        </form>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="background: var(--primary-color); color: white; padding: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 2.5rem;">${{ number_format($total, 2) }}</h2>
                <p style="opacity: 0.8; text-transform: uppercase; font-size: 0.8rem; font-weight: 700; letter-spacing: 1px;">Total a Cobrar {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</p>
            </div>
            <div style="text-align: right;">
                <button onclick="window.print()" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">Imprimir Resumen</button>
            </div>
        </div>

        <div style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Desglose del Mes</h3>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr style="border-bottom: 1px solid var(--secondary-color);">
                        <td style="padding: 1rem 0; font-weight: 600;">Precio de Alquiler Base</td>
                        <td style="padding: 1rem 0; text-align: right;">${{ number_format($basePrice, 2) }}</td>
                    </tr>
                    
                    @foreach($lease->fixedCharges as $charge)
                        <tr style="border-bottom: 1px solid var(--secondary-color);">
                            <td style="padding: 1rem 0; color: var(--text-light);">{{ $charge->name }} (Fijo)</td>
                            <td style="padding: 1rem 0; text-align: right;">${{ number_format($charge->amount, 2) }}</td>
                        </tr>
                    @endforeach

                    @foreach($extraCharges as $extra)
                        <tr style="border-bottom: 1px solid var(--secondary-color);">
                            <td style="padding: 1rem 0; color: var(--text-light);">
                                {{ $extra->description }}
                                @if($extra->total_installments > 1)
                                    <span style="font-size: 0.75rem; background: var(--secondary-color); padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.5rem;">Cuota {{ $extra->installment_number }}/{{ $extra->total_installments }}</span>
                                @endif
                            </td>
                            <td style="padding: 1rem 0; text-align: right;">${{ number_format($extra->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="padding: 1.5rem 0; font-size: 1.25rem; font-weight: 700; color: var(--primary-color);">TOTAL FINAL</td>
                        <td style="padding: 1.5rem 0; text-align: right; font-size: 1.25rem; font-weight: 700; color: var(--accent-color);">${{ number_format($total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
        <a href="{{ route('leases.show', $lease) }}" class="btn" style="background: var(--secondary-color);">Volver al Contrato</a>
    </div>
</div>
@endsection
