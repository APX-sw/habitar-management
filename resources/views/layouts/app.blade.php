<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Habitar') }} @yield('title')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Base Styles -->
    <style>
        :root {
            --primary-color: #1a202c;
            --sidebar-width: 260px;
            --accent-color: #38B2AC;
            --accent-gradient: linear-gradient(135deg, #38B2AC 0%, #319795 100%);
            --secondary-color: #edf2f7;
            --text-main: #2d3748;
            --text-light: #718096;
            --bg-body: #f7fafc;
            --bg-card: #ffffff;
            --border-radius: 12px;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        /* Form Styles */
        input, textarea, select {
            width: 100% !important;
            padding: 0.8rem 1rem !important;
            border-radius: 10px !important;
            border: 2px solid #e2e8f0 !important;
            background-color: #ffffff !important;
            color: var(--text-main) !important;
            font-size: 0.95rem !important;
            transition: all var(--transition-speed) !important;
            outline: none !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02) inset;
        }

        input:focus, textarea:focus, select:focus {
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 4px rgba(56, 178, 172, 0.1), 0 2px 4px rgba(0,0,0,0.02) inset !important;
            background-color: #fff !important;
        }

        input::placeholder, textarea::placeholder {
            color: #a0aec0 !important;
        }

        label {
            display: block !important;
            margin-bottom: 0.6rem !important;
            font-weight: 600 !important;
            color: var(--primary-color) !important;
            font-size: 0.9rem !important;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar-brand {
            display: block;
            width: 100%;
            margin-bottom: 1.5rem;
            text-decoration: none;
            padding: 2rem 1.5rem 1rem;
        }

        .sidebar-brand img {
            width: 100%;
            height: auto;
            max-height: 80px;
            display: block;
            object-fit: contain;
        }

        .sidebar-nav {
            list-style: none;
            flex: 1;
            padding: 0 1.5rem;
        }

        .sidebar-item {
            margin-bottom: 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 1rem;
            color: #a0aec0;
            text-decoration: none;
            font-weight: 500;
            border-radius: 10px;
            transition: all var(--transition-speed);
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }

        .sidebar-link.active {
            background: var(--accent-gradient);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(56, 178, 172, 0.3);
        }

        .sidebar-link svg {
            width: 20px;
            height: 20px;
            opacity: 0.8;
        }

        .sidebar-link.active svg {
            opacity: 1;
        }

        /* Main Content Container */
        .app-container {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-bar {
            background: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-bottom: 1px solid #edf2f7;
        }

        .main-content {
            padding: 2.5rem;
            flex: 1;
        }

        /* Global Components */
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px rgba(0,0,0,0.02), 0 1px 3px rgba(0,0,0,0.05);
            padding: 1.5rem;
            border: 1px solid #edf2f7;
        }

        .btn {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed);
            border: none;
        }

        .btn-primary {
            background: var(--accent-gradient);
            color: white;
            box-shadow: 0 4px 10px rgba(56, 178, 172, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(56, 178, 172, 0.3);
        }

        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

    </style>
    @yield('styles')
</head>
<body>
    <aside class="sidebar">
        <a href="/" class="sidebar-brand">
            <img src="{{ asset('img/logo.png') }}" alt="Habitar">
        </a>

        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="/" class="sidebar-link {{ request()->is('/') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Tablero
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('properties.index') }}" class="sidebar-link {{ request()->is('properties*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                    Propiedades
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('owners.index') }}" class="sidebar-link {{ request()->is('owners*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Propietarios
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('tenants.index') }}" class="sidebar-link {{ request()->is('tenants*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Inquilinos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('leases.index') }}" class="sidebar-link {{ request()->is('leases*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Contratos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('collections.index') }}" class="sidebar-link {{ request()->is('collections*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    Cobros
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('cash_register.index') }}" class="sidebar-link {{ request()->is('cash-register*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    Caja
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->is('expenses*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Gastos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('settlements.index') }}" class="sidebar-link {{ request()->is('settlements*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Rendiciones
                </a>
            </li>

            <li class="sidebar-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
                <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->is('settings*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Configuración
                </a>
            </li>
        </ul>

        <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05);">
            <div style="display: flex; align-items: center; gap: 0.8rem; padding: 0.5rem;">
                <div style="background: #4a5568; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div style="overflow: hidden;">
                    <div style="font-size: 0.85rem; font-weight: 600; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ Auth::user()->name ?? 'Usuario' }}</div>
                    <div style="font-size: 0.7rem; color: #718096;">Administrador</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="app-container">
        <header class="top-bar">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 0.85rem; color: var(--text-light); text-transform: capitalize;">{{ now()->translatedFormat('l, d \d\e F Y') }}</span>
                <div style="width: 1px; height: 20px; background: #edf2f7;"></div>
                <button style="background: none; border: none; color: var(--text-light); cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
            </div>
        </header>

        <main class="main-content">
            @if(session('success'))
                <div style="background: #f0fff4; color: #276749; padding: 1rem 1.5rem; border-radius: var(--border-radius); margin-bottom: 2rem; border-left: 5px solid #48bb78; display: flex; align-items: center; gap: 0.8rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span style="font-weight: 600;">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fff5f5; color: #c53030; padding: 1rem 1.5rem; border-radius: var(--border-radius); margin-bottom: 2rem; border-left: 5px solid #f56565; display: flex; align-items: center; gap: 0.8rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span style="font-weight: 600;">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
