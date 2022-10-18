<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDecimoTerceroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('decimo_tercero', function (Blueprint $table) {
            $table->id('decimo_id');
            $table->date('decimo_fecha'); 
            $table->date('decimo_fecha_emision');      
            $table->string('decimo_tipo');     
            $table->double('decimo_valor',19,4);
            $table->string('decimo_periodo');
            $table->string('decimo_descripcion');
            $table->string('decimo_estado');
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario');
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
        Schema::dropIfExists('decimo_tercero');
    }
}
