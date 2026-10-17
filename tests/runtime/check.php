<?php
// Integración real sin dependencias de un framework de pruebas.
use App\Models\{Asignacion, Clausura, Comerciante, Hallazgo, Inspeccion, Obligacion, Pago, Puesto, Seccion, Tarifa};
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\{Artisan, DB};
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

function check(bool $condition, string $message): void
{
    if (! $condition) throw new RuntimeException($message);
}
function rejected(callable $action, string $sqlstate): void
{
    try {
        DB::transaction($action);
    } catch (QueryException $exception) {
        check($exception->errorInfo[0] === $sqlstate, 'SQLSTATE inesperado: '.$exception->errorInfo[0]);
        return;
    }
    throw new RuntimeException('La base aceptó una operación inválida');
}
function requestCheck(string $name, array $data, bool $valid, bool $authenticated = true): void
{
    global $app;
    $class = 'App\\Http\\Requests\\Save'.$name.'Request';
    $request = $class::create('/', 'POST', $data);
    $request->setContainer($app)->setRedirector($app->make('redirect'));
    $request->setUserResolver(fn () => $authenticated ? (object) ['id' => 1] : null);
    try {
        $request->validateResolved();
        check($valid && $authenticated, "Request $name aceptó datos inválidos");
    } catch (ValidationException $exception) {
        check(! $valid && $authenticated, "Request $name rechazó datos válidos: ".json_encode($exception->errors()));
    } catch (AuthorizationException $exception) {
        check(! $authenticated, 'Autorización inesperada');
    }
}

$worker = ($argv[1] ?? '') === '--worker';
$schema = $worker ? ($argv[2] ?? '') : 'test_mercado_'.bin2hex(random_bytes(6));
check((bool) preg_match('/^test_mercado_[a-f0-9]{12}$/', $schema), 'Esquema inválido');
if (! $worker) DB::statement('CREATE SCHEMA '.$schema);
config(['database.connections.pgsql.search_path' => $schema.',public']);
DB::purge('pgsql');

if ($worker) {
    try {
        DB::beginTransaction();
        echo "READY\n";
        flush();
        Pago::factory()->create(['obligacion_id' => (int) $argv[3], 'recibo' => $argv[4], 'importe' => '150.00']);
        echo "INSERTED\n";
        flush();
        if ($argv[5] === 'hold') fgets(STDIN);
        DB::commit();
        echo "COMMITTED\n";
    } catch (QueryException $exception) {
        DB::rollBack();
        echo 'REJECTED '.$exception->errorInfo[0]."\n";
    }
    exit;
}

