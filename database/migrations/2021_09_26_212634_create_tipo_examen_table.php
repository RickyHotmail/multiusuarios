<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoExamenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_examen', function (Blueprint $table) {
            $table->id('tipo_id');          
            $table->string('tipo_nombre');
            $table->string('tipo_estado');
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');   
            $table->bigInteger('tipo_muestra_id');
            $table->foreign('tipo_muestra_id')->references('tipo_muestra_id')->on('tipo_muestra');   
            $table->bigInteger('tipo_recipiente_id');
            $table->foreign('tipo_recipiente_id')->references('tipo_recipiente_id')->on('tipo_recipiente');   
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
        Schema::dropIfExists('tipo_examen');
    }
}
