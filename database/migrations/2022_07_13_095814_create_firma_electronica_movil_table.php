<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmaElectronicaMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firma_electronica_movil', function (Blueprint $table) {
            $table->id('firmae_id');
            $table->date('firmae_fecha');
            $table->string('firmae_archivo');
            $table->integer('firmae_disponibilidad');
            $table->integer('firmae_ambiente');
            $table->integer('firmae_estado')->default(1);
            $table->integer('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente_movil');
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
        Schema::dropIfExists('firma_electronica_movil');
    }
}
