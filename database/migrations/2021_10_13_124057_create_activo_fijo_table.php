<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivoFijoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activo_fijo', function (Blueprint $table) {
            $table->id('activo_id');
            $table->date('activo_fecha_inicio');
            $table->date('activo_fecha_fin');
            $table->date('activo_fecha_documento');
            $table->text('activo_descripcion');
            $table->float('activo_valor');
            $table->float('activo_valor2');
            $table->float('activo_base_depreciar');
            $table->float('activo_vida_util');
            $table->float('activo_valor_util');
            $table->float('activo_depreciacion');
            $table->float('activo_depreciacion_mensual');
            $table->float('activo_depreciacion_anual');
            $table->float('activo_depreciacion_acumulada');            
            $table->string('activo_estado');
            $table->bigInteger('grupo_id')->nullable();;
            $table->foreign('grupo_id')->references('grupo_id')->on('grupo_activo');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('producto_id');
            $table->foreign('producto_id')->references('producto_id')->on('producto');
            $table->bigInteger('proveedor_id')->nullable();
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedor'); 
            $table->bigInteger('transaccion_id')->nullable();
            $table->foreign('transaccion_id')->references('transaccion_id')->on('transaccion_compra');
            $table->bigInteger('departamento_id')->nullable();
            $table->foreign('departamento_id')->references('departamento_id')->on('empresa_departamento');
            $table->bigInteger('empleado_id')->nullable();
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');                  
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
        Schema::dropIfExists('activo_fijo');
    }
}
