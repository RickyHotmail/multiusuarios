<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuscripcionTable extends Migration{
    public function up(){
        Schema::create('suscripcion', function (Blueprint $table) {
            $table->id('suscripcion_id');
            $table->integer('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->integer('plan_id');
            $table->foreign('plan_id')->references('plan_id')->on('plan');
            $table->date('suscripcion_fecha_inicio');
            $table->date('suscripcion_fecha_finalizacion');
            $table->integer('suscripcion_cantidad_generado')->default(0);
            $table->string('suscripcion_permiso')->default('4');
            $table->integer('suscripcion_ilimitada')->default(0);
            $table->integer('suscripcion_estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('suscripcion');
    }
}