$processes = [];
$exitStatus = 0;
try {
    check(Artisan::call('migrate', ['--force' => true]) === 0, 'Falló la migración');
    check(Artisan::call('migrate:rollback', ['--force' => true]) === 0, 'Falló el rollback');
    check(Artisan::call('migrate', ['--force' => true]) === 0, 'Falló la segunda migración');
    echo "OK migración, rollback y segunda migración\n";
    $a = Asignacion::factory()->create();
    $puesto = $a->puesto;
    $puesto->update(['codigo' => 'TEST-001']);
    rejected(fn () => Puesto::factory()->create(['codigo' => $puesto->codigo]), '23505');
    rejected(fn () => Puesto::factory()->create(['superficie_m2' => '0']), '23514');
    rejected(fn () => Asignacion::factory()->create(['puesto_id' => $puesto->id]), '23P01');
    $tarifa = Tarifa::factory()->create(['seccion_id' => $puesto->seccion_id]);
    rejected(fn () => Tarifa::factory()->create(['seccion_id' => $puesto->seccion_id]), '23P01');
    rejected(fn () => $tarifa->update(['monto_m2' => '99']), 'P0001');
    echo "OK unicidad, valores positivos, intervalos y tarifas históricas\n";

    // Valores simples: 6 m² × 25 BOB = 150 BOB.
    $a2 = Asignacion::factory()->create(['puesto_id' => Puesto::factory()->create(['superficie_m2' => '6.00'])->id]);
    $t2 = Tarifa::factory()->create(['seccion_id' => $a2->puesto->seccion_id]);
    $o = Obligacion::factory()->create(['asignacion_id' => $a2->id, 'tarifa_id' => $t2->id])->refresh();
    check($o->importe === '150.00' && $o->superficie_m2 === '6.00', 'Cálculo de emisión incorrecto');
    rejected(fn () => Obligacion::factory()->create(['asignacion_id' => $a2->id, 'tarifa_id' => $t2->id]), '23505');
    rejected(fn () => $o->update(['monto_m2' => '1']), 'P0001');
    rejected(fn () => $a2->update(['comerciante_id' => Comerciante::factory()->create()->id]), 'P0001');
    rejected(fn () => $a2->puesto->update(['superficie_m2' => '10']), 'P0001');
    $pago = Pago::factory()->create(['obligacion_id' => $o->id, 'importe' => '60.00']);
    check(DB::table('saldos_obligaciones')->where('obligacion_id', $o->id)->value('saldo') === '90.00', 'Saldo parcial incorrecto');
    rejected(fn () => Pago::factory()->create(['obligacion_id' => $o->id, 'importe' => '90.01']), 'P0001');
    rejected(fn () => $pago->delete(), 'P0001');
    rejected(fn () => DB::table('pagos')->where('id', $pago->id)->update(['anulado_en' => now(), 'motivo_anulacion' => null]), '23514');
    DB::table('pagos')->where('id', $pago->id)->update(['anulado_en' => now(), 'motivo_anulacion' => 'Recibo emitido por error']);
    check(DB::table('saldos_obligaciones')->where('obligacion_id', $o->id)->value('saldo') === '150.00', 'Anulación no restituyó el saldo');
    echo "OK obligaciones, pagos parciales, sobrepagos y anulaciones\n";

    $visita = Inspeccion::factory()->create(['asignacion_id' => $a->id, 'fecha' => today()->subDays(2)]);
    Hallazgo::factory()->create(['inspeccion_id' => $visita->id]);
    rejected(fn () => Inspeccion::factory()->create(['asignacion_id' => $a->id, 'fecha' => $a->fecha_inicio->copy()->subDay()]), 'P0001');
    rejected(fn () => Hallazgo::factory()->create(['inspeccion_id' => $visita->id, 'subsanado_en' => today(), 'evidencia' => null]), '23514');
    // Transferir hoy: una visita anterior permanece con el titular previo.
    $a->update(['fecha_fin' => today()]);
    $nuevo = Asignacion::factory()->create(['puesto_id' => $puesto->id, 'fecha_inicio' => today()]);
    check($puesto->estadoEn(today()->toDateString()) === 'ocupado', 'Estado ocupado incorrecto');
    check($puesto->ultimaInspeccion()->id === $visita->id, 'La transferencia perdió la visita previa');
    $clausura = Clausura::factory()->create(['puesto_id' => $puesto->id]);
    check($puesto->estadoEn(today()->toDateString()) === 'clausurado', 'Estado clausurado incorrecto');
    $clausura->update(['fecha_fin' => today()]);
    check($puesto->estadoEn(today()->toDateString()) === 'ocupado', 'La reapertura perdió la titularidad');
    check(Puesto::factory()->create()->estadoEn(today()->toDateString()) === 'libre', 'Estado libre incorrecto');
    echo "OK transferencia, inspecciones, hallazgos y estados\n";

    $seccionData = ['nombre' => 'Sección de pruebas', 'intervalo_inspeccion_dias' => 30];
    requestCheck('Seccion', $seccionData, true);
    requestCheck('Seccion', $seccionData, true, false);
    foreach (['Seccion','Comerciante','Puesto','Tarifa','Asignacion','Clausura','Obligacion','Pago','Inspeccion','Hallazgo'] as $name) requestCheck($name, [], false);
    requestCheck('Comerciante', Comerciante::factory()->make()->getAttributes(), true);
    requestCheck('Puesto', ['seccion_id' => $puesto->seccion_id, 'codigo' => 'REQ-001', 'superficie_m2' => '4.25'], true);
    requestCheck('Tarifa', ['seccion_id' => Seccion::factory()->create()->id, 'monto_m2' => '23.7500', 'vigente_desde' => today()->toDateString()], true);
    requestCheck('Asignacion', ['puesto_id' => Puesto::factory()->create()->id, 'comerciante_id' => $nuevo->comerciante_id, 'fecha_inicio' => today()->toDateString(), 'motivo' => 'Adjudicación de prueba'], true);
    requestCheck('Clausura', ['puesto_id' => Puesto::factory()->create()->id, 'fecha_inicio' => today()->toDateString(), 'motivo' => 'Incumplimiento sanitario'], true);
    requestCheck('Obligacion', ['asignacion_id' => $a->id, 'tarifa_id' => $tarifa->id, 'periodo' => today()->startOfMonth()->toDateString(), 'fecha_emision' => today()->startOfMonth()->toDateString(), 'fecha_vencimiento' => today()->toDateString()], true);
    requestCheck('Pago', ['obligacion_id' => $o->id, 'recibo' => 'REQ-VALIDO', 'fecha_pago' => today()->toDateString(), 'importe' => '1.00', 'metodo' => 'efectivo'], true);
    requestCheck('Inspeccion', ['asignacion_id' => $nuevo->id, 'fecha' => today()->toDateString(), 'inspector' => 'Inspector de prueba', 'resultado' => 'aprobada', 'proxima_fecha' => today()->addDays(30)->toDateString()], true);
    requestCheck('Hallazgo', ['inspeccion_id' => $visita->id, 'descripcion' => 'Limpieza de contenedores', 'fecha_limite' => today()->addDays(7)->toDateString()], true);
    requestCheck('Asignacion', ['puesto_id' => $puesto->id, 'comerciante_id' => $nuevo->comerciante_id, 'fecha_inicio' => today()->toDateString(), 'motivo' => 'Solicitud duplicada'], false);
    requestCheck('Pago', ['obligacion_id' => $o->id, 'recibo' => 'REQ-PAGO', 'fecha_pago' => today()->toDateString(), 'importe' => '150.01', 'metodo' => 'efectivo'], false);
    requestCheck('Inspeccion', ['asignacion_id' => $a->id, 'fecha' => today()->toDateString(), 'inspector' => 'Inspector de prueba', 'resultado' => 'aprobada', 'proxima_fecha' => today()->addDays(30)->toDateString()], false);
    echo "OK Form Requests: validación, autorización y reglas entre registros\n";

    // Dos conexiones independientes intentan pagar el mismo saldo completo.
    $spec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    foreach (['hold','normal'] as $index => $mode) {
        $process = proc_open([PHP_BINARY, __FILE__, '--worker', $schema, (string) $o->id, 'RACE-'.$index, $mode], $spec, $pipes);
        check(is_resource($process), 'No se pudo iniciar el proceso de prueba');
        $processes[] = [$process, $pipes];
        stream_set_timeout($pipes[1], 10);
        check(trim(fgets($pipes[1])) === 'READY', 'El proceso no inició');
        if ($index === 0) check(trim(fgets($pipes[1])) === 'INSERTED', 'Primer pago no insertado');
    }
    $read = [$processes[1][1][1]]; $write = null; $except = null;
    check(stream_select($read, $write, $except, 0, 300000) === 0, 'El segundo pago no esperó al bloqueo');
    fwrite($processes[0][1][0], "commit\n");
    check(trim(fgets($processes[0][1][1])) === 'COMMITTED', 'El primer pago no confirmó');
    check(trim(fgets($processes[1][1][1])) === 'REJECTED P0001', 'El segundo pago no rechazó el sobrepago');
    check(DB::table('saldos_obligaciones')->where('obligacion_id', $o->id)->value('saldo') === '0.00', 'Saldo concurrente incorrecto');
    echo "OK pagos concurrentes: un pago confirmado y otro rechazado\n";
} catch (Throwable $exception) {
    $exitStatus = 1;
    fwrite(STDERR, $exception->getMessage()."\n");
    $caller = $exception->getTrace()[0] ?? [];
    fwrite(STDERR, 'Prueba: '.($caller['file'] ?? $exception->getFile()).':'.($caller['line'] ?? $exception->getLine())."\n");
} finally {
    foreach ($processes as [$process, $pipes]) {
        if (proc_get_status($process)['running']) proc_terminate($process);
        foreach ($pipes as $pipe) fclose($pipe);
        proc_close($process);
    }
    DB::disconnect('pgsql');
    config(['database.connections.pgsql.search_path' => 'public']);
    DB::purge('pgsql');
    DB::statement('DROP SCHEMA '.$schema.' CASCADE');
}
if ($exitStatus === 0) echo "Todas las pruebas de integración pasaron; esquema temporal eliminado.\n";
exit($exitStatus);
