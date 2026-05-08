@extends('layouts.app')

@section('title', '| Inquilinos')

@section('content')
<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="color: var(--primary-color);">Gestión de Inquilinos</h1>
    <a href="{{ route('tenants.create') }}" class="btn btn-primary">+ Nuevo Inquilino</a></div>

<!-- Filtro de Búsqueda y Deuda AJAX -->
<div class="card" style="margin-bottom: 1.5rem; padding: 0.7rem 1.2rem; border: 1px solid var(--secondary-color); background: #f8fafc;">
    <form id="filter-form" action="{{ route('tenants.index') }}" method="GET" style="display: flex; gap: 0.7rem; align-items: center; flex-wrap: wrap;">
        
        <!-- Búsqueda Principal -->
        <div style="flex: 2; min-width: 250px;">
            <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="🔍 Buscar inquilino por nombre, DNI o email..." style="width: 100%; padding: 0.6rem 1rem; border-radius: 8px; border: 1px solid #cbd5e0; font-size: 0.85rem;">
        </div>

        <!-- Estado de Deuda -->
        <div style="flex: 1; min-width: 180px;">
            <select name="debt_status" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Todos los Inquilinos</option>
                <option value="with_debt" {{ request('debt_status') == 'with_debt' ? 'selected' : '' }}>Con Deuda Pendiente</option>
                <option value="up_to_date" {{ request('debt_status') == 'up_to_date' ? 'selected' : '' }}>Al Día</option>
            </select>
        </div>

        <a href="{{ route('tenants.index') }}" class="btn" style="padding: 0.6rem 0.8rem; border-radius: 8px; background: #edf2f7; color: #4a5568; display: flex; align-items: center;" title="Limpiar Filtros">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
        </a>
    </form>
</div>

<div id="tenants-table-container" class="card">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 2px solid var(--secondary-color);">
                <th style="padding: 1rem; color: var(--text-light);">Nombre</th>
                <th style="padding: 1rem; color: var(--text-light);">DNI/CUIT</th>
                <th style="padding: 1rem; color: var(--text-light);">Teléfono</th>
                <th style="padding: 1rem; color: var(--text-light);">Email</th>
                <th style="padding: 1rem; color: var(--text-light);">Contratos</th>
                <th style="padding: 1rem; color: var(--text-light);">Deuda</th>
                <th style="padding: 1rem; color: var(--text-light); text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody id="tenants-tbody">
            @forelse($tenants as $tenant)
                <tr style="border-bottom: 1px solid var(--secondary-color); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem; font-weight: 500;">{{ $tenant->name }}</td>
                    <td style="padding: 1rem;">{{ $tenant->dni_cuit }}</td>
                    <td style="padding: 1rem;">{{ $tenant->phone ?? '-' }}</td>
                    <td style="padding: 1rem; color: var(--text-light); font-size: 0.9rem;">{{ $tenant->email ?? '-' }}</td>
                    <td style="padding: 1rem;">
                        <span style="background: var(--secondary-color); padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            {{ $tenant->leases_count }}
                        </span>
                    </td>
                    <td style="padding: 1rem;">
                        @php $debt = $tenant->total_debt; @endphp
                        <div style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;" onclick="openDebtModal({{ $tenant->id }})">
                            <span style="font-weight: 800; color: {{ $debt > 0 ? '#C53030' : '#38A169' }};">
                                ${{ number_format($debt, 2) }}
                            </span>
                            @if($debt > 0)
                                <div style="background: #C53030; color: white; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 900;">!</div>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <a href="{{ route('tenants.show', $tenant) }}" class="btn" style="color: var(--accent-color); font-size: 0.85rem;">Ver</a>
                            <a href="{{ route('tenants.edit', $tenant) }}" class="btn" style="color: #4A5568; font-size: 0.85rem;">Editar</a>
                            <form action="{{ route('tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('¿Eliminar inquilino?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="color: #C53030; font-size: 0.85rem;">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay inquilinos que coincidan con la búsqueda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="pagination-container" style="margin-top: 2rem; display: flex; justify-content: center;">
    {{ $tenants->links() }}
