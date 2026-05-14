@extends('layouts.app')

@section('title', '| Categorías')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <a href="{{ route('settings.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg> 
            <span style="font-weight: 600;">Volver a Configuración</span>
        </a>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Categorías de Movimientos</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Administra los conceptos para clasificar tus ingresos y gastos.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: start;">
    
    <!-- Lista de Categorías -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #edf2f7; background: #f8fafc;">
            <h3 style="margin: 0; color: var(--primary-color); font-size: 1.1rem;">Conceptos Existentes</h3>
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #edf2f7; background: #fcfcfc;">
                    <th style="padding: 1rem 1.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Nombre del Concepto</th>
                    <th style="padding: 1rem 1.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase;">Tipo</th>
                    <th style="padding: 1rem 1.5rem; color: #718096; font-size: 0.75rem; text-transform: uppercase; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem 1.5rem;">
                        <div style="font-weight: 700; color: var(--primary-color);">
                            {{ $category->name }}
                            @if($category->is_system)
                                <span style="font-size: 0.6rem; background: #e2e8f0; color: #4a5568; padding: 2px 6px; border-radius: 4px; margin-left: 5px; vertical-align: middle;">SISTEMA</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        @if($category->type === 'income')
                            <span class="badge" style="background: #c6f6d5; color: #22543d; font-size: 0.7rem;">Ingreso</span>
                        @elseif($category->type === 'expense')
                            <span class="badge" style="background: #fed7d7; color: #822727; font-size: 0.7rem;">Egreso</span>
                        @else
                            <span class="badge" style="background: #edf2f7; color: #4a5568; font-size: 0.7rem;">Ambos</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.5rem; text-align: right;">
                        @if(!$category->is_system)
                        <form action="{{ route('settings.categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este concepto?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn" style="background: none; border: none; color: #cbd5e0; cursor: pointer; padding: 0.5rem; transition: color 0.2s;" onmouseover="this.style.color='#c53030'" onmouseout="this.style.color='#cbd5e0'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Formulario de Categoría -->
    <div class="card" style="padding: 2rem; position: sticky; top: 2rem;">
        <h3 style="margin: 0 0 1.5rem; color: var(--primary-color); font-size: 1.1rem;">Nuevo Concepto</h3>
        <form action="{{ route('settings.categories.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Nombre del Concepto</label>
                <input type="text" name="name" required placeholder="Ej: Papelería, Mantenimiento..." style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; margin-bottom: 0.5rem;">Tipo de Movimiento</label>
                <select name="type" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc; background: white;">
                    <option value="expense">Egreso / Gasto</option>
                    <option value="income">Ingreso</option>
                    <option value="both">Ambos (Créditos/Débitos)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Crear Concepto</button>
        </form>
    </div>

</div>
@endsection
