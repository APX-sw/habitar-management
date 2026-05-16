@extends('layouts.app')

@section('title', '| Propiedades')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin-bottom: 0.5rem;">Inventario de Propiedades</h1>
        <p style="color: var(--text-light);">Gestiona y monitorea el estado de todos tus inmuebles.</p>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <!-- Toggle View -->
        <div style="background: #edf2f7; padding: 0.3rem; border-radius: 10px; display: flex; gap: 0.2rem;">
            <button onclick="switchView('grid')" id="btn-grid" class="view-btn active-view">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </button>
            <button onclick="switchView('table')" id="btn-table" class="view-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            </button>
        </div>
        <a href="{{ route('properties.create') }}" class="btn btn-primary" style="padding: 0.8rem 1.5rem; border-radius: 10px; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Nueva Propiedad
        </a>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem; padding: 0.7rem 1.2rem; border: 1px solid var(--secondary-color); background: #f8fafc;">
    <form id="filter-form" action="{{ route('properties.index') }}" method="GET" style="display: flex; gap: 0.7rem; align-items: center; flex-wrap: wrap;">
        
        <!-- Búsqueda Principal -->
        <div style="flex: 2; min-width: 250px;">
            <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="🔍 Buscar por dirección, ciudad o dueño..." style="width: 100%; padding: 0.6rem 1rem; border-radius: 8px; border: 1px solid #cbd5e0; font-size: 0.85rem; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
        </div>

        <!-- Estado -->
        <div style="flex: 1; min-width: 140px;">
            <select name="status" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Cualquier Estado</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Alquilada</option>
            </select>
        </div>

        <!-- Ciudad -->
        <div style="flex: 1; min-width: 140px;">
            <select name="city_id" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Todas las Ciudades</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tipo -->
        <div style="flex: 1; min-width: 140px;">
            <select name="property_type_id" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Todos los Tipos</option>
                @foreach($propertyTypes as $type)
                    <option value="{{ $type->id }}" {{ request('property_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Ambientes -->
        <div style="flex: 1; min-width: 120px;">
            <select name="rooms" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Ambientes</option>
                @for($i=1; $i<=6; $i++)
                    <option value="{{ $i }}" {{ request('rooms') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'amb' : 'ambs' }}</option>
                @endfor
            </select>
        </div>

        <!-- Dueño -->
        <div style="flex: 1; min-width: 140px;">
            <select name="owner_id" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Cualquier Dueño</option>
                @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('properties.index') }}" class="btn" style="padding: 0.6rem 0.8rem; border-radius: 8px; background: #edf2f7; color: #4a5568; display: flex; align-items: center;" title="Limpiar Filtros">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
        </a>
    </form>
</div>

<!-- Estilos internos para el toggle y vistas -->
<style>
    .view-btn { border: none; background: transparent; padding: 0.5rem 0.8rem; border-radius: 8px; cursor: pointer; color: #718096; transition: all 0.3s; display: flex; align-items: center; }
    .active-view { background: white; color: var(--primary-color); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    
    .status-badge { padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .status-active { background: #C6F6D5; color: #22543D; }
    .status-available { background: #FED7D7; color: #822727; }

    .amenity-icon { display: flex; align-items: center; gap: 0.4rem; color: var(--text-light); font-size: 0.85rem; }
    
    /* Grid View Styles */
    .property-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
    .property-card { display: flex; flex-direction: column; height: 100%; border: 1px solid var(--secondary-color); border-radius: 15px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; background: white; }
    .property-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    
    /* Table View Styles */
    .property-table-container { background: white; border-radius: 15px; border: 1px solid var(--secondary-color); overflow: hidden; display: none; }
    .property-table { width: 100%; border-collapse: collapse; }
    .property-table th { background: #f8fafc; padding: 1.2rem; text-align: left; font-size: 0.8rem; color: #718096; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--secondary-color); }
    .property-table td { padding: 1.2rem; border-bottom: 1px solid var(--secondary-color); vertical-align: middle; }

    /* Pagination Styling */
    .pagination { display: flex; gap: 0.5rem; list-style: none; padding: 0; align-items: center; }
    .page-item .page-link { border: 1px solid var(--secondary-color); padding: 0.6rem 1rem; border-radius: 8px; color: var(--primary-color); text-decoration: none; transition: all 0.3s; font-weight: 600; background: white; }
    .page-item.active .page-link { background: var(--primary-color); color: white; border-color: var(--primary-color); }
    .page-item.disabled .page-link { opacity: 0.5; cursor: not-allowed; }
    .page-item:hover:not(.active):not(.disabled) .page-link { background: var(--secondary-color); border-color: var(--accent-color); }
</style>

<div id="properties-content">
    <!-- VISTA GRID (KANBAN) -->
    <div id="grid-view" class="property-grid" style="display: {{ request('view', 'grid') == 'grid' ? 'grid' : 'none' }};">
        @forelse($properties as $property)
            <div class="property-card">
                <!-- Header Card -->
                <div style="padding: 1.5rem; flex-grow: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <span class="status-badge {{ $property->activeLease ? 'status-active' : 'status-available' }}">
                            {{ $property->activeLease ? 'Alquilada' : 'Disponible' }}
                        </span>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <button onclick="openQuickUpload({{ $property->id }}, '{{ $property->location }}')" style="background: none; border: none; color: var(--primary-color); cursor: pointer; padding: 0;" title="Subir Documento">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                            </button>
                            <a href="{{ route('properties.edit', $property) }}" style="color: var(--text-light);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                            @if(!$property->activeLease)
                            <form action="{{ route('properties.destroy', $property) }}" method="POST" onsubmit="return confirm('¿Eliminar propiedad?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:#C53030; cursor:pointer; padding:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <h3 style="font-size: 1.25rem; color: var(--primary-color); margin-bottom: 0.3rem;">{{ $property->location }}</h3>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.2rem; background: #f0f4f8; padding: 0.5rem 0.8rem; border-radius: 8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2b6cb0" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span style="color: #2d3748; font-size: 0.9rem; font-weight: 700;">{{ $property->city->name ?? 'N/A' }}</span>
                        <span style="color: #718096; font-size: 0.8rem;">• {{ $property->province->name ?? 'N/A' }}</span>
                    </div>

                    <!-- Amenities Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1.5rem;">
                        <div class="amenity-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            {{ $property->rooms }} amb.
                        </div>
                        <div class="amenity-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><line x1="9" y1="20" x2="9" y2="4"></line></svg>
                            {{ $property->square_meters ?? '0' }} m²
                        </div>
                        <div class="amenity-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                            {{ $property->type->name ?? 'Tipo' }}
                        </div>
                        <div class="amenity-icon">
                            @if($property->has_garage)
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="22" height="13" rx="2" ry="2"></rect><path d="M7 21h10"></path><path d="M12 16v5"></path></svg>
                                Cochera
                            @else
                                <span style="opacity: 0.3; text-decoration: line-through;">Cochera</span>
                            @endif
                        </div>
                    </div>

                    <div style="border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                        <label style="display: block; color: #94a3b8; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.3rem;">Dueño</label>
                        <a href="{{ route('owners.show', $property->owner) }}" style="color: var(--primary-color); font-weight: 600; text-decoration: none; font-size: 0.9rem;">{{ $property->owner->name }}</a>
                    </div>
                </div>

                <!-- Footer Card -->
                <div style="background: #f8fafc; padding: 1rem 1.5rem; border-top: 1px solid var(--secondary-color);">
                    @if($property->activeLease)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8rem; color: #64748b;">Inquilino: <strong>{{ $property->activeLease->tenant->name }}</strong></span>
                            <a href="{{ route('leases.show', $property->activeLease) }}" class="btn" style="background: var(--primary-color); color: white; font-size: 0.75rem; padding: 0.4rem 1rem;">Ver Contrato</a>
                        </div>
                    @else
                        <a href="{{ route('leases.create', ['property_id' => $property->id]) }}" class="btn" style="width: 100%; background: #ebf8ff; color: #2b6cb0; font-size: 0.8rem; font-weight: 700; text-align: center;">ALQUILAR AHORA</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
                <p style="color: var(--text-light);">No hay propiedades que coincidan con los filtros.</p>
            </div>
        @endforelse
    </div>

    <!-- VISTA TABLA -->
    <div id="table-view" class="property-table-container" style="display: {{ request('view') == 'table' ? 'block' : 'none' }};">
        <table class="property-table">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Dirección</th>
                    <th>Propietario</th>
                    <th>Inquilino</th>
                    <th>Ubicación</th>
                    <th>Detalle</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($properties as $property)
                    <tr>
                        <td>
                            <span class="status-badge {{ $property->activeLease ? 'status-active' : 'status-available' }}" style="font-size: 0.65rem;">
                                {{ $property->activeLease ? 'Alquilada' : 'Disponible' }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--primary-color);">{{ $property->location }}</div>
                        </td>
                        <td>
                            <a href="{{ route('owners.show', $property->owner) }}" style="color: var(--primary-color); text-decoration: none; font-size: 0.85rem; font-weight: 500;">{{ $property->owner->name }}</a>
                        </td>
                        <td>
                            @if($property->activeLease)
                                <a href="{{ route('tenants.show', $property->activeLease->tenant_id) }}" style="color: var(--accent-color); text-decoration: none; font-size: 0.85rem; font-weight: 700;">{{ $property->activeLease->tenant->name }}</a>
                            @else
                                <span style="color: var(--text-light); font-size: 0.85rem;">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #2d3748; font-size: 0.85rem;">{{ $property->city->name ?? 'N/A' }}</div>
                            <div style="font-size: 0.7rem; color: #718096; text-transform: uppercase;">{{ $property->province->name ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; font-weight: 600;">{{ $property->type->name ?? 'Tipo' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);">{{ $property->rooms }} Amb. | {{ $property->square_meters ?? '0' }} m²</div>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                <button onclick="openQuickUpload({{ $property->id }}, '{{ $property->location }}')" class="btn" style="background: #EBF8FF; color: #3182CE; font-size: 0.7rem; padding: 0.4rem 0.8rem; border: none; cursor: pointer;" title="Subir Documento">📎 Doc</button>
                                <a href="{{ route('properties.show', $property) }}" class="btn" style="background: #edf2f7; color: #4a5568; font-size: 0.7rem; padding: 0.4rem 0.8rem;">Ver</a>
                                <a href="{{ route('properties.edit', $property) }}" class="btn" style="background: #edf2f7; color: #4a5568; font-size: 0.7rem; padding: 0.4rem 0.8rem;">Editar</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="pagination-container" style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $properties->links() }}
    </div>
</div>

<!-- Modal Rápido Subir Documento -->
<div id="quickUploadModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="color: var(--primary-color); margin: 0;">Subir Documentación</h2>
                <p id="quick-prop-name" style="font-size: 0.85rem; color: var(--text-light); margin: 0.2rem 0 0 0; font-weight: 600;"></p>
            </div>
            <button onclick="closeQuickUpload()" style="background: none; border: none; font-size: 1.5rem; color: #a0aec0; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('property-documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="property_id" id="quick-property-id">
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Seleccionar Archivo (Máx 10MB)</label>
                <input type="file" name="file" required style="width: 100%; padding: 1rem; border: 2px dashed #e2e8f0; border-radius: 10px; cursor: pointer;">
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="closeQuickUpload()" class="btn" style="flex: 1; background: #edf2f7; color: #4a5568;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Subir Archivo</button>
            </div>
        </form>
    </div>
</div>

<style>
    #properties-content.loading { opacity: 0.5; pointer-events: none; }
</style>

<script>
    function openQuickUpload(id, location) {
        document.getElementById('quick-property-id').value = id;
        document.getElementById('quick-prop-name').innerText = location;
        document.getElementById('quickUploadModal').style.display = 'flex';
    }

    function closeQuickUpload() {
        document.getElementById('quickUploadModal').style.display = 'none';
    }

    function switchView(view) {
        const grid = document.getElementById('grid-view');
        const table = document.getElementById('table-view');
        const btnGrid = document.getElementById('btn-grid');
        const btnTable = document.getElementById('btn-table');

        if (view === 'grid') {
            grid.style.display = 'grid';
            table.style.display = 'none';
            btnGrid.classList.add('active-view');
            btnTable.classList.remove('active-view');
        } else {
            grid.style.display = 'none';
            table.style.display = 'block';
            btnGrid.classList.remove('active-view');
            btnTable.classList.add('active-view');
        }
        
        localStorage.setItem('property_view_preference', view);
        
        // Actualizar URL sin recargar para que el filtro AJAX sepa qué vista mantener
        const url = new URL(window.location);
        url.searchParams.set('view', view);
        window.history.pushState({}, '', url);
    }

    // --- Lógica de Auto-filtrado AJAX ---
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search-input');
    const autoFilters = document.querySelectorAll('.auto-filter');
    const contentContainer = document.getElementById('properties-content');

    async function fetchProperties() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        // Mantener la vista actual en la petición
        const currentView = localStorage.getItem('property_view_preference') || 'grid';
        params.set('view', currentView);

        const url = `${window.location.pathname}?${params.toString()}`;
        contentContainer.classList.add('loading');

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            document.getElementById('properties-content').innerHTML = doc.getElementById('properties-content').innerHTML;
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error fetching properties:', error);
        } finally {
            contentContainer.classList.remove('loading');
        }
    }

    // Al cargar la página, restaurar preferencia
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const savedView = urlParams.get('view') || localStorage.getItem('property_view_preference') || 'grid';
        switchView(savedView);

        // Listeners
        autoFilters.forEach(select => {
            select.addEventListener('change', fetchProperties);
        });

        let searchTimeout;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(fetchProperties, 300);
        });

        // Paginación AJAX
        document.addEventListener('click', (e) => {
            const pageLink = e.target.closest('#pagination-container a');
            if (pageLink) {
                e.preventDefault();
                fetchPage(pageLink.href);
            }
        });
    });

    async function fetchPage(url) {
        contentContainer.classList.add('loading');
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            document.getElementById('properties-content').innerHTML = doc.getElementById('properties-content').innerHTML;
            window.history.pushState({}, '', url);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (error) {
            console.error('Error loading page:', error);
        } finally {
            contentContainer.classList.remove('loading');
        }
    }
</script>
@endsection
