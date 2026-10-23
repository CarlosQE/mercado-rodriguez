@extends('Layout.layout')

@section('title', 'Nueva asignación')
@section('header', 'Nueva asignación')
@section('subtitle', 'Solo aparecen puestos libres y comerciantes activos.')

@section('content')
    <form method="POST" action="{{ route('asignaciones.store') }}" class="card">
        @csrf

        <div class="campo">
            <label class="etiqueta">Puesto</label>
            <select name="puesto_id" class="input">
                <option value="">Selecciona un puesto</option>
                @foreach ($puestos as $p)
                    <option value="{{ $p->id }}" @selected(old('puesto_id') == $p->id)>{{ $p->codigo }} · {{ $p->seccion->nombre }} · {{ number_format($p->superficie_m2, 2) }} m²</option>
                @endforeach
            </select>
            @error('puesto_id') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Comerciante</label>
            <select name="comerciante_id" class="input">
                <option value="">Selecciona un comerciante</option>
                @foreach ($comerciantes as $c)
                    <option value="{{ $c->id }}" @selected(old('comerciante_id') == $c->id)>{{ $c->apellidos }}, {{ $c->nombres }} ({{ $c->documento }})</option>
                @endforeach
            </select>
            @error('comerciante_id') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Fecha de inicio</label>
            <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', now()->toDateString()) }}" class="input">
            @error('fecha_inicio') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Motivo</label>
            <input type="text" name="motivo" list="motivos" value="{{ old('motivo') }}" class="input">
            <datalist id="motivos">
                <option value="Asignación inicial">
                <option value="Transferencia">
                <option value="Renovación">
            </datalist>
            @error('motivo') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="acciones">
            <button class="btn">Guardar</button>
            <a href="{{ route('asignaciones.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection