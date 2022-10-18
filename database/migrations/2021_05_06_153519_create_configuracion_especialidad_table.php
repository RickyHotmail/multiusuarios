<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionEspecialidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracion_especialidad', function (Blueprint $table) {
            $table->id('configuracion_id');
            $table->text('configuracion_nombre');
            $table->string('configuracion_tipo');
            $table->string('configuracion_medida');
            $table->text('configuracion_url');
            $table->string('configuracion_multiple');
            $table->string('configuracion_estado');                      
            $table->bigInteger('especialidad_id');
            $table->foreign('especialidad_id')->references('especialidad_id')->on('especialidad');
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
        Schema::dropIfExists('configuracion_especialidad');
    }
}
