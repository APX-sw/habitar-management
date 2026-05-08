@extends('layouts.app')

@section('title', '| Contratos')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2rem; margin: 0;">Gestión de Alquileres</h1>
        <p style="color: var(--text-light); margin-top: 0.3rem;">Administra los contratos vigentes y su documentación.</p>
    </div>
    <a href="{{ route('leases.create') }}" class="btn btn-primary" style="padding: 0.8rem 1.5rem; border-radius: 10px; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nuevo Contrato
    </a>
</div>

<!-- Filtros de Alquileres AJAX -->
<div class="card" style="margin-bottom: 1.5rem; padding: 0.7rem 1.2rem; border: 1px solid var(--secondary-color); background: #f8fafc;">
    <form id="filter-form" action="{{ route('leases.index') }}" method="GET" style="display: flex; gap: 0.7rem; align-items: center; flex-wrap: wrap;">
        
        <!-- Búsqueda Principal -->
        <div style="flex: 2; min-width: 250px;">
            <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="🔍 Buscar por propiedad o inquilino..." style="width: 100%; padding: 0.6rem 1rem; border-radius: 8px; border: 1px solid #cbd5e0; font-size: 0.85rem;">
        </div>

        <!-- Mes de Vencimiento -->
        <div style="flex: 1; min-width: 130px;">
            <select name="expiry_month" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Mes Vencimiento</option>
                @php
                    $months = [1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 
                               7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'];
                @endphp
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ request('expiry_month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Estado -->
        <div style="flex: 1; min-width: 130px;">
            <select name="status" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Todos los Estados</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Vigentes</option>
                <option value="near" {{ request('status') == 'near' ? 'selected' : '' }}>Por Vencer</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Vencidos</option>
            </select>
        </div>

        <!-- Rango de Precio -->
        <div style="display: flex; gap: 0.4rem; align-items: center; flex: 1.5; min-width: 220px;">
            <input type="number" name="min_price" class="auto-filter-input" value="{{ request('min_price') }}" placeholder="Min $" style="width: 50%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; font-size: 0.85rem;">
            <span style="color: #cbd5e0;">-</span>
            <input type="number" name="max_price" class="auto-filter-input" value="{{ request('max_price') }}" placeholder="Max $" style="width: 50%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; font-size: 0.85rem;">
        </div>

        <a href="{{ route('leases.index') }}" class="btn" style="padding: 0.6rem 0.8rem; border-radius: 8px; background: #edf2f7; color: #4a5568; display: flex; align-items: center;" title="Limpiar Filtros">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
        </a>
    </form>
</div>

<div id="leases-table-container">
    <div class="card" style="padding: 0; overflow: hidden; border-radius: 15px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid var(--secondary-color);">
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Propiedad</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Inquilino</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Vencimiento</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Precio Base</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Valor Actual</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leases as $lease)
                    @php
                        try {
                            $currentPrice = $lease->calculateRentForDate(now()->month, now()->year);
                        } catch (\Exception $e) {
                            $currentPrice = $lease->base_price;
                        }
                    @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='white'">
                        <td style="padding: 1.2rem;">
                            <div style="font-weight: 700; color: var(--primary-color); font-size: 1rem; margin-bottom: 0.2rem;">{{ $lease->property->location }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-light); display: flex; align-items: center; gap: 0.3rem;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                {{ $lease->property->city->name }}, {{ $lease->property->province->name }}
                            </div>
                        </td>
                        <td style="padding: 1.2rem;">
                            <div style="font-weight: 600; color: #2d3748;">{{ $lease->tenant->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);">{{ $lease->tenant->dni_cuit }}</div>
                        </td>
                        <td style="padding: 1.2rem;">
                            @php
                                $endDate = \Carbon\Carbon::parse($lease->end_date);
                                $isExpired = $endDate->isPast();
                                $isNear = $endDate->diffInMonths(now()) < 2 && !$isExpired;
                            @endphp
                            <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                                <div style="color: {{ $isExpired ? '#C53030' : ($isNear ? '#B7791F' : '#2d3748') }}; font-weight: 700; font-size: 1rem;">
                                    {{ $endDate->format('d/m/Y') }}
                                </div>
                                <span style="
                                    display: inline-block;
                                    padding: 0.2rem 0.5rem;
                                    border-radius: 4px;
                                    font-size: 0.65rem;
                                    font-weight: 800;
                                    text-transform: uppercase;
                                    width: fit-content;
                                    background: {{ $isExpired ? '#FFF5F5' : ($isNear ? '#FFFAF0' : '#F0FFF4') }};
                                    color: {{ $isExpired ? '#C53030' : ($isNear ? '#B7791F' : '#38A169') }};
                                    border: 1px solid {{ $isExpired ? '#FEB2B2' : ($isNear ? '#FBD38D' : '#9AE6B4') }};
                                ">
                                    {{ $isExpired ? 'Vencido' : ($isNear ? 'Por Vencer' : 'Vigente') }}
                                </span>
                            </div>
                        </td>
                        <td style="padding: 1.2rem;">
                            <div style="font-weight: 800; color: var(--accent-color); font-size: 1.1rem;">${{ number_format($lease->base_price, 2) }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Base</div>
                        </td>
                        <td style="padding: 1.2rem;">
                            <div style="font-weight: 800; color: #553C9A; font-size: 1.1rem;">${{ number_format($currentPrice, 2) }}</div>
                            <div style="font-size: 0.7rem; color: #805AD5; text-transform: uppercase; font-weight: 700;">Alquiler Hoy</div>
                        </td>
                        <td style="padding: 1.2rem; text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap;">
                                <button onclick="openDocsModal({{ $lease->id }}, '{{ $lease->property->location }}')" class="btn" style="background: #ebf4ff; color: #2b6cb0; font-size: 0.75rem; font-weight: 700; border: 1px solid #bee3f8; padding: 0.4rem 0.8rem;">📂 DOCS</button>
                                <a href="{{ route('leases.show', $lease) }}" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.75rem; font-weight: 700; border: 1px solid #e2e8f0; padding: 0.4rem 0.8rem;">VER</a>
                                <a href="{{ route('leases.renew', $lease) }}" class="btn" style="background: var(--primary-color); color: white; font-size: 0.75rem; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-decoration: none;" title="Renovar Contrato">
                                    RENOVAR
                                </a>
                                <a href="{{ route('leases.renegotiate', $lease) }}" class="btn" style="background: #E9D8FD; color: #553C9A; font-size: 0.75rem; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid #D6BCFA; text-decoration: none;" title="Renegociar Contrato">
                                    RENEGOCIAR
                                </a>
                                @if($lease->is_active)
                                    <a href="{{ route('leases.show', $lease) }}?terminate=1" class="btn" style="background: #fff5f5; color: #c53030; font-size: 0.75rem; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid #feb2b2; text-decoration: none;" title="Finalizar Contrato">
                                        FINALIZAR
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 4rem; text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📄</div>
                            <p style="color: var(--text-light); font-size: 1.1rem;">No hay contratos que coincidan con la búsqueda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="pagination-container" style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $leases->links() }}
    </div>
</div>

<style>
    #leases-table-container.loading { opacity: 0.5; pointer-events: none; }
</style>

@include('leases.partials.docs_modal')
@endsection

@section('scripts')
<script>
    // --- Lógica de Filtrado AJAX (Fluido) ---
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search-input');
    const autoFilters = document.querySelectorAll('.auto-filter, .auto-filter-input');
    const tableContainer = document.getElementById('leases-table-container');

    async function fetchLeases() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = `${window.location.pathname}?${params.toString()}`;

        tableContainer.classList.add('loading');

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newContent = doc.getElementById('leases-table-container');
            if (newContent) {
                tableContainer.innerHTML = newContent.innerHTML;
            }
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error fetching leases:', error);
        } finally {
            tableContainer.classList.remove('loading');
        }
    }

    // Listeners con Debounce
    let timer;
    function handleFilterChange() {
        clearTimeout(timer);
        timer = setTimeout(fetchLeases, 300);
    }

    if (searchInput) searchInput.addEventListener('input', handleFilterChange);
    
    autoFilters.forEach(el => {
        el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', handleFilterChange);
    });

    // Paginación AJAX
    document.addEventListener('click', (e) => {
        const pageLink = e.target.closest('.pagination a');
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

            const newContent = doc.getElementById('leases-table-container');
            if (newContent) {
                tableContainer.innerHTML = newContent.innerHTML;
            }
            window.history.pushState({}, '', url);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (error) {
            console.error('Error loading page:', error);
        } finally {
            tableContainer.classList.remove('loading');
        }
    }

    // Mantener foco si hay búsqueda activa
    window.addEventListener('DOMContentLoaded', () => {
        if (searchInput && searchInput.value !== '') {
            searchInput.focus();
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
    });
</script>
@endsection
