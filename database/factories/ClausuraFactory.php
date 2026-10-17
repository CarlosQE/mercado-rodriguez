<?php
namespace Database\Factories;
use App\Models\Clausura;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Puesto;
/** @extends Factory<Clausura> */
class ClausuraFactory extends Factory
{
    protected $model = Clausura::class;
    public function definition(): array
    {
        return ['puesto_id' => Puesto::factory(), 'fecha_inicio' => today()->subDays(7), 'fecha_fin' => null, 'motivo' => 'Suspensión temporal por deficiencias sanitarias; pendiente reinspección'];
    }
}
