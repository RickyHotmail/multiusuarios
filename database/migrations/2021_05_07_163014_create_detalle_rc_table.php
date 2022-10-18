<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleRcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_rc', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->string('detalle_tipo');
            $table->double('detalle_base');
            $table->double('detalle_porcentaje');
            $table->double('detalle_valor');
            $table->string('detalle_asumida');
            $table->string('detalle_estado');
            $table->bigInteger('retencion_id');
            $table->foreign('retencion_id')->references('retencion_id')->on('retencion_compra');
            $table->bigInteger('concepto_id');
            $table->foreign('concepto_id')->references('concepto_id')->on('concepto_retencion');
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
        Schema::dropIfExists('detalle_rc');
    }
}
