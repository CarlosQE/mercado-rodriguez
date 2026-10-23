<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveFotoMercadoRequest;
use App\Models\FotoMercado;
use Illuminate\Support\Facades\Storage;

class FotoMercadoController extends Controller
{
    public function index()
    {
        $fotos = FotoMercado::orderBy('orden')->orderBy('id')->get();

        return view('admin.fotos.index', compact('fotos'));
    }

    public function store(SaveFotoMercadoRequest $request)
    {
        $datos = $request->validated();
        $datos['ruta'] = $request->file('imagen')->store('mercado', 'public');

        FotoMercado::create($datos);

        return redirect()->route('admin.fotos.index')->with('ok', 'Foto agregada.');
    }

    public function update(SaveFotoMercadoRequest $request, FotoMercado $foto)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            $this->borrarArchivo($foto);
            $datos['ruta'] = $request->file('imagen')->store('mercado', 'public');
        } else {
            unset($datos['imagen']);
        }

        $foto->update($datos);

        return redirect()->route('admin.fotos.index')->with('ok', 'Foto actualizada.');
    }

    public function destroy(FotoMercado $foto)
    {
        $this->borrarArchivo($foto);
        $foto->delete();

        return redirect()->route('admin.fotos.index')->with('ok', 'Foto eliminada.');
    }

    private function borrarArchivo(FotoMercado $foto): void
    {
        if (! $foto->esDeRelleno()) {
            Storage::disk('public')->delete($foto->ruta);
        }
    }
}
