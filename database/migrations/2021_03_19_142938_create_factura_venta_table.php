<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturaVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura_venta', function (Blueprint $table) {
            $table->id('factura_id');
            $table->string('factura_numero')->unique();
            $table->string('factura_serie');
            $table->bigInteger('factura_secuencial');
            $table->date('factura_fecha');
            $table->string('factura_lugar');
            $table->string('factura_tipo_pago');
            $table->bigInteger('factura_dias_plazo');
            $table->date('factura_fecha_pago');
            $table->double('factura_subtotal');
            $table->double('factura_descuento');
            $table->double('factura_tarifa0');
            $table->double('factura_tarifa12');
            $table->double('factura_iva');
            $table->double('factura_total');
            $table->text('factura_comentario');
            $table->float('factura_porcentaje_iva');
            $table->string('factura_emision');
            $table->string('factura_ambiente');
            $table->string('factura_autorizacion');
            $table->string('factura_xml_nombre')->nullable();
            $table->string('factura_xml_estado')->nullable();
            $table->text('factura_xml_mensaje')->nullable();
            $table->text('factura_xml_respuestaSRI')->nullable();
            $table->date('factura_xml_fecha')->nullable();
            $table->time('factura_xml_hora')->nullable();
            $table->string('factura_estado');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega'); 
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente'); 
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario'); 
            $table->bigInteger('forma_pago_id');
            $table->foreign('forma_pago_id')->references('forma_pago_id')->on('forma_pago'); 
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento'); 
            $table->bigInteger('cuenta_id')->nullable();
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta_cobrar'); 
            $table->bigInteger('vendedor_id')->nullable();
            $table->foreign('vendedor_id')->references('vendedor_id')->on('vendedor'); 
            $table->bigInteger('documento_anulado_id')->nullable();
            $table->foreign('documento_anulado_id')->references('documento_anulado_id')->on('documento_anulado'); 
            $table->bigInteger('diario_costo_id')->nullable();
            $table->foreign('diario_costo_id')->references('diario_id')->on('diario'); 
            $table->bigInteger('arqueo_id')->nullable();
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');            
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
        Schema::dropIfExists('factura_venta');
    }
}
