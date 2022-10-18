<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabeceraIngresoBodegaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cabecera_ingreso_bodega', function (Blueprint $table) {
            $table->id('cabecera_ingreso_id');
            $table->string('cabecera_ingreso_numero')->unique();
            $table->string('cabecera_ingreso_serie');
            $table->float('cabecera_ingreso_secuencial');
            $table->date('cabecera_ingreso_fecha');       
            $table->string('cabecera_ingreso_motivo');
            $table->string('cabecera_ingreso_pago');
            $table->float('cabecera_ingreso_plazo');        
            $table->double('cabecera_ingreso_total',19,4);
            $table->text('cabecera_ingreso_comentario');
            $table->string('cabecera_ingreso_estado');
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
            $table->bigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedor');
            $table->bigInteger('cuenta_id')->nullable();;
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta_pagar');
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
        Schema::dropIfExists('cabecera_ingreso_bodega');
    }
}
