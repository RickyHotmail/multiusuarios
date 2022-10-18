<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleRolCmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_rol_cm', function (Blueprint $table) {
            $table->id('detalle_rol_id');
            $table->date('detalle_rol_fecha_inicio');
            $table->date('detalle_rol_fecha_fin');
            $table->string('detalle_rol_descripcion');
            $table->double('detalle_rol_valor',19,2);
            $table->string('detalle_rol_contabilizado');
            $table->string('detalle_rol_estado');
            $table->bigInteger('rubro_id');
            $table->foreign('rubro_id')->references('rubro_id')->on('rubro');
            $table->bigInteger('cabecera_rol_id');
            $table->foreign('cabecera_rol_id')->references('cabecera_rol_id')->on('cabecera_rol_cm');
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
        Schema::dropIfExists('detalle_rol_cm');
    }
}
