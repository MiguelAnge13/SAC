<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('calibraciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // quien hizo la calibración
            $table->string('session_id')->nullable(); // id de sesión para filtrar historial por sesión
            $table->unsignedInteger('servo_num'); // número del servomotor
            $table->integer('angulo'); // ángulo calibrado
            $table->text('nota')->nullable(); // opcional
            $table->timestamps();

            $table->index('session_id');
            $table->index('user_id');
        });
    }

    public function down() {
        Schema::dropIfExists('calibraciones');
    }
};