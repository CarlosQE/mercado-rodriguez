@extends('Layout.layout')

@section('title', 'Pagos')
@section('header', 'Pagos')
@section('subtitle', 'Cada pago cubre una o más obligaciones completas de un comerciante.')

@section('content')
    <div class="herramientas">
        <form method="GET" class="filtros">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Comprobante o comerciante" class="input">
            <select name="metodo" class="input">
                <option value="">Todos los métodos</option>
                <option value="efectivo" @selected(request('metodo') === 'efectivo')>Efectivo</option>
                <option value="transferencia" @selected(request('metodo') === 'transferencia')>Transferencia</option>
            </select>
            <button class="btn btn-ghost">Filtrar</button>
        </form>
        <a href="{{ route('pagos.create') }}" class="btn">Nuevo pago</a>
    </div>

    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Comprobante</th>
                    <th>Fecha</th>
                    <th>Comerciante</th>
                    <th>Método</th>
                    <th>Obligaciones</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pagos as $p)
                    <tr>
                        <td>{{ $p->comprobante }}</td>
                        <td>{{ $p->fecha_pago->format('d/m/Y') }}</td>
                        <td>{{ $p->comerciante->apellidos }}, {{ $p->comerciante->nombres }}</td>
                        <td>{{ ucfirst($p->metodo) }}</td>
                        <td>{{ $p->obligaciones_count }}</td>
                        <td>Bs {{ number_format($p->total, 2) }}</td>
                        <td><a href="{{ route('pagos.show', $p) }}" class="btn btn-ghost btn-sm">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center; color: var(--slate)">No hay pagos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px">{{ $pagos->links() }}</div>
@endsection