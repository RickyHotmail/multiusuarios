<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaccionCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaccion_compra', function (Blueprint $table) {
            $table->id('transaccion_id');
            $table->date('transaccion_fecha');
            $table->date('transaccion_caducidad');
            $table->date('transaccion_impresion');
            $table->date('transaccion_vencimiento');
            $table->date('transaccion_inventario');
            $table->string('transaccion_numero');
            $table->string('transaccion_serie');
            $table->bigInteger('transaccion_secuencial');
            $table->double('transaccion_subtotal');
            $table->double('transaccion_descuento');
            $table->double('transaccion_tarifa0');
            $table->double('transaccion_tarifa12');
            $table->double('transaccion_iva');
            $table->double('transaccion_total');
            $table->double('transaccion_ivaB');
            $table->double('transaccion_ivaS');
            $table->bigInteger('transaccion_dias_plazo');
            $table->text('transaccion_descripcion');
            $table->string('transaccion_tipo_pago');
            $table->float('transaccion_porcentaje_iva');
            $table->string('transaccion_autorizacion');
            $table->string('transaccion_estado');
            $table->bigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedor');
            $table->bigInteger('tipo_comprobante_id');
            $table->foreign('tipo_comprobante_id')->references('tipo_comprobante_id')->on('tipo_comprobante');
            $table->bigInteger('sustento_id');
            $table->foreign('sustento_id')->references('sustento_id')->on('sustento_tributario');
            $table->bigInteger('diario_id')->nullable();
            $table->foreign('diario_id')->references('diario_id')->on('diario');
            $table->bigInteger('forma_pago_id')->nullable();
            $table->foreign('forma_pago_id')->references('forma_pago_id')->on('forma_pago');
            $table->bigInteger('cuenta_id')->nullable();
            $table->foreign('cuenta_id')->references('cuenta_id')->on('cuenta_pagar');
            $table->bigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('sucursal_id')->on('sucursal');
            $table->bigInteger('transaccion_id_f')->nullable();
            $table->foreign('transaccion_id_f')->references('transaccion_id')->on('transaccion_compra');
            $table->bigInteger('arqueo_id')->nullable();
            $table->foreign('arqueo_id')->references('arqueo_id')->on('arqueo_caja');
            $table->text('transaccion_factura_manual');
            $table->text('transaccion_autorizacion_manual');
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
        Schema::dropIfExists('transaccion_compra');
    }
}
