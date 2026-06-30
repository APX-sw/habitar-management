@extends('layouts.app')

@section('title', '| Tablero')

@section('content')
<!-- Header con Selector de Fecha -->
<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1.5rem;">
    <div>
        <h1 style="color: var(--primary-color); font-size: 2.2rem; margin-bottom: 0.4rem; font-weight: 800; letter-spacing: -0.03em;">Tablero de Control</h1>
        <p style="color: var(--text-light); font-size: 1rem; font-weight: 500; font-family: 'Outfit', sans-serif; margin: 0; line-height: 1.5;">
            Resumen general y estado financiero del mes de <span style="color: var(--accent-color); font-weight: 700; text-transform: capitalize;">{{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->locale('es')->translatedFormat('F \d\e Y') }}</span>
        </p>
    </div>
    
    <!-- Selector Dinámico de Mes y Año -->
    <form action="{{ route('dashboard') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center; background: white; padding: 0.35rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <select name="month" onchange="this.form.submit()" style="width: auto !important; padding: 0.5rem 2rem 0.5rem 1rem !important; border-radius: 8px !important; border: 1px solid #e2e8f0 !important; font-size: 0.9rem !important; font-weight: 600 !important; color: #4a5568 !important; background-color: #f7fafc !important; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%234A5568%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E') !important; background-repeat: no-repeat !important; background-position: right 0.75rem top 50% !important; background-size: 0.65rem auto !important; height: auto !important; line-height: 1.2 !important; box-shadow: none !important;">
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                    {{ ucfirst(\Carbon\Carbon::create(2000, $m, 1)->locale('es')->translatedFormat('F')) }}
                </option>
            @endforeach
        </select>
        <select name="year" onchange="this.form.submit()" style="width: auto !important; padding: 0.5rem 2rem 0.5rem 1rem !important; border-radius: 8px !important; border: 1px solid #e2e8f0 !important; font-size: 0.9rem !important; font-weight: 600 !important; color: #4a5568 !important; background-color: #f7fafc !important; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%234A5568%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E') !important; background-repeat: no-repeat !important; background-position: right 0.75rem top 50% !important; background-size: 0.65rem auto !important; height: auto !important; line-height: 1.2 !important; box-shadow: none !important;">
            @foreach(range(Carbon\Carbon::now()->year - 3, Carbon\Carbon::now()->year + 2) as $y)
                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endforeach
        </select>
    </form>
</div>

<!-- Banner de Espacio de Trabajo (Workspace) -->
@if(isset($employee) && $employee)
    <div style="background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%); padding: 1.5rem; border-radius: 12px; color: white; display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; box-shadow: 0 4px 10px rgba(49, 130, 206, 0.2);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: white;">Mi Espacio de Trabajo</h3>
                <p style="margin: 0.2rem 0 0; font-size: 0.95rem; opacity: 0.9;">Registrá tu asistencia diaria y gestioná tus metas desde tu Workspace unificado.</p>
            </div>
        </div>
        <a href="{{ route('workspace.index') }}" class="btn" style="background: white; color: #2b6cb0; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            Abrir Workspace
        </a>
    </div>
@endif

@can('dashboard.read')
<!-- KPI Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
    <div class="card" style="display: flex; align-items: center; gap: 1.2rem; padding: 1.5rem; border-left: 4px solid #3182ce; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background: #ebf4ff; color: #3182ce; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary-color); line-height: 1;">{{ $propertiesCount }}</div>
            <div style="font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; font-weight: 700; margin-top: 0.25rem;">Propiedades</div>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; gap: 1.2rem; padding: 1.5rem; border-left: 4px solid #805ad5; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background: #faf5ff; color: #805ad5; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary-color); line-height: 1;">{{ $activeLeasesCount }}</div>
            <div style="font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; font-weight: 700; margin-top: 0.25rem;">Contratos Activos</div>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; gap: 1.2rem; padding: 1.5rem; border-left: 4px solid #dd6b20; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background: #fffaf0; color: #dd6b20; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary-color); line-height: 1;">{{ $expiringThisMonthCount }}</div>
            <div style="font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; font-weight: 700; margin-top: 0.25rem;">Vencimientos del Mes</div>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; gap: 1.2rem; padding: 1.5rem; border-left: 4px solid #e53e3e; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background: #fff5f5; color: #e53e3e; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary-color); line-height: 1;">{{ $pendingCollectionsCount }}</div>
            <div style="font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; font-weight: 700; margin-top: 0.25rem;">Cobros Pendientes</div>
        </div>
    </div>
</div>

<!-- Financial Summary Section -->
<h2 style="font-size: 1.5rem; color: var(--primary-color); margin-bottom: 1.25rem; font-weight: 700; letter-spacing: -0.01em;">
    Flujo Financiero ({{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->locale('es')->translatedFormat('F Y') }})
</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <!-- Ingresos -->
    <div class="card" style="padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="color: var(--text-color); font-size: 1.1rem; font-weight: 600;">Ingresos por Alquileres</h3>
            <span style="background: #c6f6d5; color: #22543d; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">COBROS</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-light); font-size: 0.9rem;">Esperado (Total)</span>
                <span style="font-weight: 600;">${{ number_format($expectedIncome, 2, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-light); font-size: 0.9rem;">Recaudado (Efectivo)</span>
                <span style="font-weight: 700; color: #38a169;">${{ number_format($collectedIncome, 2, ',', '.') }}</span>
            </div>
            <div style="width: 100%; background-color: #e2e8f0; height: 8px; border-radius: 4px; margin-top: 0.5rem; overflow: hidden;">
                @php $incomePercent = $expectedIncome > 0 ? ($collectedIncome / $expectedIncome) * 100 : 0; @endphp
                <div style="width: {{ min(100, $incomePercent) }}%; background-color: #38a169; height: 100%;"></div>
            </div>
            <div style="text-align: right; font-size: 0.8rem; color: var(--text-light);">{{ number_format($incomePercent, 1) }}% Recaudado</div>
        </div>
    </div>

    <!-- Egresos -->
    <div class="card" style="padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="color: var(--text-color); font-size: 1.1rem; font-weight: 600;">Pagos a Propietarios</h3>
            <span style="background: #fed7d7; color: #822727; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">LIQUIDACIONES</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-light); font-size: 0.9rem;">A Liquidar (Total)</span>
                <span style="font-weight: 600;">${{ number_format($expectedSettlements, 2, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-light); font-size: 0.9rem;">Abonado (Pagado)</span>
                <span style="font-weight: 700; color: #e53e3e;">${{ number_format($paidSettlements, 2, ',', '.') }}</span>
            </div>
            <div style="width: 100%; background-color: #e2e8f0; height: 8px; border-radius: 4px; margin-top: 0.5rem; overflow: hidden;">
                @php $settlementPercent = $expectedSettlements > 0 ? ($paidSettlements / $expectedSettlements) * 100 : 0; @endphp
                <div style="width: {{ min(100, $settlementPercent) }}%; background-color: #e53e3e; height: 100%;"></div>
            </div>
            <div style="text-align: right; font-size: 0.8rem; color: var(--text-light);">{{ number_format($settlementPercent, 1) }}% Abonado</div>
        </div>
    </div>

    <!-- Ganancia -->
    <div class="card" style="padding: 1.5rem; background: linear-gradient(135deg, #2b6cb0 0%, #2c5282 100%); color: white; display: flex; flex-direction: column; justify-content: center;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 600; margin: 0; color: #ebf8ff;">Ganancia Estimada</h3>
                <p style="font-size: 0.85rem; opacity: 0.8; margin: 0;">Comisiones inmobiliarias del mes</p>
            </div>
        </div>
        <div style="font-size: 2.5rem; font-weight: 800; line-height: 1;">
            ${{ number_format($agencyCommission, 2, ',', '.') }}
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem;">
    <!-- Próximos Vencimientos -->
    <div class="card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: var(--primary-color); font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                Próximos Vencimientos (90 días)
            </h3>
            <a href="{{ route('leases.index') }}" style="color: #3182ce; font-size: 0.85rem; font-weight: 600; text-decoration: none;">Ver todos</a>
        </div>
        
        @if($upcomingExpirations->isEmpty())
            <div style="text-align: center; padding: 2rem 0; color: var(--text-light);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0" stroke-width="1.5" style="margin-bottom: 1rem;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <p>No hay vencimientos de contratos en los próximos 90 días.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                            <th style="padding: 0.75rem 0.5rem; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Propiedad</th>
                            <th style="padding: 0.75rem 0.5rem; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Inquilino</th>
                            <th style="padding: 0.75rem 0.5rem; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Vence en</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingExpirations as $lease)
                            @php
                                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($lease->end_date), false);
                                $statusColor = $daysLeft <= 30 ? '#e53e3e' : ($daysLeft <= 60 ? '#dd6b20' : '#3182ce');
                                $statusBg = $daysLeft <= 30 ? '#fff5f5' : ($daysLeft <= 60 ? '#fffaf0' : '#ebf4ff');
                            @endphp
                            <tr style="border-bottom: 1px solid #edf2f7;">
                                <td style="padding: 1rem 0.5rem; vertical-align: middle;">
                                    <a href="{{ route('leases.show', $lease->id) }}" style="font-weight: 600; color: var(--primary-color); text-decoration: none;">
                                        {{ $lease->property->location ?? 'N/D' }}
                                    </a>
                                </td>
                                <td style="padding: 1rem 0.5rem; color: var(--text-color); vertical-align: middle;">
                                    {{ $lease->tenant->name ?? 'N/D' }} {{ $lease->tenant->last_name ?? '' }}
                                </td>
                                <td style="padding: 1rem 0.5rem; vertical-align: middle;">
                                    <span style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-block;">
                                        @if($daysLeft < 0)
                                            Vencido (hace {{ abs(intval($daysLeft)) }} días)
                                        @elseif($daysLeft == 0)
                                            Vence hoy
                                        @else
                                            {{ intval($daysLeft) }} días
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    <!-- Actividad Reciente -->
    <div class="card" style="padding: 1.5rem;">
        <h3 style="color: var(--primary-color); font-weight: 700; margin-bottom: 1.5rem; margin-top: 0; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Actividad Reciente
        </h3>
        
        @if(isset($recentActivity) && $recentActivity->count() > 0)
            <div style="position: relative; border-left: 2px solid #e2e8f0; margin-left: 10px; padding-left: 20px;">
                @foreach($recentActivity as $activity)
                    @php
                        $modelName = class_basename($activity->subject_type);
                        $action = $activity->description; // created, updated, deleted
                        
                        $iconMap = [
                            'created' => ['color' => '#38a169', 'bg' => '#c6f6d5', 'icon' => '<path d="M12 5v14M5 12h14"></path>'],
                            'updated' => ['color' => '#3182ce', 'bg' => '#ebf4ff', 'icon' => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>'],
                            'deleted' => ['color' => '#e53e3e', 'bg' => '#fff5f5', 'icon' => '<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>'],
                        ];
                        
                        $defaultMap = ['color' => '#718096', 'bg' => '#edf2f7', 'icon' => '<circle cx="12" cy="12" r="10"></circle>'];
                        $map = $iconMap[$action] ?? $defaultMap;
                        
                        // Traducciones amigables de Modelos para el usuario final
                        $modelTranslations = [
                            'Property' => 'Propiedad',
                            'Lease' => 'Contrato de Alquiler',
                            'Tenant' => 'Inquilino',
                            'Owner' => 'Propietario',
                            'Collection' => 'Cobro de Alquiler',
                            'Settlement' => 'Liquidación de Propietario',
                            'Expense' => 'Egreso/Gasto',
                            'OwnerBankAccount' => 'Cuenta Bancaria de Propietario',
                            'ExtraCharge' => 'Cargo Extra',
                            'FixedCharge' => 'Cargo Fijo',
                            'CollectionPayment' => 'Pago de Alquiler',
                            'SettlementPayment' => 'Pago de Liquidación',
                            'CashRegisterMovement' => 'Movimiento de Caja',
                            'Account' => 'Cuenta Financiera',
                            'User' => 'Usuario',
                            'AgencySetting' => 'Configuración de Agencia',
                            'AgencyBankAccount' => 'Cuenta Bancaria de Agencia',
                            'Attendance' => 'Asistencia',
                            'AbsenceReason' => 'Motivo de Ausencia',
                            'Employee' => 'Legajo de Empleado',
                            'Objective' => 'Objetivo',
                            'ObjectiveComment' => 'Comentario de Objetivo',
                        ];
                        $translatedModel = $modelTranslations[$modelName] ?? $modelName;
                        
                        // Traducciones de Acciones
                        $actionTranslations = [
                            'created' => 'Nuevo registro de',
                            'updated' => 'Actualización de',
                            'deleted' => 'Eliminación de',
                        ];
                        $translatedAction = $actionTranslations[$action] ?? $action;

                        // Construcción de detalles legibles y amigables
                        $subjectDetails = '';
                        if ($activity->subject) {
                            $subject = $activity->subject;
                            if ($modelName === 'Property') {
                                $subjectDetails = ' - ' . ($subject->location ?? '');
                            } elseif ($modelName === 'Tenant' || $modelName === 'Owner') {
                                $subjectDetails = ' - ' . ($subject->name ?? '') . ' ' . ($subject->last_name ?? '');
                            } elseif ($modelName === 'Lease') {
                                $tenantName = ($subject->tenant->name ?? '') . ' ' . ($subject->tenant->last_name ?? '');
                                $subjectDetails = ' - Inquilino: ' . $tenantName . ' (' . ($subject->property->location ?? '') . ')';
                            } elseif ($modelName === 'Collection') {
                                $monthName = \Carbon\Carbon::create(2000, $subject->month ?? 1, 1)->locale('es')->translatedFormat('F');
                                $subjectDetails = ' - ' . ($subject->lease->property->location ?? '') . ' (' . ucfirst($monthName) . ' ' . ($subject->year ?? '') . ')';
                            } elseif ($modelName === 'Settlement') {
                                $ownerName = ($subject->owner->name ?? '') . ' ' . ($subject->owner->last_name ?? '');
                                $monthName = \Carbon\Carbon::create(2000, $subject->month ?? 1, 1)->locale('es')->translatedFormat('F');
                                $subjectDetails = ' - Propietario: ' . $ownerName . ' (' . ucfirst($monthName) . ' ' . ($subject->year ?? '') . ')';
                            } elseif ($modelName === 'Expense') {
                                $subjectDetails = ' - ' . ($subject->description ?? '') . ' ($' . number_format($subject->amount ?? 0, 2, ',', '.') . ')';
                            } elseif ($modelName === 'OwnerBankAccount') {
                                $subjectDetails = ' - Titular: ' . ($subject->holder_name ?? '') . ' (' . ($subject->cbu_alias ?? 'Sin Alias/CBU') . ')';
                            } elseif ($modelName === 'ExtraCharge') {
                                $subjectDetails = ' - ' . ($subject->description ?? '') . ' ($' . number_format($subject->amount ?? 0, 2, ',', '.') . ')';
                            } elseif ($modelName === 'FixedCharge') {
                                $subjectDetails = ' - ' . ($subject->name ?? '') . ' ($' . number_format($subject->amount ?? 0, 2, ',', '.') . ')';
                            } elseif ($modelName === 'CashRegisterMovement') {
                                $accountName = $subject->account->name ?? 'Caja';
                                $subjectDetails = ' - Cuenta: ' . $accountName . ' - ' . ($subject->description ?? '') . ' ($' . number_format($subject->amount ?? 0, 2, ',', '.') . ')';
                            } elseif ($modelName === 'Account') {
                                $subjectDetails = ' - ' . ($subject->name ?? '') . ' (Saldo Inicial: $' . number_format($subject->initial_balance ?? 0, 2, ',', '.') . ')';
                            } elseif ($modelName === 'CollectionPayment') {
                                $subjectDetails = ' - ' . ($subject->collection->lease->property->location ?? 'Cobro') . ' ($' . number_format($subject->amount ?? 0, 2, ',', '.') . ')';
                            } elseif ($modelName === 'SettlementPayment') {
                                $subjectDetails = ' - ' . ($subject->settlement->owner->name ?? 'Liquidación') . ' ($' . number_format($subject->amount ?? 0, 2, ',', '.') . ')';
                            } elseif ($modelName === 'Objective') {
                                $employeeName = ($subject->employee->name ?? '') . ' ' . ($subject->employee->last_name ?? '');
                                $subjectDetails = ' - ' . ($subject->title ?? '') . ' (' . $employeeName . ')';
                            } elseif ($modelName === 'ObjectiveComment') {
                                $employeeName = ($subject->objective->employee->name ?? '') . ' ' . ($subject->objective->employee->last_name ?? '');
                                $subjectDetails = ' - ' . ($subject->objective->title ?? '') . ' (' . $employeeName . ')';
                            } elseif ($modelName === 'Employee') {
                                $subjectDetails = ' - ' . ($subject->name ?? '') . ' ' . ($subject->last_name ?? '');
                            } elseif ($modelName === 'Attendance') {
                                $employeeName = ($subject->employee->name ?? '') . ' ' . ($subject->employee->last_name ?? '');
                                $dateStr = $subject->date ? \Carbon\Carbon::parse($subject->date)->format('d/m') : '';
                                $subjectDetails = ' - ' . $employeeName . ' (' . $dateStr . ')';
                            } elseif ($modelName === 'AbsenceReason') {
                                $subjectDetails = ' - ' . ($subject->name ?? '');
                            }
                        } else {
                            // Fallback dinámico si el elemento fue eliminado (para poder mostrar la información histórica guardada en properties)
                            $attrs = $activity->properties['attributes'] ?? [];
                            if (!empty($attrs)) {
                                if (isset($attrs['location'])) {
                                    $subjectDetails = ' - ' . $attrs['location'];
                                } elseif (isset($attrs['name'])) {
                                    $subjectDetails = ' - ' . $attrs['name'] . (isset($attrs['last_name']) ? ' ' . $attrs['last_name'] : '');
                                } elseif (isset($attrs['holder_name'])) {
                                    $subjectDetails = ' - Titular: ' . $attrs['holder_name'];
                                } elseif (isset($attrs['description'])) {
                                    $subjectDetails = ' - ' . $attrs['description'] . (isset($attrs['amount']) ? ' ($' . number_format($attrs['amount'], 2, ',', '.') . ')' : '');
                                }
                            }
                        }
                    @endphp
                    <div style="position: relative; margin-bottom: 1.5rem;">
                        <div style="position: absolute; left: -31px; top: 0; width: 20px; height: 20px; border-radius: 50%; background: {{ $map['bg'] }}; color: {{ $map['color'] }}; display: flex; align-items: center; justify-content: center; border: 2px solid white;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">{!! $map['icon'] !!}</svg>
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 500; color: var(--text-main); line-height: 1.4;">
                            <span style="font-weight: 600; color: #2d3748;">{{ $translatedAction }}</span>
                            <span style="color: {{ $map['color'] }}; font-weight: 700;">{{ $translatedModel }}</span>
                            <span style="color: #4a5568; font-weight: 500; font-size: 0.85rem;">{!! e($subjectDetails) !!}</span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 0.25rem; font-weight: 500;">
                            {{ $activity->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 1.5rem 0; color: var(--text-light);">
                <p>No hay actividad registrada aún.</p>
            </div>
        @endif
    </div>
</div>
@else
<div class="card" style="padding: 3rem; text-align: center; background: white; border-radius: 12px; margin-top: 1rem; border-left: 4px solid var(--accent-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <h2 style="color: var(--primary-color); font-weight: 700; margin-bottom: 1rem;">Bienvenido/a a Habitar, {{ Auth::user()->name }}</h2>
    <p style="color: var(--text-light); font-size: 1.05rem; max-width: 600px; margin: 0 auto 1.5rem; line-height: 1.6;">
        Tu ingreso diario se encuentra habilitado para registrarse. Podés marcar tu presente o avisar ausencias desde el panel superior en esta misma pantalla.
    </p>
    <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">
        Utilizá el menú lateral izquierdo para acceder a las secciones autorizadas para tu puesto de trabajo.
    </div>
</div>
@endcan
@endsection
