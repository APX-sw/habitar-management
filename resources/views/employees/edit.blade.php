@extends('layouts.app')

@section('title', '| Editar Empleado')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700;">Editar Empleado</h1>
            <p style="color: var(--text-light); font-size: 0.95rem;">Modificá la información de legajo de {{ $employee->full_name }}.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn" style="background: var(--secondary-color); color: var(--text-main);">Volver</a>
    </div>

    <form action="{{ route('employees.update', $employee) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Tarjeta 1: Información Personal y de Puesto -->
        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid #edf2f7; padding-bottom: 0.8rem; font-weight: 600;">Información General</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label>Nombre *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
                </div>

                <div>
                    <label>Apellido *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                </div>

                <div>
                    <label>DNI / Documento *</label>
                    <input type="text" name="document_number" value="{{ old('document_number', $employee->document_number) }}" required>
                </div>

                <div>
                    <label>Puesto / Cargo *</label>
                    <input type="text" name="job_title" value="{{ old('job_title', $employee->job_title) }}" required>
                </div>

                <div>
                    <label>Fecha de Ingreso *</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}" required>
                </div>

                <div>
                    <label>Usuario del Sistema</label>
                    <select name="user_id">
                        <option value="">-- Sin cuenta de usuario --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $employee->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Contacto y Emergencias -->
        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid #edf2f7; padding-bottom: 0.8rem; font-weight: 600;">Contacto y Emergencias</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label>Email de Contacto *</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" required>
                </div>

                <div>
                    <label>Teléfono *</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                </div>

                <div>
                    <label>Nombre Contacto de Emergencia</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                </div>

                <div>
                    <label>Teléfono de Emergencia</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}">
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Información Bancaria -->
        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid #edf2f7; padding-bottom: 0.8rem; font-weight: 600;">Datos Bancarios</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label>Banco</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}">
                </div>

                <div>
                    <label>CBU / Alias</label>
                    <input type="text" name="cbu_alias" value="{{ old('cbu_alias', $employee->cbu_alias) }}">
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="padding: 1rem 4rem;">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection
