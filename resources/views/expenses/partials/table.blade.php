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
                    @if($expense->attachment_path)
                        <a href="{{ asset('storage/' . $expense->attachment_path) }}" target="_blank" style="color: var(--primary-color); text-decoration: none;" title="Ver Comprobante">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                        </a>
                    @else
                        <span style="color: #cbd5e0;">-</span>
                    @endif
                </td>
                <td style="padding: 1rem; text-align: right; font-weight: 800; color: #E53E3E;">
                    ${{ number_format($expense->amount, 2) }}
                </td>
                <td style="padding: 1rem; text-align: right;">
                    <button onclick="openEditModal({{ $expense->id }}, '{{ $expense->description }}')" class="btn" style="padding: 0.4rem; background: #EBF8FF; color: #3182CE; border: 1px solid #BEE3F8;" title="Editar / Adjuntar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay gastos registrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 1.5rem;" class="pagination-links">
    {{ $expenses->links() }}
</div>
