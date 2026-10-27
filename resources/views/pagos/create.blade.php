@extends('Layout.layout')

@section('title', 'Nuevo pago')
@section('header', 'Nuevo pago')
@section('subtitle', 'Elige un comerciante y marca las obligaciones que paga. Se pagan completas.')

@section('content')
    <form method="GET" action="{{ route('pagos.create') }}" class="filtros" style="margin-bottom: 24px">
        <select name="comerciante_id" class="input" onchange="this.form.submit()" style="width: 100%; min-width: 0">
            <option value="">Selecciona un comerciante con deudas</option>
            @foreach ($comerciantes as $c)
                <option value="{{ $c->id }}" @selected(optional($comerciante)->id === $c->id)>{{ $c->apellidos }}, {{ $c->nombres }} ({{ $c->documento }})</option>
            @endforeach
        </select>
        <noscript><button class="btn btn-ghost">Ver pendientes</button></noscript>
    </form>

    @if ($comerciante && $pendientes->isEmpty())
        <div class="aviso">Este comerciante no tiene obligaciones pendientes.</div>
    @endif

    @if ($comerciante && $pendientes->isNotEmpty())
        <form method="POST" action="{{ route('pagos.store') }}">
            @csrf
            <input type="hidden" name="comerciante_id" value="{{ $comerciante->id }}">

            @error('obligaciones') <p class="error" style="margin-bottom: 12px">{{ $message }}</p> @enderror
            @error('comerciante_id') <p class="error" style="margin-bottom: 12px">{{ $message }}</p> @enderror

            <div class="artefacto">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="todos" title="Seleccionar todas"></th>
                            <th>Período</th>
                            <th>Puesto</th>
                            <th>Vence</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendientes as $o)
                            <tr>
                                <td>
                                    <input type="checkbox" class="chk" name="obligaciones[]" value="{{ $o->id }}"
                                           data-importe="{{ $o->importe }}"
                                           @checked(in_array($o->id, old('obligaciones', [])))>
                                </td>
                                <td>{{ ucfirst($o->periodo->translatedFormat('F Y')) }}</td>
                                <td>{{ $o->asignacion->puesto->codigo }}</td>
                                <td>
                                    @if ($o->fecha_vencimiento->lt(today()))
                                        <span class="etiqueta-estado clausurado">{{ $o->fecha_vencimiento->format('d/m/Y') }}</span>
                                    @else
                                        {{ $o->fecha_vencimiento->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>Bs {{ number_format($o->importe, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card" style="margin-top: 24px">
                <div class="campo">
                    <label class="etiqueta">Fecha de pago</label>
                    <input type="date" name="fecha_pago" value="{{ old('fecha_pago', now()->toDateString()) }}" max="{{ now()->toDateString() }}" class="input">
                    @error('fecha_pago') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="campo">
                    <label class="etiqueta">Método</label>
                    <select name="metodo" class="input">
                        <option value="efectivo" @selected(old('metodo') === 'efectivo')>Efectivo</option>
                        <option value="transferencia" @selected(old('metodo') === 'transferencia')>Transferencia</option>
                    </select>
                    @error('metodo') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="campo">
                    <label class="etiqueta">Total a pagar</label>
                    <p class="serif" id="total" style="font-size: 32px">Bs 0.00</p>
                </div>

                <div class="acciones">
                    <button class="btn">Registrar pago</button>
                    <a href="{{ route('pagos.index') }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </div>
        </form>
    @endif
@endsection

@section('script')
<script>
    const checks = document.querySelectorAll('.chk');
    const total = document.getElementById('total');
    const todos = document.getElementById('todos');

    function recalcular() {
        if (!total) return;
        let suma = 0;
        checks.forEach(c => { if (c.checked) suma += parseFloat(c.dataset.importe); });
        total.textContent = 'Bs ' + suma.toFixed(2);
    }

    checks.forEach(c => c.addEventListener('change', recalcular));
    if (todos) {
        todos.addEventListener('change', () => {
            checks.forEach(c => c.checked = todos.checked);
            recalcular();
        });
    }
    recalcular();
</script>
@endsection