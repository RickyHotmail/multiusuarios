<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiembraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siembra', function (Blueprint $table) {
            $table->id("siembra_id");
            $table->string("siembra_codigo")->unique();
            $table->bigInteger("siembra_secuencial");
            $table->string("siembra_larvas");
            $table->string("siembra_entregas");
            $table->date("siembra_fecha");
            $table->date("siembra_fecha_costo");
            $table->date("siembra_fecha_siembra");
            $table->double("siembra_longitud");
            $table->double("siembra_peso");
            $table->double("siembra_densidad");
            $table->string("siembra_cultivo");
            $table->double("siembra_precio_larva");
            $table->string("siembra_estado");
            $table->bigInteger('piscina_id');
            $table->foreign('piscina_id')->references('piscina_id')->on('piscina');
            $table->bigInteger('laboratorio_id');
            $table->foreign('laboratorio_id')->references('laboratorio_id')->on('laboratorio_camaronera');
            $table->bigInteger('nauplio_id');
            $table->foreign('nauplio_id')->references('nauplio_id')->on('nauplio');
            $table->bigInteger('siembra_ref_id')->nullable();
            $table->foreign('siembra_ref_id')->references('siembra_id')->on('siembra');
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
        Schema::dropIfExists('siembra');
    }
}
