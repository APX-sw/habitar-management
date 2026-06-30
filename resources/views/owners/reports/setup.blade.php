@extends('layouts.app')

@section('title', '| Nuevo Reporte')

@section('content')
<style>
    .owner-card-item {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 1rem 1.5rem !important;
        border: 1px solid #edf2f7 !important;
        border-radius: 12px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        background: #ffffff !important;
        margin-bottom: 0.5rem !important;
    }
    .owner-card-item:hover {
        background: #f8fafc !important;
        border-color: #cbd5e0 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05) !important;
    }
    .owner-checkbox {
        display: inline-block !important;
        width: 22px !important;
        height: 22px !important;
        padding: 0 !important;
        margin: 0 !important;
        border-radius: 6px !important;
        border: 2px solid #cbd5e0 !important;
        background-color: #ffffff !important;
        cursor: pointer !important;
        box-shadow: none !important;
        flex-shrink: 0 !important;
        appearance: auto !important;
        -webkit-appearance: auto !important;
    }
</style>
<div style="max-width: 800px; margin: 0 auto; padding-bottom: 3rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
        <div>
            <h1 style="color: var(--primary-color); font-size: 2rem; margin: 0;">Generar Nuevo Reporte Patrimonial</h1>
            <p style="color: var(--text-light); margin-top: 0.5rem; font-size: 0.95rem;">Seleccioná el período y los propietarios. El reporte se guardará en el historial del sistema.</p>
        </div>
    </div>

    @if(session('error'))
        <div style="background: #fff5f5; color: #c53030; padding: 1rem; border-radius: 10px; border-left: 5px solid #e53e3e; margin-bottom: 2rem; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card" style="padding: 2.5rem; border-top: 5px solid var(--accent-color);">
        <form action="{{ route('reports.store') }}" method="POST">
            @csrf

            <!-- Selección de Período -->
            <h3 style="margin: 0 0 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Período a Reportar
            </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #edf2f7; margin-bottom: 2.5rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; margin-bottom: 0.8rem;">Desde</label>
                    <div style="display: flex; gap: 0.5rem; width: 100%;">
                        <div style="flex: 1.5; min-width: 140px;">
                            <select name="start_month" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.95rem; cursor: pointer;">
                                @php
                                    $months = [1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 
                                               7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'];
                                    $currentMonth = now()->month;
                                @endphp
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $num == 1 ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 90px;">
                            <select name="start_year" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.95rem; cursor: pointer;">
                                @for($y = now()->year; $y >= 2023; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #a0aec0; text-transform: uppercase; margin-bottom: 0.8rem;">Hasta</label>
                    <div style="display: flex; gap: 0.5rem; width: 100%;">
                        <div style="flex: 1.5; min-width: 140px;">
                            <select name="end_month" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.95rem; cursor: pointer;">
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 90px;">
                            <select name="end_year" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #cbd5e0; background: white; font-size: 0.95rem; cursor: pointer;">
                                @for($y = now()->year; $y >= 2023; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selección de Propietarios -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Propietarios a Incluir
                </h3>
                <button type="button" id="select-all-btn" class="btn" style="background: #edf2f7; color: #4a5568; font-size: 0.8rem; padding: 0.5rem 1rem; font-weight: 700; border-radius: 6px; border: 1px solid #cbd5e0; cursor: pointer; transition: all 0.2s;">Seleccionar Todos</button>
            </div>

            <div style="border: 1px solid #edf2f7; border-radius: 12px; max-height: 400px; overflow-y: auto; background: #fff; padding: 0.8rem; display: flex; flex-direction: column; gap: 0.4rem;">
                @foreach($owners as $owner)
                    <div class="owner-card-item" onclick="toggleOwnerCheckbox(event, this)">
                        <div style="display: flex; align-items: center; gap: 1rem; pointer-events: none;">
                            <div style="width: 40px; height: 40px; background: var(--secondary-color); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem;">
                                {{ substr($owner->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--primary-color); font-size: 1.05rem;">{{ $owner->name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-light); margin-top: 0.1rem; font-weight: 500;">DNI/CUIT: {{ $owner->dni_cuit }}</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <input type="checkbox" name="owner_ids[]" value="{{ $owner->id }}" class="owner-checkbox" {{ $selectedOwnerId == $owner->id ? 'checked' : '' }} onclick="event.stopPropagation();">
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 3rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #edf2f7; padding-top: 1.5rem;">
                <a href="{{ route('reports.index') }}" class="btn" style="background: #edf2f7; color: #4a5568; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 1.75rem; border: 1px solid #cbd5e0; border-radius: 8px; font-size: 0.95rem; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#edf2f7'">
                    Cancelar
                </a>
                <button type="submit" class="btn" style="background: var(--accent-color); color: white; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 2rem; border: 1px solid var(--accent-color); border-radius: 8px; font-size: 0.95rem; gap: 0.5rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(56, 178, 172, 0.2);" onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-1px)'" onmouseout="this.style.opacity='1'; this.style.transform='none'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Generar y Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleOwnerCheckbox(event, card) {
        // Evitamos disparar dos veces si se hace click directamente en el checkbox
        if (event.target.classList.contains('owner-checkbox')) {
            return;
        }
        const cb = card.querySelector('.owner-checkbox');
        if (cb) {
            cb.checked = !cb.checked;
            const changeEvent = new Event('change');
            cb.dispatchEvent(changeEvent);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectAllBtn = document.getElementById('select-all-btn');
        const checkboxes = document.querySelectorAll('.owner-checkbox');
        let allSelected = false;

        selectAllBtn.addEventListener('click', function() {
            allSelected = !allSelected;
            checkboxes.forEach(cb => {
                cb.checked = allSelected;
            });
            selectAllBtn.textContent = allSelected ? 'Deseleccionar Todos' : 'Seleccionar Todos';
        });

        // Verificamos si vino preseleccionado por query parameter
        const checkedCount = document.querySelectorAll('.owner-checkbox:checked').length;
        if(checkedCount > 0 && checkedCount === checkboxes.length) {
            allSelected = true;
            selectAllBtn.textContent = 'Deseleccionar Todos';
        }
    });
</script>
@endsection
