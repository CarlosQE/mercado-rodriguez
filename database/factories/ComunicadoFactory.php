<?php

namespace Database\Factories;

use App\Models\Comunicado;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Comunicado> */
class ComunicadoFactory extends Factory
{
    protected $model = Comunicado::class;

    public function definition(): array
    {
        $publicacion = fake()->dateTimeBetween('-2 months', 'now');

        return [
            'user_id' => User::factory(),
            'titulo' => fake()->sentence(5),
            'contenido' => fake()->paragraphs(2, true),
            'tipo' => fake()->randomElement(['general', 'urgente', 'evento', 'cobranza']),
            'fecha_publicacion' => $publicacion,
            'fecha_vencimiento' => fake()->optional()->dateTimeBetween($publicacion, '+1 month'),
            'fijado' => false,
            'activo' => true,
        ];
    }
}
