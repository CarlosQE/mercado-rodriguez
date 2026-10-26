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
        return [
            'nombre' => 'Sección '.fake()->unique()->numerify('######'),
            'prefijo' => fake()->unique()->regexify('[A-Z]{3}'),
            'tarifa_m2' => fake()->randomElement(['5.00', '6.50', '8.00', '10.00', '12.50']),
        ];
    }
}