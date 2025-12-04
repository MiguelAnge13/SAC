<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('proyecto_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->string('ruta'); // path en storage (public)
            $table->string('nombre_original')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('proyecto_imagenes');
    }
};
