@extends('layouts.app')

@section('title', '| Ubicaciones')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('settings.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver a Configuración</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Provincias y Localidades</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Administra las zonas y regiones de operación de tus propiedades.</p>
    </div>
    
    <button onclick="document.getElementById('province-form').style.display='block'" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 700;">➕ Nueva Provincia</button>
</div>

<div class="card" style="padding: 2rem;">
    <!-- Form nueva provincia -->
    <form id="province-form" onsubmit="storeProvince(event, this)" style="display:none; background: #f8fafc; padding: 1.5rem; border-radius: 12px; margin-bottom: 2.5rem; border: 1px dashed #cbd5e0;">
        @csrf
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Nombre de la Provincia</label>
        <div style="display: flex; gap: 0.5rem;">
            <input type="text" name="name" placeholder="Ej: Santiago del Estero" required style="flex: 1; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px;">
            <button type="submit" class="btn btn-primary" style="font-weight: 700;">Guardar Provincia</button>
        </div>
    </form>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
            <thead>
                <tr style="border-bottom: 2px solid #edf2f7; text-align: left; color: var(--text-light); font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">
                    <th style="padding: 1rem 1.5rem; width: 45%;">Provincia</th>
                    <th style="padding: 1rem 1.5rem; width: 25%;">Localidades</th>
                    <th style="padding: 1rem 1.5rem; width: 30%; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($provinces as $province)
                    <!-- Fila Principal de la Provincia -->
                    <tr id="province-row-{{ $province->id }}" style="border-bottom: 1px solid #edf2f7; background: white; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <td style="padding: 1.1rem 1.5rem; font-weight: 700; color: var(--primary-color); font-size: 1.05rem;">
                            {{ $province->name }}
                        </td>
                        <td style="padding: 1.1rem 1.5rem; font-weight: 600;">
                            <span id="count-{{ $province->id }}" style="background: #F7FAFC; border: 1px solid #E2E8F0; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; color: #4A5568; font-weight: 700;">
                                {{ $province->cities_count }} localidades
                            </span>
                        </td>
                        <td style="padding: 1.1rem 1.5rem; text-align: right;">
                            <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                <!-- Botón Expandir Localidades -->
                                <button onclick="toggleLocalities({{ $province->id }})" class="btn" style="background: #EBF8FF; color: #3182CE; border: 1px solid #BEE3F8; font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer;">
                                    <svg id="chevron-{{ $province->id }}" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    Ver Localidades
                                </button>
                                <!-- Botón Añadir Localidad -->
                                <button onclick="toggleCityForm({{ $province->id }})" class="btn" style="background: var(--accent-color); color: white; font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.2rem; cursor: pointer;">
                                    ➕ Localidad
                                </button>
                                <!-- Botón Eliminar Provincia -->
                                <button onclick="ajaxDeleteProvince('{{ route('settings.provinces.destroy', $province) }}', {{ $province->id }})" style="background: #FFF5F5; border: 1px solid #FED7D7; color: #C53030; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;" title="Eliminar Provincia" onmouseover="this.style.background='#FED7D7'" onmouseout="this.style.background='#FFF5F5'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Fila Colapsable de Localidades (Oculta por defecto) -->
                    <tr id="localities-row-{{ $province->id }}" style="display: none; background: #fafbfc;">
                        <td colspan="3" style="padding: 1.5rem 2.2rem; border-bottom: 1px solid #edf2f7; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                            <!-- Formulario de carga rápida para Añadir Localidad -->
                            <div id="city-form-{{ $province->id }}" style="display: none; background: #ebf8ff; padding: 1.2rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px dashed #4299e1;">
                                <form onsubmit="storeCity(event, this, {{ $province->id }})" style="display: flex; gap: 0.5rem; align-items: center;">
                                    @csrf
                                    <input type="hidden" name="province_id" value="{{ $province->id }}">
                                    <input type="text" name="name" placeholder="Nombre de localidad..." required style="flex: 1; padding: 0.7rem 1rem; border: 1px solid #bee3f8; border-radius: 8px; font-size: 0.9rem; background: white;">
                                    <button type="submit" class="btn btn-primary" style="font-size: 0.85rem; background: #3182ce; padding: 0.7rem 1.2rem; font-weight: 700; border-radius: 8px;">Añadir Localidad</button>
                                </form>
                            </div>

                            <!-- Listado de Localidades -->
                            <div id="cities-list-{{ $province->id }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 0.8rem;">
                                @forelse($province->cities as $city)
                                    <div id="city-tag-{{ $city->id }}" style="background: #f1f5f9; padding: 0.6rem 0.8rem; border-radius: 8px; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; font-weight: 600; color: #475569; border: 1px solid #e2e8f0; transition: all 0.2s;">
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px;" title="{{ $city->name }}">{{ $city->name }}</span>
                                        <button onclick="ajaxDeleteCity('{{ route('settings.cities.destroy', $city) }}', {{ $city->id }}, {{ $province->id }})" style="background: none; border: none; color: #fc8181; cursor: pointer; padding: 0; margin-left: 0.5rem; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='#C53030'" onmouseout="this.style.color='#fc8181'">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </div>
                                @empty
                                    <p class="empty-msg" style="grid-column: 1 / -1; text-align: center; color: var(--text-light); font-size: 0.9rem; font-style: italic; padding: 1rem 0; font-weight: 600;">Sin localidades cargadas en esta provincia.</p>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleLocalities(provinceId) {
        const row = document.getElementById(`localities-row-${provinceId}`);
        const chevron = document.getElementById(`chevron-${provinceId}`);
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            row.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }

    function toggleCityForm(provinceId) {
        const form = document.getElementById(`city-form-${provinceId}`);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        
        // Si se abre el formulario, asegurar que la fila de la localidad esté desplegada
        if (form.style.display === 'block') {
            const row = document.getElementById(`localities-row-${provinceId}`);
            const chevron = document.getElementById(`chevron-${provinceId}`);
            row.style.display = 'table-row';
            chevron.style.transform = 'rotate(180deg)';
        }
    }

    async function storeProvince(e, form) {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch("{{ route('settings.provinces.store') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok) { location.reload(); } else { alert(result.message || 'Error al guardar'); }
        } catch (error) { console.error(error); }
    }

    async function storeCity(e, form, provinceId) {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch("{{ route('settings.cities.store') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok) {
                const list = document.getElementById(`cities-list-${provinceId}`);
                const emptyMsg = list.querySelector('.empty-msg');
                if (emptyMsg) emptyMsg.remove();
                
                const div = document.createElement('div');
                div.id = `city-tag-${result.city.id}`;
                div.style = "background: #f1f5f9; padding: 0.6rem 0.8rem; border-radius: 8px; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; font-weight: 600; color: #475569; border: 1px solid #e2e8f0; animation: slideDown 0.3s ease-out;";
                div.innerHTML = `<span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px;" title="${result.city.name}">${result.city.name}</span> <button onclick="ajaxDeleteCity('/settings/cities/${result.city.id}', ${result.city.id}, ${provinceId})" style="background: none; border: none; color: #fc8181; cursor: pointer; padding: 0; margin-left: 0.5rem; display: flex; align-items: center; justify-content: center;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>`;
                list.appendChild(div);
                
                const countSpan = document.getElementById(`count-${provinceId}`);
                const currentCount = parseInt(countSpan.innerText.split(' ')[0]) || 0;
                countSpan.innerText = `${currentCount + 1} localidades`;
                form.reset();
                form.parentElement.style.display = 'none';
            } else { alert(result.message || 'Error al guardar'); }
        } catch (error) { console.error(error); }
    }

    async function ajaxDeleteProvince(url, provinceId) {
        if (!confirm('¿Estás seguro de eliminar esta provincia y todas sus localidades asociadas? Esta acción es irreversible.')) return;
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok) {
                const row = document.getElementById(`province-row-${provinceId}`);
                const rowLoc = document.getElementById(`localities-row-${provinceId}`);
                
                row.style.transition = 'all 0.4s ease';
                rowLoc.style.transition = 'all 0.4s ease';
                row.style.opacity = '0';
                rowLoc.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                rowLoc.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    row.remove();
                    rowLoc.remove();
                }, 400);
            } else {
                alert(result.error || 'Error al eliminar el registro.');
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión.');
        }
    }

    async function ajaxDeleteCity(url, cityId, provinceId) {
        if (!confirm('¿Estás seguro de eliminar esta localidad?')) return;
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok) {
                const element = document.getElementById(`city-tag-${cityId}`);
                element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                element.style.opacity = '0';
                element.style.transform = 'scale(0.85)';
                
                setTimeout(() => {
                    element.remove();
                    
                    // Actualizar contador
                    const countSpan = document.getElementById(`count-${provinceId}`);
                    const currentCount = Math.max(0, (parseInt(countSpan.innerText.split(' ')[0]) || 0) - 1);
                    countSpan.innerText = `${currentCount} localidades`;
                    
                    // Mostrar mensaje de vacío si no hay localidades
                    const list = document.getElementById(`cities-list-${provinceId}`);
                    if (list.querySelectorAll('[id^="city-tag-"]').length === 0) {
                        list.innerHTML = `<p class="empty-msg" style="grid-column: 1 / -1; text-align: center; color: var(--text-light); font-size: 0.9rem; font-style: italic; padding: 1rem 0; font-weight: 600;">Sin localidades cargadas en esta provincia.</p>`;
                    }
                }, 300);
            } else {
                alert(result.error || 'Error al eliminar el registro.');
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión.');
        }
    }
</script>

<style>
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
