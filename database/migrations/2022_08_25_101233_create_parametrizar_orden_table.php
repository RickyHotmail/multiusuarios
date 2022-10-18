<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizarOrdenTable extends Migration
{

    public function up()
    {
        Schema::create('parametrizar_orden', function (Blueprint $table) {
            $table->id('parametrizar_id');
            $table->string('parametrizar_descripcion', 100);
            $table->integer('parametrizar_porcentaje');
            $table->integer('parametrizar_estado')->default(1);
            $table->integer('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parametrizar_orden');
    }
}
