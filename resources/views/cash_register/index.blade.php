@extends('layouts.app')

@section('title', '| Caja General')

@section('content')
<div style="margin-bottom: 2.5rem;">
    <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Caja General</h1>
    <p style="color: var(--text-light); margin-top: 0.5rem;">Visualiza los saldos y el historial de todas las cuentas de la inmobiliaria.</p>
    
    <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
        <button onclick="document.getElementById('transferModal').style.display='flex'" class="btn" style="background: #EBF8FF; color: #3182CE; border: 1px solid #BEE3F8; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; outline: none; border-radius: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
            Transferir entre cuentas
        </button>
        <button onclick="document.getElementById('adjustModal').style.display='flex'" class="btn" style="background: #F7FAFC; color: #4A5568; border: 1px solid #E2E8F0; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; outline: none; border-radius: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20v-6M9 17l3 3 3-3M12 4v6m3-3l-3-3-3 3"></path></svg>
            Ajustar Saldo
        </button>
        <button onclick="document.getElementById('advanceModal').style.display='flex'" class="btn" style="background: #F0FFF4; color: #38A169; border: 1px solid #C6F6D5; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; outline: none; border-radius: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            Adelanto de Sueldo
        </button>
        <button onclick="document.getElementById('auditModal').style.display='flex'" class="btn" style="background: #FFF5F5; color: #E53E3E; border: 1px solid #FED7D7; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; outline: none; border-radius: 8px; margin-left: auto;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            Ver Auditoría de Eliminaciones
        </button>
    </div>
</div>

<!-- Saldos -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <!-- Balance Total -->
    <div class="card account-card" data-account-id="" style="padding: 1.5rem; border-left: 5px solid var(--primary-color); background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); cursor: pointer; min-height: 130px; display: flex; flex-direction: column; justify-content: center;">
        <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 0.85rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.025em;">Balance Total de Cuentas</h3>
        <div style="font-size: clamp(1.4rem, 4.5vw, 2rem); font-weight: 900; color: var(--primary-color); line-height: 1.2; word-break: break-all;">
            ${{ number_format($totalBalance, 2) }}
        </div>
    </div>

    @foreach($accounts as $account)
        <div class="card account-card" data-account-id="{{ $account->id }}" style="padding: 1.5rem; border-left: 5px solid {{ $account->type === 'cash' ? '#48BB78' : ($account->type === 'habitar_fund' ? '#00B5D8' : '#4299E1') }}; cursor: pointer; min-height: 130px; display: flex; flex-direction: column; justify-content: center; position: relative;">
            <h3 style="margin: 0 0 0.5rem; color: var(--text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.025em;">
                {{ $account->name }}
                @if($account->type === 'habitar_fund')
                    <span style="font-size: 0.7rem; background: #EBF8FF; color: #3182CE; padding: 0.2rem 0.5rem; border-radius: 4px; margin-left: 0.5rem; font-weight: 700;">Informativa</span>
                @endif
            </h3>
            <div style="font-size: clamp(1.2rem, 3.5vw, 1.7rem); font-weight: 800; color: var(--primary-color); line-height: 1.2; word-break: break-all;">
                ${{ number_format($account->current_balance, 2) }}
            </div>
        </div>
    @endforeach
</div>

<!-- Filtros -->
<div class="card" style="padding: 1.5rem; margin-bottom: 2rem;">
    <form action="{{ route('cash_register.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; align-items: end;">
        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-light); margin-bottom: 0.5rem;">Cuenta</label>
            <select name="account_id" class="form-control" style="appearance: auto;">
                <option value="">Todas las cuentas</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-light); margin-bottom: 0.5rem;">Categoría</label>
            <select name="transaction_category_id" class="form-control" style="appearance: auto;">
                <option value="">Todas las categorías</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('transaction_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-light); margin-bottom: 0.5rem;">Desde</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>

        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-light); margin-bottom: 0.5rem;">Hasta</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>

        <div style="display: flex; gap: 0.8rem;">
            <button type="submit" class="btn btn-primary" style="flex: 2; padding: 0.8rem 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background-color: var(--primary-color); border: none; border-radius: 10px; color: white; cursor: pointer; font-weight: 700; font-size: 1rem;">
                Filtrar
            </button>
            <a href="{{ route('cash_register.index') }}" class="btn btn-secondary" style="flex: 1; padding: 0.8rem 1rem; display: flex; align-items: center; justify-content: center; text-decoration: none; background-color: #f7fafc; border: 2px solid #edf2f7; border-radius: 10px; color: #4a5568; font-weight: 900; font-size: 1.2rem; box-sizing: border-box;" title="Limpiar">
                ×
            </a>
        </div>
    </form>
