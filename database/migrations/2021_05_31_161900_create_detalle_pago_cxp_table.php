<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePagoCxpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_pago_cxp', function (Blueprint $table) {
            $table->id('detalle_pago_id');
            $table->text('detalle_pago_descripcion');
            $table->double('detalle_pago_valor');
            $table->bigInteger('detalle_pago_cuota');
            $table->string('detalle_pago_estado');
            $table->bigInteger('cuenta_pagar_id');
            $table->foreign('cuenta_pagar_id')->references('cuenta_id')->on('cuenta_pagar');
            $table->bigInteger('pago_id');
            $table->foreign('pago_id')->references('pago_id')->on('pago_cxp');
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
        Schema::dropIfExists('detalle_pago_cxp');
    }
}
