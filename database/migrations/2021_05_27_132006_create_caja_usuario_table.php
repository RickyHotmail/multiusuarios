<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja_usuario', function (Blueprint $table) {
            $table->id('cajau_id');         
            $table->bigInteger('caja_id');
            $table->foreign('caja_id')->references('caja_id')->on('caja'); 
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users'); 
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
        Schema::dropIfExists('caja_usuario');
    }
}
