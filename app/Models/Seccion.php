<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';
    protected $fillable = ['nombre', 'intervalo_inspeccion_dias'];
    protected function casts(): array
    {
        return [];
    }

    public function puestos(): HasMany
    {
        return $this->hasMany(Puesto::class, 'seccion_id');
    }

    public function tarifas(): HasMany
    {
        return $this->hasMany(Tarifa::class, 'seccion_id');
    }
}
