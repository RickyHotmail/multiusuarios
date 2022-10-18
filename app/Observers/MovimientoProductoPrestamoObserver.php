<?php

namespace App\Observers;

use App\Models\Cliente;
use App\Models\Movimiento_Prestamo_Producto;

class MovimientoProductoPrestamoObserver
{
    /**
     * Handle the Movimiento_Prestamo_Producto "created" event.
     *
     * @param  \App\Models\Movimiento_Prestamo_Producto  $movimiento_Prestamo_Producto
     * @return void
     */
    public function created(Movimiento_Prestamo_Producto $movimiento_Prestamo_Producto)
    {
        if($movimiento_Prestamo_Producto->producto->producto_tipo == '1'){
            $cliente= Cliente::findOrFail($movimiento_Prestamo_Producto->cliente_id);
            $cliente->cliente_prestamo = Movimiento_Prestamo_Producto::MovProductoByFechaCorte($movimiento_Prestamo_Producto->movimiento_fecha)->where('movimiento_tipo','=','SALIDA')->sum('movimiento_valor')-Movimiento_Prestamo_Producto::MovProductoByFechaCorte($movimiento_Prestamo_Producto->movimiento_fecha)->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_valor');
            $cliente->update();
        }
    }

    /**
     * Handle the Movimiento_Prestamo_Producto "updated" event.
     *
     * @param  \App\Models\Movimiento_Prestamo_Producto  $movimiento_Prestamo_Producto
     * @return void
     */
    public function updated(Movimiento_Prestamo_Producto $movimiento_Prestamo_Producto)
    {
        //
    }

    /**
     * Handle the Movimiento_Prestamo_Producto "deleted" event.
     *
     * @param  \App\Models\Movimiento_Prestamo_Producto  $movimiento_Prestamo_Producto
     * @return void
     */
    public function deleted(Movimiento_Prestamo_Producto $movimiento_Prestamo_Producto)
    {
        if($movimiento_Prestamo_Producto->producto->producto_tipo == '1'){
            $cliente= Cliente::findOrFail($movimiento_Prestamo_Producto->cliente_id);
            $cliente->cliente_prestamo = Movimiento_Prestamo_Producto::MovProductoByFechaCorte($movimiento_Prestamo_Producto->movimiento_fecha)->where('movimiento_tipo','=','SALIDA')->sum('movimiento_valor')-Movimiento_Prestamo_Producto::MovProductoByFechaCorte($movimiento_Prestamo_Producto->movimiento_fecha)->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_valor');
            $cliente->update();
        }
    }

    /**
     * Handle the Movimiento_Prestamo_Producto "restored" event.
     *
     * @param  \App\Models\Movimiento_Prestamo_Producto  $movimiento_Prestamo_Producto
     * @return void
     */
    public function restored(Movimiento_Prestamo_Producto $movimiento_Prestamo_Producto)
    {
        //
    }

    /**
     * Handle the Movimiento_Prestamo_Producto "force deleted" event.
     *
     * @param  \App\Models\Movimiento_Prestamo_Producto  $movimiento_Prestamo_Producto
     * @return void
     */
    public function forceDeleted(Movimiento_Prestamo_Producto $movimiento_Prestamo_Producto)
    {
        //
    }
}
