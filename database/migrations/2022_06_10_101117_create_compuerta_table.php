<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompuertaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compuerta', function (Blueprint $table) {
            $table->id("compuerta_id");
            $table->string("compuerta_codigo");
            $table->string("compuerta_tipo");
            $table->string("compuerta_entr_sal");
            $table->double("compuerta_altura");
            $table->double("compuerta_ancho");
            $table->double("compuerta_estado");
            $table->bigInteger('piscina_id');
            $table->foreign('piscina_id')->references('piscina_id')->on('piscina');
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
        Schema::dropIfExists('compuerta');
    }
}
