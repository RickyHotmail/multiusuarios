<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaCobrarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_cobrar', function (Blueprint $table) {
            $table->id('cuenta_id');
            $table->text('cuenta_descripcion');
            $table->string('cuenta_tipo');
            $table->date('cuenta_fecha');
            $table->date('cuenta_fecha_inicio');
            $table->date('cuenta_fecha_fin');
            $table->double('cuenta_monto', 8, 4);
            $table->double('cuenta_saldo', 8, 4);
            $table->double('cuenta_valor_factura', 8, 4);
            $table->string('cuenta_cheque_anticipado')->nullable();
            $table->string('cuenta_banco_anticipado')->nullable();
            $table->string('cuenta_estado');
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('cliente_id')->on('cliente'); 
            $table->bigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal'); 
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
        Schema::dropIfExists('cuenta_cobrar');
    }
}
