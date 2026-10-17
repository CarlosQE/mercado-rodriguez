<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarifa extends Model
{
    use HasFactory;

    protected $table = 'tarifas';
    protected $fillable = ['seccion_id', 'monto_m2', 'vigente_desde', 'vigente_hasta'];
    protected function casts(): array
    {
        return ['monto_m2' => 'decimal:4', 'vigente_desde' => 'date', 'vigente_hasta' => 'date'];
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function obligaciones(): HasMany
    {
        return $this->hasMany(Obligacion::class, 'tarifa_id');
    }
}
