<?php
namespace Database\Factories;
use App\Models\Pago;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Obligacion;
/** @extends Factory<Pago> */
class PagoFactory extends Factory
{
    protected $model = Pago::class;
    public function definition(): array
    {
        return [
            'obligacion_id' => Obligacion::factory(),
            'recibo' => 'REC-'.fake()->unique()->numerify('##########'), 'fecha_pago' => today(),
            'importe' => fn (array $attributes) => Obligacion::findOrFail($attributes['obligacion_id'])->importe,
            'metodo' => 'efectivo', 'referencia' => null,
        ];
    }
}
