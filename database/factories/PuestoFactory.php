<?php
namespace Database\Factories;
use App\Models\Puesto;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Seccion;
/** @extends Factory<Puesto> */
class PuestoFactory extends Factory
{
    protected $model = Puesto::class;
    public function definition(): array
    {
        return [
            'seccion_id' => Seccion::inRandomOrder()->value('id') ?? Seccion::factory(),
            'codigo' => 'P-'.fake()->unique()->numerify('######'),
            'superficie_m2' => fake()->randomElement(['4.00', '6.00', '8.00', '10.50', '12.00']),
            'estado' => 'libre',
        ];
    }
}