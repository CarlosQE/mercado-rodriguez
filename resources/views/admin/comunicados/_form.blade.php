<div class="campo">
    <label class="etiqueta">Título</label>
    <input type="text" name="titulo" maxlength="120" value="{{ old('titulo', $comunicado->titulo) }}" class="input">
    @error('titulo') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Contenido</label>
    <textarea name="contenido" rows="6" class="input">{{ old('contenido', $comunicado->contenido) }}</textarea>
    @error('contenido') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Tipo</label>
    <select name="tipo" class="input">
        @foreach (['general' => 'General', 'urgente' => 'Urgente', 'evento' => 'Evento', 'cobranza' => 'Cobranza'] as $valor => $etiqueta)
            <option value="{{ $valor }}" @selected(old('tipo', $comunicado->tipo ?: 'general') === $valor)>{{ $etiqueta }}</option>
        @endforeach
    </select>
    @error('tipo') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Fecha de publicación</label>
    <input type="date" name="fecha_publicacion" value="{{ old('fecha_publicacion', $comunicado->fecha_publicacion?->format('Y-m-d') ?? today()->toDateString()) }}" class="input">
    @error('fecha_publicacion') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Fecha de vencimiento (opcional)</label>
    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $comunicado->fecha_vencimiento?->format('Y-m-d')) }}" class="input">
    @error('fecha_vencimiento') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label style="display:flex; align-items:center; gap:8px">
        <input type="checkbox" name="fijado" value="1" @checked(old('fijado', $comunicado->exists ? $comunicado->fijado : false))>
        <span>Fijar este aviso</span>
    </label>
</div>

<div class="campo">
    <label style="display:flex; align-items:center; gap:8px">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $comunicado->exists ? $comunicado->activo : true))>
        <span>Activo</span>
    </label>
</div>
