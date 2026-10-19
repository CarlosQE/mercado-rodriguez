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
            'tarifa_m2' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],        ];
    }
}
