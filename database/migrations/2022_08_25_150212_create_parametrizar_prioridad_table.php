<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizarPrioridadTable extends Migration
{
    public function up()
    {
        Schema::create('parametrizar_prioridad', function (Blueprint $table) {
            $table->id('prioridad_id');
            $table->string('prioridad_descripcion');
            $table->integer('prioridad_desde');
            $table->integer('prioridad_hasta');
            $table->integer('prioridad_estado')->default(1);
            $table->integer('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parametrizar_prioridad');
    }
}
