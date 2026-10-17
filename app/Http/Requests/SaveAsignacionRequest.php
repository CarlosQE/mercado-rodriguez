<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SaveAsignacionRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'puesto_id' => ['required', 'integer', 'exists:puestos,id'],
            'comerciante_id' => ['required', 'integer', Rule::exists('comerciantes', 'id')->where('activo', true)],
            'fecha_inicio' => ['required', 'date_format:Y-m-d'],
            'fecha_fin' => ['nullable', 'date_format:Y-m-d', 'after:fecha_inicio'],
            'motivo' => ['required', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) return;
            $query = DB::table('asignaciones')->where('puesto_id', $this->input('puesto_id'))
                ->where(fn ($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>', $this->input('fecha_inicio')));
            if ($this->filled('fecha_fin')) $query->where('fecha_inicio', '<', $this->input('fecha_fin'));
            $model = $this->route('asignacion');
            if ($model instanceof \Illuminate\Database\Eloquent\Model) $query->where('id', '<>', $model->getKey());
            if ($query->exists()) $validator->errors()->add('fecha_inicio', 'El intervalo se superpone con otro registro.');
        }];
    }
}
