<?php
namespace Database\Factories;
use App\Models\Comerciante;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Comerciante> */
class ComercianteFactory extends Factory
{
    protected $model = Comerciante::class;
    public function definition(): array
    {
        $nombres = ['María', 'Juan', 'Rosa', 'Luis', 'Carmen', 'Jorge', 'Ana', 'René', 'Sonia', 'Víctor', 'Elena', 'Mario', 'Patricia', 'Edgar', 'Lucía', 'Óscar'];
        $apellidos = ['Quispe', 'Mamani', 'Flores', 'Condori', 'Choque', 'Vargas', 'Rojas', 'Gutiérrez', 'Huanca', 'Aguilar', 'Pérez', 'Limachi', 'Apaza', 'Calle'];
        return [
            'tipo_documento' => 'CI', 'documento' => fake()->unique()->numerify('########'),
            'nombres' => fake()->randomElement($nombres),
            'apellidos' => fake()->randomElement($apellidos).' '.fake()->randomElement($apellidos),
            'telefono' => fake()->randomElement(['6', '7']).fake()->numerify('#######'),
            'email' => fake()->optional(0.55)->safeEmail(),
            'direccion' => fake()->randomElement(['Calle Illampu', 'Av. Buenos Aires', 'Calle Max Paredes', 'Av. Baptista', 'Calle Santa Cruz']).' N.º '.fake()->numberBetween(100, 1800).', La Paz',
            'activo' => true,
        ];
    }
}
