@extends('Layout.layout')

@section('title', 'Acceso denegado')
@section('header', 'Acceso denegado')
@section('subtitle', 'Tu cuenta no tiene permiso para abrir esta página.')

@section('content')
    <div class="card">
        <p style="margin-bottom: 24px">Si crees que se trata de un error, consulta con la administración del mercado.</p>
        <div class="acciones">
            <a href="{{ url('/') }}" class="btn">Ir al mercado</a>
            <a href="{{ route('puestos.index') }}" class="btn btn-ghost">Ver puestos</a>
        </div>
    </div>
@endsection
