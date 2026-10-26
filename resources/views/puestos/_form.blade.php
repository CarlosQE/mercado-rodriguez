<div class="campo">
    <label class="etiqueta">Sección</label>
    <select name="seccion_id" class="input">
        <option value="">Selecciona una sección</option>
        @foreach ($secciones as $s)
            <option value="{{ $s->id }}" @selected(old('seccion_id', $puesto->seccion_id) == $s->id)>{{ $s->nombre }}</option>
        @endforeach
    </select>
    @error('seccion_id') <p class="error">{{ $message }}</p> @enderror
</div>

@if ($puesto->exists)
    <div class="campo">
        <label class="etiqueta">Código</label>
        <p>{{ $puesto->codigo }}</p>
    </div>
@else
    <div class="campo">
        <p class="etiqueta">El código se asigna automáticamente al guardar.</p>
    </div>
@endif

<div class="campo">
    <label class="etiqueta">Superficie (m²)</label>
    <input type="number" step="0.01" name="superficie_m2" value="{{ old('superficie_m2', $puesto->superficie_m2) }}" class="input">
    @error('superficie_m2') <p class="error">{{ $message }}</p> @enderror
</div>

@if ($puesto->exists)
    <div class="campo">
        <label class="etiqueta">Estado</label>
        @if ($puesto->estado === 'ocupado')
            <p>Ocupado (cambia por asignaciones)</p>
        @else
            <select name="estado" class="input">
                @foreach (['libre', 'clausurado'] as $e)
                    <option value="{{ $e }}" @selected(old('estado', $puesto->estado) === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
            @error('estado') <p class="error">{{ $message }}</p> @enderror
        @endif
    </div>
@endif