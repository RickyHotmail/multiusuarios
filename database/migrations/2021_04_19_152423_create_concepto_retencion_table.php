<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConceptoRetencionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concepto_retencion', function (Blueprint $table) {
            $table->id('concepto_id');
            $table->string('concepto_nombre');
            $table->string('concepto_codigo');
            $table->decimal('concepto_porcentaje', $precision = 19, $scale = 2);
            $table->string('concepto_tipo');
            $table->string('concepto_objeto');
            $table->string('concepto_estado');              
            //datos foraneos
            $table->bigInteger('concepto_emitida_cuenta');
            $table->foreign('concepto_emitida_cuenta')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('concepto_recibida_cuenta');
            $table->foreign('concepto_recibida_cuenta')->references('cuenta_id')->on('cuenta');
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
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
        Schema::dropIfExists('concepto_retencion');
    }
}
