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
        <p style="color: var(--text-light); margin-top: 0.5rem;">Administra las zonas donde operan tus propiedades.</p>
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
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>

    <div id="provinces-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        @foreach($provinces as $province)
            <div id="province-card-{{ $province->id }}" style="border: 1px solid #edf2f7; border-radius: 15px; overflow: hidden; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <div style="background: #f8fafc; padding: 1.2rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #edf2f7;">
                    <div>
                        <span style="font-weight: 800; color: var(--primary-color); font-size: 1.1rem;">{{ $province->name }}</span>
                        <span id="count-{{ $province->id }}" style="display: block; font-size: 0.75rem; color: var(--text-light); font-weight: 600;">{{ $province->cities_count }} localidades</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="ajaxDelete('{{ route('settings.provinces.destroy', $province) }}', 'province-card-{{ $province->id }}')" style="background: #FFF5F5; border: none; color: #C53030; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                        <button onclick="toggleCityForm({{ $province->id }})" class="btn" style="background: var(--accent-color); color: white; font-size: 0.75rem; padding: 0.5rem 1rem; border-radius: 8px;">
                            + Localidad
                        </button>
                    </div>
                </div>

                <div style="padding: 1.5rem;">
                    <div id="city-form-{{ $province->id }}" style="display: none; background: #ebf8ff; padding: 1.2rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px dashed #4299e1;">
                        <form onsubmit="storeCity(event, this, {{ $province->id }})" style="display: flex; gap: 0.5rem;">
                            @csrf
                            <input type="hidden" name="province_id" value="{{ $province->id }}">
                            <input type="text" name="name" placeholder="Nombre de localidad..." required style="flex: 1; padding: 0.7rem; border: 1px solid #bee3f8; border-radius: 8px;">
                            <button type="submit" class="btn btn-primary" style="font-size: 0.8rem; background: #3182ce;">Añadir</button>
                        </form>
                    </div>
                    
                    <div id="cities-list-{{ $province->id }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.8rem;">
                        @forelse($province->cities as $city)
                            <div id="city-tag-{{ $city->id }}" style="background: #f1f5f9; padding: 0.6rem 0.8rem; border-radius: 8px; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; font-weight: 600; color: #475569;">
                                {{ $city->name }}
                                <button onclick="ajaxDelete('{{ route('settings.cities.destroy', $city) }}', 'city-tag-{{ $city->id }}')" style="background: none; border: none; color: #fc8181; cursor: pointer; padding: 0; margin-left: 0.5rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>
                        @empty
                            <p class="empty-msg" style="grid-column: 1 / -1; text-align: center; color: var(--text-light); font-size: 0.85rem;">Sin localidades.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function toggleCityForm(provinceId) {
        const form = document.getElementById(`city-form-${provinceId}`);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
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
                div.style = "background: #f1f5f9; padding: 0.6rem 0.8rem; border-radius: 8px; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; font-weight: 600; color: #475569; animation: slideDown 0.3s ease-out;";
                div.innerHTML = `${result.city.name} <button onclick="ajaxDelete('/settings/cities/${result.city.id}', 'city-tag-${result.city.id}')" style="background: none; border: none; color: #fc8181; cursor: pointer; padding: 0; margin-left: 0.5rem;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>`;
                list.appendChild(div);
                const countSpan = document.getElementById(`count-${provinceId}`);
                const currentCount = parseInt(countSpan.innerText.split(' ')[0]) || 0;
                countSpan.innerText = `${currentCount + 1} localidades`;
                form.reset();
                form.parentElement.style.display = 'none';
            } else { alert(result.message || 'Error al guardar'); }
        } catch (error) { console.error(error); }
    }

    async function ajaxDelete(url, elementId) {
        if (!confirm('¿Estás seguro de eliminar este registro?')) return;
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
                const element = document.getElementById(elementId);
                element.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                element.style.opacity = '0';
                element.style.transform = 'scale(0.9) translateY(10px)';
                setTimeout(() => element.remove(), 400);
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
