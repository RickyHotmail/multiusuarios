<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresoPrestamoProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingreso_prestamo_producto', function (Blueprint $table) {
            $table->id('ingreso_id');
            $table->string('ingreso_numero')->unique();
            $table->string('ingreso_serie');
            $table->float('ingreso_secuencial');
            $table->date('ingreso_fecha'); 
            $table->double('ingreso_valor');
            $table->string('ingreso_placa');
            $table->string('ingreso_descripcion');
            $table->string('ingreso_estado');     
            $table->bigInteger('transportista_id');
            $table->foreign('transportista_id')->references('transportista_id')->on('transportista');   
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');   
            $table->bigInteger('rango_id');
            $table->foreign('rango_id')->references('rango_id')->on('rango_documento'); 
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa'); 
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
        Schema::dropIfExists('ingreso_prestamo_producto');
    }
}
