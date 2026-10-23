<?php

namespace Database\Factories;

use App\Models\DatoMercado;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DatoMercado> */
class DatoMercadoFactory extends Factory
{
    protected $model = DatoMercado::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Mercado Rodríguez',
            'eslogan' => 'Un mercado de barrio con memoria y futuro.',
            'historia' => fake()->paragraphs(3, true),
            'direccion' => 'Av. Rodríguez N.º 1420, zona San Pedro, La Paz, Bolivia',
            'referencia' => 'A media cuadra de la plaza del barrio, ingreso por la calle lateral.',
            'latitud' => '-16.4955000',
            'longitud' => '-68.1330000',
            'horario_atencion' => 'Lunes a sábado, de 06:00 a 19:00',
            'telefono' => '+591 2 245 6789',
            'email' => 'contacto@mercadorodriguez.test',
        ];
    }
}
