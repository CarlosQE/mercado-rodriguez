<?php
namespace Database\Factories;
use App\Models\Asignacion;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Puesto;
use App\Models\Comerciante;
/** @extends Factory<Asignacion> */
class AsignacionFactory extends Factory
{
    protected $model = Asignacion::class;
    public function definition(): array
    {
        return ['puesto_id' => Puesto::factory(), 'comerciante_id' => Comerciante::factory(), 'fecha_inicio' => today()->startOfMonth()->subMonths(5), 'fecha_fin' => null, 'motivo' => 'Adjudicación municipal de uso del puesto'];
    }
}
