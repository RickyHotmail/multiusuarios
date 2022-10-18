<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiscinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piscina', function (Blueprint $table) {
            $table->id("piscina_id");
            $table->string("piscina_codigo")->unique();
            $table->bigInteger("piscina_secuencial");
            $table->string("piscina_nombre");
            $table->double("piscina_largo");
            $table->double("piscina_ancho");
            $table->double("piscina_columna_agua");
            $table->double("piscina_espejo_agua");
            $table->double("piscina_declinacion");
            $table->double("piscina_volumen_agua");
            $table->double("piscina_entrada_agua");
            $table->double("piscina_salida_agua");
            $table->string("piscina_tipo_estado");
            $table->string("piscina_estado");
            $table->bigInteger('camaronera_id');
            $table->foreign('camaronera_id')->references('camaronera_id')->on('camaronera');
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
        Schema::dropIfExists('piscina');
    }
}
