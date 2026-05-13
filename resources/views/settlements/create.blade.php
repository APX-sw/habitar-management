@extends('layouts.app')

@section('title', '| Borrador de Rendición')

@section('content')
<div style="margin-bottom: 2.5rem;">
    <a href="{{ route('settlements.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
        <span style="font-weight: 600;">Volver a Rendiciones</span>
    </a>
    <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Rendición: Mes {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}</h1>
    <p style="color: var(--text-light); margin-top: 0.5rem;">Verifica los saldos antes de generar la liquidación final.</p>
</div>

@if(!$ownerId)
    <div class="card" style="padding: 2rem; text-align: center;">
        <p>Selecciona un propietario en la pantalla anterior.</p>
    </div>
@else
    @php
        $owner = $owners->firstWhere('id', $ownerId);
        $agencyCommission = $rentTotal * ($commissionPercentage / 100);
        $net = $income - $expense - $agencyCommission;
    @endphp

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Ingresos -->
            <div class="card" style="padding: 1.5rem; border-top: 4px solid #48BB78;">
                <h3 style="margin: 0 0 1rem; color: #22543D; font-size: 1.1rem;">Cobros Registrados (+)</h3>
                @forelse($collections as $col)
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #edf2f7;">
                        <div>
                            <span style="font-weight: 600;">{{ $col->lease->property->location }}</span><br>
                            <span style="font-size: 0.8rem; color: var(--text-light);">Inquilino: {{ $col->lease->tenant->name }}</span>
                        </div>
                        <span style="font-weight: 700; color: #48BB78; align-self: center;">${{ number_format($col->total_amount, 2) }}</span>
                    </div>
                @empty
                    <p style="font-size: 0.9rem; color: var(--text-light);">No hay cobros pagados este mes para este propietario.</p>
                @endforelse
                <div style="text-align: right; margin-top: 1rem; font-size: 1.2rem; font-weight: 800; color: #22543D;">
                    Total Ingresos: ${{ number_format($income, 2) }}
                </div>
            </div>

            <!-- Egresos -->
            <div class="card" style="padding: 1.5rem; border-top: 4px solid #E53E3E;">
                <h3 style="margin: 0 0 1rem; color: #9B2C2C; font-size: 1.1rem;">Gastos Registrados (-)</h3>
                @forelse($expenses as $exp)
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #edf2f7;">
                        <div>
                            <span style="font-weight: 600;">{{ $exp->property->location }}</span><br>
                            <span style="font-size: 0.8rem; color: var(--text-light);">{{ $exp->description }} ({{ \Carbon\Carbon::parse($exp->date)->format('d/m') }})</span>
                        </div>
                        <span style="font-weight: 700; color: #E53E3E; align-self: center;">${{ number_format($exp->amount, 2) }}</span>
                    </div>
                @empty
                    <p style="font-size: 0.9rem; color: var(--text-light);">No hay gastos registrados este mes para este propietario.</p>
                @endforelse
                <div style="text-align: right; margin-top: 1rem; font-size: 1.2rem; font-weight: 800; color: #9B2C2C;">
                    Total Egresos: ${{ number_format($expense, 2) }}
                </div>
            </div>

            <!-- Honorarios Inmobiliaria -->
            <div class="card" style="padding: 1.5rem; border-top: 4px solid #4299E1;">
                <h3 style="margin: 0 0 1rem; color: #2B6CB0; font-size: 1.1rem;">Honorarios Inmobiliaria (-)</h3>
                <p style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 1rem;">
                    Se calcula sobre el subtotal de los montos puros de alquiler cobrados (${{ number_format($rentTotal, 2) }}).
                </p>
                <div style="display: flex; align-items: center; justify-content: space-between; background: #ebf8ff; padding: 1rem; border-radius: 8px; border: 1px solid #bee3f8;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #2B6CB0; font-size: 0.9rem;">Porcentaje:</label>
                        <div style="position: relative; width: 80px;">
                            <input type="number" id="commissionPercentageInput" value="{{ $commissionPercentage }}" min="0" max="100" step="0.1" style="width: 100%; padding: 0.5rem; border: 1px solid #90cdf4; border-radius: 6px; font-weight: 700; color: #2B6CB0; background: white; text-align: right; padding-right: 1.5rem;" oninput="recalculateNet()">
                            <span style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); font-weight: 700; color: #2B6CB0;">%</span>
                        </div>
                    </div>
                    <div style="font-size: 1.2rem; font-weight: 800; color: #2B6CB0;" id="commissionAmountDisplay">
                        ${{ number_format($agencyCommission, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="padding: 2rem; background: var(--primary-color); color: white;">
                <h3 style="margin: 0 0 1.5rem; opacity: 0.9; font-size: 1rem; text-transform: uppercase;">Resumen a Pagar</h3>
                
                <div style="margin-bottom: 1rem; display: flex; justify-content: space-between;">
                    <span style="opacity: 0.8;">Propietario:</span>
                    <span style="font-weight: 600;">{{ $owner->name }}</span>
                </div>
                <div style="margin-bottom: 1rem; display: flex; justify-content: space-between;">
                    <span style="opacity: 0.8;">Cobros Brutos:</span>
                    <span style="color: #9AE6B4;">${{ number_format($income, 2) }}</span>
                </div>
                <div style="margin-bottom: 1rem; display: flex; justify-content: space-between;">
                    <span style="opacity: 0.8;">Gastos Dueño:</span>
                    <span style="color: #FEB2B2;">${{ number_format($expense, 2) }}</span>
                </div>
                <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between;">
                    <span style="opacity: 0.8;">Honorarios Inmo:</span>
                    <span style="color: #90CDF4;" id="summaryCommissionDisplay">${{ number_format($agencyCommission, 2) }}</span>
                </div>
                
                <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 1.5rem;">
                    <div style="font-size: 0.8rem; opacity: 0.8; margin-bottom: 0.5rem;">NETO A RENDIR</div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: white;" id="summaryNetDisplay">
                        ${{ number_format($net, 2) }}
                    </div>
                </div>

                <form action="{{ route('settlements.store') }}" method="POST" style="margin-top: 2rem;">
                    @csrf
                    <input type="hidden" name="owner_id" value="{{ $ownerId }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="rent_total" value="{{ $rentTotal }}">
                    <input type="hidden" name="total_income" value="{{ $income }}">
                    <input type="hidden" name="total_expense" value="{{ $expense }}">
                    <input type="hidden" name="agency_commission" id="formAgencyCommission" value="{{ $agencyCommission }}">
                    <input type="hidden" name="net_amount" id="formNetAmount" value="{{ $net }}">
                    
                    <button type="submit" class="btn" style="width: 100%; background: var(--accent-color); color: white; padding: 1rem; font-weight: 700; font-size: 1.1rem;">Guardar e ir a Pago</button>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
<script>
    const rentTotal = {{ $rentTotal }};
    const income = {{ $income }};
    const expense = {{ $expense }};

    function recalculateNet() {
        let percent = parseFloat(document.getElementById('commissionPercentageInput').value) || 0;
        
        let commission = rentTotal * (percent / 100);
        let net = income - expense - commission;

        // Formato para mostrar
        let formattedCommission = '$' + commission.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        let formattedNet = '$' + net.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        // Actualizar UI
        document.getElementById('commissionAmountDisplay').innerText = formattedCommission;
        document.getElementById('summaryCommissionDisplay').innerText = formattedCommission;
        document.getElementById('summaryNetDisplay').innerText = formattedNet;

        // Actualizar inputs del form
        document.getElementById('formAgencyCommission').value = commission.toFixed(2);
        document.getElementById('formNetAmount').value = net.toFixed(2);
    }
</script>
@endsection
