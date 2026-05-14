<div style="overflow-x: auto;">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #edf2f7;">
                <th style="padding: 1rem; text-align: left; color: var(--text-light);">Fecha</th>
                <th style="padding: 1rem; text-align: left; color: var(--text-light);">Cuenta</th>
                <th style="padding: 1rem; text-align: left; color: var(--text-light);">Descripción</th>
                <th style="padding: 1rem; text-align: right; color: var(--text-light);">Ingreso</th>
                <th style="padding: 1rem; text-align: right; color: var(--text-light);">Egreso</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
                <tr style="border-bottom: 1px solid #edf2f7;">
                    <td style="padding: 1rem;">
                        <div style="font-weight: 600; color: var(--primary-color);">{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-light);">{{ \Carbon\Carbon::parse($movement->movement_date)->format('H:i') }} hs</div>
                    </td>
                    <td style="padding: 1rem; font-weight: 600;">{{ $movement->account->name }}</td>
                    <td style="padding: 1rem; color: var(--text-light);">
                        <div style="margin-bottom: 0.3rem;">
                            @if($movement->transactionCategory)
                                <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700; background-color: #edf2f7; color: var(--primary-color); text-transform: uppercase;">
                                    {{ $movement->transactionCategory->name }}
                                </span>
                            @else
                                <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700; background-color: #fbd38d; color: #975a16; text-transform: uppercase;">
                                    Sin Categoría
                                </span>
                            @endif
                        </div>
                        {{ $movement->description }}
                    </td>
                    <td style="padding: 1rem; text-align: right; font-weight: 700; color: #48BB78;">
                        {{ $movement->type === 'income' ? '$' . number_format($movement->amount, 2) : '-' }}
                    </td>
                    <td style="padding: 1rem; text-align: right; font-weight: 700; color: #E53E3E;">
                        {{ $movement->type === 'expense' ? '$' . number_format($movement->amount, 2) : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-light);">No hay movimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem;" id="pagination-links">
    {{ $movements->links() }}
</div>
