<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSobranteCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sobrante_caja', function (Blueprint $table) {
            $table->id('sobrante_id');
            $table->string('sobrante_numero')->unique();
            $table->string('sobrante_serie');
            $table->float('sobrante_secuencial');
            $table->date('sobrante_fecha');
            $table->string('sobrante_observacion');
            $table->double('sobrante_monto');
            $table->string('sobrante_estado');          
            $table->bigInteger('arqueo_id');
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento'); 
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
        Schema::dropIfExists('sobrante_caja');
    }
}
