<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // CHECK permite NULL: exigirlo explícitamente evita anulaciones sin motivo.
        DB::statement("ALTER TABLE pagos ADD CONSTRAINT pagos_anulacion_motivo_check CHECK (
            (anulado_en IS NULL AND motivo_anulacion IS NULL) OR
            (anulado_en IS NOT NULL AND motivo_anulacion IS NOT NULL AND length(trim(motivo_anulacion)) > 0)
        )");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pagos DROP CONSTRAINT pagos_anulacion_motivo_check');
    }
};
