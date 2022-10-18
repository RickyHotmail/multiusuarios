<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaPagarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_pagar', function (Blueprint $table) {
            $table->id('cuenta_id');
            $table->string('cuenta_descripcion');
            $table->string('cuenta_tipo');
            $table->date('cuenta_fecha');
            $table->date('cuenta_fecha_inicio');
            $table->date('cuenta_fecha_fin');
            $table->double('cuenta_monto');
            $table->double('cuenta_saldo');
            $table->double('cuenta_valor_factura');
            $table->string('cuenta_estado');
            $table->bigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedor');
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
        Schema::dropIfExists('cuenta_pagar');
    }
}
