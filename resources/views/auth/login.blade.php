@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@push('styles')
<style>
    /* Full height centering specifically for login */
    body {
        justify-content: center;
        align-items: center;
        margin: 0;
        padding: 0;
    }
    
    .main-content {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 1rem;
        width: 100%;
        height: 100vh;
    }

    .login-container {
        width: 100%;
        max-width: 420px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .login-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }

    .login-header h1 span {
        color: var(--primary-color);
    }

    .login-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .form-submit {
        margin-top: 2rem;
    }

    .btn-login {
        width: 100%;
        padding: 0.875rem;
        font-size: 1rem;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
        color: white;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        font-weight: 600;
        box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
    }

    .error-alert {
        background-color: rgba(239, 68, 68, 0.1);
        border-left: 4px solid var(--danger-color);
        color: #fca5a5;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
        font-size: 0.875rem;
    }

    /* Abstract shapes behind the glass */
    .shape {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        z-index: -1;
    }

    .shape-1 {
        width: 200px;
        height: 200px;
        background: rgba(59, 130, 246, 0.4);
        top: -50px;
        left: -50px;
    }

    .shape-2 {
        width: 150px;
        height: 150px;
        background: rgba(139, 92, 246, 0.3);
        bottom: -20px;
        right: -20px;
    }
</style>
@endpush

@section('content')
    <!-- Background glow elements -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="login-container glass-panel">
        <div class="login-header">
            <h1>Habitar<span>.</span></h1>
            <p>Ingresa tus credenciales para acceder</p>
        </div>

        @if ($errors->any())
            <div class="error-alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="admin@habitar.com.ar"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    required
                    placeholder="••••••••"
                >
            </div>

            <div class="form-submit">
                <button type="submit" class="btn-login">
                    Iniciar Sesión
                </button>
            </div>
        </form>
    </div>
@endsection
