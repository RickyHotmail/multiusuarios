<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescuentoAnticipoEmpleadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descuento_anticipo_empleado', function (Blueprint $table) {
            $table->id('descuento_id');
            $table->date('descuento_fecha');
            $table->text('descuento_descripcion');
            $table->double('descuento_valor');
            $table->string('descuento_estado');
            $table->bigInteger('anticipo_id');
            $table->foreign('anticipo_id')->references('anticipo_id')->on('anticipo_empleado');
            $table->bigInteger('cabecera_rol_id')->nullable();
            $table->foreign('cabecera_rol_id')->references('cabecera_rol_id')->on('cabecera_rol');
            $table->bigInteger('cabecera_rol_cm_id')->nullable();
            $table->foreign('cabecera_rol_cm_id')->references('cabecera_rol_id')->on('cabecera_rol_cm');
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
        Schema::dropIfExists('descuento_anticipo_empleado');
    }
}
