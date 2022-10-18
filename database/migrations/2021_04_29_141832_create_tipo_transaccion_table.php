<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoTransaccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_transaccion', function (Blueprint $table) {
            $table->id('tipo_transaccion_id');
            $table->string('tipo_transaccion_nombre');
            $table->string('tipo_transaccion_codigo');
            $table->string('tipo_transaccion_estado');
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
        Schema::dropIfExists('tipo_transaccion');
    }
}
