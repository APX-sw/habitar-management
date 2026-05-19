<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="border-bottom: 2px solid #edf2f7;">
            <th style="padding: 1rem; text-align: left; color: var(--text-light);">Fecha</th>
            <th style="padding: 1rem; text-align: left; color: var(--text-light);">Propiedad (Opcional)</th>
            <th style="padding: 1rem; text-align: left; color: var(--text-light);">Descripción</th>
            <th style="padding: 1rem; text-align: left; color: var(--text-light);">Concepto</th>
            <th style="padding: 1rem; text-align: left; color: var(--text-light);">Cuenta (Origen)</th>
            <th style="padding: 1rem; text-align: center; color: var(--text-light);">Adjunto</th>
            <th style="padding: 1rem; text-align: right; color: var(--text-light);">Monto</th>
            <th style="padding: 1rem; text-align: right; color: var(--text-light);">Acción</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $expense)
            <tr style="border-bottom: 1px solid #edf2f7;">
                <td style="padding: 1rem;">{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                <td style="padding: 1rem;">
                    @if($expense->property)
                        <a href="{{ route('properties.show', $expense->property) }}" style="color: var(--accent-color); text-decoration: none; font-weight: 600;">{{ $expense->property->location }}</a>
                    @else
                        <span style="color: var(--text-light); font-style: italic;">Gasto Inmobiliaria</span>
                    @endif
                </td>
                <td style="padding: 1rem; font-weight: 500;">{{ $expense->description ?? '-' }}</td>
                <td style="padding: 1rem;">
                    <span style="font-size: 0.8rem; background: #EDF2F7; color: #4A5568; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                        {{ $expense->transactionCategory->name ?? 'S/C' }}
                    </span>
                </td>
                <td style="padding: 1rem;">{{ $expense->account->name }}</td>
                <td style="padding: 1rem; text-align: center;">
                    @php
                        $detailsString = ($expense->description ? $expense->description . ' • ' : '') . 'Fecha: ' . \Carbon\Carbon::parse($expense->date)->format('d/m/Y') . ' • Monto: $' . number_format($expense->amount, 2);
                    @endphp
                    <button onclick="openDocsModal(null, '{{ addslashes($detailsString) }}', {{ $expense->id }})" style="background: none; border: none; padding: 0; color: {{ $expense->documents->count() > 0 ? 'var(--primary-color)' : '#cbd5e0' }}; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; outline: none; position: relative;" title="{{ $expense->documents->count() > 0 ? 'Ver Comprobantes (' . $expense->documents->count() . ')' : 'Subir Comprobante' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                        @if($expense->documents->count() > 0)
                            <span style="position: absolute; top: -5px; right: -5px; background: var(--primary-color); color: white; font-size: 0.65rem; border-radius: 50%; width: 14px; height: 14px; display: flex; align-items: center; justify-content: center; font-weight: 800; box-shadow: 0 0 0 2px white;">{{ $expense->documents->count() }}</span>
                        @endif
                    </button>
                </td>
                <td style="padding: 1rem; text-align: right; font-weight: 800; color: #E53E3E;">
                    ${{ number_format($expense->amount, 2) }}
                </td>
                <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                    <button onclick="openEditModal({{ $expense->id }}, '{{ $expense->description }}')" class="btn" style="padding: 0.4rem; background: #EBF8FF; color: #3182CE; border: 1px solid #BEE3F8; display: inline-flex; align-items: center; justify-content: center; outline: none; border-radius: 6px;" title="Editar / Adjuntar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </button>
                    <button type="button" onclick="triggerDeleteExpense({{ $expense->id }}, '{{ addslashes($expense->description ?? $expense->transactionCategory->name) }}', '{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}', '${{ number_format($expense->amount, 2) }}', '{{ addslashes($expense->account->name) }}', '{{ route('expenses.destroy', $expense) }}')" class="btn" style="padding: 0.4rem; background: #FFF5F5; color: #E53E3E; border: 1px solid #FED7D7; display: inline-flex; align-items: center; justify-content: center; outline: none; cursor: pointer; border-radius: 6px;" title="Eliminar Gasto">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay gastos registrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 1.5rem;" class="pagination-links">
    {{ $expenses->links() }}
</div>
