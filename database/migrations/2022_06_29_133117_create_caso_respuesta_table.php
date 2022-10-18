<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasoRespuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caso_respuesta', function (Blueprint $table) {
            $table->id('caso_respuesta_id');
            $table->date('caso_respuesta_pregunta');
            $table->integer('caso_respuesta_valor');
            $table->integer('caso_respuesta_estado');
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
        Schema::dropIfExists('caso_respuesta');
    }
}
