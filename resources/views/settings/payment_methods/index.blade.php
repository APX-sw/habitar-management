@extends('layouts.app')

@section('title', '| Métodos de Pago')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('settings.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver a Configuración</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Métodos de Pago</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Gestiona las formas en las que los inquilinos pueden realizar pagos.</p>
    </div>
    
    <button onclick="document.getElementById('addMethodModal').style.display='flex'" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700;">➕ Nuevo Método</button>
</div>

<div class="card" style="padding: 0; overflow: hidden; border-radius: 15px;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid var(--secondary-color);">
                <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Nombre del Método</th>
                <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($methods as $method)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='white'">
                    <td style="padding: 1.2rem;">
                        <div style="font-weight: 700; color: var(--primary-color); font-size: 1.1rem;">{{ $method->name }}</div>
                    </td>
                    <td style="padding: 1.2rem;">
                        <span class="badge" style="background: {{ $method->is_active ? '#C6F6D5' : '#FED7D7' }}; color: {{ $method->is_active ? '#22543D' : '#C53030' }};">
                            {{ $method->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td style="padding: 1.2rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button onclick="editMethod({{ $method->id }}, '{{ $method->name }}', {{ $method->is_active }})" class="btn" style="background: var(--secondary-color); color: var(--primary-color); padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 700;">EDITAR</button>
                            
                            <form action="{{ route('payment-methods.destroy', $method) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este método?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background: #FFF5F5; color: #C53030; padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 700; border: 1px solid #FEB2B2;">ELIMINAR</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="padding: 4rem; text-align: center; color: var(--text-light);">No hay métodos de pago configurados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal para Nuevo Método -->
<div id="addMethodModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 450px; padding: 2.5rem; position: relative;">
        <button onclick="document.getElementById('addMethodModal').style.display='none'" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; cursor: pointer; color: var(--text-light);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Nuevo Método de Pago</h3>
        <form action="{{ route('payment-methods.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Nombre</label>
                <input type="text" name="name" required placeholder="Ej: Mercado Pago, Transferencia Banco X..." style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('addMethodModal').style.display='none'" class="btn" style="background: #f1f5f9; color: #475569;">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear Método</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Editar Método -->
<div id="editMethodModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 450px; padding: 2.5rem; position: relative;">
        <button onclick="document.getElementById('editMethodModal').style.display='none'" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; cursor: pointer; color: var(--text-light);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Editar Método</h3>
        <form id="editMethodForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Nombre</label>
                <input type="text" id="edit_name" name="name" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1">
                    <span style="font-size: 0.9rem; font-weight: 600; color: var(--primary-color);">Método Activo</span>
                </label>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('editMethodModal').style.display='none'" class="btn" style="background: #f1f5f9; color: #475569;">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function editMethod(id, name, isActive) {
        const form = document.getElementById('editMethodForm');
        form.action = `/payment-methods/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_is_active').checked = !!isActive;
        document.getElementById('editMethodModal').style.display = 'flex';
    }
</script>
@endsection
