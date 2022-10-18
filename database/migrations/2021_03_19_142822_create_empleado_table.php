<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleado', function (Blueprint $table) {
            $table->id('empleado_id');
            $table->string('empleado_cedula')->unique();
            $table->string('empleado_nombre');
            $table->string('empleado_telefono');
            $table->string('empleado_celular');
            $table->string('empleado_direccion');
            $table->string('empleado_sexo');
            $table->float('empleado_estatura');
            $table->string('empleado_grupo_sanguineo');
            $table->string('empleado_lugar_nacimiento');
            $table->date('empleado_fecha_nacimiento');
            $table->integer('empleado_edad');
            $table->string('empleado_nacionalidad');
            $table->string('empleado_estado_civil');
            $table->text('empleado_correo');
            $table->string('empleado_jornada');
            $table->double('empleado_cosecha');
            $table->integer('empleado_carga_familiar');
            $table->text('empleado_contacto_nombre');
            $table->string('empleado_contacto_telefono');
            $table->string('empleado_contacto_celular');
            $table->text('empleado_contacto_direccion');
            $table->text('empleado_observacion');
            $table->double('empleado_sueldo');
            $table->double('empleado_quincena');
            $table->date('empleado_fecha_ingreso');
            $table->date('empleado_fecha_salida')->nullable();
            $table->string('empleado_horas_extra');
            $table->string('empleado_afiliado');
            $table->string('empleado_iess_asumido');
            $table->string('empleado_fondos_reserva');
            $table->date('empleado_fecha_afiliacion')->nullable();
            $table->date('empleado_fecha_inicioFR')->nullable();
            $table->string('empleado_impuesto_renta');
            $table->string('empleado_decimo_tercero');
            $table->string('empleado_decimo_cuarto');
            $table->string('empleado_iess_gerente');
            $table->string('empleado_estado');
            $table->string('empleado_cuenta_tipo')->nullable();
            $table->string('empleado_cuenta_numero')->nullable();
            //foreign Key
            $table->bigInteger('cargo_id');
            $table->foreign('cargo_id')->references('empleado_cargo_id')->on('empleado_cargo');
            $table->bigInteger('departamento_id')->nullable();
            $table->foreign('departamento_id')->references('departamento_id')->on('empresa_departamento');
            $table->bigInteger('empleado_cuenta_anticipo')->nullable();
            $table->foreign('empleado_cuenta_anticipo')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('empleado_cuenta_prestamo')->nullable();
            $table->foreign('empleado_cuenta_prestamo')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('tipo_id')->nullable();
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_empleado');
            $table->bigInteger('banco_lista_id');
            $table->foreign('banco_lista_id')->references('banco_lista_id')->on('banco_lista');
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
        Schema::dropIfExists('empleado');
    }
}
