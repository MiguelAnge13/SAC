<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('codigos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable(); // nombre corto del código
            $table->string('lenguaje')->nullable();
            $table->date('fecha')->nullable();
            $table->text('codigo')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('codigos');
    }
};
