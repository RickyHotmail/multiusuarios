<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleOfacturaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ofactura', function (Blueprint $table) {
            $table->id('detalle_id');                       
            $table->string('detalle_observacion');                        
            $table->decimal('detalle_precio');         
            $table->string('detalle_estado');
            $table->bigInteger('orden_id');
            $table->foreign('orden_id')->references('orden_id')->on('orden_factura');  
            $table->bigInteger('procedimientoA_id');
            $table->foreign('procedimientoA_id')->references('procedimientoA_id')->on('aseguradora_procedimiento'); 
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
        Schema::dropIfExists('detalle_ofactura');
    }
}
