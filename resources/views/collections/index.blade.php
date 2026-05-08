@extends('layouts.app')

@section('title', '| Cobros de Alquiler')

@section('content')
<div style="padding: 0.5rem;">
    <!-- Encabezado -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="color: var(--primary-color); margin-bottom: 0.5rem; font-size: 2rem;">Gestión de Cobros de Alquiler</h1>
            <p style="color: var(--text-light); margin: 0;">Administra los cobros mensuales, honorarios y gastos de cada contrato.</p>
        </div>
        <a href="{{ route('collections.create') }}" class="btn btn-primary" style="padding: 0.8rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 600;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Generar Cobros del Mes
        </a>
    </div>

    <!-- Filtro de Periodo AJAX -->
    <div class="card" style="margin-bottom: 2rem; padding: 1rem 1.5rem; border: 1px solid var(--secondary-color); background: #f8fafc; border-radius: 12px;">
        <form id="filter-form" action="{{ route('collections.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: center;">
            <div style="flex: 1;">
                <select name="filter_month" class="auto-filter" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.9rem; cursor: pointer;">
                    <option value="">Todos los Meses</option>
                    @php
                        $months = [1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 
                                   7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'];
                    @endphp
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('filter_month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1;">
                <select name="filter_year" class="auto-filter" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.9rem; cursor: pointer;">
                    <option value="">Todos los Años</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('filter_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('collections.index') }}" class="btn" style="padding: 0.7rem 1rem; border-radius: 8px; background: #edf2f7; color: #4a5568; display: flex; align-items: center; border: 1px solid #cbd5e0;" title="Limpiar Filtros">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
            </a>
        </form>
    </div>

    <!-- Contenedor de Periodos -->
    <div id="periods-container">
        <div class="card" style="border-radius: 15px; border: 1px solid var(--secondary-color);">
            <h3 style="margin-bottom: 2rem; color: var(--primary-color); font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Periodos y Cobros Generados
            </h3>
            
            @if($periods->isEmpty())
                <div style="text-align: center; padding: 5rem 2rem; background: #f8fafc; border-radius: 12px; border: 2px dashed #cbd5e0;">
                    <div style="font-size: 4rem; margin-bottom: 1.5rem; filter: grayscale(1);">💰</div>
                    <p style="color: var(--text-light); margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 500;">No se encontraron periodos con los filtros aplicados.</p>
                    <a href="{{ route('collections.index') }}" class="btn" style="background: var(--primary-color); color: white; padding: 0.8rem 2rem; border-radius: 8px;">Ver Todos los Periodos</a>
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
                    @foreach($periods as $period)
                        @php
                            $monthName = \Carbon\Carbon::createFromDate(null, $period->month, 1)->locale('es')->translatedFormat('F');
                        @endphp
                        <a href="{{ route('collections.show_period', ['month' => $period->month, 'year' => $period->year]) }}" style="text-decoration: none; color: inherit; display: block;">
                            <div class="period-card" style="padding: 1.5rem; border-radius: 15px; transition: all 0.3s ease; border: 1px solid #e2e8f0; background: white; position: relative; overflow: hidden; height: 100%;">
                                <div style="position: absolute; top: 0; left: 0; width: 6px; height: 100%; background: var(--accent-color);"></div>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div style="background: #ebf4ff; color: #2b6cb0; width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.3rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                                            {{ str_pad($period->month, 2, '0', STR_PAD_LEFT) }}
                                        </div>
                                        <div>
                                            <h4 style="margin: 0; color: var(--primary-color); font-size: 1.15rem; font-weight: 700;">{{ ucfirst($monthName) }}</h4>
                                            <div style="font-size: 0.9rem; color: #718096; font-weight: 600;">Año {{ $period->year }}</div>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 800; color: var(--accent-color); font-size: 1.2rem;">
                                            ${{ number_format($period->sum_total, 2) }}
                                        </div>
                                        <div style="font-size: 0.65rem; text-transform: uppercase; color: #a0aec0; font-weight: 800; letter-spacing: 0.05em;">Total Periodo</div>
                                    </div>
                                </div>

                                <div style="font-size: 0.85rem; color: #4a5568; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    {{ $period->total_collections }} contratos procesados
                                </div>

                                <div style="padding-top: 1rem; border-top: 1px solid #edf2f7; display: flex; justify-content: space-between; font-size: 0.8rem;">
                                    <span style="color: #38A169; font-weight: 700; display: flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 8px; height: 8px; background: #38A169; border-radius: 50%;"></span>
                                        {{ $period->paid_count }} Pagados
                                    </span>
                                    <span style="color: #D69E2E; font-weight: 700; display: flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 8px; height: 8px; background: #D69E2E; border-radius: 50%;"></span>
                                        {{ $period->pending_count }} Pendientes
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .period-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 12px 20px rgba(0,0,0,0.08); 
        border-color: var(--accent-color) !important; 
    }
    #periods-container.loading { 
        opacity: 0.6; 
        pointer-events: none; 
    }
</style>

@endsection

@section('scripts')
<script>
    const filterForm = document.getElementById('filter-form');
    const autoFilters = document.querySelectorAll('.auto-filter');
    const periodsContainer = document.getElementById('periods-container');

    async function fetchPeriods() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = `${window.location.pathname}?${params.toString()}`;

        periodsContainer.classList.add('loading');

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newContent = doc.getElementById('periods-container');
            if (newContent) {
                periodsContainer.innerHTML = newContent.innerHTML;
            }
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error fetching periods:', error);
        } finally {
            periodsContainer.classList.remove('loading');
        }
    }

    autoFilters.forEach(select => {
        select.addEventListener('change', fetchPeriods);
    });
</script>
@endsection
