<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizacionPermisoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametrizacion_permiso', function (Blueprint $table) {
            $table->id('parametrizacionp_id');
            $table->integer('parameretrizaciong_id');
            $table->foreign('parameretrizaciong_id')->references('parameretrizaciong_id')->on('parametrizacion_grupo_permiso');
            $table->integer('permiso_id');
            $table->foreign('permiso_id')->references('permiso_id')->on('permiso');
            $table->integer('parametrizacionp_estado')->default(1);
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
        Schema::dropIfExists('parametrizacion_permiso');
    }
}
