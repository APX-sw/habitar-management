@extends('layouts.app')

@section('title', '| Gastos')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Gastos</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Registro de egresos generales o de propiedades.</p>
    </div>
    <a href="{{ route('expenses.create') }}" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700;">➕ Registrar Gasto</a>
</div>

<!-- FILTROS -->
<div class="card" style="padding: 1.5rem; margin-bottom: 2rem; background: #f8fafc;">
    <form id="filterForm" action="{{ route('expenses.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: flex-end;">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Propiedad</label>
            <select name="property_id" onchange="applyFilters()" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
                <option value="">Todas</option>
                <option value="none" {{ request('property_id') == 'none' ? 'selected' : '' }}>Gasto Inmobiliaria</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>{{ $prop->location }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Caja</label>
            <select name="account_id" onchange="applyFilters()" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
                <option value="">Todas</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Concepto</label>
            <select name="category_id" onchange="applyFilters()" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
                <option value="">Todos</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Desde</label>
            <input type="date" name="date_from" onchange="applyFilters()" value="{{ request('date_from') }}" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.4rem;">Hasta</label>
            <input type="date" name="date_to" onchange="applyFilters()" value="{{ request('date_to') }}" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #d2d6dc; font-size: 0.85rem;">
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('expenses.index') }}" class="btn" style="background: #edf2f7; color: #4a5568; padding: 0.6rem 1rem; width: 100%; text-align: center;">Limpiar</a>
        </div>
    </form>
</div>

<div class="card" style="padding: 2rem;">
    <div id="tableContainer" style="overflow-x: auto;">
        @include('expenses.partials.table')
    </div>
</div>

<!-- Modal de Edición / Adjunto -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--primary-color); margin: 0;">Editar Gasto</h2>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; color: #a0aec0; cursor: pointer;">&times;</button>
        </div>
        <form id="editForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Descripción</label>
                <input type="text" name="description" id="modalDescription" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Actualizar/Añadir Comprobante</label>
                <input type="file" name="attachment" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; background: white;">
                <p style="font-size: 0.7rem; color: #a0aec0; margin-top: 0.5rem;">Si ya tiene un archivo, se reemplazará por el nuevo.</p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Guardar Cambios</button>
        </form>
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

    // Modal Logic
    function openEditModal(id, description) {
        const form = document.getElementById('editForm');
        form.action = `/expenses/${id}`;
        document.getElementById('modalDescription').value = description || '';
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Interceptar clics en la paginación para AJAX
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
