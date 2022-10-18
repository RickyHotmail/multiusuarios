<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacacion', function (Blueprint $table) {
            $table->id('vacacion_id');
            $table->string('vacacion_numero')->unique();
            $table->string('vacacion_serie');
            $table->float('vacacion_secuencial');
            $table->date('vacacion_fecha');       
            $table->string('vacacion_tipo');     
            $table->double('vacacion_valor',19,4);
            $table->string('vacacion_descripcion');
            $table->string('vacacion_estado');
            $table->bigInteger('cabecera_rol_id')->nullable();
            $table->foreign('cabecera_rol_id')->references('cabecera_rol_id')->on('cabecera_rol'); 
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
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
        Schema::dropIfExists('vacacion');
    }
}
