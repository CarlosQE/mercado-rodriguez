<?php
namespace App\Http\Requests;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SavePuestoRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'seccion_id' => ['required', 'integer', 'exists:secciones,id'],
            'superficie_m2' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'max:999999.99'],
        ];
    }
}
