@extends('layouts.app')

@section('title', '| Tipos de Inmuebles')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('settings.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver a Configuración</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Tipos de Inmuebles</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Gestiona las categorías de propiedades disponibles.</p>
    </div>
</div>

<div class="card" style="padding: 2.5rem; max-width: 800px;">
    <form onsubmit="storeType(event, this)" style="display: flex; gap: 1rem; margin-bottom: 3rem; background: #f8fafc; padding: 1.5rem; border-radius: 15px; border: 1px solid #edf2f7;">
        @csrf
        <div style="flex: 1;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Nombre del Tipo</label>
            <input type="text" name="name" placeholder="Ej: Departamento, Local, Cochera..." required style="width: 100%; padding: 0.8rem; border: 1px solid #d2d6dc; border-radius: 8px; font-weight: 600;">
        </div>
        <button type="submit" class="btn btn-primary" style="align-self: flex-end; padding: 0.8rem 2rem;">Añadir Categoría</button>
    </form>

    <div id="types-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
        @foreach($propertyTypes as $type)
            <div id="type-tag-{{ $type->id }}" style="padding: 1.2rem; background: white; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #edf2f7; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div style="display: flex; align-items: center; gap: 0.8rem;">
                    <div style="width: 10px; height: 10px; background: #48BB78; border-radius: 50%;"></div>
                    <span style="font-weight: 800; color: #2D3748; font-size: 1.1rem;">{{ $type->name }}</span>
                </div>
                <button onclick="ajaxDelete('{{ route('settings.property-types.destroy', $type) }}', 'type-tag-{{ $type->id }}')" style="background: #FFF5F5; border: none; color: #fc8181; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='#fc8181'; this.style.color='white'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        @endforeach
    </div>
</div>

<script>
    async function storeType(e, form) {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch("{{ route('settings.property-types.store') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok) {
                const container = document.getElementById('types-container');
                const div = document.createElement('div');
                div.id = `type-tag-${result.type.id}`;
                div.style = "padding: 1.2rem; background: white; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #edf2f7; box-shadow: 0 4px 6px rgba(0,0,0,0.02); animation: slideDown 0.3s ease-out;";
                div.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 0.8rem;">
                        <div style="width: 10px; height: 10px; background: #48BB78; border-radius: 50%;"></div>
                        <span style="font-weight: 800; color: #2D3748; font-size: 1.1rem;">${result.type.name}</span>
                    </div>
                    <button onclick="ajaxDelete('/settings/property-types/${result.type.id}', 'type-tag-${result.type.id}')" style="background: #FFF5F5; border: none; color: #fc8181; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                `;
                container.appendChild(div);
                form.reset();
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

            if (response.ok) {
                const element = document.getElementById(elementId);
                element.style.transition = 'all 0.4s ease';
                element.style.opacity = '0';
                element.style.transform = 'scale(0.9)';
                setTimeout(() => element.remove(), 400);
            } else {
                const result = await response.json();
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
