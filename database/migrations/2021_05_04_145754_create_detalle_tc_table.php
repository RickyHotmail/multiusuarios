<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleTcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_tc', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->double('detalle_cantidad');
            $table->double('detalle_precio_unitario');
            $table->double('detalle_descuento');
            $table->double('detalle_iva');
            $table->double('detalle_total');
            $table->text('detalle_descripcion');
            $table->string('detalle_estado');
            $table->bigInteger('transaccion_id');
            $table->foreign('transaccion_id')->references('transaccion_id')->on('transaccion_compra'); 
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto'); 
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega'); 
            $table->bigInteger('centro_consumo_id');
            $table->foreign('centro_consumo_id')->references('centro_consumo_id')->on('centro_consumo'); 
            $table->bigInteger('movimiento_id')->nullable();
            $table->foreign('movimiento_id')->references('movimiento_id')->on('movimiento_producto'); 
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
        Schema::dropIfExists('detalle_tc');
    }
}
