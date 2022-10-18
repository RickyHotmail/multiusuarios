<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermisoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permiso', function (Blueprint $table) {
            $table->id('permiso_id');
            $table->string('permiso_nombre');
            $table->string('permiso_ruta');
            $table->string('permiso_tipo');
            $table->string('permiso_icono');
            $table->integer('permiso_orden');
            $table->string('permiso_estado');
            $table->integer('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
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
        Schema::dropIfExists('permiso');
    }
}
