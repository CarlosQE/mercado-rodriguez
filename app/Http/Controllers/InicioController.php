<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Models\DatoMercado;
use App\Models\FotoMercado;
use App\Models\Seccion;

class InicioController extends Controller
{
    public function index()
    {
        $dato = DatoMercado::first();

        $secciones = Seccion::query()
            ->withCount([
                'puestos',
                'puestos as puestos_libres_count' => fn ($q) => $q->where('estado', 'libre'),
            ])
            ->orderBy('nombre')
            ->get();

        $fotos = FotoMercado::orderBy('orden')->orderBy('id')->get();

        $comunicados = Comunicado::vigentes()->take(3)->get();

        return view('inicio', compact('dato', 'secciones', 'fotos', 'comunicados'));
    }
}
