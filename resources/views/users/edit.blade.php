@extends('layouts.app')

@section('title', '| Editar Usuario')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <h2 style="margin: 0; color: var(--primary-color); font-size: 1.5rem;">Editar Usuario</h2>
        <p style="margin: 0.2rem 0 0; color: var(--text-light); font-size: 0.9rem;">Actualiza la información y roles de {{ $user->name }}.</p>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div>
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
        </div>

        <div style="background: #fffaf0; padding: 1rem; border-radius: var(--border-radius); border-left: 4px solid #dd6b20; margin-bottom: 1.5rem;">
            <p style="margin: 0; color: #c05621; font-size: 0.9rem;"><strong>Nota:</strong> Solo completa los campos de contraseña si deseas cambiarla. Si los dejas en blanco, la contraseña actual se mantendrá intacta.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label for="password">Nueva Contraseña (Opcional)</label>
                <input type="password" id="password" name="password">
            </div>
            <div>
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation">
            </div>
        </div>

        <div style="margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: var(--border-radius); border: 1px solid #edf2f7;">
            <label style="font-size: 1.1rem; margin-bottom: 1rem !important;">Asignar Roles</label>
            
            @if($user->email === 'superadmin@habitar.com.ar')
                <div style="margin-bottom: 1rem; padding: 0.8rem; background: #ebf4ff; color: #2b6cb0; border-radius: 8px;">
                    Este es el usuario superadministrador principal. Su rol 'superadmin' es inmutable.
                </div>
            @endif

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                @foreach($roles as $role)
                    @php
                        $isSuperadminChecked = ($user->email === 'superadmin@habitar.com.ar' && $role->name === 'superadmin');
                    @endphp
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500 !important; cursor: pointer; background: white; padding: 0.8rem 1.2rem; border-radius: 8px; border: 1px solid #e2e8f0; margin: 0 !important; {{ $isSuperadminChecked ? 'opacity: 0.7;' : '' }}">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                            {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }} 
                            {{ $isSuperadminChecked ? 'disabled' : '' }}
                            style="width: auto !important; margin: 0 !important;">
                        {{ ucfirst($role->name) }}
                    </label>
                    
                    @if($isSuperadminChecked)
                        <!-- Hidden input para asegurar que el rol superadmin se envíe incluso si está deshabilitado visualmente -->
                        <input type="hidden" name="roles[]" value="superadmin">
                    @endif
                @endforeach
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('users.index') }}" class="btn" style="background: #edf2f7; color: #4a5568;">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        </div>
    </form>
</div>
@endsection
