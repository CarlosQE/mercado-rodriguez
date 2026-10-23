@extends('Layout.layout')

@section('title', 'Editar sección')
@section('header', 'Editar sección')

@section('content')
    <form method="POST" action="{{ route('secciones.update', $seccion) }}" class="card">
        @csrf
        @method('PUT')
        <div class="campo">
            <label class="etiqueta">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $seccion->nombre) }}" class="input">
            @error('nombre') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div class="campo">
            <label class="etiqueta">Tarifa por m² (Bs)</label>
            <input type="number" step="0.01" name="tarifa_m2" value="{{ old('tarifa_m2', $seccion->tarifa_m2) }}" class="input">
            @error('tarifa_m2') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div class="acciones">
            <button class="btn">Actualizar</button>
            <a href="{{ route('secciones.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection