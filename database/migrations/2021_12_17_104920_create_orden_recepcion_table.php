<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenRecepcionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_recepcion', function (Blueprint $table) {
           
            $table->id('ordenr_id');
            $table->string('ordenr_numero')->unique();
            $table->string('ordenr_serie');
            $table->float('ordenr_secuencial');
            $table->date('ordenr_fecha');
            $table->string('ordenr_guia')->nullable();
            $table->string('ordenr_observacion');
            $table->string('ordenr_estado');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega');
            $table->bigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedor');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');
            $table->bigInteger('transaccion_id')->nullable();
            $table->foreign('transaccion_id')->references('transaccion_id')->on('transaccion_compra');
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
        Schema::dropIfExists('orden_recepcion');
    }
}
