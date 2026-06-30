@extends('layouts.app')

@section('title', '| Renovar Contrato')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="{{ route('leases.show', $lease) }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver al contrato
            </a>
            <h1 style="color: var(--primary-color);">Renovación de Contrato</h1>
            <p style="color: var(--text-light);">Generando renovación para <strong>{{ $lease->property->location }}</strong></p>
        </div>
        <div style="background: #EBF8FF; color: #2B6CB0; padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid #BEE3F8; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 0.3rem;">Depósito en Poder</div>
            <div style="font-size: 1.4rem; font-weight: 900;">${{ number_format($lease->security_deposit_amount, 2) }}</div>
            <input type="hidden" id="old_deposit_value" value="{{ $lease->security_deposit_amount }}">
        </div>
    </div>

    <form action="{{ route('leases.renew.store', $lease) }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 2rem; margin-bottom: 2rem;">
            
            <!-- Columna Izquierda -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Partes -->
                <div class="card">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; font-size: 1rem;">Partes Vinculadas</h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="background: #f8fafc; padding: 1rem; border-radius: 10px; border: 1px solid #edf2f7;">
                            <label style="display: block; font-size: 0.7rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; margin-bottom: 0.3rem;">Inquilino</label>
                            <div style="font-weight: 700; color: var(--primary-color);">{{ $lease->tenant->name }}</div>
                        </div>
                        <div style="background: #f8fafc; padding: 1rem; border-radius: 10px; border: 1px solid #edf2f7;">
                            <label style="display: block; font-size: 0.7rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; margin-bottom: 0.3rem;">Propiedad</label>
                            <div style="font-weight: 700; color: var(--primary-color);">{{ $lease->property->location }}</div>
                        </div>
                    </div>
                </div>

                <!-- Revisión -->
                <div class="card" style="border-left: 5px solid #ECC94B;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; font-size: 1rem;">Revisión de la Propiedad</h3>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.7rem; cursor: pointer; background: #fffaf0; padding: 1.2rem; border-radius: 12px; border: 1px solid #feebc8;">
                            <input type="checkbox" name="property_review_status" value="1" required style="width: 22px; height: 22px; cursor: pointer;">
                            <span style="font-weight: 700; color: #744210;">¿Se realizó la inspección satisfactoriamente?</span>
                        </label>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-light);">Notas de la Inspección</label>
                        <textarea name="property_review_notes" placeholder="Describa el estado general, arreglos realizados o pendientes..." style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--secondary-color); min-height: 120px; font-family: inherit; resize: vertical;"></textarea>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Condiciones -->
                <div class="card">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; font-size: 1rem;">Nuevas Condiciones del Contrato</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light);">INICIO RENOVACIÓN</label>
                            <input type="date" name="start_date" value="{{ \Carbon\Carbon::parse($lease->end_date)->addDay()->format('Y-m-d') }}" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--secondary-color);">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light);">FIN RENOVACIÓN</label>
                            <input type="date" name="end_date" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--secondary-color);">
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--primary-color);">NUEVO PRECIO BASE MENSUAL ($)</label>
                        <input type="number" step="0.01" name="base_price" id="new_base_price" required placeholder="0.00" style="width: 100%; padding: 1rem; border-radius: 12px; border: 2px solid var(--accent-color); font-weight: 800; font-size: 1.5rem; color: var(--primary-color);">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                        <!-- Honorarios -->
                        <div style="background: #f8fafc; padding: 1.2rem; border-radius: 15px; border: 1px solid #edf2f7;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <label style="font-weight: 800; color: var(--primary-color); font-size: 0.75rem; text-transform: uppercase;">Honorarios</label>
                                <button type="button" onclick="copyValue('new_base_price', 'agency_fee_amount')" style="background: var(--secondary-color); color: var(--primary-color); border: none; border-radius: 6px; padding: 0.3rem 0.6rem; font-size: 0.7rem; font-weight: 800; cursor: pointer;">= 1 Mes</button>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 80px; gap: 0.5rem;">
                                <div>
                                    <span style="font-size: 0.7rem; color: #a0aec0; font-weight: 700;">Monto Total</span>
                                    <input type="number" step="0.01" name="agency_fee_amount" id="agency_fee_amount" placeholder="0.00" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0; font-weight: 700;">
                                </div>
                                <div>
                                    <span style="font-size: 0.7rem; color: #a0aec0; font-weight: 700;">Cuotas</span>
                                    <input type="number" name="initial_fee_installments" value="1" min="1" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0; font-weight: 700; text-align: center;">
                                </div>
                            </div>
                        </div>

                        <!-- Depósito -->
                        <div style="background: #f8fafc; padding: 1.2rem; border-radius: 15px; border: 1px solid #edf2f7;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <label style="font-weight: 800; color: var(--primary-color); font-size: 0.75rem; text-transform: uppercase;">Depósito (Diferencia)</label>
                                <button type="button" onclick="suggestDepositDiff()" style="background: #48BB78; color: white; border: none; border-radius: 6px; padding: 0.3rem 0.6rem; font-size: 0.7rem; font-weight: 800; cursor: pointer;">Sugerir Dif.</button>
                            </div>
                            <div>
                                <span style="font-size: 0.7rem; color: #a0aec0; font-weight: 700;">Monto a Cobrar</span>
                                <input type="number" step="0.01" name="security_deposit_diff" id="security_deposit_diff" placeholder="0.00" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0; font-weight: 700; color: #38A169;">
                            </div>
                            <p style="font-size: 0.65rem; color: #718096; margin-top: 0.5rem; font-style: italic;">Se sumará al depósito actual.</p>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--secondary-color); padding-top: 1.5rem;">
                         <label style="display: block; margin-bottom: 1rem; font-weight: 800; color: var(--primary-color); text-transform: uppercase; font-size: 0.75rem;">Actualización</label>
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; background: #edf2f7; padding: 0.3rem; border-radius: 10px;">
                            <button type="button" onclick="setUpdateType('fixed')" id="btn-fixed" class="btn" style="flex: 1; font-size: 0.8rem; background: var(--accent-color); color: white;">Fijo (%)</button>
                            <button type="button" onclick="setUpdateType('indexed')" id="btn-indexed" class="btn" style="flex: 1; font-size: 0.8rem; background: transparent; color: var(--text-light);">Indexado</button>
                            <input type="hidden" name="update_type" id="update_type" value="fixed">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div id="update-value-container">
                                <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700; color: var(--text-light);">AUMENTO (%)</label>
                                <input type="number" step="0.01" name="update_value" placeholder="15" style="width: 100%; padding: 0.7rem; border-radius: 10px; border: 1px solid var(--secondary-color);">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700; color: var(--text-light);">CADA (MESES)</label>
                                <input type="number" name="update_frequency_months" value="6" min="1" style="width: 100%; padding: 0.7rem; border-radius: 10px; border: 1px solid var(--secondary-color);">
                            </div>
                        </div>
                        <div id="index-name-container" style="display: none; margin-top: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700; color: var(--text-light);">SELECCIONAR ÍNDICE</label>
                            <select name="index_type_id" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--secondary-color); background: white;">
                                <option value="">-- Seleccionar --</option>
                                @foreach($indexTypes as $index)
                                    <option value="{{ $index->id }}">{{ $index->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                    </div>

                    <!-- Facturación -->
                    <div style="border-top: 1px solid var(--secondary-color); padding-top: 1.5rem; margin-top: 1.5rem;">
                         <label style="display: block; margin-bottom: 1rem; font-weight: 800; color: #2B6CB0; text-transform: uppercase; font-size: 0.75rem;">Facturación Oficial (IVA/IB)</label>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" id="invoicing_enabled" name="invoicing_enabled" value="1" onchange="toggleInvoicing()" {{ old('invoicing_enabled', $lease->invoicing_enabled) ? 'checked' : '' }} style="margin-right: 0.5rem; width: 1.2rem; height: 1.2rem; accent-color: #3182CE;">
                                <span style="font-weight: 600; color: #2D3748;">¿Requiere Facturación Parcial?</span>
                            </label>
                        </div>
                        <div id="invoicing_percentage_container" style="display: {{ old('invoicing_enabled', $lease->invoicing_enabled) ? 'block' : 'none' }}; margin-top: 1rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Porcentaje a Facturar (%)</label>
                            <input type="number" name="invoicing_percentage" id="invoicing_percentage" value="{{ old('invoicing_percentage', $lease->invoicing_percentage) }}" min="1" max="99" step="1" placeholder="Ej: 50" style="width: 100%; padding: 0.7rem; border-radius: 10px; border: 1px solid var(--secondary-color);">
                            @error('invoicing_percentage') <span style="color: #E53E3E; font-size: 0.75rem; font-weight: 600;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1.5rem; justify-content: flex-end; margin-top: 2rem; align-items: center;">
            <a href="{{ route('leases.show', $lease) }}" style="text-decoration: none; color: #a0aec0; font-weight: 700;">Cancelar</a>
            <button type="submit" class="btn btn-primary" style="padding: 1.2rem 4rem; font-size: 1.2rem; border-radius: 15px; font-weight: 800; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">Confirmar Renovación</button>
        </div>
    </form>
</div>

<script>
    function copyValue(sourceId, targetId) {
        const val = document.getElementById(sourceId).value;
        if (val) {
            document.getElementById(targetId).value = val;
        } else {
            alert('Primero ingresa el Nuevo Precio Base');
        }
    }

    function suggestDepositDiff() {
        const newPrice = parseFloat(document.getElementById('new_base_price').value) || 0;
        const oldDeposit = parseFloat(document.getElementById('old_deposit_value').value) || 0;
        
        if (newPrice > 0) {
            const diff = Math.max(0, newPrice - oldDeposit);
            document.getElementById('security_deposit_diff').value = diff.toFixed(2);
        } else {
            alert('Primero ingresa el Nuevo Precio Base');
        }
    }

    function setUpdateType(type) {
        document.getElementById('update_type').value = type;
        const btnFixed = document.getElementById('btn-fixed');
        const btnIndexed = document.getElementById('btn-indexed');
        const indexContainer = document.getElementById('index-name-container');
        const valueContainer = document.getElementById('update-value-container');

        if (type === 'fixed') {
            btnFixed.style.background = 'var(--accent-color)';
            btnFixed.style.color = 'white';
            btnIndexed.style.background = 'transparent';
            btnIndexed.style.color = 'var(--text-light)';
            indexContainer.style.display = 'none';
            valueContainer.style.display = 'block';
        } else {
            btnIndexed.style.background = 'var(--accent-color)';
            btnIndexed.style.color = 'white';
            btnFixed.style.background = 'transparent';
            btnFixed.style.color = 'var(--text-light)';
            indexContainer.style.display = 'block';
            valueContainer.style.display = 'none';
        }
    }

    function toggleInvoicing() {
        const isEnabled = document.getElementById('invoicing_enabled').checked;
        const container = document.getElementById('invoicing_percentage_container');
        const input = document.getElementById('invoicing_percentage');
        
        if (isEnabled) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            input.value = '';
        }
    }
</script>
@endsection
