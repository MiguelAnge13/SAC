<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('microcontroladores', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->nullable()->unique(); // identificador único si existe
            $table->string('vendor_id')->nullable();
            $table->string('product_id')->nullable();
            $table->string('port')->nullable(); // p.ej. COM3
            $table->string('modelo')->nullable(); // descripción o modelo
            $table->timestamp('primera_conexion_at')->nullable();
            $table->timestamp('ultima_conexion_at')->nullable();
            $table->boolean('conectado')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('serial');
            $table->index('port');
            $table->index('conectado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('microcontroladores');
    }
};
