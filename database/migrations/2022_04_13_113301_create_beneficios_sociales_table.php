<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiosSocialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficios_sociales', function (Blueprint $table) {
            $table->id('beneficios_id');
            $table->date('beneficios_fecha');
            $table->date('beneficios_fecha_emision');  
            $table->string('beneficios_tipo');     
            $table->double('beneficios_valor',19,4);
            $table->string('beneficios_periodo');
            $table->string('beneficios_descripcion');
            $table->string('beneficios_estado');
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_movimiento_empleado');
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
        Schema::dropIfExists('beneficios_sociales');
    }
}
