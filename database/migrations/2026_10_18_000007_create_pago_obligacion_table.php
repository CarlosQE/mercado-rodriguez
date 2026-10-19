<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('pago_obligacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('obligacion_id')->constrained('obligaciones')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('monto', 10, 2);
            $table->timestamps();

            $table->unique(['pago_id', 'obligacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_obligacion');
    }
};