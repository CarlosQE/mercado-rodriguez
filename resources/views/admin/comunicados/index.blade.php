@extends('Layout.layout')

@section('title', 'Comunicados')
@section('header', 'Comunicados')
@section('subtitle', 'Publica y administra los avisos del tablero. También puedes revisar los vencidos e inactivos.')

@section('content')
    <div class="herramientas">
        <span class="etiqueta-estado">{{ $comunicados->total() }} comunicados registrados</span>
        <a href="{{ route('admin.comunicados.create') }}" class="btn">Nuevo comunicado</a>
    </div>

    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Publicación</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comunicados as $comunicado)
                    @php
                        $vencido = $comunicado->fecha_vencimiento && $comunicado->fecha_vencimiento->lt(today());
                        $estado = ! $comunicado->activo ? 'Inactivo' : ($vencido ? 'Vencido' : 'Vigente');
                    @endphp
                    <tr>
                        <td>
                            {{ $comunicado->titulo }}
                            @if ($comunicado->fijado)
                                <span class="etiqueta-comunicado" style="margin-left: 6px">Fijado</span>
                            @endif
                        </td>
                        <td><span class="etiqueta-comunicado {{ $comunicado->tipo }}">{{ ucfirst($comunicado->tipo) }}</span></td>
                        <td>{{ $comunicado->fecha_publicacion->format('d/m/Y') }}</td>
                        <td>{{ $comunicado->fecha_vencimiento?->format('d/m/Y') ?? 'Sin vencimiento' }}</td>
                        <td>
                            <span class="etiqueta-estado {{ $estado === 'Vigente' ? '' : 'clausurado' }}">{{ $estado }}</span>
                        </td>
                        <td>
                            <div class="acciones">
                                <a href="{{ route('admin.comunicados.edit', $comunicado) }}" class="btn btn-ghost btn-sm">Editar</a>
                                <form method="POST" action="{{ route('admin.comunicados.destroy', $comunicado) }}" onsubmit="return confirm('¿Eliminar este comunicado?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-ghost btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; color: var(--slate)">No hay comunicados registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px">{{ $comunicados->links() }}</div>
@endsection
