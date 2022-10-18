<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicoEspecialidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medico_especialidad', function (Blueprint $table) {
            $table->id('mespecialidad_id');
            $table->bigInteger('especialidad_id');
            $table->foreign('especialidad_id')->references('especialidad_id')->on('especialidad');
            $table->bigInteger('medico_id');
            $table->foreign('medico_id')->references('medico_id')->on('medico');  
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
        Schema::dropIfExists('medico_especialidad');
    }
}
