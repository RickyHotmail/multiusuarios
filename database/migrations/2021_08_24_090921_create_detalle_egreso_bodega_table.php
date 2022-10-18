<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleEgresoBodegaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_egreso_bodega', function (Blueprint $table) {
            $table->id('detalle_egreso_id');
            $table->double('detalle_egreso_cantidad',19,4);
            $table->double('detalle_egreso_precio_unitario',19,4);
            $table->double('detalle_egreso_total',19,4);
            $table->text('detalle_egreso_descripcion');
            $table->string('detalle_egreso_estado');
            $table->bigInteger('cabecera_egreso_id');
            $table->foreign('cabecera_egreso_id')->references('cabecera_egreso_id')->on('cabecera_egreso_bodega');
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
        Schema::dropIfExists('detalle_egreso_bodega');
    }
}
