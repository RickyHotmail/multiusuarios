<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoActivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_activo', function (Blueprint $table) {
            $table->id('grupo_id');
            $table->string('grupo_nombre');
            $table->float('grupo_porcentaje');
            $table->string('grupo_estado');
            $table->bigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');
            $table->bigInteger('cuenta_depreciacion');
            $table->foreign('cuenta_depreciacion')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('cuenta_gasto');
            $table->foreign('cuenta_gasto')->references('cuenta_id')->on('cuenta');            
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
        Schema::dropIfExists('grupo_activo');
    }
}
