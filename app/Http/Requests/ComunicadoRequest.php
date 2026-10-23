<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ComunicadoRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:120'],
            'contenido' => ['required', 'string'],
            'tipo' => ['required', Rule::in(['general', 'urgente', 'evento', 'cobranza'])],
            'fecha_publicacion' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_publicacion'],
            'fijado' => ['sometimes', 'boolean'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.string' => 'El título debe ser texto.',
            'titulo.max' => 'El título no puede superar los 120 caracteres.',
            'contenido.required' => 'El contenido es obligatorio.',
            'contenido.string' => 'El contenido debe ser texto.',
            'tipo.required' => 'Selecciona un tipo de comunicado.',
            'tipo.in' => 'El tipo de comunicado no es válido.',
            'fecha_publicacion.required' => 'La fecha de publicación es obligatoria.',
            'fecha_publicacion.date' => 'La fecha de publicación no es válida.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento no es válida.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de publicación.',
            'fijado.boolean' => 'El valor de fijado no es válido.',
            'activo.boolean' => 'El valor de activo no es válido.',
        ];
    }
}