</div>

<!-- Historial -->
<div class="card" style="padding: 2rem;">
    <h3 style="margin: 0 0 1.5rem; color: var(--primary-color);">Últimos Movimientos</h3>
    
    <div id="movements-table-container">
        @include('cash_register._movements_table')
    </div>
</div>

<!-- Modal Auditoría de Eliminaciones -->
<div id="auditModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 950px; padding: 2.2rem; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); margin: 1rem; border-left: 5px solid #E53E3E; background: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #edf2f7; padding-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.8rem;">
                <div style="background: #FFF5F5; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #E53E3E; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <div>
                    <h3 style="margin: 0; color: #C53030; font-size: 1.3rem; font-weight: 800;">Registro de Auditoría: Movimientos Eliminados</h3>
                    <p style="margin: 0.2rem 0 0; font-size: 0.8rem; color: var(--text-light); font-weight: 600;">Control de seguridad estricto para evitar manipulación o malversación de fondos.</p>
                </div>
            </div>
            <button onclick="document.getElementById('auditModal').style.display='none'" style="background: #edf2f7; border: none; font-size: 1.5rem; color: #a0aec0; cursor: pointer; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">&times;</button>
        </div>
        
        <div style="overflow-x: auto; max-height: 380px; overflow-y: auto; margin-bottom: 1rem; padding-right: 0.2rem;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #edf2f7; text-align: left; color: var(--text-light); font-size: 0.8rem; text-transform: uppercase; font-weight: 700; position: sticky; top: 0; background: white; z-index: 1; user-select: none;">
                        <th onclick="toggleAuditSort()" style="padding: 1rem 0.5rem; cursor: pointer;" title="Haga clic para ordenar por fecha de eliminación">
                            <div style="display: flex; align-items: center; gap: 0.3rem; color: #C53030;">
                                <span>Fecha Eliminación</span>
                                <span id="auditSortIcon" style="display: inline-flex; align-items: center;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </span>
                            </div>
                        </th>
                        <th style="padding: 1rem 0.5rem;">Fecha Original</th>
                        <th style="padding: 1rem 0.5rem;">Cuenta</th>
                        <th style="padding: 1rem 0.5rem;">Descripción Original</th>
                        <th style="padding: 1rem 0.5rem;">Monto</th>
                        <th style="padding: 1rem 0.5rem;">Usuario</th>
                        <th style="padding: 1rem 0.5rem;">Motivo / Ajuste</th>
                    </tr>
                </thead>
                <tbody id="auditTableBody">
                    <!-- Filas inyectadas por Javascript -->
                </tbody>
            </table>
        </div>

        <!-- Paginación de la Auditoría -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; border-top: 1px solid #edf2f7; padding-top: 1rem; margin-bottom: 1.5rem;">
            <span id="auditPaginationInfo" style="font-size: 0.85rem; color: var(--text-light); font-weight: 700;">
                Cargando registros...
            </span>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button type="button" id="prevAuditPageBtn" onclick="prevAuditPage()" class="btn" style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #f7fafc; color: #4a5568; font-weight: 700; border-radius: 6px; border: 1px solid #e2e8f0; cursor: pointer; display: flex; align-items: center; gap: 0.3rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Anterior
                </button>
                <button type="button" id="nextAuditPageBtn" onclick="nextAuditPage()" class="btn" style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #f7fafc; color: #4a5568; font-weight: 700; border-radius: 6px; border: 1px solid #e2e8f0; cursor: pointer; display: flex; align-items: center; gap: 0.3rem;">
                    Siguiente
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
        
        <button onclick="document.getElementById('auditModal').style.display='none'" class="btn" style="width: 100%; padding: 0.8rem; background: #edf2f7; color: #4a5568; font-weight: 700; border-radius: 8px;">Cerrar Ventana</button>
    </div>
</div>

<!-- Modal Transferencia -->
<div id="transferModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--primary-color); margin: 0;">Transferir Fondos</h2>
            <button onclick="document.getElementById('transferModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; color: #a0aec0; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('cash_register.transfer') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Origen</label>
                <select name="source_account_id" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; background: white;">
                    <option value="">Seleccionar cuenta origen</option>
                    @foreach($accounts as $acc)
                        @if($acc->type !== 'habitar_fund')
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: ${{ number_format($acc->current_balance, 2) }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Destino</label>
                <select name="destination_account_id" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; background: white;">
                    <option value="">Seleccionar cuenta destino</option>
                    @foreach($accounts as $acc)
                        @if($acc->type !== 'habitar_fund')
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: ${{ number_format($acc->current_balance, 2) }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Monto a transferir</label>
                <input type="number" step="0.01" name="amount" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 1.2rem; font-weight: 800;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Notas (Opcional)</label>
                <input type="text" name="notes" placeholder="Ej: Pago de sueldos desde banco" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: 700;">Confirmar Transferencia</button>
        </form>
    </div>
</div>

<!-- Modal Ajuste -->
<div id="adjustModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--primary-color); margin: 0;">Ajustar Saldo</h2>
            <button onclick="document.getElementById('adjustModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; color: #a0aec0; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('cash_register.adjust') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Cuenta a Ajustar</label>
                <select name="account_id" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; background: white;">
                    <option value="">Seleccionar cuenta</option>
                    @foreach($accounts as $acc)
                        @if($acc->type !== 'habitar_fund')
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Balance Actual: ${{ number_format($acc->current_balance, 2) }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div style="margin-bottom: 1.5rem; display: grid; grid-template-columns: 1fr; gap: 0.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase;">Tipo de Ajuste</label>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.8rem; border: 1px solid #edf2f7; border-radius: 8px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f7fafc'" onmouseout="this.style.background='transparent'">
                        <input type="radio" name="adjustment_type" value="new_balance" checked>
                        <span>Establecer Nuevo Saldo Final</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.8rem; border: 1px solid #edf2f7; border-radius: 8px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f7fafc'" onmouseout="this.style.background='transparent'">
                        <input type="radio" name="adjustment_type" value="delta_income">
                        <span>Ingreso Directo (Sumar $)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.8rem; border: 1px solid #edf2f7; border-radius: 8px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f7fafc'" onmouseout="this.style.background='transparent'">
                        <input type="radio" name="adjustment_type" value="delta_expense">
                        <span>Egreso Directo (Restar $)</span>
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Monto / Valor</label>
                <input type="number" step="0.01" name="amount" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 1.2rem; font-weight: 800;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Notas / Motivo</label>
                <input type="text" name="notes" placeholder="Ej: Arqueo de caja diario" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: 700;">Aplicar Ajuste</button>
        </form>
    </div>
</div>

<!-- Modal Nuevo Adelanto -->
<div id="advanceModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; color: var(--primary-color);">Registrar Adelanto de Sueldo</h3>
            <button onclick="document.getElementById('advanceModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #a0aec0;">&times;</button>
        </div>

        <form action="{{ route('salaries.advance') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Empleado *</label>
                <select name="employee_id" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    <option value="">Seleccionar empleado...</option>
                    @if(isset($employees))
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Monto del Adelanto ($) *</label>
                <input type="number" step="0.01" name="amount" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;" placeholder="Ej: 50000">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Fecha del Adelanto *</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                <small style="color: var(--text-light); display: block; margin-top: 0.25rem;">Este adelanto se descontará automáticamente en la liquidación de este mes y año.</small>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Cuenta Origen (Caja/Banco) *</label>
                <select name="account_id" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    <option value="">Seleccionar cuenta de la que sale el dinero...</option>
                    @foreach($accounts as $account)
                        @if($account->type !== 'habitar_fund')
                            <option value="{{ $account->id }}">{{ $account->name }} (Saldo: ${{ number_format($account->current_balance ?? $account->balance, 2, ',', '.') }})</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Notas / Descripción (Opcional)</label>
                <input type="text" name="description" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;" placeholder="Ej: Adelanto por vacaciones">
            </div>

            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('advanceModal').style.display='none'" class="btn" style="background: #edf2f7; color: var(--text-main); font-weight: 700; padding: 1rem;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700; padding: 1rem;">Registrar Adelanto</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('form[action*="cash-register"]');
    const tableContainer = document.getElementById('movements-table-container');

    if (filterForm) {
        const accountSelect = filterForm.querySelector('select[name="account_id"]');

        // Manejar clics en las tarjetas de cuenta
        document.querySelectorAll('.account-card').forEach(card => {
            card.addEventListener('click', function() {
                const accountId = this.getAttribute('data-account-id');
                accountSelect.value = accountId;
                fetchMovements();
                
                // Resaltar visualmente la tarjeta seleccionada (opcional)
                document.querySelectorAll('.account-card').forEach(c => c.style.boxShadow = '');
                this.style.boxShadow = '0 0 0 3px rgba(66, 153, 225, 0.5)';
            });
        });

        // Manejar cambios en los filtros
        filterForm.addEventListener('change', function() {
            fetchMovements();
        });

        // Manejar el submit (para el botón Filtrar)
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchMovements();
        });
    }

    // Manejar clics en paginación
    tableContainer.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            fetchMovements(url);
        }
    });

    function fetchMovements(url = null) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        let fetchUrl = url || "{{ route('cash_register.index') }}?" + params.toString();
        
        // Mostrar un estado de carga (opcional)
        tableContainer.style.opacity = '0.5';

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            tableContainer.style.opacity = '1';
            
            // Actualizar la URL del navegador sin recargar (opcional pero recomendado)
            if (!url) {
                window.history.pushState({}, '', fetchUrl);
            } else {
                window.history.pushState({}, '', url);
            }
        })
        .catch(error => {
            console.error('Error fetching movements:', error);
            tableContainer.style.opacity = '1';
        });
    }
});

