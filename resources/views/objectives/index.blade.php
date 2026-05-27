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
            <select name="employee_id" onchange="this.form.submit()" class="form-control" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <option value="">Todos los empleados</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->last_name }}, {{ $emp->first_name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--text-light);">Estado</label>
            <select name="status" onchange="this.form.submit()" class="form-control" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <option value="">Todos los estados</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Proceso</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--text-light);">Periodicidad</label>
            <select name="period" onchange="this.form.submit()" class="form-control" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <option value="">Todas</option>
                <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Diario</option>
                <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Mensual</option>
            </select>
        </div>
        <div>
            <a href="{{ route('objectives.index') }}" class="btn" style="color: #718096; background: #edf2f7; border-radius: 6px; text-decoration: none; padding: 0.6rem 1.5rem; font-weight: 600; display: inline-block;">Limpiar</a>
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
                                    {{ substr($obj->employee->first_name ?? 'E', 0, 1) }}{{ substr($obj->employee->last_name ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--primary-color);">{{ $obj->employee->last_name }}, {{ $obj->employee->first_name }}</div>
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
                                <!-- Ver Detalles Button -->
                                <button onclick="openDetailsModal({{ $obj->id }})" class="btn" style="background: #ebf8ff; color: #2b6cb0; padding: 0.4rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;" title="Ver Detalles">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    Detalles
                                </button>

                                <form action="{{ route('objectives.destroy', $obj) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Seguro que deseás eliminar este objetivo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #fc8181; cursor: pointer; padding: 0.4rem;" title="Eliminar">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <!-- Modal de Detalles del Objetivo (Reutilizado para Admin) -->
                            <div id="details-modal-{{ $obj->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center; backdrop-filter: blur(4px); text-align: left;">
                                <div class="card" style="width: 700px; max-width: 95%; max-height: 90vh; background: white; border-radius: 12px; padding: 2rem; display: flex; flex-direction: column;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid #edf2f7; padding-bottom: 1rem;">
                                        <div>
                                            <h3 style="margin: 0 0 0.5rem 0; color: var(--primary-color); font-weight: 800; font-size: 1.4rem;">{{ $obj->title }}</h3>
                                            <div style="display: flex; gap: 1rem; font-size: 0.85rem; color: #718096;">
                                                <span><strong style="color: #4a5568;">Empleado:</strong> {{ $obj->employee->full_name ?? 'Empleado' }}</span>
                                                @if($obj->due_date)
                                                    <span><strong style="color: #4a5568;">Vence:</strong> {{ \Carbon\Carbon::parse($obj->due_date)->format('d/m/Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" onclick="closeDetailsModal({{ $obj->id }})" style="background: none; border: none; cursor: pointer; color: #a0aec0; transition: color 0.2s;" onmouseover="this.style.color='#4a5568'" onmouseout="this.style.color='#a0aec0'">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </div>

                                    <div style="flex: 1; overflow-y: auto; padding-right: 0.5rem; margin-bottom: 1.5rem;">
                                        <div style="margin-bottom: 1.5rem;">
                                            <h4 style="font-size: 0.95rem; color: #4a5568; margin: 0 0 0.5rem 0;">Descripción del Objetivo</h4>
                                            <p style="margin: 0; font-size: 0.95rem; color: #2d3748; line-height: 1.5; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">{{ $obj->description }}</p>
                                        </div>

                                        <h4 style="font-size: 0.95rem; color: #4a5568; margin: 0 0 1rem 0; border-bottom: 2px solid #edf2f7; padding-bottom: 0.5rem;">Historial y Comentarios</h4>
                                        
                                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                                            @forelse($obj->comments as $comment)
                                                <div style="background: {{ $comment->user_id === \Illuminate\Support\Facades\Auth::id() ? '#ebf8ff' : '#f7fafc' }}; padding: 1rem; border-radius: 8px; border: 1px solid {{ $comment->user_id === \Illuminate\Support\Facades\Auth::id() ? '#bee3f8' : '#e2e8f0' }};">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                                        <strong style="font-size: 0.85rem; color: {{ $comment->user_id === \Illuminate\Support\Facades\Auth::id() ? '#2b6cb0' : '#4a5568' }};">{{ $comment->user->name ?? 'Usuario' }}</strong>
                                                        <span style="font-size: 0.75rem; color: #a0aec0;">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                    <div style="font-size: 0.95rem; color: #2d3748; white-space: pre-wrap; margin-bottom: {{ $comment->file_path ? '0.8rem' : '0' }};">{{ $comment->comment }}</div>
                                                    
                                                    @if($comment->file_path)
                                                        <a href="{{ Storage::url($comment->file_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; background: white; padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid #cbd5e0; color: #4a5568; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)'" onmouseout="this.style.borderColor='#cbd5e0'; this.style.boxShadow='none'">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                                            {{ $comment->file_name ?? 'Ver archivo adjunto' }}
                                                        </a>
                                                    @endif
                                                </div>
                                            @empty
                                                <div style="text-align: center; padding: 2rem; color: #a0aec0; font-size: 0.9rem; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e0;">
                                                    No hay comentarios o avances registrados aún.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    
                                    <form action="{{ route('objectives.comments.store', $obj) }}" method="POST" enctype="multipart/form-data" style="margin: 0; background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid #edf2f7;">
                                        @csrf
                                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Agregar Feedback o Documento</label>
                                        <textarea name="comment" rows="2" required placeholder="Escribí acá tu comentario..." style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #cbd5e0; margin-bottom: 0.8rem; resize: vertical;"></textarea>
                                        
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="position: relative; overflow: hidden; display: inline-block;">
                                                <button type="button" class="btn" style="background: white; border: 1px solid #cbd5e0; color: #4a5568; padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px; display: flex; align-items: center; gap: 0.4rem;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                                    Adjuntar archivo
                                                </button>
                                                <input type="file" name="attachment" style="position: absolute; top: 0; left: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;" onchange="document.getElementById('file-name-admin-{{ $obj->id }}').innerText = this.files.length > 0 ? this.files[0].name : '';">
                                            </div>
                                            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.9rem;">
                                                Enviar
                                            </button>
                                        </div>
                                        <div id="file-name-admin-{{ $obj->id }}" style="font-size: 0.8rem; color: #718096; margin-top: 0.5rem; font-weight: 600;"></div>
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
    function openDetailsModal(id) { document.getElementById('details-modal-' + id).style.display = 'flex'; }
    function closeDetailsModal(id) { document.getElementById('details-modal-' + id).style.display = 'none'; }
</script>
@endsection
