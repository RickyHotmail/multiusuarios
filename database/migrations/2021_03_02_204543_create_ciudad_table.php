<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCiudadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ciudad', function (Blueprint $table) {
            $table->id('ciudad_id');            
            $table->string('ciudad_codigo')->unique();           
            $table->string('ciudad_nombre')->unique();
            $table->string('ciudad_estado');
            $table->integer('provincia_id');
            $table->foreign('provincia_id')->references('provincia_id')->on('provincia');  
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
        Schema::dropIfExists('ciudad');
    }
}
