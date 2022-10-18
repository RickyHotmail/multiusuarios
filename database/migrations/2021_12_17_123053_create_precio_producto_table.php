<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecioProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precio_producto', function (Blueprint $table) {
            $table->id('precio_id');
            $table->string('precio_dias');
            $table->string('precio_valor');
            $table->string('precio_estado');
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
        Schema::dropIfExists('precio_producto');
    }
}
