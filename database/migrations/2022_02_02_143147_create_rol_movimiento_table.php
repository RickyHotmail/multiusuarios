<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolMovimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rol_movimiento', function (Blueprint $table) {
            $table->id('rol_movimiento_id'); 
            $table->float('rol_movimiento_valor'); 
            $table->float('rol_movimiento_valor'); 
            $table->string('rol_movimiento_mes');
            $table->string('rol_movimiento_anio');
            $table->string('rol_movimiento_tipo');
            $table->string('rol_movimiento_estado');         
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado'); 
            $table->bigInteger('rubro_id');
            $table->foreign('rubro_id')->references('rubro_id')->on('rubro'); 
            $table->bigInteger('cabecera_rol_cm_id')->nullable();
            $table->foreign('cabecera_rol_cm_id')->references('cabecera_rol_id')->on('cabecera_rol_cm'); 
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
        Schema::dropIfExists('rol_movimiento');
    }
}
