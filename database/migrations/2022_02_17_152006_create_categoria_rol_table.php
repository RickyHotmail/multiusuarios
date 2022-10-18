<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriaRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoria_rol', function (Blueprint $table) {
            $table->id('categoria_id');
            $table->text('categoria_nombre');        
            $table->text('categoria_estado');  
            $table->bigInteger('centro_consumo_id');
            $table->foreign('centro_consumo_id')->references('centro_consumo_id')->on('centro_consumo');
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
        Schema::dropIfExists('categoria_rol');
    }
}
