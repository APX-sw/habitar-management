@extends('layouts.app')

@section('title', '| Nuevo Inquilino')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="color: var(--primary-color);">Registrar Nuevo Inquilino</h1>
        <a href="{{ route('tenants.index') }}" class="btn" style="background: var(--secondary-color);">Volver</a>
    </div>

    <form action="{{ route('tenants.store') }}" method="POST">
        @csrf
        <div class="card">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">DNI / CUIT</label>
                    <input type="text" name="dni_cuit" value="{{ old('dni_cuit') }}" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contacto Emergencia</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                </div>

                <div style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Otros Contactos / Redes</label>
                    <input type="text" name="contact" value="{{ old('contact') }}" placeholder="Ej: Instagram, LinkedIn, etc." style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                </div>

                <div style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Referencias / Notas</label>
                    <textarea name="references" rows="4" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--secondary-color);">{{ old('references') }}</textarea>
                </div>
            </div>

            <div style="margin-top: 2rem; text-align: center;">
                <button type="submit" class="btn btn-primary" style="padding: 1rem 4rem;">Guardar Inquilino</button>
            </div>
        </div>
    </form>
</div>
@endsection
