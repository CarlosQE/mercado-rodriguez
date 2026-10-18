<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Obligacion extends Model
{
    use HasFactory;

    protected $table = 'obligaciones';
    protected $fillable = ['asignacion_id', 'periodo', 'fecha_vencimiento', 'superficie_m2', 'tarifa_m2', 'importe', 'estado'];
    protected function casts(): array
    {
        return ['periodo' => 'date', 'fecha_vencimiento' => 'date', 'superficie_m2' => 'decimal:2', 'tarifa_m2' => 'decimal:2', 'importe' => 'decimal:2'];
    }

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class, 'asignacion_id');
    }

    public function pagos(): BelongsToMany
    {
        return $this->belongsToMany(Pago::class, 'pago_obligacion', 'obligacion_id', 'pago_id')
            ->withPivot('monto')
            ->withTimestamps();
    }
}