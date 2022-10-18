<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleAnalisisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_analisis', function (Blueprint $table) {
            $table->id('detalle_id'); 
            $table->string('detalle_estado', 255);
            $table->bigInteger('producto_id');
            $table->string('id_externo', 60)->nullable();
            $table->string('tecnica', 60)->nullable();
            $table->date('fecha_recepcion_muestra')->nullable();
            $table->date('fecha_reporte')->nullable();
            $table->date('fecha_validacion')->nullable();
            $table->string('usuario_validacion', 100)->nullable();
            $table->string('estado', 1)->nullable();


            $table->foreign('producto_id')->references('producto_id')->on('producto'); 
            $table->bigInteger('analisis_laboratorio_id');
            $table->foreign('analisis_laboratorio_id')->references('analisis_laboratorio_id')->on('analisis_laboratorio');   
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
        Schema::dropIfExists('detalle_analisis');
    }
}
