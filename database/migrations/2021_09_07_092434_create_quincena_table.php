<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuincenaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quincena', function (Blueprint $table) {
            $table->id('quincena_id');
            $table->string('quincena_numero')->unique();
            $table->string('quincena_serie');
            $table->float('quincena_secuencial');
            $table->date('quincena_fecha');       
            $table->string('quincena_tipo');     
            $table->double('quincena_valor',19,2);
            $table->double('quincena_saldo',19,2);
            $table->string('quincena_descripcion');
            $table->string('quincena_estado');
            $table->bigInteger('cabecera_rol_id')->nullable();
            $table->foreign('cabecera_rol_id')->references('cabecera_rol_id')->on('cabecera_rol'); 
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
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
        Schema::dropIfExists('quincena');
    }
}
