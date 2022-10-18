<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencia', function (Blueprint $table) {
            $table->id('transferencia_id');
            $table->string('transferencia_descripcion');
            $table->string('transferencia_beneficiario');
            $table->date('transferencia_fecha');
            $table->double('transferencia_valor');
            $table->string('transferencia_estado');
            $table->bigInteger('cuenta_bancaria_id');
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
        Schema::dropIfExists('transferencia');
    }
}
