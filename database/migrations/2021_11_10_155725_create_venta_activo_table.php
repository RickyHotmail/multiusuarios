<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentaActivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venta_activo', function (Blueprint $table) {
            $table->id('venta_id');
            $table->string('venta_fecha');
            $table->string('venta_descripcion');
            $table->float('venta_monto');
            $table->bigInteger('activo_id');
            $table->foreign('activo_id')->references('activo_id')->on('activo_fijo');
            $table->string('venta_estado');
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
        Schema::dropIfExists('venta_activo');
    }
}
