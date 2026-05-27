@extends('layouts.app')

@section('title', "| Legajo de {$employee->full_name}")

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700;">{{ $employee->full_name }}</h1>
            <p style="color: var(--text-light); font-size: 0.95rem;">Legajo digital centralizado.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('employees.index') }}" class="btn" style="background: var(--secondary-color); color: var(--text-main);">Volver al Listado</a>
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">Editar Datos</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Left Side: Profile Details -->
        <div>
            <!-- Personal Info Card -->
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); font-weight: 600; border-bottom: 1px solid #edf2f7; padding-bottom: 0.8rem;">
                    Datos del Empleado
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem 2rem;">
                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">DNI / Documento</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ $employee->document_number }}</span>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Puesto / Cargo</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ $employee->job_title }}</span>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Fecha de Ingreso</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</span>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Usuario Asociado</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ $employee->user->name ?? 'Sin cuenta vinculada' }}</span>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Email</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ $employee->email }}</span>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Teléfono</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ $employee->phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Emergencies & Bank Info -->
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); font-weight: 600; border-bottom: 1px solid #edf2f7; padding-bottom: 0.8rem;">
                    Contacto de Emergencia y Cuentas
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem 2rem;">
                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Contacto de Emergencia</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ $employee->emergency_contact_name ?? 'No especificado' }}</span>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Teléfono de Emergencia</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ $employee->emergency_contact_phone ?? 'No especificado' }}</span>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Banco</span>
                        <span style="font-weight: 600; font-size: 1.05rem;">{{ $employee->bank_name ?? 'No especificado' }}</span>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">CBU / Alias</span>
                        <span style="font-weight: 600; font-size: 1.05rem; word-break: break-all;">{{ $employee->cbu_alias ?? 'No especificado' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Document Vault -->
        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); font-weight: 600; border-bottom: 1px solid #edf2f7; padding-bottom: 0.8rem;">
                    Documentación Adjunta
                </h3>

                <!-- Upload Form -->
                <form action="{{ route('employees.documents.store', $employee) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px dashed #cbd5e0;">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem;">Tipo de Documento</label>
                        <select name="document_type" required style="padding: 0.5rem; font-size: 0.9rem;">
                            <option value="dni">DNI / Identificación</option>
                            <option value="contract">Contrato Laboral</option>
                            <option value="health">Certificado Médico</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem;">Archivo</label>
                        <input type="file" name="file" required style="border: none !important; box-shadow: none !important; padding: 0 !important; font-size: 0.85rem;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.6rem 1rem; font-size: 0.9rem;">Subir Archivo</button>
                </form>

                <!-- Documents List -->
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    @forelse($employee->documents as $document)
                        <div style="display: flex; justify-content: space-between; align-items: center; background: white; border: 1px solid #edf2f7; padding: 0.8rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.01);">
                            <div style="overflow: hidden; flex: 1; padding-right: 0.5rem;">
                                <div style="font-weight: 600; font-size: 0.85rem; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $document->original_name }}
                                </div>
                                <div style="font-size: 0.75rem; color: #718096; text-transform: uppercase;">
                                    {{ $document->document_type }}
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 0.4rem;">
                                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn" style="background: var(--secondary-color); color: var(--text-main); padding: 0.4rem; border-radius: 6px;" title="Ver/Descargar">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                </a>

                                <form action="{{ route('employees.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este documento?')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn" style="background: #fff5f5; color: #e53e3e; padding: 0.4rem; border-radius: 6px; border: none; cursor: pointer;" title="Eliminar">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 1.5rem; color: var(--text-light); font-size: 0.9rem;">
                            No hay documentos cargados aún.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Historial de Objetivos -->
            <div class="card" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #edf2f7; padding-bottom: 0.8rem;">
                    <h3 style="color: var(--primary-color); font-weight: 600; margin: 0;">Historial de Objetivos</h3>
                    <a href="{{ route('objectives.index', ['employee_id' => $employee->id]) }}" style="font-size: 0.85rem; color: #3182ce; font-weight: 600; text-decoration: none;">Ver Todo</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    @php
                        $employeeObjectives = \App\Models\Objective::where('employee_id', $employee->id)->orderBy('created_at', 'desc')->take(5)->get();
                    @endphp
                    @forelse($employeeObjectives as $obj)
                        <div style="background: white; border: 1px solid #edf2f7; padding: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.01);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <strong style="color: var(--text-color); font-size: 0.95rem;">{{ $obj->title }}</strong>
                                @php
                                    $statusColors = [
                                        'pending' => ['bg' => '#edf2f7', 'color' => '#4a5568', 'label' => 'Pendiente'],
                                        'in_progress' => ['bg' => '#ebf8ff', 'color' => '#3182ce', 'label' => 'En Proceso'],
                                        'completed' => ['bg' => '#c6f6d5', 'color' => '#38a169', 'label' => 'Completado']
                                    ];
                                    $s = $statusColors[$obj->status] ?? ['bg' => '#edf2f7', 'color' => '#4a5568', 'label' => ucfirst($obj->status)];
                                @endphp
                                <span style="background: {{ $s['bg'] }}; color: {{ $s['color'] }}; font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 700;">{{ $s['label'] }}</span>
                            </div>
                            <p style="margin: 0 0 0.5rem 0; font-size: 0.85rem; color: #718096; line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $obj->description }}</p>
                            
                            @if($obj->due_date)
                                <div style="font-size: 0.75rem; color: #a0aec0;">Vence: {{ \Carbon\Carbon::parse($obj->due_date)->format('d/m/Y') }}</div>
                            @endif
                        </div>
                    @empty
                        <div style="text-align: center; padding: 1.5rem; color: var(--text-light); font-size: 0.9rem;">
                            No tiene objetivos registrados.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
