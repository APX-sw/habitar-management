@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-4 text-center">
                    <h3 class="mb-0" style="color: var(--primary-color); font-weight: 700;">Apertura de Caja</h3>
                    <p class="text-muted mt-2 mb-0">Indica el dinero con el que arranca físicamente la caja en este momento.</p>
                </div>
                <div class="card-body p-4 bg-light">
                    <form action="{{ route('cash-register-closures.open') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label" style="font-weight: 600; color: #4a5568;">Físico Inicial en Caja ($)</label>
                            <input type="number" step="0.01" min="0" name="initial_balance" value="{{ $recommendedBalance ?? 0 }}" class="form-control form-control-lg text-end" required placeholder="0.00" style="font-size: 1.5rem; font-weight: 700;">
                            <small class="text-muted d-block mt-2">Este será el monto inicial con el que arrancará tu día de arqueo. A este monto se le sumarán los ingresos y restarán los egresos que generes hoy.</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" style="font-weight: 700; border-radius: 8px;">
                                <i class="fas fa-lock-open me-2"></i> Abrir Caja y Comenzar Turno
                            </button>
                            <a href="{{ route('cash-register-closures.index') }}" class="btn btn-link text-muted">Cancelar y volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
