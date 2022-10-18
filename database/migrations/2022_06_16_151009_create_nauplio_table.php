<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNauplioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nauplio', function (Blueprint $table) {
            $table->id("nauplio_id");
            $table->string("nauplio_nombre");
            $table->string("nauplio_estado");
            $table->integer('laboratorio_id');
            $table->foreign('laboratorio_id')->references('laboratorio_id')->on('laboratorio_camaronera');
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
        Schema::dropIfExists('nauplio');
    }
}
