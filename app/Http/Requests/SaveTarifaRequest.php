<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SaveTarifaRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'seccion_id' => ['required', 'integer', 'exists:secciones,id'],
            'monto_m2' => ['required', 'numeric', 'decimal:0,4', 'gt:0', 'max:99999999.9999'],
            'vigente_desde' => ['required', 'date_format:Y-m-d'],
            'vigente_hasta' => ['nullable', 'date_format:Y-m-d', 'after:vigente_desde'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) return;
            $query = DB::table('tarifas')->where('seccion_id', $this->input('seccion_id'))
                ->where(fn ($q) => $q->whereNull('vigente_hasta')->orWhere('vigente_hasta', '>', $this->input('vigente_desde')));
            if ($this->filled('vigente_hasta')) $query->where('vigente_desde', '<', $this->input('vigente_hasta'));
            $model = $this->route('tarifa');
            if ($model instanceof \Illuminate\Database\Eloquent\Model) $query->where('id', '<>', $model->getKey());
            if ($query->exists()) $validator->errors()->add('vigente_desde', 'El intervalo se superpone con otro registro.');
        }];
    }
}
