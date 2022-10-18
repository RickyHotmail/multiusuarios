<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleOrdenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_orden', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->double('detalle_cantidad',19,4);
            $table->double('detalle_precio_unitario',19,4);
            $table->double('detalle_descuento',19,4);
            $table->double('detalle_iva',19,4);
            $table->double('detalle_total',19,4);
            $table->text('detalle_descripcion');
            $table->string('detalle_estado');
            $table->bigInteger('orden_id');
            $table->foreign('orden_id')->references('orden_id')->on('orden_despacho');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
            $table->bigInteger('movimiento_id')->nullable();;
            $table->foreign('movimiento_id')->references('movimiento_id')->on('movimiento_producto');
            $table->bigInteger('movimientop_id')->nullable();;
            $table->foreign('movimientop_id')->references('movimiento_id')->on('movimiento_prestamo_producto');
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
        Schema::dropIfExists('detalle_orden_');
    }
}
