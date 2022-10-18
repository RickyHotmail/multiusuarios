<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEgresoBancoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('egreso_banco', function (Blueprint $table) {
            $table->id('egreso_id');
            $table->string('egreso_numero')->unique();
            $table->string('egreso_serie');
            $table->float('egreso_secuencial');
            $table->date('egreso_fecha'); 
            $table->double('egreso_valor');
            $table->string('egreso_descripcion');
            $table->string('egreso_beneficiario');   
            $table->string('egreso_estado');     
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_movimiento_banco');   
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento'); 
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('cheque_id')->nullable();
            $table->foreign('cheque_id')->references('cheque_id')->on('cheque');
            $table->bigInteger('transferencia_id')->nullable();
            $table->foreign('transferencia_id')->references('transferencia_id')->on('transferencia');
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
        Schema::dropIfExists('egreso_banco');
    }
}
