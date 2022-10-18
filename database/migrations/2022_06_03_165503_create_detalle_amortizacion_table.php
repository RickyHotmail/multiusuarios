<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleAmortizacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_amortizacion', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->date('detalle_fecha');
            $table->string('detalle_mes');
            $table->string('detalle_anio');
            $table->double('detalle_valor');
            $table->string('detalle_estado');
            $table->bigInteger('amortizacion_id');
            $table->foreign('amortizacion_id')->references('amortizacion_id')->on('amortizacion_seguros');
            $table->bigInteger('diario_id')->nullable();
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
        Schema::dropIfExists('detalle_amortizacion');
    }
}
