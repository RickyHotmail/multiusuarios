<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBateriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bateria', function (Blueprint $table) {
            $table->id("bateria_id");
            $table->string("bateria_descripcion");
            $table->double("bateria_cantidad");
            $table->string("bateria_marca");
            $table->string("bateria_estado");
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
        Schema::dropIfExists('bateria');
    }
}
