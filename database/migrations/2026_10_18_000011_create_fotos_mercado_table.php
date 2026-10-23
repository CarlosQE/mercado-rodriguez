<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotos_mercado', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 120);
            $table->string('descripcion', 255)->nullable();
            $table->string('ruta', 255);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos_mercado');
    }
};
