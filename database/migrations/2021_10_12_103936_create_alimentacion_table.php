<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlimentacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alimentacion', function (Blueprint $table) {
            $table->id('alimentacion_id');
            $table->string('alimentacion_fecha');
            $table->float('alimentacion_valor');
            $table->string('alimentacion_estado');
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
            $table->bigInteger('cabecera_rol_id')->nullable();
            $table->foreign('cabecera_rol_id')->references('cabecera_rol_id')->on('cabecera_rol');
            $table->bigInteger('cabecera_rol_cm_id')->nullable();
            $table->foreign('cabecera_rol_cm_id')->references('cabecera_rol_id')->on('cabecera_rol_cm');
            $table->bigInteger('transaccion_id');
            $table->foreign('transaccion_id')->references('transaccion_id')->on('transaccion_compra');
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
        Schema::dropIfExists('alimentacion');
    }
}
