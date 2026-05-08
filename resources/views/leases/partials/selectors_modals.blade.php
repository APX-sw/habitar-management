<!-- Property Selection Modal -->
<div id="property-modal" class="modal-custom" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="display: flex; gap: 1rem; width: 95%; max-width: 1200px; height: 90vh;">
        <!-- Selector Panel -->
        <div class="card" style="flex: 1; overflow-y: auto; background: white; border-radius: 12px; padding: 2rem; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="color: var(--primary-color);">Seleccionar Propiedad</h2>
                <button type="button" onclick="closeModal('property-modal')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <input type="text" id="search-property" onkeyup="handlePropertySearch()" placeholder="Buscar por dirección o tipo..." style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color); margin-bottom: 1.5rem;">
            
            <div style="overflow-x: auto; min-height: 400px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid var(--secondary-color);">
                            <th style="padding: 1rem;">Dirección</th>
                            <th style="padding: 1rem;">Tipo</th>
                            <th style="padding: 1rem; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="properties-table-body">
                        <!-- Rendered by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination Properties -->
            <div id="property-pagination" style="display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 2rem;">
                <button type="button" id="prop-prev" onclick="changePage('property', -1)" class="btn" style="background: var(--secondary-color); font-size: 0.8rem;">Anterior</button>
                <div id="prop-page-info" style="font-size: 0.9rem; font-weight: 600; min-width: 150px; text-align: center;">Página 1</div>
                <button type="button" id="prop-next" onclick="changePage('property', 1)" class="btn" style="background: var(--secondary-color); font-size: 0.8rem;">Siguiente</button>
            </div>
        </div>
        <!-- Details Panel -->
        <div id="property-details-panel" class="card" style="width: 400px; display: none; overflow-y: auto; background: white; border-radius: 12px; padding: 2rem; border-left: 4px solid var(--accent-color); animation: slideInRight 0.3s ease-out;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--primary-color); margin: 0;">Detalles de la Propiedad</h3>
                <button type="button" onclick="document.getElementById('property-details-panel').style.display='none'" style="background: none; border: none; cursor: pointer;">&times;</button>
            </div>
            <div id="property-details-content"></div>
        </div>
    </div>
</div>

<!-- Tenant Selection Modal -->
<div id="tenant-modal" class="modal-custom" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="display: flex; gap: 1rem; width: 95%; max-width: 1200px; height: 90vh;">
        <!-- Selector Panel -->
        <div class="card" style="flex: 1; overflow-y: auto; background: white; border-radius: 12px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="color: var(--primary-color);">Seleccionar Inquilino</h2>
                <button type="button" onclick="closeModal('tenant-modal')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <input type="text" id="search-tenant" onkeyup="handleTenantSearch()" placeholder="Buscar por nombre o documento..." style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color); margin-bottom: 1.5rem;">
            <div style="overflow-x: auto; min-height: 400px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid var(--secondary-color);">
                            <th style="padding: 1rem;">Nombre</th>
                            <th style="padding: 1rem;">Documento</th>
                            <th style="padding: 1rem; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tenants-table-body">
                        <!-- Rendered by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination Tenants -->
            <div id="tenant-pagination" style="display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 2rem;">
                <button type="button" id="tenant-prev" onclick="changePage('tenant', -1)" class="btn" style="background: var(--secondary-color); font-size: 0.8rem;">Anterior</button>
                <div id="tenant-page-info" style="font-size: 0.9rem; font-weight: 600; min-width: 150px; text-align: center;">Página 1</div>
                <button type="button" id="tenant-next" onclick="changePage('tenant', 1)" class="btn" style="background: var(--secondary-color); font-size: 0.8rem;">Siguiente</button>
            </div>
        </div>
        <!-- Details Panel -->
        <div id="tenant-details-panel" class="card" style="width: 400px; display: none; overflow-y: auto; background: white; border-radius: 12px; padding: 2rem; border-left: 4px solid var(--accent-color); animation: slideInRight 0.3s ease-out;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--primary-color); margin: 0;">Perfil del Inquilino</h3>
                <button type="button" onclick="document.getElementById('tenant-details-panel').style.display='none'" style="background: none; border: none; cursor: pointer;">&times;</button>
            </div>
            <div id="tenant-details-content"></div>
        </div>
    </div>
</div>

<style>
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>

