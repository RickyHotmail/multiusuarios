<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagen', function (Blueprint $table) {
            $table->id('imagen_id');          
            $table->string('imagen_nombre')->nullable();
            $table->string('imagen_estado');
            $table->bigInteger('producto_id');
            $table->bigInteger('tipo_id');

            $table->foreign('producto_id')->references('producto_id')->on('producto'); 
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_imagen'); 

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
        Schema::dropIfExists('imagen');
    }
}
