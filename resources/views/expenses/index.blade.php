@extends('layouts.app')

@section('title', '| Gastos')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Gastos</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Registro de egresos generales o de propiedades.</p>
    </div>
    @if(isset($isCashRegisterOpen) && !$isCashRegisterOpen)
        <button class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700; opacity: 0.6; cursor: not-allowed;" title="Debes abrir una sesión de caja primero" disabled>➕ Registrar Gasto</button>
    @else
        <a href="{{ route('expenses.create') }}" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700;">➕ Registrar Gasto</a>
    @endif
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
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Añadir Comprobante(s)</label>
                <input type="file" name="attachments[]" id="edit-attachment-input" multiple style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; background: white;">
                <div id="edit-attachment-feedback" style="margin-top: 0.5rem; font-size: 0.85rem; font-weight: 700; color: #2f855a; display: none;"></div>
                <p style="font-size: 0.7rem; color: #a0aec0; margin-top: 0.5rem;">Puedes adjuntar múltiples comprobantes. Se sumarán a los ya existentes.</p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Guardar Cambios</button>
        </form>
    </div>
</div>

<!-- Modal de Visualización de Documentos (Premium) -->
@include('expenses.partials.docs_modal')


<!-- Modal de Confirmación de Eliminación Personalizado -->
<div id="deleteConfirmModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 480px; padding: 2.2rem; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border-top: 5px solid #E53E3E; background: white;">
        <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            <!-- Icono de Advertencia -->
            <div style="background: #FFF5F5; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #E53E3E; margin-bottom: 1.5rem;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
            
            <h3 style="margin: 0; color: #C53030; font-size: 1.4rem; font-weight: 800;">¿Confirmas la eliminación?</h3>
            <p style="margin: 0.5rem 0 1.5rem; font-size: 0.85rem; color: var(--text-light); font-weight: 600; line-height: 1.5;">Esta acción es irreversible. Se eliminarán todos los comprobantes asociados y el saldo se reintegrará automáticamente a la cuenta origen.</p>
            
            <!-- Detalles del Gasto a Borrar -->
            <div style="width: 100%; background: #F7FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.2rem; margin-bottom: 2rem; text-align: left; font-size: 0.85rem; box-sizing: border-box;">
                <div style="display: grid; grid-template-columns: 80px 1fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span style="font-weight: 700; color: #718096;">Gasto:</span>
                    <span id="delModalDescription" style="font-weight: 700; color: #2D3748; word-break: break-word;"></span>
                </div>
                <div style="display: grid; grid-template-columns: 80px 1fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span style="font-weight: 700; color: #718096;">Fecha:</span>
                    <span id="delModalDate" style="color: #4A5568; font-weight: 600;"></span>
                </div>
                <div style="display: grid; grid-template-columns: 80px 1fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span style="font-weight: 700; color: #718096;">Cuenta:</span>
                    <span id="delModalAccount" style="color: #4A5568; font-weight: 600;"></span>
                </div>
                <div style="display: grid; grid-template-columns: 80px 1fr; gap: 0.5rem;">
                    <span style="font-weight: 700; color: #718096;">Monto:</span>
                    <span id="delModalAmount" style="font-weight: 800; color: #E53E3E; font-size: 1rem;"></span>
                </div>
            </div>
            
            <!-- Botones -->
            <div style="display: flex; gap: 1rem; width: 100%;">
                <button type="button" onclick="closeDeleteModal()" class="btn" style="flex: 1; padding: 0.8rem; background: #edf2f7; color: #4a5568; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; font-size: 0.9rem;">Cancelar</button>
                <form id="deleteModalForm" action="" method="POST" style="flex: 1; margin: 0; display: inline;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn" style="width: 100%; padding: 0.8rem; background: #E53E3E; color: white; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem; font-size: 0.9rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Eliminar Gasto
                    </button>
                </form>
            </div>
        </div>
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
        document.getElementById('edit-attachment-input').value = '';
        document.getElementById('edit-attachment-feedback').style.display = 'none';
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Custom Delete Confirmation Modal Logic
    function triggerDeleteExpense(id, description, date, amount, account, deleteUrl) {
        document.getElementById('delModalDescription').innerText = description;
        document.getElementById('delModalDate').innerText = date;
        document.getElementById('delModalAmount').innerText = amount;
        document.getElementById('delModalAccount').innerText = account;
        document.getElementById('deleteModalForm').action = deleteUrl;
        document.getElementById('deleteConfirmModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }

    // Docs Modal
    function openDocsModal(docs, details, expenseId) {
        openExpenseDocsModal(expenseId, details);
    }

    function closeDocsModal() {
        closeExpenseDocsModal();
    }

    // Attach edit input listener
    document.getElementById('edit-attachment-input').addEventListener('change', function(e) {
        const files = e.target.files;
        const feedback = document.getElementById('edit-attachment-feedback');
        if (files.length > 0) {
            feedback.innerText = `✓ ${files.length} archivo(s) seleccionado(s)`;
            feedback.style.display = 'block';
        } else {
            feedback.style.display = 'none';
        }
    });

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
