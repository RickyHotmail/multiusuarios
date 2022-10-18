<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmortizacionSegurosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amortizacion_seguros', function (Blueprint $table) {
            $table->id('amortizacion_id');
            $table->date('amortizacion_fecha');
            $table->double('amortizacion_periodo');
            $table->double('amortizacion_total');
            $table->double('amortizacion_pago_total');
            $table->text('amortizacion_observacion');
            $table->string('amortizacion_estado');
            $table->bigInteger('cuenta_debe');
            $table->foreign('cuenta_debe')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('transaccion_id');
            $table->foreign('transaccion_id')->references('transaccion_id')->on('transaccion_compra');
            $table->bigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');
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
        Schema::dropIfExists('amortizacion_seguros');
    }
}
