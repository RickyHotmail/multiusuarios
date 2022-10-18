<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoNotaDebitoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_nota_debito', function (Blueprint $table) {           
            $table->id('movimientond_id'); 
            $table->string('movimientond_tipo');
            $table->float('movimientond_valor');
            $table->text('movimientond_descripcion');
            $table->bigInteger('nota_id');
            $table->foreign('nota_id')->references('nota_id')->on('nota_debito_banco'); 
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_movimiento_banco'); 
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
        Schema::dropIfExists('movimiento_nota_debito');
    }
}
