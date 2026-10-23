<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAsignacionRequest;
use App\Models\Asignacion;
use App\Models\Comerciante;
use App\Models\Puesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AsignacionController extends Controller
{
    public function index(Request $request)
    {
        $asignaciones = Asignacion::with(['puesto.seccion', 'comerciante'])
            ->when($request->buscar, function ($q, $b) {
                $q->where(function ($q) use ($b) {
                    $q->whereHas('puesto', fn ($p) => $p->where('codigo', 'ILIKE', "%{$b}%"))
                      ->orWhereHas('comerciante', function ($c) use ($b) {
                          $c->where('nombres', 'ILIKE', "%{$b}%")
                            ->orWhere('apellidos', 'ILIKE', "%{$b}%")
                            ->orWhere('documento', 'ILIKE', "%{$b}%");
                      });
                });
            })
            ->when($request->estado === 'vigente', fn ($q) => $q->whereNull('fecha_fin'))
            ->when($request->estado === 'cerrada', fn ($q) => $q->whereNotNull('fecha_fin'))
            ->orderByDesc('fecha_inicio')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('asignaciones.index', compact('asignaciones'));
    }

    public function create()
    {
        $puestos = Puesto::with('seccion')->where('estado', 'libre')->orderBy('codigo')->get();
        $comerciantes = Comerciante::where('activo', true)->orderBy('apellidos')->orderBy('nombres')->get();

        return view('asignaciones.create', compact('puestos', 'comerciantes'));
    }

    public function store(SaveAsignacionRequest $request)
    {
        $datos = $request->safe()->except('fecha_fin');

        DB::transaction(function () use ($datos) {
            $puesto = Puesto::lockForUpdate()->findOrFail($datos['puesto_id']);

            if ($puesto->estado !== 'libre') {
                throw ValidationException::withMessages(['puesto_id' => 'El puesto no está libre.']);
            }

            Asignacion::create($datos);
            $puesto->update(['estado' => 'ocupado']);
        });

        return redirect()->route('asignaciones.index')->with('ok', 'Asignación registrada.');
    }

    public function cerrar(Request $request, Asignacion $asignacion)
    {
        if ($asignacion->fecha_fin !== null) {
            return back()->with('error', 'La asignación ya está cerrada.');
        }

        $datos = $request->validate([
            'fecha_fin' => ['required', 'date', 'after_or_equal:' . $asignacion->fecha_inicio->toDateString(), 'before_or_equal:today'],
        ]);

        DB::transaction(function () use ($asignacion, $datos) {
            $asignacion->update($datos);
            $asignacion->puesto()->update(['estado' => 'libre']);
        });

        return back()->with('ok', 'Asignación cerrada; el puesto quedó libre.');
    }
}