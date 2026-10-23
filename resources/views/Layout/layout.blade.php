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
        html { scroll-behavior: smooth; }
        @view-transition { navigation: auto; }
        ::view-transition-old(root),
        ::view-transition-new(root) { animation-duration: 200ms; animation-timing-function: cubic-bezier(.2,.8,.2,1); }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; font-size: 17px; line-height: 1.35; color: var(--ink); background: var(--paper); min-height: 100vh; display: flex; flex-direction: column; }
        .serif { font-family: 'Source Serif 4', Georgia, serif; font-weight: 400; }
        .contenedor { max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; }

        /* Barra superior */
        .barra { display: flex; align-items: center; justify-content: space-between; padding-top: 24px; padding-bottom: 24px; flex-wrap: wrap; gap: 12px 24px; }
        .logo { font-size: 22px; letter-spacing: -0.23px; color: var(--ink); text-decoration: none; flex-shrink: 0; }
        .menu { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 20px; }
        .menu a { position: relative; color: var(--ink); text-decoration: none; font-size: 15px; padding: 4px 8px; border-radius: 9999px; white-space: nowrap; transition: color 200ms ease-out, background-color 200ms ease-out, transform 200ms cubic-bezier(.2,.8,.2,1); }
        .menu a::after { content: ""; position: absolute; left: 8px; right: 8px; bottom: 1px; height: 2px; border-radius: 9999px; background: var(--sienna); transform: scaleX(0); transform-origin: left center; transition: transform 200ms cubic-bezier(.2,.8,.2,1); }
        .menu a:hover, .menu a.activo { background: var(--peach); color: var(--sienna); }
        .menu a:hover { transform: translateY(-1px); }
        .menu a:hover::after, .menu a.activo::after { transform: scaleX(1); }
        .menu a.activo::after { animation: subrayado-activo 300ms cubic-bezier(.2,.8,.2,1) backwards; }
        .menu .usuario { font-size: 14px; color: var(--slate); white-space: nowrap; }
        .menu form { display: inline-flex; margin: 0; }

        /* Encabezado de página */
        .titulo { font-size: 44px; line-height: 1.3; letter-spacing: -0.66px; margin-top: 56px; }
        .subtitulo { font-size: 17px; color: var(--slate); margin-top: 10px; max-width: 640px; }
        main { flex-grow: 1; padding-top: 48px; padding-bottom: 80px; }

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

        /* Portada y comunicados */
        .historia-grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); gap: 40px; align-items: center; }
        .historia-imagen { margin: 0; overflow: hidden; border-radius: 24px; }
        .historia-imagen img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; display: block; }
        .historia-placeholder { aspect-ratio: 4 / 3; display: flex; align-items: center; justify-content: center; padding: 24px; border-radius: 24px; background: var(--peach); color: var(--sienna); text-align: center; font-family: 'Source Serif 4', Georgia, serif; font-size: 22px; }
        .bloque-encabezado { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
        .bloque-encabezado h2 { margin-bottom: 0; }
        .comunicados-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .comunicado { position: relative; display: flex; flex-direction: column; gap: 10px; padding: 26px; border-radius: 24px; background: var(--mist); }
        .comunicado h3 { font-size: 22px; line-height: 1.2; padding-right: 72px; }
        .comunicado-contenido { color: #33363b; }
        .comunicado-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 14px; font-size: 14px; color: var(--slate); }
        .etiqueta-comunicado { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 9999px; background: var(--peach); color: var(--sienna); font-size: 13px; line-height: 1.4; white-space: nowrap; }
        .etiqueta-comunicado.urgente, .etiqueta-comunicado.cobranza { background: var(--sienna); color: #fff; }
        .comunicado-pin { position: absolute; top: 20px; right: 20px; display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 9999px; background: var(--paper); color: var(--sienna); font-size: 12px; font-weight: 500; box-shadow: 0 0 0 1px rgba(93,42,26,.12); }
        .comunicado-pin svg { width: 13px; height: 13px; }
        .mapa { width: 100%; height: 380px; display: block; border: 1px solid var(--line); border-radius: 24px; }

        @media (max-width: 800px) {
            .historia-grid { grid-template-columns: minmax(0, 1fr); gap: 24px; }
            .mapa { height: 280px; }
        }

        /* Formularios */
        .campo { margin-bottom: 20px; }
        .etiqueta { display: block; font-size: 14px; color: var(--slate); margin-bottom: 6px; }
        .input { width: 100%; background: var(--paper); border: 1px solid var(--line); border-radius: 16px; padding: 12px 16px; font-size: 16px; color: var(--ink); transition: border-color 180ms ease-out, box-shadow 180ms ease-out, background-color 180ms ease-out; }
        .input::placeholder { color: var(--smoke); }
        .input:focus { outline: none; border-color: var(--sienna); box-shadow: 0 0 0 4px var(--peach); }
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

        /* Movimiento y transiciones */
        @keyframes subrayado-activo {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }
        @keyframes contenido-entrada {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes elemento-entrada {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes aviso-entrada {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fila-entrada {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes imagen-entrada {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes crecer-x {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }
        @keyframes crecer-y {
            from { transform: scaleY(0); }
            to { transform: scaleY(1); }
        }

        main { animation: contenido-entrada 300ms cubic-bezier(.2,.8,.2,1) backwards; }

        .btn { transition: transform 180ms cubic-bezier(.2,.8,.2,1), background-color 180ms ease-out, border-color 180ms ease-out, color 180ms ease-out, opacity 180ms ease-out; }
        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }

        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid var(--sienna);
            outline-offset: 2px;
            box-shadow: 0 0 0 4px var(--peach);
        }

        .indicador, .panel, .tarjeta-mercado, .ficha-puesto .card, .foto-card, .comunicado { transition: transform 220ms cubic-bezier(.2,.8,.2,1), box-shadow 220ms ease-out; }
        .indicador:hover, .panel:hover, .tarjeta-mercado:hover, .ficha-puesto .card:hover, .foto-card:hover, .comunicado:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px -24px rgba(4,23,43,.35);
        }

        .indicadores > .indicador, .seccion-mercado > .tarjeta-mercado, .ficha-puesto > .card, .foto-grid > .foto-card, .dos-columnas > .panel, .comunicados-grid > .comunicado, .bloque-seccion {
            animation: elemento-entrada 320ms cubic-bezier(.2,.8,.2,1) backwards;
        }
        .indicadores > .indicador:nth-child(2), .seccion-mercado > .tarjeta-mercado:nth-child(2), .foto-grid > .foto-card:nth-child(2), .dos-columnas > .panel:nth-child(2), .comunicados-grid > .comunicado:nth-child(2), .bloque-seccion:nth-of-type(2) { animation-delay: 50ms; }
        .indicadores > .indicador:nth-child(3), .seccion-mercado > .tarjeta-mercado:nth-child(3), .foto-grid > .foto-card:nth-child(3), .comunicados-grid > .comunicado:nth-child(3), .bloque-seccion:nth-of-type(3) { animation-delay: 100ms; }
        .indicadores > .indicador:nth-child(4), .seccion-mercado > .tarjeta-mercado:nth-child(4), .foto-grid > .foto-card:nth-child(4), .comunicados-grid > .comunicado:nth-child(4), .bloque-seccion:nth-of-type(4) { animation-delay: 150ms; }
        .indicadores > .indicador:nth-child(5), .foto-grid > .foto-card:nth-child(5), .comunicados-grid > .comunicado:nth-child(5), .bloque-seccion:nth-of-type(5) { animation-delay: 200ms; }
        .indicadores > .indicador:nth-child(6), .foto-grid > .foto-card:nth-child(6), .comunicados-grid > .comunicado:nth-child(6), .bloque-seccion:nth-of-type(6) { animation-delay: 250ms; }

        .tabla tbody tr { animation: fila-entrada 280ms ease-out backwards; }
        .tabla tbody td { transition: background-color 180ms ease-out; }
        .tabla tbody tr:nth-child(2) { animation-delay: 30ms; }
        .tabla tbody tr:nth-child(3) { animation-delay: 60ms; }
        .tabla tbody tr:nth-child(4) { animation-delay: 90ms; }
        .tabla tbody tr:nth-child(5) { animation-delay: 120ms; }
        .tabla tbody tr:nth-child(6) { animation-delay: 150ms; }

        .puesto { transition: transform 180ms cubic-bezier(.2,.8,.2,1), box-shadow 180ms ease-out, background-color 180ms ease-out, color 180ms ease-out; }
        .mapa-grid .puesto { position: relative; }
        .mapa-grid .puesto:hover { opacity: 1; transform: scale(1.05); box-shadow: 0 12px 20px -14px rgba(4,23,43,.5); z-index: 2; }
        .mapa-grid .puesto:active { transform: scale(.98); }

        .galeria figure, .foto-card { overflow: hidden; }
        .galeria figure { border-radius: 20px; }
        .galeria figcaption { position: relative; background: var(--paper); }
        .galeria img, .foto-card img, .historia-imagen img { animation: imagen-entrada 400ms ease-out backwards; transition: transform 250ms cubic-bezier(.2,.8,.2,1); }
        .galeria figure:hover img, .foto-card:hover img, .historia-imagen:hover img { transform: scale(1.03); }

        .barra-ocupacion span { transform-origin: left center; animation: crecer-x 400ms cubic-bezier(.2,.8,.2,1) backwards; }
        .grafico .barra-mes { transform-origin: bottom center; animation: crecer-y 400ms cubic-bezier(.2,.8,.2,1) backwards; }
        .grafico .columna:nth-child(2) .barra-mes { animation-delay: 50ms; }
        .grafico .columna:nth-child(3) .barra-mes { animation-delay: 100ms; }
        .grafico .columna:nth-child(4) .barra-mes { animation-delay: 150ms; }
        .grafico .columna:nth-child(5) .barra-mes { animation-delay: 200ms; }
        .grafico .columna:nth-child(6) .barra-mes { animation-delay: 250ms; }

        .aviso { animation: aviso-entrada 350ms cubic-bezier(.2,.8,.2,1) backwards; }

        @media (max-width: 640px) { .titulo { font-size: 32px; } .menu { gap: 6px 16px; } }

        @media (prefers-reduced-motion: reduce) {
            @view-transition { navigation: none; }
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .btn:hover, .btn:active, .menu a:hover, .indicador:hover, .panel:hover, .tarjeta-mercado:hover, .ficha-puesto .card:hover, .foto-card:hover, .comunicado:hover, .mapa-grid .puesto:hover, .mapa-grid .puesto:active, .galeria figure:hover img, .foto-card:hover img, .historia-imagen:hover img { transform: none; }
            ::view-transition-old(root), ::view-transition-new(root) { animation: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="contenedor barra">
        <a href="{{ route('inicio') }}" class="logo serif">Mercado Rodríguez</a>
        <nav class="menu">
            @guest
                <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'activo' : '' }}">El mercado</a>
                <a href="{{ route('tablero') }}" class="{{ request()->routeIs('tablero') ? 'activo' : '' }}">Tablero</a>
                <a href="{{ route('puestos.libres') }}" class="{{ request()->routeIs('puestos.libres') ? 'activo' : '' }}">Puestos libres</a>
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'activo' : '' }}">Iniciar sesión</a>
            @else
                @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN))
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'activo' : '' }}">Dashboard</a>
                @endif
                <a href="{{ route('mapa.index') }}" class="{{ request()->routeIs('mapa.*') ? 'activo' : '' }}">Mapa</a>
                <a href="{{ route('comerciantes.index') }}" class="{{ request()->routeIs('comerciantes.*') ? 'activo' : '' }}">Comerciantes</a>
                <a href="{{ route('puestos.index') }}" class="{{ request()->routeIs('puestos.*') ? 'activo' : '' }}">Puestos</a>
                <a href="{{ route('secciones.index') }}" class="{{ request()->routeIs('secciones.*') ? 'activo' : '' }}">Secciones</a>
                <a href="{{ route('asignaciones.index') }}" class="{{ request()->routeIs('asignaciones.*') ? 'activo' : '' }}">Asignaciones</a>
                <a href="{{ route('obligaciones.index') }}" class="{{ request()->routeIs('obligaciones.*') ? 'activo' : '' }}">Obligaciones</a>
                <a href="{{ route('pagos.index') }}" class="{{ request()->routeIs('pagos.*') ? 'activo' : '' }}">Pagos</a>
                <a href="{{ route('tablero') }}" class="{{ request()->routeIs('tablero') ? 'activo' : '' }}">Tablero</a>
                @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN))
                    <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'activo' : '' }}">El mercado</a>
                    <a href="{{ route('admin.comunicados.index') }}" class="{{ request()->routeIs('admin.comunicados.*') ? 'activo' : '' }}">Comunicados</a>
                    <a href="{{ route('admin.fotos.index') }}" class="{{ request()->routeIs('admin.fotos.*') ? 'activo' : '' }}">Fotos</a>
                @endif
                <span class="usuario">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-ghost btn-sm">Cerrar sesión</button>
                </form>
            @endguest
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