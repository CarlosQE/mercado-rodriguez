<?php
namespace Database\Seeders;
use App\Models\{Asignacion, Comerciante, Obligacion, Pago, Puesto, Seccion};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $inicio = today()->startOfMonth()->subMonths(5);
        $cambio = today()->startOfMonth()->subMonths(2);

        $comerciantes = Comerciante::factory()->count(280)->create();

        // 80 + 100 + 80 + 60 = 320 puestos
        $puestos = collect();
        foreach ([['Carnes', 'CAR', 80, '30.00'], ['Verduras', 'VER', 100, '18.00'], ['Abarrotes', 'ABA', 80, '24.00'], ['Comida', 'COM', 60, '35.00']] as [$nombre, $prefijo, $cantidad, $tarifa]) {
            $seccion = Seccion::create(['nombre' => $nombre, 'tarifa_m2' => $tarifa]);
            for ($numero = 1; $numero <= $cantidad; $numero++) {
                $puestos->push(Puesto::factory()->create([
                    'seccion_id' => $seccion->id,
                    'codigo' => sprintf('%s-%03d', $prefijo, $numero),
                ]));
            }
        }
        $puestos = Puesto::with('seccion')->orderBy('id')->get();

        // De cada 16 puestos: 1 libre, 1 clausurado y 14 ocupados (20 libres, 20 clausurados, 280 ocupados).
        $k = 0;
        foreach ($puestos as $n => $puesto) {
            if ($n % 16 === 0) {
                continue;
            }
            if ($n % 16 === 1) {
                $puesto->update(['estado' => 'clausurado']);
                continue;
            }

            // Los primeros 20 puestos ocupados tuvieron una transferencia hace 3 meses.
            $transferido = $k < 20;
            if ($transferido) {
                Asignacion::factory()->create([
                    'puesto_id' => $puesto->id,
                    'comerciante_id' => $comerciantes[($k + 140) % 280]->id,
                    'fecha_inicio' => $inicio,
                    'fecha_fin' => $cambio,
                    'motivo' => 'Adjudicación inicial; finalizada por transferencia',
                ]);
            }
            $desde = $transferido ? $cambio : $inicio;
            $asignacion = Asignacion::factory()->create([
                'puesto_id' => $puesto->id,
                'comerciante_id' => $comerciantes[$k]->id,
                'fecha_inicio' => $desde,
                'motivo' => $transferido ? 'Transferencia autorizada al nuevo titular' : 'Adjudicación municipal de uso del puesto',
            ]);
            $puesto->update(['estado' => 'ocupado']);

            // Una obligación por mes desde que empieza la asignación vigente.
            $obligaciones = collect();
            for ($periodo = $desde->copy(); $periodo->lte(today()); $periodo->addMonth()) {
                $obligaciones->push(Obligacion::factory()->create([
                    'asignacion_id' => $asignacion->id,
                    'periodo' => $periodo->copy(),
                    'fecha_vencimiento' => $periodo->copy()->addDays(9),
                    'superficie_m2' => $puesto->superficie_m2,
                    'tarifa_m2' => $puesto->seccion->tarifa_m2,
                    'importe' => round($puesto->superficie_m2 * $puesto->seccion->tarifa_m2, 2),
                ]));
            }

            // Un solo pago cubre varias obligaciones: 0, 1 o 3 meses según el comerciante.
            $cuantas = match ($k % 5) {
                0 => 0,
                1 => 1,
                default => 3,
            };
            if ($cuantas > 0) {
                $cubiertas = $obligaciones->take($cuantas);
                $fechaPago = $cubiertas->last()->periodo->copy()->addDays(5);
                if ($fechaPago->gt(today())) {
                    $fechaPago = today();
                }
                $pago = Pago::factory()->create([
                    'comerciante_id' => $comerciantes[$k]->id,
                    'comprobante' => sprintf('MR-%05d', $k + 1),
                    'fecha_pago' => $fechaPago,
                    'total' => $cubiertas->sum('importe'),
                ]);
                foreach ($cubiertas as $obligacion) {
                    $pago->obligaciones()->attach($obligacion->id, ['monto' => $obligacion->importe]);
                    $obligacion->update(['estado' => 'pagada']);
                }
            }
            $k++;
        }
    }
}