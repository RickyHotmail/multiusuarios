<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->id('cliente_id');
            $table->string('cliente_cedula')->unique();
            $table->string('cliente_nombre');
            $table->string('cliente_abreviatura')->nullable();
            $table->string('cliente_direccion');
            $table->string('cliente_telefono');
            $table->string('cliente_celular');
            $table->text('cliente_email');
            $table->date('cliente_fecha_ingreso');
            $table->string('cliente_lleva_contabilidad');
            $table->string('cliente_tiene_credito');
            //datos foraneos
            $table->bigInteger('cliente_cuenta_cobrar')->nullable();
            $table->foreign('cliente_cuenta_cobrar')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('cliente_cuenta_anticipo')->nullable();
            $table->foreign('cliente_cuenta_anticipo')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('ciudad_id');
            $table->foreign('ciudad_id')->references('ciudad_id')->on('ciudad');  
            $table->bigInteger('tipo_identificacion_id');
            $table->foreign('tipo_identificacion_id')->references('tipo_identificacion_id')->on('tipo_identificacion');
            $table->bigInteger('tipo_cliente_id');
            $table->foreign('tipo_cliente_id')->references('tipo_cliente_id')->on('tipo_cliente'); 
            $table->bigInteger('credito_id');
            $table->foreign('credito_id')->references('credito_id')->on('credito');
            $table->bigInteger('categoria_cliente_id');
            $table->foreign('categoria_cliente_id')->references('categoria_cliente_id')->on('categoria_cliente');       
            $table->bigInteger('lista_id')->nullable();
            $table->foreign('lista_id')->references('lista_id')->on('lista_precio');

            $table->string('cliente_estado');
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
        Schema::dropIfExists('cliente');
    }
}
