<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormaPagoMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forma_pago_movil', function (Blueprint $table) {
            $table->id('formap_id');
            $table->double('formap_valor');
            $table->integer('formap_tiempo');
            $table->integer('formap_plazo')->default(0);
            $table->integer('formap_estado')->default(1);
            $table->integer('factura_id');
            $table->foreign('factura_id')->references('factura_id')->on('factura_movil');
            $table->integer('tipop_id');
            $table->foreign('tipop_id')->references('tipop_id')->on('tipo_pago_movil');
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
        Schema::dropIfExists('forma_pago_movil');
    }
}
