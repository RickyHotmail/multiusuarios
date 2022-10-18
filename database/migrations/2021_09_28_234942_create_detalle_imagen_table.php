<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleImagenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_imagen', function (Blueprint $table) {
            $table->id('detalle_id');                       
            $table->string('detalle_indicacion')->nullable();         
            $table->string('detalle_estado');
            $table->bigInteger('orden_id');
            $table->foreign('orden_id')->references('orden_id')->on('orden_imagen');  
            $table->bigInteger('imagen_id');
            $table->foreign('imagen_id')->references('imagen_id')->on('imagen'); 
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
        Schema::dropIfExists('detalle_imagen');
    }
}
