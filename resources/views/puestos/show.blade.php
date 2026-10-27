@extends('Layout.layout')

@section('title', 'Puesto ' . $puesto->codigo)
@section('header', 'Puesto ' . $puesto->codigo)
@section('subtitle', $puesto->seccion->nombre . ' · ' . number_format($puesto->superficie_m2, 2) . ' m²')

@push('styles')
    <style>
        .ficha-puesto { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 20px; }
        .dato { color: var(--slate); font-size: 15px; }
        .linea-tiempo { border-left: 2px solid var(--line); margin-left: 8px; padding-left: 22px; }
        .linea-tiempo article { position: relative; margin-bottom: 22px; }
        .linea-tiempo article::before { content: ""; position: absolute; left: -29px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: var(--ink); }
        .linea-tiempo article.vigente::before { background: var(--sienna); }
    </style>
@endpush

@section('content')
    <div class="ficha-puesto">
        <section class="card" style="max-width: none">
            <h2 class="serif" style="font-size: 24px; margin-bottom: 14px">Estado del puesto</h2>
            <p class="dato">Estado: <span class="etiqueta-estado {{ $puesto->estado }}">{{ ucfirst($puesto->estado) }}</span></p>
            <p class="dato">Código: {{ $puesto->codigo }}</p>
            <p class="dato">Sección: {{ $puesto->seccion->nombre }}</p>
            <p class="dato">Superficie: {{ number_format($puesto->superficie_m2, 2) }} m²</p>
            <p class="dato">Cuota estimada: Bs {{ number_format($puesto->superficie_m2 * $puesto->seccion->tarifa_m2, 2) }} por mes</p>
        </section>

        <section class="card" style="max-width: none">
            <h2 class="serif" style="font-size: 24px; margin-bottom: 14px">Titular vigente</h2>
            @if ($puesto->asignacionVigente)
                @php $vigente = $puesto->asignacionVigente; @endphp
                <p>{{ $vigente->comerciante->apellidos }}, {{ $vigente->comerciante->nombres }}</p>
                <p class="dato">Documento: {{ $vigente->comerciante->documento }}</p>
                <p class="dato">Desde: {{ $vigente->fecha_inicio->format('d/m/Y') }}</p>
                <p class="dato">Motivo: {{ $vigente->motivo }}</p>
            @else
                <p style="color: var(--slate)">El puesto no tiene un titular vigente.</p>
            @endif
        </section>
    </div>

    <section style="margin-top: 40px">
        <h2 class="serif" style="font-size: 26px; margin-bottom: 20px">Historial de asignaciones</h2>
        <div class="linea-tiempo">
            @forelse ($puesto->asignaciones as $asignacion)
                <article class="{{ $asignacion->fecha_fin ? '' : 'vigente' }}">
                    <p>
                        <strong>{{ $asignacion->comerciante->apellidos }}, {{ $asignacion->comerciante->nombres }}</strong>
                        <span class="etiqueta-estado">· {{ $asignacion->fecha_fin ? 'Cerrada' : 'Vigente' }}</span>
                    </p>
                    <p class="dato">{{ $asignacion->fecha_inicio->format('d/m/Y') }} — {{ $asignacion->fecha_fin ? $asignacion->fecha_fin->format('d/m/Y') : 'hoy' }}</p>
                    <p class="dato">{{ $asignacion->motivo }}</p>
                </article>
            @empty
                <p style="color: var(--slate)">Sin asignaciones registradas.</p>
            @endforelse
        </div>
    </section>

    <section style="margin-top: 40px">
        <h2 class="serif" style="font-size: 26px; margin-bottom: 20px">Últimas obligaciones</h2>
        <div class="artefacto">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>Importe</th>
                        <th>Vence</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($obligaciones as $obligacion)
                        @php $vencida = $obligacion->estado === 'pendiente' && $obligacion->fecha_vencimiento->lt(today()); @endphp
                        <tr>
                            <td>{{ ucfirst($obligacion->periodo->translatedFormat('F Y')) }}</td>
                            <td>Bs {{ number_format($obligacion->importe, 2) }}</td>
                            <td>{{ $obligacion->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td><span class="etiqueta-estado {{ $vencida ? 'clausurado' : '' }}">{{ $vencida ? 'Vencida' : ucfirst($obligacion->estado) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center; color: var(--slate)">Sin obligaciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="acciones" style="margin-top: 28px">
        <a href="{{ route('mapa.index') }}" class="btn btn-ghost">Volver al mapa</a>
        @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN))
            <a href="{{ route('puestos.edit', $puesto) }}" class="btn">Editar puesto</a>
            <a href="{{ route('asignaciones.create') }}" class="btn btn-ghost">Asignar puesto</a>
        @endif
    </div>

    @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN) && $puesto->asignacionVigente)
        <form method="POST" action="{{ route('asignaciones.cerrar', $puesto->asignacionVigente) }}" class="acciones" style="margin-top: 16px" onsubmit="return confirm('¿Cerrar la asignación vigente?')">
            @csrf
            @method('PATCH')
            <input type="date" name="fecha_fin" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" min="{{ $puesto->asignacionVigente->fecha_inicio->toDateString() }}" class="input" style="width: auto">
            <button class="btn btn-ghost btn-sm">Cerrar asignación</button>
        </form>
    @endif
@endsection
