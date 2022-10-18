<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diario', function (Blueprint $table) {
            $table->id('diario_id');
            $table->string('diario_codigo')->unique();
            $table->date('diario_fecha');
            $table->text('diario_referencia');  
            $table->string('diario_tipo_documento');
            $table->string('diario_numero_documento');
            $table->string('diario_beneficiario');
            $table->string('diario_tipo');
            $table->bigInteger('diario_secuencial');
            $table->string('diario_mes');
            $table->string('diario_ano');
            $table->text('diario_comentario');
            $table->string('diario_cierre');
            $table->string('diario_estado');
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->bigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal'); 
            $table->bigInteger('diario_cierre_id')->nullable();
            $table->foreign('diario_cierre_id')->references('diario_id')->on('diario'); 
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
        Schema::dropIfExists('diario');
    }
}
