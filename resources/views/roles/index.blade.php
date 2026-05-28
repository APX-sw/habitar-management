@extends('layouts.app')

@section('title', '| Roles')

@section('content')
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; color: var(--primary-color); font-size: 1.5rem;">Gestión de Roles y Permisos</h2>
            <p style="margin: 0.2rem 0 0; color: var(--text-light); font-size: 0.9rem;">Configura qué funcionalidades del sistema puede usar cada tipo de usuario.</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Nuevo Rol
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #edf2f7;">
                    <th style="padding: 1rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Nombre del Rol</th>
                    <th style="padding: 1rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Usuarios Asignados</th>
                    <th style="padding: 1rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1rem; font-weight: 500;">
                            <span class="badge" style="font-size: 0.9rem; background: {{ $role->name === 'superadmin' ? '#ebf4ff' : '#edf2f7' }}; color: {{ $role->name === 'superadmin' ? '#2b6cb0' : '#4a5568' }};">
                                {{ ucfirst($role->name) }}
                            </span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-light);">
                            <span style="font-weight: 600; color: var(--primary-color);">{{ $role->users_count }}</span> usuarios
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                @if($role->name !== 'superadmin')
                                    <a href="{{ route('roles.edit', $role) }}" class="btn" style="background: #edf2f7; color: #4a5568; padding: 0.4rem 0.8rem; font-size: 0.85rem;">Editar Permisos</a>
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este rol?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn" style="background: #fff5f5; color: #e53e3e; padding: 0.4rem 0.8rem; font-size: 0.85rem;" {{ $role->users_count > 0 ? 'disabled title="Quita los usuarios de este rol primero"' : '' }} {{ $role->users_count > 0 ? 'style="background: #fed7d7; color: #f56565; cursor: not-allowed;"' : '' }}>Eliminar</button>
                                    </form>
                                @else
                                    <span style="color: #a0aec0; font-size: 0.85rem; padding: 0.4rem 0.8rem; font-style: italic;">Rol inmutable</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay roles registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $roles->links() }}
    </div>
</div>
@endsection
