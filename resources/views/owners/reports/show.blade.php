@extends('layouts.app')

@section('title', '| Detalle del Reporte')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
        <div>
            <a href="{{ route('reports.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.8rem; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='var(--accent-color)'" onmouseout="this.style.color='var(--text-light)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Volver al historial
            </a>
            <h1 style="color: var(--primary-color); font-size: 2.2rem; font-weight: 800; letter-spacing: -0.03em; margin: 0;">{{ $report->title }}</h1>
            <p style="color: var(--text-light); margin-top: 0.4rem; font-size: 0.95rem;">Generado el {{ $report->created_at->format('d/m/Y H:i') }} hs • Período: <strong>{{ $periodLabel }}</strong></p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('reports.show_batch', $report) }}" target="_blank" class="btn" style="background: #edf2f7; color: var(--primary-color); font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Imprimir Lote (Todos)
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div style="background: #f0fff4; color: #276749; padding: 1rem 1.5rem; border-radius: var(--border-radius); margin-bottom: 2rem; border-left: 5px solid #48bb78; display: flex; align-items: center; gap: 0.8rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1.5rem;">Propietarios Incluidos ({{ count($ownersData) }})</h2>

    <!-- List of Owners -->
    <div style="display: grid; gap: 1.5rem;">
        @foreach($ownersData as $data)
            @php
                $publicLink = route('reports.show_public', [$report, $data['owner']->id]);
                
                // Mensaje preestablecido de WhatsApp
                $waMessage = "¡Hola " . $data['owner']->name . "! Te adjunto el Resumen de Gestión Patrimonial de tu propiedad administrada por Habitar para el período " . $periodLabel . ". Podés ver el dossier online ingresando al siguiente link: " . $publicLink . " ¡Muchas gracias por confiar en nosotros!";
                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $data['owner']->phone ?? '') . "?text=" . urlencode($waMessage);
                
                // Mailto link
                $mailSubject = "Resumen de Gestión Patrimonial - Inmobiliaria Habitar";
                $mailBody = "Estimado/a " . $data['owner']->name . ",\n\nEsperamos que se encuentre muy bien. Le adjuntamos el enlace para visualizar de manera interactiva su Resumen de Gestión Patrimonial para el período " . $periodLabel . ".\n\nEnlace al dossier: " . $publicLink . "\n\nCualquier consulta o duda contable quedamos enteramente a su disposición.\n\nAtentamente,\nAdministración Habitar";
                $mailUrl = "mailto:" . ($data['owner']->email ?? '') . "?subject=" . urlencode($mailSubject) . "&body=" . urlencode($mailBody);
            @endphp
            
            <div class="card" style="padding: 2rem; border-left: 5px solid var(--accent-color); border-radius: 12px; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.05)'" onmouseout="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02), 0 1px 3px rgba(0,0,0,0.05)'">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                    
                    <!-- Owner Contact -->
                    <div style="flex: 1; min-width: 250px;">
                        <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--primary-color); margin: 0 0 0.5rem 0;">{{ $data['owner']->name }}</h3>
                        <div style="display: grid; gap: 0.3rem; font-size: 0.85rem; color: var(--text-light); font-weight: 500;">
                            <div>DNI/CUIT: <strong style="color: var(--text-main);">{{ $data['owner']->dni_cuit }}</strong></div>
                            @if($data['owner']->email)
                                <div>Email: <strong style="color: var(--text-main);">{{ $data['owner']->email }}</strong></div>
                            @endif
                            @if($data['owner']->phone)
                                <div>Teléfono: <strong style="color: var(--text-main);">{{ $data['owner']->phone }}</strong></div>
                            @endif
                        </div>
                    </div>

                    <!-- Mini KPI Summary -->
                    <div style="display: flex; gap: 2rem; background: #f8fafc; padding: 1rem 1.5rem; border-radius: 10px; border: 1px solid #edf2f7; text-align: center;">
                        <div>
                            <span style="font-size: 0.7rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.2rem;">Neto Liquidado</span>
                            <span style="font-size: 1.15rem; font-weight: 800; color: var(--accent-color);">${{ number_format($data['totalNetIncome'], 2) }}</span>
                        </div>
                        <div style="width: 1px; background: #edf2f7;"></div>
                        <div>
                            <span style="font-size: 0.7rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.2rem;">Gastos Gestionados</span>
                            <span style="font-size: 1.15rem; font-weight: 800; color: var(--primary-color);">${{ number_format($data['totalExpensesManaged'], 2) }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; min-width: 200px;">
                        <a href="{{ route('reports.show_individual', [$report, $data['owner']->id]) }}" target="_blank" class="btn" style="background: var(--primary-color); color: white; font-size: 0.85rem; padding: 0.6rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.4rem; border-radius: 8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            Ver Dossier
                        </a>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                            @if($data['owner']->phone)
                                <a href="{{ $waUrl }}" target="_blank" class="btn" style="background: #25d366; color: white; font-size: 0.8rem; padding: 0.5rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.3rem; border-radius: 8px;" title="Enviar por WhatsApp">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                    WhatsApp
                                </a>
                            @else
                                <button disabled class="btn" style="background: #edf2f7; color: #a0aec0; font-size: 0.8rem; padding: 0.5rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.3rem; border-radius: 8px; cursor: not-allowed;" title="Requiere teléfono">
                                    WhatsApp
                                </button>
                            @endif

                            @if($data['owner']->email)
                                <form action="{{ route('reports.send_email', [$report, $data['owner']->id]) }}" method="POST" style="display: inline; width: 100%;">
                                    @csrf
                                    <button type="submit" class="btn" style="width: 100%; background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; font-size: 0.8rem; padding: 0.5rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.3rem; border-radius: 8px; cursor: pointer;" title="Enviar por Email a través de n8n">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        Email
                                    </button>
                                </form>
                            @else
                                <button disabled class="btn" style="background: #edf2f7; color: #a0aec0; font-size: 0.8rem; padding: 0.5rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.3rem; border-radius: 8px; cursor: not-allowed; width: 100%;" title="Requiere email">
                                    Email
                                </button>
                            @endif
                        </div>

                        <button onclick="copyPublicLink('{{ $publicLink }}', this)" class="btn" style="background: #edf2f7; color: #4a5568; font-size: 0.8rem; padding: 0.5rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.3rem; border-radius: 8px; border: 1px solid #cbd5e0;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            <span>Copiar Enlace</span>
                        </button>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function copyPublicLink(url, button) {
        navigator.clipboard.writeText(url).then(() => {
            const originalContent = button.innerHTML;
            button.style.background = '#e6fffa';
            button.style.color = '#319795';
            button.style.borderColor = '#b2f5ea';
            button.querySelector('span').textContent = '¡Copiado!';
            
            setTimeout(() => {
                button.innerHTML = originalContent;
                button.style.background = '#edf2f7';
                button.style.color = '#4a5568';
                button.style.borderColor = '#cbd5e0';
            }, 2000);
        }).catch(err => {
            console.error('Error copying text: ', err);
            alert('No se pudo copiar el enlace.');
        });
    }
</script>
@endsection
