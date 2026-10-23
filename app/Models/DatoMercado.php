<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatoMercado extends Model
{
    use HasFactory;

    protected $table = 'datos_mercado';

    protected $fillable = [
        'nombre',
        'eslogan',
        'historia',
        'direccion',
        'referencia',
        'latitud',
        'longitud',
        'horario_atencion',
        'telefono',
        'email',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }
}
