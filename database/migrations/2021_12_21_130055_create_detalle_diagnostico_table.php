<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleDiagnosticoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_diagnostico', function (Blueprint $table) {
            $table->id('detalled_id');
            $table->string('detalled_estado');
            $table->bigInteger('diagnostico_id');
            $table->foreign('diagnostico_id')->references('diagnostico_id')->on('diagnostico');
            $table->bigInteger('enfermedad_id');
            $table->foreign('enfermedad_id')->references('enfermedad_id')->on('enfermedad'); 
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
        Schema::dropIfExists('detalle_diagnostico');
    }
}
