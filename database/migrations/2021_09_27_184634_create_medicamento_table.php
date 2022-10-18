<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicamentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicamento', function (Blueprint $table) {
            $table->id('medicamento_id');          
            $table->string('medicamento_composicion');
            $table->string('medicamento_indicacion');
            $table->string('medicamento_contraindicacion');
            $table->string('medicamento_estado');
            $table->bigInteger('tipo_id');
            $table->foreign('tipo_id')->references('tipo_id')->on('tipo_medicamento'); 
            $table->bigInteger('producto_id')->unique();
            $table->foreign('producto_id')->references('producto_id')->on('producto'); 
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
        Schema::dropIfExists('medicamento');
    }
}
