<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMallaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('malla', function (Blueprint $table) {
            $table->id("malla_id");
            $table->string("malla_nombre");
            $table->string("malla_ojo");
            $table->double("malla_largo");
            $table->double("malla_ancho");
            $table->double("malla_estado");
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
        Schema::dropIfExists('malla');
    }
}
