@extends('layouts.app')

@section('title', '| Detalle de Contrato')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('leases.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver al listado de contratos</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Ficha de Contrato</h1>
        <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
            <div style="background: var(--bg-card); padding: 0.4rem 1rem; border-radius: 50px; border: 1px solid var(--secondary-color); font-size: 0.9rem; font-weight: 700; color: var(--primary-color);">
                ID #{{ $lease->id }}
            </div>
            <div style="color: var(--text-light); font-weight: 500;">
                {{ \Carbon\Carbon::parse($lease->start_date)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($lease->end_date)->format('d/m/Y') }}
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('leases.renew', $lease) }}" class="btn" style="background: var(--primary-color); color: white; border: none; font-weight: 700; text-decoration: none; display: flex; align-items: center;">♻️ Renovar</a>
        <a href="{{ route('leases.renegotiate', $lease) }}" class="btn" style="background: #E9D8FD; color: #553C9A; border: 1px solid #D6BCFA; font-weight: 700; text-decoration: none; display: flex; align-items: center;">📝 Renegociar</a>
        <button onclick="openDocsModal({{ $lease->id }}, '{{ $lease->property->location }}')" class="btn" style="background: #ebf4ff; color: #2b6cb0; border: 1px solid #bee3f8; font-weight: 700;">📂 Documentos</button>
        @if($lease->is_active)
            <button onclick="document.getElementById('terminateModal').style.display='flex'" class="btn" style="background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; font-weight: 700;">Finalizar Contrato</button>
        @endif
    </div>
</div>

@php
    try {
        $currentRent = $lease->calculateRentForDate(now()->month, now()->year);
    } catch (\Exception $e) {
        $currentRent = $lease->base_price;
    }
@endphp

