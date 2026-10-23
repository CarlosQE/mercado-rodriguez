<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Puesto extends Model
{
    use HasFactory;

    protected $table = 'puestos';
    protected $fillable = ['seccion_id', 'codigo', 'superficie_m2', 'estado'];
    protected function casts(): array
    {
        return ['superficie_m2' => 'decimal:2'];
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'puesto_id');
    }

    public function asignacionVigente(): HasOne
    {
        return $this->hasOne(Asignacion::class, 'puesto_id')->whereNull('fecha_fin');
    }
}