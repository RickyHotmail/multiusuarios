<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresoBancoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingreso_banco', function (Blueprint $table) {
            $table->id('ingreso_id');
            $table->string('ingreso_numero')->unique();
            $table->string('ingreso_serie');
            $table->float('ingreso_secuencial');
            $table->date('ingreso_fecha'); 
            $table->double('ingreso_valor');
            $table->string('ingreso_descripcion');
            $table->string('ingreso_beneficiario');   
            $table->string('ingreso_estado');     
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_movimiento_banco');   
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento'); 
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('deposito_id')->nullable();
            $table->foreign('deposito_id')->references('deposito_id')->on('deposito');  
            $table->bigInteger('cuenta_bancaria_id');
            $table->foreign('cuenta_bancaria_id')->references('cuenta_bancaria_id')->on('cuenta_bancaria');             
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
        Schema::dropIfExists('ingreso_banco');
    }
}
