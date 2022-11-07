<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoTable extends Migration{
    public function up(){
        Schema::create('pago', function (Blueprint $table) {
            $table->id('pago_id');
            $table->integer('suscripcion_id');
            $table->foreign('suscripcion_id')->references('suscripcion_id')->on('suscripcion');
            $table->integer('plan_id');
            $table->foreign('plan_id')->references('plan_id')->on('plan');
            $table->date('pago_fecha');
            $table->date('pago_fecha_validacion')->nullable();
            $table->string('pago_documento');
            $table->string('pago_comprobante');
            $table->string('pago_banco_nombre');
            $table->string('pago_banco_numero');
            $table->double('pago_valor');
            $table->integer('pago_estado')->default(0);
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('pago');
    }
}
