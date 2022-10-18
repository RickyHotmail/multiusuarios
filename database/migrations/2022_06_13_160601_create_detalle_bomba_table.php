<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleBombaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_bomba', function (Blueprint $table) {
            $table->id("detalle_id");
            $table->string("detalle_grupo");
            $table->string("detalle_unidad");
            $table->double("detalle_Valor");
            $table->string("detalle_estado");
            $table->bigInteger('bomba_id');
            $table->foreign('bomba_id')->references('bomba_id')->on('bomba');
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
        Schema::dropIfExists('detalle_bomba');
    }
}
