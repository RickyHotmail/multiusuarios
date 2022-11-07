<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa', function (Blueprint $table) {
            $table->id('empresa_id');
            $table->string('empresa_ruc');
            $table->string('empresa_nombreComercial');
            $table->string('empresa_razonSocial');
            $table->string('empresa_direccion');
            $table->string('empresa_telefono');
            $table->string('empresa_celular')->nullable();
            $table->string('empresa_ciudad');
            $table->string('empresa_logo')->nullable();
            $table->string('empresa_contador')->nullable();
            $table->string('empresa_cedula_contador')->nullable();
            $table->string('empresa_cedula_representante')->nullable();
            $table->string('empresa_representante')->nullable();
            $table->date('empresa_fecha_ingreso');
            $table->string('empresa_email');
            $table->string('empresa_llevaContabilidad');
            $table->string('empresa_tipo');
            $table->string('empresa_contribuyenteEspecial');
            $table->string('empresa_contabilidad');
            $table->string('empresa_electronica');
            $table->string('empresa_nomina');
            $table->string('empresa_medico');
            $table->string('empresa_estado_cambiar_precio');
            $table->string('empresa_control_inventario');
            $table->string('empresa_estado');
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
        Schema::dropIfExists('empresa');
    }
}
