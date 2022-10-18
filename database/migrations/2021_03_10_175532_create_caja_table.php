<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->id('caja_id');
            $table->string('caja_nombre');
            $table->string('caja_estado');            
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->bigInteger('cuenta_id')->nullable();
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta');
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
        Schema::dropIfExists('caja');
    }
}
