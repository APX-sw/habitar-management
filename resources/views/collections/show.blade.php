@extends('layouts.app')

@section('title', '| Gestionar Cobro')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('collections.show_period', ['month' => $collection->month, 'year' => $collection->year]) }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver al periodo</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Gestionar Cobro</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">
            {{ $collection->lease->property->location }} • {{ \Carbon\Carbon::createFromDate(null, $collection->month, 1)->translatedFormat('F') }} {{ $collection->year }}
        </p>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
        @if($collection->status === 'incompleto' || $collection->status === 'ready' || $collection->status === 'draft' || $collection->status === 'partial')
            <button onclick="document.getElementById('extraChargeModal').style.display='flex'" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 1.5rem; border: 1px solid #d2d6dc; gap: 0.5rem;">➕ Añadir Cargo Extra</button>
        @endif
        @if($collection->status === 'ready' || $collection->status === 'sent' || $collection->status === 'partial')
            <button onclick="document.getElementById('paymentModal').style.display='flex'" class="btn" style="background: #48BB78; color: white; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 1.5rem; border: 1px solid #38a169;">Registrar Pago</button>
        @endif
        @if($collection->status !== 'paid')
            @if($collection->status === 'draft' || $collection->status === 'incompleto')
                <button type="button" class="btn" style="background: #e2e8f0; color: #a0aec0; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 1.5rem; border: 1px solid #cbd5e0; cursor: not-allowed;" title="Debes guardar y marcar como listo para cobrar antes de enviar" disabled>📧 Enviar al Inquilino</button>
            @else
                <form action="{{ route('collections.send', $collection) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn" style="background: #4299E1; color: white; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 1.5rem; border: 1px solid #3182ce;">📧 {{ $collection->status === 'sent' ? 'Re-enviar Mail' : 'Enviar al Inquilino' }}</button>
                </form>
            @endif
        @endif
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem;">
    <!-- Column 1: Details and Edits -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        @php
            $isUpdateMonth = $collection->lease->isUpdateMonthForDate($collection->month, $collection->year);
        @endphp
        
        @if($isUpdateMonth)
            <div style="background: #EBF8FF; border-left: 4px solid #3182CE; color: #2B6CB0; padding: 1rem 1.5rem; border-radius: 8px; display: flex; align-items: center; gap: 1rem; font-size: 0.95rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-size: 1.4rem;">🔄</span>
                <div>
                    <strong style="display: block; margin-bottom: 0.2rem; color: #2B6CB0; font-weight: 800;">Mes de Actualización de Alquiler</strong>
                    Este periodo corresponde a la indexación del precio del alquiler (cada {{ $collection->lease->update_frequency_months }} meses) por el índice <strong>{{ $collection->lease->indexType->name ?? 'Fijo' }}</strong>.
                </div>
            </div>
        @endif

        <div class="card" style="padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid #edf2f7; padding-bottom: 1rem;">
                <h3 style="margin: 0; color: var(--primary-color);">Desglose del Cobro</h3>
                @php
                    $badgeBg = '#EDF2F7'; $badgeColor = '#4A5568'; $label = 'INCOMPLETO';
                    if($collection->status === 'paid') { $badgeBg = '#C6F6D5'; $badgeColor = '#22543D'; $label = 'COBRADO'; }
                    elseif($collection->status === 'partial') { $badgeBg = '#E9D8FD'; $badgeColor = '#553C9A'; $label = 'PAGO PARCIAL'; }
                    elseif($collection->status === 'ready') { $badgeBg = '#FEFCBF'; $badgeColor = '#744210'; $label = 'LISTO PARA COBRAR'; }
                    elseif($collection->status === 'sent') { $badgeBg = '#EBF8FF'; $badgeColor = '#2B6CB0'; $label = 'LISTO PARA COBRAR - MAIL ENVIADO'; }
                @endphp
                <span class="badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                    {{ $label }}
                </span>
            </div>

            <form action="{{ route('collections.update', $collection) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($collection->details as $detail)
                        <div style="display: grid; grid-template-columns: 1fr 180px; gap: 1.5rem; align-items: center; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid #edf2f7;">
                            <div>
                                <div style="font-weight: 700; color: var(--primary-color); font-size: 1rem;">{{ $detail->name }}</div>
                                <div style="font-size: 0.7rem; color: #718096; text-transform: uppercase; font-weight: 700; margin-top: 0.2rem;">
                                    {{ $detail->type === 'rent' ? 'Alquiler' : ($detail->type === 'extra_charge' ? 'Cargo Extra' : 'Concepto Mensual Variable') }}
                                </div>
                            </div>
                            <div>
                                @if($collection->status !== 'paid')
                                    <div style="display: flex; align-items: center; gap: 0.5rem; background: white; padding: 0.3rem 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                                        <span style="font-weight: 800; color: #a0aec0;">$</span>
                                        <input type="number" step="0.01" name="details[{{ $detail->id }}]" value="{{ $detail->amount }}" style="width: 100%; border: none; outline: none; font-weight: 800; color: var(--accent-color); font-size: 1.1rem;">
                                    </div>
                                    @if($detail->original_amount !== null && $detail->original_amount != $detail->amount)
                                        <div style="font-size: 0.65rem; color: #a0aec0; text-align: right; margin-top: 0.2rem; font-style: italic;">
                                            Original: ${{ number_format($detail->original_amount, 2) }}
                                        </div>
                                    @endif
                                @else
                                    <div style="text-align: right; font-weight: 800; font-size: 1.2rem; color: var(--primary-color);">
                                        ${{ number_format($detail->amount, 2) }}
                                    </div>
                                    @if($detail->original_amount !== null && $detail->original_amount != $detail->amount)
                                        <div style="font-size: 0.65rem; color: #a0aec0; text-align: right; margin-top: 0.2rem; font-style: italic;">
                                            Original: ${{ number_format($detail->original_amount, 2) }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($collection->status !== 'paid')
                    <div style="margin-top: 2rem; text-align: right;">
                        <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem;">Guardar y Marcar como Listo</button>
                    </div>
                @endif
            </form>
        </div>

        <div class="card" style="padding: 2rem; background: var(--primary-color); color: white;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0; font-size: 0.8rem; text-transform: uppercase; opacity: 0.8;">Resumen de Saldos</h4>
                    <div style="display: flex; gap: 2rem; margin-top: 0.5rem;">
                        <div>
                            <div style="font-size: 0.7rem; opacity: 0.7;">TOTAL</div>
                            <div style="font-size: 1.5rem; font-weight: 800;">${{ number_format($collection->total_amount, 2) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; opacity: 0.7;">PAGADO</div>
                            <div style="font-size: 1.5rem; font-weight: 800; color: #68D391;">${{ number_format($collection->total_paid, 2) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; opacity: 0.7;">SALDO</div>
                            <div style="font-size: 1.5rem; font-weight: 800; color: #F6AD55;">${{ number_format($collection->balance, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 0.9rem; opacity: 0.9;">Inquilino: {{ $collection->lease->tenant->name }}</p>
                    <p style="font-size: 0.9rem; opacity: 0.9;">Vencimiento: 10/{{ str_pad($collection->month, 2, '0', STR_PAD_LEFT) }}/{{ $collection->year }}</p>
                </div>
            </div>
        </div>

        @if($collection->payments->count() > 0)
            <div class="card" style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0; color: var(--primary-color); font-weight: 800;">Historial de Pagos</h4>
                    <span style="background: #edf2f7; color: #4a5568; padding: 0.3rem 0.7rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700;">{{ $collection->payments->count() }} pagos</span>
                </div>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #edf2f7; text-align: left;">
                                <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Fecha</th>
                                <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Cuenta / Método</th>
                                <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; text-align: right;">Monto</th>
                                <th style="padding: 0.75rem 0.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collection->payments as $payment)
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 1rem 0.5rem; font-weight: 600; font-size: 0.85rem; color: #4a5568;">
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 1rem 0.5rem;">
                                        <div style="font-weight: 700; color: var(--primary-color); font-size: 0.85rem;">{{ $payment->account->name ?? 'N/A' }}</div>
                                        @if($payment->notes)
                                            <div style="font-size: 0.75rem; color: #a0aec0; font-style: italic;">{{ Str::limit($payment->notes, 30) }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem 0.5rem; text-align: right; font-weight: 800; color: #48BB78; font-size: 1rem;">
                                        ${{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td style="padding: 1rem 0.5rem; text-align: right;">
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                            @if(!$payment->transferred_to_owner)
                                                <form action="{{ route('collections.transfer_payment', [$collection, $payment]) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Confirmas que este monto ya fue transferido al propietario directamente? Esto generará una salida en caja automáticamente.')">
                                                    @csrf
                                                    <button type="submit" class="btn" style="background: #FFF5F5; color: #C53030; padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 800; border: 1px solid #FEB2B2; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.3rem;" title="Marcar como pago directo al propietario">
                                                        💸 PAGO DIRECTO
                                                    </button>
                                                </form>
                                            @else
                                                <span style="background: #E6FFFA; color: #319795; padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 800; border: 1px solid #B2F5EA; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.3rem;" title="Este pago ya fue transferido al propietario">
                                                    ✅ TRANSFERIDO
                                                </span>
                                            @endif
                                            <a href="{{ route('collections.payment_receipt', [$collection, $payment]) }}" class="btn" style="background: #EDF2F7; color: var(--primary-color); padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 800; border: 1px solid #CBD5E0; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                RECIBO
                                            </a>
                                            <form action="{{ route('collections.send_receipt', [$collection, $payment]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn" style="background: var(--accent-gradient); color: white; padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 800; border: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.3rem; box-shadow: 0 2px 4px rgba(56, 178, 172, 0.2);">
                                                    📧 ENVIAR
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Column 2: Contract Info & Extras -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div class="card" style="padding: 1.5rem;">
            <h4 style="margin: 0 0 1.2rem; color: var(--primary-color); font-size: 0.9rem; text-transform: uppercase;">Información del Inquilino</h4>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #edf2f7; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color);">
                    {{ substr($collection->lease->tenant->name, 0, 1) }}
                </div>
                <div>
                    <div style="font-weight: 700;">{{ $collection->lease->tenant->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-light);">{{ $collection->lease->tenant->email }}</div>
                </div>
            </div>
            <div style="margin-top: 1.5rem; font-size: 0.9rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: var(--text-light);">DNI/CUIT:</span>
                    <span style="font-weight: 600;">{{ $collection->lease->tenant->dni_cuit }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-light);">Teléfono:</span>
                    <span style="font-weight: 600;">{{ $collection->lease->tenant->phone }}</span>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <h4 style="margin: 0 0 1.2rem; color: var(--primary-color); font-size: 0.9rem; text-transform: uppercase;">Información del Propietario</h4>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #edf2f7; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color);">
                    {{ substr($collection->lease->property->owner->name ?? 'P', 0, 1) }}
                </div>
                <div>
                    <div style="font-weight: 700;">{{ $collection->lease->property->owner->name ?? 'N/A' }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-light);">Propietario del inmueble</div>
                </div>
            </div>
            @if($collection->lease->property->owner)
                <div style="margin-top: 1.5rem; font-size: 0.9rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: var(--text-light);">Teléfono:</span>
                        <span style="font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem;">
                            {{ $collection->lease->property->owner->phone ?? 'N/A' }}
                            @if($collection->lease->property->owner->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $collection->lease->property->owner->phone) }}" target="_blank" style="text-decoration: none; font-size: 0.95rem;" title="Enviar WhatsApp">
                                    💬
                                </a>
                            @endif
                        </span>
                    </div>
                    @if($collection->lease->property->owner->email)
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-light);">Email:</span>
                            <span style="font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 170px;" title="{{ $collection->lease->property->owner->email }}">
                                {{ $collection->lease->property->owner->email }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="card" style="padding: 1.5rem;">
            <h4 style="margin: 0 0 1.2rem; color: var(--primary-color); font-size: 0.9rem; text-transform: uppercase;">Cargos Extras de este mes</h4>
            @php
                $extras = $collection->details->where('type', 'extra_charge');
            @endphp
            @forelse($extras as $extra)
                <div style="display: flex; justify-content: space-between; padding: 0.8rem 0; border-bottom: 1px solid #edf2f7;">
                    <span style="font-weight: 500;">{{ $extra->name }}</span>
                    <span style="font-weight: 700; color: var(--accent-color);">${{ number_format($extra->amount, 2) }}</span>
                </div>
            @empty
                <p style="font-size: 0.85rem; color: var(--text-light);">No hay cargos extras específicos para este mes.</p>
            @endforelse
            
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px dashed #cbd5e0;">
                <p style="font-size: 0.75rem; color: var(--text-light); font-style: italic;">Para añadir nuevos cargos permanentes, ve a la <a href="{{ route('leases.show', $collection->lease) }}" style="color: var(--accent-color); font-weight: 700;">Ficha del Contrato</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Modal para Cargo Extra -->
<div id="extraChargeModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2.5rem; position: relative;">
        <button onclick="document.getElementById('extraChargeModal').style.display='none'" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; cursor: pointer; color: var(--text-light);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Añadir Cargo Extra</h3>
        <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 2rem;">Crea un cargo para este mes. Si tiene cuotas, se generarán automáticamente para los meses siguientes.</p>

        <form action="{{ route('extra-charges.store') }}" method="POST">
            @csrf
            <input type="hidden" name="lease_id" value="{{ $collection->lease_id }}">
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Descripción</label>
                <input type="text" name="description" required placeholder="Ej: Arreglo de mesa, Multa expensas..." style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Monto Total</label>
                    <input type="number" step="0.01" name="amount" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Cuotas</label>
                    <input type="number" name="total_installments" value="1" min="1" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Categoría</label>
                <select name="transaction_category_id" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $cat->id == 4 ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Mes de Inicio</label>
                <input type="date" name="billing_date" value="{{ $collection->year }}-{{ str_pad($collection->month, 2, '0', STR_PAD_LEFT) }}-01" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('extraChargeModal').style.display='none'" class="btn" style="background: #f1f5f9; color: #475569;">Cancelar</button>
                <button type="submit" class="btn btn-primary">Generar Cargos</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Registrar Pago -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 850px; padding: 2.5rem; position: relative; max-height: 90vh; overflow-y: auto;">
        <button onclick="document.getElementById('paymentModal').style.display='none'" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; cursor: pointer; color: var(--text-light);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <h3 style="margin-bottom: 0.5rem; color: var(--primary-color);">Registrar Pagos</h3>
        <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 2rem;">Puedes desglosar el pago en múltiples métodos si es necesario.</p>

        <form action="{{ route('collections.pay', $collection) }}" method="POST">
            @csrf
            
            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.8rem; color: var(--text-light);">Saldo Pendiente Actual:</span>
                    <div style="font-weight: 800; color: #F6AD55; font-size: 1.5rem;">${{ number_format($collection->balance, 2) }}</div>
                    <input type="hidden" id="currentBalance" value="{{ $collection->balance }}">
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 0.8rem; color: var(--text-light);">Nuevo Saldo Restante:</span>
                    <div id="remainingBalanceDisplay" style="font-weight: 800; color: var(--primary-color); font-size: 1.5rem;">${{ number_format($collection->balance, 2) }}</div>
                </div>
            </div>

            <div id="paymentRows">
                <!-- Fila de pago inicial -->
                <div class="payment-row" style="display: grid; grid-template-columns: 1.5fr 1.5fr 1.5fr 40px; gap: 1rem; margin-bottom: 1rem; align-items: flex-end; padding-bottom: 1rem; border-bottom: 1px dashed #edf2f7;">
                    <div>
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Monto</label>
                        <input type="number" step="0.01" name="payments[0][amount]" class="payment-amount" value="{{ $collection->balance }}" oninput="updateRemainingBalance()" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 700;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Cuenta de Ingreso</label>
                        <select name="payments[0][account_id]" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 600;">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Fecha</label>
                        <input type="date" name="payments[0][payment_date]" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    </div>
                    <div>
                        <!-- No delete for first row -->
                    </div>
                    <div style="grid-column: span 4; margin-top: 0.5rem; display: flex; gap: 1rem; align-items: center;">
                        <input type="text" name="payments[0][notes]" placeholder="Notas adicionales..." style="flex: 1; padding: 0.5rem; border-radius: 6px; border: 1px solid #edf2f7; font-size: 0.8rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; font-weight: 700; color: #E53E3E; cursor: pointer; background: #FFF5F5; padding: 0.5rem 0.8rem; border-radius: 6px; border: 1px solid #FEB2B2; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                            <input type="checkbox" name="payments[0][transferred_to_owner]" value="1" style="width: 16px; height: 16px; accent-color: #E53E3E;">
                            Transferido directo al propietario
                        </label>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addPaymentRow()" class="btn" style="background: #edf2f7; color: var(--primary-color); font-weight: 700; font-size: 0.8rem; margin-bottom: 2rem; width: 100%;">➕ Agregar otro método / pago</button>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('paymentModal').style.display='none'" class="btn" style="background: white; border: 1px solid #d2d6dc; color: #475569;">Cancelar</button>
                <button type="submit" class="btn" style="background: #48BB78; color: white; font-weight: 700; padding: 1rem 2.5rem;">Registrar Todos los Pagos</button>
            </div>
        </form>
    </div>
</div>

<script>
    let paymentRowCount = 1;
    const paymentMethodsOptions = `@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach`;

    function addPaymentRow() {
        const container = document.getElementById('paymentRows');
        const newRow = document.createElement('div');
        newRow.className = 'payment-row';
        newRow.style = 'display: grid; grid-template-columns: 1.5fr 1.5fr 1.5fr 40px; gap: 1rem; margin-bottom: 1rem; align-items: flex-end; padding-bottom: 1rem; border-bottom: 1px dashed #edf2f7;';
        
        newRow.innerHTML = `
            <div>
                <input type="number" step="0.01" name="payments[${paymentRowCount}][amount]" class="payment-amount" value="0" oninput="updateRemainingBalance()" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 700;">
            </div>
            <div>
                <select name="payments[${paymentRowCount}][account_id]" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc; font-weight: 600;">
                    ${paymentMethodsOptions}
                </select>
            </div>
            <div>
                <input type="date" name="payments[${paymentRowCount}][payment_date]" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>
            <div style="text-align: center;">
                <button type="button" onclick="this.closest('.payment-row').remove(); updateRemainingBalance();" style="background: #FED7D7; color: #C53030; border: none; border-radius: 6px; padding: 0.5rem; cursor: pointer;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
            <div style="grid-column: span 4; margin-top: 0.5rem; display: flex; gap: 1rem; align-items: center;">
                <input type="text" name="payments[${paymentRowCount}][notes]" placeholder="Notas adicionales..." style="flex: 1; padding: 0.5rem; border-radius: 6px; border: 1px solid #edf2f7; font-size: 0.8rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; font-weight: 700; color: #E53E3E; cursor: pointer; background: #FFF5F5; padding: 0.5rem 0.8rem; border-radius: 6px; border: 1px solid #FEB2B2; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                    <input type="checkbox" name="payments[${paymentRowCount}][transferred_to_owner]" value="1" style="width: 16px; height: 16px; accent-color: #E53E3E;">
                    Transferido directo al propietario
                </label>
            </div>
        `;
        container.appendChild(newRow);
        paymentRowCount++;
        updateRemainingBalance();
    }

    function updateRemainingBalance() {
        const balance = parseFloat(document.getElementById('currentBalance').value);
        let paid = 0;
        document.querySelectorAll('.payment-amount').forEach(input => {
            paid += parseFloat(input.value || 0);
        });
        
        const remaining = balance - paid;
        const display = document.getElementById('remainingBalanceDisplay');
        display.innerText = '$' + remaining.toLocaleString('es-AR', { minimumFractionDigits: 2 });
        
        if (remaining < 0) {
            display.style.color = '#C53030';
        } else if (remaining === 0) {
            display.style.color = '#48BB78';
        } else {
            display.style.color = 'var(--primary-color)';
        }
    }
</script>
@endsection
