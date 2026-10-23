<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveDatoMercadoRequest;
use App\Models\DatoMercado;

class DatoMercadoController extends Controller
{
    public function edit()
    {
        $dato = DatoMercado::first() ?? new DatoMercado();

        return view('admin.mercado.edit', compact('dato'));
    }

    public function update(SaveDatoMercadoRequest $request)
    {
        $dato = DatoMercado::first();

        if ($dato) {
            $dato->update($request->validated());
        } else {
            DatoMercado::create($request->validated());
        }

        return redirect()->route('admin.mercado.edit')->with('ok', 'Ficha del mercado actualizada.');
    }
}
