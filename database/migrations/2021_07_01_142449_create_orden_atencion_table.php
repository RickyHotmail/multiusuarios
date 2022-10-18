<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenAtencionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_atencion', function (Blueprint $table) {
            $table->id('orden_id');
            $table->string('orden_codigo');            
            $table->string('orden_numero')->unique();            
            $table->float('orden_secuencial');
            $table->string('orden_reclamo');
            $table->float('orden_secuencial_reclamo');
            $table->date('orden_fecha');
            $table->time('orden_hora');
            $table->text('orden_observacion');
            $table->string('orden_iess');
            $table->string('orden_frecuencia');
            $table->string('orden_dependencia');
            $table->string('orden_cedula_afiliado');
            $table->text('orden_nombre_afiliado');
            $table->double('orden_precio');
            $table->double('orden_cobertura_porcentaje');
            $table->double('orden_cobertura');
            $table->double('orden_copago');
            $table->string('orden_estado');
            $table->bigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');
            $table->bigInteger('paciente_id');
            $table->foreign('paciente_id')->references('paciente_id')->on('paciente');
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_seguro');
            $table->bigInteger('tipod_id');
            $table->foreign('tipod_id')->references('tipod_id')->on('tipo_dependencia');
            $table->bigInteger('factura_id');
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta');
            $table->bigInteger('medico_id');
            $table->foreign('medico_id')->references('medico_id')->on('medico');
            $table->bigInteger('especialidad_id');
            $table->foreign('especialidad_id')->references('especialidad_id')->on('especialidad');
            $table->bigInteger('entidad_id');
            $table->foreign('entidad_id')->references('entidad_id')->on('entidad');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
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
        Schema::dropIfExists('orden_atencion');
    }
}
