<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductoMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto_movil', function (Blueprint $table) {
            $table->id('producto_id');
            $table->string('producto_codigo_principal', 15);
            $table->string('producto_codigo_auxiliar', 20)->nullable();
            $table->integer('producto_tipo')->default(1);
            $table->string('producto_nombre', 200);
            $table->double('producto_valor_unitario');
            $table->integer('producto_grava_iva')->default(0);
            $table->integer('producto_grava_ice')->default(0);
            $table->integer('producto_grava_irbpnr');
            $table->string('producto_atributo1', 100)->nullable();
            $table->string('producto_descripcion1', 100)->nullable();
            $table->string('producto_atributo2', 100)->nullable();
            $table->string('producto_descripcion2', 100)->nullable();
            $table->string('producto_atributo3', 100)->nullable();
            $table->string('producto_descripcion3', 100)->nullable();
            $table->integer('producto_estado')->default(1);
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
        Schema::dropIfExists('producto_movil');
    }
}