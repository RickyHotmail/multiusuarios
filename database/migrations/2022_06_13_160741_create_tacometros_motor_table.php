<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTacometrosMotorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tacometros_motor', function (Blueprint $table) {
            $table->id("tacometros_id");
            $table->string("tacometros_descripcion");
            $table->string("tacometros_funcionamiento");
            $table->string("tacometros_estado");
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
        Schema::dropIfExists('tacometros_motor');
    }
}
