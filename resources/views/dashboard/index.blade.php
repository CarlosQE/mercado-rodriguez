@extends('Layout.layout')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subtitle', 'Indicadores del mercado, deuda pendiente y movimientos recientes.')

@push('styles')
    <style>
        .indicadores { display: grid; grid-template-columns: minmax(0, 1fr); grid-auto-rows: 1fr; gap: 16px; margin-bottom: 32px; }
        @media (min-width: 640px) { .indicadores { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (min-width: 1024px) { .indicadores { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        .indicador { background: var(--mist); border-radius: 20px; padding: 24px; height: 100%; min-width: 0; display: flex; flex-direction: column; }
        .indicador .cifra { font-size: clamp(22px, 2.4vw, 34px); line-height: 1.15; margin-top: 8px; white-space: nowrap; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }
        .indicador .detalle { font-size: 14px; color: var(--slate); margin-top: 6px; }
        .panel { background: var(--paper); border-radius: 24px; padding: 28px; box-shadow: 0 0 0 1px rgba(4,23,43,.05), 0 12px 24px -18px rgba(0,0,0,.25); }
        .panel h2 { font-size: 24px; margin-bottom: 18px; }
        .panel + .panel { margin-top: 20px; }
        .dos-columnas { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap: 20px; margin-top: 20px; }
        .barra-ocupacion { height: 10px; background: var(--mist); border-radius: 9999px; overflow: hidden; margin-top: 6px; }
        .barra-ocupacion span { display: block; height: 100%; background: var(--ink); border-radius: 9999px; }
        .grafico { display: flex; align-items: flex-end; gap: 16px; height: 230px; padding-top: 12px; }
        .grafico .columna { flex: 1; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; gap: 6px; }
        .grafico .barra-mes { width: 100%; max-width: 58px; min-height: 2px; background: var(--ink); border-radius: 10px 10px 0 0; transition: opacity 180ms ease-out; }
        .grafico .barra-mes:hover { opacity: .82; }
        .grafico .barra-mes.mes-actual { background: var(--peach); }
        .grafico .monto-mes { animation: fila-entrada 200ms ease-out backwards; }
        .tabla-contenedor-deudores > .tabla { min-width: 0; }

        @media (max-width: 639.98px) {
            .panel { padding: 20px; }
            .grafico { gap: 8px; }
            .grafico .monto-mes { font-size: 11px; }
            .tabla-deudores th:nth-child(2), .tabla-deudores td:nth-child(2) { display: none; }
        }
    </style>
@endpush

@section('content')
    @if ($hayAlerta)
        <div class="aviso aviso-error">
            <strong>Alertas.</strong>
            @if ($vencidasCantidad > 0)
                Hay {{ $vencidasCantidad }} obligaciones vencidas por Bs {{ number_format($vencidasMonto, 2) }}.
            @endif
            @if ($clausurados > 0)
                Hay {{ $clausurados }} puestos clausurados.
            @endif
        </div>
    @endif

    <div class="herramientas">
        <span class="etiqueta-estado">Período actual: {{ ucfirst(now()->translatedFormat('F Y')) }}</span>
        <form method="POST" action="{{ route('obligaciones.emitir') }}" onsubmit="return confirm('¿Emitir las obligaciones del mes actual?')">
            @csrf
            <input type="hidden" name="periodo" value="{{ now()->format('Y-m') }}">
            <button class="btn">Emitir obligaciones del mes</button>
        </form>
    </div>

    <div class="indicadores">
        <div class="indicador">
            <span class="etiqueta">Puestos ocupados</span>
            <p class="cifra serif" data-contador data-valor="{{ $ocupados }}" data-formato="entero">{{ number_format($ocupados) }}</p>
            <p class="detalle">{{ $porcentajeOcupacion }}% de ocupación sobre {{ $totalPuestos }} puestos</p>
        </div>
        <div class="indicador">
            <span class="etiqueta">Puestos libres</span>
            <p class="cifra serif" data-contador data-valor="{{ $libres }}" data-formato="entero">{{ number_format($libres) }}</p>
        </div>
        <div class="indicador">
            <span class="etiqueta">Puestos clausurados</span>
            <p class="cifra serif" data-contador data-valor="{{ $clausurados }}" data-formato="entero">{{ number_format($clausurados) }}</p>
        </div>
        <div class="indicador">
            <span class="etiqueta">Recaudado este mes</span>
            <p class="cifra serif" data-contador data-valor="{{ $recaudadoMes }}" data-formato="bs">Bs {{ number_format($recaudadoMes, 2) }}</p>
        </div>
        <div class="indicador">
            <span class="etiqueta">Pendiente total</span>
            <p class="cifra serif" data-contador data-valor="{{ $pendienteTotal }}" data-formato="bs">Bs {{ number_format($pendienteTotal, 2) }}</p>
        </div>
        <div class="indicador">
            <span class="etiqueta">Obligaciones vencidas</span>
            <p class="cifra serif" data-contador data-valor="{{ $vencidasCantidad }}" data-formato="entero">{{ number_format($vencidasCantidad) }}</p>
            <p class="detalle">Bs {{ number_format($vencidasMonto, 2) }} sin cobrar</p>
        </div>
    </div>

    <div class="dos-columnas">
        <section class="panel">
            <h2 class="serif">Ocupación por sección</h2>
            @forelse ($secciones as $seccion)
                @php
                    $porcentaje = $seccion->puestos_count > 0
                        ? round($seccion->ocupados_count / $seccion->puestos_count * 100, 1)
                        : 0;
                @endphp
                <div style="margin-bottom: 18px">
                    <div style="display: flex; justify-content: space-between; gap: 12px">
                        <span>{{ $seccion->nombre }}</span>
                        <span class="etiqueta-estado">{{ $seccion->ocupados_count }}/{{ $seccion->puestos_count }} · {{ $porcentaje }}%</span>
                    </div>
                    <div class="barra-ocupacion">
                        <span style="width: {{ $porcentaje }}%; animation-delay: {{ min($loop->index * 100, 600) }}ms"></span>
                    </div>
                </div>
            @empty
                <p style="color: var(--slate)">No hay secciones registradas.</p>
            @endforelse
        </section>

        <section class="panel">
            <h2 class="serif">Recaudación de los últimos 6 meses</h2>
            @php
                $maxMes = max(1, $meses->max('total'));
                $mesActual = now()->format('Y-m');
            @endphp
            <div class="grafico">
                @foreach ($meses as $mes)
                    <div class="columna">
                        <span class="etiqueta-estado monto-mes" style="animation-delay: {{ 800 + $loop->index * 100 }}ms">Bs {{ number_format($mes['total'], 0) }}</span>
                        <div class="barra-mes{{ $mes['clave'] === $mesActual ? ' mes-actual' : '' }}" style="height: {{ $mes['total'] / $maxMes * 100 }}%" title="Bs {{ number_format($mes['total'], 2) }}"></div>
                        <span class="etiqueta-estado">{{ $mes['etiqueta'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="dos-columnas">
        <section class="panel">
            <h2 class="serif">Comerciantes con mayor deuda pendiente</h2>
            <div class="tabla-contenedor tabla-contenedor-deudores">
                <table class="tabla tabla-deudores">
                    <thead>
                        <tr>
                            <th>Comerciante</th>
                            <th>Documento</th>
                            <th>Deuda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deudores as $deudor)
                            <tr>
                                <td>{{ $deudor->apellidos }}, {{ $deudor->nombres }}</td>
                                <td>{{ $deudor->documento }}</td>
                                <td>Bs {{ number_format($deudor->deuda, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center; color: var(--slate)">No hay deuda pendiente.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <h2 class="serif">Últimos pagos registrados</h2>
            <div class="tabla-contenedor">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Fecha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ultimosPagos as $pago)
                            <tr>
                                <td><a href="{{ route('pagos.show', $pago) }}">{{ $pago->comprobante }}</a></td>
                                <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                <td>Bs {{ number_format($pago->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center; color: var(--slate)">No hay pagos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="panel" style="margin-top: 20px">
        <h2 class="serif">Obligaciones que vencen en los próximos 7 días</h2>
        <div class="tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Vence</th>
                        <th>Puesto</th>
                        <th>Comerciante</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($porVencer as $obligacion)
                        <tr>
                            <td>{{ $obligacion->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td>{{ $obligacion->asignacion->puesto->codigo }}</td>
                            <td>{{ $obligacion->asignacion->comerciante->apellidos }}, {{ $obligacion->asignacion->comerciante->nombres }}</td>
                            <td>Bs {{ number_format($obligacion->importe, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center; color: var(--slate)">No hay vencimientos en los próximos 7 días.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('script')
    <script>
        (() => {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            const contadores = document.querySelectorAll('[data-contador]');

            if (!contadores.length) return;

            const duracion = 900;
            const numeros = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });
            const dinero = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const suavizar = (avance) => 1 - Math.pow(1 - avance, 3);
            const formatear = (elemento, valor) => elemento.dataset.formato === 'bs'
                ? `Bs ${dinero.format(valor)}`
                : numeros.format(valor);

            contadores.forEach((elemento) => {
                const final = Number(elemento.dataset.valor);

                if (!Number.isFinite(final)) return;

                const inicio = performance.now();

                const dibujar = (ahora) => {
                    const avance = Math.min((ahora - inicio) / duracion, 1);

                    elemento.textContent = formatear(elemento, final * suavizar(avance));

                    if (avance < 1) {
                        requestAnimationFrame(dibujar);
                    } else {
                        elemento.textContent = formatear(elemento, final);
                    }
                };

                requestAnimationFrame(dibujar);
            });
        })();
    </script>
@endsection
