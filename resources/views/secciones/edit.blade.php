@extends('Layout.layout')

@section('title', 'Editar sección')
@section('header', 'Editar sección')

@section('content')
    <form method="POST" action="{{ route('secciones.update', $seccion) }}" class="max-w-md space-y-4 rounded bg-white p-6 shadow">
        @csrf
        @method('PUT')
        <div>
            <label class="block font-semibold">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $seccion->nombre) }}" class="w-full rounded border p-2">
            @error('nombre') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block font-semibold">Tarifa por m² (Bs)</label>
            <input type="number" step="0.01" name="tarifa_m2" value="{{ old('tarifa_m2', $seccion->tarifa_m2) }}" class="w-full rounded border p-2">
            @error('tarifa_m2') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <button class="rounded bg-emerald-600 px-4 py-2 text-white">Actualizar</button>
            <a href="{{ route('secciones.index') }}" class="rounded bg-gray-300 px-4 py-2">Cancelar</a>
        </div>
    </form>
@endsection