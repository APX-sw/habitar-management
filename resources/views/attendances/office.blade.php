@extends('layouts.app')

@section('title', '| Tablero de Oficina')

@section('content')
<div style="font-family: 'Outfit', 'Inter', sans-serif;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700; margin: 0;">Tablero Oficina</h1>
            <p style="color: var(--text-light); font-size: 0.95rem; margin: 0.25rem 0 0 0;">Monitoreá quién está presencial hoy o en cualquier otra fecha.</p>
        </div>
        
        <!-- Date Navigation -->
        <div class="card" style="padding: 0.5rem 1rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem; margin: 0;">
            <a href="{{ route('attendances.office', ['date' => $date->clone()->subDay()->format('Y-m-d')]) }}" class="btn" style="background: var(--secondary-color); color: var(--text-main); padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.9rem; border: 1px solid #e2e8f0;" title="Día Anterior">
                &lt; Anterior
            </a>
            
            <form action="{{ route('attendances.office') }}" method="GET" style="display: flex; align-items: center; margin: 0;">
                <input type="date" name="date" value="{{ $formattedDate }}" onchange="this.form.submit()" style="padding: 0.4rem 0.8rem; font-size: 0.95rem; border: 1px solid #cbd5e0; border-radius: 8px; font-weight: 600; color: var(--primary-color);">
            </form>
            
            <a href="{{ route('attendances.office', ['date' => $date->clone()->addDay()->format('Y-m-d')]) }}" class="btn" style="background: var(--secondary-color); color: var(--text-main); padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.9rem; border: 1px solid #e2e8f0;" title="Día Siguiente">
                Siguiente &gt;
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="border-left: 4px solid #718096; padding: 1rem 1.25rem; border-radius: 12px;">
            <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-light); font-weight: 600;">Sin Registro</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #2d3748; margin-top: 0.25rem;">{{ count($pendientes) }}</div>
        </div>
        <div class="card" style="border-left: 4px solid #38a169; padding: 1rem 1.25rem; border-radius: 12px;">
            <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-light); font-weight: 600;">En la Oficina</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #276749; margin-top: 0.25rem;">{{ count($enOficina) }}</div>
        </div>
        <div class="card" style="border-left: 4px solid #3182ce; padding: 1rem 1.25rem; border-radius: 12px;">
            <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-light); font-weight: 600;">Ya se retiraron</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #2b6cb0; margin-top: 0.25rem;">{{ count($retirados) }}</div>
        </div>
        <div class="card" style="border-left: 4px solid #e53e3e; padding: 1rem 1.25rem; border-radius: 12px;">
            <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-light); font-weight: 600;">Ausentes</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #9b2c2c; margin-top: 0.25rem;">{{ count($ausentes) }}</div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; align-items: start;">
        
        <!-- Column: Pendientes -->
        <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; min-height: 480px; display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.01);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 2px solid #cbd5e0; padding-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #4a5568;">Sin Registro</h3>
                <span style="background: #edf2f7; color: #4a5568; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px;">{{ count($pendientes) }}</span>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.8rem; flex: 1;">
                @forelse($pendientes as $item)
                    <div class="card" style="border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.01); padding: 1rem; border-radius: 12px; background: white;">
                        <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem; font-weight: 700; color: #2d3748;">{{ $item['employee']->full_name }}</h4>
                        <span style="font-size: 0.8rem; color: #a0aec0; display: inline-flex; align-items: center; gap: 0.25rem;">
                            <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Aún no marcó
                        </span>
                    </div>
                @empty
                    <div style="margin: auto 0; text-align: center; color: #a0aec0; font-size: 0.9rem; padding: 2rem 0;">
                        Todos los empleados marcados
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column: En la Oficina -->
        <div style="background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 16px; padding: 1.25rem; min-height: 480px; display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.01);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 2px solid #9ae6b4; padding-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #22543d;">En la Oficina</h3>
                <span style="background: #c6f6d5; color: #22543d; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px;">{{ count($enOficina) }}</span>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.8rem; flex: 1;">
                @forelse($enOficina as $item)
                    <div class="card" style="border: 1px solid #c6f6d5; box-shadow: 0 2px 4px rgba(0,0,0,0.01); padding: 1rem; border-radius: 12px; background: white;">
                        <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem; font-weight: 700; color: #22543d;">{{ $item['employee']->full_name }}</h4>
                        <span style="font-size: 0.8rem; color: #38a169; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600;">
                            <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                            Ingreso: {{ \Carbon\Carbon::parse($item['attendance']->created_at)->setTimezone('America/Argentina/Buenos_Aires')->format('H:i') }}
                        </span>
                    </div>
                @empty
                    <div style="margin: auto 0; text-align: center; color: #9ae6b4; font-size: 0.9rem; padding: 2rem 0;">
                        Nadie en la oficina aún
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column: Ya se retiraron -->
        <div style="background: #ebf8ff; border: 1px solid #bee3f8; border-radius: 16px; padding: 1.25rem; min-height: 480px; display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.01);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 2px solid #90cdf4; padding-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #2b6cb0;">Ya se retiraron</h3>
                <span style="background: #bee3f8; color: #2b6cb0; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px;">{{ count($retirados) }}</span>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.8rem; flex: 1;">
                @forelse($retirados as $item)
                    <div class="card" style="border: 1px solid #bee3f8; box-shadow: 0 2px 4px rgba(0,0,0,0.01); padding: 1rem; border-radius: 12px; background: white;">
                        <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem; font-weight: 700; color: #2b6cb0;">{{ $item['employee']->full_name }}</h4>
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <span style="font-size: 0.8rem; color: #4a5568; display: inline-flex; align-items: center; gap: 0.25rem;">
                                Ingreso: {{ \Carbon\Carbon::parse($item['attendance']->created_at)->setTimezone('America/Argentina/Buenos_Aires')->format('H:i') }}
                            </span>
                            <span style="font-size: 0.8rem; color: #3182ce; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600;">
                                <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                Salida: {{ \Carbon\Carbon::parse($item['attendance']->check_out)->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="margin: auto 0; text-align: center; color: #bee3f8; font-size: 0.9rem; padding: 2rem 0;">
                        Ningún egreso registrado
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column: Ausentes -->
        <div style="background: #fff5f5; border: 1px solid #fed7d7; border-radius: 16px; padding: 1.25rem; min-height: 480px; display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.01);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 2px solid #feb2b2; padding-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #9b2c2c;">Ausentes</h3>
                <span style="background: #fed7d7; color: #9b2c2c; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px;">{{ count($ausentes) }}</span>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.8rem; flex: 1;">
                @forelse($ausentes as $item)
                    <div class="card" style="border: 1px solid #fed7d7; box-shadow: 0 2px 4px rgba(0,0,0,0.01); padding: 1rem; border-radius: 12px; background: white;">
                        <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem; font-weight: 700; color: #9b2c2c;">{{ $item['employee']->full_name }}</h4>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                            <span style="background: #fed7d7; color: #9b2c2c; font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 4px; display: inline-block; align-self: start; text-transform: uppercase;">
                                {{ optional($item['attendance']->absenceReason)->name ?? 'Motivo no especificado' }}
                            </span>
                            @if($item['attendance']->notes)
                                <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: #718096; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $item['attendance']->notes }}">
                                    "{{ $item['attendance']->notes }}"
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="margin: auto 0; text-align: center; color: #feb2b2; font-size: 0.9rem; padding: 2rem 0;">
                        No hay ausentes reportados
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
