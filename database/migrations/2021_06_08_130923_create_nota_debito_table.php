<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaDebitoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nota_debito', function (Blueprint $table) {
            $table->id('nd_id');
            $table->string('nd_numero')->unique();
            $table->string('nd_serie');
            $table->bigInteger('nd_secuencial');
            $table->date('nd_fecha');
            $table->string('nd_tipo_pago');
            $table->bigInteger('nd_dias_plazo');
            $table->date('nd_fecha_pago');
            $table->double('nd_subtotal');
            $table->double('nd_descuento');
            $table->double('nd_tarifa0');
            $table->double('nd_tarifa12');
            $table->double('nd_iva');
            $table->double('nd_total');
            $table->text('nd_motivo');
            $table->text('nd_comentario');
            $table->float('nd_porcentaje_iva');
            $table->string('nd_emision');
            $table->string('nd_ambiente');
            $table->string('nd_autorizacion');
            $table->string('nd_xml_nombre')->nullable();
            $table->string('nd_xml_estado')->nullable();
            $table->text('nd_xml_mensaje')->nullable();
            $table->text('nd_xml_respuestaSRI')->nullable();
            $table->date('nd_xml_fecha')->nullable();
            $table->time('nd_xml_hora')->nullable();
            $table->string('nd_estado');
            $table->bigInteger('forma_pago_id');
            $table->foreign('forma_pago_id')->references('forma_pago_id')->on('forma_pago'); 
            $table->bigInteger('factura_id');
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta'); 
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario'); 
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento'); 
            $table->bigInteger('cuenta_id')->nullable();
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta_cobrar');  
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
        Schema::dropIfExists('nota_debito');
    }
}
