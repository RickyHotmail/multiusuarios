<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenMantenimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_mantenimiento', function (Blueprint $table) {
            $table->id('orden_id');
            $table->string('orden_numero')->unique();
            $table->string('orden_serie');
            $table->integer('orden_secuencial');
            $table->date('orden_fecha_inicio');
            $table->date('orden_finalizacion')->nullable();
            $table->string('orden_prioridad');
            $table->string('orden_lugar');
            $table->text('orden_descripcion');
            $table->string('orden_logistica')->nullable();
            $table->string('orden_observacion')->nullable();
            $table->string('orden_resultado');
            $table->string('orden_informe')->nullable();
            $table->string('orden_recibido_por');
            $table->string('orden_estado');
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_mantenimiento');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->bigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');
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
        Schema::dropIfExists('orden_mantenimiento');
    }
}
