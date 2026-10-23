@extends('Layout.layout')

@section('title', 'Nuevo comerciante')
@section('header', 'Nuevo comerciante')

@section('content')
    <form method="POST" action="{{ route('comerciantes.store') }}" class="card">
        @csrf
        @include('comerciantes._form')
        <div class="acciones">
            <button class="btn">Guardar</button>
            <a href="{{ route('comerciantes.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection