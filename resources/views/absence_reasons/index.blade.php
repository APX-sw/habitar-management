@extends('layouts.app')

@section('title', '| Motivos de Ausencia')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700;">Motivos de Ausencia</h1>
            <p style="color: var(--text-light); font-size: 0.95rem;">Gestioná los motivos que los empleados pueden seleccionar al reportar una inasistencia.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('employees.index') }}" class="btn" style="background: var(--secondary-color); color: var(--text-main);">Volver a Legajos</a>
            <button onclick="openCreateModal()" class="btn btn-primary">Nuevo Motivo</button>
        </div>
    </div>

    <!-- Main List Card -->
    <div class="card" style="padding: 0; overflow: hidden; border-radius: var(--border-radius); box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Nombre del Motivo</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Descripción</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Estado</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reasons as $reason)
                        <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1.2rem; font-weight: 600; color: var(--primary-color);">
                                {{ $reason->name }}
                            </td>
                            <td style="padding: 1.2rem; color: var(--text-main);">
                                {{ $reason->description ?? 'Sin descripción' }}
                            </td>
                            <td style="padding: 1.2rem;">
                                @if($reason->is_active)
                                    <span class="badge" style="background: #e6fffa; color: #319795;">Activo</span>
                                @else
                                    <span class="badge" style="background: #eedfdf; color: #e53e3e;">Inactivo</span>
                                @endif
                            </td>
                            <td style="padding: 1.2rem; text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button onclick="openEditModal({{ json_encode($reason) }})" class="btn" style="background: #edf2f7; color: var(--text-main); padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px;">
                                        Editar
                                    </button>
                                    <form action="{{ route('absence-reasons.destroy', $reason) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este motivo?')" style="margin: 0; display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn" style="background: #fff5f5; color: #e53e3e; padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px; border: none; cursor: pointer;">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 3rem; text-align: center; color: var(--text-light);">
                                No hay motivos de ausencia configurados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reasons->hasPages())
            <div style="padding: 1rem; border-top: 1px solid #edf2f7;">
                {{ $reasons->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Premium Interactive Modal for Creating/Editing -->
<div id="reason-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 500px; max-width: 95%; background: white; border-radius: 12px; padding: 2rem;">
        <h3 id="modal-title" style="margin-bottom: 1.5rem; color: var(--primary-color);">Agregar Motivo</h3>
        
        <form id="modal-form" action="{{ route('absence-reasons.store') }}" method="POST">
            @csrf
            <div id="method-field"></div>
            
            <div style="margin-bottom: 1.2rem;">
                <label>Nombre del Motivo *</label>
                <input type="text" id="reason-name" name="name" required placeholder="Ej: Enfermedad, Mudanza">
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label>Descripción / Observación</label>
                <textarea id="reason-description" name="description" rows="3" placeholder="Opcional. Explicación breve de cuándo aplica."></textarea>
            </div>

            <div id="status-field" style="margin-bottom: 1.5rem; display: none;">
                <label>Estado</label>
                <select id="reason-is-active" name="is_active">
                    <option value="1">Activo (Visible para Empleados)</option>
                    <option value="0">Inactivo (Oculto)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" onclick="closeModal()" class="btn" style="background: var(--secondary-color); color: var(--text-main);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modal-title').innerText = 'Agregar Motivo';
        document.getElementById('modal-form').action = "{{ route('absence-reasons.store') }}";
        document.getElementById('method-field').innerHTML = '';
        document.getElementById('reason-name').value = '';
        document.getElementById('reason-description').value = '';
        document.getElementById('status-field').style.display = 'none';
        document.getElementById('reason-modal').style.display = 'flex';
    }

    function openEditModal(reason) {
        document.getElementById('modal-title').innerText = 'Editar Motivo';
        document.getElementById('modal-form').action = `/absence-reasons/${reason.id}`;
        document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('reason-name').value = reason.name;
        document.getElementById('reason-description').value = reason.description || '';
        document.getElementById('reason-is-active').value = reason.is_active ? "1" : "0";
        document.getElementById('status-field').style.display = 'block';
        document.getElementById('reason-modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('reason-modal').style.display = 'none';
    }

    // Close when clicking outside card
    window.onclick = function(event) {
        const modal = document.getElementById('reason-modal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection
