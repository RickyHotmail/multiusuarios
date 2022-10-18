<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaccionIdentificacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaccion_identificacion', function (Blueprint $table) {
            $table->id('transaccion_id');
            $table->string('transaccion_codigo');
            $table->string('transaccion_estado');
            $table->bigInteger('tipo_transaccion_id');            
            $table->foreign('tipo_transaccion_id')->references('tipo_transaccion_id')->on('tipo_transaccion');
            $table->bigInteger('tipo_identificacion_id');            
            $table->foreign('tipo_identificacion_id')->references('tipo_identificacion_id')->on('tipo_identificacion');
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
        Schema::dropIfExists('transaccion_identificacion');
    }
}
