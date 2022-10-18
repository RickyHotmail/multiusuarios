<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaBancariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_bancaria', function (Blueprint $table) {
            $table->id('cuenta_bancaria_id');
            $table->string('cuenta_bancaria_numero');
            $table->string('cuenta_bancaria_tipo');
            $table->decimal('cuenta_bancaria_saldo_inicial', $precision = 19, $scale = 2);             
            $table->string('cuenta_bancaria_jefe');
            $table->string('cuenta_bancaria_estado');               
            //datos foraneos
            $table->bigInteger('banco_id');
            $table->foreign('banco_id')->references('banco_id')->on('banco');
            $table->bigInteger('cuenta_id');
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta');                    
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
        Schema::dropIfExists('cuenta_bancaria');
    }
}
