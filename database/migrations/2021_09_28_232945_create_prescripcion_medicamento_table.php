<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescripcionMedicamentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescripcion_medicamento', function (Blueprint $table) {
            $table->id('prescripcionm_id');            
            $table->float('prescripcionm_cantidad');    
            $table->string('prescripcionm_indicacion');
            $table->string('prescripcionm_estado');
            $table->bigInteger('prescripcion_id');
            $table->foreign('prescripcion_id')->references('prescripcion_id')->on('prescripcion');  
            $table->bigInteger('medicamento_id');
            $table->foreign('medicamento_id')->references('medicamento_id')->on('medicamento');  
            $table->bigInteger('movimiento_id');
            $table->foreign('movimiento_id')->references('movimiento_id')->on('movimiento_producto');  
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
        Schema::dropIfExists('prescripcion_medicamento');
    }
}
