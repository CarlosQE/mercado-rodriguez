<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SaveInspeccionRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'asignacion_id' => ['required', 'integer', 'exists:asignaciones,id'],
            'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'inspector' => ['required', 'string', 'max:150'],
            'resultado' => ['required', Rule::in(['aprobada', 'con_observaciones', 'no_aprobada'])],
            'proxima_fecha' => ['required', 'date_format:Y-m-d', 'after:fecha'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) return;
            $a = DB::table('asignaciones')->find($this->integer('asignacion_id'));
            if ($a && ($this->input('fecha') < $a->fecha_inicio || ($a->fecha_fin && $this->input('fecha') >= $a->fecha_fin))) $validator->errors()->add('fecha', 'La inspección debe corresponder al titular de esa fecha.');
        }];
    }
}
