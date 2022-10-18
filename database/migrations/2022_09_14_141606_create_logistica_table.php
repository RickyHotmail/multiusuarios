<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogisticaTable extends Migration
{
    public function up()
    {
        Schema::create('logistica', function (Blueprint $table) {
            $table->id('logistica_id');
            $table->string('logistica_nombre');
            $table->string('logistica_descripcion')->nullable();
            $table->integer('estado')->default(1);
            $table->integer('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logistica');
    }
}
