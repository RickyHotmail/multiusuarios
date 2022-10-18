<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntidadAseguradoraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entidad_aseguradora', function (Blueprint $table) {
            $table->id('entidada_id');
            $table->string('entidada_estado');
            //datos foraneos
            $table->bigInteger('entidad_id');
            $table->foreign('entidad_id')->references('entidad_id')->on('entidad');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
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
        Schema::dropIfExists('entidad_aseguradora');
    }
}
