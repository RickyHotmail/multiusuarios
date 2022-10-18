<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleListaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_lista', function (Blueprint $table) {
            $table->id('detallel_id');
            $table->string('detallel_dias');
            $table->decimal('detallel_valor');
            $table->string('detallel_estado');
            $table->bigInteger('lista_id');
            $table->foreign('lista_id')->references('lista_id')->on('lista_precio');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');  
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
        Schema::dropIfExists('detalle_lista');
    }
}
