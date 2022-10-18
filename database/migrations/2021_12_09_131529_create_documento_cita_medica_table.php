<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoCitaMedicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_cita_medica', function (Blueprint $table) {
            $table->id('doccita_id');
            $table->string('doccita_nombre');
            $table->string('doccita_url');
            $table->string('doccita_estado');
            $table->bigInteger('orden_id');
            $table->foreign('orden_id')->references('orden_id')->on('orden_atencion'); 
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
        Schema::dropIfExists('documento_cita_medica');
    }
}
