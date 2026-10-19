@extends('Layout.layout')

@section('title', 'Secciones')
@section('header', 'Secciones del mercado')

@section('content')
    @if (session('ok'))
        <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('ok') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 p-3 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-4 flex items-center justify-between gap-2">
        <form method="GET" class="flex gap-2">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar sección" class="rounded border p-2">
            <button class="rounded bg-teal-700 px-4 py-2 text-white">Buscar</button>
        </form>
        <a href="{{ route('secciones.create') }}" class="rounded bg-emerald-600 px-4 py-2 text-white">Nueva sección</a>
    </div>

    <table class="w-full overflow-hidden rounded bg-white shadow">
        <thead class="bg-teal-700 text-left text-white">
            <tr>
                <th class="p-3">Nombre</th>
                <th class="p-3">Tarifa por m²</th>
                <th class="p-3">Puestos</th>
                <th class="p-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($secciones as $seccion)
                <tr class="border-t">
                    <td class="p-3">{{ $seccion->nombre }}</td>
                    <td class="p-3">Bs {{ number_format($seccion->tarifa_m2, 2) }}</td>
                    <td class="p-3">{{ $seccion->puestos_count }}</td>
                    <td class="flex gap-2 p-3">
                        <a href="{{ route('secciones.edit', $seccion) }}" class="rounded bg-amber-500 px-3 py-1 text-white">Editar</a>
                        <form method="POST" action="{{ route('secciones.destroy', $seccion) }}" onsubmit="return confirm('¿Eliminar la sección?')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded bg-red-600 px-3 py-1 text-white">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-3 text-center">No hay secciones.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $secciones->links() }}</div>
@endsection