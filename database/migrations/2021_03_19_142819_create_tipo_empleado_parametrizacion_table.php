<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoEmpleadoParametrizacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_empleado_parametrizacion', function (Blueprint $table) {
            $table->id('parametrizacion_id');
            $table->string('parametrizacion_estado');
            $table->bigInteger('cuenta_debe')->nullable();
            $table->foreign('cuenta_debe')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('cuenta_haber')->nullable();
            $table->foreign('cuenta_haber')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_empleado');
            $table->bigInteger('rubro_id');
            $table->foreign('rubro_id')->references('rubro_id')->on('rubro');
            $table->bigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('categoria_id')->on('categoria_rol');
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
        Schema::dropIfExists('tipo_empleado_parametrizacion');
    }
}
