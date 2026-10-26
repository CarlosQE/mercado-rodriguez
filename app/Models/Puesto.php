<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

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

    public function generarCodigo(Seccion $seccion): string
    {
        $prefijo = Str::upper(Str::substr(Str::ascii($seccion->nombre), 0, 3));

        $mayor = static::query()
            ->where('codigo', 'like', $prefijo.'-%')
            ->pluck('codigo')
            ->map(fn (string $codigo) => preg_match('/^'.preg_quote($prefijo, '/').'-(\d+)$/', $codigo, $coincidencias) ? (int) $coincidencias[1] : 0)
            ->max() ?? 0;

        return $prefijo.'-'.str_pad((string) ($mayor + 1), 3, '0', STR_PAD_LEFT);
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