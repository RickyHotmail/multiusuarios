<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleDiarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_diario', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->double('detalle_debe', 8, 4);
            $table->double('detalle_haber', 8, 4);
            $table->text('detalle_comentario');
            $table->string('detalle_tipo_documento');
            $table->string('detalle_numero_documento');
            $table->date('detalle_fecha_conciliacion')->nullable();
            $table->string('detalle_conciliacion');
            $table->string('detalle_estado');
            $table->bigInteger('diario_id');
            $table->foreign('diario_id')->references('diario_id')->on('diario'); 
            $table->bigInteger('cuenta_id');
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta'); 
            $table->bigInteger('cliente_id')->nullable();

            $table->foreign('cliente_id')->references('cliente_id')->on('cliente'); 
            $table->bigInteger('proveedor_id')->nullable();
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedor');
            $table->bigInteger('empleado_id')->nullable();
            $table->foreign('empleado_id')->references('empleado_id')->on('empleado');
            $table->bigInteger('movimiento_id')->nullable();
            $table->foreign('movimiento_id')->references('movimiento_id')->on('movimiento_producto');

            $table->bigInteger('transferencia_id')->nullable();
            $table->foreign('transferencia_id')->references('transferencia_id')->on('transferencia');
            $table->bigInteger('cheque_id')->nullable();
            $table->foreign('cheque_id')->references('cheque_id')->on('cheque');
            $table->bigInteger('deposito_id')->nullable();
            $table->foreign('deposito_id')->references('deposito_id')->on('deposito');
            $table->bigInteger('voucher_id')->nullable();
            $table->foreign('voucher_id')->references('voucher_id')->on('voucher');
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
        Schema::dropIfExists('detalle_diario');
    }
}
