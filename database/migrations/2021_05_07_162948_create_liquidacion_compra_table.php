<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiquidacionCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liquidacion_compra', function (Blueprint $table) {
            $table->id('lc_id');
            $table->string('lc_numero')->unique();
            $table->string('lc_serie');
            $table->bigInteger('lc_secuencial');
            $table->date('lc_fecha');
            $table->double('lc_subtotal');
            $table->double('lc_descuento');
            $table->double('lc_tarifa0');
            $table->double('lc_tarifa12');
            $table->double('lc_iva');
            $table->double('lc_total');
            $table->double('lc_ivaB');
            $table->double('lc_ivaS');
            $table->bigInteger('lc_dias_plazo');
            $table->text('lc_comentario');
            $table->string('lc_tipo_pago');
            $table->float('lc_porcentaje_iva');
            $table->string('lc_emision');
            $table->string('lc_ambiente');
            $table->string('lc_autorizacion');
            $table->string('lc_xml_nombre')->nullable();
            $table->string('lc_xml_estado')->nullable();
            $table->text('lc_xml_mensaje')->nullable();
            $table->text('lc_xml_respuestaSRI')->nullable();
            $table->date('lc_xml_fecha')->nullable();
            $table->time('lc_xml_hora')->nullable();
            $table->string('lc_estado');
            $table->bigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedor');
            $table->bigInteger('sustento_id');
            $table->foreign('sustento_id')->references('sustento_id')->on('sustento_tributario');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('forma_pago_id')->nullable();
            $table->foreign('forma_pago_id')->references('forma_pago_id')->on('forma_pago');
            $table->bigInteger('cuenta_id')->nullable();
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta_pagar');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');
            $table->bigInteger('documento_anulado_id')->nullable();
            $table->foreign('documento_anulado_id')->references('documento_anulado_id')->on('documento_anulado');
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
        Schema::dropIfExists('liquidacion_compra');
    }
}
