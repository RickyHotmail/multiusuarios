<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarcoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marco', function (Blueprint $table) {
            $table->id("marco_id");
            $table->string("marco_tipo");
            $table->double("marco_largo");
            $table->double("marco_ancho");
            $table->double("marco_estado");
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
        Schema::dropIfExists('marco');
    }
}
