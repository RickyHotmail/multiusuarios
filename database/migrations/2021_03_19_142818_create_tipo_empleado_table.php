<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoEmpleadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_empleado', function (Blueprint $table) {
            $table->id('tipo_id');
            $table->text('tipo_descripcion');
            $table->string('tipo_categoria');
            $table->string('tipo_estado');
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
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
        Schema::dropIfExists('tipo_empleado');
    }
}
