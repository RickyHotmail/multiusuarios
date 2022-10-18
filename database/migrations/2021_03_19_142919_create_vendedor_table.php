<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendedor', function (Blueprint $table) {
            $table->id('vendedor_id');
            $table->string('vendedor_cedula')->unique();
            $table->string('vendedor_nombre');
            $table->string('vendedor_direccion');
            $table->string('vendedor_telefono');
            $table->string('vendedor_email');
            $table->double('vendedor_comision_porcentaje',8,4);
            $table->date('vendedor_fecha_ingreso');
            $table->date('vendedor_fecha_salida')->nullable();
            $table->string('vendedor_estado');
            $table->bigInteger('zona_id');
            $table->foreign('zona_id')->references('zona_id')->on('zona');
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
        Schema::dropIfExists('vendedor');
    }
}
