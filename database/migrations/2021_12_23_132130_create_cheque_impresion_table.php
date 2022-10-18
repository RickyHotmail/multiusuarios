<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequeImpresionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheque_impresion', function (Blueprint $table) {
            $table->id('chequei_id');           
            $table->float('chequei_valorx');
            $table->float('chequei_valory');
            $table->float('chequei_valorfont');
            $table->float('chequei_beneficiariox');
            $table->float('chequei_beneficiarioy');
            $table->float('chequei_beneficiariofont');
            $table->float('chequei_letrasx');
            $table->float('chequei_letrasy');
            $table->float('chequei_letrasfont');
            $table->float('chequei_fechax');
            $table->float('chequei_fechay');
            $table->float('chequei_fechafont');
            $table->bigInteger('cuenta_bancaria_id');
            $table->foreign('cuenta_bancaria_id')->references('cuenta_bancaria_id')->on('cuenta_bancaria');
            $table->string('chequei_estado');
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
        Schema::dropIfExists('cheque_impresion');
    }
}
