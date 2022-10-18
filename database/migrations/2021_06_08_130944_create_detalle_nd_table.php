<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleNdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_nd', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->double('detalle_cantidad');
            $table->double('detalle_precio_unitario');
            $table->double('detalle_descuento');
            $table->double('detalle_iva');
            $table->double('detalle_total');
            $table->string('detalle_estado');
            $table->bigInteger('nd_id');
            $table->foreign('nd_id')->references('nd_id')->on('nota_debito');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
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
        Schema::dropIfExists('detalle_nd');
    }
}
