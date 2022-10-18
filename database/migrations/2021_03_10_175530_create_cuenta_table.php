<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta', function (Blueprint $table) {
            $table->id('cuenta_id');
            $table->text('cuenta_numero')->unique();
            $table->text('cuenta_nombre');
            $table->bigInteger('cuenta_secuencial');
            $table->bigInteger('cuenta_nivel');
            $table->text('cuenta_estado');
            $table->bigInteger('cuenta_padre_id')->nullable();
            $table->foreign('cuenta_padre_id')->references('cuenta_id')->on('cuenta');  
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');  
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
        Schema::dropIfExists('cuenta');
    }
}
