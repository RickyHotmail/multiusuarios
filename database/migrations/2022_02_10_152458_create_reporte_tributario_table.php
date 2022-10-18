<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReporteTributarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reporte_tributario', function (Blueprint $table) {
            $table->id('reporte_id'); 
            $table->string('reporte_mes'); 
            $table->string('reporte_ano');
            $table->string('reporte_tipo');
            $table->string('reporte_casillero');
            $table->float('reporte_vbruto');
            $table->float('reporte_vnc');
            $table->float('reporte_vneto');
            $table->float('reporte_viva');
            $table->string('reporte_estado');
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
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
        Schema::dropIfExists('reporte_tributario');
    }
}
