<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking', function (Blueprint $table) {
            $table->id('tracking_id');
            $table->date('tracking_fecha');
            $table->string('tracking_origen');
            $table->string('tracking_destino');
            $table->integer('tracking_estado');
            $table->integer('caso_id');
            $table->foreign('caso_id')->references('caso_id')->on('caso');
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
        Schema::dropIfExists('tracking');
    }
}
