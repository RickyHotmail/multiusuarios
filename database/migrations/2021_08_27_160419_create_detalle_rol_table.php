<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_rol', function (Blueprint $table) {
            $table->id('detalle_rol_id');
            $table->date('detalle_rol_fecha_inicio');
            $table->date('detalle_rol_fecha_fin');
            $table->double('detalle_rol_sueldo',19,2); 
            $table->double('detalle_rol_porcentaje',19,2);
            $table->double('detalle_rol_dias');
            $table->float('detalle_rol_valor_dia');
            $table->double('detalle_rol_total_dias',19,2);
            $table->double('detalle_rol_horas_extras',19,2);
            $table->double('detalle_rol_valor_he',19,2);
            $table->double('detalle_rol_transporte',19,2);
            $table->double('detalle_rol_fondo_reserva',19,2);
            $table->double('detalle_rol_bonificacion_dias',19,2);
            $table->double('detalle_rol_horas_suplementarias',19,2);
            $table->double('detalle_rol_otra_bonificacion',19,2);
            $table->double('detalle_rol_otros_ingresos',19,2);
            $table->double('detalle_rol_sueldo_rembolsable',19,2);
            $table->double('detalle_rol_impuesto_renta',19,2);
            $table->double('detalle_rol_iess',19,2);
            $table->double('detalle_rol_multa',19,2); 
            $table->double('detalle_rol_quincena',19,2);  
            $table->double('detalle_rol_cosecha',19,2);     
            $table->double('detalle_rol_total_anticipo',19,2);
            $table->double('detalle_rol_total_comisariato',19,2);
            $table->double('detalle_rol_prestamo_quirografario',19,2);
            $table->double('detalle_rol_prestamo_hipotecario',19,2);
            $table->double('detalle_rol_prestamo',19,2);
            $table->double('detalle_rol_ext_salud',19,2);
            $table->double('detalle_rol_ley_sol',19,2);
            $table->double('detalle_rol_total_permiso',19,2);
            $table->double('detalle_rol_permiso_no_rem',19,2);
            $table->double('detalle_rol_otros_egresos',19,2);
            $table->double('detalle_rol_liquido_pagar',19,2);
            $table->string('detalle_rol_contabilizado');
            $table->double('detalle_rol_iess_asumido',19,2);
            $table->double('detalle_rol_aporte_patronal',19,2);
            $table->double('detalle_rol_aporte_iecesecap',19,2);
            $table->double('detalle_rol_vacaciones',19,2);
            $table->double('detalle_rol_vacaciones_anticipadas',19,2);
            $table->double('detalle_rol_decimo_tercero',19,2);
            $table->double('detalle_rol_decimo_cuarto',19,2);
            $table->double('detalle_rol_decimo_cuartoacum',19,2);
            $table->double('detalle_rol_decimo_terceroacum',19,2);
            $table->double('detalle_rol_total_ingreso',19,2);
            $table->double('detalle_rol_total_egreso',19,2);
            $table->string('detalle_rol_estado');
            $table->bigInteger('cabecera_rol_id');
            $table->foreign('cabecera_rol_id')->references('cabecera_rol_id')->on('cabecera_rol');
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
        Schema::dropIfExists('detalle_rol');
    }
}
