<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class SavePagoRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'obligacion_id' => ['required', 'integer', 'exists:obligaciones,id'],
            'recibo' => ['required', 'string', 'max:50', 'unique:pagos,recibo'],
            'fecha_pago' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'importe' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'max:9999999999999999.99'],
            'metodo' => ['required', Rule::in(['efectivo', 'transferencia'])],
            'referencia' => ['nullable', 'required_if:metodo,transferencia', 'string', 'max:100'],
            'anulado_en' => ['prohibited'],
            'motivo_anulacion' => ['prohibited'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) return;
            $o = DB::table('saldos_obligaciones')->where('obligacion_id', $this->integer('obligacion_id'))->first();
            if ($o && $this->input('importe') > $o->saldo) $validator->errors()->add('importe', 'El pago supera el saldo pendiente.');
        }];
    }
}
