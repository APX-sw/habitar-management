@extends('layouts.app')

@section('title', '| Objetivos Generales')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1.5rem;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin-bottom: 0.4rem; font-weight: 800; letter-spacing: -0.03em;">Objetivos y Metas</h1>
        <p style="color: var(--text-light); font-size: 1rem; font-weight: 500; font-family: 'Outfit', sans-serif; margin: 0; line-height: 1.5;">
            Supervisión general del rendimiento y metas de todo el equipo de trabajo.
        </p>
    </div>
    <div>
        <button onclick="openCreateObjectiveModal()" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; font-weight: 600; border-radius: 8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Asignar Nuevo Objetivo
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background: #c6f6d5; color: #22543d; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div style="background: #fed7d7; color: #822727; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600;">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<!-- Filtros -->
<div class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
    <form action="{{ route('objectives.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--text-light);">Empleado</label>
            <select name="employee_id" class="form-control" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <option value="">Todos los empleados</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->last_name }}, {{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--text-light);">Estado</label>
            <select name="status" class="form-control" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <option value="">Todos los estados</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Proceso</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--text-light);">Periodicidad</label>
            <select name="period" class="form-control" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <option value="">Todas</option>
                <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Diario</option>
                <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Mensual</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn" style="background: #edf2f7; color: #4a5568; padding: 0.6rem 1.5rem; font-weight: 600; border-radius: 6px;">Filtrar</button>
            <a href="{{ route('objectives.index') }}" class="btn" style="color: #718096; text-decoration: none; padding: 0.6rem 1rem;">Limpiar</a>
        </div>
    </form>
</div>

