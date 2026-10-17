<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspeccion extends Model
{
    use HasFactory;

    protected $table = 'inspecciones';
    protected $fillable = ['asignacion_id', 'fecha', 'inspector', 'resultado', 'proxima_fecha', 'observaciones'];
    protected function casts(): array
    {
        return ['fecha' => 'date', 'proxima_fecha' => 'date'];
    }

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class, 'asignacion_id');
    }

    public function hallazgos(): HasMany
    {
        return $this->hasMany(Hallazgo::class, 'inspeccion_id');
    }
}
