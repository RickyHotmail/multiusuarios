<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalisisLaboratorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analisis_laboratorio', function (Blueprint $table) {
            $table->id('analisis_laboratorio_id'); 
            $table->string('analisis_numero');
            $table->string('analisis_serie');
            $table->float('analisis_secuencial');  
            $table->date('analisis_fecha');  
            $table->string('analisis_otros');
            $table->string('analisis_observacion');
            $table->string('analisis_estado');
            $table->bigInteger('factura_id');
            $table->foreign('factura_id')->references('factura_id')->on('factura_venta');
            $table->bigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');
            $table->bigInteger('orden_id')->nullable();
            $table->foreign('orden_id')->references('orden_id')->on('orden_examen');
            $table->bigInteger('orden_particular_id')->nullable();
            $table->foreign('orden_particular_id')->references('orden_id')->on('orden_examen_particular');
            $table->bigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('user_id')->on('users');
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
        Schema::dropIfExists('analisis_laboratorio');
    }
}
