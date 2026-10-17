<?php
namespace Database\Factories;
use App\Models\Hallazgo;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Inspeccion;
/** @extends Factory<Hallazgo> */
class HallazgoFactory extends Factory
{
    protected $model = Hallazgo::class;
    public function definition(): array
    {
        return ['inspeccion_id' => Inspeccion::factory()->state(['resultado' => 'con_observaciones']), 'descripcion' => 'Reponer el contenedor de residuos con tapa.', 'fecha_limite' => today()->addDays(7), 'subsanado_en' => null, 'evidencia' => null];
    }
}
