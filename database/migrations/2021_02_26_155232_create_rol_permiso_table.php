<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolPermisoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->id('rol_permiso_id');
            $table->integer('permiso_id')->unsigned();
            $table->foreign('permiso_id')->references('permiso_id')->on('permiso');
            $table->integer('rol_id')->unsigned();
            $table->foreign('rol_id')->references('rol_id')->on('rol');
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
        Schema::dropIfExists('rol_permiso');
    }
}
