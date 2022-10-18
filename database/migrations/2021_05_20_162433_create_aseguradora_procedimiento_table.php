<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAseguradoraProcedimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aseguradora_procedimiento', function (Blueprint $table) {
            $table->id('procedimientoA_id');
            $table->string('procedimientoA_codigo');
            $table->decimal('procedimientoA_valor', $precision = 19, $scale = 4);
            $table->string('procedimientoA_estado');

            $table->bigInteger('procedimiento_id');
            $table->foreign('procedimiento_id')->references('procedimiento_id')->on('procedimiento_especialidad');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');  
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
        Schema::dropIfExists('aseguradora_procedimiento');
    }
}
