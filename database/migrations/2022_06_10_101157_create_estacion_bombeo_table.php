<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstacionBombeoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estacion_bombeo', function (Blueprint $table) {
            $table->id("estacion_id");
            $table->string("estacion_nombre");
            $table->text("estacion_ubicacion");
            $table->double("estacion_estado");
            $table->bigInteger('camaronera_id');
            $table->foreign('camaronera_id')->references('camaronera_id')->on('camaronera');
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
        Schema::dropIfExists('estacion_bombeo');
    }
}
