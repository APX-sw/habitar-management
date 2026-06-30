@extends('layouts.app')

@section('title', '| Detalles del Periodo')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('collections.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver al listado de periodos</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0; text-transform: capitalize;">
            Cobros de {{ \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F') }} {{ $year }}
        </h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Listado de contratos vigentes para este periodo de cobro.</p>
    </div>
    
    <div style="display: flex; gap: 1.5rem;">
        @if($collections->where('status', 'ready')->count() > 0)
            <button onclick="document.getElementById('bulkSendModal').style.display='flex'" class="btn" style="background: #4299E1; color: white; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; padding: 1rem 1.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"></path><path d="M22 2L15 22L11 13L2 9L22 2z"></path></svg>
                Envío Masivo de Mails
            </button>
        @endif
        @if(count($servicesReport) > 0)
            <button onclick="document.getElementById('servicesReportModal').style.display='flex'" class="btn" style="background: var(--bg-body); border: 1px solid var(--secondary-color); color: var(--primary-color); font-weight: 700; display: flex; align-items: center; gap: 0.5rem; padding: 1rem 1.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Ver Servicios a Pagar
            </button>
        @endif
        <div class="card" style="padding: 1rem 1.5rem; border-left: 4px solid var(--accent-color); margin: 0;">
            <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--text-light); font-weight: 700; margin-bottom: 0.2rem;">Total Proyectado</div>
            <div style="font-size: 1.3rem; font-weight: 800; color: var(--primary-color);">${{ number_format($collections->sum('total_amount'), 2) }}</div>
        </div>
        <div class="card" style="padding: 1rem 1.5rem; border-left: 4px solid #48BB78; margin: 0;">
            <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--text-light); font-weight: 700; margin-bottom: 0.2rem;">Recaudado Real</div>
            <div style="font-size: 1.3rem; font-weight: 800; color: #38a169;">${{ number_format($collections->sum('total_paid'), 2) }}</div>
        </div>
    </div>
</div>

<!-- Barra de Filtros AJAX -->
<div class="card" style="margin-bottom: 1.5rem; padding: 0.8rem 1.2rem; border: 1px solid var(--secondary-color); background: #f8fafc;">
    <form id="filter-form" action="{{ url()->current() }}" method="GET" style="display: flex; gap: 0.7rem; align-items: center; flex-wrap: wrap;">
        
        <!-- Búsqueda -->
        <div style="flex: 2; min-width: 250px;">
            <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="🔍 Buscar por propiedad o inquilino..." style="width: 100%; padding: 0.6rem 1rem; border-radius: 8px; border: 1px solid #cbd5e0; font-size: 0.85rem;">
        </div>

        <!-- Estado -->
        <div style="flex: 1; min-width: 180px;">
            <select name="status" class="auto-filter" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.85rem; cursor: pointer;">
                <option value="">Todos los Estados</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borrador (Incompleto)</option>
                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Listo para Cobrar</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Mail Enviado</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Pago Parcial</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Cobrado</option>
            </select>
        </div>

        <a href="{{ url()->current() }}" class="btn" style="padding: 0.6rem 0.8rem; border-radius: 8px; background: #edf2f7; color: #4a5568; display: flex; align-items: center;" title="Limpiar Filtros">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
        </a>
    </form>
</div>

<div id="collections-table-container">
    <div class="card" style="padding: 0; overflow: hidden; border-radius: 15px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid var(--secondary-color);">
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Propiedad / Contrato</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Inquilino</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Monto Total</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                    <th style="padding: 1.2rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $collection)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='white'">
                        <td style="padding: 1.2rem;">
                            <div style="font-weight: 700; color: var(--primary-color);">{{ $collection->lease->property->location }}</div>
                        </td>
                        <td style="padding: 1.2rem;">
                            <div style="font-weight: 600; color: #2d3748;">{{ $collection->lease->tenant->name }}</div>
                        </td>
                        <td style="padding: 1.2rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; position: relative;" class="tooltip-container">
                                <div style="font-weight: 800; color: var(--accent-color); font-size: 1.1rem;">${{ number_format($collection->total_amount, 2) }}</div>
                                <div style="color: #cbd5e0; cursor: help; display: flex; align-items: center;" title="Ver desglose">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                </div>
                                <!-- Tooltip Desglose -->
                                <div class="desglose-tooltip" style="display: none; position: absolute; left: 100%; top: 50%; transform: translateY(-50%); background: white; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 10px; padding: 1rem; width: 250px; z-index: 100; margin-left: 10px;">
                                    <div style="font-size: 0.7rem; text-transform: uppercase; color: #a0aec0; font-weight: 700; margin-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">Desglose del Total</div>
                                    @foreach($collection->details as $detail)
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.4rem;">
                                            <span style="color: #4a5568;">{{ $detail->name }}</span>
                                            <span style="font-weight: 700; color: var(--primary-color);">${{ number_format($detail->amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1.2rem;">
                            @php
                                $badgeBg = '#EDF2F7'; $badgeColor = '#4A5568'; $label = 'INCOMPLETO';
                                if($collection->status === 'paid') { $badgeBg = '#C6F6D5'; $badgeColor = '#22543D'; $label = 'COBRADO'; }
                                elseif($collection->status === 'partial') { $badgeBg = '#E9D8FD'; $badgeColor = '#553C9A'; $label = 'PAGO PARCIAL'; }
                                elseif($collection->status === 'ready') { $badgeBg = '#FEFCBF'; $badgeColor = '#744210'; $label = 'LISTO PARA COBRAR'; }
                                elseif($collection->status === 'sent') { $badgeBg = '#EBF8FF'; $badgeColor = '#2B6CB0'; $label = 'LISTO PARA COBRAR - MAIL ENVIADO'; }
                            @endphp
                            <span class="badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 0.4rem 0.8rem; font-size: 0.7rem; border-radius: 50px; font-weight: 700; white-space: nowrap;">
                                {{ $label }}
                            </span>
                        </td>
                        <td style="padding: 1.2rem; text-align: right;">
                            <a href="{{ route('collections.show', $collection) }}" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.75rem; font-weight: 700; border: 1px solid #e2e8f0;">GESTIONAR COBRO</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 4rem; text-align: center; color: var(--text-light);">No hay cobros generados para este periodo o los filtros aplicados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    #collections-table-container.loading { opacity: 0.5; pointer-events: none; }
    .tooltip-container:hover .desglose-tooltip {
        display: block !important;
    }
    .desglose-tooltip::before {
        content: "";
        position: absolute;
        left: -6px;
        top: 50%;
        transform: translateY(-50%) rotate(45deg);
        width: 12px;
        height: 12px;
        background: white;
        border-left: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
</style>

<div id="bulkSendModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 650px; padding: 2.5rem; position: relative; max-height: 90vh; overflow-y: auto;">
        <button onclick="document.getElementById('bulkSendModal').style.display='none'" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; cursor: pointer; color: var(--text-light);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <h3 style="margin-bottom: 1rem; color: var(--primary-color);">Envío Masivo de Cobros</h3>
        <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 2rem;">Selecciona los cobros que deseas enviar por mail. Solo se muestran los que están en estado "Listo para Cobrar".</p>

        <form action="{{ route('collections.bulk_send') }}" method="POST">
            @csrf
            <div style="max-height: 400px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 12px; margin-bottom: 2rem;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                    <thead style="background: #f8fafc; position: sticky; top: 0;">
                        <tr>
                            <th style="padding: 1rem; border-bottom: 1px solid #edf2f7;"><input type="checkbox" checked onchange="document.querySelectorAll('.bulk-check').forEach(c => c.checked = this.checked)"></th>
                            <th style="padding: 1rem; border-bottom: 1px solid #edf2f7;">Propiedad / Inquilino</th>
                            <th style="padding: 1rem; border-bottom: 1px solid #edf2f7; text-align: right;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collections->where('status', 'ready') as $col)
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1rem;"><input type="checkbox" name="collection_ids[]" value="{{ $col->id }}" class="bulk-check" checked></td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 700;">{{ $col->lease->property->location }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-light);">{{ $col->lease->tenant->name }}</div>
                                </td>
                                <td style="padding: 1rem; text-align: right; font-weight: 700;">${{ number_format($col->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('bulkSendModal').style.display='none'" class="btn" style="background: #f1f5f9; color: #475569;">Cancelar</button>
                <button type="submit" class="btn" style="background: #4299E1; color: white; font-weight: 700;">Confirmar y Enviar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reporte de Servicios -->
<div id="servicesReportModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 800px; padding: 2.5rem; position: relative; max-height: 90vh; overflow-y: auto;">
        <button onclick="document.getElementById('servicesReportModal').style.display='none'" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; cursor: pointer; color: var(--text-light);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <h3 style="margin-bottom: 0.5rem; color: var(--primary-color);">Servicios a Pagar por Habitar</h3>
        <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 2rem;">Listado de servicios (expensas, tasas, etc.) que la inmobiliaria debe abonar este mes, agrupados por concepto, con el código de pago correspondiente a cada propiedad.</p>

        @foreach($servicesReport as $conceptName => $data)
            <div style="margin-bottom: 2rem; border: 1px solid #edf2f7; border-radius: 12px; overflow: hidden;">
                <div style="background: #f8fafc; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #edf2f7;">
                    <h4 style="margin: 0; color: var(--primary-color); font-size: 1.1rem;">{{ $conceptName }}</h4>
                    <span style="font-weight: 800; color: var(--accent-color); font-size: 1.1rem;">Total: ${{ number_format($data['total'], 2) }}</span>
                </div>
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                    <thead>
                        <tr>
                            <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #edf2f7; color: var(--text-light);">Propiedad</th>
                            <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #edf2f7; color: var(--text-light);">Cód. Pago Electrónico</th>
                            <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #edf2f7; color: var(--text-light); text-align: right;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['items'] as $item)
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1rem 1.5rem; font-weight: 600; color: #2d3748;">{{ $item['location'] }}</td>
                                <td style="padding: 1rem 1.5rem;">
                                    <span style="background: #edf2f7; padding: 0.3rem 0.6rem; border-radius: 6px; font-family: monospace; font-size: 0.85rem; color: #4a5568;">{{ $item['payment_code'] }}</span>
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; font-weight: 700; color: var(--primary-color);">${{ number_format($item['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
            <button type="button" onclick="document.getElementById('servicesReportModal').style.display='none'" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-weight: 700;">Cerrar Reporte</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search-input');
    const autoFilters = document.querySelectorAll('.auto-filter');
    const tableContainer = document.getElementById('collections-table-container');

    async function fetchCollections() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = `${window.location.pathname}?${params.toString()}`;

        tableContainer.classList.add('loading');

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newContent = doc.getElementById('collections-table-container');
            if (newContent) {
                tableContainer.innerHTML = newContent.innerHTML;
            }
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error fetching collections:', error);
        } finally {
            tableContainer.classList.remove('loading');
        }
    }

    let timer;
    function handleFilterChange() {
        clearTimeout(timer);
        timer = setTimeout(fetchCollections, 300);
    }

    if (searchInput) searchInput.addEventListener('input', handleFilterChange);
    autoFilters.forEach(select => {
        select.addEventListener('change', handleFilterChange);
    });
</script>
@endsection
