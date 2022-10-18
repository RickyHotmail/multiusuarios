<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePrestamoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_prestamo', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->date('detalle_fecha');
            $table->double('detalle_interes');
            $table->double('detalle_valor_interes');
            $table->double('detalle_total');
            $table->double('detalle_dias');
            $table->string('detalle_estado');
            $table->bigInteger('prestamo_id');
            $table->foreign('prestamo_id')->references('prestamo_id')->on('prestamo_banco');
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
        Schema::dropIfExists('detalle_prestamo');
    }
}
