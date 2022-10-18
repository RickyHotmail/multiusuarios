<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturaMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura_movil', function (Blueprint $table) {
            $table->id('factura_id');
            $table->integer('factura_secuencia');
            $table->string('factura_serie', 15);
            $table->date('factura_fecha');
            $table->string('factura_lugar', 100);
            $table->string('factura_comentario', 300);
            $table->double('factura_total_0');
            $table->double('factura_total_iva');
            $table->double('factura_descuento');
            $table->double('factura_ice');
            $table->double('factura_tarifa_especial');
            $table->double('factura_irbpnr');
            $table->double('factura_total');

            $table->integer('factura_emision');
            $table->integer('factura_ambiente');
            $table->string('factura_autorizacion', 49);
            $table->string('factura_xml_respuesta_sri', 300);
            $table->dateTime('factura_xml_fecha', 300);
            $table->integer('factura_estado')->default(1);

            $table->integer('emisor_id');
            $table->foreign('emisor_id')->references('emisor_id')->on('emisor_movil');
            $table->integer('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente_movil');
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
        Schema::dropIfExists('factura_movil');
    }
}
