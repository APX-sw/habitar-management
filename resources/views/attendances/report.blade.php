@extends('layouts.app')

@section('title', '| Reportes de Asistencia')

@section('content')
<div>
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700;">Reportes de Asistencia</h1>
            <p style="color: var(--text-light); font-size: 0.95rem;">Monitoreá las presencias, inasistencias y motivos del personal.</p>
        </div>
        <div>
            <a href="{{ route('employees.index') }}" class="btn" style="background: var(--secondary-color); color: var(--text-main);">
                Volver a Legajos
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; color: var(--primary-color); font-size: 1.1rem; font-weight: 600;">Filtros de Búsqueda</h3>
        <form action="{{ route('attendances.index') }}" method="GET">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.2rem; align-items: flex-end;">
                <div>
                    <label style="font-size: 0.85rem;">Desde</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; width: 100%; border: 1px solid #cbd5e0; border-radius: 8px;">
                </div>

                <div>
                    <label style="font-size: 0.85rem;">Hasta</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; width: 100%; border: 1px solid #cbd5e0; border-radius: 8px;">
                </div>

                <div>
                    <label style="font-size: 0.85rem;">Empleado</label>
                    <select name="employee_id" onchange="this.form.submit()" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; width: 100%; border: 1px solid #cbd5e0; border-radius: 8px;">
                        <option value="">-- Todos --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.85rem;">Estado</label>
                    <select name="status" onchange="this.form.submit()" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; width: 100%; border: 1px solid #cbd5e0; border-radius: 8px;">
                        <option value="">-- Todos --</option>
                        <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Presente</option>
                        <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Ausente</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.85rem;">Motivo Ausencia</label>
                    <select name="absence_reason_id" onchange="this.form.submit()" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; width: 100%; border: 1px solid #cbd5e0; border-radius: 8px;">
                        <option value="">-- Todos --</option>
                        @foreach($reasons as $re)
                            <option value="{{ $re->id }}" {{ request('absence_reason_id') == $re->id ? 'selected' : '' }}>
                                {{ $re->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('attendances.index') }}" class="btn" style="background: #edf2f7; color: #4a5568; padding: 0.65rem 1rem; font-size: 0.9rem; border-radius: 8px; width: 100%; text-align: center;" title="Limpiar Filtros">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="card" style="padding: 0; overflow: hidden; border-radius: var(--border-radius); box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Fecha</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Empleado</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Puesto</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Estado</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Motivo Ausencia</th>
                        <th style="padding: 1.2rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1.2rem; font-weight: 600; color: var(--primary-color);">
                                {{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}
                            </td>
                            <td style="padding: 1.2rem; font-weight: 600;">
                                <a href="{{ route('employees.show', $att->employee) }}" style="color: var(--accent-color); text-decoration: none;">
                                    {{ $att->employee->full_name }}
                                </a>
                            </td>
                            <td style="padding: 1.2rem; color: var(--text-main);">
                                {{ $att->employee->job_title }}
                            </td>
                            <td style="padding: 1.2rem;">
                                @if($att->status === 'present')
                                    <span class="badge" style="background: #e6fffa; color: #319795;">
                                        Presente 
                                        @if($att->check_out)
                                            (Salida: {{ \Carbon\Carbon::parse($att->check_out)->format('H:i') }})
                                        @endif
                                    </span>
                                @else
                                    <span class="badge" style="background: #fff5f5; color: #e53e3e;">Ausente</span>
                                @endif
                            </td>
                            <td style="padding: 1.2rem; font-weight: 600; color: var(--primary-color);">
                                {{ $att->absenceReason->name ?? '-' }}
                            </td>
                            <td style="padding: 1.2rem; color: var(--text-light); font-size: 0.9rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $att->notes }}">
                                {{ $att->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-light);">
                                <svg style="width: 48px; height: 48px; margin: 0 auto 1rem; display: block; stroke: currentColor; color: #cbd5e0;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                    <path d="m9 16 2 2 4-4"/>
                                </svg>
                                No se encontraron registros de asistencia con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div style="padding: 1rem; border-top: 1px solid #edf2f7;">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
