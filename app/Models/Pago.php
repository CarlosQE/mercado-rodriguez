<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $fillable = ['comerciante_id', 'comprobante', 'fecha_pago', 'metodo', 'total'];
    protected function casts(): array
    {
        return ['fecha_pago' => 'date', 'total' => 'decimal:2'];
    }

    public function comerciante(): BelongsTo
    {
        return $this->belongsTo(Comerciante::class, 'comerciante_id');
    }

    public function obligaciones(): BelongsToMany
    {
        return $this->belongsToMany(Obligacion::class, 'pago_obligacion', 'pago_id', 'obligacion_id')
            ->withPivot('monto')
            ->withTimestamps();
    }
}