<?php
namespace Database\Factories;
use App\Models\Pago;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comerciante;
/** @extends Factory<Pago> */
class PagoFactory extends Factory
{
    protected $model = Pago::class;
    public function definition(): array
    {
        return [
            'comerciante_id' => Comerciante::inRandomOrder()->value('id') ?? Comerciante::factory(),
            'comprobante' => 'REC-'.fake()->unique()->numerify('##########'),
            'fecha_pago' => today(),
            'metodo' => fake()->randomElement(['efectivo', 'transferencia']),
            'total' => 0,
        ];
    }
}