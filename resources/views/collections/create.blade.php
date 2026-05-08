@extends('layouts.app')

@section('title', '| Generar Cobros')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('collections.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver
        </a>
        <h1 style="color: var(--primary-color);">Generar Cobros Mensuales</h1>
    </div>

    <form id="period-selector" action="{{ route('collections.create') }}" method="GET" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end; background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--secondary-color);">
        <div style="flex: 1;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Seleccionar Mes</label>
            <select name="month" onchange="this.form.submit()" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                @endfor
            </select>
        </div>
        <div style="flex: 1;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Seleccionar Año</label>
            <select name="year" onchange="this.form.submit()" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                @for($y=date('Y')+1; $y>=2024; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <!-- El botón ahora es opcional pero lo dejamos por UX -->
        <button type="submit" class="btn" style="background: var(--primary-color); color: white; padding: 0.7rem 2rem; display: none;">Ver Contratos</button>
    </form>

    <form action="{{ route('collections.store') }}" method="POST">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">

        <div class="card">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Contratos Vigentes para {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</h3>
            
            @if($leases->isEmpty())
                <p style="text-align: center; color: var(--text-light); padding: 2rem;">No hay contratos activos para este periodo.</p>
            @else
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid var(--secondary-color);">
                            <th style="padding: 1rem; width: 40px;"><input type="checkbox" id="select-all" checked></th>
                            <th style="padding: 1rem;">Propiedad / Inquilino</th>
                            <th style="padding: 1rem;">Monto Alquiler Est.</th>
                            <th style="padding: 1rem;">Próxima Act.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leases as $lease)
                            <tr style="border-bottom: 1px solid var(--secondary-color); opacity: {{ $lease->has_error ? '0.7' : '1' }};">
                                <td style="padding: 1rem;">
                                    <input type="checkbox" name="lease_ids[]" value="{{ $lease->id }}" class="lease-check" {{ $lease->has_error ? 'disabled' : 'checked' }}>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 600;">{{ $lease->property->location }}</div>
                                    <div style="font-size: 0.85rem; color: var(--text-light);">{{ $lease->tenant->name }}</div>
                                    @if($lease->has_error)
                                        <div style="font-size: 0.75rem; color: #e53e3e; font-weight: 700; margin-top: 0.3rem;">⚠️ {{ $lease->error_msg }}</div>
                                    @endif
                                </td>
                                <td style="padding: 1rem; font-weight: 700; color: {{ $lease->has_error ? '#718096' : 'var(--accent-color)' }};">
                                    @if($lease->has_error)
                                        N/A
                                    @else
                                        ${{ number_format($lease->projected_rent, 2) }}
                                    @endif
                                </td>
                                <td style="padding: 1rem; font-size: 0.85rem; color: var(--text-light);">
                                    {{ $lease->update_type === 'fixed' ? 'Fija' : 'Indexada' }} ({{ $lease->update_frequency_months }}m)
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">Generar Borradores de Cobro</button>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
    document.getElementById('select-all').addEventListener('click', function() {
        document.querySelectorAll('.lease-check').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
