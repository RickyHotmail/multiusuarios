<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proforma', function (Blueprint $table) {
            $table->id('proforma_id');
            $table->string('proforma_numero')->unique();
            $table->string('proforma_serie');
            $table->float('proforma_secuencial');
            $table->string('proforma_tipo_pago');
            $table->bigInteger('proforma_dias_plazo');
            $table->date('proforma_fecha');
            $table->double('proforma_subtotal');
            $table->double('proforma_tarifa0');
            $table->double('proforma_tarifa12');
            $table->double('proforma_descuento');
            $table->double('proforma_iva');
            $table->double('proforma_total');
            $table->text('proforma_comentario');
            $table->float('proforma_porcentaje_iva');
            $table->string('proforma_estado');
            $table->bigInteger('bodega_id');
            $table->foreign('bodega_id')->references('bodega_id')->on('bodega');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento');
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
        Schema::dropIfExists('proforma');
    }
}
