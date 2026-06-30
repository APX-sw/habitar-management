@extends('layouts.app')

@section('title', '| Editar Propietario')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="color: var(--primary-color); font-size: 2.4rem; font-weight: 700; letter-spacing: -0.025em; margin: 0;">Editar Propietario</h1>
            <p style="color: var(--text-light); margin-top: 0.4rem; font-size: 1.1rem;">{{ $owner->name }} • ID: #{{ $owner->id }}</p>
        </div>
        <a href="{{ route('owners.index') }}" class="btn-back" style="text-decoration: none; display: flex; align-items: center; gap: 0.5rem; color: var(--accent-color); font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 10px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Volver
        </a>
    </div>

    <form action="{{ route('owners.update', $owner) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            <!-- Main Data Card -->
            <div class="card" style="padding: 2.5rem; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.03); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--accent-gradient);"></div>
                
                <h3 style="margin-bottom: 2rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 32px; height: 32px; background: rgba(56, 178, 172, 0.1); color: var(--accent-color); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    Información General
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.6rem; font-weight: 600; font-size: 0.9rem; color: var(--text-main);">Nombre / Razón Social</label>
                        <input type="text" name="name" value="{{ $owner->name }}" required class="form-control-premium">
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.6rem; font-weight: 600; font-size: 0.9rem; color: var(--text-main);">DNI / CUIT</label>
                        <input type="text" name="dni_cuit" value="{{ $owner->dni_cuit }}" required class="form-control-premium">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.6rem; font-weight: 600; font-size: 0.9rem; color: var(--text-main);">Email de Contacto</label>
                        <input type="email" name="email" value="{{ $owner->email }}" class="form-control-premium" placeholder="ejemplo@correo.com">
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.6rem; font-weight: 600; font-size: 0.9rem; color: var(--text-main);">Comisión Inmobiliaria (%)</label>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="number" step="0.1" name="commission_percentage" value="{{ $owner->commission_percentage ?? 10 }}" class="form-control-premium" style="width: 100px; text-align: center; font-weight: 700; color: var(--accent-color);">
                            <span style="font-weight: 700; color: var(--text-light);">%</span>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.6rem; font-weight: 600; font-size: 0.9rem; color: var(--text-main);">Teléfono</label>
                        <input type="text" name="phone" value="{{ $owner->phone }}" class="form-control-premium" placeholder="+54 ...">
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.6rem; font-weight: 600; font-size: 0.9rem; color: var(--text-main);">Notas Adicionales</label>
                        <textarea name="contact" rows="1" class="form-control-premium" style="resize: none;" placeholder="Cualquier información relevante sobre el propietario...">{{ $owner->contact }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Bank Accounts Card -->
            <div class="card" style="padding: 2.5rem; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.03); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: #4299E1;"></div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h3 style="color: var(--primary-color); display: flex; align-items: center; gap: 0.75rem; margin: 0;">
                        <div style="width: 32px; height: 32px; background: rgba(66, 153, 225, 0.1); color: #4299E1; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                        </div>
                        Cuentas para Rendiciones
                    </h3>
                    <button type="button" onclick="addBankAccount()" class="btn-add-account">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Agregar Cuenta
                    </button>
                </div>

                <div id="bank-accounts-container" style="display: grid; gap: 1rem;">
                    @foreach($owner->bankAccounts as $index => $account)
                        <div class="bank-account-card">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label class="label-small">CBU / ALIAS</label>
                                    <input type="text" name="bank_accounts[{{ $index }}][cbu_alias]" value="{{ $account->cbu_alias }}" required class="form-control-compact">
                                </div>
                                <div>
                                    <label class="label-small">TITULAR</label>
                                    <input type="text" name="bank_accounts[{{ $index }}][holder_name]" value="{{ $account->holder_name }}" required class="form-control-compact">
                                </div>
                            </div>
                            <div style="display: flex; align-items: flex-end; gap: 1rem;">
                                <div style="flex: 1;">
                                    <label class="label-small">CUIT TITULAR</label>
                                    <input type="text" name="bank_accounts[{{ $index }}][holder_cuit]" value="{{ $account->holder_cuit }}" required class="form-control-compact">
                                </div>
                                <button type="button" onclick="this.closest('.bank-account-card').remove()" class="btn-delete-row">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E53E3E" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($owner->bankAccounts->isEmpty())
                    <div id="no-accounts-message" style="text-align: center; padding: 2rem; background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; color: var(--text-light);">
                        <p>No hay cuentas bancarias registradas. Son necesarias para realizar las rendiciones.</p>
                    </div>
                @endif
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 3rem;">
            <a href="{{ route('owners.index') }}" class="btn-secondary-premium">Cancelar</a>
            <button type="submit" class="btn-save-premium">
                Actualizar Propietario
            </button>
        </div>
    </form>
</div>

<style>
    .form-control-premium {
        width: 100%;
        padding: 0.85rem 1rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #fdfdfd;
        font-size: 1rem;
        transition: all 0.2s;
        color: var(--text-main);
    }
    .form-control-premium:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px rgba(56, 178, 172, 0.1);
        background: white;
    }

    .form-control-compact {
        width: 100%;
        padding: 0.6rem 0.8rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .form-control-compact:focus {
        outline: none;
        border-color: #4299E1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }

    .label-small {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-light);
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .bank-account-card {
        background: white;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.3s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .bank-account-card:hover {
        border-color: #4299E1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .btn-add-account {
        background: #ebf8ff;
        color: #2b6cb0;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-add-account:hover {
        background: #bee3f8;
        transform: translateY(-1px);
    }

    .btn-delete-row {
        background: #fff5f5;
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete-row:hover {
        background: #fed7d7;
        transform: scale(1.05);
    }

    .btn-save-premium {
        background: var(--accent-gradient);
        color: white;
        border: none;
        padding: 1rem 3rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 14px rgba(49, 151, 149, 0.4);
    }
    .btn-save-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(49, 151, 149, 0.5);
    }

    .btn-secondary-premium {
        background: white;
        color: var(--text-light);
        text-decoration: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .btn-secondary-premium:hover {
        background: #f8fafc;
        color: var(--text-main);
        border-color: #cbd5e0;
    }

    .btn-back:hover {
        background: var(--bg-body) !important;
        transform: translateX(-3px);
    }
</style>

<script>
    let accountCount = {{ $owner->bankAccounts->count() }};
    
    function addBankAccount() {
        const noMsg = document.getElementById('no-accounts-message');
        if(noMsg) noMsg.style.display = 'none';

        const container = document.getElementById('bank-accounts-container');
        const div = document.createElement('div');
        div.className = 'bank-account-card';
        div.style.animation = 'slideIn 0.3s ease-out';
        
        div.innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="label-small">CBU / ALIAS</label>
                    <input type="text" name="bank_accounts[${accountCount}][cbu_alias]" required class="form-control-compact">
                </div>
                <div>
                    <label class="label-small">TITULAR</label>
                    <input type="text" name="bank_accounts[${accountCount}][holder_name]" required class="form-control-compact">
                </div>
            </div>
            <div style="display: flex; align-items: flex-end; gap: 1rem;">
                <div style="flex: 1;">
                    <label class="label-small">CUIT TITULAR</label>
                    <input type="text" name="bank_accounts[${accountCount}][holder_cuit]" required class="form-control-compact">
                </div>
                <button type="button" onclick="this.closest('.bank-account-card').remove(); checkEmpty()" class="btn-delete-row">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E53E3E" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        `;
        container.appendChild(div);
        accountCount++;
    }

    function checkEmpty() {
        const container = document.getElementById('bank-accounts-container');
        const noMsg = document.getElementById('no-accounts-message');
        if (container.children.length === 0 && noMsg) {
            noMsg.style.display = 'block';
        }
    }
</script>

<style>
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

