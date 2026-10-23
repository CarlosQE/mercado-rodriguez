<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use Illuminate\Http\Request;

class MapaController extends Controller
{
    public function index(Request $request)
    {
        $secciones = Seccion::query()
            ->with(['puestos' => fn ($q) => $q->with('asignacionVigente.comerciante')->orderBy('codigo')])
            ->withCount([
                'puestos',
                'puestos as libres_count' => fn ($q) => $q->where('estado', 'libre'),
                'puestos as ocupados_count' => fn ($q) => $q->where('estado', 'ocupado'),
                'puestos as clausurados_count' => fn ($q) => $q->where('estado', 'clausurado'),
            ])
            ->when($request->filled('seccion_id'), fn ($q) => $q->where('id', $request->seccion_id))
            ->orderBy('nombre')
            ->get();

        $todasLasSecciones = Seccion::orderBy('nombre')->get(['id', 'nombre']);

        return view('mapa.index', compact('secciones', 'todasLasSecciones'));
    }
}
