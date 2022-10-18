<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tablon', function (Blueprint $table) {
            $table->id("tablon_id");
            $table->string("tablon_ancho");
            $table->string("tablon_cantidad");
            $table->double("tablon_largo");
            $table->double("tablon_espesor");
            $table->double("tablon_estado");
            $table->bigInteger('compuerta_id');
            $table->foreign('compuerta_id')->references('compuerta_id')->on('compuerta'); 
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
        Schema::dropIfExists('tablon');
    }
}
