<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaltanteCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faltante_caja', function (Blueprint $table) {
            $table->id('faltante_id');
            $table->string('faltante_numero')->unique();
            $table->string('faltante_serie');
            $table->float('faltante_secuencial');
            $table->date('faltante_fecha');
            $table->string('faltante_observacion');
            $table->double('faltante_monto');
            $table->string('faltante_estado');          
            $table->bigInteger('arqueo_id');
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');            
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
        Schema::dropIfExists('faltante_caja');
    }
}
