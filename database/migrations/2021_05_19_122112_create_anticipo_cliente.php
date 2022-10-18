<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnticipoCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anticipo_cliente', function (Blueprint $table) {
            $table->id('anticipo_id');
            $table->string('anticipo_numero')->unique();
            $table->string('anticipo_serie');
            $table->float('anticipo_secuencial');
            $table->date('anticipo_fecha');
            $table->string('anticipo_tipo');
            $table->string('anticipo_documento');
            $table->string('anticipo_motivo');
            $table->double('anticipo_valor');
            $table->double('anticipo_saldo');
            $table->double('anticipo_saldom');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');             
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('arqueo_id')->nullable();
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');
            $table->string('anticipo_estado');
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
        Schema::dropIfExists('anticipo_cliente');
    }
}
