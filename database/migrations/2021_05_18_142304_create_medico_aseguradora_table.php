<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicoAseguradoraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medico_aseguradora', function (Blueprint $table) {
            $table->id('aseguradoraM_id');
            $table->string('aseguradoraM_estado');
            //datos foraneos
            $table->bigInteger('medico_id');
            $table->foreign('medico_id')->references('medico_id')->on('medico');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente');

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
        Schema::dropIfExists('medico_aseguradora');
    }
}
