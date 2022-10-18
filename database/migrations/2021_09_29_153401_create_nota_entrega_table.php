<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaEntregaTable extends Migration
{
    /**
     * Run the mintations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nota_entrega', function (Blueprint $table) {
            $table->id('nt_id');
            $table->string('nt_numero')->unique();
            $table->string('nt_serie');
            $table->float('nt_secuencial');
            $table->date('nt_fecha');
            $table->string('nt_total');
            $table->string('nt_tipo_pago');
            $table->string('nt_comentario');
            $table->string('nt_estado');
            $table->bigInteger('diario_costo_id')->nullable();
            $table->foreign('diario_costo_id')->references('diario_id')->on('diario');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');
            $table->bigInteger('cuenta_id')->nullable();
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta_cobrar');
            $table->bigInteger('arqueo_id')->nullable();
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');
            $table->timestamps();
        });
    }

    /**
     * Reverse the mintations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nota_entrega');
    }
}
