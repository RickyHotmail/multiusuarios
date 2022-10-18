<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizarEspecialidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametrizar_especialidad', function (Blueprint $table) {
            $table->id('parametrizare_id');
            $table->string('parametrizare_nombre', 100);
            $table->integer('parametrizare_tipo');
            $table->string('parametrizare_medida', 50);
            $table->integer('tipod_id');
            $table->foreign('tipod_id')->references('tipod_id')->on('tipo_detalle_consulta');
            $table->integer('parametrizare_estado')->default(1);
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
        Schema::dropIfExists('parametrizar_especialidad');
    }
}
