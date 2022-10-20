<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizacionPermisoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametrizacion_permiso', function (Blueprint $table) {
            $table->id('parametrizacionp_id');
            $table->integer('permiso_id');
            $table->foreign('permiso_id')->references('permiso_id')->on('permiso');
            $table->integer('parametrizacionp_general')->default(1);
            $table->integer('parametrizacionp_medico')->default(1);
            $table->integer('parametrizacionp_camaronero')->default(1);
            $table->integer('parametrizacionp_facturacion')->default(1);
            $table->integer('parametrizacionp_estado')->default(1);
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
        Schema::dropIfExists('parametrizacion_permiso');
    }
}
