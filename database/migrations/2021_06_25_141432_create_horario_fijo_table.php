<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioFijoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horario_fijo', function (Blueprint $table) {
            $table->id('horario_id');            
            $table->time('horario_hora_inicio');
            $table->time('horario_hora_fin');
            $table->string('horario_dia');
            $table->string('horario_estado');

            $table->bigInteger('mespecialidad_id');
            $table->foreign('mespecialidad_id')->references('mespecialidad_id')->on('medico_especialidad');
            
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
        Schema::dropIfExists('horario_fijo');
    }
}
