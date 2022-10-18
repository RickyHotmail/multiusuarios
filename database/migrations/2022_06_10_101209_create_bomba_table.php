<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBombaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bomba', function (Blueprint $table) {
            $table->id("bomba_id");
            $table->string("bomba_numero");
            $table->string("bomba_marca");
            $table->string("bomba_modelo");
            $table->string("bomba_serie");
            $table->string("bomba_tipo");
            $table->double("bomba_posicion");
            $table->double("bomba_estado");
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
        Schema::dropIfExists('bomba');
    }
}
