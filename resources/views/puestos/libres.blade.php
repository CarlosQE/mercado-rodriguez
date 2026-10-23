@extends('Layout.layout')

@section('title', 'Puestos libres')
@section('header', 'Puestos libres')
@section('subtitle', 'Puestos disponibles para una nueva asignación. La cuota es una estimación mensual: superficie por la tarifa de la sección.')

@section('content')
    <div class="herramientas">
        <form method="GET" action="{{ route('puestos.libres') }}" class="filtros">
            <select name="seccion_id" class="input">
                <option value="">Todas las secciones</option>
                @foreach ($secciones as $s)
                    <option value="{{ $s->id }}" @selected(request('seccion_id') == $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
            <button class="btn btn-ghost">Filtrar</button>
        </form>
        <span class="etiqueta-estado">{{ $puestos->total() }} puestos libres</span>
    </div>

    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Sección</th>
                    <th>Superficie</th>
                    <th>Cuota mensual estimada</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($puestos as $puesto)
                    <tr>
                        <td>{{ $puesto->codigo }}</td>
                        <td>{{ $puesto->seccion->nombre }}</td>
                        <td>{{ number_format($puesto->superficie_m2, 2) }} m²</td>
                        <td>Bs {{ number_format($puesto->superficie_m2 * $puesto->seccion->tarifa_m2, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center; color: var(--slate)">No hay puestos libres con este filtro.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px">{{ $puestos->links() }}</div>
@endsection
