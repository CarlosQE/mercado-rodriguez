<?php

use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\ComercianteController;
use App\Http\Controllers\ObligacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\SeccionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/secciones');

Route::resource('secciones', SeccionController::class)
    ->except('show')
    ->parameters(['secciones' => 'seccion']);
Route::resource('puestos', PuestoController::class)->except('show');
Route::resource('comerciantes', ComercianteController::class)->except('show');

Route::resource('asignaciones', AsignacionController::class)
    ->only(['index', 'create', 'store'])
    ->parameters(['asignaciones' => 'asignacion']);
Route::patch('asignaciones/{asignacion}/cerrar', [AsignacionController::class, 'cerrar'])->name('asignaciones.cerrar');

Route::get('obligaciones', [ObligacionController::class, 'index'])->name('obligaciones.index');
Route::post('obligaciones/emitir', [ObligacionController::class, 'emitir'])->name('obligaciones.emitir');

Route::resource('pagos', PagoController::class)->only(['index', 'create', 'store', 'show']);