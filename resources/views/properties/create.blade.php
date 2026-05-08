@extends('layouts.app')

@section('title', '| Nueva Propiedad')

@section('styles')
<style>
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .full-width {
        grid-column: 1 / -1;
    }
    .search-container {
        position: relative;
    }
    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid var(--secondary-color);
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 100;
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }
    .search-item {
        padding: 0.8rem;
        cursor: pointer;
        border-bottom: 1px solid var(--secondary-color);
    }
    .search-item:hover {
        background: var(--bg-body);
    }
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius);
        width: 100%;
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--secondary-color); padding-bottom: 1rem;">
        <div>
            <h1 style="color: var(--primary-color); font-size: 2rem;">Nueva Propiedad</h1>
            <p style="color: var(--text-light); font-size: 0.95rem;">Completa los datos técnicos del inmueble.</p>
        </div>
        <div style="display: flex; gap: 0.8rem;">
            <a href="{{ route('settings.index') }}" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Configuración
            </a>
            <a href="{{ route('properties.index') }}" class="btn" style="background: var(--primary-color); color: white;">Cancelar</a>
        </div>
    </div>

    <form action="{{ route('properties.store') }}" method="POST">
        @csrf
        <div class="card" style="margin-bottom: 2rem;">
            <div class="form-grid">
                <!-- Propietario -->
                <div class="full-width" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; font-weight: 600; font-size: 1.1rem; color: var(--primary-color);">Selección de Propietario</label>
                    <div style="display: flex; gap: 0.8rem; margin-bottom: 1rem;">
                        <div class="search-container" style="flex: 1;">
                            <input type="text" id="owner-search" placeholder="Buscar por nombre o DNI/CUIT..." autocomplete="off" style="width: 100%; padding: 1rem; border-radius: var(--border-radius); border: 2px solid var(--secondary-color); font-size: 1rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--accent-color)'" onblur="this.style.borderColor='var(--secondary-color)'">
                            <div id="search-results" class="search-results"></div>
                            <input type="hidden" name="owner_id" id="owner_id_hidden" required>
                        </div>
                        <button type="button" onclick="openOwnerModal()" class="btn btn-primary" style="padding: 0 1.5rem; border-radius: var(--border-radius);">+ Nuevo Dueño</button>
                    </div>

                    <!-- Selected Owner Display -->
                    <div id="selected-owner-card" style="display: none; background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 8px; padding: 1.2rem; margin-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 0.8rem; color: #38a169; font-weight: 700; text-transform: uppercase;">Propietario Seleccionado</p>
                                <h3 id="display-owner-name" style="color: #22543d; margin: 0.2rem 0;"></h3>
                            </div>
                            <button type="button" onclick="toggleOwnerInfo()" id="btn-toggle-info" class="btn" style="background: #38a169; color: white; font-size: 0.8rem;">
                                Ver Info Propietario
                            </button>
                        </div>
                        
                        <div id="owner-info-details" style="display: none; margin-top: 1.5rem; border-top: 1px solid #c6f6d5; padding-top: 1rem; animation: slideDown 0.3s ease-out;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <div>
                                    <p style="font-size: 0.75rem; color: #38a169; font-weight: 700;">DNI / CUIT</p>
                                    <p id="display-owner-dni" style="font-weight: 500;"></p>
                                </div>
                                <div>
                                    <p style="font-size: 0.75rem; color: #38a169; font-weight: 700;">CONTACTO</p>
                                    <p id="display-owner-contact" style="font-weight: 500;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación y Datos Técnicos -->
                <div class="full-width" style="margin-top: 1rem;">
                    <label style="display: block; margin-bottom: 1.5rem; font-weight: 600; font-size: 1.1rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Detalles de la Propiedad</label>
                </div>

                <div class="full-width">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Dirección / Calle y Número</label>
                    <input type="text" name="location" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <!-- Comodidades -->
                <div class="full-width" style="margin-top: 1rem;">
                    <label style="display: block; margin-bottom: 1.5rem; font-weight: 600; font-size: 1.1rem; color: var(--primary-color); border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Comodidades y Detalles</label>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Ambientes</label>
                    <input type="number" name="rooms" value="1" min="1" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Baños</label>
                    <input type="number" name="bathrooms" value="1" min="0" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <div class="full-width">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Metros Cuadrados Totales (m²)</label>
                    <input type="number" step="0.01" name="square_meters" placeholder="Ej: 45.5" style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>

                <div class="full-width" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin: 1rem 0;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        <input type="checkbox" name="has_garage" value="1" style="width: 18px; height: 18px;">
                        <span style="font-weight: 500;">Cochera</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        <input type="checkbox" name="has_patio" value="1" style="width: 18px; height: 18px;">
                        <span style="font-weight: 500;">Patio</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        <input type="checkbox" name="has_balcony" value="1" style="width: 18px; height: 18px;">
                        <span style="font-weight: 500;">Balcón</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                        <input type="checkbox" name="pets_allowed" value="1" style="width: 18px; height: 18px;">
                        <span style="font-weight: 500;">Acepta Mascotas</span>
                    </label>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Provincia</label>
                    <select name="province_id" id="province-select" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                        <option value="">-- Seleccionar --</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Localidad</label>
                    <select name="city_id" id="city-select" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);" disabled>
                        <option value="">-- Seleccione Provincia primero --</option>
                    </select>
                </div>

                <div class="full-width">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tipo de Inmueble</label>
                    <select name="property_type_id" required style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                        <option value="">-- Seleccionar --</option>
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="full-width">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción / Comodidades</label>
                    <textarea name="description" rows="4" placeholder="Ej: 2 dormitorios, cochera, patio amplio..." style="width: 100%; padding: 0.9rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);"></textarea>
                </div>

                <div class="full-width" style="display: flex; justify-content: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 1.2rem 5rem; font-size: 1.1rem; border-radius: 50px; box-shadow: 0 4px 15px rgba(56, 178, 172, 0.3);">
                        Guardar Propiedad en Sistema
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal para Nuevo Propietario -->
<div id="owner-modal" class="modal-overlay">
    <div class="modal-content">
        <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Cargar Nuevo Propietario</h2>
        <form id="new-owner-form">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre / Razón Social</label>
                    <input type="text" name="name" required style="width: 100%; padding: 0.8rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">DNI / CUIT</label>
                    <input type="text" name="dni_cuit" required style="width: 100%; padding: 0.8rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                    <input type="email" name="email" style="width: 100%; padding: 0.8rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Teléfono</label>
                    <input type="text" name="phone" style="width: 100%; padding: 0.8rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Notas de Contacto</label>
                <textarea name="contact" rows="2" style="width: 100%; padding: 0.8rem; border-radius: 4px; border: 1px solid var(--secondary-color);"></textarea>
            </div>

            <div style="border-top: 1px solid var(--secondary-color); padding-top: 1.5rem;">
                <h3 style="color: var(--primary-color); font-size: 1.1rem; margin-bottom: 1rem;">Cuentas Bancarias</h3>
                <div id="modal-bank-accounts">
                    <div style="background: var(--bg-body); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.5rem;">
                            <input type="text" name="bank_accounts[0][cbu_alias]" placeholder="CBU / Alias" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                            <input type="text" name="bank_accounts[0][holder_name]" placeholder="Titular" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                        </div>
                        <input type="text" name="bank_accounts[0][holder_cuit]" placeholder="CUIT Titular" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                    </div>
                </div>
                <button type="button" onclick="addModalBankAccount()" class="btn" style="background: var(--secondary-color); font-size: 0.8rem;">+ Agregar Cuenta</button>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" onclick="closeOwnerModal()" class="btn" style="background: var(--secondary-color);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar y Seleccionar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<script>
    const owners = @json($owners);
    const searchInput = document.getElementById('owner-search');
    const resultsContainer = document.getElementById('search-results');
    const hiddenInput = document.getElementById('owner_id_hidden');
    
    const selectedOwnerCard = document.getElementById('selected-owner-card');
    const displayOwnerName = document.getElementById('display-owner-name');
    const displayOwnerDni = document.getElementById('display-owner-dni');
    const displayOwnerContact = document.getElementById('display-owner-contact');
    const ownerInfoDetails = document.getElementById('owner-info-details');

    // Show list on focus
    searchInput.addEventListener('focus', function() {
        showResults(this.value);
    });

    searchInput.addEventListener('input', function() {
        showResults(this.value);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
    });

    function showResults(query) {
        query = query.toLowerCase();
        resultsContainer.innerHTML = '';
        
        const filtered = query.length < 1 
            ? owners.slice(0, 10) // Show first 10 if empty
            : owners.filter(o => o.name.toLowerCase().includes(query) || o.dni_cuit.includes(query));

        if (filtered.length > 0) {
            filtered.forEach(o => {
                const div = document.createElement('div');
                div.className = 'search-item';
                div.innerHTML = `<strong>${o.name}</strong> <br> <small style="color: var(--text-light)">${o.dni_cuit}</small>`;
                div.onclick = () => selectOwner(o);
                resultsContainer.appendChild(div);
            });
            resultsContainer.style.display = 'block';
        } else {
            resultsContainer.style.display = 'none';
        }
    }

    function selectOwner(owner) {
        hiddenInput.value = owner.id;
        searchInput.value = owner.name;
        
        // Update display card
        displayOwnerName.innerText = owner.name;
        displayOwnerDni.innerText = owner.dni_cuit;
        displayOwnerContact.innerText = `${owner.email || '-'} / ${owner.phone || '-'}`;
        
        selectedOwnerCard.style.display = 'block';
        resultsContainer.style.display = 'none';
        
        // Hide info details by default when selecting new
        ownerInfoDetails.style.display = 'none';
        document.getElementById('btn-toggle-info').innerText = 'Ver Info Propietario';
    }

    function toggleOwnerInfo() {
        const btn = document.getElementById('btn-toggle-info');
        if (ownerInfoDetails.style.display === 'none') {
            ownerInfoDetails.style.display = 'block';
            btn.innerText = 'Ocultar Info';
        } else {
            ownerInfoDetails.style.display = 'none';
            btn.innerText = 'Ver Info Propietario';
        }
    }

    // Modal Logic
    function openOwnerModal() {
        document.getElementById('owner-modal').style.display = 'flex';
    }

    function closeOwnerModal() {
        document.getElementById('owner-modal').style.display = 'none';
    }

    let modalAccountCount = 1;
    function addModalBankAccount() {
        const container = document.getElementById('modal-bank-accounts');
        const div = document.createElement('div');
        div.style.background = 'var(--bg-body)';
        div.style.padding = '1rem';
        div.style.borderRadius = '8px';
        div.style.marginBottom = '1rem';
        div.innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.5rem;">
                <input type="text" name="bank_accounts[${modalAccountCount}][cbu_alias]" placeholder="CBU / Alias" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                <input type="text" name="bank_accounts[${modalAccountCount}][holder_name]" placeholder="Titular" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 40px; gap: 1rem;">
                <input type="text" name="bank_accounts[${modalAccountCount}][holder_cuit]" placeholder="CUIT Titular" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                <button type="button" onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #C53030; cursor: pointer;">&times;</button>
            </div>
        `;
        container.appendChild(div);
        modalAccountCount++;
    }

    // Form AJAX Submission
    document.getElementById('new-owner-form').onsubmit = async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch("{{ route('owners.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const result = await response.json();
                // Add to our local owners list
                owners.push(result.owner);
                // Select it
                selectOwner(result.owner);
                closeOwnerModal();
                this.reset();
            } else {
                alert('Error al guardar el propietario. Revisa los datos.');
            }
        } catch (error) {
            console.error(error);
            alert('Ocurrió un error en la conexión.');
        }
    };

    // Dynamic City Selection
    const provinceSelect = document.getElementById('province-select');
    const citySelect = document.getElementById('city-select');

    async function updateCities(provinceId, selectedCityId = null) {
        citySelect.innerHTML = '<option value="">-- Cargando... --</option>';
        citySelect.disabled = true;

        if (!provinceId) {
            citySelect.innerHTML = '<option value="">-- Seleccione Provincia primero --</option>';
            return;
        }

        try {
            const response = await fetch(`/api/provinces/${provinceId}/cities`);
            const cities = await response.json();

            citySelect.innerHTML = '<option value="">-- Seleccionar Localidad --</option>';
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.name;
                if (selectedCityId && city.id == selectedCityId) {
                    option.selected = true;
                }
                citySelect.appendChild(option);
            });

            citySelect.disabled = false;
        } catch (error) {
            console.error('Error fetching cities:', error);
            citySelect.innerHTML = '<option value="">-- Error al cargar --</option>';
        }
    }

    provinceSelect.addEventListener('change', function() {
        updateCities(this.value);
    });

    // Populate on load if province is already selected (e.g., old input after validation error)
    window.addEventListener('load', () => {
        const selectedProvince = provinceSelect.value;
        const oldCityId = "{{ old('city_id') }}";
        if (selectedProvince) {
            updateCities(selectedProvince, oldCityId);
        }
    });
</script>
@endsection
