<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControlDiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_dias', function (Blueprint $table) {
            $table->id('control_id');
            $table->string('control_serie');
            $table->string('control_numero')->unique();
            $table->float('control_secuencial');
            
            $table->float('control_normal');
            $table->float('control_decanso');
            $table->float('control_vacaciones');
            $table->float('control_permiso');
            $table->float('control_cosecha');
            $table->float('control_extra');
            $table->float('control_ausente');

            $table->string('control_mes');
            $table->string('control_ano', 4);
            $table->string('control_estado', 1);
            $table->date('control_fecha');
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');
            $table->bigInteger('cabecera_rol_cm_id')->nullable();
            $table->foreign('cabecera_rol_cm_id')->references('cabecera_rol_id')->on('cabecera_rol_cm');
            $table->bigInteger('cabecera_rol__id')->nullable();
            $table->foreign('cabecera_rol_id')->references('cabecera_rol_id')->on('cabecera_rol');
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
        Schema::dropIfExists('control_dias');
    }
}
