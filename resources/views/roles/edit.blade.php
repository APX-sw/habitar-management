@extends('layouts.app')

@section('title', '| Editar Rol')

@section('content')
<div class="card" style="max-width: 1100px; margin: 0 auto;">

    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #edf2f7;">
        <h2 style="margin: 0; font-size: 1.4rem; color: var(--primary-color);">Editar Rol: {{ ucfirst($role->name) }}</h2>
        <p style="margin: 0.3rem 0 0; color: var(--text-light); font-size: 0.9rem;">Modifica el nombre y los permisos que tiene este rol.</p>
    </div>

    @if($role->name === 'superadmin')
        <div style="background: #fff5f5; color: #c53030; padding: 1.5rem; border-radius: var(--border-radius); border-left: 5px solid #e53e3e;">
            <p style="margin: 0; font-weight: 600;">El rol Super Administrador es inmutable y no puede modificarse.</p>
        </div>
    @else

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 2rem; max-width: 380px;">
            <label for="name">Nombre del Rol</label>
            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required autofocus>
        </div>

        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; color: var(--primary-color);">Permisos del Rol</h3>

        <div style="border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
            @foreach($groupedPermissions as $key => $section)
                @php
                    $hasChecked = false;
                    foreach($section['permissions'] as $perm) {
                        if(in_array($perm['name'], old('permissions', $rolePermissions))) {
                            $hasChecked = true; break;
                        }
                    }
                    $count = count($section['permissions']);
                @endphp

                <div class="perm-section" style="border-bottom: 1px solid #e2e8f0;">

                    {{-- Cabecera del acordeón --}}
                    <button type="button"
                        onclick="toggleSection('{{ $key }}')"
                        style="width: 100%; display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #f8fafc; border: none; cursor: pointer; text-align: left; transition: background 0.15s;">

                        <span style="width: 18px; height: 18px; color: var(--accent-color); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            {!! $section['icon'] !!}
                        </span>

                        <span style="flex: 1; font-size: 0.9rem; font-weight: 600; color: var(--primary-color);">
                            {{ $section['name'] }}
                        </span>

                        <span id="badge-{{ $key }}" style="display: {{ $hasChecked ? 'inline-block' : 'none' }}; font-size: 0.72rem; color: var(--accent-color); background: #e6fffa; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 600;">
                            Activos
                        </span>

                        <span style="font-size: 0.75rem; color: #a0aec0;">{{ $count }} permisos</span>

                        <svg id="arrow-{{ $key }}" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: #a0aec0; transition: transform 0.2s; flex-shrink: 0; {{ $hasChecked ? 'transform: rotate(180deg);' : '' }}">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Panel de permisos --}}
                    <div id="panel-{{ $key }}" style="display: {{ $hasChecked ? 'block' : 'none' }}; padding: 0.6rem 1rem; background: white; border-top: 1px solid #f0f4f8;">

                        @php
                            $subModules = [];
                            foreach($section['permissions'] as $perm) {
                                $subModules[$perm['module_display_name']][] = $perm;
                            }
                        @endphp

                        @foreach($subModules as $subName => $perms)
                            @if(count($subModules) > 1)
                                <p style="margin: 0.4rem 0 0.3rem; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #718096;">{{ $subName }}</p>
                            @endif

                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem; padding: 0.25rem 0;">
                                @foreach($perms as $perm)
                                    @php $isChecked = in_array($perm['name'], old('permissions', $rolePermissions)); @endphp
                                    <label class="perm-pill perm-pill-{{ $key }}" style="display: flex; align-items: center; padding: 0.3rem 0.8rem; border-radius: 999px; cursor: pointer; font-size: 0.83rem; font-weight: 500; transition: all 0.15s; {{ $isChecked ? 'background: var(--accent-color); border: 2px solid var(--accent-color); color: white; font-weight: 600;' : 'background: #f1f5f9; border: 2px solid #e2e8f0; color: #4a5568;' }}">
                                        <input type="checkbox"
                                            name="permissions[]"
                                            value="{{ $perm['name'] }}"
                                            class="perm-cb perm-cb-{{ $key }}"
                                            style="display:none;"
                                            onchange="updatePill(this); updateBadgeAndCheckAll('{{ $key }}')"
                                            {{ $isChecked ? 'checked' : '' }}>
                                        {{ $actionNames[$perm['action']] ?? ucfirst($perm['action']) }}
                                    </label>
                                @endforeach

                                {{-- Seleccionar todos inline al final --}}
                                @if($loop->last)
                                    <label style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.78rem; font-weight: 600; color: #319795; cursor: pointer; margin-left: 0.4rem;">
                                        <input type="checkbox" class="check-all-{{ $key }}" style="width: auto; margin: 0; accent-color: var(--accent-color);" onchange="checkAll('{{ $key }}', this.checked)">
                                        Todos
                                    </label>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>
            @endforeach
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; margin-top: 1.5rem; border-top: 1px solid #edf2f7;">
            <a href="{{ route('roles.index') }}" class="btn" style="background: #edf2f7; color: #4a5568;">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>

    @endif
</div>

<style>
    .perm-section:last-child { border-bottom: none; }
    .perm-section button:hover { background: #f0faf9 !important; }
    .perm-pill:hover { background: #e6fffa !important; border-color: var(--accent-color) !important; color: var(--accent-color) !important; }
</style>

<script>
function toggleSection(key) {
    const panel = document.getElementById('panel-' + key);
    const arrow = document.getElementById('arrow-' + key);
    const isOpen = panel.style.display === 'block';
    panel.style.display = isOpen ? 'none' : 'block';
    arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

function updatePill(cb) {
    const label = cb.closest('label');
    if (cb.checked) {
        label.style.background = 'var(--accent-color)';
        label.style.borderColor = 'var(--accent-color)';
        label.style.color = 'white';
        label.style.fontWeight = '600';
    } else {
        label.style.background = '#f1f5f9';
        label.style.borderColor = '#e2e8f0';
        label.style.color = '#4a5568';
        label.style.fontWeight = '500';
    }
}

function checkAll(key, checked) {
    document.querySelectorAll('.perm-cb-' + key).forEach(cb => {
        cb.checked = checked;
        updatePill(cb);
    });
    document.getElementById('badge-' + key).style.display = checked ? 'inline-block' : 'none';
}

function updateBadgeAndCheckAll(key) {
    const cbs = document.querySelectorAll('.perm-cb-' + key);
    const hasChecked = Array.from(cbs).some(cb => cb.checked);
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    document.getElementById('badge-' + key).style.display = hasChecked ? 'inline-block' : 'none';
    document.querySelectorAll('.check-all-' + key).forEach(ca => ca.checked = allChecked);
}
</script>
@endsection
