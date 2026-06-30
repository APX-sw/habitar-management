<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Sueldo - {{ $payment->settlement->employee->full_name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 2rem; background: #f4f4f4; }
        .receipt-container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #2b6cb0; padding-bottom: 1rem; margin-bottom: 2rem; }
        .logo { font-size: 2rem; font-weight: bold; color: #2b6cb0; margin: 0; }
        .title { font-size: 1.5rem; text-transform: uppercase; color: #4a5568; margin: 0; text-align: right; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem; }
        .info-box { border: 1px solid #e2e8f0; padding: 1rem; border-radius: 4px; }
        .info-label { font-size: 0.75rem; color: #718096; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 0.25rem; }
        .info-value { font-size: 1rem; font-weight: 500; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f7fafc; color: #4a5568; font-size: 0.85rem; text-transform: uppercase; }
        .amount-col { text-align: right; }
        .total-row { background-color: #ebf8ff; font-weight: bold; font-size: 1.2rem; }
        .footer { margin-top: 4rem; display: flex; justify-content: space-around; text-align: center; }
        .signature-line { width: 200px; border-top: 1px solid #a0aec0; margin-top: 3rem; margin-bottom: 0.5rem; display: inline-block; }
        @media print {
            body { background: white; padding: 0; }
            .receipt-container { box-shadow: none; max-width: 100%; }
        }
    </style>
</head>
<body>
    @php
        $settlement = $payment->settlement;
    @endphp
    <div class="receipt-container">
        <div class="header">
            <div>
                <h1 class="logo">Habitar</h1>
                <p style="margin: 0; color: #718096; font-size: 0.85rem;">Gestión Inmobiliaria</p>
            </div>
            <div>
                <h2 class="title">Recibo de Sueldo</h2>
                <p style="margin: 0; color: #718096; text-align: right; font-size: 0.9rem;">
                    Período: <strong>{{ str_pad($settlement->month, 2, '0', STR_PAD_LEFT) }} / {{ $settlement->year }}</strong>
                </p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <span class="info-label">Empleado</span>
                <p class="info-value" style="font-size: 1.1rem; font-weight: bold;">{{ $settlement->employee->full_name }}</p>
                <span class="info-label" style="margin-top: 0.5rem;">Documento / CUIL</span>
                <p class="info-value">{{ $settlement->employee->document_number }}</p>
                <span class="info-label" style="margin-top: 0.5rem;">Puesto</span>
                <p class="info-value">{{ $settlement->employee->job_title }}</p>
            </div>
            <div class="info-box">
                <span class="info-label">Fecha de Ingreso</span>
                <p class="info-value">{{ Carbon\Carbon::parse($settlement->employee->hire_date)->format('d/m/Y') }}</p>
                <span class="info-label" style="margin-top: 0.5rem;">Datos Bancarios</span>
                <p class="info-value">{{ $settlement->employee->bank_name ?: 'No especificado' }} <br> <span style="font-size: 0.85rem;">{{ $settlement->employee->cbu_alias }}</span></p>
                <span class="info-label" style="margin-top: 0.5rem;">Fecha de Pago</span>
                <p class="info-value">{{ Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th class="amount-col">Haberes</th>
                    <th class="amount-col">Deducciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sueldo Básico</td>
                    <td class="amount-col">$ {{ number_format($settlement->base_amount, 2, ',', '.') }}</td>
                    <td class="amount-col"></td>
                </tr>
                
                @foreach($settlement->bonuses as $bonus)
                <tr>
                    <td>Bono / Extra: {{ $bonus->description }}</td>
                    <td class="amount-col">$ {{ number_format($bonus->amount, 2, ',', '.') }}</td>
                    <td class="amount-col"></td>
                </tr>
                @endforeach

                @if($settlement->advances_amount > 0)
                <tr>
                    <td>Adelantos Descontados</td>
                    <td class="amount-col"></td>
                    <td class="amount-col">$ {{ number_format($settlement->advances_amount, 2, ',', '.') }}</td>
                </tr>
                @endif
                
                <tr style="background: #f7fafc;">
                    <td style="text-align: right; font-weight: bold;">Subtotales</td>
                    <td class="amount-col" style="font-weight: bold;">$ {{ number_format($settlement->base_amount + $settlement->bonuses_amount, 2, ',', '.') }}</td>
                    <td class="amount-col" style="font-weight: bold; color: #e53e3e;">$ {{ number_format($settlement->advances_amount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="text-align: right; font-weight: bold; color: #4a5568;" colspan="2">Neto Total del Sueldo</td>
                    <td class="amount-col" style="color: #4a5568;">$ {{ number_format($settlement->net_amount, 2, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td style="text-align: right; font-weight: bold; color: #2b6cb0;" colspan="2">MONTO ABONADO EN ESTE RECIBO</td>
                    <td class="amount-col" style="color: #2b6cb0;">$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <div>
                <span class="signature-line"></span><br>
                <span class="info-label">Firma del Empleador</span>
            </div>
            <div>
                <span class="signature-line"></span><br>
                <span class="info-label">Firma del Empleado</span>
                <p style="font-size: 0.75rem; margin: 0.2rem 0; color: #718096;">Recibí conforme el importe neto arriba indicado.</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button onclick="window.print()" style="padding: 0.5rem 2rem; background: #2b6cb0; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem;" class="no-print">Imprimir Recibo</button>
        </div>
    </div>
    
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</body>
</html>
