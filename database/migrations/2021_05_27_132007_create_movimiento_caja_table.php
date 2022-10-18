<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_caja', function (Blueprint $table) {
            $table->id('movimiento_id');          
            $table->date('movimiento_fecha');
            $table->time('movimiento_hora');
            $table->string('movimiento_tipo');
            $table->string('movimiento_descripcion');
            $table->double('movimiento_valor',19,4);
            $table->string('movimiento_documento');
            $table->string('movimiento_numero_documento');
            $table->string('movimiento_estado');
            $table->bigInteger('arqueo_id');
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');         
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
        Schema::dropIfExists('movimiento_caja');
    }
}
