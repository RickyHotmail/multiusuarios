<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleFacturaMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_factura_movil', function (Blueprint $table) {
            $table->id('detallev_id');
            $table->timestamps();
            $table->integer('detallev_cantidad');
            $table->double('detallev_precio_unitario');
            $table->double('detallev_subsidio');
            $table->double('detallev_descuento');
            $table->double('detallev_ice')->nullable();
            $table->double('detallev_irbpnr')->nullable();
            $table->double('detallev_tarifa_especial');

            $table->integer('factura_id');
            $table->foreign('factura_id')->references('factura_id')->on('factura_movil');
            $table->integer('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto_movil');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_factura_movil');
    }
}
