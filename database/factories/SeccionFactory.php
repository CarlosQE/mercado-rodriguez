<?php
namespace Database\Factories;
use App\Models\Seccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Seccion> */
class SeccionFactory extends Factory
{
    protected $model = Seccion::class;
    public function definition(): array
    {
        return ['nombre' => 'Sección '.fake()->unique()->numerify('######'), 'intervalo_inspeccion_dias' => 30];
    }
}
