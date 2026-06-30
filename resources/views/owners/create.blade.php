@extends('layouts.app')

@section('title', '| Nuevo Propietario')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('owners.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver al listado
        </a>
        <h1 style="color: var(--primary-color);">Cargar Nuevo Propietario</h1>
    </div>

    <div class="card">
        <form action="{{ route('owners.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre / Razón Social</label>
                    <input type="text" name="name" required style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">DNI / CUIT</label>
                    <input type="text" name="dni_cuit" required style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                    <input type="email" name="email" style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Teléfono</label>
                    <input type="text" name="phone" style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Notas de Contacto</label>
                    <textarea name="contact" rows="2" style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color);"></textarea>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Comisión Inmobiliaria (%)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" step="0.1" name="commission_percentage" value="10" style="width: 100%; padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid var(--secondary-color); font-weight: 700;">
                        <span style="font-weight: 700;">%</span>
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--secondary-color); padding-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="color: var(--primary-color); font-size: 1.1rem;">Cuentas Bancarias</h3>
                    <button type="button" onclick="addBankAccount()" class="btn" style="background: var(--secondary-color); color: var(--primary-color); font-size: 0.85rem;">+ Agregar Cuenta</button>
                </div>
                
                <div id="bank-accounts-container">
                    <!-- Template for first account -->
                    <div style="background: var(--bg-body); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;" class="bank-account-row">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.5rem;">
                            <input type="text" name="bank_accounts[0][cbu_alias]" placeholder="CBU / Alias" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                            <input type="text" name="bank_accounts[0][holder_name]" placeholder="Nombre del Titular" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 40px; gap: 1rem;">
                            <input type="text" name="bank_accounts[0][holder_cuit]" placeholder="CUIT del Titular" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="{{ route('owners.index') }}" class="btn" style="background: var(--secondary-color);">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Propietario</button>
            </div>
        </form>
    </div>
</div>

<script>
    let accountCount = 1;
    function addBankAccount() {
        const container = document.getElementById('bank-accounts-container');
        const div = document.createElement('div');
        div.style.background = 'var(--bg-body)';
        div.style.padding = '1rem';
        div.style.borderRadius = '8px';
        div.style.marginBottom = '1rem';
        div.innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.5rem;">
                <input type="text" name="bank_accounts[${accountCount}][cbu_alias]" placeholder="CBU / Alias" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                <input type="text" name="bank_accounts[${accountCount}][holder_name]" placeholder="Nombre del Titular" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 40px; gap: 1rem;">
                <input type="text" name="bank_accounts[${accountCount}][holder_cuit]" placeholder="CUIT del Titular" required style="width: 100%; padding: 0.6rem; border-radius: 4px; border: 1px solid var(--secondary-color);">
                <button type="button" onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #C53030; cursor: pointer; font-size: 1.2rem;">&times;</button>
            </div>
        `;
        container.appendChild(div);
        accountCount++;
    }
</script>
@endsection
