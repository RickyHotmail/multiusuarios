<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleIngresoBodegaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ingreso_bodega', function (Blueprint $table) {
            $table->id('detalle_ingreso_id');
            $table->double('detalle_ingreso_cantidad',19,4);
            $table->double('detalle_ingreso_precio_unitario',19,4);
            $table->double('detalle_ingreso_total',19,4);
            $table->text('detalle_ingreso_descripcion');
            $table->string('detalle_ingreso_estado');
            $table->bigInteger('cabecera_ingreso_id');
            $table->foreign('cabecera_ingreso_id')->references('cabecera_ingreso_id')->on('cabecera_ingreso_bodega');
            $table->bigInteger('movimiento_id')->nullable();
            $table->foreign('movimiento_id')->references('movimiento_id')->on('movimiento_producto');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
            $table->bigInteger('centro_consumo_id');
            $table->foreign('centro_consumo_id')->references('centro_consumo_id')->on('centro_consumo');
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
        Schema::dropIfExists('detalle_ingreso_bodega');
    }
}
