<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiltroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filtro', function (Blueprint $table) {
            $table->id("filtro_id");
            $table->string("filtro_nombre");
            $table->string("filtro_descripcion");
            $table->string("filtro_medida");
            $table->string("filtro_estado");
            $table->bigInteger('motor_id');
            $table->foreign('motor_id')->references('motor_id')->on('motor');
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
        Schema::dropIfExists('filtro');
    }
}
