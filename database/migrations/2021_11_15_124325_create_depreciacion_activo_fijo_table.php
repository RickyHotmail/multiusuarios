<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepreciacionActivoFijoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depreciacion_activo_fijo', function (Blueprint $table) {
            $table->id('depreciacion_id');
            $table->string('depreciacion_fecha');
            $table->float('depreciacion_valor');            
            $table->string('depreciacion_estado');
            $table->bigInteger('activo_id');
            $table->foreign('activo_id')->references('activo_id')->on('activo_fijo');
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
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
        Schema::dropIfExists('depreciacion_activo_fijo');
    }
}
