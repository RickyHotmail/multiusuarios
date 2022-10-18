<?php

namespace App\Observers;

use App\Http\Controllers\generalController;
use App\Models\Movimiento_Producto;
use App\Models\Producto;

class MovimientoProductoObserver
{
    /**
     * Handle the Movimiento_Producto "created" event.
     *
     * @param  \App\Models\Movimiento_Producto  $movimiento_Producto
     * @return void
     */
    public function created(Movimiento_Producto $movimiento_Producto)
    {
        $producto = Producto::producto($movimiento_Producto->producto_id)->first();
        if($producto->producto_tipo == '1'){
            $producto->producto_stock = Movimiento_Producto::MovProductoByFechaCorte($movimiento_Producto->producto_id,$movimiento_Producto->movimiento_fecha)->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_cantidad')-Movimiento_Producto::MovProductoByFechaCorte($movimiento_Producto->producto_id,$movimiento_Producto->movimiento_fecha)->where('movimiento_tipo','=','SALIDA')->sum('movimiento_cantidad');
            $movimiento_Producto->movimiento_stock_actual = $producto->producto_stock;
            $movimiento_Producto->update();
            if($movimiento_Producto->movimiento_motivo == 'COMPRA'){
                $general = new generalController();
                $producto->producto_precio_costo = $general->preciocosto('',$movimiento_Producto->movimiento_fecha,$producto->producto_id);
            }        
            $producto->update();
            $movimiento_Producto->movimiento_costo_promedio = $producto->producto_precio_costo;
            $movimiento_Producto->update();
        }
    }

    /**
     * Handle the Movimiento_Producto "updated" event.
     *
     * @param  \App\Models\Movimiento_Producto  $movimiento_Producto
     * @return void
     */
    public function updated(Movimiento_Producto $movimiento_Producto)
    {

    }

    /**
     * Handle the Movimiento_Producto "deleted" event.
     *
     * @param  \App\Models\Movimiento_Producto  $movimiento_Producto
     * @return void
     */
    public function deleted(Movimiento_Producto $movimiento_Producto)
    {
        $producto = Producto::producto($movimiento_Producto->producto_id)->first();
        if($producto->producto_tipo == '1'){
            $producto->producto_stock = Movimiento_Producto::MovProductoByFechaCorte($movimiento_Producto->producto_id,$movimiento_Producto->movimiento_fecha)->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_cantidad')-Movimiento_Producto::MovProductoByFechaCorte($movimiento_Producto->producto_id,$movimiento_Producto->movimiento_fecha)->where('movimiento_tipo','=','SALIDA')->sum('movimiento_cantidad');
            if($movimiento_Producto->movimiento_motivo == 'COMPRA'){
                $general = new generalController();
                $producto->producto_precio_costo = $general->preciocosto('',$movimiento_Producto->movimiento_fecha,$producto->producto_id);
            }        
            $producto->update();
        }
    }

    /**
     * Handle the Movimiento_Producto "restored" event.
     *
     * @param  \App\Models\Movimiento_Producto  $movimiento_Producto
     * @return void
     */
    public function restored(Movimiento_Producto $movimiento_Producto)
    {

    }

    /**
     * Handle the Movimiento_Producto "force deleted" event.
     *
     * @param  \App\Models\Movimiento_Producto  $movimiento_Producto
     * @return void
     */
    public function forceDeleted(Movimiento_Producto $movimiento_Producto)
    {

    }
}
