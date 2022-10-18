<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_producto', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->date('movimiento_fecha');
            $table->float('movimiento_cantidad');
            $table->double('movimiento_precio');
            $table->double('movimiento_iva');
            $table->double('movimiento_total');
            $table->float('movimiento_stock_actual');
            $table->double('movimiento_costo_promedio');
            $table->string('movimiento_documento');
            $table->string('movimiento_motivo');
            $table->string('movimiento_tipo');
            $table->string('movimiento_costeo')->nullable();
            $table->text('movimiento_descripcion');
            $table->string('movimiento_estado');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega');
            $table->bigInteger('siembra_id')->nullable();
            $table->foreign('siembra_id')->references('siembra_id')->on('siembra');
            $table->bigInteger('centro_consumo_id')->nullable();
            $table->foreign('centro_consumo_id')->references('centro_consumo_id')->on('centro_consumo');
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
        Schema::dropIfExists('movimiento_producto');
    }
}
