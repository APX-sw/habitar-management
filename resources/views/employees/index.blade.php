@extends('layouts.app')

@section('title', '| Legajos de Empleados')

@section('content')
<div>
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700;">Legajos de Empleados</h1>
            <p style="color: var(--text-light); font-size: 0.95rem;">Gestioná el personal, sus datos de contacto, bancarios y documentación adjunta.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('attendances.index') }}" class="btn" style="background: var(--secondary-color); color: var(--text-main);">
                Reporte de Asistencias
            </a>
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                Registrar Empleado
            </a>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border-radius: var(--border-radius); box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Empleado</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">DNI</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Puesto</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Contacto</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Fecha de Ingreso</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1.2rem; font-weight: 600; color: var(--primary-color);">
                                <a href="{{ route('employees.show', $employee) }}" style="color: var(--accent-color); text-decoration: none; font-weight: 600;">
                                    {{ $employee->full_name }}
                                </a>
                                @if($employee->user)
                                    <div style="font-size: 0.75rem; color: #718096;">Asociado a: {{ $employee->user->name }}</div>
                                @else
                                    <div style="font-size: 0.75rem; color: #e53e3e;">Sin cuenta asociada</div>
                                @endif
                            </td>
                            <td style="padding: 1.2rem; color: var(--text-main);">{{ $employee->document_number }}</td>
                            <td style="padding: 1.2rem; color: var(--text-main);">{{ $employee->job_title }}</td>
                            <td style="padding: 1.2rem; color: var(--text-main);">
                                <div style="font-size: 0.9rem;">{{ $employee->email }}</div>
                                <div style="font-size: 0.8rem; color: #718096;">{{ $employee->phone }}</div>
                            </td>
                            <td style="padding: 1.2rem; color: var(--text-main);">{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</td>
                            <td style="padding: 1.2rem; text-align: right; white-space: nowrap;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                    <a href="{{ route('employees.show', $employee) }}" class="btn" style="background: #edf2f7; color: var(--text-main); padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px;">
                                        Ver Legajo
                                    </a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn" style="background: #e2e8f0; color: var(--text-main); padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px;">
                                        Editar
                                    </a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este legajo y todos sus documentos asociados?')" style="margin: 0; display: inline-block;">
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
                            <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-light);">
                                <svg style="width: 48px; height: 48px; margin: 0 auto 1rem; display: block; stroke: currentColor; color: #cbd5e0;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                                </svg>
                                No hay empleados registrados aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div style="padding: 1rem; border-top: 1px solid #edf2f7;">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
