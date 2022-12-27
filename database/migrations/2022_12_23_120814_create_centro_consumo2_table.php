<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentroConsumo2Table extends Migration{
    public function up(){
        Schema::create('centro_consumo2', function (Blueprint $table) {
            $table->id('centroc2_id');
            $table->string('centroc2_nombre');
            $table->string('centroc2_descripcion')->nullable;
            $table->date('centroc2_fecha_ingreso');
            $table->integer('sustento_id')->nullable();
            $table->string('centroc2_secuencial');
            $table->integer('centroc2_nivel');
            $table->integer('centroc2_padre_id')->nullable();
            $table->integer('empresa_id');
            $table->foreign('empresa_id')->references(('empresa_id'))->on('empresa');
            $table->integer('centroc2_estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('centro_consumo2');
    }
}