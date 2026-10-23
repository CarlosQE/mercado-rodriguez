<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveComercianteRequest;
use App\Models\Comerciante;
use Illuminate\Http\Request;

class ComercianteController extends Controller
{
    public function index(Request $request)
    {
        $comerciantes = Comerciante::query()
            ->when($request->buscar, function ($q, $b) {
                $q->where(function ($q) use ($b) {
                    $q->where('documento', 'ILIKE', "%{$b}%")
                      ->orWhere('nombres', 'ILIKE', "%{$b}%")
                      ->orWhere('apellidos', 'ILIKE', "%{$b}%");
                });
            })
            ->when($request->estado === 'activo', fn ($q) => $q->where('activo', true))
            ->when($request->estado === 'inactivo', fn ($q) => $q->where('activo', false))
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        return view('comerciantes.index', compact('comerciantes'));
    }

    public function create()
    {
        return view('comerciantes.create', ['comerciante' => new Comerciante()]);
    }

    public function store(SaveComercianteRequest $request)
    {
        $datos = $request->validated();
        $datos['activo'] = $request->boolean('activo');

        Comerciante::create($datos);

        return redirect()->route('comerciantes.index')->with('ok', 'Comerciante registrado.');
    }

    public function edit(Comerciante $comerciante)
    {
        return view('comerciantes.edit', compact('comerciante'));
    }

    public function update(SaveComercianteRequest $request, Comerciante $comerciante)
    {
        $datos = $request->validated();
        $datos['activo'] = $request->boolean('activo');

        $comerciante->update($datos);

        return redirect()->route('comerciantes.index')->with('ok', 'Comerciante actualizado.');
    }

    public function destroy(Comerciante $comerciante)
    {
        if ($comerciante->asignaciones()->exists() || $comerciante->pagos()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene asignaciones o pagos. Desactívalo en su lugar.');
        }

        $comerciante->delete();

        return redirect()->route('comerciantes.index')->with('ok', 'Comerciante eliminado.');
    }
}