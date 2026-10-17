<?php
namespace Database\Seeders;
use App\Models\{Asignacion, Clausura, Comerciante, Hallazgo, Inspeccion, Obligacion, Pago, Puesto, Seccion, Tarifa};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MercadoDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Un demo nunca mezcla ni borra datos existentes.
        if (Puesto::exists() || Comerciante::exists() || Tarifa::exists()) {
            throw new RuntimeException('El demo requiere puestos, comerciantes y tarifas vacíos. Use una base de pruebas nueva.');
        }
        fake()->seed(20261017);
        fake()->unique(true);
        DB::transaction(function (): void {
            $inicio = today()->startOfMonth()->subMonths(5);
            $cambio = today()->startOfMonth()->subMonths(2);
            $comerciantes = Comerciante::factory()->count(280)->create();
            $contador = 0;
            // 80 carnes + 100 verduras + 80 abarrotes + 40 comida = 300.
            foreach ([['Carnes', 'CAR', 80, '30.0000'], ['Verduras', 'VER', 100, '18.0000'], ['Abarrotes', 'ABA', 80, '24.0000'], ['Comida', 'COM', 40, '35.0000']] as [$nombre, $prefijo, $cantidad, $precio]) {
                $seccion = Seccion::where('nombre', $nombre)->firstOrFail();
                $anterior = Tarifa::factory()->create(['seccion_id' => $seccion->id, 'monto_m2' => $precio, 'vigente_desde' => $inicio, 'vigente_hasta' => $cambio]);
                $actual = Tarifa::factory()->create(['seccion_id' => $seccion->id, 'monto_m2' => number_format((float) $precio + 2, 4, '.', ''), 'vigente_desde' => $cambio]);
                for ($numero = 1; $numero <= $cantidad; $numero++, $contador++) {
                    $puesto = Puesto::factory()->create(['seccion_id' => $seccion->id, 'codigo' => sprintf('%s-%03d', $prefijo, $numero), 'superficie_m2' => fake()->randomElement($nombre === 'Comida' ? ['9.00', '12.00', '15.00', '18.00'] : ['4.00', '6.00', '7.50', '8.00'])]);
                    // Distribuir 40 libres por las cuatro secciones.
                    if ($contador % 15 < 2) continue;
                    $indice = $contador - 2 * (intdiv($contador, 15) + 1);
                    $titular = $comerciantes[$indice];
                    $transferida = $indice < 20;
                    $asignaciones = collect();
                    if ($transferida) {
                        $asignaciones->push(Asignacion::factory()->create(['puesto_id' => $puesto->id, 'comerciante_id' => $comerciantes[260 + $indice]->id, 'fecha_inicio' => $inicio, 'fecha_fin' => $cambio, 'motivo' => 'Adjudicación inicial; finalizada por transferencia autorizada']));
                    }
                    $vigente = Asignacion::factory()->create(['puesto_id' => $puesto->id, 'comerciante_id' => $titular->id, 'fecha_inicio' => $transferida ? $cambio : $inicio, 'motivo' => $transferida ? 'Transferencia autorizada al nuevo titular' : 'Adjudicación municipal de uso del puesto']);
                    $asignaciones->push($vigente);
                    // Seis meses. La emisión repetida del mismo período está prohibida por UNIQUE.
                    for ($mes = 0; $mes < 6; $mes++) {
                        $periodo = $inicio->copy()->addMonths($mes);
                        $asignacion = $transferida && $periodo->lt($cambio) ? $asignaciones->first() : $vigente;
                        $obligacion = Obligacion::factory()->create(['asignacion_id' => $asignacion->id, 'tarifa_id' => ($periodo->lt($cambio) ? $anterior : $actual)->id, 'periodo' => $periodo, 'fecha_emision' => $periodo, 'fecha_vencimiento' => $periodo->copy()->addDays(9)]);
                        $obligacion->refresh(); // Importe generado por PostgreSQL.
                        $patron = $indice % 5;
                        if (($patron === 0 && $mes >= 3) || ($patron === 1 && $mes >= 4)) continue;
                        $centavos = (int) str_replace('.', '', $obligacion->importe);
                        $parcial = $patron === 2 && $mes >= 4;
                        $monto = $parcial ? intdiv($centavos, 2) : $centavos;
                        $fechaPago = $periodo->copy()->addDays(4 + ($indice % 8));
                        if ($fechaPago->gt(today())) $fechaPago = today();
                        $metodo = $indice % 3 === 0 ? 'transferencia' : 'efectivo';
                        Pago::factory()->create(['obligacion_id' => $obligacion->id, 'recibo' => sprintf('MR-%s-%04d', $periodo->format('Ym'), $indice + 1), 'fecha_pago' => $fechaPago, 'importe' => sprintf('%d.%02d', intdiv($monto, 100), $monto % 100), 'metodo' => $metodo, 'referencia' => $metodo === 'transferencia' ? sprintf('DEMO-%s-%04d', $periodo->format('Ym'), $indice + 1) : null]);
                    }
                    // Dos visitas: parte de los puestos tiene seguimiento vencido.
                    $clausurado = $indice % 26 === 0;
                    $dias = $clausurado ? 4 : ($indice % 4 === 0 ? 75 : 10);
                    foreach ([110, $dias] as $antiguedad) {
                        $fecha = today()->subDays($antiguedad);
                        $responsable = $transferida && $fecha->lt($cambio) ? $asignaciones->first() : $vigente;
                        $observada = $indice % 6 === 0 || $clausurado;
                        $inspeccion = Inspeccion::factory()->create(['asignacion_id' => $responsable->id, 'fecha' => $fecha, 'resultado' => $clausurado && $antiguedad !== 110 ? 'no_aprobada' : ($observada ? 'con_observaciones' : 'aprobada'), 'proxima_fecha' => $fecha->copy()->addDays($observada ? 7 : $seccion->intervalo_inspeccion_dias), 'observaciones' => $observada ? 'Se requiere corregir el almacenamiento de residuos.' : 'Limpieza y almacenamiento conformes.']);
                        if ($observada) Hallazgo::factory()->create(['inspeccion_id' => $inspeccion->id, 'fecha_limite' => $fecha->copy()->addDays(7), 'subsanado_en' => $antiguedad === 110 ? $fecha->copy()->addDays(5) : null, 'evidencia' => $antiguedad === 110 ? 'Acta de seguimiento: contenedor con tapa instalado.' : null]);
                    }
                    if ($clausurado) Clausura::factory()->create(['puesto_id' => $puesto->id, 'fecha_inicio' => today()->subDays(3)]);
                }
            }
        });
    }
}
