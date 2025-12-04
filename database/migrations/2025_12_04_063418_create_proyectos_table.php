<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_hora')->nullable();
            $table->unsignedBigInteger('codigo_id')->nullable(); // referencia a codigos
            $table->enum('estatus', ['pendiente','en_progreso','completado'])->default('pendiente');
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->nullOnDelete(); // creador
            $table->timestamps();

            $table->foreign('codigo_id')->references('id')->on('codigos')->nullOnDelete();
        });
    }

    public function down() {
        Schema::dropIfExists('proyectos');
    }
};
