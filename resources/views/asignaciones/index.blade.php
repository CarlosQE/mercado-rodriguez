@extends('Layout.layout')

@section('title', 'Asignaciones')
@section('header', 'Asignaciones')
@section('subtitle', 'Historial de quién ocupa cada puesto. Una asignación sin fecha de fin está vigente.')

@section('content')
    @error('fecha_fin')
        <div class="aviso aviso-error">{{ $message }}</div>
    @enderror

    <div class="herramientas">
        <form method="GET" class="filtros">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Puesto o comerciante" class="input">
            <select name="estado" class="input">
                <option value="">Todas</option>
                <option value="vigente" @selected(request('estado') === 'vigente')>Vigentes</option>
                <option value="cerrada" @selected(request('estado') === 'cerrada')>Cerradas</option>
            </select>
            <button class="btn btn-ghost">Filtrar</button>
        </form>
        <a href="{{ route('asignaciones.create') }}" class="btn">Nueva asignación</a>
    </div>

    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Comerciante</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Motivo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($asignaciones as $a)
                    <tr>
                        <td>{{ $a->puesto->codigo }}<br><span class="etiqueta-estado">{{ $a->puesto->seccion->nombre }}</span></td>
                        <td>{{ $a->comerciante->apellidos }}, {{ $a->comerciante->nombres }}</td>
                        <td>{{ $a->fecha_inicio->format('d/m/Y') }}</td>
                        <td>{{ $a->fecha_fin ? $a->fecha_fin->format('d/m/Y') : 'Vigente' }}</td>
                        <td>{{ $a->motivo }}</td>
                        <td>
                            @if (! $a->fecha_fin)
                                <form method="POST" action="{{ route('asignaciones.cerrar', $a) }}" class="acciones" onsubmit="return confirm('¿Cerrar la asignación?')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="date" name="fecha_fin" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" min="{{ $a->fecha_inicio->toDateString() }}" class="input" style="width:auto; padding:5px 10px; font-size:14px">
                                    <button class="btn btn-ghost btn-sm">Cerrar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; color: var(--slate)">No hay asignaciones.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px">{{ $asignaciones->links() }}</div>
@endsection