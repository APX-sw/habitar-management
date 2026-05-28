@extends('layouts.app')

@section('title', '| Usuarios')

@section('content')
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; color: var(--primary-color); font-size: 1.5rem;">Gestión de Usuarios</h2>
            <p style="margin: 0.2rem 0 0; color: var(--text-light); font-size: 0.9rem;">Administra el acceso y los roles de las personas en el sistema.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Nuevo Usuario
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #edf2f7;">
                    <th style="padding: 1rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Nombre</th>
                    <th style="padding: 1rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Email</th>
                    <th style="padding: 1rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Roles</th>
                    <th style="padding: 1rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1rem; font-weight: 500;">{{ $user->name }}</td>
                        <td style="padding: 1rem; color: var(--text-light);">{{ $user->email }}</td>
                        <td style="padding: 1rem;">
                            @foreach($user->roles as $role)
                                <span class="badge" style="background: {{ $role->name === 'superadmin' ? '#ebf4ff' : '#e6fffa' }}; color: {{ $role->name === 'superadmin' ? '#3182ce' : '#319795' }}; border: 1px solid {{ $role->name === 'superadmin' ? '#bee3f8' : '#b2f5ea' }};">{{ ucfirst($role->name) }}</span>
                            @endforeach
                            @if($user->roles->isEmpty())
                                <span style="color: #a0aec0; font-size: 0.85rem;">Sin rol</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('users.edit', $user) }}" class="btn" style="background: #edf2f7; color: #4a5568; padding: 0.4rem 0.8rem; font-size: 0.85rem;">Editar</a>
                                @if(auth()->id() !== $user->id && $user->email !== 'superadmin@habitar.com.ar')
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn" style="background: #fff5f5; color: #e53e3e; padding: 0.4rem 0.8rem; font-size: 0.85rem;">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay usuarios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $users->links() }}
    </div>
</div>
@endsection
