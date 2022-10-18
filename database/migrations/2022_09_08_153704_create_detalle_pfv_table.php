<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePfvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_pfv', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->double('detalle_cantidad',8,4);
            $table->double('detalle_precio_unitario',8,4);
            $table->double('detalle_descuento',8,4);
            $table->double('detalle_iva',8,4);
            $table->double('detalle_total',8,4);
            $table->text('detalle_descripcion');
            $table->string('detalle_estado');
            $table->bigInteger('prefactura_id');
            $table->foreign('prefactura_id')->references('prefactura_id')->on('prefactura_venta');
            $table->bigInteger('gr_id');
            $table->foreign('gr_id')->references('gr_id')->on('guia_remision');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
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
        Schema::dropIfExists('detalle_pfv');
    }
}
