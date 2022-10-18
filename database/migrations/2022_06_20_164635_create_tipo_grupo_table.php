<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoGrupoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_grupo', function (Blueprint $table) {
            $table->id('tipo_id');
            $table->string('tipo_nombre');
            $table->string('tipo_icono');
            $table->integer('tipo_orden');
            $table->string('tipo_estado');
            $table->integer('grupo_id');
            $table->foreign('grupo_id')->references('grupo_id')->on('grupo_permiso');
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
        Schema::dropIfExists('tipo_grupo');
    }
}
