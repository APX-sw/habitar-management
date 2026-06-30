@extends('layouts.app')

@section('title', '| Generar Sueldos')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('salaries.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver
        </a>
        <h1 style="color: var(--primary-color);">Generar Sueldos Mensuales</h1>
    </div>

    <form id="period-selector" action="{{ route('salaries.create') }}" method="GET" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end; background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--secondary-color);">
        <div style="flex: 1;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Seleccionar Mes</label>
            <select name="month" onchange="this.form.submit()" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                @php
                    $months = [1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 
                               7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'];
                @endphp
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 1;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Seleccionar Año</label>
            <select name="year" onchange="this.form.submit()" style="width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid var(--secondary-color);">
                @for($y=date('Y')+1; $y>=2024; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>

    <form action="{{ route('salaries.store_period') }}" method="POST">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">

        <div class="card">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Empleados Activos para {{ $months[$month] }} {{ $year }}</h3>
            
            @if($employees->isEmpty())
                <p style="text-align: center; color: var(--text-light); padding: 2rem;">No hay empleados activos para generar sueldos.</p>
            @else
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid var(--secondary-color);">
                            <th style="padding: 1rem;">Empleado</th>
                            <th style="padding: 1rem;">Sueldo Base (Base)</th>
                            <th style="padding: 1rem;">Actualización</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr style="border-bottom: 1px solid var(--secondary-color);">
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 600;">{{ $employee->full_name }}</div>
                                    <div style="font-size: 0.85rem; color: var(--text-light);">{{ $employee->job_title ?? 'Sin Puesto' }}</div>
                                </td>
                                <td style="padding: 1rem; font-weight: 700; color: var(--accent-color);">
                                    ${{ number_format($employee->base_salary, 2, ',', '.') }}
                                </td>
                                <td style="padding: 1rem; font-size: 0.85rem; color: var(--text-light);">
                                    @if($employee->update_type)
                                        {{ $employee->update_type === 'fixed' ? 'Fija' : 'Indexada' }} (cada {{ $employee->update_frequency_months }} meses)
                                    @else
                                        Sin actualizaciones programadas
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">Generar Borradores de Sueldo</button>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
