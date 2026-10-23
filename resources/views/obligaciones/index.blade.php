@extends('Layout.layout')

@section('title', 'Obligaciones')
@section('header', 'Obligaciones')
@section('subtitle', 'Cobro mensual por puesto: superficie por la tarifa de su sección, fijado al emitir.')

@section('content')
    @if ($errors->any())
        <div class="aviso aviso-error">{{ $errors->first() }}</div>
    @endif

    @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN))
        <div class="herramientas">
            <form method="POST" action="{{ route('obligaciones.emitir') }}" class="filtros">
                @csrf
                <input type="text" name="periodo" value="{{ old('periodo', now()->format('Y-m')) }}" placeholder="AAAA-MM" pattern="\d{4}-\d{2}" class="input" style="width:120px">
                <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" class="input" title="Vencimiento (vacío = día 10)">
                <button class="btn">Emitir período</button>
            </form>
        </div>
    @endif

    <div class="herramientas">
        <form method="GET" class="filtros">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Puesto o comerciante" class="input">
            <input type="text" name="periodo" value="{{ request('periodo') }}" placeholder="AAAA-MM" pattern="\d{4}-\d{2}" class="input" style="width:120px">
            <select name="estado" class="input" onchange="this.form.submit()">
                <option value="">Todas</option>
                <option value="pendiente" @selected(request('estado') === 'pendiente')>Por vencer</option>
                <option value="vencida" @selected(request('estado') === 'vencida')>Vencidas</option>
                <option value="pagada" @selected(request('estado') === 'pagada')>Pagadas</option>
            </select>
            <button class="btn btn-ghost">Filtrar</button>
        </form>
        @if (request('estado') !== 'pagada')
            <span class="etiqueta-estado">Pendiente en esta vista: Bs {{ number_format($pendiente, 2) }}</span>
        @endif
    </div>

    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Período</th>
                    <th>Puesto</th>
                    <th>Comerciante</th>
                    <th>Vence</th>
                    <th>Importe</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($obligaciones as $o)
                    @php $vencida = $o->estado === 'pendiente' && $o->fecha_vencimiento->lt(today()); @endphp
                    <tr>
                        <td>{{ ucfirst($o->periodo->translatedFormat('F Y')) }}</td>
                        <td>{{ $o->asignacion->puesto->codigo }}<br><span class="etiqueta-estado">{{ $o->asignacion->puesto->seccion->nombre }}</span></td>
                        <td>{{ $o->asignacion->comerciante->apellidos }}, {{ $o->asignacion->comerciante->nombres }}</td>
                        <td>{{ $o->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td>Bs {{ number_format($o->importe, 2) }}</td>
                        <td>
                            <span class="etiqueta-estado {{ $vencida ? 'clausurado' : '' }}">{{ $vencida ? 'Vencida' : ucfirst($o->estado) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; color: var(--slate)">No hay obligaciones.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px">{{ $obligaciones->links() }}</div>
@endsection