<script>
    // Data from Laravel
    const allProperties = @json($properties);
    const allTenants = @json($tenants);

    let filteredProperties = [...allProperties];
    let filteredTenants = [...allTenants];

    let propertyPage = 1;
    let tenantPage = 1;
    const itemsPerPage = 10;

    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Fix background scroll
        if(id === 'property-modal') renderTable('property');
        if(id === 'tenant-modal') renderTable('tenant');
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        document.body.style.overflow = 'auto'; // Re-enable background scroll
        if(id === 'property-modal') document.getElementById('property-details-panel').style.display = 'none';
        if(id === 'tenant-modal') document.getElementById('tenant-details-panel').style.display = 'none';
    }

    function handlePropertySearch() {
        const query = document.getElementById('search-property').value.toUpperCase();
        filteredProperties = allProperties.filter(p => 
            p.location.toUpperCase().includes(query) || 
            p.type.name.toUpperCase().includes(query) ||
            p.city.name.toUpperCase().includes(query)
        );
        propertyPage = 1;
        renderTable('property');
    }

    function handleTenantSearch() {
        const query = document.getElementById('search-tenant').value.toUpperCase();
        filteredTenants = allTenants.filter(t => 
            t.name.toUpperCase().includes(query) || 
            t.dni_cuit.toUpperCase().includes(query)
        );
        tenantPage = 1;
        renderTable('tenant');
    }

    function changePage(type, delta) {
        const list = type === 'property' ? filteredProperties : filteredTenants;
        const totalPages = Math.ceil(list.length / itemsPerPage) || 1;
        
        if(type === 'property') {
            const newPage = propertyPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                propertyPage = newPage;
                renderTable('property');
            }
        } else {
            const newPage = tenantPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                tenantPage = newPage;
                renderTable('tenant');
            }
        }
    }

    function renderTable(type) {
        const list = type === 'property' ? filteredProperties : filteredTenants;
        const page = type === 'property' ? propertyPage : tenantPage;
        const tbody = document.getElementById(`${type === 'property' ? 'properties' : 'tenants'}-table-body`);
        
        const start = (page - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, list.length);
        const items = list.slice(start, end);

        tbody.innerHTML = '';
        
        if (list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" style="text-align:center; padding:2rem; color:var(--text-light);">No se encontraron resultados</td></tr>`;
            document.getElementById(`${type}-pagination`).style.display = 'none';
            return;
        } else {
            document.getElementById(`${type}-pagination`).style.display = 'flex';
        }

        items.forEach(item => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid var(--secondary-color)';
            if(type === 'property') {
                row.innerHTML = `
                    <td style="padding: 1rem;">
                        <div style="font-weight: 600;">${item.location}</div>
                        <div style="font-size: 0.85rem; color: var(--text-light);">${item.city.name}</div>
                    </td>
                    <td style="padding: 1rem;">
                        <span class="badge" style="background: #edf2f7; color: var(--primary-color);">${item.type.name}</span>
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                            <button type="button" onclick="loadPropertyDetails(${item.id})" style="background: none; border: none; color: var(--accent-color); font-size: 0.85rem; cursor: pointer;">Ver Ficha</button>
                            <button type="button" onclick="selectProperty(${item.id}, '${item.location.replace(/'/g, "\\'")}', '${item.type.name}')" class="btn" style="background: var(--primary-color); color: white; padding: 0.4rem 1rem; font-size: 0.85rem;">Seleccionar</button>
                        </div>
                    </td>
                `;
            } else {
                row.innerHTML = `
                    <td style="padding: 1rem;"><div style="font-weight: 600;">${item.name}</div></td>
                    <td style="padding: 1rem;"><div style="font-size: 0.85rem; color: var(--text-light);">${item.dni_cuit}</div></td>
                    <td style="padding: 1rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                            <button type="button" onclick="loadTenantDetails(${item.id})" style="background: none; border: none; color: var(--accent-color); font-size: 0.85rem; cursor: pointer;">Ver Perfil</button>
                            <button type="button" onclick="selectTenant(${item.id}, '${item.name.replace(/'/g, "\\'")}', '${item.dni_cuit}')" class="btn" style="background: var(--primary-color); color: white; padding: 0.4rem 1rem; font-size: 0.85rem;">Seleccionar</button>
                        </div>
                    </td>
                `;
            }
            tbody.appendChild(row);
        });

        // Update pagination info
        const totalPages = Math.ceil(list.length / itemsPerPage) || 1;
        const currentCount = end - start;
        
        document.getElementById(`${type}-page-info`).innerHTML = `
            <div style="text-align: center;">
                <div style="font-size: 0.9rem; font-weight: 700; color: var(--primary-color);">Página ${page} de ${totalPages}</div>
                <div style="font-size: 0.75rem; color: var(--text-light);">${end} de ${list.length} ${type === 'property' ? 'propiedades' : 'inquilinos'}</div>
            </div>
        `;

        const prevBtn = document.getElementById(`${type === 'property' ? 'prop' : 'tenant'}-prev`);
        const nextBtn = document.getElementById(`${type === 'property' ? 'prop' : 'tenant'}-next`);

        prevBtn.disabled = page === 1;
        prevBtn.style.opacity = page === 1 ? '0.5' : '1';
        prevBtn.style.cursor = page === 1 ? 'not-allowed' : 'pointer';

        nextBtn.disabled = page === totalPages;
        nextBtn.style.opacity = page === totalPages ? '0.5' : '1';
        nextBtn.style.cursor = page === totalPages ? 'not-allowed' : 'pointer';
    }

    async function loadPropertyDetails(id) {
        const panel = document.getElementById('property-details-panel');
        const content = document.getElementById('property-details-content');
        content.innerHTML = '<p>Cargando detalles...</p>';
        panel.style.display = 'block';

        try {
            const response = await fetch(`/api/properties/${id}`);
            const p = await response.json();
            content.innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="font-weight: 700; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Ubicación</label>
                        <p style="margin: 0.2rem 0; font-weight: 600;">${p.location}</p>
                        <p style="margin: 0; font-size: 0.85rem;">${p.city.name}, ${p.province.name}</p>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="font-weight: 700; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Ambientes</label>
                            <p style="margin: 0.2rem 0;">${p.rooms} Ambientes</p>
                        </div>
                        <div>
                            <label style="font-weight: 700; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Baños</label>
                            <p style="margin: 0.2rem 0;">${p.bathrooms} Baños</p>
                        </div>
                    </div>
                    <div>
                        <label style="font-weight: 700; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Propietario</label>
                        <p style="margin: 0.2rem 0;">${p.owner.name}</p>
                    </div>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px;">
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.85rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                            <li>${p.has_garage ? '✅ Cochera' : '❌ Cochera'}</li>
                            <li>${p.has_patio ? '✅ Patio' : '❌ Patio'}</li>
                            <li>${p.has_balcony ? '✅ Balcón' : '❌ Balcón'}</li>
                            <li>${p.pets_allowed ? '✅ Mascotas' : '❌ Mascotas'}</li>
                        </ul>
                    </div>
                </div>
            `;
        } catch (error) { content.innerHTML = '<p style="color: red;">Error</p>'; }
    }

    async function loadTenantDetails(id) {
        const panel = document.getElementById('tenant-details-panel');
        const content = document.getElementById('tenant-details-content');
        content.innerHTML = '<p>Cargando detalles...</p>';
        panel.style.display = 'block';

        try {
            const response = await fetch(`/api/tenants/${id}`);
            const t = await response.json();
            content.innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div><label style="font-weight: 700; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Nombre</label><p style="margin: 0.2rem 0; font-weight: 600;">${t.name}</p></div>
                    <div><label style="font-weight: 700; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">DNI/CUIT</label><p style="margin: 0.2rem 0;">${t.dni_cuit}</p></div>
                    <div><label style="font-weight: 700; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase;">Contacto</label><p style="margin: 0.2rem 0;">📞 ${t.phone || 'N/A'}</p><p style="margin: 0.2rem 0;">📧 ${t.email || 'N/A'}</p></div>
                </div>
            `;
        } catch (error) { content.innerHTML = '<p style="color: red;">Error</p>'; }
    }

    function selectProperty(id, location, type) {
        document.getElementById('selected-property-id').value = id;
        document.getElementById('property-display').innerHTML = `
            <div style="text-align: left; display: flex; justify-content: space-between; align-items: center;">
                <div><h4 style="color: var(--primary-color); margin: 0;">${location}</h4><span class="badge" style="background: #edf2f7; color: var(--primary-color); font-size: 0.75rem;">${type}</span></div>
                <button type="button" onclick="openModal('property-modal')" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.75rem;">Cambiar</button>
            </div>
        `;
        document.getElementById('property-display').style.borderStyle = 'solid';
        document.getElementById('property-display').style.borderColor = 'var(--accent-color)';
        closeModal('property-modal');
    }

    function selectTenant(id, name, dni) {
        document.getElementById('selected-tenant-id').value = id;
        document.getElementById('tenant-display').innerHTML = `
            <div style="text-align: left; display: flex; justify-content: space-between; align-items: center;">
                <div><h4 style="color: var(--primary-color); margin: 0;">${name}</h4><span style="font-size: 0.85rem; color: var(--text-light);">${dni}</span></div>
                <button type="button" onclick="openModal('tenant-modal')" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.75rem;">Cambiar</button>
            </div>
        `;
        document.getElementById('tenant-display').style.borderStyle = 'solid';
        document.getElementById('tenant-display').style.borderColor = 'var(--accent-color)';
        closeModal('tenant-modal');
    }
</script>
