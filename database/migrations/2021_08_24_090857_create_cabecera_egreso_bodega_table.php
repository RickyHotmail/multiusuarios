<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabeceraEgresoBodegaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cabecera_egreso_bodega', function (Blueprint $table) {
            $table->id('cabecera_egreso_id');
            $table->string('cabecera_egreso_numero')->unique();
            $table->string('cabecera_egreso_serie');
            $table->float('cabecera_egreso_secuencial');
            $table->date('cabecera_egreso_fecha');       
            $table->string('cabecera_egreso_destino');
            $table->string('cabecera_egreso_destinatario');
            $table->string('cabecera_egreso_motivo');
            $table->double('cabecera_egreso_total',19,4);
            $table->text('cabecera_egreso_comentario');
            $table->string('cabecera_egreso_estado');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_movimiento_inventario');
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
        Schema::dropIfExists('cabecera_egreso_bodega');
    }
}
