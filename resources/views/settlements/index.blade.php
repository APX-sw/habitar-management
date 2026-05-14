@extends('layouts.app')

@section('title', '| Rendiciones')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Rendiciones a Propietarios</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Gestión de pagos y liquidaciones mensuales.</p>
    </div>
    <a href="{{ route('settlements.create') }}" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700;">➕ Nueva Rendición</a>
</div>

<!-- FILTROS -->
<div class="card" style="padding: 1.5rem; margin-bottom: 2rem; background: #f8fafc;">
    <form id="filterForm" action="{{ route('settlements.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: flex-end;">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Propietario</label>
            <select name="owner_id" onchange="applyFilters()" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
                <option value="">Todos</option>
                @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Estado</label>
            <select name="status" onchange="applyFilters()" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
                <option value="">Todos</option>
                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Pendiente de Pago</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagada</option>
            </select>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Mes</label>
                <select name="month" onchange="applyFilters()" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
                    <option value="">Todos</option>
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Año</label>
                <select name="year" onchange="applyFilters()" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
                    <option value="">Todos</option>
                    @for($i=2024; $i<=2027; $i++)
                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('settlements.index') }}" class="btn" style="background: #edf2f7; color: #4a5568; padding: 0.6rem 1rem; width: 100%; text-align: center;">Limpiar</a>
        </div>
    </form>
</div>

<div class="card" style="padding: 2rem;">
    <div id="tableContainer" style="overflow-x: auto;">
        @include('settlements.partials.table')
    </div>
</div>
@endsection

@section('scripts')
<script>
    function applyFilters() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        // Update URL
        window.history.pushState({}, '', `${window.location.pathname}?${params.toString()}`);

        // Fetch new table
        fetch(`${window.location.pathname}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('tableContainer').innerHTML = html;
        });
    }

    // AJAX Paginación
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination-links a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('tableContainer').innerHTML = html;
                window.history.pushState({}, '', url);
            });
        }
    });
</script>
@endsection
