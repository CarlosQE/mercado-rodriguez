@extends('Layout.layout')

@section('title', 'Administración del mercado')
@section('header', 'Administración del mercado')
@section('subtitle', 'Edita la ficha pública que se muestra en la portada.')

@section('content')
    <form method="POST" action="{{ route('admin.mercado.update') }}" class="card" style="max-width: 760px">
        @csrf
        @method('PUT')

        <div class="campo">
            <label class="etiqueta">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $dato->nombre) }}" class="input">
            @error('nombre') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Eslogan (opcional)</label>
            <input type="text" name="eslogan" value="{{ old('eslogan', $dato->eslogan) }}" class="input">
            @error('eslogan') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Historia</label>
            <textarea name="historia" rows="10" class="input">{{ old('historia', $dato->historia) }}</textarea>
            <p class="etiqueta" style="margin-top: 6px">Separa los párrafos con una línea en blanco.</p>
            @error('historia') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion', $dato->direccion) }}" class="input">
            @error('direccion') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Referencia (opcional)</label>
            <input type="text" name="referencia" value="{{ old('referencia', $dato->referencia) }}" class="input">
            @error('referencia') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Latitud</label>
            <input type="number" step="0.0000001" name="latitud" value="{{ old('latitud', $dato->latitud) }}" class="input">
            @error('latitud') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Longitud</label>
            <input type="number" step="0.0000001" name="longitud" value="{{ old('longitud', $dato->longitud) }}" class="input">
            @error('longitud') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Horario de atención</label>
            <input type="text" name="horario_atencion" value="{{ old('horario_atencion', $dato->horario_atencion) }}" class="input">
            @error('horario_atencion') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $dato->telefono) }}" class="input">
            @error('telefono') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Correo (opcional)</label>
            <input type="email" name="email" value="{{ old('email', $dato->email) }}" class="input">
            @error('email') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="acciones">
            <button class="btn">Guardar ficha</button>
            <a href="{{ route('inicio') }}" class="btn btn-ghost">Ver portada</a>
        </div>
    </form>
@endsection
