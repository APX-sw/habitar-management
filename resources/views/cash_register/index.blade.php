@extends('layouts.app')

@section('title', '| Caja General')

@section('content')
<div style="margin-bottom: 2.5rem;">
    <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Caja General</h1>
    <p style="color: var(--text-light); margin-top: 0.5rem;">Visualiza los saldos y el historial de todas las cuentas de la inmobiliaria.</p>
    
    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
        <button onclick="document.getElementById('transferModal').style.display='flex'" class="btn" style="background: #EBF8FF; color: #3182CE; border: 1px solid #BEE3F8; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
            Transferir entre cuentas
        </button>
        <button onclick="document.getElementById('adjustModal').style.display='flex'" class="btn" style="background: #F7FAFC; color: #4A5568; border: 1px solid #E2E8F0; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20v-6M9 17l3 3 3-3M12 4v6m3-3l-3-3-3 3"></path></svg>
            Ajustar Saldo
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
        <div class="card account-card" data-account-id="{{ $account->id }}" style="padding: 1.5rem; border-left: 5px solid {{ $account->type === 'cash' ? '#48BB78' : '#4299E1' }}; cursor: pointer; min-height: 130px; display: flex; flex-direction: column; justify-content: center;">
            <h3 style="margin: 0 0 0.5rem; color: var(--text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.025em;">{{ $account->name }}</h3>
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
            <select name="account_id" class="form-control" style="width: 100%; height: 45px; border-radius: 10px; border: 2px solid #edf2f7; padding: 0 1rem; font-size: 0.95rem; background: white; appearance: auto;">
                <option value="">Todas las cuentas</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-light); margin-bottom: 0.5rem;">Categoría</label>
            <select name="transaction_category_id" class="form-control" style="width: 100%; height: 45px; border-radius: 10px; border: 2px solid #edf2f7; padding: 0 1rem; font-size: 0.95rem; background: white; appearance: auto;">
                <option value="">Todas las categorías</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('transaction_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-light); margin-bottom: 0.5rem;">Desde</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" style="width: 100%; height: 45px; border-radius: 10px; border: 2px solid #edf2f7; padding: 0 1rem;">
        </div>

        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-light); margin-bottom: 0.5rem;">Hasta</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="width: 100%; height: 45px; border-radius: 10px; border: 2px solid #edf2f7; padding: 0 1rem;">
        </div>

        <div style="display: flex; gap: 0.8rem;">
            <button type="submit" class="btn btn-primary" style="flex: 2; height: 45px; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background-color: var(--primary-color); border: none; border-radius: 10px; color: white; cursor: pointer; font-weight: 700; font-size: 1rem;">
                Filtrar
            </button>
            <a href="{{ route('cash_register.index') }}" class="btn btn-secondary" style="flex: 1; height: 45px; display: flex; align-items: center; justify-content: center; text-decoration: none; background-color: #f7fafc; border: 2px solid #edf2f7; border-radius: 10px; color: #4a5568; font-weight: 900; font-size: 1.2rem;" title="Limpiar">
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
                        <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: ${{ number_format($acc->current_balance, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Destino</label>
                <select name="destination_account_id" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; background: white;">
                    <option value="">Seleccionar cuenta destino</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: ${{ number_format($acc->current_balance, 2) }})</option>
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
                        <option value="{{ $acc->id }}">{{ $acc->name }} (Balance Actual: ${{ number_format($acc->current_balance, 2) }})</option>
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
</script>
@endsection
