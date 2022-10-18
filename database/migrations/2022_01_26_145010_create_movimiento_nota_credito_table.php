<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoNotaCreditoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_nota_credito', function (Blueprint $table) {
            $table->id('movimientonc_id'); 
            $table->string('movimientonc_tipo');
            $table->float('movimientonc_valor'); 
            $table->text('movimientonc_descripcion');         
            $table->bigInteger('nota_id');
            $table->foreign('nota_id')->references('nota_id')->on('nota_credito_banco'); 
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
        Schema::dropIfExists('movimiento_nota_credito');
    }
}
