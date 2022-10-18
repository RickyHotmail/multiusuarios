<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRangoDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rango_documento', function (Blueprint $table) {
            $table->id('rango_id');
            $table->string('rango_descripcion');            
            $table->bigInteger('rango_inicio');
            $table->bigInteger('rango_fin');                
            $table->date('rango_fecha_inicio');
            $table->date('rango_fecha_fin');            
            $table->string('rango_autorizacion');
            $table->string('rango_estado');            
            $table->bigInteger('tipo_comprobante_id');
            $table->foreign('tipo_comprobante_id')->references('tipo_comprobante_id')->on('tipo_comprobante');
            $table->bigInteger('punto_id');
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
        Schema::dropIfExists('rango_documento');
    }
}
