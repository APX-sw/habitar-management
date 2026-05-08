@extends('layouts.app')

@section('title', '| Propietarios')

@section('content')
<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="color: var(--primary-color);">Gestión de Propietarios</h1>
    <a href="{{ route('owners.create') }}" class="btn btn-primary">+ Nuevo Propietario</a>
</div>

<!-- Filtro de Búsqueda Compacto -->
<div class="card" style="margin-bottom: 1.5rem; padding: 0.7rem 1.2rem; border: 1px solid var(--secondary-color); background: #f8fafc;">
    <form id="filter-form" action="{{ route('owners.index') }}" method="GET" style="display: flex; gap: 0.7rem; align-items: center;">
        <div style="flex: 1; position: relative;">
            <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="🔍 Buscar por nombre o DNI/CUIT..." style="width: 100%; padding: 0.6rem 1rem; border-radius: 8px; border: 1px solid #cbd5e0; font-size: 0.85rem; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
        </div>
        <a href="{{ route('owners.index') }}" class="btn" style="padding: 0.6rem 0.8rem; border-radius: 8px; background: #edf2f7; color: #4a5568; display: flex; align-items: center;" title="Limpiar Búsqueda">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
        </a>
    </form>
</div>

<div id="owners-table-container" class="card">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 2px solid var(--secondary-color);">
                <th style="padding: 1rem; color: var(--text-light);">Nombre</th>
                <th style="padding: 1rem; color: var(--text-light);">DNI/CUIT</th>
                <th style="padding: 1rem; color: var(--text-light);">Teléfono</th>
                <th style="padding: 1rem; color: var(--text-light);">Email</th>
                <th style="padding: 1rem; color: var(--text-light);">Propiedades</th>
                <th style="padding: 1rem; color: var(--text-light); text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody id="owners-tbody">
            @forelse($owners as $owner)
                <tr style="border-bottom: 1px solid var(--secondary-color); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem; font-weight: 500;">{{ $owner->name }}</td>
                    <td style="padding: 1rem;">{{ $owner->dni_cuit }}</td>
                    <td style="padding: 1rem;">{{ $owner->phone ?? '-' }}</td>
                    <td style="padding: 1rem; color: var(--text-light); font-size: 0.9rem;">{{ $owner->email ?? '-' }}</td>
                    <td style="padding: 1rem;">
                        <span style="background: var(--secondary-color); padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            {{ $owner->properties_count }}
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <a href="{{ route('owners.show', $owner) }}" class="btn" style="color: var(--accent-color); font-size: 0.9rem; padding: 0.4rem 0.8rem;">Ver</a>
                        <a href="{{ route('owners.edit', $owner) }}" class="btn" style="color: #4A5568; font-size: 0.9rem; padding: 0.4rem 0.8rem;">Editar</a>
                        <button type="button" class="btn" style="color: #C53030; font-size: 0.9rem; padding: 0.4rem 0.8rem;" 
                            onclick="openDeleteModal('{{ route('owners.destroy', $owner) }}', '{{ $owner->name }}')">
                            Eliminar
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay propietarios que coincidan con la búsqueda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="pagination-container" style="margin-top: 2rem; display: flex; justify-content: center;">
    {{ $owners->links() }}
</div>

<style>
    /* Pagination Styling */
    .pagination { display: flex; gap: 0.5rem; list-style: none; padding: 0; align-items: center; }
    .page-item .page-link { border: 1px solid var(--secondary-color); padding: 0.6rem 1rem; border-radius: 8px; color: var(--primary-color); text-decoration: none; transition: all 0.3s; font-weight: 600; background: white; }
    .page-item.active .page-link { background: var(--primary-color); color: white; border-color: var(--primary-color); }
    .page-item.disabled .page-link { opacity: 0.5; cursor: not-allowed; }
    .page-item:hover:not(.active):not(.disabled) .page-link { background: var(--secondary-color); border-color: var(--accent-color); }
    
    /* Loading overlay */
    #owners-table-container.loading { opacity: 0.5; pointer-events: none; }
</style>
@endsection

@section('scripts')
<!-- Modal de Eliminación Personalizado -->
<div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2.5rem; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="background: #fff5f5; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #c53030;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
        </div>
        
        <h2 style="color: var(--primary-color); margin-bottom: 1rem;">¿Eliminar Propietario?</h2>
        <p style="color: var(--text-light); margin-bottom: 1.5rem; line-height: 1.5;">
            Estás a punto de eliminar a <strong id="deleteOwnerName" style="color: var(--primary-color);"></strong>.<br>
            <span style="color: #c53030; font-weight: 600;">Esta acción también eliminará todas sus propiedades asociadas que no estén alquiladas.</span>
        </p>
        
        <div style="background: #ebf8ff; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-size: 0.85rem; color: #2b6cb0; text-align: left;">
            <strong>Nota:</strong> Si el propietario tiene contratos de alquiler activos, el sistema bloqueará la eliminación por seguridad.
        </div>

        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button type="button" onclick="closeDeleteModal()" class="btn" style="background: #edf2f7; color: #4a5568; flex: 1;">Cancelar</button>
                <button type="submit" class="btn" style="background: #c53030; color: white; flex: 1;">Eliminar Permanentemente</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(actionUrl, ownerName) {
        document.getElementById('deleteForm').action = actionUrl;
        document.getElementById('deleteOwnerName').innerText = ownerName;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Cerrar al hacer clic fuera del modal
    window.onclick = function(event) {
        let modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeDeleteModal();
        }
    }

    // --- Lógica de Filtrado AJAX (Fluido) ---
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search-input');
    const tableContainer = document.getElementById('owners-table-container');
    const paginationContainer = document.getElementById('pagination-container');

    async function fetchOwners() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = `${window.location.pathname}?${params.toString()}`;

        tableContainer.classList.add('loading');

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Reemplazar solo los contenedores necesarios
            tableContainer.innerHTML = doc.getElementById('owners-table-container').innerHTML;
            paginationContainer.innerHTML = doc.getElementById('pagination-container').innerHTML;
            
            // Actualizar URL sin recargar la página
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error fetching owners:', error);
        } finally {
            tableContainer.classList.remove('loading');
        }
    }

    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchOwners, 300); // 300ms es suficiente para sentir fluidez
    });

    // Manejar clics en la paginación para que también sean AJAX
    document.addEventListener('click', (e) => {
        const pageLink = e.target.closest('#pagination-container a');
        if (pageLink) {
            e.preventDefault();
            const url = new URL(pageLink.href);
            const searchParams = new URLSearchParams(url.search);
            
            // Sincronizar el input de búsqueda si es necesario
            if (searchParams.has('search')) {
                searchInput.value = searchParams.get('search');
            }
            
            // Reutilizar la lógica de fetch con la URL del link
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

            tableContainer.innerHTML = doc.getElementById('owners-table-container').innerHTML;
            paginationContainer.innerHTML = doc.getElementById('pagination-container').innerHTML;
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error loading page:', error);
        } finally {
            tableContainer.classList.remove('loading');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
</script>
@endsection

