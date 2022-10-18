<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleOrdenMantenimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_orden_mantenimiento', function (Blueprint $table) {
            $table->id('detalle_orden_id');
            $table->integer('detalle_orden_cantidad');
            
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');

            $table->integer('detalle_orden_estado');

            $table->bigInteger('orden_id');
            $table->foreign('orden_id')->references('orden_id')->on('orden_mantenimiento');
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
        Schema::dropIfExists('detalle_orden_mantenimiento');
    }
}
