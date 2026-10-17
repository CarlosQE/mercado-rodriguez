<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asignacion extends Model
{
    use HasFactory;

    protected $table = 'asignaciones';
    protected $fillable = ['puesto_id', 'comerciante_id', 'fecha_inicio', 'fecha_fin', 'motivo'];
    protected function casts(): array
    {
        return ['fecha_inicio' => 'date', 'fecha_fin' => 'date'];
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_id');
    }

    public function comerciante(): BelongsTo
    {
        return $this->belongsTo(Comerciante::class, 'comerciante_id');
    }

    public function obligaciones(): HasMany
    {
        return $this->hasMany(Obligacion::class, 'asignacion_id');
    }

    public function inspecciones(): HasMany
    {
        return $this->hasMany(Inspeccion::class, 'asignacion_id');
    }
}
