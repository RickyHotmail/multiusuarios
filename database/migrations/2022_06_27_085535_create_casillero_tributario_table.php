<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasilleroTributarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casillero_tributario', function (Blueprint $table) {
            $table->id('casillero_id');
            $table->string('casillero_codigo');
            $table->string('casillero_descripcion');
            $table->string('casillero_tipo');
            $table->string('casillero_estado'); 
            $table->integer('empresa_id');
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
        Schema::dropIfExists('casillero_tributario');
    }
}
