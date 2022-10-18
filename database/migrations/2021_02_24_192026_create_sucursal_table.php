<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSucursalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sucursal', function (Blueprint $table) {
            $table->id('sucursal_id');
            $table->string('sucursal_nombre');
            $table->string('sucursal_codigo');
            $table->string('sucursal_direccion');            
            $table->string('sucursal_telefono')->nullable();           
            $table->integer('empresa_id');
            $table->string('sucursal_estado');
            
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
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
        Schema::dropIfExists('sucursal');
    }
}
