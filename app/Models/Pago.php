<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $fillable = ['obligacion_id', 'recibo', 'fecha_pago', 'importe', 'metodo', 'referencia'];
    protected function casts(): array
    {
        return ['fecha_pago' => 'date', 'importe' => 'decimal:2', 'anulado_en' => 'datetime'];
    }

    public function obligacion(): BelongsTo
    {
        return $this->belongsTo(Obligacion::class, 'obligacion_id');
    }
}
