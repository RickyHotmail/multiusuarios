<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTareaProgramadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarea_programada', function (Blueprint $table) {
            $table->id("tarea_id");

            $table->string("tarea_nombre_proceso");
            $table->integer("tarea_tipo_tiempo");
            $table->string("tarea_hora_ejecucion", 5)->nullable();
            $table->integer("tarea_estado");
            $table->integer('empresa_id');

            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tarea_programada');
    }
}
