<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('obligaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')->constrained('asignaciones')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('periodo');
            $table->date('fecha_vencimiento');
            $table->decimal('superficie_m2', 8, 2);
            $table->decimal('tarifa_m2', 8, 2);
            $table->decimal('importe', 10, 2); // superficie_m2 * tarifa_m2
            $table->string('estado', 20)->default('pendiente'); // pendiente, pagada
            $table->timestamps();

            $table->unique(['asignacion_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obligaciones');
    }
};