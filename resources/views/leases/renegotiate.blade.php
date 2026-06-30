@extends('layouts.app')

@section('title', '| Renegociar Contrato')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="{{ route('leases.show', $lease) }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver al contrato
            </a>
            <h1 style="color: var(--primary-color);">Renegociación de Contrato</h1>
            <p style="color: var(--text-light);">Modificando condiciones económicas para el periodo vigente.</p>
        </div>
        <div style="background: #FAF5FF; color: #553C9A; padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid #E9D8FD; text-align: center;">
            <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 800;">Alquiler Actual</div>
            <div style="font-size: 1.4rem; font-weight: 900;">${{ number_format($lease->base_price, 2) }}</div>
        </div>
    </div>

    <form action="{{ route('leases.renegotiate.store', $lease) }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            
            <!-- Bloque 1: Resumen y Fecha -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="card">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; font-size: 1rem;">Detalles de la Renegociación</h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Fecha de Vigencia (Nuevo Precio)</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--secondary-color);">
                        <p style="font-size: 0.7rem; color: #A0AEC0; margin-top: 0.5rem;">Los cobros generados a partir de este mes usarán los nuevos valores.</p>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Fecha de Finalización (Se mantiene)</label>
                        <input type="date" name="end_date" value="{{ $lease->end_date }}" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--secondary-color); background: #f8fafc;">
                    </div>
                </div>

                <div class="card" style="border-left: 5px solid var(--accent-color);">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; font-size: 1rem;">Honorarios por Gestión</h3>
                    <div style="display: grid; grid-template-columns: 1fr 80px; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Monto Total ($)</label>
                            <input type="number" step="0.01" name="agency_fee_amount" placeholder="0.00" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Cuotas</label>
                            <input type="number" name="initial_fee_installments" value="1" min="1" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color); text-align: center;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloque 2: Nuevos Valores -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="card">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem; font-size: 1rem;">Nuevos Valores Económicos</h3>
                    
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--accent-color);">NUEVO PRECIO BASE ($)</label>
                        <input type="number" step="0.01" name="base_price" required value="{{ $lease->base_price }}" style="width: 100%; padding: 1rem; border-radius: 12px; border: 2px solid var(--accent-color); font-weight: 800; font-size: 1.6rem; color: var(--primary-color);">
                    </div>

                    <div style="margin-bottom: 2rem; padding: 1.2rem; background: #f8fafc; border-radius: 12px; border: 1px solid #edf2f7;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; margin-bottom: 1rem;">Nuevo Esquema de Actualización</label>
                        
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; background: #edf2f7; padding: 0.3rem; border-radius: 10px;">
                            <button type="button" onclick="setUpdateType('fixed')" id="btn-fixed" class="btn" style="flex: 1; font-size: 0.8rem; background: {{ $lease->update_type === 'fixed' ? 'var(--accent-color)' : 'transparent' }}; color: {{ $lease->update_type === 'fixed' ? 'white' : 'var(--text-light)' }};">Fijo (%)</button>
                            <button type="button" onclick="setUpdateType('indexed')" id="btn-indexed" class="btn" style="flex: 1; font-size: 0.8rem; background: {{ $lease->update_type === 'indexed' ? 'var(--accent-color)' : 'transparent' }}; color: {{ $lease->update_type === 'indexed' ? 'white' : 'var(--text-light)' }};">Indexado</button>
                            <input type="hidden" name="update_type" id="update_type" value="{{ $lease->update_type }}">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div id="update-value-container" style="display: {{ $lease->update_type === 'fixed' ? 'block' : 'none' }}">
                                <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700;">Aumento (%)</label>
                                <input type="number" step="0.01" name="update_value" value="{{ $lease->update_value }}" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700;">Cada (meses)</label>
                                <input type="number" name="update_frequency_months" value="{{ $lease->update_frequency_months }}" min="1" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0;">
                            </div>
                        </div>

                        <div id="index-name-container" style="display: {{ $lease->update_type === 'indexed' ? 'block' : 'none' }}; margin-top: 1rem;">
                            <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700;">Seleccionar Índice</label>
                            <select name="index_type_id" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white;">
                                <option value="">-- Seleccionar --</option>
                                @foreach($indexTypes as $index)
                                    <option value="{{ $index->id }}" {{ $lease->index_type_id == $index->id ? 'selected' : '' }}>{{ $index->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="background: #fff; padding: 1.2rem; border-radius: 12px; border: 1px dashed #cbd5e0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <label style="font-weight: 800; color: var(--primary-color); font-size: 0.75rem; text-transform: uppercase;">Ajuste Depósito (Opcional)</label>
                        </div>
                        <input type="number" step="0.01" name="security_deposit_diff" placeholder="0.00" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0; margin-top: 0.5rem;">
                        <p style="font-size: 0.65rem; color: #718096; margin-top: 0.5rem; font-style: italic;">Deja en 0 si no se requiere actualizar el depósito.</p>
                    </div>

                    <!-- Facturación -->
                    <div style="background: #EBF8FF; padding: 1.2rem; border-radius: 12px; border: 1px solid #BEE3F8; margin-top: 2rem;">
                         <label style="display: block; margin-bottom: 1rem; font-weight: 800; color: #2B6CB0; text-transform: uppercase; font-size: 0.75rem;">Facturación Oficial (IVA/IB)</label>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" id="invoicing_enabled" name="invoicing_enabled" value="1" onchange="toggleInvoicing()" {{ old('invoicing_enabled', $lease->invoicing_enabled) ? 'checked' : '' }} style="margin-right: 0.5rem; width: 1.2rem; height: 1.2rem; accent-color: #3182CE;">
                                <span style="font-weight: 600; color: #2D3748;">¿Requiere Facturación Parcial?</span>
                            </label>
                        </div>
                        <div id="invoicing_percentage_container" style="display: {{ old('invoicing_enabled', $lease->invoicing_enabled) ? 'block' : 'none' }}; margin-top: 1rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Porcentaje a Facturar (%)</label>
                            <input type="number" name="invoicing_percentage" id="invoicing_percentage" value="{{ old('invoicing_percentage', $lease->invoicing_percentage) }}" min="1" max="99" step="1" placeholder="Ej: 50" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #BEE3F8;">
                            @error('invoicing_percentage') <span style="color: #E53E3E; font-size: 0.75rem; font-weight: 600;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1.5rem; justify-content: flex-end; margin-top: 3rem;">
            <a href="{{ route('leases.show', $lease) }}" style="text-decoration: none; color: #a0aec0; font-weight: 700; align-self: center;">Cancelar</a>
            <button type="submit" class="btn btn-primary" style="padding: 1.2rem 4rem; font-size: 1.2rem; border-radius: 15px; font-weight: 800; background: #553C9A; border: none; box-shadow: 0 10px 15px -3px rgba(85, 60, 154, 0.2);">Confirmar Renegociación</button>
        </div>
    </form>
</div>

<script>
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
