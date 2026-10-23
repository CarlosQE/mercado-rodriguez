<?php

namespace App\Http\Requests;

class SaveFotoMercadoRequest extends MarketRequest
{
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'orden' => ['required', 'integer', 'min:0', 'max:9999'],
            'imagen' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ];
    }
}