// Auditoría de Eliminaciones: Datos y Paginación
const deletedMovementsData = {!! json_encode($deletedMovementsFormatted) !!};

let auditCurrentPage = 1;
let auditSortDirection = 'desc'; // 'desc' o 'asc'

function renderAuditTable() {
    const tbody = document.getElementById('auditTableBody');
    const sortIcon = document.getElementById('auditSortIcon');
    const paginationInfo = document.getElementById('auditPaginationInfo');
    const prevBtn = document.getElementById('prevAuditPageBtn');
    const nextBtn = document.getElementById('nextAuditPageBtn');

    if (!tbody) return;

    // 1. Ordenar
    const sortedItems = [...deletedMovementsData].sort((a, b) => {
        const dateA = new Date(a.created_at_raw);
        const dateB = new Date(b.created_at_raw);
        return auditSortDirection === 'desc' ? dateB - dateA : dateA - dateB;
    });

    // Actualizar icono de ordenamiento
    sortIcon.innerHTML = auditSortDirection === 'desc' 
        ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>' // Abajo (Desc)
        : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>'; // Arriba (Asc)

    // 2. Paginar de 10
    const totalItems = sortedItems.length;
    const totalPages = Math.ceil(totalItems / 10) || 1;

    if (auditCurrentPage > totalPages) auditCurrentPage = totalPages;
    if (auditCurrentPage < 1) auditCurrentPage = 1;

    const startIndex = (auditCurrentPage - 1) * 10;
    const endIndex = startIndex + 10;
    const pageItems = sortedItems.slice(startIndex, endIndex);

    // 3. Renderizar filas
    if (pageItems.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-light); font-style: italic; font-weight: 600;">
                    No hay registros de movimientos eliminados. La caja se encuentra 100% íntegra.
                </td>
            </tr>
        `;
    } else {
        let html = '';
        pageItems.forEach(item => {
            const amountFormatted = new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(item.amount);
            html += `
                <tr style="border-bottom: 1px solid #edf2f7; background: #fffbfb; transition: background 0.2s;" onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='#fffbfb'">
                    <td style="padding: 1rem 0.5rem; font-weight: 700; color: #C53030;">${item.created_at_formatted}</td>
                    <td style="padding: 1rem 0.5rem; color: var(--text-light);">${item.movement_date_formatted}</td>
                    <td style="padding: 1rem 0.5rem; font-weight: 600;">${item.account_name}</td>
                    <td style="padding: 1rem 0.5rem; font-style: italic; color: #4A5568;">${item.description}</td>
                    <td style="padding: 1rem 0.5rem; font-weight: 800; color: #E53E3E;">${amountFormatted}</td>
                    <td style="padding: 1rem 0.5rem; font-weight: 700; color: var(--primary-color);">${item.user_name}</td>
                    <td style="padding: 1rem 0.5rem; font-size: 0.8rem; color: #718096; font-weight: 600;">${item.reason}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // 4. Actualizar controles de paginación
    paginationInfo.innerText = `Página ${auditCurrentPage} de ${totalPages} (${totalItems} registros)`;
    prevBtn.disabled = auditCurrentPage === 1;
    prevBtn.style.opacity = auditCurrentPage === 1 ? '0.5' : '1';
    prevBtn.style.cursor = auditCurrentPage === 1 ? 'not-allowed' : 'pointer';

    nextBtn.disabled = auditCurrentPage === totalPages;
    nextBtn.style.opacity = auditCurrentPage === totalPages ? '0.5' : '1';
    nextBtn.style.cursor = auditCurrentPage === totalPages ? 'not-allowed' : 'pointer';
}

function toggleAuditSort() {
    auditSortDirection = auditSortDirection === 'desc' ? 'asc' : 'desc';
    renderAuditTable();
}

function prevAuditPage() {
    if (auditCurrentPage > 1) {
        auditCurrentPage--;
        renderAuditTable();
    }
}

function nextAuditPage() {
    const totalPages = Math.ceil(deletedMovementsData.length / 10) || 1;
    if (auditCurrentPage < totalPages) {
        auditCurrentPage++;
        renderAuditTable();
    }
}

// Inicializar la tabla de auditoría al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    renderAuditTable();
});
</script>
@endsection
