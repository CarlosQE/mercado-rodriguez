<?php
namespace Database\Factories;
use App\Models\Obligacion;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Asignacion;
use App\Models\Tarifa;
/** @extends Factory<Obligacion> */
class ObligacionFactory extends Factory
{
    protected $model = Obligacion::class;
    public function definition(): array
    {
        return [
            'asignacion_id' => Asignacion::factory(),
            'tarifa_id' => function (array $attributes) {
                $asignacion = Asignacion::findOrFail($attributes['asignacion_id']);
                return Tarifa::factory()->create(['seccion_id' => $asignacion->puesto->seccion_id])->id;
            },
            'periodo' => today()->startOfMonth(), 'fecha_emision' => today()->startOfMonth(),
            'fecha_vencimiento' => today()->startOfMonth()->addDays(9),
        ];
    }
}
