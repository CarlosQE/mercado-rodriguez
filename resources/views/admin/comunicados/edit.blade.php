@extends('Layout.layout')

@section('title', 'Editar comunicado')
@section('header', 'Editar comunicado')

@section('content')
    <form method="POST" action="{{ route('admin.comunicados.update', $comunicado) }}" class="card" style="max-width: 760px">
        @csrf
        @method('PUT')
        @include('admin.comunicados._form')
        <div class="acciones">
            <button class="btn">Actualizar</button>
            <a href="{{ route('admin.comunicados.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection
