@extends('layouts.app')

@section('title', '| Nuevo Arqueo de Caja')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('cash-register-closures.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver al Historial</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Nuevo Arqueo de Caja</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Ingresa la cantidad de billetes para calcular el dinero físico.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <form action="{{ route('cash-register-closures.store') }}" method="POST" id="closureForm">
        @csrf
        <div class="card" style="padding: 2.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Fecha del Arqueo</label>
                    <input type="date" name="closure_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 600;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Saldo Esperado (Sistema)</label>
                    <div style="background: #f8fafc; padding: 0.8rem; border-radius: 8px; border: 1px solid #edf2f7; font-weight: 800; color: var(--primary-color); font-size: 1.1rem;">
                        ${{ number_format($systemBalance, 2) }}
                    </div>
                    <input type="hidden" name="system_balance" id="systemBalance" value="{{ $systemBalance }}">
                </div>
            </div>

            <h3 style="color: var(--primary-color); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid #edf2f7;">Conteo de Billetes</h3>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
                @php
                    $denominations = [20000, 10000, 2000, 1000, 500, 200, 100, 50, 20, 10];
                @endphp
                @foreach($denominations as $val)
                    <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 0.8rem 1.2rem; border-radius: 10px; border: 1px solid #edf2f7;">
                        <span style="font-weight: 800; color: #4A5568; font-size: 1.1rem;">$ {{ number_format($val, 0, ',', '.') }}</span>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="color: #a0aec0; font-size: 0.8rem; font-weight: 600;">x</span>
                            <input type="number" name="bills[{{ $val }}]" class="bill-input" data-value="{{ $val }}" value="0" min="0" style="width: 80px; padding: 0.5rem; border-radius: 6px; border: 1px solid #cbd5e0; text-align: center; font-weight: 700;">
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Notas / Observaciones (Opcional)</label>
                <textarea name="notes" rows="3" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;" placeholder="Ej: Falta para cambio, se retiró dinero para gastos..."></textarea>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1.1rem;">Guardar Arqueo</button>
            </div>
        </div>
    </form>

    <div>
        <div class="card" style="padding: 2rem; position: sticky; top: 2rem;">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--primary-color); text-align: center;">Resumen</h3>
            
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Físico Contado</div>
                    <div id="physicalBalanceDisplay" style="font-size: 2rem; font-weight: 900; color: #48BB78;">$0.00</div>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Diferencia</div>
                    <div id="differenceDisplay" style="font-size: 1.5rem; font-weight: 800; color: #A0AEC0;">$0.00</div>
                    <div id="differenceStatus" style="font-size: 0.8rem; font-weight: 700; margin-top: 0.3rem;">OK</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.bill-input');
        const systemBalance = parseFloat(document.getElementById('systemBalance').value);
        
        function calculateTotals() {
            let physicalTotal = 0;
            inputs.forEach(input => {
                const val = parseFloat(input.dataset.value);
                const qty = parseInt(input.value) || 0;
                physicalTotal += (val * qty);
            });

            document.getElementById('physicalBalanceDisplay').innerText = '$' + physicalTotal.toLocaleString('es-AR', {minimumFractionDigits: 2});

            const diff = physicalTotal - systemBalance;
            const diffDisplay = document.getElementById('differenceDisplay');
            const diffStatus = document.getElementById('differenceStatus');

            if (diff === 0) {
                diffDisplay.innerText = '$0.00';
                diffDisplay.style.color = '#48BB78';
                diffStatus.innerText = 'OK (Coincide)';
                diffStatus.style.color = '#48BB78';
            } else if (diff > 0) {
                diffDisplay.innerText = '+$' + diff.toLocaleString('es-AR', {minimumFractionDigits: 2});
                diffDisplay.style.color = '#4299E1';
                diffStatus.innerText = 'SOBRANTE';
                diffStatus.style.color = '#4299E1';
            } else {
                diffDisplay.innerText = '-$' + Math.abs(diff).toLocaleString('es-AR', {minimumFractionDigits: 2});
                diffDisplay.style.color = '#E53E3E';
                diffStatus.innerText = 'FALTANTE';
                diffStatus.style.color = '#E53E3E';
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculateTotals);
        });

        calculateTotals();
    });
</script>
@endsection
