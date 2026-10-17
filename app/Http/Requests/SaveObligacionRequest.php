<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SaveObligacionRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'asignacion_id' => ['required', 'integer', 'exists:asignaciones,id'],
            'tarifa_id' => ['required', 'integer', 'exists:tarifas,id'],
            'periodo' => ['required', 'date_format:Y-m-d', 'regex:/^\d{4}-\d{2}-01$/', Rule::unique('obligaciones')->where('asignacion_id', $this->input('asignacion_id'))],
            'fecha_emision' => ['required', 'date_format:Y-m-d'],
            'fecha_vencimiento' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecha_emision'],
            'superficie_m2' => ['prohibited'],
            'monto_m2' => ['prohibited'],
            'importe' => ['prohibited'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) return;
            $a = DB::table('asignaciones')->find($this->integer('asignacion_id'));
            $t = DB::table('tarifas')->find($this->integer('tarifa_id'));
            $puesto = $a ? DB::table('puestos')->find($a->puesto_id) : null;
            $fecha = $this->input('periodo');
            if ($a && ($fecha < $a->fecha_inicio || ($a->fecha_fin && $fecha >= $a->fecha_fin))) $validator->errors()->add('periodo', 'La asignación debe estar vigente al inicio del mes.');
            if ($t && $puesto && ($t->seccion_id !== $puesto->seccion_id || $fecha < $t->vigente_desde || ($t->vigente_hasta && $fecha >= $t->vigente_hasta))) $validator->errors()->add('tarifa_id', 'La tarifa debe corresponder a la sección y estar vigente en el período.');
        }];
    }
}
