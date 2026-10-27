<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Artisan::command('deploy:demo', function () {
    // A session lock serializes first boots, including migrations.
    DB::select('select pg_advisory_lock(?)', [9032026]);
    try {
        if ($this->call('migrate', ['--force' => true]) !== 0) {
            throw new RuntimeException('No se pudieron aplicar las migraciones.');
        }

        DB::transaction(function () {
            if (! Schema::hasTable('deployment_markers')) {
                Schema::create('deployment_markers', function (Blueprint $table) {
                    $table->string('name')->primary();
                });
            }
            if (DB::table('deployment_markers')->where('name', 'initial-demo')->exists()) {
                $this->info('Demo ya inicializado; se conservan los registros.');
                return;
            }
            foreach (['users', 'secciones', 'comerciantes', 'puestos', 'asignaciones',
                'obligaciones', 'pagos', 'datos_mercado', 'fotos_mercado', 'comunicados'] as $table) {
                if (DB::table($table)->exists()) {
                    throw new RuntimeException("La tabla {$table} contiene datos. No se sembrará ni borrará la base existente.");
                }
            }
            if ($this->call('db:seed', ['--force' => true]) !== 0) {
                throw new RuntimeException('La carga del demo falló.');
            }
            DB::table('deployment_markers')->insert(['name' => 'initial-demo']);
            $this->info('Datos de demostración creados.');
        });
    } finally {
        DB::select('select pg_advisory_unlock(?)', [9032026]);
    }
})->purpose('Inicializa una base PostgreSQL vacía una sola vez sin borrar datos');
