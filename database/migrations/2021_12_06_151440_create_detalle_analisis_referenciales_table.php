<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleAnalisisReferencialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_analisis_referenciales', function (Blueprint $table) {
            $table->id('detalle_referenciales_id');
            $table->string('detalle_Columna1')->nullable();
            $table->string('detalle_Columna2')->nullable();
            $table->string('detalle_estado');
            $table->bigInteger('detalle_valores_id');
            $table->foreign('detalle_valores_id')->references('detalle_valores_id')->on('detalle_analisis_valores'); 
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
        Schema::dropIfExists('detalle_analisis_referenciales');
    }
}
