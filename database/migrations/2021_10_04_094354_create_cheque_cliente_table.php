<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequeClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheque_cliente', function (Blueprint $table) {
            $table->id('cheque_id');
            $table->float('cheque_numero');
            $table->string('cheque_cuenta');
            $table->string('cheque_valor');
            $table->string('cheque_dueno');
            $table->string('cheque_estado');
            $table->bigInteger('banco_lista_id');
            $table->foreign('banco_lista_id')->references('banco_lista_id')->on('banco_lista');
            $table->bigInteger('deposito_id');
            $table->foreign('deposito_id')->references('deposito_id')->on('deposito');
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
        Schema::dropIfExists('cheque_cliente');
    }
}
