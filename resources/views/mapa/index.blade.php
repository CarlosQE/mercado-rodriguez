@extends('Layout.layout')

@section('title', 'Mapa de puestos')
@section('header', 'Mapa de puestos')
@section('subtitle', 'Cada recuadro representa un puesto. Toca uno para ver su ficha y su historial.')

@push('styles')
    <style>
        .leyenda { display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 28px; }
        .leyenda span { display: inline-flex; align-items: center; gap: 8px; font-size: 15px; color: var(--slate); }
        .muestra { width: 22px; height: 22px; border-radius: 7px; display: inline-block; }
        .muestra.libre { background: #fff; border: 1px solid var(--ink); }
        .muestra.ocupado { background: var(--ink); }
        .muestra.clausurado { background: var(--peach); }
        .bloque-seccion { margin-bottom: 40px; }
        .bloque-seccion h2 { font-size: 26px; margin-bottom: 6px; }
        .mapa-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(76px, 1fr)); gap: 10px; margin-top: 14px; }
        .puesto { display: flex; align-items: center; justify-content: center; min-height: 52px; padding: 8px; border-radius: 14px; border: 1px solid var(--ink); background: #fff; color: var(--ink); text-decoration: none; font-size: 14px; text-align: center; }
        .puesto:hover { opacity: .82; }
        .puesto.ocupado { background: var(--ink); color: #fff; }
        .puesto.clausurado { background: var(--peach); color: var(--sienna); border-color: var(--peach); }
    </style>
@endpush

@section('content')
    <div class="leyenda">
        <span><i class="muestra libre"></i> Libre</span>
        <span><i class="muestra ocupado"></i> Ocupado</span>
        <span><i class="muestra clausurado"></i> Clausurado</span>
    </div>

    <div class="herramientas">
        <form method="GET" action="{{ route('mapa.index') }}" class="filtros">
            <select name="seccion_id" class="input">
                <option value="">Todas las secciones</option>
                @foreach ($todasLasSecciones as $s)
                    <option value="{{ $s->id }}" @selected(request('seccion_id') == $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
            <button class="btn btn-ghost">Filtrar</button>
        </form>
    </div>

    @forelse ($secciones as $seccion)
        <section class="bloque-seccion">
            <h2 class="serif">{{ $seccion->nombre }}</h2>
            <span class="etiqueta-estado">
                {{ $seccion->puestos_count }} puestos · {{ $seccion->libres_count }} libres · {{ $seccion->ocupados_count }} ocupados · {{ $seccion->clausurados_count }} clausurados
            </span>

            <div class="mapa-grid">
                @foreach ($seccion->puestos as $puesto)
                    @php $titular = $puesto->asignacionVigente?->comerciante; @endphp
                    <a
                        href="{{ route('puestos.show', $puesto) }}"
                        class="puesto {{ $puesto->estado }}"
                        title="{{ $puesto->codigo }} · {{ ucfirst($puesto->estado) }}@if ($titular) · Titular: {{ $titular->apellidos }}, {{ $titular->nombres }}@endif">
                        {{ $puesto->codigo }}
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <p style="color: var(--slate)">No hay secciones con puestos para mostrar.</p>
    @endforelse
@endsection
