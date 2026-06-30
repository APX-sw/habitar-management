@extends('layouts.app')

@section('title', '| Detalles del Periodo de Sueldos')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('salaries.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver al listado de periodos</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0; text-transform: capitalize;">
            Sueldos de {{ \Carbon\Carbon::createFromDate(null, $month, 1)->locale('es')->translatedFormat('F') }} {{ $year }}
        </h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Listado de liquidaciones y borradores para este periodo.</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background-color: #f8fafc; border-bottom: 1px solid #edf2f7;">
            <tr>
                <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Empleado</th>
                <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Sueldo Base/Actual</th>
                <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Adelantos</th>
                <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Bonos</th>
                <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Neto a Pagar</th>
                <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Pagado</th>
                <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase;">Estado</th>
                <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                @php
                    $settlement = $employee->salarySettlements->first();
                    $baseAmount = $settlement ? $settlement->base_amount : ($employee->base_salary ?? 0);
                    
                    $employeeAdvances = $advances->get($employee->id, collect());
                    $advancesAmount = $settlement ? $settlement->advances_amount : $employeeAdvances->sum('amount');
                    $advancesTooltip = '';
                    foreach($employeeAdvances as $adv) {
                        $advancesTooltip .= \Carbon\Carbon::parse($adv->date)->format('d/m') . ': -$' . number_format($adv->amount, 2, ',', '.') . "\n";
                    }
                    if($advancesTooltip == '') $advancesTooltip = 'Sin adelantos';

                    $bonusesAmount = $settlement ? $settlement->bonuses_amount : 0;
                    $bonusesTooltip = '';
                    if ($settlement && $settlement->bonuses) {
                        foreach($settlement->bonuses as $bon) {
                            $bonusesTooltip .= $bon->description . ': +$' . number_format($bon->amount, 2, ',', '.') . "\n";
                        }
                    }
                    if($bonusesTooltip == '') $bonusesTooltip = 'Sin bonos';

                    $netAmount = $settlement ? $settlement->net_amount : ($baseAmount - $advancesAmount);
                    $paidAmount = $settlement ? $settlement->paid_amount : 0;
                    $status = $settlement ? $settlement->status : 'pending';
                @endphp
                <tr style="border-bottom: 1px solid #edf2f7; transition: background-color 0.2s;">
                    <td style="padding: 1rem; font-weight: 500;">
                        {{ $employee->full_name }}
                    </td>
                    <td style="padding: 1rem;">
                        $ {{ number_format($baseAmount, 2, ',', '.') }}
                    </td>
                    <td style="padding: 1rem; color: #e53e3e; display: flex; align-items: center; gap: 0.5rem;">
                        -$ {{ number_format($advancesAmount, 2, ',', '.') }}
                        @if($advancesAmount > 0)
                        <span title="{{ $advancesTooltip }}" style="cursor: help; background: #FED7D7; color: #C53030; border-radius: 50%; width: 16px; height: 16px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800;">!</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; color: #38a169;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            +$ {{ number_format($bonusesAmount, 2, ',', '.') }}
                            @if($bonusesAmount > 0)
                            <span title="{{ $bonusesTooltip }}" style="cursor: help; background: #C6F6D5; color: #276749; border-radius: 50%; width: 16px; height: 16px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800;">!</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 1rem; font-weight: 700; font-size: 1.1rem; color: var(--text-main);">
                        $ {{ number_format($netAmount, 2, ',', '.') }}
                    </td>
                    <td style="padding: 1rem; font-weight: 700; font-size: 1.1rem; color: #38a169;">
                        $ {{ number_format($paidAmount, 2, ',', '.') }}
                    </td>
                    <td style="padding: 1rem;">
                        @if($status == 'paid')
                            <span style="background: #c6f6d5; color: #2f855a; padding: 0.3rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">Pagado</span>
                        @elseif($status == 'ready')
                            <span style="background: #fefcbf; color: #744210; padding: 0.3rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">Listo para Pagar</span>
                        @elseif($status == 'partial')
                            <span style="background: #e9d8fd; color: #553c9a; padding: 0.3rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">Pago Parcial</span>
                        @elseif($status == 'draft')
                            <span style="background: #edf2f7; color: #4a5568; padding: 0.3rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">Borrador Incompleto</span>
                        @else
                            <span style="background: #f1f5f9; color: #94a3b8; padding: 0.3rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">Pendiente Generar</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                        @if($status == 'pending')
                            <button onclick="openManageModal({{ $employee->id }}, '{{ $employee->full_name }}', {{ $baseAmount }}, {{ $advancesAmount }}, 'pending', 0, '{{ $employee->bank_name ?? 'No registrado' }}', '{{ $employee->cbu_alias ?? 'No registrado' }}', '{{ $employee->update_type ?? 'No registrado' }}')" class="btn" style="background: #edf2f7; color: #4a5568; padding: 0.4rem 0.8rem; font-size: 0.85rem; font-weight: 600;">
                                Generar Borrador Individual
                            </button>
                        @else
                            <button onclick="openManageModal({{ $employee->id }}, '{{ $employee->full_name }}', {{ $baseAmount }}, {{ $advancesAmount }}, '{{ $status }}', {{ $paidAmount }}, '{{ $employee->bank_name ?? 'No registrado' }}', '{{ $employee->cbu_alias ?? 'No registrado' }}', '{{ $employee->update_type ?? 'No registrado' }}')" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; font-weight: 600;">
                                ⚙️ Gestionar
                            </button>
                            <button onclick="openPaymentsModal({{ $employee->id }}, '{{ $employee->full_name }}')" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; font-weight: 600; {{ ($status == 'ready' || $status == 'partial' || $status == 'paid') ? 'background: #38A169; color: white;' : 'background: #edf2f7; color: #a0aec0; cursor: not-allowed;' }}" {{ ($status == 'ready' || $status == 'partial' || $status == 'paid') ? '' : 'disabled' }}>
                                💸 Pagos
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Gestionar Sueldo -->
<div id="manageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;">
    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 850px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin: 2rem 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; color: var(--primary-color);">⚙️ Gestionar Sueldo</h3>
            <button onclick="document.getElementById('manageModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #a0aec0;">&times;</button>
        </div>

        <form id="manageForm" method="POST">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid #edf2f7; padding-bottom: 1rem;">
                <div style="flex: 1;">
                    <p style="font-weight: 800; color: #2d3748; font-size: 1.2rem; margin: 0 0 0.5rem 0;" id="modalEmployeeName">Empleado</p>
                    <div style="font-size: 0.85rem; color: var(--text-light); line-height: 1.4;">
                        <div>🏦 <span id="modalEmployeeBank" style="font-weight: 600;"></span> - <span id="modalEmployeeCbu"></span></div>
                        <div>📈 Actualización: <span id="modalEmployeeUpdateType" style="font-weight: 600;"></span></div>
                    </div>
                </div>
                <div style="flex: 1; background: #f8fafc; padding: 0.8rem; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.8rem;">
                    <strong style="color: #4a5568; display: block; margin-bottom: 0.4rem;">Últimos Bonos Recibidos:</strong>
                    <div id="modalPastBonuses" style="color: var(--text-light);">
                        <!-- Inyectado por JS -->
                    </div>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
                <span style="color: var(--text-light);">Sueldo Base:</span>
                <span style="font-weight: 600;" id="modalBaseAmount">$0.00</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; color: #e53e3e; font-size: 0.95rem; align-items: flex-start;">
                <span>Adelantos Descontados:</span>
                <div style="text-align: right;">
                    <span style="font-weight: 600; display: block;" id="modalAdvancesAmount">-$0.00</span>
                    <div id="modalAdvancesList" style="font-size: 0.8rem; margin-top: 0.2rem;">
                        <!-- Inyectado por JS -->
                    </div>
                </div>
            </div>

            <!-- Bonos Dinámicos -->
            <div style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1.5rem; border-radius: 10px; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 1rem 0; font-size: 0.95rem; color: var(--primary-color);">Bonos (Comisiones, Extras)</h4>
                
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    <input type="text" id="newBonusDesc" placeholder="Descripción del bono..." style="flex: 2; padding: 0.6rem; border-radius: 6px; border: 1px solid #cbd5e0; font-size: 0.85rem;">
                    <input type="number" id="newBonusAmount" step="0.01" placeholder="Monto $" style="flex: 1; padding: 0.6rem; border-radius: 6px; border: 1px solid #cbd5e0; font-size: 0.85rem;">
                    <button type="button" onclick="addBonusFromInputs()" class="btn" style="background: var(--primary-color); color: white; padding: 0.6rem 1rem; font-weight: 600; font-size: 0.85rem;">Añadir</button>
                </div>
                
                <div id="bonusesList" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <!-- Bonos se inyectan acá visualmente -->
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.2rem; border-top: 2px dashed #edf2f7; padding-top: 1rem;">
                <span style="font-weight: 700; color: #2d3748;">Neto a Pagar:</span>
                <span style="font-weight: 800; color: #38a169;" id="modalNetAmount">$0.00</span>
            </div>

            <div style="text-align: right; display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="document.getElementById('manageModal').style.display='none'" class="btn" style="background: #edf2f7; color: var(--text-main); font-weight: 600;">Cerrar</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 600; padding: 0.8rem 1.5rem;" id="btnSaveReady">Guardar como Listo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pagos -->
<div id="paymentsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;">
    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 850px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin: 2rem 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h3 style="margin: 0; color: #2f855a; display: flex; align-items: center; gap: 0.5rem;">💸 Registrar Pagos</h3>
                <p style="margin: 0.2rem 0 0 0; font-size: 0.85rem; color: var(--text-light);" id="payModalEmployeeName">Empleado</p>
            </div>
            <button onclick="document.getElementById('paymentsModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #a0aec0;">&times;</button>
        </div>

        <form id="payForm" method="POST">
            @csrf
            
            <div style="background: #f8fafc; padding: 1.2rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-light);">Saldo Pendiente Actual:</span>
                    <div style="font-weight: 800; color: #F6AD55; font-size: 1.4rem;" id="currentBalanceDisplay">$0,00</div>
                    <input type="hidden" id="currentBalance" value="0">
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 0.75rem; color: var(--text-light);">Nuevo Saldo Restante:</span>
                    <div id="remainingBalanceDisplay" style="font-weight: 800; color: #48BB78; font-size: 1.4rem;">$0,00</div>
                </div>
            </div>

            <div id="salaryPaymentRows">
                <!-- Fila de pago inicial -->
                <div class="salary-payment-row" style="display: grid; grid-template-columns: 1.5fr 1.5fr 1.5fr 40px; gap: 0.8rem; margin-bottom: 1rem; align-items: flex-end; padding-bottom: 1rem; border-bottom: 1px dashed #edf2f7;">
                    <div>
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Monto</label>
                        <input type="number" step="0.01" name="payments[0][amount]" class="salary-payment-amount" value="0" oninput="updateSalaryRemainingBalance()" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 700;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Cuenta de Egreso</label>
                        <select name="payments[0][account_id]" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 600;">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Fecha</label>
                        <input type="date" name="payments[0][payment_date]" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    </div>
                    <div>
                        <!-- No delete for first row -->
                    </div>
                </div>
            </div>

            <button type="button" onclick="addSalaryPaymentRow()" class="btn" style="background: #edf2f7; color: var(--primary-color); font-weight: 700; font-size: 0.8rem; margin-bottom: 1.5rem; width: 100%;">➕ Agregar otro método / pago</button>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('paymentsModal').style.display='none'" class="btn" style="background: white; border: 1px solid #d2d6dc; color: #475569;">Cancelar</button>
                <button type="submit" class="btn" style="background: #48BB78; color: white; font-weight: 700; padding: 0.8rem 2rem;" id="btnSubmitPayments">Registrar Pagos</button>
            </div>
        </form>
        
        <div id="paymentsHistory" style="margin-top: 1.5rem; display: none;">
            <h5 style="margin: 0 0 0.8rem 0; color: #2f855a; font-size: 0.9rem; border-bottom: 1px solid #9ae6b4; padding-bottom: 0.4rem;">Historial de Pagos Anteriores</h5>
            <div id="paymentsList" style="display: flex; flex-direction: column; gap: 0.5rem;"></div>
        </div>
    </div>
</div>

<script>
    let bonusIndex = 0;
    let currentBase = 0;
    let currentAdvances = 0;
    let currentPaid = 0;
    let salaryPaymentRowCount = 1;
    const allSettlements = @json($employees->flatMap->salarySettlements->keyBy('employee_id'));
    const pastBonusesMap = @json($pastBonuses);
    const advancesMap = @json($advances);
    const salaryAccountOptions = `@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }} ($ {{ number_format($account->current_balance, 2, ',', '.') }})</option>@endforeach`;

    function openManageModal(employeeId, employeeName, baseAmount, advancesAmount, status, paidAmount, bankName, cbuAlias, updateType) {
        currentBase = parseFloat(baseAmount);
        currentAdvances = parseFloat(advancesAmount);
        currentPaid = parseFloat(paidAmount);
        bonusIndex = 0;
        
        document.getElementById('modalEmployeeName').innerText = employeeName;
        document.getElementById('modalEmployeeBank').innerText = bankName;
        document.getElementById('modalEmployeeCbu').innerText = cbuAlias;
        document.getElementById('modalEmployeeUpdateType').innerText = updateType;

        document.getElementById('modalBaseAmount').innerText = '$ ' + currentBase.toLocaleString('es-AR', {minimumFractionDigits: 2});
        document.getElementById('modalAdvancesAmount').innerText = '-$ ' + currentAdvances.toLocaleString('es-AR', {minimumFractionDigits: 2});
        
        // Populating past bonuses
        const pastBonusesContainer = document.getElementById('modalPastBonuses');
        pastBonusesContainer.innerHTML = '';
        if (pastBonusesMap[employeeId] && pastBonusesMap[employeeId].length > 0) {
            pastBonusesMap[employeeId].forEach(b => {
                let d = new Date(b.created_at);
                pastBonusesContainer.innerHTML += `<div style="margin-bottom: 0.2rem;">${d.toLocaleDateString('es-AR')} - ${b.description}: <strong style="color: #38a169;">+$${parseFloat(b.amount).toLocaleString('es-AR', {minimumFractionDigits: 2})}</strong></div>`;
            });
        } else {
            pastBonusesContainer.innerHTML = '<em>Sin bonos recientes.</em>';
        }

        // Populating advances breakdown
        const advancesListContainer = document.getElementById('modalAdvancesList');
        advancesListContainer.innerHTML = '';
        if (advancesMap[employeeId] && advancesMap[employeeId].length > 0) {
            advancesMap[employeeId].forEach(adv => {
                let d = new Date(adv.date);
                advancesListContainer.innerHTML += `<div>${d.toLocaleDateString('es-AR')} - $${parseFloat(adv.amount).toLocaleString('es-AR', {minimumFractionDigits: 2})}</div>`;
            });
        }

        document.getElementById('bonusesList').innerHTML = '';
        
        let formAction = '/salaries/' + employeeId;
        let settleId = null;

        if (allSettlements[employeeId]) {
            let settlement = allSettlements[employeeId];
            settleId = settlement.id;

            if (settlement.bonuses && settlement.bonuses.length > 0) {
                settlement.bonuses.forEach(b => {
                    addBonusRow(b.description, b.amount);
                });
            }
        }
        
        document.getElementById('manageForm').action = formAction;

        // For manage modal, it's either ready or draft mode (saving state)
        document.getElementById('btnSaveReady').innerText = (status == 'pending' || status == 'draft') ? 'Guardar como Listo' : 'Actualizar Liquidación';
        
        if(status == 'paid') {
            document.getElementById('btnSaveReady').style.display = 'none';
        } else {
            document.getElementById('btnSaveReady').style.display = 'inline-block';
        }
        
        let existingEmpInput = document.getElementById('manageEmployeeId');
        if(existingEmpInput) existingEmpInput.remove();

        let empInput = document.createElement('input');
        empInput.type = 'hidden';
        empInput.name = 'employee_id';
        empInput.value = employeeId;
        empInput.id = 'manageEmployeeId';
        document.getElementById('manageForm').appendChild(empInput);

        updateManageTotals();
        document.getElementById('manageModal').style.display = 'flex';
    }

    function openPaymentsModal(employeeId, employeeName) {
        const settlement = allSettlements[employeeId];
        if(!settlement) return;

        salaryPaymentRowCount = 1;
        document.getElementById('payModalEmployeeName').innerText = employeeName;
        
        const netAmount = parseFloat(settlement.net_amount);
        const paidAmount = parseFloat(settlement.paid_amount);
        const remaining = netAmount - paidAmount;

        document.getElementById('payForm').action = '/salaries/pay/' + settlement.id;
        
        document.getElementById('currentBalance').value = remaining;
        document.getElementById('currentBalanceDisplay').innerText = '$ ' + remaining.toLocaleString('es-AR', {minimumFractionDigits: 2});
        
        // Reset rows
        const rowsContainer = document.getElementById('salaryPaymentRows');
        rowsContainer.innerHTML = `
            <div class="salary-payment-row" style="display: grid; grid-template-columns: 1.5fr 1.5fr 1.5fr 40px; gap: 0.8rem; margin-bottom: 1rem; align-items: flex-end; padding-bottom: 1rem; border-bottom: 1px dashed #edf2f7;">
                <div>
                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Monto</label>
                    <input type="number" step="0.01" name="payments[0][amount]" class="salary-payment-amount" value="${remaining.toFixed(2)}" oninput="updateSalaryRemainingBalance()" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 700;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Cuenta de Egreso</label>
                    <select name="payments[0][account_id]" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 600;">
                        ${salaryAccountOptions}
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Fecha</label>
                    <input type="date" name="payments[0][payment_date]" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                </div>
                <div></div>
            </div>
        `;
        
        updateSalaryRemainingBalance();

        // History
        const paymentsHistory = document.getElementById('paymentsHistory');
        const paymentsList = document.getElementById('paymentsList');
        paymentsList.innerHTML = '';
        
        if (settlement.payments && settlement.payments.length > 0) {
            paymentsHistory.style.display = 'block';
            settlement.payments.forEach(p => {
                let pDate = new Date(p.payment_date);
                pDate.setMinutes(pDate.getMinutes() + pDate.getTimezoneOffset()); // Fix tz
                paymentsList.innerHTML += `
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #f0fff4; padding: 0.8rem 1rem; border-radius: 8px; border: 1px solid #c6f6d5;">
                        <div>
                            <span style="font-weight: 700; color: #2f855a; font-size: 1.1rem;">$ ${parseFloat(p.amount).toLocaleString('es-AR', {minimumFractionDigits:2})}</span>
                            <span style="color: #48bb78; font-size: 0.85rem; margin-left: 0.5rem;">(${p.account ? p.account.name : 'Caja'}) - ${pDate.toLocaleDateString('es-AR')}</span>
                        </div>
                        <a href="/salaries/receipt/${p.id}" target="_blank" class="btn" style="background: white; border: 1px solid #48bb78; color: #2f855a; font-size: 0.75rem; padding: 0.3rem 0.6rem;">📄 RECIBO</a>
                    </div>
                `;
            });
        } else {
            paymentsHistory.style.display = 'none';
        }

        if(settlement.status == 'paid') {
            document.getElementById('btnSubmitPayments').style.display = 'none';
            document.getElementById('salaryPaymentRows').style.display = 'none';
        } else {
            document.getElementById('btnSubmitPayments').style.display = 'inline-block';
            document.getElementById('salaryPaymentRows').style.display = 'block';
        }

        document.getElementById('paymentsModal').style.display = 'flex';
    }

    function addSalaryPaymentRow() {
        const container = document.getElementById('salaryPaymentRows');
        const newRow = document.createElement('div');
        newRow.className = 'salary-payment-row';
        newRow.style = 'display: grid; grid-template-columns: 1.5fr 1.5fr 1.5fr 40px; gap: 0.8rem; margin-bottom: 1rem; align-items: flex-end; padding-bottom: 1rem; border-bottom: 1px dashed #edf2f7;';
        
        newRow.innerHTML = `
            <div>
                <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Monto</label>
                <input type="number" step="0.01" name="payments[${salaryPaymentRowCount}][amount]" class="salary-payment-amount" value="0" oninput="updateSalaryRemainingBalance()" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 700;">
            </div>
            <div>
                <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Cuenta de Egreso</label>
                <select name="payments[${salaryPaymentRowCount}][account_id]" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 600;">
                    ${salaryAccountOptions}
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.4rem;">Fecha</label>
                <input type="date" name="payments[${salaryPaymentRowCount}][payment_date]" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>
            <div>
                <button type="button" onclick="this.parentElement.parentElement.remove(); updateSalaryRemainingBalance();" style="background: #FFF5F5; border: 1px solid #FEB2B2; color: #C53030; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;" title="Eliminar fila">🗑️</button>
            </div>
        `;
        container.appendChild(newRow);
        salaryPaymentRowCount++;
        updateSalaryRemainingBalance();
    }

    function updateSalaryRemainingBalance() {
        const balance = parseFloat(document.getElementById('currentBalance').value) || 0;
        let paid = 0;
        document.querySelectorAll('.salary-payment-amount').forEach(input => {
            paid += parseFloat(input.value || 0);
        });
        
        const remaining = balance - paid;
        const display = document.getElementById('remainingBalanceDisplay');
        display.innerText = '$' + remaining.toLocaleString('es-AR', { minimumFractionDigits: 2 });
        
        if (remaining < -0.01) {
            display.style.color = '#E53E3E'; // Rojo si paga de más
        } else if (remaining < 0.01) {
            display.style.color = '#38A169'; // Verde si cubre justo
        } else {
            display.style.color = '#DD6B20'; // Naranja si falta pagar
        }
    }

    function addBonusFromInputs() {
        let desc = document.getElementById('newBonusDesc').value;
        let amount = document.getElementById('newBonusAmount').value;
        if(desc && amount) {
            addBonusRow(desc, amount);
            document.getElementById('newBonusDesc').value = '';
            document.getElementById('newBonusAmount').value = '';
        }
    }

    function addBonusRow(desc, amount) {
        const container = document.getElementById('bonusesList');
        const html = `
            <div class="bonus-row" style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 0.6rem 1rem; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.85rem;" id="bonusRow_${bonusIndex}">
                <input type="hidden" name="bonuses[${bonusIndex}][description]" value="${desc}">
                <input type="hidden" name="bonuses[${bonusIndex}][amount]" class="bonus-amount-hidden" value="${amount}">
                
                <span style="font-weight: 600; color: #4a5568;">${desc}</span>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span style="font-weight: 700; color: #38a169;">+$ ${parseFloat(amount).toLocaleString('es-AR', {minimumFractionDigits: 2})}</span>
                    <button type="button" onclick="removeBonus(${bonusIndex})" style="background: none; border: none; color: #e53e3e; cursor: pointer; font-size: 1.1rem; padding: 0;" title="Quitar">&times;</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        bonusIndex++;
        updateManageTotals();
    }
    
    function removeBonus(index) {
        document.getElementById('bonusRow_' + index).remove();
        updateManageTotals();
    }
    
    function updateManageTotals() {
        let totalBonuses = 0;
        document.querySelectorAll('.bonus-amount-hidden').forEach(input => {
            totalBonuses += parseFloat(input.value) || 0;
        });
        
        let netAmount = currentBase - currentAdvances + totalBonuses;
        if(netAmount < 0) netAmount = 0;
        document.getElementById('modalNetAmount').innerText = '$ ' + netAmount.toLocaleString('es-AR', {minimumFractionDigits: 2});
    }
</script>
@endsection
