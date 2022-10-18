<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentroConsumoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centro_consumo', function (Blueprint $table) {
            $table->id('centro_consumo_id');
            $table->string('centro_consumo_nombre');
            $table->string('centro_consumo_descripcion');
            $table->date('centro_consumo_fecha_ingreso');           
            $table->string('centro_consumo_estado');
            $table->bigInteger('sustento_id');
            $table->foreign('sustento_id')->references('sustento_id')->on('sustento_tributario');               
            $table->bigInteger('empresa_id');
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
        Schema::dropIfExists('centro_consumo');
    }
}
