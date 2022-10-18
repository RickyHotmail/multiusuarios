<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenDespachoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_despacho', function (Blueprint $table) {
            $table->id('orden_id');
            $table->string('orden_numero')->unique();
            $table->string('orden_serie');
            $table->float('orden_secuencial');
            $table->date('orden_fecha');
            $table->string('orden_tipo_pago');
            $table->float('orden_dias_plazo');
            $table->date('orden_fecha_pago');
            $table->double('orden_subtotal',19,4);
            $table->double('orden_tarifa0',19,4);
            $table->double('orden_tarifa12',19,4);
            $table->double('orden_descuento',19,4);
            $table->double('orden_iva',19,4);
            $table->double('orden_total',19,4);
            $table->text('orden_comentario');
            $table->float('orden_porcentaje_iva');
            $table->string('orden_reserva');
            $table->string('orden_estado');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');
            $table->bigInteger('vendedor_id');
            $table->foreign('vendedor_id')->references('vendedor_id')->on('vendedor');
            $table->bigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta');
            $table->bigInteger('gr_id')->nullable();
            $table->foreign('gr_id')->references('gr_id')->on('guia_remision');
            
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
        Schema::dropIfExists('orden_despacho');
    }
}
