<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvinciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provincia', function (Blueprint $table) {
            $table->id('provincia_id');
            $table->string('provincia_nombre')->unique();
            $table->string('provincia_codigo')->unique();
            $table->string('provincia_estado');
            $table->integer('pais_id');
            $table->foreign('pais_id')->references('pais_id')->on('pais');            
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
        Schema::dropIfExists('provincia');
    }
}
