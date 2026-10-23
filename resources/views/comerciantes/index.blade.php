@extends('Layout.layout')

@section('title', 'Comerciantes')
@section('header', 'Comerciantes')
@section('subtitle', 'Personas que pueden recibir un puesto y registrar pagos.')

@section('content')
    <div class="herramientas">
        <form method="GET" class="filtros">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o documento" class="input">
            <select name="estado" class="input">
                <option value="">Todos</option>
                <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
            </select>
            <button class="btn btn-ghost">Filtrar</button>
        </form>
        <a href="{{ route('comerciantes.create') }}" class="btn">Nuevo comerciante</a>
    </div>

    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Rubro</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comerciantes as $c)
                    <tr>
                        <td>{{ $c->documento }}</td>
                        <td>{{ $c->apellidos }}, {{ $c->nombres }}</td>
                        <td>{{ $c->rubro }}</td>
                        <td>{{ $c->telefono }}</td>
                        <td>
                            <span class="etiqueta-estado {{ $c->activo ? '' : 'clausurado' }}">{{ $c->activo ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                        <td>
                            <div class="acciones">
                                <a href="{{ route('comerciantes.edit', $c) }}" class="btn btn-ghost btn-sm">Editar</a>
                                <form method="POST" action="{{ route('comerciantes.destroy', $c) }}" onsubmit="return confirm('¿Eliminar al comerciante?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-ghost btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; color: var(--slate)">No hay comerciantes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px">{{ $comerciantes->links() }}</div>
@endsection