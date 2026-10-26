<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';
    protected $fillable = ['nombre', 'prefijo', 'tarifa_m2'];
    protected function casts(): array
    {
        return ['tarifa_m2' => 'decimal:2'];
    }

    public function puestos(): HasMany
    {
        return $this->hasMany(Puesto::class, 'seccion_id');
    }
}