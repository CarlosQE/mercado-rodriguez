@extends('Layout.layout')

@section('title', 'Nuevo comunicado')
@section('header', 'Nuevo comunicado')

@section('content')
    <form method="POST" action="{{ route('admin.comunicados.store') }}" class="card" style="max-width: 760px">
        @csrf
        @include('admin.comunicados._form')
        <div class="acciones">
            <button class="btn">Publicar</button>
            <a href="{{ route('admin.comunicados.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
@endsection
