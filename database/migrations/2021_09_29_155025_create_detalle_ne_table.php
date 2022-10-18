<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleNeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ne', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->double('detalle_cantidad',19,2);
            $table->double('detalle_precio_unitario',19,2);
            $table->double('detalle_total',19,2);
            $table->string('detalle_estado');
            $table->bigInteger('nt_id');
            $table->foreign('nt_id')->references('nt_id')->on('nota_entrega');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
            $table->bigInteger('movimiento_id');
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
        Schema::dropIfExists('detalle_ne');
    }
}
