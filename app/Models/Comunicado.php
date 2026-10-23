<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comunicado extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titulo',
        'contenido',
        'tipo',
        'fecha_publicacion',
        'fecha_vencimiento',
        'fijado',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_publicacion' => 'date',
            'fecha_vencimiento' => 'date',
            'fijado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVigentes(Builder $query): Builder
    {
        return $query
            ->where('activo', true)
            ->where(function (Builder $query) {
                $query->whereNull('fecha_vencimiento')
                    ->orWhereDate('fecha_vencimiento', '>=', today());
            })
            ->orderByDesc('fijado')
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('id');
    }
}
