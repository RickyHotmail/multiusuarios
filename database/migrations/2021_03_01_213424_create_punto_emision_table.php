<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePuntoEmisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punto_emision', function (Blueprint $table) {
            $table->id('punto_id');            
            $table->string('punto_serie');           
            $table->string('punto_descripcion');
            $table->string('punto_estado');
            $table->integer('sucursal_id');
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
        Schema::dropIfExists('punto_emision');
    }
}
