<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoCxpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago_cxp', function (Blueprint $table) {
            $table->id('pago_id');
            $table->text('pago_descripcion');
            $table->date('pago_fecha');
            $table->string('pago_tipo');
            $table->double('pago_valor');
            $table->string('pago_estado');
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('arqueo_id')->nullable();
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');
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
        Schema::dropIfExists('pago_cxp');
    }
}
