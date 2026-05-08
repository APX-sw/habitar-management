@extends('layouts.app')

@section('title', '| Editar Propietario')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="color: var(--primary-color);">Editar Propietario: {{ $owner->name }}</h1>
        <a href="{{ route('owners.index') }}" class="btn" style="background: var(--secondary-color);">Volver</a>
    </div>

    <div class="card">
        <form action="{{ route('owners.update', $owner) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre / Razón Social</label>
                    <input type="text" name="name" value="{{ $owner->name }}" required style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">DNI / CUIT</label>
                    <input type="text" name="dni_cuit" value="{{ $owner->dni_cuit }}" required style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                    <input type="email" name="email" value="{{ $owner->email }}" style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Teléfono</label>
                    <input type="text" name="phone" value="{{ $owner->phone }}" style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Notas de Contacto</label>
                <textarea name="contact" rows="3" style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">{{ $owner->contact }}</textarea>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem;">Actualizar Datos</button>
            </div>
        </form>
    </div>
</div>
@endsection
