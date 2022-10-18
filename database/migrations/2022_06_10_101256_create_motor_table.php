<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motor', function (Blueprint $table) {
            $table->id("motor_id");
            $table->string("motor_serie");
            $table->string("motor_marca");
            $table->string("motor_modelo");
            $table->date("motor_fecha_compra");
            $table->string("motor_tipo");
            $table->double("motor_potencia");
            $table->double("motor_velocidad");
            $table->double("motor_energia");
            $table->string("motor_aspiracion");
            $table->double("motor_eficiencia");
            $table->double("motor_aspiracion");
            $table->double("motor_aspiracion");
            $table->double("motor_grasa");
            $table->double("motor_radio");
            $table->double("motor_estado");
            $table->bigInteger('estacion_id');
            $table->foreign('estacion_id')->references('estacion_id')->on('estacion_bombeo');
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
        Schema::dropIfExists('motor');
    }
}
