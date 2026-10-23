@extends('Layout.layout')

@section('title', 'Tablero de comunicados')
@section('header', 'Tablero de comunicados')
@section('subtitle', 'Avisos importantes para los comerciantes y las personas que visitan el mercado.')

@section('content')
    @if ($comunicados->isEmpty())
        <div class="card" style="max-width: 640px">
            <p style="color: var(--slate)">No hay avisos vigentes por el momento. Vuelve a consultar pronto.</p>
        </div>
    @else
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
                        @if ($comunicado->fecha_vencimiento)
                            <span>Hasta {{ $comunicado->fecha_vencimiento->translatedFormat('d M Y') }}</span>
                        @endif
                    </div>

                    <h3 class="serif">{{ $comunicado->titulo }}</h3>
                    <p class="comunicado-contenido">{!! nl2br(e($comunicado->contenido)) !!}</p>
                </article>
            @endforeach
        </div>
    @endif
@endsection
