<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncuestaPreguntaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encuesta_pregunta', function (Blueprint $table) {
            $table->id('encuesta_pregunta_id');
            $table->integer('encuenta_pregunta_tipo');
            $table->string('encuesta_pregunta_descripcion');
            $table->integer('encuesta_pregunta_estado');
            $table->integer('encuesta_id');
            $table->foreign('encuesta_id')->references('encuesta_id')->on('encuesta');
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
        Schema::dropIfExists('encuesta_pregunta');
    }
}
