<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoComprobanteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_comprobante', function (Blueprint $table) {           
            $table->id('tipo_comprobante_id');            
            $table->string('tipo_comprobante_codigo')->unique();           
            $table->string('tipo_comprobante_nombre');
            $table->string('tipo_comprobante_estado');
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
        Schema::dropIfExists('tipo_comprobante');
    }
}
