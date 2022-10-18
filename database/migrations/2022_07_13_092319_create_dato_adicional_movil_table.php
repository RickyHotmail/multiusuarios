<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatoAdicionalMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dato_adicional_movil', function (Blueprint $table) {
            $table->id('datoa_id');
            $table->string('datoa_nombre', 100);
            $table->string('datoa_descripcion', 200);
            $table->integer('datoa_estado')->default(1);
            $table->integer('factura_id');
            $table->foreign('factura_id')->references('factura_id')->on('factura_movil');
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
        Schema::dropIfExists('dato_adicional_movil');
    }
}
