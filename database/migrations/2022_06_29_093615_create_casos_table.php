<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caso', function (Blueprint $table) {
            $table->id('caso_id');
            $table->date('caso_fecha');

            $table->integer('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('clientes');
            $table->integer('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_caso');
            $table->integer('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');
        
            $table->string('caso_informe_tecnico')->default("");
            $table->integer('caso_proforma_proveedor')->default(0);
            $table->integer('caso_proforma_cliente')->default(0);
            $table->boolean('caso_contrato');
            $table->integer('caso_pedido');
            $table->integer('caso_pedido_estado');

            $table->date('caso_fecha_soporte')->nullable();
            $table->string('caso_informe_instalacion')->default("");
            $table->integer('caso_evaluacion_estado')->default(0);
            $table->date('caso_fecha_mantenimiento');
            $table->integer('caso_estado')->default(1);;
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
        Schema::dropIfExists('caso');
    }
}
