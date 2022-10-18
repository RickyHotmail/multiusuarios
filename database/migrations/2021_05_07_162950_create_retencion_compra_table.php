<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetencionCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retencion_compra', function (Blueprint $table) {
            $table->id('retencion_id');
            $table->date('retencion_fecha');
            $table->string('retencion_numero')->unique();
            $table->string('retencion_serie');
            $table->bigInteger('retencion_secuencial');
            $table->string('retencion_emision');
            $table->string('retencion_ambiente');
            $table->string('retencion_autorizacion');
            $table->string('retencion_xml_nombre')->nullable();
            $table->string('retencion_xml_estado')->nullable();
            $table->text('retencion_xml_mensaje')->nullable();
            $table->text('retencion_xml_respuestaSRI')->nullable();
            $table->date('retencion_xml_fecha')->nullable();
            $table->time('retencion_xml_hora')->nullable();
            $table->string('retencion_estado');
            $table->bigInteger('transaccion_id')->nullable();
            $table->foreign('transaccion_id')->references('transaccion_id')->on('transaccion_compra');
            $table->bigInteger('lc_id')->nullable();
            $table->foreign('lc_id')->references('lc_id')->on('liquidacion_compra');
            $table->bigInteger('documento_anulado_id')->nullable();
            $table->foreign('documento_anulado_id')->references('documento_anulado_id')->on('documento_anulado');
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
        Schema::dropIfExists('retencion_compra');
    }
}
