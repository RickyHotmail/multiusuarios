<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArqueoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arqueo_caja', function (Blueprint $table) {
            $table->id('arqueo_id');          
            $table->date('arqueo_fecha');
            $table->time('arqueo_hora');
            $table->string('arqueo_observacion');
            $table->string('arqueo_tipo');
            $table->float('arqueo_saldo_inicial',9,2);
            $table->float('arqueo_monto',9,2);
            $table->integer('arqueo_billete1');
            $table->integer('arqueo_billete5');
            $table->integer('arqueo_billete10');
            $table->integer('arqueo_billete20');
            $table->integer('arqueo_billete50');
            $table->integer('arqueo_billete100');
            $table->integer('arqueo_moneda01');
            $table->integer('arqueo_moneda05');
            $table->integer('arqueo_moneda10');
            $table->integer('arqueo_moneda25');
            $table->integer('arqueo_moneda50');
            $table->integer('arqueo_moneda1');
            $table->string('arqueo_estado');         
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->bigInteger('caja_id');
            $table->foreign('caja_id')->references('caja_id')->on('caja');
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->bigInteger('cierre_id')->nullable();
            $table->foreign('cierre_id')->references('arqueo_id')->on('arqueo_caja');
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
        Schema::dropIfExists('arqueo_caja');
    }
}
