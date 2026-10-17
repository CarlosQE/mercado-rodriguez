<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obligacion extends Model
{
    use HasFactory;

    protected $table = 'obligaciones';
    protected $fillable = ['asignacion_id', 'tarifa_id', 'periodo', 'fecha_emision', 'fecha_vencimiento', 'superficie_m2', 'monto_m2'];
    protected function casts(): array
    {
        return ['periodo' => 'date', 'fecha_emision' => 'date', 'fecha_vencimiento' => 'date', 'superficie_m2' => 'decimal:2', 'monto_m2' => 'decimal:4', 'importe' => 'decimal:2'];
    }

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class, 'asignacion_id');
    }

    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class, 'tarifa_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'obligacion_id');
    }
}
