<?php

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Costo extends Command
{

    protected $signature = 'costo:actualizar';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $fechaActual = date('d-m-Y');
            $productos=DB::table('producto')->where('producto_estado','=','1')->get();
            foreach ($productos as $producto) {
                $can=DB::table('movimiento_producto')->where('movimiento_producto.producto_id','=',$producto->producto_id)->where('movimiento_fecha','<=',$fechaActual)->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_cantidad');
                $prec=DB::table('movimiento_producto')->where('movimiento_producto.producto_id','=',$producto->producto_id)->where('movimiento_fecha','<=',$fechaActual)->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_total');
                $can2=DB::table('movimiento_producto')->where('movimiento_producto.producto_id','=',$producto->producto_id)->where('movimiento_fecha','<=',$fechaActual)->where('movimiento_tipo','=','SALIDA')->sum('movimiento_cantidad');
                $precio=DB::table('movimiento_producto')->where('movimiento_producto.producto_id','=',$producto->producto_id)->where('movimiento_fecha','<=',$fechaActual)->where('movimiento_tipo','=','ENTRADA')->where('movimiento_motivo','=','COMPRA')->orderBy('movimiento_fecha', 'desc')->first();
                if($can > 0 && $prec > 0){
                    $producto=Producto::findOrFail($producto->producto_id);
                    $producto->producto_precio_costo = 0;
                    if ($producto->producto_tipo=='1') {
                        $producto->producto_stock = $can-$can2;
                        $producto->producto_precio_costo = (round(floatval($precio->movimiento_precio), 2));  
                    }
                    $producto->save();
                   
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        // El método line () es un método que viene con la clase de comando, que puede generar nuestra información personalizada
        $this->line('calculate Data Success!');
    }
}
