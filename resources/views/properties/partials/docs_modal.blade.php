<!-- Modal Documentos de Propiedad (Premium) -->
<div id="property-docs-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 3000; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 850px; max-width: 95%; background: white; border-radius: 15px; padding: 0; overflow: hidden; animation: modalAppear 0.3s ease-out;">
        <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin: 0; color: var(--primary-color);">Documentos de la Propiedad</h3>
                <p id="modal-prop-location" style="margin: 0.2rem 0 0; font-size: 0.8rem; color: var(--text-light);"></p>
            </div>
            <button onclick="closePropertyDocsModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #a0aec0;">&times;</button>
        </div>
        
        <div style="padding: 1.5rem 2rem;">
            <!-- Toolbar: Search and Sort -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: center;">
                <div style="position: relative; flex: 1;">
                    <svg style="position: absolute; left: 0.8rem; top: 50%; transform: translateY(-50%); color: #cbd5e0;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="prop-docs-search" oninput="renderPropertyDocuments()" placeholder="Buscar documento por nombre..." style="width: 100%; padding: 0.7rem 1rem 0.7rem 2.5rem; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.9rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--accent-color)'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button onclick="setPropertySort('desc')" id="prop-sort-desc" class="btn" style="padding: 0.6rem 1rem; font-size: 0.8rem; background: #f1f5f9; color: #475569; display: flex; align-items: center; gap: 0.4rem; font-weight: 600; border: 1px solid #e2e8f0;">
                        <span>Recientes</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <button onclick="setPropertySort('asc')" id="prop-sort-asc" class="btn" style="padding: 0.6rem 1rem; font-size: 0.8rem; background: #f1f5f9; color: #475569; display: flex; align-items: center; gap: 0.4rem; font-weight: 600; border: 1px solid #e2e8f0;">
                        <span>Antiguos</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </button>
                </div>
            </div>

            <!-- Upload area -->
            <div id="prop-drop-zone" onclick="document.getElementById('prop-file-input').click()" style="border: 2px dashed #cbd5e0; border-radius: 10px; padding: 1.2rem; text-align: center; margin-bottom: 1.5rem; background: #fdfdfd; cursor: pointer; transition: all 0.2s;">
                <input type="file" id="prop-file-input" style="display: none;" onchange="handlePropFileSelect(this)">
                <div id="prop-upload-status" style="display: none; margin-bottom: 0.5rem; font-weight: 700; color: var(--accent-color); font-size: 0.8rem;">Subiendo...</div>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2" style="margin-bottom: 0.3rem;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <p style="margin: 0; font-size: 0.8rem; color: #718096;">Sube escrituras, planos, reglamento de copropiedad o imágenes.</p>
            </div>

            <div id="prop-docs-list" style="max-height: 400px; overflow-y: auto; border: 1px solid #f1f5f9; border-radius: 12px;">
                <p style="text-align: center; padding: 3rem; color: var(--text-light); font-size: 0.9rem;">Cargando documentos...</p>
            </div>
        </div>

        <div style="padding: 1.2rem; background: #f8fafc; border-top: 1px solid #edf2f7; text-align: right;">
            <button onclick="closePropertyDocsModal()" class="btn" style="background: var(--primary-color); color: white; padding: 0.7rem 2rem;">Cerrar</button>
        </div>
    </div>
</div>

