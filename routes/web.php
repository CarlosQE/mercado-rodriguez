<?php

use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\ComercianteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ObligacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\SeccionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/secciones');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'rol:admin,cobrador'])->group(function () {
    Route::resource('secciones', SeccionController::class)
        ->only(['index'])
        ->parameters(['secciones' => 'seccion']);
    Route::resource('puestos', PuestoController::class)->only(['index'])->whereNumber('puesto');
    Route::resource('comerciantes', ComercianteController::class)->only(['index']);
    Route::resource('asignaciones', AsignacionController::class)
        ->only(['index'])
        ->parameters(['asignaciones' => 'asignacion']);
    Route::get('obligaciones', [ObligacionController::class, 'index'])->name('obligaciones.index');
    Route::resource('pagos', PagoController::class)->only(['index', 'show'])->whereNumber('pago');
});

Route::middleware(['auth', 'rol:admin,cobrador'])->group(function () {
    Route::resource('pagos', PagoController::class)->only(['create', 'store']);
});

Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::resource('secciones', SeccionController::class)
        ->except(['index', 'show'])
        ->parameters(['secciones' => 'seccion']);
    Route::resource('puestos', PuestoController::class)->except(['index', 'show']);
    Route::resource('comerciantes', ComercianteController::class)->except(['index', 'show']);
    Route::resource('asignaciones', AsignacionController::class)
        ->only(['create', 'store'])
        ->parameters(['asignaciones' => 'asignacion']);
    Route::patch('asignaciones/{asignacion}/cerrar', [AsignacionController::class, 'cerrar'])->name('asignaciones.cerrar');
    Route::post('obligaciones/emitir', [ObligacionController::class, 'emitir'])->name('obligaciones.emitir');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
