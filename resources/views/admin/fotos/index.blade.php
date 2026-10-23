@extends('Layout.layout')

@section('title', 'Fotos del mercado')
@section('header', 'Fotos del mercado')
@section('subtitle', 'Sube imágenes de hasta 4 MB en JPG, PNG o WebP. El orden menor aparece primero en la galería.')

@push('styles')
    <style>
        .foto-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .foto-card { background: var(--mist); border-radius: 24px; padding: 20px; }
        .foto-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 16px; display: block; margin-bottom: 16px; }
        .foto-card .acciones { margin-top: 8px; }
    </style>
@endpush

@section('content')
    <form method="POST" action="{{ route('admin.fotos.store') }}" enctype="multipart/form-data" class="card" style="max-width: 760px; margin-bottom: 40px">
        @csrf

        <div class="campo">
            <label class="etiqueta">Título</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" class="input">
            @error('titulo') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Descripción (opcional)</label>
            <input type="text" name="descripcion" value="{{ old('descripcion') }}" class="input">
            @error('descripcion') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Orden</label>
            <input type="number" name="orden" value="{{ old('orden', 0) }}" min="0" class="input">
            @error('orden') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta">Imagen</label>
            <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" class="input">
            @error('imagen') <p class="error">{{ $message }}</p> @enderror
        </div>

        <button class="btn">Subir foto</button>
    </form>

    <div class="foto-grid">
        @forelse ($fotos as $foto)
            <div class="foto-card">
                <img src="{{ $foto->url }}" alt="{{ $foto->titulo }}" loading="lazy">

                <form method="POST" action="{{ route('admin.fotos.update', $foto) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="campo">
                        <label class="etiqueta">Título</label>
                        <input type="text" name="titulo" value="{{ old('titulo', $foto->titulo) }}" class="input">
                        @error('titulo') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="campo">
                        <label class="etiqueta">Descripción</label>
                        <input type="text" name="descripcion" value="{{ old('descripcion', $foto->descripcion) }}" class="input">
                        @error('descripcion') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="campo">
                        <label class="etiqueta">Orden</label>
                        <input type="number" name="orden" value="{{ old('orden', $foto->orden) }}" min="0" class="input">
                        @error('orden') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="campo">
                        <label class="etiqueta">Reemplazar imagen (opcional)</label>
                        <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" class="input">
                        @error('imagen') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <button class="btn btn-sm">Guardar cambios</button>
                </form>

                <form method="POST" action="{{ route('admin.fotos.destroy', $foto) }}" class="acciones" onsubmit="return confirm('¿Eliminar esta foto?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-ghost btn-sm">Eliminar</button>
                </form>
            </div>
        @empty
            <p style="color: var(--slate)">Todavía no hay fotos cargadas.</p>
        @endforelse
    </div>
@endsection
