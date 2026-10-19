<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('comerciantes', function (Blueprint $table) {
            $table->id();
            $table->string('documento', 30)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('rubro', 80);
            $table->string('telefono', 25);
            $table->string('email')->nullable();
            $table->string('direccion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comerciantes');
    }
};