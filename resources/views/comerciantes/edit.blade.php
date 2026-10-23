@extends('Layout.layout')

@section('title', 'Editar comerciante')
@section('header', 'Editar comerciante')

@section('content')
    <form method="POST" action="{{ route('comerciantes.update', $comerciante) }}" class="card">
        @csrf
        @method('PUT')
        @include('comerciantes._form')
        <div class="acciones">
            <button class="btn">Actualizar</button>
            <a href="{{ route('comerciantes.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection