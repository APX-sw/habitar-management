<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Patrimonial - Habitar</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2d3748;
            --accent-color: #0077b6; /* Habitar Teal/Blue */
            --text-main: #4a5568;
            --text-light: #718096;
            --bg-light: #f8fafc;
            --border-color: #edf2f7;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
            background: #e2e8f0;
            margin: 0;
            padding: 2rem;
            -webkit-font-smoothing: antialiased;
        }

        .controls {
            max-width: 800px;
            margin: 0 auto 2rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            font-family: inherit;
        }
        
        .btn-secondary {
            background: #edf2f7;
            color: var(--primary-color);
        }

        .page {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 3.5rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            border-radius: 4px; /* Slight rounding for screen, flat for print */
            margin-bottom: 3rem;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 2rem;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: -0.05em;
        }

        .brand-logo span {
            color: var(--accent-color);
        }

        .report-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-light);
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .owner-name {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin: 0 0 0.5rem 0;
            line-height: 1.1;
        }

        .period-badge {
            background: #ebf8ff;
            color: #2b6cb0;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
            border: 1px solid #bee3f8;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .kpi-card {
            background: var(--bg-light);
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .kpi-card.highlight {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a202c 100%);
            color: white;
            border: none;
        }

        .kpi-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-light);
        }

        .highlight .kpi-label {
            color: #a0aec0;
        }

        .kpi-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            line-height: 1;
        }

        .highlight .kpi-value {
            color: white;
        }

        .kpi-value.accent {
            color: var(--accent-color);
        }

        .kpi-desc {
            font-size: 0.85rem;
            margin-top: 0.8rem;
            line-height: 1.4;
        }

        .highlight .kpi-desc {
            color: #cbd5e0;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin: 0 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .property-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .property-item {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .prop-address {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
        }

        .prop-city {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-rented {
            background: #f0fff4;
            color: #2f855a;
            border: 1px solid #9ae6b4;
        }

        .status-vacant {
            background: #fff5f5;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        .footer {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
        }

        /* --- Estilos específicos para IMPRESIÓN / PDF --- */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .controls {
                display: none !important;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 2cm;
                border-radius: 0;
                width: 100%;
                max-width: none;
                page-break-after: always;
                box-sizing: border-box;
            }
            /* Remove last page break to avoid empty blank page at the end */
            .page:last-of-type {
                page-break-after: avoid;
            }
            
            /* Asegurar que los fondos de color se impriman (Chrome/Edge/Safari) */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    @if(!($isPublic ?? false))
        <div class="controls">
            <div>
                <div style="font-weight: 700; color: var(--primary-color);">Dossiers Generados: {{ count($ownersData) }}</div>
                <div style="font-size: 0.85rem; color: var(--text-light);">Listo para imprimir o guardar como PDF.</div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('reports.show', $report) }}" class="btn btn-secondary">Volver</a>
                <button onclick="window.print()" class="btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Imprimir / PDF
                </button>
            </div>
        </div>
    @endif

    @forelse($ownersData as $data)
        <div class="page">
            <div class="header">
                <div>
                    <div class="report-title">Resumen de Gestión Patrimonial</div>
                    <h1 class="owner-name">{{ $data['owner']->name }}</h1>
                    <div style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 1rem;">DNI/CUIT: {{ $data['owner']->dni_cuit }}</div>
                    <div class="period-badge">Período: {{ $periodLabel }}</div>
                </div>
                <div class="brand-logo">
                    Habit<span>ar</span>
                </div>
            </div>

            <div style="margin-bottom: 2rem; font-size: 1.05rem; line-height: 1.6; color: var(--primary-color);">
                Estimado/a <strong>{{ $data['owner']->name }}</strong>,<br>
                A continuación presentamos el balance de nuestra gestión sobre su patrimonio inmobiliario durante el período seleccionado. Nuestro objetivo es brindarle tranquilidad, rentabilidad y un servicio transparente.
            </div>

            <div class="kpi-grid">
                <!-- Ganancia Neta -->
                <div class="kpi-card highlight">
                    <div class="kpi-label">Rendimiento Neto Transferido</div>
                    <div class="kpi-value">${{ number_format($data['totalNetIncome'], 2) }}</div>
                    <div class="kpi-desc">Monto total libre de gastos y honorarios que fue depositado directamente en sus cuentas bancarias durante este período ({{ $data['settlementsCount'] }} liquidaciones).</div>
                </div>

                <!-- Gastos Gestionados -->
                <div class="kpi-card">
                    <div class="kpi-label">Problemas y Pagos Gestionados</div>
                    <div class="kpi-value accent">${{ number_format($data['totalExpensesManaged'], 2) }}</div>
                    <div class="kpi-desc">Capital administrado por Habitar para el pago de impuestos, mantenimientos y resoluciones técnicas de sus propiedades, ahorrándole a usted el trámite.</div>
                </div>
            </div>

            <div class="section-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Estado Patrimonial
            </div>
            
            <div class="property-list">
                @forelse($data['properties'] as $prop)
                    <div class="property-item">
                        <div>
                            <div class="prop-address">{{ $prop->location }}</div>
                            <div class="prop-city">{{ $prop->city->name ?? 'Ciudad' }}, {{ $prop->province->name ?? 'Provincia' }} • {{ $prop->rooms }} amb.</div>
                            
                            @if($prop->activeLease)
                                <div style="margin-top: 0.8rem; font-size: 0.85rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    Inquilino: <strong>{{ $prop->activeLease->tenant->name }}</strong>
                                </div>
                                <div style="margin-top: 0.2rem; font-size: 0.85rem; color: var(--text-light); padding-left: 1.4rem;">
                                    Contrato vigente hasta {{ \Carbon\Carbon::parse($prop->activeLease->end_date)->format('d/m/Y') }}
                                </div>
                            @else
                                <div style="margin-top: 0.8rem; font-size: 0.85rem; color: var(--text-light);">
                                    Actualmente sin contrato de alquiler activo.
                                </div>
                            @endif
                        </div>
                        <div>
                            @if($prop->activeLease)
                                <span class="status-badge status-rented">Alquilada</span>
                            @else
                                <span class="status-badge status-vacant">Vacante</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--text-light); border: 1px dashed var(--border-color); border-radius: 10px;">
                        No posee propiedades registradas actualmente.
                    </div>
                @endforelse
            </div>

            <div class="footer">
                Generado por el sistema de administración <strong>Habitar</strong> el {{ now()->format('d/m/Y') }}.<br>
                Para cualquier consulta sobre este reporte, por favor comuníquese con nuestra administración.
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 4rem; background: white; border-radius: 12px;">
            <h2>No se seleccionaron propietarios o no hay datos.</h2>
            <a href="{{ route('reports.show', $report) }}" class="btn">Volver</a>
        </div>
    @endforelse

</body>
</html>
