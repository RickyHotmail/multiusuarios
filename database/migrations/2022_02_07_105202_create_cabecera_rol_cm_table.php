<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabeceraRolCmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cabecera_rol_cm', function (Blueprint $table) {
            $table->id('cabecera_rol_id');
            $table->date('cabecera_rol_fecha');
            $table->string('cabecera_rol_tipo');
            $table->float('cabecera_rol_total_dias');
            $table->double('cabecera_rol_total_ingresos',19,2);
            $table->double('cabecera_rol_total_egresos',19,2);
            $table->double('cabecera_rol_sueldo',19,2);
            $table->double('cabecera_rol_pago',19,2);
            $table->string('cabecera_rol_anticipos',19,2);
            $table->string('cabecera_rol_quincena',19,2);
            $table->string('cabecera_rol_comisariato',19,2);
            $table->string('cabecera_rol_fr_acumula',19,2);
            $table->string('cabecera_rol_fondo_reserva',19,2);
            $table->double('cabecera_rol_decimotercero',19,2);
            $table->double('cabecera_rol_decimocuarto',19,2);
            $table->double('cabecera_rol_decimotercero_acumula',19,2);
            $table->double('cabecera_rol_decimocuarto_acumula',19,2);
            $table->double('cabecera_rol_aporte_patronal',19,2);
            $table->double('cabecera_rol_vacaciones',19,2);
            $table->double('cabecera_rol_viaticos',19,2);
            $table->double('cabecera_rol_iece_secap',19,2);
            $table->string('cabecera_rol_estado');
            $table->bigInteger('empleado_id');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
            $table->bigInteger('diario_contabilizacion_id')->nullable();
            $table->foreign('diario_contabilizacion_id')->references('diario_id')->on('diario');
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
        Schema::dropIfExists('cabecera_rol_cm');
    }
}
