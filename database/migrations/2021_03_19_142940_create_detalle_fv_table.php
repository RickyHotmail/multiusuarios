<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleFvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_fv', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->double('detalle_cantidad',8,4);
            $table->double('detalle_precio_unitario',8,4);
            $table->double('detalle_descuento',8,4);
            $table->double('detalle_iva',8,4);
            $table->double('detalle_total',8,4);
            $table->text('detalle_descripcion');
            $table->string('detalle_estado');
            $table->bigInteger('factura_id');
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta');
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
        Schema::dropIfExists('detalle_fv');
    }
}
