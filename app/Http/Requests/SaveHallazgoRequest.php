<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SaveHallazgoRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'inspeccion_id' => ['required', 'integer', 'exists:inspecciones,id'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'fecha_limite' => ['required', 'date_format:Y-m-d'],
            'subsanado_en' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'evidencia' => ['nullable', 'required_with:subsanado_en', 'string', 'max:5000'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) return;
            $i = DB::table('inspecciones')->find($this->integer('inspeccion_id'));
            if ($i && ($this->input('fecha_limite') < $i->fecha || ($this->filled('subsanado_en') && $this->input('subsanado_en') < $i->fecha))) $validator->errors()->add('fecha_limite', 'Las fechas no pueden preceder a la inspección.');
        }];
    }
}
