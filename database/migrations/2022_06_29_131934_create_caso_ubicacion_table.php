<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasoUbicacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caso_ubicacion', function (Blueprint $table) {
            $table->id('ubicacion_id');
            $table->double('ubicacion_latitud');
            $table->double('longitud');
            $table->string('ubicacion_ciudad', 100);
            $table->integer('caso_id');
            $table->foreign('caso_id')->references('caso_id')->on('caso');
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
        Schema::dropIfExists('caso_ubicacion');
    }
}
