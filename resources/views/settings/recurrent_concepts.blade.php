@extends('layouts.app')

@section('title', '| Conceptos Recurrentes')

@section('content')
<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin: 0;">Conceptos Recurrentes</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Catálogo de conceptos que se cobran periódicamente en los contratos (Expensas, ABL, Aguas, etc.).</p>
    </div>
    <a href="{{ route('settings.index') }}" class="btn btn-secondary" style="padding: 0.6rem 1.2rem;">Volver a Configuración</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <!-- Lista de Conceptos -->
    <div class="card" style="padding: 0; overflow: hidden; align-self: start;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                <tr>
                    <th style="padding: 1rem 1.5rem; font-size: 0.8rem; text-transform: uppercase; color: #718096;">Concepto</th>
                    <th style="padding: 1rem 1.5rem; font-size: 0.8rem; text-transform: uppercase; color: #718096;">Categoría Contable</th>
                    <th style="padding: 1rem 1.5rem; font-size: 0.8rem; text-transform: uppercase; color: #718096; width: 100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recurrentConcepts as $concept)
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 1rem 1.5rem; font-weight: 600; color: #4A5568;">
                            {{ $concept->name }}
                        </td>
                        <td style="padding: 1rem 1.5rem; color: #718096; font-size: 0.9rem;">
                            {{ $concept->transactionCategory ? $concept->transactionCategory->name : 'N/A' }}
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            <form action="{{ route('settings.recurrent_concepts.destroy', $concept) }}" method="POST" onsubmit="return confirm('¿Eliminar este concepto? No afectará a los cargos ya creados.');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background: #FFF5F5; color: #E53E3E; border: 1px solid #FED7D7; padding: 0.4rem 0.8rem; border-radius: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay conceptos recurrentes configurados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Formulario Crear Concepto -->
    <div class="card" style="padding: 1.5rem; align-self: start; position: sticky; top: 2rem;">
        <h3 style="margin-top: 0; color: var(--primary-color); border-bottom: 1px solid #edf2f7; padding-bottom: 0.5rem; margin-bottom: 1.5rem;">Nuevo Concepto</h3>
        <form action="{{ route('settings.recurrent_concepts.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Nombre</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;" placeholder="Ej: Expensas Ordinarias">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem;">Categoría Contable</label>
                <select name="transaction_category_id" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #d2d6dc;">
                    <option value="">(Sin categoría por defecto)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <p style="font-size: 0.75rem; color: #a0aec0; margin-top: 0.5rem;">Categoría que se asignará al cobrar este concepto.</p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-weight: 700;">Guardar Concepto</button>
        </form>
    </div>
</div>
@endsection
