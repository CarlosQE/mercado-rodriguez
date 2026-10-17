<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('schema/mercado.sql')));
    }
    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS saldos_obligaciones; DROP TABLE IF EXISTS hallazgos, inspecciones, pagos, obligaciones, clausuras, asignaciones, tarifas, puestos, comerciantes, secciones; DROP FUNCTION IF EXISTS preparar_obligacion(), validar_pago(), proteger_historial(), validar_inspeccion(), validar_hallazgo();');
    }
};
