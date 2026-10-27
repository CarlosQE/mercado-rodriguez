@extends('Layout.layout')

@section('title', $dato?->nombre ?? 'Mercado Rodríguez')
@section('header', $dato?->nombre ?? 'Mercado Rodríguez')
@section('subtitle', $dato?->eslogan ?? 'Un mercado de barrio con memoria, oficio y futuro.')

@push('styles')
    <style>
        .portada-acciones { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 32px; }
        .banner-ruleta { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 56px; padding: 24px 28px; border: 1px solid var(--line); border-radius: 24px; background: var(--fog); }
        .banner-ruleta h2 { margin-top: 8px; font-size: 26px; line-height: 1.2; letter-spacing: -0.03em; }
        .banner-ruleta p { max-width: 560px; margin-top: 4px; color: var(--slate); font-size: 15px; }
        .banner-ruleta-etiqueta { display: inline-flex; padding: 3px 10px; border-radius: 9999px; background: var(--peach); color: var(--sienna); font-size: 13px; }
        .banner-ruleta .btn { flex-shrink: 0; }
        .seccion-mercado { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 20px; }
        .tarjeta-mercado { background: var(--mist); border-radius: 24px; padding: 28px; }
        .tarjeta-mercado h3 { font-size: 22px; margin-bottom: 12px; }
        .tarjeta-mercado p { color: var(--slate); margin-bottom: 6px; }
        .bloque { margin-top: 64px; }
        .bloque h2 { font-size: 30px; letter-spacing: -0.4px; margin-bottom: 20px; }
        .prosa p { max-width: 760px; margin-bottom: 18px; color: #33363b; }
        .galeria { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: 20px; }
        .galeria figure { margin: 0; }
        .galeria img { width: 100%; height: 210px; object-fit: cover; border-radius: 20px; display: block; }
        .galeria figcaption { font-size: 15px; color: var(--slate); margin-top: 8px; }
    </style>
@endpush

@section('content')
    <div class="portada-acciones">
        <a href="{{ route('puestos.libres') }}" class="btn">Ver puestos libres</a>
        @guest
            <a href="{{ route('login') }}" class="btn btn-ghost">Iniciar sesión</a>
        @endguest
    </div>

    @guest
        <section class="banner-ruleta">
            <div>
                <span class="banner-ruleta-etiqueta">Juego rápido</span>
                <h2 class="serif">¿Cuánto sabes de web?</h2>
                <p>Gira la ruleta, responde las preguntas y descubre cuánto aprendiste sobre el proyecto.</p>
            </div>
            <a href="{{ route('ruleta') }}" class="btn">Gira la ruleta</a>
        </section>
    @endguest

    @if ($dato)
        <section class="bloque" style="margin-top: 0">
            <div class="historia-grid">
                <div>
                    <h2 class="serif">Nuestra historia</h2>
                    <div class="prosa">
                        @foreach (preg_split('/\r?\n\r?\n/', trim($dato->historia)) as $parrafo)
                            <p>{{ $parrafo }}</p>
                        @endforeach
                    </div>
                </div>

                @php $fotoHistoria = $fotos->first(); @endphp
                <figure class="historia-imagen">
                    @if ($fotoHistoria)
                        <img src="{{ $fotoHistoria->url }}" alt="{{ $fotoHistoria->titulo }}" loading="lazy">
                    @else
                        <div class="historia-placeholder">Próximamente: foto del mercado</div>
                    @endif
                </figure>
            </div>
        </section>
    @endif

    @if ($comunicados->isNotEmpty())
        <section class="bloque">
            <div class="bloque-encabezado">
                <h2 class="serif">Comunicados</h2>
                <a href="{{ route('tablero') }}" class="btn btn-ghost btn-sm">Ver tablero completo</a>
            </div>

            <div class="comunicados-grid">
                @foreach ($comunicados as $comunicado)
                    <article class="comunicado">
                        @if ($comunicado->fijado)
                            <span class="comunicado-pin" title="Aviso fijado">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z"></path>
                                    <circle cx="12" cy="10" r="2.5"></circle>
                                </svg>
                                Fijado
                            </span>
                        @endif

                        <div class="comunicado-meta">
                            <span class="etiqueta-comunicado {{ $comunicado->tipo }}">{{ ucfirst($comunicado->tipo) }}</span>
                            <time datetime="{{ $comunicado->fecha_publicacion->toDateString() }}">{{ $comunicado->fecha_publicacion->translatedFormat('d M Y') }}</time>
                        </div>

                        <h3 class="serif">{{ $comunicado->titulo }}</h3>
                        <p class="comunicado-contenido">{!! nl2br(e($comunicado->contenido)) !!}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="bloque">
        <h2 class="serif">Secciones del mercado</h2>
        <div class="seccion-mercado">
            @forelse ($secciones as $seccion)
                <a href="{{ route('puestos.libres', ['seccion_id' => $seccion->id]) }}" class="tarjeta-mercado" style="text-decoration: none; color: inherit">
                    <h3 class="serif">{{ $seccion->nombre }}</h3>
                    <p>Tarifa: Bs {{ number_format($seccion->tarifa_m2, 2) }} por m²</p>
                    <p>{{ $seccion->puestos_count }} puestos</p>
                    <p>{{ $seccion->puestos_libres_count }} disponibles</p>
                </a>
            @empty
                <p style="color: var(--slate)">Todavía no hay secciones registradas.</p>
            @endforelse
        </div>
    </section>

    @if ($dato)
        <section class="bloque">
            <h2 class="serif">Ubicación y contacto</h2>
            <div class="seccion-mercado" style="margin-bottom: 24px">
                <div class="tarjeta-mercado">
                    <p><strong>Dirección:</strong> {{ $dato->direccion }}</p>
                    @if ($dato->referencia)
                        <p><strong>Referencia:</strong> {{ $dato->referencia }}</p>
                    @endif
                    <p><strong>Horario:</strong> {{ $dato->horario_atencion }}</p>
                </div>
                <div class="tarjeta-mercado">
                    <p><strong>Teléfono:</strong> {{ $dato->telefono }}</p>
                    @if ($dato->email)
                        <p><strong>Correo:</strong> {{ $dato->email }}</p>
                    @endif
                </div>
            </div>

            @php
                $latTexto = number_format((float) $dato->latitud, 6, '.', '');
                $lonTexto = number_format((float) $dato->longitud, 6, '.', '');
            @endphp
            <iframe
                class="mapa"
                src="https://www.google.com/maps?q={{ $latTexto }},{{ $lonTexto }}&amp;hl=es&amp;z=16&amp;output=embed"
                width="100%"
                height="380"
                title="Ubicación del Mercado Rodríguez"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </section>
    @endif

    <section class="bloque">
        <h2 class="serif">Galería</h2>
        <div class="galeria">
            @forelse ($fotos as $foto)
                <figure>
                    <img src="{{ $foto->url }}" alt="{{ $foto->titulo }}" loading="lazy">
                    <figcaption>{{ $foto->titulo }}@if ($foto->descripcion) · {{ $foto->descripcion }}@endif</figcaption>
                </figure>
            @empty
                <p style="color: var(--slate)">Todavía no hay fotografías publicadas.</p>
            @endforelse
        </div>
    </section>
@endsection
