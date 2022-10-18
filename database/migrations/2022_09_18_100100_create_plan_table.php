<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanTable extends Migration{
    public function up(){
        Schema::create('plan', function (Blueprint $table) {
            $table->id('plan_id');
            $table->string('plan_nombre');
            $table->integer('plan_cantidad_documentos');
            $table->string('plan_tiempo');
            $table->integer('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('plan');
    }
}