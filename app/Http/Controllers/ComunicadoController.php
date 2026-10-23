<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComunicadoRequest;
use App\Models\Comunicado;

class ComunicadoController extends Controller
{
    public function tablero()
    {
        $comunicados = Comunicado::vigentes()->get();

        return view('tablero', compact('comunicados'));
    }

    public function index()
    {
        $comunicados = Comunicado::orderByDesc('fijado')
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.comunicados.index', compact('comunicados'));
    }

    public function create()
    {
        return view('admin.comunicados.create', ['comunicado' => new Comunicado()]);
    }

    public function store(ComunicadoRequest $request)
    {
        $datos = $request->validated();
        $datos['user_id'] = $request->user()->id;
        $datos['fijado'] = $request->boolean('fijado');
        $datos['activo'] = $request->boolean('activo');

        Comunicado::create($datos);

        return redirect()->route('admin.comunicados.index')->with('ok', 'Comunicado publicado.');
    }

    public function edit(Comunicado $comunicado)
    {
        return view('admin.comunicados.edit', compact('comunicado'));
    }

    public function update(ComunicadoRequest $request, Comunicado $comunicado)
    {
        $datos = $request->validated();
        $datos['fijado'] = $request->boolean('fijado');
        $datos['activo'] = $request->boolean('activo');

        $comunicado->update($datos);

        return redirect()->route('admin.comunicados.index')->with('ok', 'Comunicado actualizado.');
    }

    public function destroy(Comunicado $comunicado)
    {
        $comunicado->delete();

        return redirect()->route('admin.comunicados.index')->with('ok', 'Comunicado eliminado.');
    }
}
