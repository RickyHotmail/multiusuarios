<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleProformaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_proforma', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->float('detalle_cantidad');
            $table->double('detalle_precio_unitario');
            $table->double('detalle_descuento');
            $table->double('detalle_iva');
            $table->double('detalle_total');
            $table->string('detalle_estado');
            $table->bigInteger('proforma_id');
            $table->foreign('proforma_id')->references('proforma_id')->on('proforma');
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
        Schema::dropIfExists('detalle_proforma');
    }
}
