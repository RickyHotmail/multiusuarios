<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleExamenParticularTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_examen_particular', function (Blueprint $table) {
            $table->id('detalle_id');            
            $table->string('detalle_estado');
            $table->bigInteger('orden_id');
            $table->foreign('orden_id')->references('orden_id')->on('orden_examen_particular');  
            $table->bigInteger('examen_id');
            $table->foreign('examen_id')->references('examen_id')->on('examen'); 
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
        Schema::dropIfExists('detalle_examen_particular');
    }
}
