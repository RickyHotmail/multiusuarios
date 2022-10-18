<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodigoProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codigo_producto', function (Blueprint $table) {
            $table->id('codigo_id');
            $table->string('codigo_nombre');
            $table->string('codigo_estado');
            $table->integer('proveedor_id');
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedor');
            $table->integer('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
            $table->unique(['codigo_nombre', 'proveedor_id'], 'codigo_unique_proveedor_id');
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
        Schema::dropIfExists('codigo_producto');
    }
}
