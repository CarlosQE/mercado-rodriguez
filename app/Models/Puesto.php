<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Puesto extends Model
{
    use HasFactory;

    protected $table = 'puestos';
    protected $fillable = ['seccion_id', 'codigo', 'superficie_m2'];
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

    public function clausuras(): HasMany
    {
        return $this->hasMany(Clausura::class, 'puesto_id');
    }

    public function estadoEn(string $fecha): string
    {
        if ($this->clausuras()->where('fecha_inicio', '<=', $fecha)->where(fn ($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>', $fecha))->exists()) {
            return 'clausurado';
        }
        return $this->asignaciones()->where('fecha_inicio', '<=', $fecha)->where(fn ($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>', $fecha))->exists() ? 'ocupado' : 'libre';
    }

    public function ultimaInspeccion(): ?Inspeccion
    {
        return Inspeccion::whereHas('asignacion', fn ($q) => $q->where('puesto_id', $this->id))
            ->orderByDesc('fecha')->orderByDesc('id')->first();
    }
}
