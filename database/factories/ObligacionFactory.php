<?php
namespace Database\Factories;
use App\Models\Obligacion;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Asignacion;
/** @extends Factory<Obligacion> */
class ObligacionFactory extends Factory
{
    protected $model = Obligacion::class;
    public function definition(): array
    {
        return [
            'asignacion_id' => Asignacion::inRandomOrder()->value('id') ?? Asignacion::factory(),
            'periodo' => today()->startOfMonth(),
            'fecha_vencimiento' => today()->startOfMonth()->addDays(9),
            'superficie_m2' => fn (array $attributes) => Asignacion::findOrFail($attributes['asignacion_id'])->puesto->superficie_m2,
            'tarifa_m2' => fn (array $attributes) => Asignacion::findOrFail($attributes['asignacion_id'])->puesto->seccion->tarifa_m2,
            'importe' => fn (array $attributes) => round($attributes['superficie_m2'] * $attributes['tarifa_m2'], 2),
            'estado' => 'pendiente',
        ];
    }
}