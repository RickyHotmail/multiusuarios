<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizarDetalleOrdenTable extends Migration
{
    public function up()
    {
        Schema::create('parametrizar_detalle_orden', function (Blueprint $table) {
            $table->id('parametrizard_id');
            $table->string('parametrizard_descripcion', 100);
            $table->integer('parametrizard_valor');
            $table->integer('parametrizard_estado')->default(1);
            $table->integer('parametrizar_id');
            $table->foreign('parametrizar_id')->references('parametrizar_id')->on('parametrizar_orden');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parametrizar_detalle_orden');
    }
}