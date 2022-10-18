<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoImagenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_imagen', function (Blueprint $table) {
            $table->id('tipo_id');          
            $table->string('tipo_nombre');
            $table->string('tipo_estado');
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
        Schema::dropIfExists('tipo_imagen');
    }
}
