<?php

use App\Http\Controllers\SeccionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/secciones');
Route::resource('secciones', SeccionController::class)
    ->except('show')
    ->parameters(['secciones' => 'seccion']);