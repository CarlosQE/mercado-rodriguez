<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SavePagoRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'comerciante_id' => ['required', 'integer', 'exists:comerciantes,id'],
            'obligaciones'   => ['required', 'array', 'min:1'],
            'obligaciones.*' => ['integer', 'distinct', Rule::exists('obligaciones', 'id')->where('estado', 'pendiente')],
            'fecha_pago'     => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'metodo'         => ['required', Rule::in(['efectivo', 'transferencia'])],
        ];
    }

    public function messages(): array
    {
        return [
            'obligaciones.required'  => 'Selecciona al menos una obligación.',
            'obligaciones.min'       => 'Selecciona al menos una obligación.',
            'obligaciones.*.exists'  => 'Una de las obligaciones ya no está pendiente.',
            'fecha_pago.before_or_equal' => 'La fecha de pago no puede ser futura.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) return;

            $ids = $this->input('obligaciones');

            $propias = DB::table('obligaciones')
                ->join('asignaciones', 'asignaciones.id', '=', 'obligaciones.asignacion_id')
                ->whereIn('obligaciones.id', $ids)
                ->where('asignaciones.comerciante_id', $this->input('comerciante_id'))
                ->count();

            if ($propias !== count($ids)) {
                $validator->errors()->add('obligaciones', 'Hay obligaciones que no pertenecen al comerciante.');
            }
        }];
    }
}