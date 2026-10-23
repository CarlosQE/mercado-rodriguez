@extends('Layout.layout')

@section('title', 'Puestos')
@section('header', 'Puestos')
@section('subtitle', 'Cada puesto pertenece a una sección y se cobra según su superficie.')

@section('content')
    <div class="herramientas">
        <form method="GET" class="filtros">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Código" class="input">
            <select name="seccion_id" class="input">
                <option value="">Todas las secciones</option>
                @foreach ($secciones as $s)
                    <option value="{{ $s->id }}" @selected(request('seccion_id') == $s->id)>{{ $s->nombre }}</option>
                @endforeach
            </select>
            <select name="estado" class="input">
                <option value="">Todos los estados</option>
                @foreach (['libre', 'ocupado', 'clausurado'] as $e)
                    <option value="{{ $e }}" @selected(request('estado') === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
            <button class="btn btn-ghost">Filtrar</button>
        </form>
        @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN))
            <a href="{{ route('puestos.create') }}" class="btn">Nuevo puesto</a>
        @endif
    </div>

    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Sección</th>
                    <th>Superficie</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($puestos as $puesto)
                    <tr>
                        <td>{{ $puesto->codigo }}</td>
                        <td>{{ $puesto->seccion->nombre }}</td>
                        <td>{{ number_format($puesto->superficie_m2, 2) }} m²</td>
                        <td><span class="etiqueta-estado {{ $puesto->estado }}">{{ ucfirst($puesto->estado) }}</span></td>
                        <td>
                            @if (auth()->user()->tieneRol(\App\Models\User::ROL_ADMIN))
                                <div class="acciones">
                                    <a href="{{ route('puestos.edit', $puesto) }}" class="btn btn-ghost btn-sm">Editar</a>
                                    <form method="POST" action="{{ route('puestos.destroy', $puesto) }}" onsubmit="return confirm('¿Eliminar el puesto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-ghost btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; color: var(--slate)">No hay puestos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px">{{ $puestos->links() }}</div>
@endsection