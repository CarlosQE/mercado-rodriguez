<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comerciante extends Model
{
    use HasFactory;

    protected $table = 'comerciantes';
    protected $fillable = ['documento', 'nombres', 'apellidos', 'rubro', 'telefono', 'email', 'direccion', 'activo'];
    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'comerciante_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'comerciante_id');
    }
}