@extends('layouts.app')

@section('title', '| Nuevo Contrato')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('leases.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver al listado
        </a>
        <h1 style="color: var(--primary-color);">Generar Nuevo Contrato de Alquiler</h1>
    </div>

    <form action="{{ route('leases.store') }}" method="POST" id="lease-form">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <!-- Property & Tenant -->
            <div class="card">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Partes Vinculadas</h3>
                
                <!-- Propiedad Selector -->
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.8rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Propiedad a Alquilar</label>
                    <input type="hidden" name="property_id" id="selected-property-id" required>
                    <div id="property-display" style="border: 2px dashed var(--secondary-color); border-radius: 12px; padding: 1.5rem; text-align: center; background: #f8fafc; transition: all 0.3s;">
                        <p style="color: var(--text-light); margin-bottom: 1rem; font-size: 0.9rem;">No hay propiedad seleccionada</p>
                        <button type="button" onclick="openModal('property-modal')" class="btn" style="background: var(--accent-color); color: white; width: 100%;">Buscar Propiedad</button>
                    </div>
                </div>

                <!-- Inquilino Selector -->
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.8rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Inquilino Responsable</label>
                    <input type="hidden" name="tenant_id" id="selected-tenant-id" required>
                    <div id="tenant-display" style="border: 2px dashed var(--secondary-color); border-radius: 12px; padding: 1.5rem; text-align: center; background: #f8fafc; transition: all 0.3s;">
                        <p style="color: var(--text-light); margin-bottom: 1rem; font-size: 0.9rem;">No hay inquilino seleccionado</p>
                        <button type="button" onclick="openModal('tenant-modal')" class="btn" style="background: var(--accent-color); color: white; width: 100%;">Buscar Inquilino</button>
                    </div>
                </div>

                <!-- Datos del Garante -->
                <div style="border-top: 1px solid var(--secondary-color); padding-top: 1.5rem; margin-top: 1rem;">
                    <h4 style="color: var(--primary-color); margin-bottom: 1.2rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Datos del Garante</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Nombre Completo</label>
                        <input type="text" name="guarantor_name" placeholder="Nombre del Garante" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">DNI / CUIT</label>
                            <input type="text" name="guarantor_id_number" placeholder="20-XXXXXXXX-X" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Teléfono</label>
                            <input type="text" name="guarantor_phone" placeholder="+54 9..." style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Email</label>
                        <input type="email" name="guarantor_email" placeholder="garante@gmail.com" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Dirección</label>
                        <input type="text" name="guarantor_address" placeholder="Calle, Nro, Piso, Dpto..." style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                    </div>
                </div>
            </div>

            @include('leases.partials.selectors_modals')


            <!-- Lease Details -->
            <div class="card">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Condiciones del Alquiler</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha Inicio</label>
                        <input type="date" name="start_date" required style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha Fin</label>
                        <input type="date" name="end_date" required style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Precio Base Mensual ($)</label>
                    <input type="number" step="0.01" name="base_price" required style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <div style="margin-bottom: 1.5rem; border: 1px solid var(--secondary-color); padding: 1rem; border-radius: 12px; background: #f8fafc;">
                    <label style="display: block; margin-bottom: 1rem; font-weight: 700; color: var(--primary-color); text-transform: uppercase; font-size: 0.75rem;">Ajuste del Alquiler</label>
                    
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; background: #edf2f7; padding: 0.3rem; border-radius: 8px;">
                        <button type="button" onclick="setUpdateType('fixed')" id="btn-fixed" class="btn" style="flex: 1; font-size: 0.8rem; background: var(--accent-color); color: white;">Fijo (%)</button>
                        <button type="button" onclick="setUpdateType('indexed')" id="btn-indexed" class="btn" style="flex: 1; font-size: 0.8rem; background: transparent; color: var(--text-light);">Indexado (IPC/ICL)</button>
                        <input type="hidden" name="update_type" id="update_type" value="fixed">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div id="update-value-container">
                            <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 600;">Aumento (%)</label>
                            <input type="number" step="0.01" name="update_value" placeholder="Ej: 15" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 600;">Cada (meses)</label>
                            <input type="number" name="update_frequency_months" value="6" min="1" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        </div>
                    </div>

                    <div id="index-name-container" style="display: none; margin-top: 1rem;">
                        <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 600;">Seleccionar Índice</label>
                        <select name="index_type_id" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color); background: white;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($indexTypes as $index)
                                <option value="{{ $index->id }}">{{ $index->name }}</option>
                            @endforeach
                        </select>
                        <p style="font-size: 0.75rem; color: var(--text-light); margin-top: 0.4rem;">Puedes dar de alta más índices en Configuración.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Initial Fees (Honorarios y Depósito) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <!-- Honorarios -->
            <div class="card" style="border-left: 5px solid var(--accent-color);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">
                    <h3 style="color: var(--primary-color); font-size: 1.1rem;">Honorarios Inmobiliaria</h3>
                    <button type="button" onclick="copyTo('agency_fee_amount')" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.75rem; font-weight: 700;">= 1 Mes</button>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 600;">Monto Total ($)</label>
                        <input type="number" step="0.01" name="agency_fee_amount" id="agency_fee_amount" placeholder="0.00" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 600;">Cuotas</label>
                        <input type="number" name="initial_fee_installments" value="1" min="1" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                    </div>
                </div>
                <p style="font-size: 0.7rem; color: var(--text-light); margin-top: 1rem;">Se dividirá en cuotas y se cobrará mensualmente.</p>
            </div>

            <!-- Depósito -->
            <div class="card" style="border-left: 5px solid #4A5568;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">
                    <h3 style="color: var(--primary-color); font-size: 1.1rem;">Depósito en Garantía</h3>
                    <button type="button" onclick="copyTo('security_deposit_amount')" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.75rem; font-weight: 700;">= 1 Mes</button>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 600;">Monto Total ($)</label>
                    <input type="number" step="0.01" name="security_deposit_amount" id="security_deposit_amount" placeholder="0.00" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                </div>
                <p style="font-size: 0.7rem; color: var(--text-light); margin-top: 1rem;">Se cobrará íntegramente en el primer mes de alquiler.</p>
            </div>
        </div>

        <!-- Fixed Charges -->
        <div class="card" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">
                <h3 style="color: var(--primary-color);">Conceptos Mensuales Recurrentes</h3>
                <button type="button" onclick="addFixedCharge()" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.85rem;">+ Agregar Concepto</button>
            </div>
            
            <div id="fixed-charges-container">
                <div style="display: grid; grid-template-columns: 1fr 40px; gap: 1rem; margin-bottom: 1rem;" class="charge-row">
                    <input type="text" name="fixed_charges[0][name]" placeholder="Concepto (Ej: Expensas, TGI, Agua)" class="form-control" style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                    <div></div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
            <a href="{{ route('leases.index') }}" class="btn" style="background: var(--secondary-color);">Cancelar</a>
            <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">Firmar y Guardar Contrato</button>
        </div>
    </form>
</div>

<script>
    function copyTo(targetId) {
        const basePrice = document.querySelector('input[name="base_price"]').value;
        if (basePrice) {
            document.getElementById(targetId).value = basePrice;
        } else {
            alert('Primero completa el Precio Base Mensual');
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

    let chargeCount = 1;
    function addFixedCharge() {
        const container = document.getElementById('fixed-charges-container');
        const row = document.createElement('div');
        row.style.display = 'grid';
        row.style.gridTemplateColumns = '1fr 40px';
        row.style.gap = '1rem';
        row.style.marginBottom = '1rem';
        row.innerHTML = `
            <input type="text" name="fixed_charges[${chargeCount}][name]" placeholder="Concepto Mensual" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #C53030; cursor: pointer; font-size: 1.2rem;">&times;</button>
        `;
        container.appendChild(row);
        chargeCount++;
    }
</script>
@endsection
