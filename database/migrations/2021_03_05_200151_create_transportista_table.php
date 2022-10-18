<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportistaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transportista', function (Blueprint $table) {
            $table->id('transportista_id');
            $table->string('transportista_cedula')->unique();
            $table->string('transportista_nombre');
            $table->string('transportista_placa');
            $table->string('transportista_embarcacion')->nullable();
            $table->string('transportista_estado');
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
        Schema::dropIfExists('transportista');
    }
}
