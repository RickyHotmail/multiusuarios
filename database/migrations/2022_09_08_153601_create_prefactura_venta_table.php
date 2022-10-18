<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrefacturaVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prefactura_venta', function (Blueprint $table) {
            $table->id('prefactura_id');
            $table->string('prefactura_numero')->unique();
            $table->string('prefactura_aguaje');
            $table->string('prefactura_tipo');
            $table->string('prefactura_serie');
            $table->bigInteger('prefactura_secuencial');
            $table->date('prefactura_fecha');
            $table->string('prefactura_lugar');
            $table->string('prefactura_tipo_pago');
            $table->bigInteger('prefactura_dias_plazo');
            $table->date('prefactura_fecha_pago');
            $table->double('prefactura_subtotal');
            $table->double('prefactura_descuento');
            $table->double('prefactura_tarifa0');
            $table->double('prefactura_tarifa12');
            $table->double('prefactura_iva');
            $table->double('prefactura_total');
            $table->double('prefactura_total_sacos');
            $table->text('prefactura_comentario');
            $table->float('prefactura_porcentaje_iva');
            $table->string('prefactura_estado');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega'); 
            $table->bigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta'); 
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente'); 
            $table->bigInteger('forma_pago_id');
            $table->foreign('forma_pago_id')->references('forma_pago_id')->on('forma_pago'); 
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento'); 

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
        Schema::dropIfExists('prefactura_venta');
    }
}
