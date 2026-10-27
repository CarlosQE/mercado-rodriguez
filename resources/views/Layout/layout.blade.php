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
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; max-width: 100%; }
        @view-transition { navigation: auto; }
        ::view-transition-old(root),
        ::view-transition-new(root) { animation-duration: 200ms; animation-timing-function: cubic-bezier(.2,.8,.2,1); }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; font-size: 17px; line-height: 1.35; color: var(--ink); background: var(--paper); min-height: 100vh; max-width: 100%; display: flex; flex-direction: column; overflow-wrap: anywhere; }
        img, svg, iframe, video, canvas { max-width: 100%; }
        img, iframe, video { height: auto; }
        .serif { font-family: 'Source Serif 4', Georgia, serif; font-weight: 400; }
        .contenedor { max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; min-width: 0; }

        /* Barra superior */
        .barra { display: flex; align-items: center; justify-content: space-between; padding-top: 24px; padding-bottom: 24px; flex-wrap: nowrap; gap: 24px; position: relative; }
        .logo { font-size: 22px; letter-spacing: -0.23px; color: var(--ink); text-decoration: none; flex-shrink: 0; }
        .nav-hamburguesa { display: none; align-items: center; justify-content: center; width: 42px; height: 42px; padding: 0; border: 1px solid var(--line); border-radius: 9999px; background: var(--paper); color: var(--ink); cursor: pointer; flex-shrink: 0; transition: background-color 180ms ease-out; }
        .nav-hamburguesa:hover { background: var(--mist); }
        .menu { display: flex; align-items: center; flex: 1; min-width: 0; }
        .nav-enlaces { display: flex; align-items: center; flex: 1; flex-wrap: nowrap; gap: 2px; }
        .nav-enlaces > a, .nav-boton { display: inline-flex; align-items: center; gap: 5px; padding: 7px 10px; border: 0; border-radius: 9999px; background: transparent; color: var(--ink); font: inherit; font-size: 15px; line-height: 1.2; text-decoration: none; white-space: nowrap; cursor: pointer; transition: color 180ms ease-out, background-color 180ms ease-out, transform 180ms cubic-bezier(.2,.8,.2,1); }
        .nav-enlaces > a:hover, .nav-enlaces > a.activo, .nav-boton:hover, .nav-boton.activo, .nav-grupo.abierto > .nav-boton { background: var(--peach); color: var(--sienna); }
        .nav-enlaces > a:hover, .nav-boton:hover { transform: translateY(-1px); }
        .nav-grupo { position: relative; }
        .nav-flecha { width: 12px; height: 12px; flex-shrink: 0; transition: transform 180ms ease-out; }
        .nav-grupo.abierto > .nav-boton .nav-flecha { transform: rotate(180deg); }
        .nav-panel { display: none; position: absolute; top: calc(100% + 8px); left: 0; z-index: 70; min-width: 196px; padding: 8px; background: var(--paper); border: 1px solid var(--line); border-radius: 18px; box-shadow: 0 22px 44px -36px rgba(4,23,43,.45); }
        .nav-grupo.abierto > .nav-panel { display: block; animation: menu-entrada 140ms cubic-bezier(.2,.8,.2,1) backwards; }
        .nav-panel a { display: block; padding: 8px 12px; border-radius: 10px; color: var(--ink); font-size: 15px; text-decoration: none; white-space: nowrap; transition: color 180ms ease-out, background-color 180ms ease-out; }
        .nav-panel a:hover { background: var(--fog); }
        .nav-panel a.activo { background: var(--peach); color: var(--sienna); }
        .nav-usuario { margin-left: auto; }
        .nav-usuario > .nav-panel { left: auto; right: 0; min-width: 210px; }
        .nav-boton-usuario { max-width: 230px; }
        .usuario-nombre { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .usuario-rol { white-space: nowrap; font-size: 11px; letter-spacing: .06em; text-transform: uppercase; color: var(--slate); }
        .nav-panel form { margin: 0; }
        .nav-cerrar-sesion { display: block; width: 100%; padding: 8px 12px; border: 0; border-radius: 10px; background: transparent; color: var(--ink); font: inherit; font-size: 15px; text-align: left; cursor: pointer; }
        .nav-cerrar-sesion:hover { background: var(--fog); }

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
        .card { background: var(--mist); border-radius: 24px; padding: 32px; max-width: 520px; min-width: 0; }
        .artefacto { background: var(--paper); border-radius: 20px; box-shadow: 0 0 0 1px rgba(4,23,43,.05), 0 20px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1); max-width: 100%; overflow-x: auto; overflow-y: hidden; }
        .tabla-contenedor { width: 100%; max-width: 100%; overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; }
        .aviso { border-radius: 16px; padding: 12px 20px; margin-bottom: 24px; background: var(--mist); }
        .aviso-error { background: var(--peach); color: var(--sienna); }

        /* Portada y comunicados */
        .historia-grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); gap: 40px; align-items: center; }
        .historia-imagen { margin: 0; overflow: hidden; border-radius: 24px; }
        .historia-imagen img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; display: block; }
        .historia-placeholder { aspect-ratio: 4 / 3; display: flex; align-items: center; justify-content: center; padding: 24px; border-radius: 24px; background: var(--peach); color: var(--sienna); text-align: center; font-family: 'Source Serif 4', Georgia, serif; font-size: 22px; }
        .bloque-encabezado { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
        .bloque-encabezado h2 { margin-bottom: 0; }
        .comunicados-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 20px; }
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
        .tabla th, .tabla td { overflow-wrap: anywhere; }
        .tabla th { text-align: left; font-size: 14px; font-weight: 400; color: var(--ash); padding: 14px 20px; }
        .tabla td { padding: 14px 20px; border-top: 1px solid var(--line); }
        .tabla tr:hover td { background: var(--fog); }
        .acciones { display: flex; flex-wrap: wrap; gap: 8px; min-width: 0; }
        .herramientas > *, .filtros > *, .panel, .indicador { min-width: 0; }
        .filtros { max-width: 100%; }
        .filtros .input { max-width: 100%; }
        .dos-columnas > *, .comunicados-grid > *, .foto-grid > *, .seccion-mercado > *, .galeria > *, .historia-grid > * { min-width: 0; }
        .etiqueta-estado { font-size: 14px; color: var(--slate); }
        .etiqueta-estado.clausurado { background: var(--peach); color: var(--sienna); padding: 3px 12px; border-radius: 9999px; }

        footer { color: var(--slate); font-size: 15px; padding-bottom: 40px; }

        /* Movimiento y transiciones */
        @keyframes menu-entrada {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
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
        .tabla tbody tr:nth-child(7) { animation-delay: 180ms; }
        .tabla tbody tr:nth-child(8) { animation-delay: 210ms; }
        .tabla tbody tr:nth-child(9) { animation-delay: 240ms; }
        .tabla tbody tr:nth-child(10) { animation-delay: 270ms; }

        .puesto { transition: transform 180ms cubic-bezier(.2,.8,.2,1), box-shadow 180ms ease-out, background-color 180ms ease-out, color 180ms ease-out; }
        .mapa-grid .puesto { position: relative; }
        .mapa-grid .puesto:hover { opacity: 1; transform: scale(1.05); box-shadow: 0 12px 20px -14px rgba(4,23,43,.5); z-index: 2; }
        .mapa-grid .puesto:active { transform: scale(.98); }

        .galeria figure, .foto-card { overflow: hidden; }
        .galeria figure { border-radius: 20px; }
        .galeria figcaption { position: relative; background: var(--paper); }
        .galeria img, .foto-card img, .historia-imagen img { animation: imagen-entrada 400ms ease-out backwards; transition: transform 250ms cubic-bezier(.2,.8,.2,1); }
        .galeria figure:hover img, .foto-card:hover img, .historia-imagen:hover img { transform: scale(1.03); }

        .barra-ocupacion span { transform-origin: left center; animation: crecer-x 800ms cubic-bezier(.2,.8,.2,1) backwards; }
        .grafico .barra-mes { transform-origin: bottom center; animation: crecer-y 800ms cubic-bezier(.2,.8,.2,1) backwards; }
        .grafico .columna:nth-child(2) .barra-mes { animation-delay: 100ms; }
        .grafico .columna:nth-child(3) .barra-mes { animation-delay: 200ms; }
        .grafico .columna:nth-child(4) .barra-mes { animation-delay: 300ms; }
        .grafico .columna:nth-child(5) .barra-mes { animation-delay: 400ms; }
        .grafico .columna:nth-child(6) .barra-mes { animation-delay: 500ms; }

        .aviso { animation: aviso-entrada 350ms cubic-bezier(.2,.8,.2,1) backwards; }

        @media (max-width: 1023.98px) {
            .barra { padding-top: 18px; padding-bottom: 18px; gap: 16px; }
            .nav-hamburguesa { display: inline-flex; }
            .menu { display: none; position: absolute; top: calc(100% - 6px); left: 20px; right: 20px; z-index: 80; flex: none; max-height: calc(100vh - 110px); overflow-y: auto; padding: 16px; background: var(--paper); border: 1px solid var(--line); border-radius: 22px; box-shadow: 0 28px 56px -40px rgba(4,23,43,.5); }
            .menu.menu-abierto { display: block; animation: menu-entrada 160ms cubic-bezier(.2,.8,.2,1) backwards; }
            .nav-enlaces { flex-direction: column; align-items: stretch; gap: 3px; }
            .nav-enlaces > a, .nav-boton { width: 100%; justify-content: space-between; white-space: normal; padding: 9px 10px; }
            .nav-grupo { width: 100%; }
            .nav-flecha { display: block; }
            .nav-panel { display: block; position: static; min-width: 0; max-height: 0; padding: 0 0 0 12px; border: 0; border-radius: 0; box-shadow: none; opacity: 0; visibility: hidden; pointer-events: none; overflow: hidden; transition: max-height 200ms cubic-bezier(.2,.8,.2,1), opacity 160ms ease-out, padding 200ms cubic-bezier(.2,.8,.2,1), visibility 0s linear 200ms; }
            .nav-grupo.abierto > .nav-panel { max-height: 320px; padding: 4px 0 8px 12px; opacity: 1; visibility: visible; pointer-events: auto; overflow-y: auto; animation: none; transition: max-height 200ms cubic-bezier(.2,.8,.2,1), opacity 160ms ease-out, padding 200ms cubic-bezier(.2,.8,.2,1), visibility 0s; }
            .nav-panel a { white-space: normal; }
            .nav-usuario { margin-left: 0; padding-top: 8px; border-top: 1px solid var(--line); }
            .nav-usuario > .nav-panel { padding: 0 0 0 12px; }
            .nav-boton-usuario { max-width: none; }
        }

        @media (max-width: 640px) {
            .titulo { font-size: 32px; }
            .card { padding: 24px; }
            .panel { padding: 20px; }
            .tabla th, .tabla td { padding: 12px 14px; }
            .artefacto > .tabla { min-width: 560px; }
            .tabla-contenedor > .tabla { min-width: 480px; }
            .grafico { gap: 8px; }
            .grafico .monto-mes { font-size: 11px; }
        }

        @media (prefers-reduced-motion: reduce) {
            @view-transition { navigation: none; }
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .btn:hover, .btn:active, .nav-enlaces > a:hover, .nav-boton:hover, .indicador:hover, .panel:hover, .tarjeta-mercado:hover, .ficha-puesto .card:hover, .foto-card:hover, .comunicado:hover, .mapa-grid .puesto:hover, .mapa-grid .puesto:active, .galeria figure:hover img, .foto-card:hover img, .historia-imagen:hover img { transform: none; }
            ::view-transition-old(root), ::view-transition-new(root) { animation: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="contenedor barra">
        <a href="{{ route('inicio') }}" class="logo serif">Mercado Rodríguez</a>

        <button class="nav-hamburguesa" type="button" aria-expanded="false" aria-controls="menu-principal" aria-label="Abrir menú">
            <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
        </button>

        <nav class="menu" id="menu-principal" aria-label="Navegación principal">
            @guest
                <div class="nav-enlaces">
                    <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'activo' : '' }}">El mercado</a>
                    <a href="{{ route('tablero') }}" class="{{ request()->routeIs('tablero') ? 'activo' : '' }}">Tablero</a>
                    <a href="{{ route('puestos.libres') }}" class="{{ request()->routeIs('puestos.libres') ? 'activo' : '' }}">Puestos libres</a>
                    <a href="{{ route('ruleta') }}" class="{{ request()->routeIs('ruleta') ? 'activo' : '' }}">Ruleta</a>
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'activo' : '' }}">Ingresar</a>
                </div>
            @else
                @php
                    $esAdmin = auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN);
                    $gestionActiva = request()->routeIs('comerciantes.*', 'puestos.*', 'secciones.*', 'asignaciones.*');
                    $cobrosActiva = request()->routeIs('obligaciones.*', 'pagos.*');
                    $sitioActivo = request()->routeIs('inicio', 'tablero', 'admin.comunicados.*', 'admin.fotos.*', 'admin.mercado.*');
                @endphp

                <div class="nav-enlaces">
                    @if ($esAdmin)
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'activo' : '' }}">Dashboard</a>
                    @endif
                    <a href="{{ route('mapa.index') }}" class="{{ request()->routeIs('mapa.*') ? 'activo' : '' }}">Mapa</a>

                    <div class="nav-grupo" data-nav-grupo @if ($gestionActiva) data-nav-activo @endif>
                        <button type="button" class="nav-boton {{ $gestionActiva ? 'activo' : '' }}" aria-expanded="false" aria-controls="nav-gestion" aria-haspopup="true">
                            Gestión
                            <svg class="nav-flecha" viewBox="0 0 12 12" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="nav-panel" id="nav-gestion">
                            <a href="{{ route('comerciantes.index') }}" class="{{ request()->routeIs('comerciantes.*') ? 'activo' : '' }}">Comerciantes</a>
                            <a href="{{ route('puestos.index') }}" class="{{ request()->routeIs('puestos.*') ? 'activo' : '' }}">Puestos</a>
                            <a href="{{ route('secciones.index') }}" class="{{ request()->routeIs('secciones.*') ? 'activo' : '' }}">Secciones</a>
                            <a href="{{ route('asignaciones.index') }}" class="{{ request()->routeIs('asignaciones.*') ? 'activo' : '' }}">Asignaciones</a>
                        </div>
                    </div>

                    <div class="nav-grupo" data-nav-grupo @if ($cobrosActiva) data-nav-activo @endif>
                        <button type="button" class="nav-boton {{ $cobrosActiva ? 'activo' : '' }}" aria-expanded="false" aria-controls="nav-cobros" aria-haspopup="true">
                            Cobros
                            <svg class="nav-flecha" viewBox="0 0 12 12" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="nav-panel" id="nav-cobros">
                            <a href="{{ route('obligaciones.index') }}" class="{{ request()->routeIs('obligaciones.*') ? 'activo' : '' }}">Obligaciones</a>
                            <a href="{{ route('pagos.index') }}" class="{{ request()->routeIs('pagos.*') ? 'activo' : '' }}">Pagos</a>
                        </div>
                    </div>

                    @if ($esAdmin)
                        <div class="nav-grupo" data-nav-grupo @if ($sitioActivo) data-nav-activo @endif>
                            <button type="button" class="nav-boton {{ $sitioActivo ? 'activo' : '' }}" aria-expanded="false" aria-controls="nav-sitio" aria-haspopup="true">
                                Sitio público
                                <svg class="nav-flecha" viewBox="0 0 12 12" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="nav-panel" id="nav-sitio">
                                <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'activo' : '' }}">El mercado</a>
                                <a href="{{ route('tablero') }}" class="{{ request()->routeIs('tablero') ? 'activo' : '' }}">Tablero</a>
                                <a href="{{ route('admin.comunicados.index') }}" class="{{ request()->routeIs('admin.comunicados.*') ? 'activo' : '' }}">Comunicados</a>
                                <a href="{{ route('admin.fotos.index') }}" class="{{ request()->routeIs('admin.fotos.*') ? 'activo' : '' }}">Fotos</a>
                                <a href="{{ route('admin.mercado.edit') }}" class="{{ request()->routeIs('admin.mercado.*') ? 'activo' : '' }}">Datos del mercado</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('tablero') }}" class="{{ request()->routeIs('tablero') ? 'activo' : '' }}">Tablero</a>
                    @endif

                    <div class="nav-grupo nav-usuario" data-nav-grupo>
                        <button type="button" class="nav-boton nav-boton-usuario" aria-expanded="false" aria-controls="nav-usuario" aria-haspopup="true">
                            <span class="usuario-nombre">{{ auth()->user()->name }}</span>
                            <span class="usuario-rol">{{ $esAdmin ? 'Admin' : 'Cobrador' }}</span>
                            <svg class="nav-flecha" viewBox="0 0 12 12" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="nav-panel" id="nav-usuario">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-cerrar-sesion">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                </div>
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

    <script>
        (() => {
            const menu = document.getElementById('menu-principal');
            const hamburguesa = document.querySelector('.nav-hamburguesa');
            const escritorio = window.matchMedia('(min-width: 1024px)');
            const grupos = [...document.querySelectorAll('[data-nav-grupo]')];

            const abrirGrupo = (grupo) => {
                grupo.classList.add('abierto');
                grupo.querySelector('.nav-boton')?.setAttribute('aria-expanded', 'true');
            };

            const cerrarGrupos = () => {
                grupos.forEach((grupo) => {
                    grupo.classList.remove('abierto');
                    grupo.querySelector('.nav-boton')?.setAttribute('aria-expanded', 'false');
                });
            };

            const abrirGrupoActivo = () => {
                if (escritorio.matches) return;

                const activo = grupos.find((grupo) => grupo.hasAttribute('data-nav-activo'));

                if (activo) abrirGrupo(activo);
            };

            const cerrarMenu = () => {
                if (!menu || !hamburguesa) return;

                menu.classList.remove('menu-abierto');
                hamburguesa.setAttribute('aria-expanded', 'false');
                hamburguesa.setAttribute('aria-label', 'Abrir menú');
            };

            const sincronizarModo = () => {
                grupos.forEach((grupo) => {
                    const boton = grupo.querySelector('.nav-boton');

                    if (!boton) return;

                    boton.tabIndex = 0;
                    boton.setAttribute('aria-expanded', grupo.classList.contains('abierto') ? 'true' : 'false');
                });
            };

            grupos.forEach((grupo) => {
                const boton = grupo.querySelector('.nav-boton');

                if (!boton) return;

                boton.addEventListener('click', () => {
                    const abierto = !grupo.classList.contains('abierto');
                    cerrarGrupos();

                    if (abierto) abrirGrupo(grupo);
                });
            });

            hamburguesa?.addEventListener('click', () => {
                if (!menu) return;

                const abierto = menu.classList.toggle('menu-abierto');
                hamburguesa.setAttribute('aria-expanded', abierto ? 'true' : 'false');
                hamburguesa.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
                cerrarGrupos();

                if (abierto) abrirGrupoActivo();
            });

            document.addEventListener('click', (evento) => {
                if (escritorio.matches && !evento.target.closest('[data-nav-grupo]')) cerrarGrupos();
                if (!evento.target.closest('.barra')) cerrarMenu();
            });

            document.addEventListener('keydown', (evento) => {
                if (evento.key !== 'Escape') return;

                cerrarGrupos();

                if (menu?.classList.contains('menu-abierto')) {
                    cerrarMenu();
                    hamburguesa?.focus();
                }
            });

            escritorio.addEventListener('change', () => {
                if (escritorio.matches) {
                    cerrarMenu();
                    cerrarGrupos();
                } else {
                    cerrarGrupos();
                    abrirGrupoActivo();
                }

                sincronizarModo();
            });

            sincronizarModo();
            abrirGrupoActivo();
        })();
    </script>
    @yield('script')
</body>
</html>