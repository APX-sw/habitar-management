@extends('layouts.app')

@section('title', '| Nuevo Usuario')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <h2 style="margin: 0; color: var(--primary-color); font-size: 1.5rem;">Nuevo Usuario</h2>
        <p style="margin: 0.2rem 0 0; color: var(--text-light); font-size: 0.9rem;">Registra un nuevo usuario en el sistema.</p>
    </div>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            </div>
            <div>
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>

        <div style="margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: var(--border-radius); border: 1px solid #edf2f7;">
            <label style="font-size: 1.1rem; margin-bottom: 1rem !important;">Asignar Roles</label>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                @foreach($roles as $role)
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500 !important; cursor: pointer; background: white; padding: 0.8rem 1.2rem; border-radius: 8px; border: 1px solid #e2e8f0; margin: 0 !important;">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }} style="width: auto !important; margin: 0 !important;">
                        {{ ucfirst($role->name) }}
                    </label>
                @endforeach
            </div>
            @if($roles->isEmpty())
                <p style="color: var(--text-light); font-size: 0.9rem;">No hay roles creados. Ve a la sección de Roles para crear uno.</p>
            @endif
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('users.index') }}" class="btn" style="background: #edf2f7; color: #4a5568;">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        </div>
    </form>
</div>
@endsection
