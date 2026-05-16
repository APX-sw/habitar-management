@extends('layouts.app')

@section('title', '| Configuración de Contacto')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2.5rem;">
        <a href="{{ route('settings.index') }}" style="text-decoration: none; color: var(--text-light); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver a Configuración
        </a>
        <h1 style="color: var(--primary-color);">Información de Contacto</h1>
        <p style="color: var(--text-light);">Define el número de contacto de la inmobiliaria para recibir comprobantes.</p>
    </div>

    <div class="card" style="padding: 2.5rem;">
        <form action="{{ route('settings.contact.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 2rem;">
                <label style="font-size: 1.1rem; margin-bottom: 1rem;">Número de WhatsApp de la Inmobiliaria</label>
                <div style="display: flex; gap: 1rem;">
                    <div style="background: #E6FFFA; color: #319795; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <input type="text" name="whatsapp_number" value="{{ $whatsapp }}" required placeholder="Ej: 5493412345678" style="font-size: 1.2rem; font-weight: 700; letter-spacing: 0.05em;">
                </div>
                <p style="font-size: 0.85rem; color: var(--text-light); margin-top: 0.8rem;">
                    <strong>Nota:</strong> Ingresar el número completo sin espacios ni símbolos (incluir código de país y área). <br>
                    Este número se utilizará para generar el enlace automático de "Enviar Comprobante por WhatsApp" en los correos electrónicos.
                </p>
            </div>

            <div style="background: #ebf8ff; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; border-left: 5px solid #4299e1;">
                <h4 style="color: #2b6cb0; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    Previsualización del enlace
                </h4>
                <p style="font-size: 0.85rem; color: #2c5282;">
                    El mail dirá: "Enviar comprobante a: <a href="#" style="color: #3182ce; font-weight: 700; text-decoration: underline;">WhatsApp Habitar</a>"
                </p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Guardar Cambios</button>
        </form>
    </div>
</div>
@endsection
