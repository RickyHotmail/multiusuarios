<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposito', function (Blueprint $table) {
            $table->id('deposito_id');
            $table->date('deposito_fecha');
            $table->string('deposito_tipo');
            $table->float('deposito_numero');
            $table->double('deposito_valor');
            $table->text('deposito_descripcion');
            $table->string('deposito_estado');
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->bigInteger('cuenta_bancaria_id')->nullable();
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
        Schema::dropIfExists('deposito');
    }
}
