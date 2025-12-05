<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('librerias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('lenguaje')->nullable();
            $table->string('version')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('icono')->nullable(); // ruta en storage/app/public/librerias
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->nullOnDelete(); // quien la importó/creó
            $table->timestamps();

            $table->unique(['nombre','lenguaje']); // evita duplicados (por lenguaje)
        });
    }

    public function down() {
        Schema::dropIfExists('librerias');
    }
};
