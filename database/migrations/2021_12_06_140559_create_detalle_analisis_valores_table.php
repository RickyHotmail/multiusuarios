<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleAnalisisValoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_analisis_valores', function (Blueprint $table) {
            $table->id('detalle_valores_id');
            $table->string('detalle_valores_id_referencia');

            $table->string('id_externo_parametro')->nullable();
            $table->string('nombre_parametro', 50);
            $table->string('resultado', 80);
            $table->string('unidad_medida', 80)->nullable();
            $table->double('valor_minimo')->nullable();
            $table->double('valor_maximo')->nullable();
            $table->string('valor_normal', 20)->nullable();

            $table->string('interpretacion')->nullable();
            $table->string('comentario')->nullable();

            $table->bigInteger('detalle_id');

            $table->foreign('detalle_id')->references('detalle_id')->on('detalle_analisis'); 
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
        Schema::dropIfExists('detalle_analisis_valores');
    }
}
