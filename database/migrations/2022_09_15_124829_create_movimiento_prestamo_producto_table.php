<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoPrestamoProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_prestamo_producto', function (Blueprint $table) {
            $table->id('movimiento_id');          
            $table->date('movimiento_fecha');
            $table->string('movimiento_tipo');
            $table->string('movimiento_descripcion');
            $table->double('movimiento_valor',19,4);
            $table->string('movimiento_documento');
            $table->string('movimiento_numero_documento');
            $table->string('movimiento_estado');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
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
        Schema::dropIfExists('movimiento_prestamo_producto');
    }
}
