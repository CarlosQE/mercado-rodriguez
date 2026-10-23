<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SaveComercianteRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'documento' => ['required', 'string', 'max:30', Rule::unique('comerciantes', 'documento')->ignore($this->route('comerciante'))],
            'nombres'   => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'rubro'     => ['required', 'string', 'max:80'],
            'telefono'  => ['required', 'string', 'max:25', 'regex:/^[+0-9 ()-]{6,25}$/'],
            'email'     => ['nullable', 'email', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'activo'    => ['sometimes', 'boolean'],
        ];
    }
}