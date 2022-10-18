<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoOrdenPacienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_orden_paciente', function (Blueprint $table) {
            $table->id('docpaciente_id');
            $table->string('docpaciente_url');
            $table->string('docpaciente_estado');
            $table->bigInteger('orden_id');
            $table->bigInteger('documento_id');

            $table->foreign('orden_id')->references('orden_id')->on('orden_atencion');
            $table->foreign('documento_id')->references('documento_id')->on('documento_orden_atencion');

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
        Schema::dropIfExists('documento_orden_paciente');
    }
}
