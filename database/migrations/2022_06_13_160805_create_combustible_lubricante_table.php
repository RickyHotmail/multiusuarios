<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCombustibleLubricanteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combustible_lubricante', function (Blueprint $table) {
            $table->id("combustible_id");
            $table->string("combustible_nombre");
            $table->string("combustible_descripcion");
            $table->string("combustible_medida");
            $table->string("combustible_estado");
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
        Schema::dropIfExists('combustible_lubricante');
    }
}
