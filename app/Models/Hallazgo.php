<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hallazgo extends Model
{
    use HasFactory;

    protected $table = 'hallazgos';
    protected $fillable = ['inspeccion_id', 'descripcion', 'fecha_limite', 'subsanado_en', 'evidencia'];
    protected function casts(): array
    {
        return ['fecha_limite' => 'date', 'subsanado_en' => 'date'];
    }

    public function inspeccion(): BelongsTo
    {
        return $this->belongsTo(Inspeccion::class, 'inspeccion_id');
    }
}
