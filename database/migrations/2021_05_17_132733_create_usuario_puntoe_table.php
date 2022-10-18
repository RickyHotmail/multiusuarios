<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioPuntoeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_puntoe', function (Blueprint $table) {
            $table->id('usuarioP_id');
            $table->string('usuarioP_estado');
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');     
            $table->bigInteger('punto_id')->nullable();
            $table->foreign('punto_id')->references('punto_id')->on('punto_emision');     
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
        Schema::dropIfExists('usuario_puntoe');
    }
}
