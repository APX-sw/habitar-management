@extends('layouts.app')

@section('title', '| Mi Espacio de Trabajo')

@section('content')
<div style="margin-bottom: 2.5rem;">
    <h1 style="color: var(--primary-color); font-size: 2.2rem; margin-bottom: 0.4rem; font-weight: 800; letter-spacing: -0.03em;">Mi Espacio de Trabajo</h1>
    <p style="color: var(--text-light); font-size: 1rem; font-weight: 500; font-family: 'Outfit', sans-serif; margin: 0; line-height: 1.5;">
        Hola, <span style="color: var(--accent-color); font-weight: 700;">{{ $employee->name }}</span>. Aquí puedes gestionar tu asistencia y objetivos.
    </p>
</div>

@if(session('success'))
    <div style="background: #c6f6d5; color: #22543d; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: #fed7d7; color: #822727; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600;">
        {{ session('error') }}
    </div>
@endif

<!-- Widget de Autogestión de Asistencia -->
<div class="card" style="margin-bottom: 3rem; background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%); border-left: 4px solid var(--accent-color); padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; align-items: center; gap: 1.2rem;">
        <div style="background: #e6fffa; color: var(--accent-color); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <h3 style="margin: 0; color: var(--primary-color); font-size: 1.15rem; font-weight: 700;">Control de Asistencia Diario</h3>
            <p style="margin: 0.2rem 0 0; color: var(--text-light); font-size: 0.9rem;">Registrá tu ingreso o informá una ausencia del día de forma simple y rápida.</p>
        </div>
    </div>

    <div style="display: flex; gap: 0.8rem; align-items: center;">
        @if(isset($todayAttendance) && $todayAttendance)
            @if($todayAttendance->status === 'present')
                @if($todayAttendance->check_out)
                    <div style="display: flex; align-items: center; gap: 0.5rem; background: #e2e8f0; color: #4a5568; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; font-size: 0.9rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        Salida Registrada a las {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') }}
                    </div>
                @else
                    <div style="display: flex; align-items: center; gap: 0.5rem; background: #e6fffa; color: #234e52; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; font-size: 0.9rem; margin-right: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Ingreso Marcado a las {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') }}
                    </div>
                    <form action="{{ route('attendances.check_out') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn" style="background: #e53e3e; color: white; padding: 0.65rem 1.5rem; font-size: 0.9rem; border-radius: 8px; font-weight: 700; box-shadow: 0 4px 10px rgba(229, 62, 62, 0.2);">
                            Marcar Salida
                        </button>
                    </form>
                @endif
            @else
                <div style="display: flex; align-items: center; gap: 0.5rem; background: #fff5f5; color: #c53030; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; font-size: 0.9rem;">
                    Ausencia Registrada: {{ $todayAttendance->absenceReason->name ?? 'Ausente' }}
                </div>
            @endif
        @else
            <form action="{{ route('attendances.check_in') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.5rem; font-size: 0.9rem; border-radius: 8px;">
                    Marcar Ingreso
                </button>
            </form>
            <button onclick="openAbsenceModal()" class="btn" style="background: #edf2f7; color: var(--text-main); padding: 0.65rem 1.2rem; font-size: 0.9rem; font-weight: 600; border-radius: 8px;">
                Avisar Ausencia
            </button>
        @endif
    </div>
</div>

<!-- Modal para Reportar Ausencia -->
<div id="absence-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 480px; max-width: 95%; background: white; border-radius: 12px; padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); font-weight: 700;">Informar Ausencia / Licencia</h3>
        
        <form action="{{ route('attendances.absence') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha de la Ausencia *</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Motivo de la Ausencia *</label>
                <select name="absence_reason_id" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <option value="">-- Seleccionar Motivo --</option>
                    @foreach($activeAbsenceReasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Observaciones / Comentario</label>
                <textarea name="notes" rows="3" placeholder="Opcional. Brindá detalles si es necesario (ej. reposo médico)." style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="closeAbsenceModal()" class="btn" style="background: #edf2f7; color: var(--text-main);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Informar</button>
            </div>
        </form>
    </div>
</div>

<!-- Objetivos Kanban -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; color: var(--primary-color); font-size: 1.5rem; font-weight: 700;">Mis Objetivos</h2>
    <button onclick="openCreateObjectiveModal()" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nuevo Objetivo
    </button>
</div>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 3rem;">
    <!-- Pendientes -->
    <div style="background: #f7fafc; border-radius: 12px; padding: 1rem; border-top: 4px solid #cbd5e0;">
        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; color: #4a5568; display: flex; justify-content: space-between; align-items: center;">
            Pendientes
            <span style="background: #e2e8f0; color: #4a5568; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.8rem;">
                {{ $objectives->where('status', 'pending')->count() }}
            </span>
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($objectives->where('status', 'pending') as $obj)
                @include('workspace._objective_card', ['obj' => $obj])
            @endforeach
            @if($objectives->where('status', 'pending')->isEmpty())
                <div style="text-align: center; color: #a0aec0; padding: 2rem 0; font-size: 0.9rem;">No hay objetivos pendientes</div>
            @endif
        </div>
    </div>

    <!-- En Proceso -->
    <div style="background: #ebf8ff; border-radius: 12px; padding: 1rem; border-top: 4px solid #3182ce;">
        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; color: #2b6cb0; display: flex; justify-content: space-between; align-items: center;">
            En Proceso
            <span style="background: #bee3f8; color: #2b6cb0; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.8rem;">
                {{ $objectives->where('status', 'in_progress')->count() }}
            </span>
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($objectives->where('status', 'in_progress') as $obj)
                @include('workspace._objective_card', ['obj' => $obj])
            @endforeach
            @if($objectives->where('status', 'in_progress')->isEmpty())
                <div style="text-align: center; color: #90cdf4; padding: 2rem 0; font-size: 0.9rem;">No hay objetivos en proceso</div>
            @endif
        </div>
    </div>

    <!-- Completados -->
    <div style="background: #f0fff4; border-radius: 12px; padding: 1rem; border-top: 4px solid #38a169;">
        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; color: #22543d; display: flex; justify-content: space-between; align-items: center;">
            Completados
            <span style="background: #c6f6d5; color: #22543d; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.8rem;">
                {{ $objectives->where('status', 'completed')->count() }}
            </span>
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($objectives->where('status', 'completed') as $obj)
                @include('workspace._objective_card', ['obj' => $obj])
            @endforeach
            @if($objectives->where('status', 'completed')->isEmpty())
                <div style="text-align: center; color: #9ae6b4; padding: 2rem 0; font-size: 0.9rem;">No hay objetivos completados</div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Crear Objetivo -->
<div id="create-objective-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 500px; max-width: 95%; background: white; border-radius: 12px; padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); font-weight: 700;">Crear Nuevo Objetivo</h3>
        
        <form action="{{ route('objectives.employee_store') }}" method="POST">
            @csrf
            
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
                <button type="submit" class="btn btn-primary">Crear</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAbsenceModal() { document.getElementById('absence-modal').style.display = 'flex'; }
    function closeAbsenceModal() { document.getElementById('absence-modal').style.display = 'none'; }
    function openCreateObjectiveModal() { document.getElementById('create-objective-modal').style.display = 'flex'; }
    function closeCreateObjectiveModal() { document.getElementById('create-objective-modal').style.display = 'none'; }
</script>

@endsection
