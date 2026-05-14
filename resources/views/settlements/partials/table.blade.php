<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="border-bottom: 2px solid #edf2f7;">
            <th style="padding: 1rem; text-align: left; color: var(--text-light);">Propietario</th>
            <th style="padding: 1rem; text-align: left; color: var(--text-light);">Periodo</th>
            <th style="padding: 1rem; text-align: right; color: var(--text-light);">Total Ingresos</th>
            <th style="padding: 1rem; text-align: right; color: var(--text-light);">Gastos/Comisión</th>
            <th style="padding: 1rem; text-align: right; color: var(--text-light);">Neto a Pagar</th>
            <th style="padding: 1rem; text-align: center; color: var(--text-light);">Estado</th>
            <th style="padding: 1rem; text-align: right; color: var(--text-light);">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($settlements as $settlement)
            <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.2s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1.2rem 1rem;">
                    <div style="font-weight: 700; color: var(--primary-color);">{{ $settlement->owner->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-light);">DNI: {{ $settlement->owner->dni_cuit }}</div>
                </td>
                <td style="padding: 1.2rem 1rem; font-weight: 600;">
                    {{ str_pad($settlement->month, 2, '0', STR_PAD_LEFT) }}/{{ $settlement->year }}
                </td>
                <td style="padding: 1.2rem 1rem; text-align: right; font-weight: 600; color: #38B2AC;">
                    ${{ number_format($settlement->total_income, 2) }}
                </td>
                <td style="padding: 1.2rem 1rem; text-align: right; color: #E53E3E;">
                    -${{ number_format($settlement->total_expense + $settlement->agency_commission, 2) }}
                </td>
                <td style="padding: 1.2rem 1rem; text-align: right; font-weight: 800; font-size: 1.1rem; color: var(--primary-color);">
                    ${{ number_format($settlement->net_amount, 2) }}
                </td>
                <td style="padding: 1.2rem 1rem; text-align: center;">
                    @if($settlement->status === 'paid')
                        <span class="badge" style="background: #c6f6d5; color: #22543d;">Pagado</span>
                    @else
                        <span class="badge" style="background: #feebc8; color: #744210;">Pendiente</span>
                    @endif
                </td>
                <td style="padding: 1.2rem 1rem; text-align: right;">
                    <a href="{{ route('settlements.show', $settlement) }}" class="btn" style="background: #edf2f7; color: var(--primary-color); padding: 0.5rem 1rem; font-size: 0.85rem;">Ver Detalles</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="padding: 3rem; text-align: center; color: var(--text-light);">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 1rem; opacity: 0.3;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <p>No se encontraron rendiciones que coincidan con los filtros.</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 1.5rem;" class="pagination-links">
    {{ $settlements->links() }}
</div>
