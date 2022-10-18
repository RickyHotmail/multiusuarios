<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSustentoTributarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sustento_tributario', function (Blueprint $table) {
            $table->id('sustento_id'); 
            $table->string('sustento_nombre')->unique();
            $table->string('sustento_codigo')->unique(); 
            $table->string('sustento_credito');
            $table->string('sustento_venta12');
            $table->string('sustento_venta0');
            $table->string('sustento_compra12');
            $table->string('sustento_compra0');
            $table->string('sustento_estado');
            $table->integer('empresa_id');
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
        Schema::dropIfExists('sustento_tributario');
    }
}
