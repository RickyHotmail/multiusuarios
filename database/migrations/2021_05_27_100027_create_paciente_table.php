<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paciente', function (Blueprint $table) {
            $table->id('paciente_id');
            $table->string('paciente_cedula')->unique();
            $table->string('paciente_apellidos');
            $table->string('paciente_nombres');
            $table->string('paciente_direccion');
            $table->date('paciente_fecha_nacimiento');
            $table->string('paciente_nacionalidad');
            $table->string('paciente_celular');
            $table->string('paciente_email');
            $table->string('paciente_sexo');
            $table->string('paciente_dependiente');
            $table->string('paciente_tipo_dependencia');
            $table->string('paciente_cedula_afiliado')->nullable();
            $table->string('paciente_nombre_afiliado')->nullable();
            $table->string('paciente_estado');
            $table->bigInteger('ciudad_id');
            $table->foreign('ciudad_id')->references('ciudad_id')->on('ciudad');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('entidad_id');
            $table->foreign('entidad_id')->references('entidad_id')->on('entidad');
            $table->bigInteger('tipo_identificacion_id');
            $table->foreign('tipo_identificacion_id')->references('tipo_identificacion_id')->on('tipo_identificacion');
            $table->bigInteger('tipod_id');
            $table->foreign('tipod_id')->references('tipod_id')->on('tipo_dependencia');
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
        Schema::dropIfExists('paciente');
    }
}
