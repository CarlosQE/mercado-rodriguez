@extends('Layout.layout')

@section('title', 'Pago ' . $pago->comprobante)
@section('header', 'Pago ' . $pago->comprobante)
@section('subtitle', $pago->comerciante->apellidos . ', ' . $pago->comerciante->nombres . ' · ' . $pago->fecha_pago->format('d/m/Y') . ' · ' . ucfirst($pago->metodo))

@section('content')
    <div class="artefacto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Período</th>
                    <th>Puesto</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pago->obligaciones as $o)
                    <tr>
                        <td>{{ ucfirst($o->periodo->translatedFormat('F Y')) }}</td>
                        <td>{{ $o->asignacion->puesto->codigo }}</td>
                        <td>Bs {{ number_format($o->importe, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2"><strong>Total</strong></td>
                    <td><strong>Bs {{ number_format($pago->total, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="acciones" style="margin-top: 24px">
        <a href="{{ route('pagos.index') }}" class="btn btn-ghost">Volver</a>
        <a href="{{ route('pagos.create') }}" class="btn">Nuevo pago</a>
    </div>
@endsection