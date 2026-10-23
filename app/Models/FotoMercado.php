<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoMercado extends Model
{
    use HasFactory;

    protected $table = 'fotos_mercado';

    protected $fillable = ['titulo', 'descripcion', 'ruta', 'orden'];

    protected function casts(): array
    {
        return ['orden' => 'integer'];
    }

    protected function url(): Attribute
    {
        return Attribute::make(get: function (): string {
            $ruta = ltrim($this->ruta, '/');

            return str_starts_with($ruta, 'img/')
                ? asset($ruta)
                : asset('storage/' . $ruta);
        });
    }

    public function esDeRelleno(): bool
    {
        return str_starts_with(ltrim($this->ruta, '/'), 'img/');
    }
}