</div>

<style>
    #tenants-table-container.loading { opacity: 0.5; pointer-events: none; }
</style>

<!-- MODAL DE DEUDA -->
<div id="debt-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; display:none; align-items:center; justify-content:center; padding: 2rem;">
    <div class="card" style="width:100%; max-width:1000px; max-height:90vh; overflow-y:auto; position:relative; padding: 2.5rem;">
        <button onclick="closeDebtModal()" style="position:absolute; top:1.5rem; right:1.5rem; background:none; border:none; font-size:1.5rem; cursor:pointer; color: var(--text-light);">&times;</button>
        
        <h2 style="color: var(--primary-color); margin-bottom: 0.5rem;">Resumen de Deuda: <span id="modal-tenant-name"></span></h2>
        <p style="color: var(--text-light); margin-bottom: 2rem;">Listado de periodos con saldos pendientes.</p>

        <div id="modal-collections-container">
            <!-- Cargado vía JS -->
        </div>
    </div>
</div>

<!-- MODAL DE PAGO (SIMPLIFICADO) -->
<div id="payment-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1100; display:none; align-items:center; justify-content:center;">
    <div class="card" style="width:100%; max-width:600px; padding: 2.5rem;">
        <h3 id="payment-title" style="margin-bottom: 1.5rem; color: var(--primary-color);">Registrar Pago</h3>
        <form id="payment-form" onsubmit="submitPayment(event)">
            @csrf
            <input type="hidden" name="collection_id" id="pay-collection-id">
            
            <div id="payments-container">
                <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr auto; gap: 0.8rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display:block; font-size:0.7rem; font-weight:700; color:var(--text-light); text-transform:uppercase; margin-bottom:0.4rem;">Método</label>
                        <select name="payments[0][payment_method_id]" required style="width:100%; padding:0.6rem; border-radius:8px; border:1px solid var(--secondary-color);">
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:0.7rem; font-weight:700; color:var(--text-light); text-transform:uppercase; margin-bottom:0.4rem;">Monto</label>
                        <input type="number" step="0.01" name="payments[0][amount]" id="pay-amount" required style="width:100%; padding:0.6rem; border-radius:8px; border:1px solid var(--secondary-color);">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.7rem; font-weight:700; color:var(--text-light); text-transform:uppercase; margin-bottom:0.4rem;">Destino</label>
                        <select name="payments[0][destination]" required style="width:100%; padding:0.6rem; border-radius:8px; border:1px solid var(--secondary-color);">
                            <option value="agency">Inmobiliaria</option>
                            <option value="owner">Propietario</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closePaymentModal()" class="btn" style="background: var(--secondary-color);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar Pago</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDebtModal(tenantId) {
        const modal = document.getElementById('debt-modal');
        modal.style.display = 'flex';
        fetch(`/tenants/${tenantId}/pending-collections`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('modal-tenant-name').innerText = data.tenant;
                const container = document.getElementById('modal-collections-container');
                if (data.collections.length === 0) {
                    container.innerHTML = '<p style="text-align:center; padding: 2rem; color: var(--text-light);">No hay deudas pendientes para este inquilino.</p>';
                    return;
                }
                let html = `
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--secondary-color); text-align: left;">
                                <th style="padding: 1rem;">Propiedad</th>
                                <th style="padding: 1rem;">Periodo</th>
                                <th style="padding: 1rem;">Total</th>
                                <th style="padding: 1rem;">Pagado</th>
                                <th style="padding: 1rem;">Pendiente</th>
                                <th style="padding: 1rem; text-align: right;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>`;
                data.collections.forEach(c => {
                    html += `
                        <tr style="border-bottom: 1px solid var(--secondary-color);">
                            <td style="padding: 1rem; font-weight: 600;">${c.lease.property.location}</td>
                            <td style="padding: 1rem;">${c.month}/${c.year}</td>
                            <td style="padding: 1rem;">$${parseFloat(c.total_amount).toLocaleString()}</td>
                            <td style="padding: 1rem; color: #38A169;">$${parseFloat(c.paid_amount).toLocaleString()}</td>
                            <td style="padding: 1rem; color: #C53030; font-weight: 800;">$${parseFloat(c.pending_amount).toLocaleString()}</td>
                            <td style="padding: 1rem; text-align: right;">
                                <button onclick="openPaymentModal(${c.id}, ${c.pending_amount}, '${c.month}/${c.year}')" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Cobrar</button>
                            </td>
                        </tr>`;
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            });
    }

    function closeDebtModal() {
        document.getElementById('debt-modal').style.display = 'none';
    }

    function openPaymentModal(collectionId, amount, period) {
        document.getElementById('pay-collection-id').value = collectionId;
        document.getElementById('pay-amount').value = amount;
        document.getElementById('payment-title').innerText = `Cobrar Periodo ${period}`;
        document.getElementById('payment-modal').style.display = 'flex';
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').style.display = 'none';
    }

    // --- Lógica de Filtrado AJAX (Fluido) ---
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search-input');
    const autoFilters = document.querySelectorAll('.auto-filter');
    const tableContainer = document.getElementById('tenants-table-container');
    const paginationContainer = document.getElementById('pagination-container');

    async function fetchTenants() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = `${window.location.pathname}?${params.toString()}`;

        tableContainer.classList.add('loading');

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            tableContainer.innerHTML = doc.getElementById('tenants-table-container').innerHTML;
            paginationContainer.innerHTML = doc.getElementById('pagination-container').innerHTML;
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error fetching tenants:', error);
        } finally {
            tableContainer.classList.remove('loading');
        }
    }

    // Listeners
    autoFilters.forEach(select => {
        select.addEventListener('change', fetchTenants);
    });

    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchTenants, 300);
    });

    // Paginación AJAX
    document.addEventListener('click', (e) => {
        const pageLink = e.target.closest('#pagination-container a');
        if (pageLink) {
            e.preventDefault();
            fetchPage(pageLink.href);
        }
    });

    async function fetchPage(url) {
        tableContainer.classList.add('loading');
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            tableContainer.innerHTML = doc.getElementById('tenants-table-container').innerHTML;
            paginationContainer.innerHTML = doc.getElementById('pagination-container').innerHTML;
            window.history.pushState({}, '', url);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (error) {
            console.error('Error loading page:', error);
        } finally {
            tableContainer.classList.remove('loading');
        }
    }

    // Mantener foco si hay búsqueda activa al cargar
    window.addEventListener('DOMContentLoaded', () => {
        if (searchInput.value !== '') {
            searchInput.focus();
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
    });

    function submitPayment(e) {
        e.preventDefault();
        const form = e.target;
        const collectionId = document.getElementById('pay-collection-id').value;
        const formData = new FormData(form);

        fetch(`/collections/${collectionId}/pay`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                alert('Pago registrado correctamente');
                location.reload();
            } else {
                alert('Error al registrar pago');
            }
        });
    }
</script>

<style>
    /* Pagination Styling */
    .pagination { display: flex; gap: 0.5rem; list-style: none; padding: 0; align-items: center; }
    .page-item .page-link { border: 1px solid var(--secondary-color); padding: 0.6rem 1rem; border-radius: 8px; color: var(--primary-color); text-decoration: none; transition: all 0.3s; font-weight: 600; background: white; }
    .page-item.active .page-link { background: var(--primary-color); color: white; border-color: var(--primary-color); }
    .page-item.disabled .page-link { opacity: 0.5; cursor: not-allowed; }
    .page-item:hover:not(.active):not(.disabled) .page-link { background: var(--secondary-color); border-color: var(--accent-color); }
</style>
@endsection
