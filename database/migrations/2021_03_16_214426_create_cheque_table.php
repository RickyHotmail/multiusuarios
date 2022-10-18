<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheque', function (Blueprint $table) {
            $table->id('cheque_id');
            $table->bigInteger('cheque_numero');
            $table->string('cheque_descripcion');
            $table->string('cheque_beneficiario');
            $table->date('cheque_fecha_emision');
            $table->date('cheque_fecha_pago');
            $table->double('cheque_valor');
            $table->string('cheque_valor_letras');
            $table->string('cheque_estado');
            $table->bigInteger('cuenta_bancaria_id');
            $table->unique(['cheque_numero', 'cuenta_bancaria_id'], 'cheque_unique_numero_cuenta_id');
            $table->foreign('cuenta_bancaria_id')->references('cuenta_bancaria_id')->on('cuenta_bancaria');
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
        Schema::dropIfExists('cheque');
    }
}
