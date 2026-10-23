<div class="campo">
    <label class="etiqueta">Documento</label>
    <input type="text" name="documento" value="{{ old('documento', $comerciante->documento) }}" class="input">
    @error('documento') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Nombres</label>
    <input type="text" name="nombres" value="{{ old('nombres', $comerciante->nombres) }}" class="input">
    @error('nombres') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Apellidos</label>
    <input type="text" name="apellidos" value="{{ old('apellidos', $comerciante->apellidos) }}" class="input">
    @error('apellidos') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Rubro</label>
    <input type="text" name="rubro" list="rubros" value="{{ old('rubro', $comerciante->rubro) }}" class="input">
    <datalist id="rubros">
        <option value="Carnes">
        <option value="Verduras">
        <option value="Abarrotes">
        <option value="Comida">
    </datalist>
    @error('rubro') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Teléfono</label>
    <input type="text" name="telefono" value="{{ old('telefono', $comerciante->telefono) }}" class="input">
    @error('telefono') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Correo (opcional)</label>
    <input type="email" name="email" value="{{ old('email', $comerciante->email) }}" class="input">
    @error('email') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label class="etiqueta">Dirección</label>
    <input type="text" name="direccion" value="{{ old('direccion', $comerciante->direccion) }}" class="input">
    @error('direccion') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="campo">
    <label style="display:flex; align-items:center; gap:8px">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $comerciante->exists ? $comerciante->activo : true))>
        <span>Activo</span>
    </label>
</div>