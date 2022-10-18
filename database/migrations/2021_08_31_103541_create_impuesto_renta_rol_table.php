<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImpuestoRentaRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('impuesto_renta_rol', function (Blueprint $table) {
            $table->id('impuestos_id');
            $table->float('impuesto_fraccion_basica');
            $table->float('impuesto_exceso_hasta');
            $table->float('impuesto_fraccion_excede');
            $table->float('impuesto_sobre_fraccion');
            $table->string('impuesto_estado');
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
        Schema::dropIfExists('impuesto_renta_rol');
    }
}
