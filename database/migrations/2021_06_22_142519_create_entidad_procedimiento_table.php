<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntidadProcedimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entidad_procedimiento', function (Blueprint $table) {
            $table->id('ep_id');            
            $table->string('ep_tipo');
            $table->double('ep_valor');
            $table->string('ep_estado');

            $table->bigInteger('procedimiento_id');
            $table->foreign('procedimiento_id')->references('procedimiento_id')->on('procedimiento_especialidad');
            $table->bigInteger('entidad_id');
            $table->foreign('entidad_id')->references('entidad_id')->on('entidad');

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
        Schema::dropIfExists('entidad_procedimiento');
    }
}
