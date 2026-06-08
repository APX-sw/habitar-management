@extends('layouts.app')

@section('title', '| Historial de Arqueos')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Historial de Arqueos de Caja</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Registro de todos los cierres y arqueos físicos de caja.</p>
    </div>
    
    <a href="{{ route('cash-register-closures.create') }}" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700; text-decoration: none;">➕ Nuevo Arqueo</a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
            <tr>
                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; text-transform: uppercase; color: #718096;">Fecha</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; text-transform: uppercase; color: #718096;">Usuario</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; text-transform: uppercase; color: #718096;">Saldo Sistema</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; text-transform: uppercase; color: #718096;">Físico</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; text-transform: uppercase; color: #718096;">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($closures as $closure)
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 1rem 1.5rem; font-weight: 600;">
                        {{ \Carbon\Carbon::parse($closure->closure_date)->format('d/m/Y') }}
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        {{ $closure->user->name ?? 'Sistema' }}
                    </td>
                    <td style="padding: 1rem 1.5rem; color: #4A5568;">
                        ${{ number_format($closure->system_balance, 2) }}
                    </td>
                    <td style="padding: 1rem 1.5rem; font-weight: 700;">
                        ${{ number_format($closure->physical_balance, 2) }}
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        @if($closure->difference == 0)
                            <span style="color: #48BB78; font-weight: 800;">OK ($0.00)</span>
                        @elseif($closure->difference > 0)
                            <span style="color: #4299E1; font-weight: 800;">+${{ number_format($closure->difference, 2) }} (Sobrante)</span>
                        @else
                            <span style="color: #E53E3E; font-weight: 800;">-${{ number_format(abs($closure->difference), 2) }} (Faltante)</span>
                        @endif
                        @if($closure->notes)
                            <div style="font-size: 0.75rem; color: #a0aec0; margin-top: 0.3rem;">{{ $closure->notes }}</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay arqueos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
