<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banco', function (Blueprint $table) {
            $table->id('banco_id');
            $table->string('banco_direccion');
            $table->string('banco_telefono');
            $table->string('banco_email');
            $table->string('banco_estado');           
            $table->bigInteger('banco_lista_id');
            $table->foreign('banco_lista_id')->references('banco_lista_id')->on('banco_lista');    
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
        Schema::dropIfExists('banco');
    }
}