<!-- Modal de Finalización -->
<div id="terminateModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2.5rem; border-top: 8px solid #c53030;">
        <h2 style="color: #c53030; margin-bottom: 1rem;">Finalizar Contrato</h2>
        <p style="color: var(--text-light); margin-bottom: 2rem; font-size: 0.95rem;">Estás por rescindir el contrato de <strong>{{ $lease->property->location }}</strong>. Esta acción desactivará el contrato y no se podrán generar nuevos cobros mensuales.</p>
        
        <form action="{{ route('leases.terminate', $lease) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; margin-bottom: 0.5rem;">Fecha de Rescisión</label>
                <input type="date" name="termination_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #d2d6dc;">
            </div>

            <div style="margin-bottom: 1.5rem; background: #fff5f5; padding: 1.2rem; border-radius: 12px; border: 1px solid #fed7d7;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 800; color: #c53030; text-transform: uppercase;">Multa por Rescisión ($)</label>
                    <button type="button" onclick="document.querySelector('#penalty_input_field').value = '{{ number_format($currentRent, 2, '.', '') }}'" style="background: #e53e3e; color: white; border: none; border-radius: 6px; padding: 0.3rem 0.6rem; font-size: 0.7rem; font-weight: 800; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Sugerir 1 Mes Actualizado</button>
                </div>
                <input type="number" step="0.01" name="penalty_amount" id="penalty_input_field" value="{{ number_format($currentRent, 2, '.', '') }}" required style="width: 100%; padding: 1rem; border-radius: 8px; border: 1px solid #feb2b2; font-size: 1.4rem; font-weight: 800; color: #c53030;">
                <p style="font-size: 0.7rem; color: #e53e3e; margin-top: 0.5rem; font-style: italic;">* Alquiler vigente hoy: ${{ number_format($currentRent, 2) }}</p>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; margin-bottom: 0.5rem;">Motivo de la baja (Opcional)</label>
                <textarea name="reason" placeholder="Ej: Incumplimiento, mutuo acuerdo..." style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #d2d6dc; min-height: 80px; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('terminateModal').style.display='none'" class="btn" style="background: #f7fafc; color: #4a5568;">Cancelar</button>
                <button type="submit" class="btn" style="background: #c53030; color: white; padding: 1rem 2rem; font-weight: 800; border: none; border-radius: 10px;">Confirmar y Finalizar</button>
            </div>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem;">
    <!-- Column 1: Core Info -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        @if($lease->renewal_status === 'terminated')
            @php
                $terminationPenalty = $lease->extraCharges->where('description', 'Multa por Rescisión Anticipada')->first();
                $reason = $lease->termination_reason ?: ($terminationPenalty->notes ?? null);
            @endphp
            <div class="card" style="border-left: 5px solid #e53e3e; background: #fff5f5; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <h3 style="color: #c53030; margin: 0 0 0.5rem 0; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Contrato Finalizado (Rescindido)
                </h3>
                <p style="color: #9b2c2c; margin: 0; font-size: 0.95rem; font-weight: 500;">
                    Este contrato fue rescindido formalmente el día <strong>{{ \Carbon\Carbon::parse($lease->end_date)->format('d/m/Y') }}</strong>.
                </p>
                @if($reason)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #fed7d7; font-size: 0.9rem; color: #742a2a; font-weight: 500;">
                        <strong>Motivo de la finalización:</strong> {{ $reason }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Propiedad & Inquilino -->
        <div class="card" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 2rem;">
            <div style="border-right: 1px solid #edf2f7; padding-right: 2rem;">
                <h4 style="margin: 0 0 1rem; color: #a0aec0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em;">Propiedad</h4>
                <div style="font-weight: 800; font-size: 1.2rem; color: var(--primary-color);">{{ $lease->property->location }}</div>
                <div style="color: var(--text-light); margin-top: 0.3rem;">{{ $lease->property->city->name }}, {{ $lease->property->province->name }}</div>
                <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                    <div style="font-size: 0.85rem; background: #f7fafc; padding: 0.3rem 0.6rem; border-radius: 6px; color: #4a5568;">{{ $lease->property->rooms }} amb.</div>
                    <div style="font-size: 0.85rem; background: #f7fafc; padding: 0.3rem 0.6rem; border-radius: 6px; color: #4a5568;">{{ $lease->property->square_meters }} m²</div>
                </div>
            </div>
            <div>
                <h4 style="margin: 0 0 1rem; color: #a0aec0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em;">Inquilino</h4>
                <div style="font-weight: 800; font-size: 1.2rem; color: var(--primary-color);">{{ $lease->tenant->name }}</div>
                <div style="color: var(--text-light); margin-top: 0.3rem;">DNI/CUIT: {{ $lease->tenant->dni_cuit }}</div>
                <div style="margin-top: 1rem;">
                    <a href="#" style="color: var(--accent-color); font-weight: 700; font-size: 0.85rem; text-decoration: none;">Ver Perfil Completo →</a>
                </div>
            </div>
        </div>

        <!-- Garante -->
        @if($lease->guarantor_name)
        <div class="card" style="padding: 2rem; border-left: 5px solid #4A5568;">
            <h3 style="margin: 0 0 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Información del Garante
            </h3>
            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem;">
                <div>
                    <div style="font-weight: 800; font-size: 1.2rem; color: var(--primary-color);">{{ $lease->guarantor_name }}</div>
                    <div style="color: var(--text-light); margin-top: 0.3rem; font-weight: 600;">DNI/CUIT: {{ $lease->guarantor_id_number ?? 'N/A' }}</div>
                    <div style="margin-top: 1rem; color: #4A5568; font-size: 0.9rem;">
                        <p style="margin: 0.3rem 0; display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            {{ $lease->guarantor_email ?? 'Sin email' }}
                        </p>
                        <p style="margin: 0.3rem 0; display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            {{ $lease->guarantor_phone ?? 'Sin teléfono' }}
                        </p>
                    </div>
                </div>
                <div style="background: #f7fafc; padding: 1rem; border-radius: 12px; border: 1px solid #edf2f7;">
                    <label style="display: block; color: #a0aec0; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.5rem;">Domicilio declarado</label>
                    <p style="margin: 0; color: #4A5568; font-size: 0.9rem; font-weight: 500;">{{ $lease->guarantor_address ?? 'No especificado' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Condiciones del Alquiler -->
        <div class="card" style="padding: 2rem;">
            <h3 style="margin: 0 0 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Condiciones y Actualización
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #edf2f7;">
                <div>
                    <label style="display: block; color: #a0aec0; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.5rem;">PRECIO BASE</label>
                    <div style="font-size: 1.4rem; font-weight: 800; color: var(--accent-color);">${{ number_format($lease->base_price, 2) }}</div>
                </div>
                <div>
                    <label style="display: block; color: #a0aec0; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.5rem;">ACTUALIZACIÓN</label>
                    <div style="font-size: 1rem; font-weight: 700; color: var(--primary-color);">
                        {{ $lease->update_type === 'fixed' ? 'Incremento Fijo (' . $lease->update_value . '%)' : 'Indexado (' . ($lease->indexType->name ?? 'N/A') . ')' }}
                        <div style="font-size: 0.8rem; color: var(--text-light);">Cada {{ $lease->update_frequency_months }} meses</div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-top: 1.5rem; padding: 0 1rem;">
                <div>
                    <label style="display: block; color: #a0aec0; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.3rem;">DEPÓSITO EN GARANTÍA</label>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #4A5568;">${{ number_format($lease->security_deposit_amount, 2) }}</div>
                </div>
                <div>
                    <label style="display: block; color: #a0aec0; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.3rem;">HONORARIOS INM.</label>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #4A5568;">${{ number_format($lease->agency_fee_amount, 2) }}</div>
                </div>
            </div>

            <!-- Cargos Mensuales -->
            <div style="margin-top: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="margin: 0; font-size: 0.85rem; color: #718096; text-transform: uppercase;">Conceptos Mensuales Adicionales</h4>
                    <button onclick="toggleAddFixedCharge()" class="btn" style="padding: 0.3rem 0.8rem; font-size: 0.75rem; background: #ebf4ff; color: #2b6cb0; border: 1px solid #bee3f8; font-weight: 700;">+ Añadir Concepto</button>
                </div>

                <!-- Formulario Oculto para añadir concepto -->
                <div id="add-fixed-charge-form" style="display: none; background: #f8fafc; padding: 1rem; border-radius: 10px; border: 1px solid #edf2f7; margin-bottom: 1rem;">
                    <form action="{{ route('fixed-charges.store') }}" method="POST" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        @csrf
                        <input type="hidden" name="lease_id" value="{{ $lease->id }}">
                        <input type="text" name="name" placeholder="Nombre (ej: Tasa Municipal)" required style="flex: 1; min-width: 150px; padding: 0.5rem; border-radius: 6px; border: 1px solid #d2d6dc;">
                        <select name="transaction_category_id" required style="padding: 0.5rem; border: 1px solid #d2d6dc; border-radius: 6px;">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == 4 ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <select name="is_paid_by_agency" required style="padding: 0.5rem; border: 1px solid #d2d6dc; border-radius: 6px;">
                            <option value="1">Lo paga Habitar</option>
                            <option value="0">Lo paga Propietario</option>
                        </select>
                        <button type="submit" class="btn" style="background: var(--accent-color); color: white; padding: 0.5rem 1rem; font-size: 0.8rem;">Guardar</button>
                    </form>
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                    @forelse($lease->fixedCharges as $charge)
                        <div class="fixed-charge-badge" style="background: white; border: 1px solid #edf2f7; padding: 0.6rem 1rem; border-radius: 50px; display: flex; align-items: center; gap: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <span style="font-weight: 700; color: var(--primary-color);">{{ $charge->name }}</span>
                            @if($charge->is_paid_by_agency)
                                <span title="El inquilino lo abona, pero el dinero no se rinde al propietario ya que Habitar efectúa el pago final" style="font-size: 0.65rem; background: #edf2f7; color: #4a5568; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 800; text-transform: uppercase;">Habitar</span>
                            @else
                                <span title="El dinero de este concepto se le rinde directamente al propietario" style="font-size: 0.65rem; background: #e6fffa; color: #319795; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 800; text-transform: uppercase;">Propietario</span>
                            @endif
                            <form action="{{ route('fixed-charges.destroy', $charge) }}" method="POST" style="display: inline;">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #cbd5e0; cursor: pointer; font-size: 1.1rem; padding: 0; line-height: 1; transition: color 0.2s;" onmouseover="this.style.color='#c53030'" onmouseout="this.style.color='#cbd5e0'">&times;</button>
                            </form>
                        </div>
                    @empty
                        <p style="color: var(--text-light); font-size: 0.9rem;">No hay conceptos mensuales configurados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAddFixedCharge() {
            const form = document.getElementById('add-fixed-charge-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>

    <!-- Column 2: Extras & Installments -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div class="card" style="padding: 2rem;">
            <h3 style="margin: 0 0 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Cargos Especiales y Cuotas
            </h3>
            
            <form action="{{ route('extra-charges.store') }}" method="POST" style="background: #f8fafc; padding: 1.2rem; border-radius: 12px; border: 1px solid #edf2f7; margin-bottom: 1.5rem;">
                @csrf
                <input type="hidden" name="lease_id" value="{{ $lease->id }}">
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <input type="text" name="description" placeholder="Descripción (ej: Reparación aire)" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                        <input type="number" step="0.01" name="amount" placeholder="Monto Total $" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                        <input type="date" name="billing_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    </div>
                    <select name="transaction_category_id" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $cat->id == 4 ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #4a5568;">
                        <span>En</span>
                        <input type="number" name="total_installments" value="1" min="1" style="width: 50px; padding: 0.4rem; border-radius: 6px; border: 1px solid #d2d6dc; text-align: center;">
                        <span>cuotas mensuales</span>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 0.85rem; font-weight: 700;">Añadir Cargo</button>
                </div>
            </form>

            <div style="max-height: 450px; overflow-y: auto; padding-right: 0.5rem;">
                @forelse($lease->extraCharges->sortBy('billing_date') as $extra)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #edf2f7; background: {{ $loop->odd ? '#fff' : '#fcfcfc' }}">
                        <div>
                            <div style="font-weight: 700; color: var(--primary-color); font-size: 0.9rem;">{{ $extra->description }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; font-weight: 600; margin-top: 0.2rem;">
                                {{ \Carbon\Carbon::parse($extra->billing_date)->format('M Y') }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 800; color: var(--accent-color);">${{ number_format($extra->amount, 2) }}</div>
                            @if($extra->total_installments > 1)
                                <div style="font-size: 0.7rem; color: var(--text-light);">Cuota {{ $extra->installment_number }}/{{ $extra->total_installments }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--text-light); font-size: 0.9rem;">No hay cargos especiales registrados.</div>
                @endforelse
            </div>
        </div>
    </div>
    <script>
        function toggleAddFixedCharge() {
            const form = document.getElementById('add-fixed-charge-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        // Abrir modal automáticamente si se solicita desde el listado
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('terminate')) {
                document.getElementById('terminateModal').style.display = 'flex';
            }
        });
    </script>
    @include('leases.partials.docs_modal')
@endsection
