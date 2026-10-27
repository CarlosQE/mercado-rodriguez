<?php

use App\Http\Middleware\VerificarRol;
use App\Models\User;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['rol' => VerificarRol::class]);

        RedirectIfAuthenticated::redirectUsing(
            fn ($request) => $request->user()?->tieneRol(User::ROL_ADMIN)
                ? route('dashboard')
                : route('mapa.index')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->create();
