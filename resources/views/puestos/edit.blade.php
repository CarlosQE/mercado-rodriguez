@extends('Layout.layout')

@section('title', 'Editar puesto')
@section('header', 'Editar puesto')

@section('content')
    <form method="POST" action="{{ route('puestos.update', $puesto) }}" class="card">
        @csrf
        @method('PUT')
        @include('puestos._form')
        <div class="acciones">
            <button class="btn">Actualizar</button>
            <a href="{{ route('puestos.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection