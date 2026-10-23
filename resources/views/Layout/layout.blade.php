<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mercado Rodríguez')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..500&family=Source+Serif+4:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #17191c; --paper: #ffffff; --mist: #f2f2f3; --fog: #fafafb;
            --slate: #777b86; --ash: #979799; --smoke: #a3a6af;
            --peach: #fbe1d1; --sienna: #5d2a1a; --line: #ececec;
        }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; font-size: 17px; line-height: 1.35; color: var(--ink); background: var(--paper); min-height: 100vh; display: flex; flex-direction: column; }
        .serif { font-family: 'Source Serif 4', Georgia, serif; font-weight: 400; }
        .contenedor { max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; }

        /* Barra superior */
        .barra { display: flex; align-items: center; justify-content: space-between; padding-top: 24px; padding-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .logo { font-size: 22px; letter-spacing: -0.23px; color: var(--ink); text-decoration: none; }
        .menu a { color: var(--ink); text-decoration: none; font-size: 16px; margin-left: 24px; padding: 2px 0; }
        .menu a:hover, .menu a.activo { border-bottom: 1px solid var(--ink); }

        /* Encabezado de página */
        .titulo { font-size: 44px; line-height: 1.3; letter-spacing: -0.66px; margin-top: 40px; }
        .subtitulo { font-size: 17px; color: var(--slate); margin-top: 8px; max-width: 640px; }
        main { flex-grow: 1; padding-top: 40px; padding-bottom: 80px; }

        /* Botones */
        .btn { display: inline-flex; align-items: center; border-radius: 9999px; padding: 10px 20px; font-size: 16px; background: var(--ink); color: #fff; border: 1px solid var(--ink); cursor: pointer; text-decoration: none; }
        .btn:hover { opacity: .88; }
        .btn-ghost { background: transparent; color: var(--ink); }
        .btn-ghost:hover { background: var(--mist); opacity: 1; }
        .btn-sm { padding: 5px 14px; font-size: 14px; }

        /* Superficies */
        .card { background: var(--mist); border-radius: 24px; padding: 32px; max-width: 520px; }
        .artefacto { background: var(--paper); border-radius: 20px; box-shadow: 0 0 0 1px rgba(4,23,43,.05), 0 20px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1); overflow: hidden; }
        .aviso { border-radius: 16px; padding: 12px 20px; margin-bottom: 24px; background: var(--mist); }
        .aviso-error { background: var(--peach); color: var(--sienna); }

        /* Formularios */
        .campo { margin-bottom: 20px; }
        .etiqueta { display: block; font-size: 14px; color: var(--slate); margin-bottom: 6px; }
        .input { width: 100%; background: var(--paper); border: 1px solid var(--line); border-radius: 16px; padding: 12px 16px; font-size: 16px; color: var(--ink); }
        .input::placeholder { color: var(--smoke); }
        .input:focus { outline: none; border-color: var(--ink); }
        .error { display: inline-block; margin-top: 6px; padding: 3px 12px; border-radius: 9999px; background: var(--peach); color: var(--sienna); font-size: 14px; }

        /* Tablas y filtros */
        .herramientas { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 24px; }
        .filtros { display: flex; flex-wrap: wrap; gap: 8px; }
        .filtros .input { width: auto; }
        .tabla { width: 100%; border-collapse: collapse; }
        .tabla th { text-align: left; font-size: 14px; font-weight: 400; color: var(--ash); padding: 14px 20px; }
        .tabla td { padding: 14px 20px; border-top: 1px solid var(--line); }
        .tabla tr:hover td { background: var(--fog); }
        .acciones { display: flex; gap: 8px; }
        .etiqueta-estado { font-size: 14px; color: var(--slate); }
        .etiqueta-estado.clausurado { background: var(--peach); color: var(--sienna); padding: 3px 12px; border-radius: 9999px; }

        footer { color: var(--slate); font-size: 15px; padding-bottom: 40px; }

        @media (max-width: 640px) { .titulo { font-size: 32px; } .menu a { margin-left: 0; margin-right: 16px; } }
    </style>
    @stack('styles')
</head>
<body>
    <header class="contenedor barra">
        <a href="{{ url('/') }}" class="logo serif">Mercado Rodríguez</a>
        <nav class="menu">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'activo' : '' }}">El mercado</a>
            @auth
                @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN, \App\Models\User::ROL_COBRADOR))
                    <a href="{{ route('puestos.index') }}" class="{{ request()->routeIs('puestos.*') ? 'activo' : '' }}">Puestos</a>
                    <a href="{{ route('secciones.index') }}" class="{{ request()->routeIs('secciones.*') ? 'activo' : '' }}">Secciones</a>
                    <a href="{{ route('comerciantes.index') }}" class="{{ request()->routeIs('comerciantes.*') ? 'activo' : '' }}">Comerciantes</a>
                    <a href="{{ route('asignaciones.index') }}" class="{{ request()->routeIs('asignaciones.*') ? 'activo' : '' }}">Asignaciones</a>
                    <a href="{{ route('obligaciones.index') }}" class="{{ request()->routeIs('obligaciones.*') ? 'activo' : '' }}">Obligaciones</a>
                    <a href="{{ route('pagos.index') }}" class="{{ request()->routeIs('pagos.*') ? 'activo' : '' }}">Pagos</a>
                @endif
                @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN))
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'activo' : '' }}">Dashboard</a>
                @endif
                <span class="etiqueta-estado" style="margin-left: 24px">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline">
                    @csrf
                    <button class="btn btn-ghost btn-sm">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'activo' : '' }}">Iniciar sesión</a>
            @endauth
        </nav>
    </header>

    <div class="contenedor">
        <h1 class="titulo serif">@yield('header')</h1>
        @hasSection('subtitle')
            <p class="subtitulo">@yield('subtitle')</p>
        @endif
    </div>

    <main class="contenedor">
        @if (session('ok'))
            <div class="aviso">{{ session('ok') }}</div>
        @endif
        @if (session('error'))
            <div class="aviso aviso-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="contenedor">
        &copy; {{ date('Y') }} Sistema de gestión del Mercado Rodríguez
    </footer>
    @yield('script')
</body>
</html>