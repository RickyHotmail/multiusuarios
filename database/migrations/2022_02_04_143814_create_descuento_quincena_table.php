<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescuentoQuincenaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descuento_quincena', function (Blueprint $table) {
            $table->id('descuento_id');
            $table->date('descuento_fecha');
            $table->string('descuento_descripcion');
            $table->double('descuento_valor');
            $table->string('descuento_estado');
            $table->bigInteger('quincena_id');
            $table->foreign('quincena_id')->references('quincena_id')->on('quincena');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('cabecera_rol_cm_id')->nullable();
            $table->foreign('cabecera_rol_cm_id')->references('cabecera_rol_id')->on('cabecera_rol_cm');
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
        Schema::dropIfExists('descuento_quincena');
    }
}
