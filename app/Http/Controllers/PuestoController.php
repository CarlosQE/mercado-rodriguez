<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavePuestoRequest;
use App\Models\Puesto;
use App\Models\Seccion;
use Illuminate\Http\Request;

class PuestoController extends Controller
{
    public function index(Request $request)
    {
        $puestos = Puesto::with('seccion')
            ->when($request->buscar, fn ($q, $b) => $q->where('codigo', 'ILIKE', "%{$b}%"))
            ->when($request->seccion_id, fn ($q, $s) => $q->where('seccion_id', $s))
            ->when($request->estado, fn ($q, $e) => $q->where('estado', $e))
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        $secciones = Seccion::orderBy('nombre')->get(['id', 'nombre']);

        return view('puestos.index', compact('puestos', 'secciones'));
    }

    public function create()
    {
        $puesto = new Puesto();
        $secciones = Seccion::orderBy('nombre')->get(['id', 'nombre']);

        return view('puestos.create', compact('puesto', 'secciones'));
    }

    public function store(SavePuestoRequest $request)
    {
        $puesto = new Puesto($request->validated());
        $puesto->estado = 'libre';
        $puesto->save();

        return redirect()->route('puestos.index')->with('ok', 'Puesto creado.');
    }

    public function edit(Puesto $puesto)
    {
        $secciones = Seccion::orderBy('nombre')->get(['id', 'nombre']);

        return view('puestos.edit', compact('puesto', 'secciones'));
    }

    public function update(SavePuestoRequest $request, Puesto $puesto)
    {
        $datos = $request->validated();

        // Un puesto ocupado cambia de estado por asignaciones, no a mano.
        if ($puesto->estado !== 'ocupado') {
            $datos['estado'] = $request->validate([
                'estado' => 'required|in:libre,clausurado',
            ])['estado'];
        }

        $puesto->fill($datos)->save();

        return redirect()->route('puestos.index')->with('ok', 'Puesto actualizado.');
    }

    public function destroy(Puesto $puesto)
    {
        if ($puesto->asignaciones()->exists()) {
            return back()->with('error', 'No se puede eliminar: el puesto tiene asignaciones.');
        }

        $puesto->delete();

        return redirect()->route('puestos.index')->with('ok', 'Puesto eliminado.');
    }
}