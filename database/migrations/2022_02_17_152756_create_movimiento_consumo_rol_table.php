<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoConsumoRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_consumo_rol', function (Blueprint $table) {
            $table->id('movimiento_id');      
            $table->date('movimiento_fecha'); 
            $table->float('movimiento_valor');
            $table->bigInteger('cabecera_rol_cm_id');
            $table->foreign('cabecera_rol_cm_id')->references('cabecera_rol_id')->on('cabecera_rol_cm');   
            $table->bigInteger('cabecera_rol_id');
            $table->foreign('cabecera_rol_id')->references('cabecera_rol_id')->on('cabecera_rol');   
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');              
            $table->bigInteger('rubro_id');
            $table->foreign('rubro_id')->references('rubro_id')->on('rubro');  
            $table->bigInteger('categoria_id');
            $table->foreign('categoria_id')->references('categoria_id')->on('categoria_rol');  
            $table->bigInteger('centro_consumo_id');
            $table->foreign('centro_consumo_id')->references('centro_consumo_id')->on('centro_consumo');  
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
        Schema::dropIfExists('movimiento_consumo_rol');
    }
}
