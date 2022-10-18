<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCierreMesContableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cierre_mes_contable', function (Blueprint $table) {
            $table->id('cierre_id');
            $table->string('cierre_ano');
            $table->string('cierre_01');
            $table->string('cierre_02');
            $table->string('cierre_03');
            $table->string('cierre_04');
            $table->string('cierre_05');
            $table->string('cierre_06');
            $table->string('cierre_07');
            $table->string('cierre_08');
            $table->string('cierre_09');
            $table->string('cierre_10');
            $table->string('cierre_11');
            $table->string('cierre_12');
            $table->string('cierre_estado');
            $table->integer('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');
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
        Schema::dropIfExists('_cierre__mes__contable');
    }
}
