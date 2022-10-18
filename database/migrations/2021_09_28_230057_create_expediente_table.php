<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpedienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expediente', function (Blueprint $table) {
            $table->id('expediente_id');          
            $table->string('expediente_observacion');
            $table->date('expediente_proxima');
            $table->string('expediente_estado');
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
        Schema::dropIfExists('expediente');
    }
}
