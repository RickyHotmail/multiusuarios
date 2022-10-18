<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega', function (Blueprint $table) {
            $table->id('bodega_id');
            $table->string('bodega_nombre');
            $table->string('bodega_descripcion'); 
            $table->string('bodega_direccion');
            $table->string('bodega_telefono');
            $table->string('bodega_fax'); 
            $table->string('bodega_estado');
            $table->integer('ciudad_id');
            $table->integer('sucursal_id');
            $table->foreign('ciudad_id')->references('ciudad_id')->on('ciudad'); 
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');  
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
        Schema::dropIfExists('bodega');
    }
}
