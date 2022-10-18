<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmisorMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emisor_movil', function (Blueprint $table) {
            $table->id('emisor_id');
            $table->string('emisor_ruc', 13);
            $table->string('emisor_razon_social', 300);
            $table->string('emisor_nombre_comercial', 300);
            $table->string('emisor_direccion_matriz', 300);
            $table->string('emisor_direccion_establecimiento', 300);
            $table->string('emisor_codigo_establecimiento', 3);
            $table->string('emisor_codigo_punto_emision', 3);
            $table->string('emisor_contribuyente_especial_resolucion', 5)->nullable();
            $table->integer('emisor_lleva_contabilidad')->default(0);
            $table->integer('emisor_contribuyente_rimpe')->default(0);
            $table->string('emisor_agente_retencion', 8)->nullable();
            $table->string('emisor_logo')->nullable()->default('');
            $table->integer('emisor_tiempo_espera')->default(50);
            $table->integer('emisor_ambiente')->default(50);
            $table->integer('emisor_tipo_token');
            $table->integer('tipo_estado')->default(1);
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
        Schema::dropIfExists('emisor_movil');
    }
}