<!-- Lista de Objetivos -->
<div class="card">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f7fafc;">
                    <th style="padding: 1rem; text-align: left; color: var(--text-light); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Empleado</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-light); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Objetivo</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-light); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Periodicidad</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-light); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Estado</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-light); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($objectives as $obj)
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 1rem; vertical-align: top;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <div style="background: #e2e8f0; color: #4a5568; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">
                                    {{ substr($obj->employee->name ?? 'E', 0, 1) }}{{ substr($obj->employee->last_name ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--primary-color);">{{ $obj->employee->last_name }}, {{ $obj->employee->name }}</div>
                                    <div style="font-size: 0.8rem; color: #718096;">Asignado por: {{ $obj->creator->name ?? 'Admin' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1rem; vertical-align: top; max-width: 350px;">
                            <div style="font-weight: 700; color: var(--text-color); margin-bottom: 0.25rem;">{{ $obj->title }}</div>
                            <div style="font-size: 0.85rem; color: #718096; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $obj->description }}">{{ $obj->description }}</div>
                            
                            @if($obj->due_date)
                                <div style="font-size: 0.8rem; color: #e53e3e; margin-top: 0.5rem; font-weight: 600;">
                                    Vence: {{ \Carbon\Carbon::parse($obj->due_date)->format('d/m/Y') }}
                                </div>
                            @endif

                            @if($obj->employee_notes || $obj->admin_comment)
                                <button onclick="openDetailsModal({{ $obj->id }})" style="margin-top: 0.5rem; background: none; border: none; color: #3182ce; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.2rem; padding: 0;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    Ver Notas y Feedback
                                </button>
                                
                                <!-- Modales de Detalles Generados aquí mismo -->
                                <div id="details-modal-{{ $obj->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
                                    <div class="card" style="width: 550px; max-width: 95%; background: white; border-radius: 12px; padding: 2rem; white-space: normal;">
                                        <h3 style="margin-bottom: 0.5rem; color: var(--primary-color); font-weight: 700;">{{ $obj->title }}</h3>
                                        <p style="margin-bottom: 1.5rem; font-size: 0.95rem; color: #4a5568;">{{ $obj->description }}</p>
                                        
                                        @if($obj->employee_notes)
                                            <div style="background: #f7fafc; padding: 1rem; border-radius: 8px; border-left: 4px solid #4a5568; margin-bottom: 1.5rem;">
                                                <div style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #a0aec0; margin-bottom: 0.5rem;">Notas del Empleado</div>
                                                <div style="font-size: 0.95rem; color: #2d3748; white-space: pre-wrap;">{{ $obj->employee_notes }}</div>
                                            </div>
                                        @endif

                                        @if($obj->admin_comment)
                                            <div style="background: #fffaf0; padding: 1rem; border-radius: 8px; border-left: 4px solid #ed8936; margin-bottom: 1.5rem;">
                                                <div style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #dd6b20; margin-bottom: 0.5rem;">Feedback del Admin</div>
                                                <div style="font-size: 0.95rem; color: #7b341e; white-space: pre-wrap;">{{ $obj->admin_comment }}</div>
                                            </div>
                                        @endif

                                        <div style="text-align: right;">
                                            <button type="button" onclick="closeDetailsModal({{ $obj->id }})" class="btn" style="background: #edf2f7; color: var(--text-main);">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center; vertical-align: top;">
                            @php
                                $periodColors = [
                                    'daily' => ['bg' => '#e6fffa', 'color' => '#234e52', 'label' => 'Diario'],
                                    'weekly' => ['bg' => '#ebf8ff', 'color' => '#2b6cb0', 'label' => 'Semanal'],
                                    'monthly' => ['bg' => '#faf5ff', 'color' => '#553c9a', 'label' => 'Mensual']
                                ];
                                $p = $periodColors[$obj->period] ?? ['bg' => '#edf2f7', 'color' => '#4a5568', 'label' => ucfirst($obj->period)];
                            @endphp
                            <span style="background: {{ $p['bg'] }}; color: {{ $p['color'] }}; font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 999px; font-weight: 700; letter-spacing: 0.02em;">
                                {{ $p['label'] }}
                            </span>
                        </td>
                        <td style="padding: 1rem; text-align: center; vertical-align: top;">
                            @php
                                $statusColors = [
                                    'pending' => ['bg' => '#edf2f7', 'color' => '#4a5568', 'label' => 'Pendiente'],
                                    'in_progress' => ['bg' => '#ebf8ff', 'color' => '#3182ce', 'label' => 'En Proceso'],
                                    'completed' => ['bg' => '#c6f6d5', 'color' => '#38a169', 'label' => 'Completado']
                                ];
                                $s = $statusColors[$obj->status] ?? ['bg' => '#edf2f7', 'color' => '#4a5568', 'label' => ucfirst($obj->status)];
                            @endphp
                            <span style="background: {{ $s['bg'] }}; color: {{ $s['color'] }}; font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 999px; font-weight: 700; letter-spacing: 0.02em;">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td style="padding: 1rem; text-align: center; vertical-align: top;">
                            <div style="display: flex; justify-content: center; gap: 0.5rem;">
                                <!-- Dar Feedback Button -->
                                <button onclick="openFeedbackModal({{ $obj->id }}, '{{ addslashes($obj->admin_comment) }}')" class="btn" style="background: #fffaf0; color: #dd6b20; padding: 0.4rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;" title="Dar Feedback">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                    Feedback
                                </button>

                                <form action="{{ route('objectives.destroy', $obj) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Seguro que deseás eliminar este objetivo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #fc8181; cursor: pointer; padding: 0.4rem;" title="Eliminar">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <!-- Modal para Dar Feedback -->
                            <div id="feedback-modal-{{ $obj->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center; backdrop-filter: blur(4px); text-align: left;">
                                <div class="card" style="width: 500px; max-width: 95%; background: white; border-radius: 12px; padding: 2rem; white-space: normal;">
                                    <h3 style="margin-bottom: 0.5rem; color: var(--primary-color); font-weight: 700;">Feedback Administrativo</h3>
                                    <p style="margin-bottom: 1.5rem; font-size: 0.9rem; color: #718096;">Dejar un comentario o devolución sobre el objetivo <strong>{{ $obj->title }}</strong> de {{ $obj->employee->name }}.</p>
                                    
                                    <form action="{{ route('objectives.feedback', $obj) }}" method="POST">
                                        @csrf
                                        <div style="margin-bottom: 1.5rem;">
                                            <textarea name="admin_comment" rows="4" required placeholder="Escriba su feedback aquí..." style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">{{ $obj->admin_comment }}</textarea>
                                        </div>

                                        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                                            <button type="button" onclick="closeFeedbackModal({{ $obj->id }})" class="btn" style="background: #edf2f7; color: var(--text-main);">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar Feedback</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-light);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0" stroke-width="1.5" style="margin-bottom: 1rem;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <p>No se encontraron objetivos que coincidan con los filtros.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($objectives->hasPages())
        <div style="padding: 1.5rem; border-top: 1px solid #e2e8f0;">
            {{ $objectives->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Modal para Crear Objetivo (Admin) -->
<div id="create-objective-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 500px; max-width: 95%; background: white; border-radius: 12px; padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); font-weight: 700;">Asignar Nuevo Objetivo</h3>
        
        <form action="{{ route('objectives.store') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Empleado *</label>
                <select name="employee_id" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <option value="">Seleccionar empleado...</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->last_name }}, {{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Título *</label>
                <input type="text" name="title" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción *</label>
                <textarea name="description" rows="3" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Periodicidad *</label>
                    <select name="period" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <option value="daily">Diario</option>
                        <option value="weekly">Semanal</option>
                        <option value="monthly">Mensual</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha de Vencimiento</label>
                    <input type="date" name="due_date" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="closeCreateObjectiveModal()" class="btn" style="background: #edf2f7; color: var(--text-main);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Asignar Objetivo</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateObjectiveModal() { document.getElementById('create-objective-modal').style.display = 'flex'; }
    function closeCreateObjectiveModal() { document.getElementById('create-objective-modal').style.display = 'none'; }
    function openFeedbackModal(id) { document.getElementById('feedback-modal-' + id).style.display = 'flex'; }
    function closeFeedbackModal(id) { document.getElementById('feedback-modal-' + id).style.display = 'none'; }
    function openDetailsModal(id) { document.getElementById('details-modal-' + id).style.display = 'flex'; }
    function closeDetailsModal(id) { document.getElementById('details-modal-' + id).style.display = 'none'; }
</script>
@endsection
