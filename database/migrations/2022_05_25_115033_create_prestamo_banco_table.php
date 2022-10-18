<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrestamoBancoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prestamo_banco', function (Blueprint $table) {
            $table->id('prestamo_id');
            $table->date('prestamo_inicio');
            $table->date('prestamo_fin');
            $table->double('prestamo_monto');
            $table->double('prestamo_interes');
            $table->double('prestamo_plazo');
            $table->double('prestamo_total_interes');
            $table->double('prestamo_pago_total');
            $table->text('prestamo_observacion');
            $table->string('prestamo_estado');
            $table->bigInteger('cuenta_debe');
            $table->foreign('cuenta_debe')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('cuenta_haber');
            $table->foreign('cuenta_haber')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('banco_id');
            $table->foreign('banco_id')->references('banco_id')->on('banco');
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
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
        Schema::dropIfExists('prestamo_banco');
    }
}
