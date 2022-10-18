<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferenciaSiembraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencia_siembra', function (Blueprint $table) {
            $table->id('transferencia_id');
            $table->string("transferencia_codigo")->unique();
            $table->double("transferencia_area");
            $table->date("transferencia_fecha");
            $table->double("transferencia_volumen");
            $table->double("transferencia_cosecha_juvenil");
            $table->double("transferencia_numero_juvenil");
            $table->string("transferencia_peso_juvenil");
            $table->double("transferencia_juvenil");
            $table->double("transferencia_libras");
            $table->double("transferencia_longitud");
            $table->string("transferencia_densidad");
            $table->string("transferencia_cultivo");
            $table->string("transferencia_estado");
            $table->bigInteger('siembra_id');
            $table->foreign('siembra_id')->references('siembra_id')->on('siembra');
            $table->bigInteger('siembra_padre_id');
            $table->foreign('siembra_padre_id')->references('siembra_id')->on('siembra');
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
        Schema::dropIfExists('transferencia_siembra');
    }
}
