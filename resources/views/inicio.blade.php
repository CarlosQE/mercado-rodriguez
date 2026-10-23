@extends('Layout.layout')

@section('title', $dato?->nombre ?? 'Mercado Rodríguez')
@section('header', $dato?->nombre ?? 'Mercado Rodríguez')
@section('subtitle', $dato?->eslogan ?? 'Un mercado de barrio con memoria, oficio y futuro.')

@push('styles')
    <style>
        .portada-acciones { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 56px; }
        .seccion-mercado { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px; }
        .tarjeta-mercado { background: var(--mist); border-radius: 24px; padding: 28px; }
        .tarjeta-mercado h3 { font-size: 22px; margin-bottom: 12px; }
        .tarjeta-mercado p { color: var(--slate); margin-bottom: 6px; }
        .bloque { margin-top: 64px; }
        .bloque h2 { font-size: 30px; letter-spacing: -0.4px; margin-bottom: 20px; }
        .prosa p { max-width: 760px; margin-bottom: 18px; color: #33363b; }
        .galeria { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
        .galeria figure { margin: 0; }
        .galeria img { width: 100%; height: 210px; object-fit: cover; border-radius: 20px; display: block; }
        .galeria figcaption { font-size: 15px; color: var(--slate); margin-top: 8px; }
        .mapa { width: 100%; height: 380px; border: 0; border-radius: 24px; }
    </style>
@endpush

@section('content')
    <div class="portada-acciones">
        <a href="{{ route('puestos.libres') }}" class="btn">Ver puestos libres</a>
        @guest
            <a href="{{ route('login') }}" class="btn btn-ghost">Iniciar sesión</a>
        @endguest
    </div>

    @if ($dato)
        <section class="bloque" style="margin-top: 0">
            <h2 class="serif">Nuestra historia</h2>
            <div class="prosa">
                @foreach (preg_split('/\r?\n\r?\n/', trim($dato->historia)) as $parrafo)
                    <p>{{ $parrafo }}</p>
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
                $lat = (float) $dato->latitud;
                $lon = (float) $dato->longitud;
                $latTexto = number_format($lat, 6, '.', '');
                $lonTexto = number_format($lon, 6, '.', '');
                $bbox = number_format($lon - 0.01, 6, '.', '') . ',' . number_format($lat - 0.01, 6, '.', '') . ',' . number_format($lon + 0.01, 6, '.', '') . ',' . number_format($lat + 0.01, 6, '.', '');
            @endphp
            <iframe
                class="mapa"
                src="https://www.openstreetmap.org/export/embed.html?bbox={{ $bbox }}&amp;layer=mapnik&amp;marker={{ $latTexto }},{{ $lonTexto }}"
                title="Mapa referencial de la ubicación del mercado"
                loading="lazy"></iframe>
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
