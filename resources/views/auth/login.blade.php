@extends('Layout.layout')

@section('title', 'Iniciar sesión')
@section('header', 'Iniciar sesión')
@section('subtitle', 'Accede con tu cuenta para consultar el mercado y registrar pagos.')

@section('content')
    <form method="POST" action="{{ route('login.store') }}" class="card">
        @csrf

        <div class="campo">
            <label class="etiqueta" for="email">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="input" autocomplete="email" autofocus>
            @error('email') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="campo">
            <label class="etiqueta" for="password">Contraseña</label>
            <input id="password" type="password" name="password" class="input" autocomplete="current-password">
            @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="acciones">
            <button class="btn">Entrar</button>
            <a href="{{ url('/') }}" class="btn btn-ghost">Volver al mercado</a>
        </div>
    </form>
@endsection
