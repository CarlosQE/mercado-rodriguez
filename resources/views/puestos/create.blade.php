@extends('Layout.layout')

@section('title', 'Nuevo puesto')
@section('header', 'Nuevo puesto')

@section('content')
    <form method="POST" action="{{ route('puestos.store') }}" class="card">
        @csrf
        @include('puestos._form')
        <div class="acciones">
            <button class="btn">Guardar</button>
            <a href="{{ route('puestos.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection