@extends('layouts.app')

@section('title', '| Generación Masiva de Rendiciones')

@section('content')
<div style="margin-bottom: 2.5rem;">
    <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Borrador de Rendiciones: {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}</h1>
    <p style="color: var(--text-light); margin-top: 0.5rem;">Revisa y selecciona los propietarios para los cuales deseas generar la rendición mensual.</p>
</div>

<form action="{{ route('settlements.bulk_store') }}" method="POST">
    @csrf
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="year" value="{{ $year }}">

    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 1rem; text-align: center;"><input type="checkbox" id="selectAll" checked onchange="toggleAll(this)"></th>
                        <th style="padding: 1rem; text-align: left; color: var(--text-light);">Propietario</th>
                        <th style="padding: 1rem; text-align: right; color: var(--text-light);">Cobros Brutos (+)</th>
                        <th style="padding: 1rem; text-align: right; color: var(--text-light);">Gastos Dueño (-)</th>
                        <th style="padding: 1rem; text-align: right; color: var(--text-light);">Honorarios Inmo. (-)</th>
                        <th style="padding: 1rem; text-align: right; color: var(--text-light);">Neto a Rendir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previews as $index => $preview)
                        <tr style="border-bottom: 1px solid #edf2f7;" class="preview-row">
                            <td style="padding: 1rem; text-align: center;">
                                <input type="checkbox" name="owners[{{ $index }}][selected]" checked value="1">
                                <input type="hidden" name="owners[{{ $index }}][owner_id]" value="{{ $preview['owner']->id }}">
                                <input type="hidden" name="owners[{{ $index }}][rent_total]" class="row-rent-total" value="{{ $preview['rent_total'] }}">
                                <input type="hidden" name="owners[{{ $index }}][total_income]" class="row-income" value="{{ $preview['income'] }}">
                                <input type="hidden" name="owners[{{ $index }}][total_expense]" class="row-expenses" value="{{ $preview['expenses'] }}">
                                <input type="hidden" name="owners[{{ $index }}][agency_commission]" class="row-commission-val" value="{{ $preview['agency_commission'] }}">
                                <input type="hidden" name="owners[{{ $index }}][net_amount]" class="row-net-val" value="{{ $preview['net'] }}">
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600;">{{ $preview['owner']->name }}</div>
                            </td>
                            <td style="padding: 1rem; text-align: right; color: #48BB78; font-weight: 600;">${{ number_format($preview['income'], 2) }}</td>
                            <td style="padding: 1rem; text-align: right; color: #E53E3E; font-weight: 600;">${{ number_format($preview['expenses'], 2) }}</td>
                            <td style="padding: 1rem; text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                    <input type="number" step="0.1" class="row-percentage" value="{{ $preview['owner']->commission_percentage ?? 10 }}" style="width: 70px; padding: 0.4rem; border: 1px solid #cbd5e0; border-radius: 8px; text-align: center; font-weight: 700; color: #2b6cb0;" oninput="updateRowCalc(this)">
                                    <span style="font-size: 0.8rem; color: var(--text-light); font-weight: 700;">%</span>
                                </div>
                                <div class="row-commission-display" style="font-size: 0.9rem; font-weight: 800; color: #4299E1; margin-top: 0.3rem;">
                                    ${{ number_format($preview['agency_commission'], 2) }}
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: right; font-weight: 900; font-size: 1.2rem; color: var(--primary-color);" class="row-net-display">
                                ${{ number_format($preview['net'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 3rem; display: flex; justify-content: space-between; align-items: center; border-top: 2px solid #edf2f7; padding-top: 2rem;">
            <a href="{{ route('settlements.index') }}" class="btn" style="background: white; border: 1px solid #d2d6dc; color: #475569; padding: 0.8rem 2rem; font-weight: 600;">Cancelar</a>
            <button type="submit" class="btn btn-primary" style="padding: 1rem 3.5rem; font-weight: 800; letter-spacing: 0.02em; box-shadow: 0 4px 12px rgba(49, 151, 149, 0.25);">CONFIRMAR Y GENERAR RENDICIONES</button>
        </div>
    </div>
</form>

<script>
    function toggleAll(source) {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"][name*="[selected]"]');
        for(let i=0; i<allCheckboxes.length; i++) {
            allCheckboxes[i].checked = source.checked;
        }
    }

    function updateRowCalc(input) {
        const row = input.closest('.preview-row');
        const percentage = parseFloat(input.value || 0);
        const rentTotal = parseFloat(row.querySelector('.row-rent-total').value);
        const income = parseFloat(row.querySelector('.row-income').value);
        const expenses = parseFloat(row.querySelector('.row-expenses').value);

        // Nuevo cálculo de comisión
        const newCommission = rentTotal * (percentage / 100);
        const newNet = income - expenses - newCommission;

        // Actualizar inputs ocultos que se envían al form
        row.querySelector('.row-commission-val').value = newCommission.toFixed(2);
        row.querySelector('.row-net-val').value = newNet.toFixed(2);

        // Actualizar displays visuales
        row.querySelector('.row-commission-display').innerText = '$' + newCommission.toLocaleString('es-AR', { minimumFractionDigits: 2 });
        row.querySelector('.row-net-display').innerText = '$' + newNet.toLocaleString('es-AR', { minimumFractionDigits: 2 });
    }
</script>
@endsection
