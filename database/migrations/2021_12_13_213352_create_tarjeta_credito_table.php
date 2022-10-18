<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarjetaCreditoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarjeta_credito', function (Blueprint $table) {
            $table->id('tarjeta_id');
            $table->string('tarjeta_nombre');
            $table->string('tarjeta_estado');
            $table->bigInteger('cuenta_id');
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta'); 
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
        Schema::dropIfExists('tarjeta_credito');
    }
}
