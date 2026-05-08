@extends('layouts.app')

@section('title', '| Índices de Actualización')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('settings.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver a Configuración</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Índices de Actualización</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Carga los porcentajes de indexación mensual para los contratos.</p>
    </div>
    
    <button onclick="document.getElementById('index-form-new').style.display='block'" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700;">➕ Nuevo Índice</button>
</div>

<div class="card" style="padding: 2.5rem;">
    <form id="index-form-new" onsubmit="storeIndex(event, this)" style="display:none; margin-bottom: 3rem; background: #f8fafc; padding: 2rem; border-radius: 15px; border: 1px dashed #cbd5e0;">
        @csrf
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Nombre del Índice</label>
        <div style="display: flex; gap: 1rem;">
            <input type="text" name="name" placeholder="Ej: ICL (Índice Contratos Locación)" required style="flex: 1; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px; font-weight: 600;">
            <button type="submit" class="btn btn-primary">Crear Índice</button>
        </div>
    </form>

    <div id="indices-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
        @foreach($indexTypes as $index)
            <div id="index-tag-{{ $index->id }}" style="border: 1px solid #edf2f7; border-radius: 20px; overflow: hidden; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-bottom: 1px solid #edf2f7;">
                    <div>
                        <span style="font-weight: 800; color: var(--accent-color); font-size: 1.2rem;">{{ $index->name }}</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="toggleIndexValues({{ $index->id }})" class="btn" style="background: white; border: 1px solid #d2d6dc; color: var(--primary-color); font-size: 0.8rem; padding: 0.5rem 1rem; font-weight: 800; border-radius: 10px;">VALORES CARGADOS</button>
                        <button onclick="ajaxDelete('{{ route('settings.index-types.destroy', $index) }}', 'index-tag-{{ $index->id }}')" style="background: #FFF5F5; border: none; color: #fc8181; width: 35px; height: 35px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
                
                <div id="index-values-{{ $index->id }}" style="display: none; padding: 1.5rem; border-top: 1px solid #edf2f7;">
                    <form onsubmit="storeIndexValue(event, this, {{ $index->id }})" style="display: grid; grid-template-columns: 1.2fr 1fr 1fr auto; gap: 0.8rem; margin-bottom: 2rem; background: #ebf8ff; padding: 1.2rem; border-radius: 15px; border: 1px solid #bee3f8;">
                        @csrf
                        <input type="hidden" name="index_type_id" value="{{ $index->id }}">
                        <div>
                            <label style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #2B6CB0; display: block; margin-bottom: 0.3rem;">Mes</label>
                            <select name="month" required style="width:100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #bee3f8; font-weight: 600;">
                                @for($m=1; $m<=12; $m++) <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromFormat('!m', $m)->translatedFormat('F') }}</option> @endfor
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #2B6CB0; display: block; margin-bottom: 0.3rem;">Año</label>
                            <select name="year" required style="width:100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #bee3f8; font-weight: 600;">
                                @for($y=date('Y'); $y>=2023; $y--) <option value="{{ $y }}">{{ $y }}</option> @endfor
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #2B6CB0; display: block; margin-bottom: 0.3rem;">% Aumento</label>
                            <input type="number" step="0.01" name="percentage" placeholder="0.00" required style="width:100%; padding: 0.6rem; border-radius: 8px; border: 1px solid #bee3f8; font-weight: 800;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="align-self: flex-end; padding: 0.7rem; background: #3182ce; border-radius: 8px;">➕</button>
                    </form>

                    <div id="values-list-{{ $index->id }}" style="max-height: 250px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.5rem; padding-right: 0.5rem;">
                        <!-- Cargado vía AJAX -->
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    async function storeIndex(e, form) {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch("{{ route('settings.index-types.store') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok) { location.reload(); } else { alert(result.message || 'Error al guardar'); }
        } catch (error) { console.error(error); }
    }

    async function toggleIndexValues(indexId) {
        const panel = document.getElementById(`index-values-${indexId}`);
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            await getIndexValues(indexId);
        } else {
            panel.style.display = 'none';
        }
    }

    async function getIndexValues(indexId) {
        const list = document.getElementById(`values-list-${indexId}`);
        list.innerHTML = '<p style="text-align:center; font-size:0.8rem; color: var(--text-light);">Cargando valores...</p>';
        try {
            const response = await fetch(`/api/index-types/${indexId}/values`);
            const data = await response.json();
            list.innerHTML = '';
            if (data.length === 0) {
                list.innerHTML = '<p style="text-align:center; color:var(--text-light); font-size:0.8rem; padding: 1rem;">No hay valores cargados aún.</p>';
                return;
            }
            data.forEach(val => {
                const item = document.createElement('div');
                item.id = `value-row-${val.id}`;
                item.style = "display:flex; justify-content:space-between; padding:0.8rem 1.2rem; background: #f8fafc; border-radius: 10px; border: 1px solid #edf2f7; font-size:0.9rem; align-items:center; transition: all 0.2s;";
                item.innerHTML = `
                    <span style="color: #4A5568; font-weight: 700;">${val.month}/${val.year}</span> 
                    <div style="display:flex; align-items:center; gap:1.5rem;">
                        <span style="color:var(--accent-color); font-weight:900; font-size: 1.1rem;">+${parseFloat(val.percentage).toFixed(2)}%</span>
                        <button onclick="deleteIndexValue(${val.id}, ${indexId})" style="background:none; border:none; color:#fc8181; cursor:pointer; padding:0; display:flex;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                `;
                list.appendChild(item);
            });
        } catch (error) { console.error(error); }
    }

    async function storeIndexValue(e, form, indexId) {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch("{{ route('settings.index-values.store') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok) {
                await getIndexValues(indexId);
                form.reset();
            } else { alert(result.message || 'Error al cargar valor'); }
        } catch (error) { console.error(error); }
    }

    async function deleteIndexValue(valueId, indexId) {
        if (!confirm('¿Eliminar este valor mensual?')) return;
        try {
            const response = await fetch(`/settings/index-values/${valueId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            if (response.ok) {
                const row = document.getElementById(`value-row-${valueId}`);
                row.remove();
            }
        } catch (error) { console.error(error); }
    }

    async function ajaxDelete(url, elementId) {
        if (!confirm('¿Estás seguro de eliminar este índice?')) return;
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const element = document.getElementById(elementId);
                element.style.transition = 'all 0.4s ease';
                element.style.opacity = '0';
                element.style.transform = 'scale(0.9)';
                setTimeout(() => element.remove(), 400);
            } else {
                const result = await response.json();
                alert(result.error || 'Error al eliminar.');
            }
        } catch (error) { console.error(error); }
    }
</script>
@endsection
