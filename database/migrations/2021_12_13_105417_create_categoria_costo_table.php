<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriaCostoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoria_costo', function (Blueprint $table) {
            $table->id('categoriac_id');
            $table->string('categoriac_general');
            $table->string('categoriac_costo');
            $table->string('categoriac_racewas');
            $table->string('categoriac_sin_aplicacion');
            $table->string('categoriac_visible');
            $table->string('categoriac_estado');
            $table->bigInteger('categoria_id');
            $table->foreign('categoria_id')->references('categoria_id')->on('categoria_producto'); 
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
        Schema::dropIfExists('categoria_costo');
    }
}