<style>
    @keyframes modalAppear {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .doc-item:hover { background: #f8fafc; }
    .btn-active-sort { background: #ebf4ff !important; color: #2b6cb0 !important; border-color: #bee3f8 !important; }
</style>

<script>
    let currentPropertyId = null;
    let allPropertyDocuments = [];
    let currentPropertySort = 'desc';

    function openPropertyDocsModal(propertyId, location) {
        currentPropertyId = propertyId;
        const locationElement = document.getElementById('modal-prop-location');
        if (locationElement) {
            locationElement.innerText = location;
        }
        document.getElementById('property-docs-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.getElementById('prop-docs-search').value = '';
        updatePropertySortButtons();
        fetchPropertyDocuments();
    }

    function closePropertyDocsModal() {
        document.getElementById('property-docs-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Si estamos en la ficha detallada (show.blade.php), recargamos para actualizar el listado estático
        if (window.location.pathname.includes('/properties/')) {
            window.location.reload();
        }
    }

    function setPropertySort(dir) {
        currentPropertySort = dir;
        updatePropertySortButtons();
        renderPropertyDocuments();
    }

    function updatePropertySortButtons() {
        const descBtn = document.getElementById('prop-sort-desc');
        const ascBtn = document.getElementById('prop-sort-asc');
        if (descBtn && ascBtn) {
            descBtn.classList.toggle('btn-active-sort', currentPropertySort === 'desc');
            ascBtn.classList.toggle('btn-active-sort', currentPropertySort === 'asc');
        }
    }

    async function fetchPropertyDocuments() {
        try {
            const response = await fetch(`/properties/${currentPropertyId}/documents`);
            allPropertyDocuments = await response.json();
            renderPropertyDocuments();
        } catch (error) {
            document.getElementById('prop-docs-list').innerHTML = '<p style="text-align: center; padding: 2rem; color: #c53030;">Error al cargar documentos.</p>';
        }
    }

    function getPropertyFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>';
        if (ext === 'pdf') return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
        if (['doc', 'docx'].includes(ext)) return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>';
        return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>';
    }

    function renderPropertyDocuments() {
        const query = document.getElementById('prop-docs-search').value.toLowerCase();
        const list = document.getElementById('prop-docs-list');
        
        let filtered = allPropertyDocuments.filter(d => d.filename.toLowerCase().includes(query));
        
        filtered.sort((a, b) => {
            const dateA = new Date(a.created_at);
            const dateB = new Date(b.created_at);
            return currentPropertySort === 'desc' ? dateB - dateA : dateA - dateB;
        });

        if (filtered.length === 0) {
            list.innerHTML = `<div style="padding: 3rem; text-align: center; color: var(--text-light);">
                ${allPropertyDocuments.length === 0 ? 'No hay documentos aún.' : 'No se encontraron documentos.'}
            </div>`;
            return;
        }

        list.innerHTML = filtered.map(doc => {
            const ext = doc.filename.split('.').pop().toUpperCase();
            return `
                <div class="doc-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 1.2rem; flex: 1;">
                        <div style="background: #e6fffa; color: #2c7a7b; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            ${getPropertyFileIcon(doc.filename)}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--primary-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 1rem;" title="${doc.filename}">${doc.filename}</div>
                            <div style="display: flex; align-items: center; gap: 0.8rem; margin-top: 0.2rem;">
                                <span style="background: #edf2f7; padding: 0.1rem 0.5rem; border-radius: 4px; font-size: 0.65rem; font-weight: 800; color: #4a5568;">${ext}</span>
                                <span style="font-size: 0.75rem; color: #a0aec0;">${(doc.size / 1024).toFixed(1)} KB • ${new Date(doc.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.8rem; margin-left: 1.5rem;">
                        <a href="/storage/${doc.path}" target="_blank" class="btn" style="padding: 0.5rem 1rem; background: #e6fffa; color: #2c7a7b; border: 1px solid #b2f5ea; display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 700;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            Ver
                        </a>
                        <button onclick="deletePropertyDoc(${doc.id})" class="btn" style="padding: 0.5rem; background: #fff5f5; color: #c53030; border: 1px solid #feb2b2;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    async function handlePropFileSelect(input) {
        if (!input.files || !input.files[0]) return;
        uploadPropertyFile(input.files[0]);
    }

    async function uploadPropertyFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('property_id', currentPropertyId);
        formData.append('_token', '{{ csrf_token() }}');

        document.getElementById('prop-upload-status').style.display = 'block';
        
        try {
            const response = await fetch('/property-documents', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if (result.success) {
                fetchPropertyDocuments();
            } else {
                alert('Error al subir archivo: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            alert('Error en la conexión');
        } finally {
            document.getElementById('prop-upload-status').style.display = 'none';
            document.getElementById('prop-file-input').value = '';
        }
    }

    async function deletePropertyDoc(id) {
        if (!await confirmDialog('¿Eliminar este documento?')) return;
        
        try {
            const response = await fetch(`/property-documents/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if (result.success) {
                fetchPropertyDocuments();
            }
        } catch (error) {
            alert('Error al eliminar');
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        const dropZone = document.getElementById('prop-drop-zone');
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = 'var(--accent-color)';
                    dropZone.style.background = '#f0f9ff';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = '#cbd5e0';
                    dropZone.style.background = '#fdfdfd';
                    if (eventName === 'drop') {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        if (files.length > 0) {
                            uploadPropertyFile(files[0]);
                        }
                    }
                }, false);
            });
        }
    });
</script>
