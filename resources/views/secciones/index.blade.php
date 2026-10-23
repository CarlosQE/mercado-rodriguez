@extends('Layout.layout')

@section('title', 'Secciones')
@section('header', 'Secciones')
@section('subtitle', 'Cada sección define la tarifa por metro cuadrado que se cobra a sus puestos.')

@section('content')
    <div class="herramientas">
        <form method="GET" class="filtros">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar sección" class="input">
            <button class="btn btn-ghost">Buscar</button>
        </form>
        <a href="{{ route('secciones.create') }}" class="btn">Nueva sección</a>
    </div>

    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tarifa por m²</th>
                    <th>Puestos</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($secciones as $seccion)
                    <tr>
                        <td>{{ $seccion->nombre }}</td>
                        <td>Bs {{ number_format($seccion->tarifa_m2, 2) }}</td>
                        <td>{{ $seccion->puestos_count }}</td>
                        <td>
                            <div class="acciones">
                                <a href="{{ route('secciones.edit', $seccion) }}" class="btn btn-ghost btn-sm">Editar</a>
                                <form method="POST" action="{{ route('secciones.destroy', $seccion) }}" onsubmit="return confirm('¿Eliminar la sección?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-ghost btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center; color: var(--slate)">No hay secciones.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px">{{ $secciones->links() }}</div>
@endsection