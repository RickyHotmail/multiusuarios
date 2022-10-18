<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->id('producto_id');
            $table->string('producto_codigo')->unique();
            $table->char('producto_codigo_referencial', 20)->nullable(); 
            $table->string('producto_nombre');      
            $table->string('producto_codigo_barras');       
            $table->string('producto_tipo');       
            $table->double('producto_precio_costo');           
            $table->float('producto_stock');
            $table->float('producto_stock_minimo');       
            $table->float('producto_stock_maximo');             
            $table->date('producto_fecha_ingreso');       
            $table->string('producto_tiene_iva');       
            $table->string('producto_tiene_descuento');       
            $table->string('producto_tiene_serie');          
            $table->string('producto_compra_venta');
            $table->double('producto_precio1');
            $table->string('producto_estado');
            /** Se relacionan con la tabla Cuenta*/            
            $table->bigInteger('producto_cuenta_inventario')->nullable();
            $table->foreign('producto_cuenta_inventario')->references('cuenta_id')->on('cuenta');         
            $table->bigInteger('producto_cuenta_venta')->nullable();
            $table->foreign('producto_cuenta_venta')->references('cuenta_id')->on('cuenta');  
            $table->bigInteger('producto_cuenta_gasto')->nullable();
            $table->foreign('producto_cuenta_gasto')->references('cuenta_id')->on('cuenta');  
            /* otras relaciones*/    
            $table->bigInteger('categoria_id');
            $table->foreign('categoria_id')->references('categoria_id')->on('categoria_producto'); 
            $table->bigInteger('marca_id');
            $table->foreign('marca_id')->references('marca_id')->on('marca_producto'); 
            $table->bigInteger('unidad_medida_id');
            $table->foreign('unidad_medida_id')->references('unidad_medida_id')->on('unidad_medida_producto'); 
            $table->bigInteger('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');      
            $table->bigInteger('tamano_id');
            $table->foreign('tamano_id')->references('tamano_id')->on('tamano_producto'); 
            $table->bigInteger('grupo_id');
            $table->foreign('grupo_id')->references('grupo_id')->on('grupo_producto'); 
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
        Schema::dropIfExists('producto');
    }
}
