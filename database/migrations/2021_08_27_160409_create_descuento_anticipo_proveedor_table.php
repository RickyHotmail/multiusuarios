<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescuentoAnticipoProveedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descuento_anticipo_proveedor', function (Blueprint $table) {
            $table->id('descuento_id');
            $table->date('descuento_fecha');
            $table->double('descuento_valor');
            $table->text('descuento_descripcion');
            $table->string('descuento_estado');
            $table->bigInteger('anticipo_id');
            $table->foreign('anticipo_id')->references('anticipo_id')->on('anticipo_proveedor');
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('transaccion_id');
            $table->foreign('transaccion_id')->references('transaccion_id')->on('transaccion_compra');
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
        Schema::dropIfExists('descuento_anticipo_proveedor');
    }
}
