<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_mercado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('eslogan', 180)->nullable();
            $table->text('historia');
            $table->string('direccion', 255);
            $table->string('referencia', 255)->nullable();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->string('horario_atencion', 180);
            $table->string('telefono', 30);
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_mercado');
    }
};
