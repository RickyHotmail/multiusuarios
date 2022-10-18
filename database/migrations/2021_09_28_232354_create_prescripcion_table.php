<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescripcionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescripcion', function (Blueprint $table) {
            $table->id('prescripcion_id');            
            $table->string('prescripcion_recomendacion')->nullable();    
            $table->string('prescripcion_observacion')->nullable();    
            $table->string('prescripcion_estado');
            $table->bigInteger('expediente_id');
            $table->foreign('expediente_id')->references('expediente_id')->on('expediente');  
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
        Schema::dropIfExists('prescripcion');
    }
}
