@extends('layouts.app')

@section('title', '| Historial de Reportes')

@section('content')
<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="color: var(--primary-color); margin: 0;">Historial de Reportes</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem; font-size: 0.95rem;">Accedé, visualizá o eliminá los reportes y dossiers patrimoniales generados en el sistema.</p>
    </div>
    <a href="{{ route('reports.create') }}" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nuevo Reporte
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid #edf2f7; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 2px solid var(--secondary-color); background: #f8fafc;">
                <th style="padding: 1.2rem 1.5rem; color: var(--text-light); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Nombre del Reporte / Período</th>
                <th style="padding: 1.2rem 1.5rem; color: var(--text-light); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Creado el</th>
                <th style="padding: 1.2rem 1.5rem; color: var(--text-light); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Propietarios Incluidos</th>
                <th style="padding: 1.2rem 1.5rem; color: var(--text-light); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr style="border-bottom: 1px solid var(--secondary-color); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.2rem 1.5rem;">
                        <div style="font-weight: 700; color: var(--primary-color); font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent-color)" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            {{ $report->title }}
                        </div>
                    </td>
                    <td style="padding: 1.2rem 1.5rem; color: var(--text-main); font-weight: 500;">
                        {{ $report->created_at->format('d/m/Y H:i') }} hs
                    </td>
                    <td style="padding: 1.2rem 1.5rem;">
                        <span style="background: #e6fffa; color: #319795; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #b2f5ea;">
                            {{ count($report->owner_ids) }} propietarios
                        </span>
                    </td>
                    <td style="padding: 1.2rem 1.5rem; text-align: right;">
                        <div style="display: flex; gap: 0.75rem; justify-content: flex-end; align-items: center;">
                            <a href="{{ route('reports.show', $report) }}" class="btn" style="background: #edf2f7; color: var(--primary-color); font-size: 0.85rem; padding: 0.5rem 1rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                Ver Dossier
                            </a>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este reporte del historial?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background: #fff5f5; color: #c53030; font-size: 0.85rem; padding: 0.5rem 1rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; border: none; cursor: pointer;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding: 3rem; text-align: center; color: var(--text-light); font-weight: 500;">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
                        No hay reportes generados todavía. Hacé clic en "Nuevo Reporte" para comenzar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem; display: flex; justify-content: center;">
    {{ $reports->links() }}
</div>
@endsection
