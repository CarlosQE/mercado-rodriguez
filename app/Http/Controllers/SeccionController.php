<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveSeccionRequest;
use App\Models\Seccion;
use Illuminate\Http\Request;

class SeccionController extends Controller
{
    public function index(Request $request)
    {
        $secciones = Seccion::withCount('puestos')
            ->when($request->filled('buscar'), fn ($q) => $q->where('nombre', 'ilike', '%'.$request->buscar.'%'))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('secciones.index', compact('secciones'));
    }

    public function create()
    {
        return view('secciones.create');
    }

    public function store(SaveSeccionRequest $request)
    {
        Seccion::create($request->validated());

        return redirect()->route('secciones.index')->with('ok', 'Sección creada.');
    }

    public function edit(Seccion $seccion)
    {
        return view('secciones.edit', compact('seccion'));
    }

    public function update(SaveSeccionRequest $request, Seccion $seccion)
    {
        $seccion->update($request->validated());

        return redirect()->route('secciones.index')->with('ok', 'Sección actualizada.');
    }

    public function destroy(Seccion $seccion)
    {
        if ($seccion->puestos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la sección tiene puestos.');
        }

        $seccion->delete();

        return redirect()->route('secciones.index')->with('ok', 'Sección eliminada.');
    }
}