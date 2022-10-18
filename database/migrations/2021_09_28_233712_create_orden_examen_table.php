<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenExamenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_examen', function (Blueprint $table) {
            $table->id('orden_id');            
            $table->string('orden_otros')->nullable();    
            $table->bigInteger('orden_id_referencia')->nullable();
            $table->char('orden_numero_referencia', 20)->nullable();
            $table->string('orden_estado');
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
        Schema::dropIfExists('orden_examen');
    }
}
