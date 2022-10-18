<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoIdentificacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_identificacion', function (Blueprint $table) {
            $table->id('tipo_identificacion_id');
            $table->string('tipo_identificacion_nombre')->unique();
            $table->string('tipo_identificacion_codigo')->unique();
            $table->string('tipo_identificacion_estado');            
            $table->bigInteger('empresa_id');
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
        Schema::dropIfExists('tipo_identificacion');
    }
}
