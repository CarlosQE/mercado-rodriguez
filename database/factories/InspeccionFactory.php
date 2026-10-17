<?php
namespace Database\Factories;
use App\Models\Inspeccion;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Asignacion;
/** @extends Factory<Inspeccion> */
class InspeccionFactory extends Factory
{
    protected $model = Inspeccion::class;
    public function definition(): array
    {
        return ['asignacion_id' => Asignacion::factory(), 'fecha' => today(),
            'inspector' => fake()->randomElement(['Lic. Andrea Salinas', 'Ing. Pablo Medina', 'Lic. Daniela Arias']),
            'resultado' => 'aprobada', 'proxima_fecha' => today()->addDays(30),
            'observaciones' => 'Superficies limpias, residuos separados y documentación revisada.'];
    }
}
