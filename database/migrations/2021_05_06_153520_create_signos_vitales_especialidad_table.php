<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignosVitalesEspecialidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signos_vitales_especialidad', function (Blueprint $table) {
            $table->id('signose_id');
            $table->text('signose_nombre');
            $table->text('signose_tipo');
            $table->string('signose_medida');
            $table->string('signose_estado');                      
            $table->bigInteger('especialidad_id');
            $table->foreign('especialidad_id')->references('especialidad_id')->on('especialidad');
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
        Schema::dropIfExists('signos_vitales_especialidad');
    }
}
