@extends('layouts.app')

@section('title', '| Recibo de Pago')

@section('content')
<div class="no-print" style="max-width: 800px; margin: 0 auto 2rem; display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('collections.show', $collection) }}" class="btn" style="background: #edf2f7; color: #4a5568; display: flex; align-items: center; gap: 0.5rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Volver a la Gestión
    </a>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <form action="{{ route('collections.send_receipt', [$collection, $payment]) }}" method="POST" style="display: inline; margin: 0;">
            @csrf
            <button type="submit" class="btn" style="background: var(--accent-gradient); color: white; display: flex; align-items: center; gap: 0.5rem; font-weight: 700; border: none; padding: 0.7rem 1.2rem; cursor: pointer; box-shadow: 0 4px 6px rgba(56, 178, 172, 0.15);">
                📧 Enviar al Inquilino
            </button>
        </form>
        <button onclick="window.print()" class="btn" style="background: var(--primary-color); color: white; display: flex; align-items: center; gap: 0.5rem; font-weight: 700; padding: 0.7rem 1.2rem; border: none; cursor: pointer;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Imprimir Recibo
        </button>
    </div>
</div>

<div id="receipt-container" style="max-width: 800px; margin: 0 auto;">
    <div class="card" style="padding: 3rem; border: 2px solid #edf2f7; position: relative; overflow: hidden; background: white;">
        <!-- Header Design -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 8px; background: var(--accent-gradient);"></div>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 3rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                    <div style="background: var(--primary-color); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 1.2rem;">H</div>
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color);">Habitar</span>
                </div>
                <p style="font-size: 0.9rem; color: var(--text-light); line-height: 1.5; margin: 0;">
                    Soluciones Inmobiliarias<br>
                    {{ \App\Models\AgencySetting::get('agency_address', 'Av. Belgrano (N) 450, Santiago del Estero') }}<br>
                    {{ \App\Models\AgencySetting::get('agency_email', 'contacto@habitar.com.ar') }}
                </p>
            </div>
            <div style="text-align: right;">
                <h1 style="margin: 0; color: var(--primary-color); font-size: 1.8rem; font-weight: 900;">RECIBO DE PAGO</h1>
                <p style="margin: 0.5rem 0 0 0; font-weight: 700; color: #718096; font-size: 1rem;">N° #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                <div style="margin-top: 1rem; padding: 0.5rem 1rem; background: #f8fafc; border-radius: 8px; display: inline-block;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-light);">FECHA:</span>
                    <span style="font-size: 0.9rem; font-weight: 800; color: var(--primary-color);">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div style="background: #f8fafc; border-radius: 15px; padding: 2rem; margin-bottom: 3rem; border: 1px solid #edf2f7;">
            <div style="margin-bottom: 1.5rem;">
                <span style="font-size: 0.75rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.05em;">Recibimos de</span>
                <p style="font-size: 1.3rem; font-weight: 800; color: var(--primary-color); margin: 0.3rem 0 0 0;">{{ $collection->lease->tenant->name }}</p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.05em;">La suma de</span>
                    <p style="font-size: 1.8rem; font-weight: 900; color: #48BB78; margin: 0.3rem 0 0 0;">${{ number_format($payment->amount, 2) }}</p>
                </div>
                <div>
                    <span style="font-size: 0.75rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.05em;">En concepto de</span>
                    <p style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin: 0.3rem 0 0 0;">
                        Pago {{ $collection->status === 'paid' ? 'Total' : 'Parcial' }} - {{ \Carbon\Carbon::createFromDate(null, $collection->month, 1)->translatedFormat('F') }} {{ $collection->year }}
                    </p>
                    <p style="font-size: 0.85rem; color: var(--text-light); margin: 0;">{{ $collection->lease->property->location }}</p>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 3rem;">
            <h4 style="font-size: 0.85rem; font-weight: 800; color: var(--text-light); text-transform: uppercase; margin-bottom: 1rem; border-bottom: 1px solid #edf2f7; padding-bottom: 0.5rem;">Detalle del Pago</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.8rem 0; color: var(--text-light); font-size: 0.9rem;">Método de Pago:</td>
                    <td style="padding: 0.8rem 0; text-align: right; font-weight: 700; color: var(--primary-color);">
                        @if(($payment->account->type ?? '') === 'cash')
                            Efectivo
                        @elseif(($payment->account->type ?? '') === 'bank')
                            Transferencia Bancaria
                        @else
                            {{ $payment->account->name ?? 'N/A' }}
                        @endif
                    </td>
                </tr>
                @if($payment->notes)
                <tr>
                    <td style="padding: 0.8rem 0; color: var(--text-light); font-size: 0.9rem;">Observaciones:</td>
                    <td style="padding: 0.8rem 0; text-align: right; font-style: italic; color: #4a5568;">{{ $payment->notes }}</td>
                </tr>
                @endif
                <tr style="border-top: 2px solid #edf2f7;">
                    <td style="padding: 1rem 0; color: var(--text-light); font-weight: 700;">SALDO RESTANTE DEL PERIODO:</td>
                    <td style="padding: 1rem 0; text-align: right; font-weight: 900; font-size: 1.2rem; color: #F6AD55;">${{ number_format($collection->balance, 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 5rem; display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="text-align: center; width: 250px;">
                <div style="border-top: 1px solid #cbd5e0; padding-top: 0.5rem; font-size: 0.8rem; color: #a0aec0; font-weight: 700;">HABITAR SOLUCIONES</div>
            </div>
            <div style="text-align: right;">
                <p style="font-size: 0.75rem; color: #a0aec0; margin: 0; font-style: italic;">Comprobante no válido como factura.</p>
                <p style="font-size: 0.75rem; color: #a0aec0; margin: 0;">Sistema de Gestión Habitar</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print, .sidebar, .top-bar, [style*="background: #f0fff4"], [style*="background: #fff5f5"] { 
            display: none !important; 
        }
        body { 
            background: white !important; 
            padding: 0 !important; 
            margin: 0 !important; 
            display: block !important;
        }
        .app-container {
            margin-left: 0 !important;
            padding: 0 !important;
            min-height: auto !important;
            display: block !important;
        }
        .main-content { 
            padding: 0 !important; 
            margin: 0 !important; 
            width: 100% !important;
            display: block !important;
        }
        .card { 
            border: none !important; 
            box-shadow: none !important; 
            padding: 0 !important; 
        }
        #receipt-container { 
            width: 100% !important; 
            max-width: 100% !important; 
            margin: 0 !important;
            padding: 0 !important;
        }
        @page {
            size: auto;
            margin: 15mm 20mm;
        }
    }
</style>
@endsection
