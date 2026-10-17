<?php
namespace Database\Factories;
use App\Models\Tarifa;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Seccion;
/** @extends Factory<Tarifa> */
class TarifaFactory extends Factory
{
    protected $model = Tarifa::class;
    public function definition(): array
    {
        return ['seccion_id' => Seccion::factory(), 'monto_m2' => '25.0000', 'vigente_desde' => today()->startOfMonth()->subMonths(5), 'vigente_hasta' => null];
    }
}
