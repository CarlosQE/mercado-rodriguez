<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SaveComercianteRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'tipo_documento' => ['required', Rule::in(['CI', 'PASAPORTE', 'NIT'])],
            'documento' => ['required', 'string', 'max:30', Rule::unique('comerciantes')->where('tipo_documento', $this->input('tipo_documento'))->ignore($this->route('comerciante'))],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:25', 'regex:/^[+0-9 ()-]{6,25}$/'],
            'email' => ['nullable', 'email', 'max:254'],
            'direccion' => ['required', 'string', 'max:255'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}
