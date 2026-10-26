<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaveSeccionRequest extends MarketRequest
{
    protected function prepareForValidation(): void
    {
        $prefijo = Str::upper(Str::ascii((string) $this->input('prefijo')));
        $prefijo = preg_replace('/\s+/u', '', $prefijo);

        if ($seccion = $this->route('seccion')) {
            $prefijo = $seccion->prefijo;
        }

        $this->merge(['prefijo' => $prefijo]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:80', Rule::unique('secciones')->ignore($this->route('seccion'))],
            'prefijo' => ['required', 'size:3', 'regex:/^[A-Z]{3}$/', Rule::unique('secciones', 'prefijo')->ignore($this->route('seccion'))],
            'tarifa_m2' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'prefijo.required' => 'El prefijo es obligatorio.',
            'prefijo.size' => 'El prefijo debe tener exactamente 3 letras.',
            'prefijo.regex' => 'El prefijo solo puede contener 3 letras.',
            'prefijo.unique' => 'Ese prefijo ya está en uso.',
        ];
    }
}
