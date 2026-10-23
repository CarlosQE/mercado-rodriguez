<?php

namespace App\Http\Requests;

class SaveDatoMercadoRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'eslogan' => ['nullable', 'string', 'max:180'],
            'historia' => ['required', 'string'],
            'direccion' => ['required', 'string', 'max:255'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'horario_atencion' => ['required', 'string', 'max:180'],
            'telefono' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
