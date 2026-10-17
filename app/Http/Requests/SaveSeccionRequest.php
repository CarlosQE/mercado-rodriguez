<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SaveSeccionRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:80', Rule::unique('secciones')->ignore($this->route('seccion'))],
            'intervalo_inspeccion_dias' => ['required', 'integer', 'min:1', 'max:3650'],
        ];
    }
}
