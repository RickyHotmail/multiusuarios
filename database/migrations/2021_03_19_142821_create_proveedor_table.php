<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedor', function (Blueprint $table) {
            $table->id('proveedor_id');
            $table->string('proveedor_ruc')->unique();
            $table->string('proveedor_nombre');
            $table->string('proveedor_nombre_comercial');
            $table->string('proveedor_gerente');
            $table->string('proveedor_direccion');
            $table->string('proveedor_telefono');
            $table->string('proveedor_celular');
            $table->string('proveedor_email');
            $table->string('proveedor_actividad'); 
            $table->string('proveedor_tipo')->nullable();           
            $table->date('proveedor_fecha_ingreso');
            $table->string('proveedor_lleva_contabilidad')->nullable();
            $table->string('proveedor_contribuyente')->nullable();
            //datos foraneos
            $table->bigInteger('proveedor_cuenta_pagar')->nullable();
            $table->foreign('proveedor_cuenta_pagar')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('proveedor_cuenta_anticipo')->nullable();
            $table->foreign('proveedor_cuenta_anticipo')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('tipo_sujeto_id');
            $table->foreign('tipo_sujeto_id')->references('tipo_sujeto_id')->on('tipo_sujeto');
            $table->bigInteger('tipo_identificacion_id');
            $table->foreign('tipo_identificacion_id')->references('tipo_identificacion_id')->on('tipo_identificacion');
            $table->bigInteger('ciudad_id');
            $table->foreign('ciudad_id')->references('ciudad_id')->on('ciudad');
            $table->bigInteger('categoria_proveedor_id');
            $table->foreign('categoria_proveedor_id')->references('categoria_proveedor_id')->on('categoria_proveedor');            
            $table->string('proveedor_estado');
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
        Schema::dropIfExists('proveedor');
    }
}
