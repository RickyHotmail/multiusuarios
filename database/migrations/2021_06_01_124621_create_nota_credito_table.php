<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaCreditoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nota_credito', function (Blueprint $table) {
            $table->id('nc_id');
            $table->string('nc_numero')->unique();
            $table->string('nc_serie');
            $table->bigInteger('nc_secuencial');
            $table->date('nc_fecha');
            $table->double('nc_subtotal');
            $table->double('nc_descuento');
            $table->double('nc_tarifa0');
            $table->double('nc_tarifa12');
            $table->double('nc_iva');
            $table->double('nc_total');
            $table->text('nc_comentario');
            $table->float('nc_porcentaje_iva');
            $table->string('nc_emision');
            $table->string('nc_ambiente');
            $table->string('nc_autorizacion');
            $table->string('nc_xml_nombre')->nullable();
            $table->string('nc_xml_estado')->nullable();
            $table->text('nc_xml_mensaje')->nullable();
            $table->text('nc_xml_respuestaSRI')->nullable();
            $table->date('nc_xml_fecha')->nullable();
            $table->time('nc_xml_hora')->nullable();
            $table->string('nc_estado');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario'); 
            $table->bigInteger('factura_id');
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta'); 
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');  
            $table->bigInteger('documento_anulado_id')->nullable();
            $table->foreign('documento_anulado_id')->references('documento_anulado_id')->on('documento_anulado'); 
            $table->bigInteger('diario_costo_id')->nullable();
            $table->foreign('diario_costo_id')->references('diario_id')->on('diario');     
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
        Schema::dropIfExists('nota_credito');
    }
}
