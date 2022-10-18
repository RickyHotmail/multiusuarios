<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValorLaboratorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valor_laboratorio', function (Blueprint $table) {
            $table->id('valor_id');          
            $table->string('valor_nombre');
            $table->string('valor_estado');
            $table->bigInteger('detalle_id');
            $table->foreign('detalle_id')->references('detalle_id')->on('detalle_laboratorio'); 
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
        Schema::dropIfExists('valor_laboratorio');
    }
}
