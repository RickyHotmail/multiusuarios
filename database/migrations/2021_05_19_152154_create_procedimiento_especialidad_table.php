<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcedimientoEspecialidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedimiento_especialidad', function (Blueprint $table) {
            $table->id('procedimiento_id');
            $table->string('procedimiento_estado');

            $table->bigInteger('especialidad_id');
            $table->foreign('especialidad_id')->references('especialidad_id')->on('especialidad');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');  
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
        Schema::dropIfExists('procedimiento_especialidad');
    }
}
