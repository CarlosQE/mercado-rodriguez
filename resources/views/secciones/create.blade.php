@extends('Layout.layout')

@section('title', 'Nueva sección')
@section('header', 'Nueva sección')

@section('content')
    <form method="POST" action="{{ route('secciones.store') }}" class="card">
        @csrf
        <div class="campo">
            <label class="etiqueta">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" class="input">
            @error('nombre') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div class="campo">
            <label class="etiqueta">Prefijo</label>
            <input type="text" name="prefijo" value="{{ old('prefijo') }}" maxlength="3" placeholder="VER" class="input" style="text-transform: uppercase">
            <p class="etiqueta">Se usa en el código de los puestos, por ejemplo VER-001. No se podrá cambiar después.</p>
            @error('prefijo') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div class="campo">
            <label class="etiqueta">Tarifa por m² (Bs)</label>
            <input type="number" step="0.01" name="tarifa_m2" value="{{ old('tarifa_m2') }}" class="input">
            @error('tarifa_m2') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div class="acciones">
            <button class="btn">Guardar</button>
            <a href="{{ route('secciones.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection