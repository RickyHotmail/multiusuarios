<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingreso_caja', function (Blueprint $table) {
            $table->id('ingreso_id');
            $table->string('ingreso_numero')->unique();
            $table->string('ingreso_serie');
            $table->float('ingreso_secuencial');            
            $table->date('ingreso_fecha');       
            $table->string('ingreso_tipo');     
            $table->double('ingreso_valor');
            $table->string('ingreso_descripcion');
            $table->string('ingreso_beneficiario');
            $table->bigInteger('arqueo_id')->nullable();
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento'); 
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_movimiento_caja');
            $table->string('ingreso_estado');
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
        Schema::dropIfExists('ingreso_caja');
    }
}
