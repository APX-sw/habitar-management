@extends('layouts.app')

@section('title', '| Ficha de Propiedad')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; border-bottom: 2px solid var(--secondary-color); padding-bottom: 1.2rem;">
        <!-- Fila superior: Link de volver sutil -->
        <div style="margin-bottom: 0.8rem;">
            <a href="{{ route('properties.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
                <span style="font-weight: 600;">Volver al listado de propiedades</span>
            </a>
        </div>

        <!-- Fila de título y botón de editar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 1rem;">
            <h1 style="color: var(--primary-color); margin: 0; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em;">Ficha Técnica de Propiedad</h1>
            <div style="display: flex; gap: 0.8rem; align-items: center;">
                <a href="{{ route('properties.edit', $property) }}" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 1.5rem; border: 1px solid #cbd5e0; border-radius: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Editar Propiedad
                </a>
            </div>
        </div>

        <!-- Fila de ubicación y dirección con excelente espaciado -->
        <div style="display: flex; align-items: center; gap: 0.5rem; background: #f0f4f8; padding: 0.5rem 1rem; border-radius: 8px; width: fit-content; margin-top: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2b6cb0" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <span style="font-weight: 700; color: #2d3748;">{{ $property->city->name ?? 'N/A' }}</span>
            <span style="color: #718096;">• {{ $property->province->name ?? 'N/A' }}</span>
            <span style="margin-left: 1rem; color: #a0aec0;">|</span>
            <span style="margin-left: 1rem; color: #4a5568; font-weight: 500;">{{ $property->location }}</span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Columna Izquierda: Datos Técnicos -->
        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Detalles del Inmueble</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase;">Tipo</p>
                        <p style="font-size: 1.1rem; font-weight: 600;">{{ $property->type->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase;">Superficie</p>
                        <p style="font-size: 1.1rem; font-weight: 600;">{{ $property->square_meters ?? '0' }} m²</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase;">Ambientes</p>
                        <p style="font-size: 1.1rem; font-weight: 600;">{{ $property->rooms }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase;">Baños</p>
                        <p style="font-size: 1.1rem; font-weight: 600;">{{ $property->bathrooms }}</p>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                    @if($property->has_garage)
                        <span style="background: #e6fffa; color: #2c7a7b; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">✓ Cochera</span>
                    @endif
                    @if($property->has_patio)
                        <span style="background: #e6fffa; color: #2c7a7b; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">✓ Patio</span>
                    @endif
                    @if($property->has_balcony)
                        <span style="background: #e6fffa; color: #2c7a7b; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">✓ Balcón</span>
                    @endif
                    @if($property->pets_allowed)
                        <span style="background: #fffaf0; color: #9c4221; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">🐾 Mascotas</span>
                    @endif
                </div>

                <div style="margin-top: 2rem;">
                    <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Descripción / Notas</p>
                    <p style="line-height: 1.6; color: #4a5568;">{{ $property->description ?: 'Sin descripción adicional.' }}</p>
                </div>
            </div>

            <!-- Historial de Alquileres -->
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">Historial de Contratos</h3>
                @forelse($property->leases as $lease)
                    <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-weight: 700; color: var(--primary-color);">{{ $lease->tenant->name }}</p>
                            <p style="font-size: 0.8rem; color: var(--text-light);">Desde: {{ $lease->start_date }}</p>
                        </div>
                        <span style="padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.7rem; font-weight: 700; {{ $lease->is_active ? 'background: #C6F6D5; color: #22543D;' : 'background: #E2E8F0; color: #4A5568;' }}">
                            {{ $lease->is_active ? 'VIGENTE' : 'FINALIZADO' }}
                        </span>
                    </div>
                @empty
                    <p style="text-align: center; color: var(--text-light); padding: 2rem;">No hay contratos registrados para esta propiedad.</p>
                @endforelse
            </div>

            <!-- Conceptos Adheridos (Impuestos / Servicios) -->
            <div class="card" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">
                    <h3 style="color: var(--primary-color); margin: 0;">Impuestos y Servicios Adheridos</h3>
                    <button onclick="document.getElementById('addConceptModal').style.display='flex'" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 34px; padding: 0 1rem; border: 1px solid #cbd5e0;">+ Adherir Concepto</button>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                    @forelse($property->recurrentConcepts as $concept)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.8rem 1rem; background: #f7fafc; border-radius: 8px; border: 1px solid #edf2f7;">
                            <div>
                                <p style="font-size: 0.95rem; font-weight: 700; color: var(--primary-color); margin: 0 0 0.3rem 0;">{{ $concept->name }}</p>
                                @if($concept->pivot->payment_code)
                                    <p style="font-size: 0.8rem; color: #4a5568; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                                        <span style="background: #e2e8f0; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 700; font-family: monospace; letter-spacing: 0.05em;">{{ $concept->pivot->payment_code }}</span>
                                    </p>
                                @endif
                                @if($concept->pivot->notes)
                                    <p style="font-size: 0.75rem; color: #718096; margin: 0.3rem 0 0 0; font-style: italic;">{{ $concept->pivot->notes }}</p>
                                @endif
                            </div>
                            <div>
                                <form action="{{ route('properties.remove-concept', [$property, $concept]) }}" method="POST" onsubmit="return confirm('¿Desvincular este concepto?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn" style="background: #FFF5F5; color: #C53030; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 32px; padding: 0 1rem; border: 1px solid #feb2b2;">Desvincular</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; color: var(--text-light); padding: 1rem; font-size: 0.9rem;">No hay impuestos o servicios adheridos.</p>
                    @endforelse
                </div>
            </div>

            <!-- Documentación -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--secondary-color); padding-bottom: 0.5rem;">
                    <h3 style="color: var(--primary-color); margin: 0;">Documentación de Propiedad</h3>
                    <button onclick="openPropertyDocsModal({{ $property->id }}, '{{ $property->location }}')" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 34px; padding: 0 1rem; border: 1px solid #cbd5e0;">+ Subir Documento</button>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                    @forelse($property->documents as $doc)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.8rem; background: #f7fafc; border-radius: 8px; border: 1px solid #edf2f7;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <div style="background: #E2E8F0; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a5568" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </div>
                                <div>
                                    <p style="font-size: 0.9rem; font-weight: 600; color: #2d3748; margin: 0;">{{ $doc->filename }}</p>
                                    <p style="font-size: 0.7rem; color: #a0aec0; margin: 0;">{{ number_format($doc->size / 1024 / 1024, 2) }} MB • {{ strtoupper(explode('/', $doc->mime_type)[1] ?? 'FILE') }}</p>
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" class="btn" style="background: #edf2f7; color: #4a5568; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 32px; padding: 0 1rem; border: 1px solid #cbd5e0;">Ver</a>
                                <form action="{{ route('property-documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('¿Eliminar este documento?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn" style="background: #FFF5F5; color: #C53030; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 32px; padding: 0 1rem; border: 1px solid #feb2b2;">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; color: var(--text-light); padding: 1rem; font-size: 0.9rem;">No hay documentos cargados.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Propietario -->
        <div>
            <div class="card" style="border-top: 4px solid var(--accent-color);">
                <p style="font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; margin-bottom: 1rem;">Dueño de la Propiedad</p>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="width: 50px; height: 50px; background: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color); font-size: 1.2rem;">
                        {{ substr($property->owner->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 style="color: var(--primary-color); margin: 0;">{{ $property->owner->name }}</h4>
                        <p style="font-size: 0.85rem; color: var(--text-light); margin: 0;">{{ $property->owner->dni_cuit }}</p>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <div style="font-size: 0.9rem;"><span style="color: var(--text-light);">📞</span> {{ $property->owner->phone ?: 'Sin teléfono' }}</div>
                    <div style="font-size: 0.9rem;"><span style="color: var(--text-light);">✉️</span> {{ $property->owner->email ?: 'Sin email' }}</div>
                </div>
                <a href="{{ route('owners.show', $property->owner) }}" class="btn" style="width: 100%; background: var(--primary-color); color: white; border: 1px solid var(--primary-color); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; height: 42px; margin-top: 1.5rem; font-size: 0.85rem;">Ver Perfil Completo</a>
            </div>

            @if(!$property->activeLease)
                <div style="margin-top: 2rem; background: #ebf8ff; padding: 2rem; border-radius: 15px; border: 1px dashed #3182ce; text-align: center;">
                    <p style="color: #2b6cb0; font-weight: 700; margin-bottom: 1rem;">¡Esta propiedad está disponible!</p>
                    <a href="{{ route('leases.create', ['property_id' => $property->id]) }}" class="btn" style="background: #3182ce; color: white; border: 1px solid #3182ce; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 1.5rem;">Crear Nuevo Contrato</a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Premium de Carga de Documentos -->
@include('properties.partials.docs_modal')

<!-- Modal para Adherir Concepto Recurrente -->
<div id="addConceptModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: var(--primary-color); margin: 0; font-size: 1.25rem;">Adherir Impuesto/Servicio</h3>
            <button type="button" onclick="document.getElementById('addConceptModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #a0aec0; line-height: 1;">&times;</button>
        </div>
        <form action="{{ route('properties.add-concept', $property) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light);">Concepto</label>
                <select name="recurrent_concept_id" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    <option value="">-- Seleccionar Concepto --</option>
                    @foreach($allRecurrentConcepts as $rc)
                        <option value="{{ $rc->id }}">{{ $rc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light);">Código de Pago (Opcional)</label>
                <input type="text" name="payment_code" placeholder="Ej: 00012345678" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-light);">Notas Adicionales (Opcional)</label>
                <input type="text" name="notes" placeholder="Ej: Vence los días 10" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="document.getElementById('addConceptModal').style.display='none'" class="btn" style="background: #edf2f7; color: #4a5568; font-weight: 700; padding: 0.6rem 1.2rem;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.2rem;">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
