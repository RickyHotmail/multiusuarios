<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetencionVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retencion_venta', function (Blueprint $table) {
            $table->id('retencion_id');
            $table->date('retencion_fecha');
            $table->string('retencion_numero');
            $table->string('retencion_serie');
            $table->bigInteger('retencion_secuencial');
            $table->string('retencion_emision');
            $table->string('retencion_estado');
            $table->bigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta');
            $table->bigInteger('nd_id')->nullable();
            $table->foreign('nd_id')->references('nd_id')->on('nota_debito');
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
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
        Schema::dropIfExists('retencion_venta');
    }
}
