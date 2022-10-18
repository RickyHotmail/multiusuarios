<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuiaRemisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guia_remision', function (Blueprint $table) {
            $table->id('gr_id');
            $table->string('gr_numero')->unique();
            $table->string('gr_serie');
            $table->float('gr_secuencial');
            $table->date('gr_fecha');
            $table->date('gr_fecha_inicio');
            $table->date('gr_fecha_fin');
            $table->string('gr_punto_partida');
            $table->string('gr_punto_destino');
            $table->string('gr_ruta');
            $table->string('gr_placa');
            $table->string('gr_motivo');
            $table->text('gr_comentario');
            $table->string('gr_doc_aduanero');
            $table->string('gr_emision');
            $table->string('gr_gabarra');
            $table->string('gr_ambiente');
            $table->string('gr_autorizacion');
            $table->string('gr_xml_nombre')->nullable();
            $table->string('gr_xml_estado')->nullable();
            $table->string('gr_xml_mensaje')->nullable();
            $table->text('gr_xml_respuestaSRI')->nullable();
            $table->date('gr_xml_fecha')->nullable();;
            $table->time('gr_xml_hora')->nullable();;
            $table->string('gr_estado');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta')->nullable();
            $table->bigInteger('transportista_id');
            $table->foreign('transportista_id')->references('transportista_id')->on('transportista');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega');
            $table->bigInteger('documento_anulado_id')->nullable();
            $table->foreign('documento_anulado_id')->references('documento_anulado_id')->on('documento_anulado')->nullable();
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
        Schema::dropIfExists('guia_remision');
    }
}
