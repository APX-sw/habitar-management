@extends('layouts.app')

@section('title', '| Detalle de Rendición')

@section('content')
<div class="no-print" style="max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <a href="{{ route('settlements.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Volver
        </a>
        <h1 style="color: var(--primary-color); margin: 0; font-size: 1.8rem; font-weight: 800;">Rendición #{{ $settlement->id }}</h1>
    </div>
    <div style="display: flex; gap: 0.8rem;">
        <button onclick="window.print()" class="btn" style="background: white; border: 1px solid #cbd5e0; color: #4a5568; display: flex; align-items: center; gap: 0.6rem; font-weight: 600;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Imprimir
        </button>
        
        <form action="{{ route('settlements.send_to_owner', $settlement) }}" method="POST" style="display: inline;">
            @csrf
            <input type="hidden" name="type" value="settlement">
            <button type="submit" class="btn" style="background: #3182ce; color: white; display: flex; align-items: center; gap: 0.6rem; font-weight: 600; box-shadow: 0 4px 6px rgba(49, 130, 206, 0.2);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                Enviar Rendición
            </button>
        </form>

        @if($settlement->status === 'paid')
            <form action="{{ route('settlements.send_to_owner', $settlement) }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="type" value="payment_confirmation">
                <button type="submit" class="btn" style="background: #38a169; color: white; display: flex; align-items: center; gap: 0.6rem; font-weight: 600; box-shadow: 0 4px 6px rgba(56, 161, 105, 0.2);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Enviar Confirmación Pago
                </button>
            </form>
        @elseif($settlement->status === 'carried_over')
            <span class="btn" style="background: #edf2f7; color: #718096; display: flex; align-items: center; gap: 0.6rem; font-weight: 700; border: 1px dashed #cbd5e0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>
                ARRASTRADA AL PRÓXIMO MES
            </span>
        @else
            @if($settlement->net_amount < 0)
                <form action="{{ route('settlements.carry_over', $settlement) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de arrastrar este saldo deudor a la liquidación del próximo mes?');">
                    @csrf
                    <button type="submit" class="btn" style="background: white; border: 1px solid #cbd5e0; color: #4a5568; display: flex; align-items: center; gap: 0.6rem; font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>
                        Arrastrar a Próximo Mes
                    </button>
                </form>
            @endif
            <button onclick="document.getElementById('paymentModal').style.display='flex'" class="btn" style="background: {{ $settlement->net_amount < 0 ? '#3182ce' : '#e53e3e' }}; color: white; display: flex; align-items: center; gap: 0.6rem; font-weight: 700; box-shadow: 0 4px 6px rgba({{ $settlement->net_amount < 0 ? '49, 130, 206' : '229, 62, 62' }}, 0.2);">
                {{ $settlement->net_amount < 0 ? 'COBRAR AHORA' : 'PAGAR AHORA' }}
            </button>
        @endif
    </div>
</div>

<div id="printable-area" style="max-width: 1000px; margin: 0 auto;">
    <div class="card" style="padding: 3rem; position: relative; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white;">
        <!-- Watermark/Background decoration (optional) -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: var(--accent-gradient);"></div>

        <!-- Document Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 3.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 2.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <img src="{{ asset('img/logo.png') }}" alt="Habitar Logo" style="height: 45px; object-fit: contain;">
                </div>
                <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.4; margin: 0;">
                    Gestión Integral de Propiedades<br>
                    {{ \App\Models\AgencySetting::get('agency_address', 'Av. Belgrano (N) 450, Santiago del Estero') }}<br>
                    <strong>Liquidación de Cuentas</strong>
                </p>
            </div>
            <div style="text-align: right;">
                <h2 style="color: var(--primary-color); font-size: 2rem; font-weight: 900; margin: 0 0 0.5rem 0; letter-spacing: -0.01em;">RENDICIÓN MENSUAL</h2>
                <div style="font-size: 1.4rem; font-weight: 700; color: var(--accent-color); margin-bottom: 0.5rem;">
                    Periodo: {{ \Carbon\Carbon::createFromDate($settlement->year, $settlement->month, 1)->translatedFormat('F Y') }}
                </div>
                <p style="color: var(--text-light); font-size: 0.9rem; font-weight: 600;">Emitido el: {{ now()->format('d/m/Y H:i') }}hs</p>
            </div>
        </div>

        <!-- Participants Info -->
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 4rem; margin-bottom: 4rem;">
            <div>
                <h4 style="text-transform: uppercase; font-size: 0.8rem; font-weight: 800; color: var(--text-light); margin-bottom: 1rem; letter-spacing: 0.05em;">Destinatario / Propietario</h4>
                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #edf2f7;">
                    <p style="font-size: 1.4rem; font-weight: 800; color: var(--primary-color); margin: 0 0 0.5rem 0;">{{ $settlement->owner->name }}</p>
                    <p style="color: var(--text-main); font-weight: 600; margin: 0;">CUIT/DNI: {{ $settlement->owner->dni_cuit }}</p>
                    <p style="color: var(--text-light); font-size: 0.9rem; margin: 0.25rem 0 0 0;">{{ $settlement->owner->email }}</p>
                </div>
            </div>
            <div style="text-align: right;">
                <h4 style="text-transform: uppercase; font-size: 0.8rem; font-weight: 800; color: var(--text-light); margin-bottom: 1rem; letter-spacing: 0.05em;">Estado de Liquidación</h4>
                <div style="padding: 1rem 0;">
                    @if($settlement->status === 'paid')
                        <div style="background: #C6F6D5; color: #22543D; padding: 0.75rem 1.5rem; border-radius: 50px; font-weight: 800; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1rem; border: 2px solid #9AE6B4;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            PAGADA
                        </div>
                    @elseif($settlement->status === 'carried_over')
                        <div style="background: #E2E8F0; color: #4A5568; padding: 0.75rem 1.5rem; border-radius: 50px; font-weight: 800; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1rem; border: 2px dashed #CBD5E0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>
                            ARRASTRADA
                        </div>
                    @else
                        <div style="background: #FEFCBF; color: #744210; padding: 0.75rem 1.5rem; border-radius: 50px; font-weight: 800; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1rem; border: 2px solid #F6E05E;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            PENDIENTE
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- BREAKDOWN GROUPED BY PROPERTY -->
        @php
            $propertiesWithMovements = [];
            foreach($collections as $col) {
                $pid = $col->lease->property_id;
                if (!isset($propertiesWithMovements[$pid])) {
                    $propertiesWithMovements[$pid] = [
                        'property' => $col->lease->property,
                        'collections' => [],
                        'expenses' => [],
                        'income_total' => 0,
                        'expenses_total' => 0
                    ];
                }
                $propertiesWithMovements[$pid]['collections'][] = $col;
                $propertiesWithMovements[$pid]['income_total'] += $col->total_amount;
                
                // Sumamos los conceptos destinados a la inmobiliaria a los egresos de la propiedad para compensar visualmente
                $agencySum = $col->details->where('destination', 'agency')->sum('amount');
                $propertiesWithMovements[$pid]['expenses_total'] += $agencySum;
            }
            foreach($expenses as $exp) {
                $pid = $exp->property_id;
                if (!isset($propertiesWithMovements[$pid])) {
                    $propertiesWithMovements[$pid] = [
                        'property' => $exp->property,
                        'collections' => [],
                        'expenses' => [],
                        'income_total' => 0,
                        'expenses_total' => 0
                    ];
                }
                $propertiesWithMovements[$pid]['expenses'][] = $exp;
                $propertiesWithMovements[$pid]['expenses_total'] += $exp->amount;
            }
        @endphp

        <div style="margin-bottom: 3rem;">
            <h3 style="color: var(--primary-color); font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 2px solid #edf2f7; padding-bottom: 0.5rem;">
                Detalle por Propiedad
            </h3>

            @foreach($propertiesWithMovements as $data)
                <div style="margin-bottom: 2rem; border: 1px solid #edf2f7; border-radius: 12px; overflow: hidden; break-inside: avoid; page-break-inside: avoid;">
                    <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-weight: 800; color: var(--primary-color); font-size: 1.1rem;">
                            {{ $data['property']->location }}
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-light); font-weight: 600;">
                            {{ $data['property']->city->name ?? 'N/A' }}
                        </div>
                    </div>
                    
                    <div style="padding: 0;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                            <thead>
                                <tr style="text-align: left; color: var(--text-light); border-bottom: 1px solid #f1f5f9;">
                                    <th style="padding: 0.75rem 1.5rem;">Concepto</th>
                                    <th style="padding: 0.75rem 1.5rem; text-align: right;">Ingreso</th>
                                    <th style="padding: 0.75rem 1.5rem; text-align: right;">Egreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['collections'] as $col)
                                    {{-- Conceptos que van al propietario --}}
                                    @foreach($col->details->where('destination', 'owner') as $detail)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 0.75rem 1.5rem;">
                                                <strong>{{ $detail->name }}</strong> (Inq: {{ $col->lease->tenant->name }})
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right; color: #38a169; font-weight: 700;">
                                                ${{ number_format($detail->amount, 2) }}
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right;">-</td>
                                        </tr>
                                    @endforeach

                                    {{-- Conceptos retenidos por la Inmobiliaria (Informativos y compensatorios) --}}
                                    @foreach($col->details->where('destination', 'agency') as $detail)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 0.75rem 1.5rem;">
                                                <strong>{{ $detail->name }}</strong> (Inq: {{ $col->lease->tenant->name }}) <span style="font-size: 0.75rem; color: #718096; font-weight: 600; background: #edf2f7; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.5rem;">Cobrado Inq.</span>
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right; color: #38a169; font-weight: 700;">
                                                ${{ number_format($detail->amount, 2) }}
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right;">-</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #f1f5f9; background: #faf5ff;">
                                            <td style="padding: 0.75rem 1.5rem; color: #6b46c1; font-style: italic;">
                                                ↳ Retención: {{ $detail->name }} (Pago a cargo de Inmobiliaria)
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right;">-</td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right; color: #e53e3e; font-weight: 700;">
                                                ${{ number_format($detail->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach

                                @foreach($data['expenses'] as $exp)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 0.75rem 1.5rem;">{{ $exp->description }}</td>
                                        <td style="padding: 0.75rem 1.5rem; text-align: right;">-</td>
                                        <td style="padding: 0.75rem 1.5rem; text-align: right; color: #e53e3e; font-weight: 700;">
                                            ${{ number_format($exp->amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="background: rgba(248, 250, 252, 0.5); font-weight: 800; border-top: 2px solid #edf2f7;">
                                    <td style="padding: 1rem 1.5rem; text-align: right; color: var(--text-light);">SUBTOTAL {{ $data['property']->location }}:</td>
                                    <td colspan="2" style="padding: 1rem 1.5rem; text-align: right; font-size: 1.1rem; color: var(--primary-color);">
                                        ${{ number_format($data['income_total'] - $data['expenses_total'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- GLOBAL DEDUCTIONS AND SUMMARY -->
        <div style="margin-bottom: 2rem; border-top: 2px solid #edf2f7; padding-top: 1.5rem; break-inside: avoid;">
            <div style="display: flex; justify-content: flex-end;">
                <div style="width: 400px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1.1rem;">
                        <span style="color: var(--text-light); font-weight: 600;">Total Ingresos:</span>
                        <span style="font-weight: 700; color: #38a169;">${{ number_format($settlement->total_income, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1.1rem;">
                        <span style="color: var(--text-light); font-weight: 600;">Total Gastos:</span>
                        <span style="font-weight: 700; color: #e53e3e;">- ${{ number_format($settlement->total_expense, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.1rem; padding-bottom: 1.5rem; border-bottom: 1px solid #edf2f7;">
                        <div style="display: flex; flex-direction: column;">
                            <span style="color: var(--text-light); font-weight: 600;">Honorarios Inmobiliaria:</span>
                            <span style="font-size: 0.75rem; color: var(--text-light); font-style: italic;">(Comisión Inmobiliaria s/Alquiler)</span>
                        </div>
                        <span style="font-weight: 700; color: #e53e3e;">- ${{ number_format($settlement->agency_commission, 2) }}</span>
                    </div>

                    @if($settlement->extraFees->count() > 0)
                        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid #edf2f7; padding-bottom: 1.5rem;">
                            <h4 style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 0.5rem 0; font-weight: 700;">Honorarios Extra / Descuentos Adicionales</h4>
                            @foreach($settlement->extraFees as $ef)
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-size: 0.9rem; color: var(--text-light);">{{ $ef->description }}</span>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-weight: 700; color: #e53e3e;">- ${{ number_format($ef->amount, 2) }}</span>
                                        @if(!in_array($settlement->status, ['paid', 'carried_over']))
                                            <form action="{{ route('settlements.extra-fees.remove', [$settlement, $ef]) }}" method="POST" style="margin:0;">
                                                @csrf @method('DELETE')
                                                <button type="submit" style="background: none; border: none; color: #e53e3e; cursor: pointer; padding: 0; font-size: 1.2rem; line-height: 1;">&times;</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @php
                        // Calcular si hay diferencia por pagos directos transferidos
                        $extraFeesTotal = $settlement->extraFees ? $settlement->extraFees->sum('amount') : 0;
                        $directDiff = $settlement->total_income - $settlement->total_expense - $settlement->agency_commission - $extraFeesTotal - $settlement->net_amount;
                    @endphp
                    @if($directDiff > 0.01)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.1rem; padding-bottom: 1.5rem; border-bottom: 1px dashed #edf2f7;">
                        <div style="display: flex; flex-direction: column;">
                            <span style="color: var(--text-light); font-weight: 600;">Ya transferido directo:</span>
                            <span style="font-size: 0.75rem; color: var(--text-light); font-style: italic;">(Fondos que el inquilino transfirió al propietario)</span>
                        </div>
                        <span style="font-weight: 700; color: #F6AD55;">- ${{ number_format($directDiff, 2) }}</span>
                    </div>
                    @endif
                    
                    <div style="background: var(--primary-color); color: white; padding: 1.5rem; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(26, 32, 44, 0.15);">
                        <span style="font-weight: 700; font-size: 1.2rem;">NETO FINAL:</span>
                        <span style="font-weight: 900; font-size: 2rem;">${{ number_format($settlement->net_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- INVOICING INFO -->
        @if(isset($invoicingData))
            <div style="margin-bottom: 2rem; background: #EBF8FF; border: 1px solid #BEE3F8; border-radius: 12px; padding: 1.5rem; break-inside: avoid;">
                <h3 style="color: #2B6CB0; font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem; text-transform: uppercase;">Detalle para Facturación Oficial (Responsabilidad Propietario)</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="text-align: left; color: #2C5282; border-bottom: 2px solid #BEE3F8;">
                            <th style="padding: 0.5rem;">Propiedad</th>
                            <th style="padding: 0.5rem; text-align: right;">Alquiler Vigente</th>
                            <th style="padding: 0.5rem; text-align: right;">% a Facturar</th>
                            <th style="padding: 0.5rem; text-align: right;">Monto a Facturar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoicingData['items'] as $item)
                            <tr style="border-bottom: 1px solid #BEE3F8;">
                                <td style="padding: 0.5rem; font-weight: 600; color: #2D3748;">{{ $item['property'] }}</td>
                                <td style="padding: 0.5rem; text-align: right; color: #4A5568;">${{ number_format($item['rent'], 2) }}</td>
                                <td style="padding: 0.5rem; text-align: right; color: #4A5568;">{{ $item['percentage'] }}%</td>
                                <td style="padding: 0.5rem; text-align: right; font-weight: 700; color: #2B6CB0;">${{ number_format($item['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="padding: 1rem 0.5rem 0.5rem; text-align: right; font-weight: 800; color: #2B6CB0;">TOTAL A FACTURAR:</td>
                            <td style="padding: 1rem 0.5rem 0.5rem; text-align: right; font-weight: 900; font-size: 1.2rem; color: #2B6CB0;">${{ number_format($invoicingData['total'], 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding: 0 0.5rem 0.5rem; text-align: right; font-weight: 600; color: #4A5568;">IVA Estimado (21%):</td>
                            <td style="padding: 0 0.5rem 0.5rem; text-align: right; font-weight: 700; color: #4A5568;">${{ number_format($invoicingData['iva_21'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <!-- PAYMENT DETAILS (Only if paid) -->
        @if($settlement->status === 'paid')
            <div style="break-inside: avoid; margin-top: 1.5rem; border-top: 3px dashed #e2e8f0; padding-top: 2rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                    <div style="width: 42px; height: 42px; background: #C6F6D5; color: #22543D; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <h3 style="color: #22543D; font-size: 1.5rem; font-weight: 900; margin: 0;">COMPROBANTE DE TRANSFERENCIA</h3>
                        <p style="color: #2f855a; font-weight: 600; margin: 0;">Los fondos han sido liquidados exitosamente según el siguiente detalle:</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    @foreach($settlement->payments as $payment)
                        <div style="background: #ffffff; border: 2px solid #f1f5f9; border-radius: 16px; padding: 1.5rem; position: relative;">
                            <div style="position: absolute; top: 1.5rem; right: 1.5rem;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#edf2f7" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">Monto Transferido</div>
                            <div style="font-size: 2rem; font-weight: 900; color: var(--primary-color); margin-bottom: 1.25rem;">${{ number_format($payment->amount, 2) }}</div>
                            
                            <div style="display: grid; gap: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--text-light); font-size: 0.85rem;">Cuenta Destino:</span>
                                    <span style="font-weight: 700; color: var(--primary-color);">{{ $payment->ownerBankAccount->holder_name }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--text-light); font-size: 0.85rem;">CBU/Alias:</span>
                                    <span style="font-weight: 600; font-family: monospace;">{{ $payment->ownerBankAccount->cbu_alias }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--text-light); font-size: 0.85rem;">Fecha Operación:</span>
                                    <span style="font-weight: 700;">{{ \Carbon\Carbon::parse($payment->date)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Footer / Legal -->
        <div style="margin-top: 5rem; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 2rem; break-inside: avoid; page-break-inside: avoid;">
            <p style="color: var(--text-light); font-size: 0.8rem; margin: 0 0 1rem 0;">
                Documento generado automáticamente por el sistema Habitar Management.<br>
                Las liquidaciones de fondos están sujetas a la validación de la administración.
            </p>
            <div style="display: flex; justify-content: center; margin-top: 4rem; gap: 4rem;">
                <div style="border-top: 1px solid #cbd5e0; width: 200px; padding-top: 0.5rem; font-size: 0.75rem; color: #a0aec0; font-weight: 700; text-transform: uppercase;">Firma Habitar S.A.</div>
                <div style="border-top: 1px solid #cbd5e0; width: 200px; padding-top: 0.5rem; font-size: 0.75rem; color: #a0aec0; font-weight: 700; text-transform: uppercase;">Firma Propietario</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Pago (Solo si no está pagado o arrastrado) -->
@if(!in_array($settlement->status, ['paid', 'carried_over']))
<div id="paymentModal" class="no-print" style="display: none; position: fixed; inset: 0; background: rgba(26, 32, 44, 0.7); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="card" style="width: 100%; max-width: 1000px; padding: 2.5rem; position: relative; max-height: 90vh; overflow-y: auto;">
        <button onclick="document.getElementById('paymentModal').style.display='none'" style="position: absolute; top: 1.5rem; right: 1.5rem; background: #edf2f7; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: var(--primary-color); display: flex; align-items: center; justify-content: center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        @php
            $isNegative = $settlement->net_amount < 0;
            $remaining = abs($settlement->net_amount) - $settlement->payments->sum('amount');
        @endphp

        <h3 style="margin-bottom: 0.5rem; color: var(--primary-color); font-size: 1.6rem; font-weight: 800;">{{ $isNegative ? 'Registrar Cobro de Rendición' : 'Liquidar Rendición' }}</h3>
        <p style="font-size: 1rem; color: var(--text-light); margin-bottom: 2rem;">
            {{ $isNegative ? 'Registra el ingreso del dinero a nuestras cuentas bancarias/cajas.' : 'Asigna el pago a las cuentas bancarias del propietario. Puedes dividir el total en múltiples transferencias.' }}
        </p>

        <form action="{{ route('settlements.pay', $settlement) }}" method="POST">
            @csrf
            
            <div style="background: #f7fafc; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-light); text-transform: uppercase;">Saldo Pendiente</span>
                    <div style="font-weight: 900; color: #d69e2e; font-size: 2rem;">${{ number_format($remaining, 2) }}</div>
                    <input type="hidden" id="currentBalance" value="{{ $remaining }}">
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-light); text-transform: uppercase;">Monto Asignado Actual</span>
                    <div id="remainingBalanceDisplay" style="font-weight: 900; color: var(--primary-color); font-size: 2rem;">$0,00</div>
                </div>
            </div>

            <div id="paymentRowsContainer">
                <!-- Fila inicial -->
                <div class="payment-row-premium" style="display: grid; grid-template-columns: {{ $isNegative ? '1fr 2.5fr 1.1fr 45px' : '1fr 1.8fr 1.8fr 1.1fr 45px' }}; gap: 0.75rem; margin-bottom: 1rem; align-items: flex-end; padding-bottom: 1.5rem; border-bottom: 1px dashed #e2e8f0;">
                    <div>
                        <label class="label-tiny">Importe</label>
                        <input type="number" step="0.01" name="payments[0][amount]" class="payment-amount-field" value="{{ $remaining }}" oninput="recalcPaymentTotal()" required>
                    </div>
                    <div>
                        <label class="label-tiny">{{ $isNegative ? 'Ingresa a (Inmobiliaria)' : 'Sale de (Inmobiliaria)' }}</label>
                        <select name="payments[0][account_id]" required class="select-premium">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (${{ number_format($acc->current_balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    @if(!$isNegative)
                    <div>
                        <label class="label-tiny">Hacia (Propietario)</label>
                        <select name="payments[0][owner_bank_account_id]" required class="select-premium">
                            @foreach($settlement->owner->bankAccounts as $oba)
                                <option value="{{ $oba->id }}">{{ $oba->holder_name }} ({{ $oba->cbu_alias }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div>
                        <label class="label-tiny">Fecha Pago</label>
                        <input type="date" name="payments[0][date]" value="{{ date('Y-m-d') }}" required class="input-premium">
                    </div>
                    <div style="text-align: center;">
                        <button type="button" class="btn-remove-disabled" disabled>&times;</button>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addNewPaymentRow()" class="btn-add-row-premium">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Dividir Pago / Agregar Cuenta
            </button>

            <div style="display: flex; gap: 1.5rem; justify-content: flex-end; margin-top: 3rem; border-top: 1px solid #edf2f7; padding-top: 2rem;">
                <button type="button" onclick="document.getElementById('paymentModal').style.display='none'" class="btn-secondary-premium" style="padding: 1rem 2.5rem;">Cancelar</button>
                <button type="submit" class="btn-save-premium" style="padding: 1rem 3rem;">Confirmar y Generar Liquidación</button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
    .label-tiny { display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .payment-amount-field { width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #cbd5e0; font-weight: 800; font-size: 1.1rem; color: var(--primary-color); outline: none; }
    .payment-amount-field:focus { border-color: var(--accent-color); box-shadow: 0 0 0 3px rgba(56, 178, 172, 0.1); }
    .select-premium, .input-premium { width: 100%; padding: 0.8rem 0.4rem; border-radius: 10px; border: 1px solid #cbd5e0; font-size: 0.82rem; font-weight: 600; outline: none; }
    .select-premium:focus, .input-premium:focus { border-color: var(--accent-color); }
    
    .btn-add-row-premium { background: #ebf8ff; color: #2b6cb0; border: none; padding: 0.8rem 1.5rem; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; margin-top: 1rem; }
    .btn-add-row-premium:hover { background: #bee3f8; }
    
    .btn-remove-disabled { background: #f7fafc; color: #cbd5e0; border: none; width: 40px; height: 40px; border-radius: 8px; font-size: 1.5rem; cursor: not-allowed; }
    .btn-remove-active { background: #fff5f5; color: #e53e3e; border: none; width: 40px; height: 40px; border-radius: 8px; font-size: 1.5rem; cursor: pointer; transition: all 0.2s; }
    .btn-remove-active:hover { background: #feb2b2; transform: scale(1.05); }

    .btn-secondary-premium { background: white; border: 1px solid #e2e8f0; color: #718096; font-weight: 700; border-radius: 12px; cursor: pointer; }
    .btn-save-premium { background: var(--accent-gradient); border: none; color: white; font-weight: 800; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 12px rgba(49, 151, 149, 0.3); }

    @media print {
        @page { margin: 0; }
        body { background: white !important; margin: 0 !important; padding: 10mm !important; font-size: 11px !important; color: black !important; }
        .no-print { display: none !important; }
        .card { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; }
        #printable-area { width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .main-content { padding: 0 !important; margin: 0 !important; }
        .sidebar { display: none !important; }
        .app-container { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; }
        .top-bar { display: none !important; }
        nav { display: none !important; }

        /* Estilos de tabla y texto */
        table th, table td { padding: 0.3rem 0.5rem !important; font-size: 11px !important; }
        h2 { font-size: 18px !important; margin-bottom: 0 !important; }
        h3 { font-size: 14px !important; margin-bottom: 0.5rem !important; padding-bottom: 0.2rem !important; }
        p { margin-bottom: 0.2rem !important; font-size: 11px !important; }
        
        /* Compactar espacios y quitar fondos pesados en impresion */
        div[style*="margin-bottom: 4rem"] { margin-bottom: 1.5rem !important; gap: 1rem !important; }
        div[style*="margin-bottom: 3.5rem"] { margin-bottom: 1rem !important; padding-bottom: 0.5rem !important; }
        div[style*="margin-bottom: 3rem"] { margin-bottom: 1rem !important; }
        div[style*="margin-bottom: 2rem"] { margin-bottom: 0.5rem !important; }
        div[style*="margin-bottom: 1.5rem"] { margin-bottom: 0.5rem !important; }
        div[style*="padding: 3rem"] { padding: 0 !important; }
        div[style*="gap: 4rem"] { gap: 1rem !important; }
        div[style*="padding-bottom: 2.5rem"] { padding-bottom: 0.5rem !important; }
        div[style*="margin-top: 5rem"] { margin-top: 1.5rem !important; }
        div[style*="margin-top: 4rem"] { margin-top: 1rem !important; }
        
        /* Reducir paddings masivos */
        div[style*="padding: 1.5rem"] { padding: 0.5rem !important; }
        div[style*="padding: 1rem 1.5rem"] { padding: 0.5rem !important; }
        div[style*="padding-top: 1.5rem"] { padding-top: 0.5rem !important; }
        div[style*="padding-top: 2rem"] { padding-top: 0.5rem !important; }
        
        /* Ajustar la caja final (Neto Final) para que no sea un bloque oscuro gigante */
        div[style*="background: var(--primary-color)"] { background: white !important; color: black !important; box-shadow: none !important; border: 2px solid black !important; padding: 0.5rem 1rem !important; }
        div[style*="color: white"] { color: black !important; }
        span[style*="color: white"] { color: black !important; }
        
        /* Badges limpios */
        .badge { border: 1px solid #cbd5e0 !important; color: black !important; background: white !important; }
        div[style*="background: #f8fafc"] { background: white !important; border: 1px solid #cbd5e0 !important; padding: 0.5rem !important; }
        div[style*="background: #C6F6D5"] { background: white !important; border: 1px solid #cbd5e0 !important; color: black !important; padding: 0.2rem 0.5rem !important; }
        div[style*="background: #E2E8F0"] { background: white !important; border: 1px solid #cbd5e0 !important; color: black !important; padding: 0.2rem 0.5rem !important; }
        div[style*="background: #FEFCBF"] { background: white !important; border: 1px solid #cbd5e0 !important; color: black !important; padding: 0.2rem 0.5rem !important; }
    }
</style>

<script>
    let pCount = 1;
    const accOpts = `@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }} (${{ number_format($acc->current_balance, 2) }})</option>@endforeach`;
    const ownAccOpts = `@foreach($settlement->owner->bankAccounts as $oba)<option value="{{ $oba->id }}">{{ $oba->holder_name }} ({{ $oba->cbu_alias }})</option>@endforeach`;

    function addNewPaymentRow() {
        const container = document.getElementById('paymentRowsContainer');
        const row = document.createElement('div');
        row.className = 'payment-row-premium';
        row.style = 'display: grid; grid-template-columns: {{ $settlement->net_amount < 0 ? "1fr 2.5fr 1.1fr 45px" : "1fr 1.8fr 1.8fr 1.1fr 45px" }}; gap: 0.75rem; margin-bottom: 1rem; align-items: flex-end; padding-bottom: 1.5rem; border-bottom: 1px dashed #e2e8f0; animation: fadeIn 0.3s ease-out;';
        
        row.innerHTML = `
            <div>
                <input type="number" step="0.01" name="payments[${pCount}][amount]" class="payment-amount-field" value="0" oninput="recalcPaymentTotal()" required>
            </div>
            <div>
                <select name="payments[${pCount}][account_id]" required class="select-premium">
                    ${accOpts}
                </select>
            </div>
            @if(!($settlement->net_amount < 0))
            <div>
                <select name="payments[${pCount}][owner_bank_account_id]" required class="select-premium">
                    ${ownAccOpts}
                </select>
            </div>
            @endif
            <div>
                <input type="date" name="payments[${pCount}][date]" value="{{ date('Y-m-d') }}" required class="input-premium">
            </div>
            <div style="text-align: center;">
                <button type="button" onclick="this.closest('.payment-row-premium').remove(); recalcPaymentTotal()" class="btn-remove-active">&times;</button>
            </div>
        `;
        container.appendChild(row);
        pCount++;
        recalcPaymentTotal();
    }

    function recalcPaymentTotal() {
        const totalToPay = parseFloat(document.getElementById('currentBalance').value);
        let currentAssigned = 0;
        document.querySelectorAll('.payment-amount-field').forEach(input => {
            currentAssigned += parseFloat(input.value || 0);
        });
        
        const display = document.getElementById('remainingBalanceDisplay');
        display.innerText = '$' + currentAssigned.toLocaleString('es-AR', { minimumFractionDigits: 2 });
        
        if (Math.abs(currentAssigned - totalToPay) < 0.01) {
            display.style.color = '#38a169';
        } else if (currentAssigned > totalToPay) {
            display.style.color = '#e53e3e';
        } else {
            display.style.color = '#d69e2e';
        }
    }

    @if($settlement->status !== 'paid')
        window.addEventListener('DOMContentLoaded', recalcPaymentTotal);
    @endif
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
