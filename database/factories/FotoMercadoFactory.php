<?php

namespace Database\Factories;

use App\Models\FotoMercado;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FotoMercado> */
class FotoMercadoFactory extends Factory
{
    protected $model = FotoMercado::class;

    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(4),
            'descripcion' => fake()->optional()->sentence(),
            'ruta' => 'img/mercado/foto-' . fake()->numberBetween(1, 6) . '.svg',
            'orden' => fake()->numberBetween(0, 10),
        ];
    }
}
