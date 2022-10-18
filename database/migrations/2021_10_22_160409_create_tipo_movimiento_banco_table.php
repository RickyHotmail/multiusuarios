<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoMovimientoBancoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_movimiento_banco', function (Blueprint $table) {
            $table->id('tipo_id');
            $table->string('tipo_nombre');
            $table->string('tipo_movimiento');
            $table->string('tipo_estado');
            $table->integer('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->integer('cuenta_id')->nullable();
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta');  
            $table->integer('sucursal_id')->nullable();
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
        Schema::dropIfExists('tipo_movimiento_banco');
    }
}
