<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabeceraRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cabecera_rol', function (Blueprint $table) {
            $table->id('cabecera_rol_id');
            $table->date('cabecera_rol_fecha');
            $table->string('cabecera_rol_tipo');
            $table->float('cabecera_rol_total_dias');
            $table->double('cabecera_rol_total_ingresos',19,4);
            $table->double('cabecera_rol_total_anticipos',19,4);
            $table->double('cabecera_rol_total_egresos',19,4);
            $table->double('cabecera_rol_sueldo',19,4);
            $table->double('cabecera_rol_pago',19,4);
            $table->string('cabecera_rol_fr_acumula');
            $table->double('cabecera_rol_iesspersonal',19,4);
            $table->double('cabecera_rol_iesspatronal',19,4);
            $table->string('cabecera_rol_estado');
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
            $table->bigInteger('diario_contabilizacion_id')->nullable();
            $table->foreign('diario_contabilizacion_id')->references('diario_id')->on('diario');
            $table->bigInteger('diario_contabilizacion_beneficios_id')->nullable();
            $table->foreign('diario_contabilizacion_beneficios_id')->references('diario_id')->on('diario');
            $table->bigInteger('diario_pago_id')->nullable();
            $table->foreign('diario_pago_id')->references('diario_id')->on('diario');      
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
        Schema::dropIfExists('cabecera_rol');
    }
}
