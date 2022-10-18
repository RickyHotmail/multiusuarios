<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleLaboratorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_laboratorio', function (Blueprint $table) {
            $table->id('detalle_id');          
            $table->string('detalle_nombre');
            $table->string('detalle_medida')->nullable();
            $table->string('detalle_abreviatura')->nullable();
            $table->double('detalle_minimo');
            $table->double('detalle_maximo');
            $table->string('detalle_estado');
            $table->bigInteger('examen_id');
            $table->foreign('examen_id')->references('examen_id')->on('examen'); 
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
        Schema::dropIfExists('detalle_laboratorio');
    }
}
