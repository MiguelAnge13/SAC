<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventLogsTable extends Migration
{
    public function up()
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // usuario que realizó la acción (nullable = acciones del sistema)
            $table->string('action', 60); // ej: login, logout, user.create, code.update, calibracion.create...
            $table->string('entity')->nullable(); // ej: 'usuario', 'codigo', 'proyecto', 'calibracion', 'libreria', 'microcontrolador'
            $table->unsignedBigInteger('entity_id')->nullable()->index(); // id de la entidad si aplica
            $table->text('description')->nullable(); // texto libre o JSON con detalles
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('meta')->nullable(); // datos estructurados: {old:..., new:...}
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_logs');
    }
}
