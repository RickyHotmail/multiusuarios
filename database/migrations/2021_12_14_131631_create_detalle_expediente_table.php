<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleExpedienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_expediente', function (Blueprint $table) {
            $table->id('detallee_id');
            $table->string('detallee_nombre');
            $table->string('detallee_tipo');
            $table->string('detallee_medida')->nullable();
            $table->string('detallee_url');
            $table->string('detallee_multiple');
            $table->string('detallee_valor');
            $table->string('detallee_estado');
            $table->bigInteger('expediente_id');
            $table->foreign('expediente_id')->references('expediente_id')->on('expediente'); 
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
        Schema::dropIfExists('detalle_expediente');
    }
}
