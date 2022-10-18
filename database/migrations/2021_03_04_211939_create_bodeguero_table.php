<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegueroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodeguero', function (Blueprint $table) {
            $table->id('bodeguero_id');
            $table->string('bodeguero_cedula')->unique();
            $table->string('bodeguero_nombre');
            $table->string('bodeguero_direccion');
            $table->string('bodeguero_telefono');
            $table->string('bodeguero_email');
            $table->date('bodeguero_fecha_ingreso');
            $table->date('bodeguero_fecha_salida')->nullable();
            $table->string('bodeguero_estado');
            $table->integer('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega');  
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
        Schema::dropIfExists('bodeguero');
    }
}
