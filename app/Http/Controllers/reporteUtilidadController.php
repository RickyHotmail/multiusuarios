<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Movimiento_Producto;
use App\Models\Orden_Despacho;
use App\Models\Producto;
use App\Models\Punto_Emision;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class reporteUtilidadController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.inventario.reportes.utilidadProducto',['productos'=>Producto::productos()->where('producto_compra_venta','=','3')->orwhere('producto_compra_venta','=','2')->get()]);
        }catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {
        try{
            $datos = [];
            $datosP = [];
            if($request->get('productoID') == '0'){
                $datos =  $this->datos($request);
            }else{
                $datosP = $this->calcular(Producto::producto($request->get('productoID'))->first(), $request->get('fecha_desde'), $request->get('fecha_hasta'),1);
            }
            if(is_array($datos)){
                if (isset($_POST['buscar'])){
                    return view('admin.inventario.reportes.utilidadProducto',['datosP'=>$datosP,'productoC'=>$request->get('productoID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'productos'=>Producto::productos()->where('producto_compra_venta','=','3')->orwhere('producto_compra_venta','=','2')->get(),'datos'=>$datos]);        
                }
                if (isset($_POST['pdf'])){
                    $empresa =  Empresa::empresa()->first();
                    $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
                    if (!is_dir($ruta)) {
                        mkdir($ruta, 0777, true);
                    }
                    $view =  \View::make('admin.formatosPDF.utilidadProducto', ['datos'=>$datos,'datosP'=>$datosP,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'empresa'=>$empresa]);
                    $nombreArchivo = 'REPORTE DE UTILIDAD POR PRODUCTO '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
                    return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
                }    
            }else{
                throw new Exception('Error vuelva a intentar.');
            }  
        }catch(\Exception $ex){
            return redirect('utilidadProducto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function datos(Request $request){
        try{
            $datos = [];
            $count = 1;
            $totalVC = 0;
            $totalV = 0;
            $totalU = 0;
            if($request->get('productoID') == '0'){
                $productos = Producto::productos()->where('producto_compra_venta','=','3')->orwhere('producto_compra_venta','=','2')->get();
            }else{
                $productos = Producto::producto($request->get('productoID'))->get();
            }
            foreach($productos as $producto){
                $datos[$count]['gru'] = $producto->grupo->grupo_nombre;
                $datos[$count]['cod'] = $producto->producto_codigo;
                if(isset($producto->sucursal->sucursal_nombre)){
                    $datos[$count]['suc'] = $producto->sucursal->sucursal_nombre;
                }else{
                    $datos[$count]['suc'] = 'TODAS';
                }
                $datos[$count]['nom'] = $producto->producto_nombre;
                $resultado = $this->calcular($producto, $request->get('fecha_desde'), $request->get('fecha_hasta'),0);
                $datos[$count]['can'] = $resultado[0];
                if($producto->producto_tipo == '2'){
                    $datos[$count]['vec'] = 0;
                }else{
                    $datos[$count]['vec'] = $resultado[1];
                }
                $datos[$count]['ven'] = $resultado[2];
                $datos[$count]['uti'] = floatval($datos[$count]['ven']) - floatval($datos[$count]['vec']);
                $totalVC = $totalVC + floatval($datos[$count]['vec']);
                $totalV = $totalV + floatval($datos[$count]['ven']);
                $totalU = $totalU + floatval($datos[$count]['uti']);
                $datos[$count]['venta'] = '';
                if(isset($producto->cuentaVenta->cuenta_numero)){
                    $datos[$count]['venta'] = $producto->cuentaVenta->cuenta_numero;
                }
                $datos[$count]['costo'] = '';
                if(isset($producto->cuentaGasto->cuenta_numero)){
                    $datos[$count]['costo'] = $producto->cuentaGasto->cuenta_numero;
                }
                if($datos[$count]['can'] > 0){
                    $count ++;
                }
            }
            $datos[$count]['gru'] = '';
            $datos[$count]['cod'] = '';
            $datos[$count]['suc'] = '';
            $datos[$count]['nom'] = '';
            $datos[$count]['can'] = '';
            $datos[$count]['vec'] = $totalVC;
            $datos[$count]['ven'] = $totalV;
            $datos[$count]['uti'] = $totalU;
            $datos[$count]['venta'] = '';
            $datos[$count]['costo'] = '';
            $count ++;
            return $datos;
        }catch(\Exception $ex){
            return redirect('utilidadProducto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function calcular($producto, $fechaI, $fechaF,$bandera){
        $datos = null;
        $count = 1;
        $sin_fecha = 0;
        $resultado = [];
        $resultado[0] = 0;
        $resultado[1] = 0;
        $resultado[2] = 0;
        $bandera2 = false;
        if($sin_fecha == 0){
            /***********************SALDO ANTERIOR***********************/
            $datos[$count]['doc'] = '';
            $datos[$count]['num'] = '';
            $datos[$count]['fec'] = '';
            $datos[$count]['can1'] = 0;
            $datos[$count]['pre1'] = 0;
            $datos[$count]['tot1'] = 0;
            $datos[$count]['can2'] = 0;
            $datos[$count]['pre2'] = 0;
            $datos[$count]['tot2'] = 0;
            $datos[$count]['can3'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($fechaI."- 1 days")) )->where('movimiento_tipo','=','ENTRADA')->where('movimiento_motivo', '<>', 'ANULACION')->sum('movimiento_cantidad') 
            - Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($fechaI."- 1 days")) )->where('movimiento_tipo','=','SALIDA')->sum('movimiento_cantidad')
            + Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($fechaI."- 1 days")) )->join('detalle_fv','movimiento_producto.movimiento_id','=','detalle_fv.movimiento_id')->join('factura_venta','detalle_fv.factura_id','=','factura_venta.factura_id')->where('movimiento_tipo','=','SALIDA')->whereNull('factura_venta.diario_id')->sum('movimiento_cantidad');
            $costoPromedio = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($fechaI."- 1 days")))->orderBy('movimiento_fecha','desc')->orderBy('movimiento_id','desc')->first();
            $datos[$count]['pre3'] = 0;
            if($costoPromedio){
                $datos[$count]['pre3'] = $costoPromedio->movimiento_costo_promedio;
            }
            $datos[$count]['tot3'] = floatval($datos[$count]['can3'])*floatval($datos[$count]['pre3']);
            $datos[$count]['dia'] = '';
            $datos[$count]['cos'] = '';
            $datos[$count]['tra'] = '';
            $datos[$count]['ref'] = '';
            $datos[$count]['des'] = '';
            $count ++;
            /***********************************************************/
        }
        foreach(Movimiento_Producto::MovProductoByFecha($producto->producto_id,$fechaI,$fechaF)->orderBy('movimiento_fecha','asc')->orderBy('movimiento_id','asc')->get() as $movimiento){
            $bandera2 = false;
            if($movimiento->movimiento_motivo != "ANULACION"){
                if($movimiento->movimiento_tipo == "SALIDA" and $movimiento->movimiento_motivo == "VENTA" and $movimiento->movimiento_documento == "FACTURA DE VENTA"){
                    if(isset($movimiento->detalle_fv->facturaVenta->diario->diario_id)){
                        $bandera2 = true;
                    }
                }else{
                    $bandera2 = true;
                }
            }
            $datos[$count]['doc'] = $movimiento->movimiento_documento;
            $datos[$count]['num'] = '';
            $datos[$count]['fec'] = $movimiento->movimiento_fecha;
            $datos[$count]['can1'] = 0;
            $datos[$count]['pre1'] = 0;
            $datos[$count]['tot1'] = 0;
            $datos[$count]['can2'] = 0;
            $datos[$count]['pre2'] = 0;
            $datos[$count]['tot2'] = 0;
            if($movimiento->movimiento_tipo == "ENTRADA"){
                $datos[$count]['can1'] = $movimiento->movimiento_cantidad;
                $datos[$count]['pre1'] = $movimiento->movimiento_precio;
                $datos[$count]['tot1'] = $movimiento->movimiento_total;
            }
            if($movimiento->movimiento_tipo == "SALIDA"){
                $datos[$count]['can2'] = $movimiento->movimiento_cantidad;
            }
            if($count-1 > 0){
                $datos[$count]['can3'] = floatval($datos[$count-1]['can3'])+floatval($datos[$count]['can1'])-floatval($datos[$count]['can2']);
            }else{
                $datos[$count]['can3'] = floatval($datos[$count]['can1'])-floatval($datos[$count]['can2']);
            }
            if($movimiento->movimiento_motivo == "COMPRA" or $movimiento->movimiento_motivo == 'AJUSTE DE INVENTARIO'){
                if(floatval($datos[$count]['can3']) <> 0){
                    if($count-1 > 0){
                        $datos[$count]['pre3'] = (floatval($datos[$count-1]['tot3'])+floatval($datos[$count]['tot1']))/floatval($datos[$count]['can3']);
                    }else{
                        $datos[$count]['pre3'] = floatval($datos[$count]['tot1'])/floatval($datos[$count]['can3']);
                    }
                    
                }else{
                    $datos[$count]['pre3'] = 0;
                }
            }else{
                if($count-1 > 0){
                    $datos[$count]['pre3'] = $datos[$count-1]['pre3'];
                }else{
                    $datos[$count]['pre3'] = 0;
                }
            }                    
            $datos[$count]['tot3'] = floatval($datos[$count]['can3'])*round(floatval($datos[$count]['pre3']),4);
            if($movimiento->movimiento_tipo == "SALIDA"){
                $datos[$count]['pre2'] = $datos[$count]['pre3'];
                $datos[$count]['tot2'] = floatval($datos[$count]['can2'])*round(floatval($datos[$count]['pre2']),4);
            }
            if($movimiento->movimiento_tipo == "ENTRADA" and $movimiento->movimiento_motivo == "ANULACION" and $movimiento->movimiento_documento == "FACTURA DE VENTA"){
                $datos[$count]['pre1'] = $datos[$count]['pre3'];
                $datos[$count]['tot1'] = floatval($datos[$count]['can1'])*floatval($datos[$count]['pre1']);
            }
            if($movimiento->movimiento_tipo == "ENTRADA" and $movimiento->movimiento_motivo == "VENTA" and $movimiento->movimiento_documento == "NOTA DE CRÉDITO"){
                $datos[$count]['pre1'] = $datos[$count]['pre3'];
                $datos[$count]['tot1'] = floatval($datos[$count]['can1'])*floatval($datos[$count]['pre1']);
            }
            if($movimiento->movimiento_tipo == "SALIDA" and $movimiento->movimiento_motivo == "ANULACION" and $movimiento->movimiento_documento == "NOTA DE CRÉDITO"){
                $datos[$count]['pre2'] = $datos[$count]['pre3'];
                $datos[$count]['tot2'] = floatval($datos[$count]['can2'])*floatval($datos[$count]['pre2']);
            }
            if($movimiento->detalle_fv){
                $datos[$count]['num'] = $movimiento->detalle_fv->facturaVenta->factura_numero;
            }
            if($bandera2){
                if($movimiento->movimiento_tipo == "SALIDA" and $movimiento->movimiento_motivo == "VENTA" and $movimiento->movimiento_documento == "FACTURA DE VENTA"){
                    $resultado [0] = $resultado[0] + round($datos[$count]['can2'],2); 
                    $resultado [1] = $resultado[1] + round($datos[$count]['tot2'],2); 
                    $resultado [2] = $resultado[2] + round($movimiento->movimiento_total,2); 
                }
                if($movimiento->movimiento_tipo == "ENTRADA" and $movimiento->movimiento_motivo == "ANULACION" and $movimiento->movimiento_documento == "FACTURA DE VENTA"){
                    $resultado [0] = $resultado[0] - round($datos[$count]['can1'],2); 
                    $resultado [1] = $resultado[1] - round($datos[$count]['tot1'],2); 
                    $resultado [2] = $resultado[2] - round($movimiento->movimiento_total,2); 
                }
                if($movimiento->movimiento_tipo == "ENTRADA" and $movimiento->movimiento_motivo == "VENTA" and $movimiento->movimiento_documento == "NOTA DE CRÉDITO"){
                    $resultado [0] = $resultado[0] - round($datos[$count]['can1'],2); 
                    $resultado [1] = $resultado[1] - round($datos[$count]['tot1'],2); 
                    $resultado [2] = $resultado[2] - round($movimiento->movimiento_total,2); 
                }
                if($movimiento->movimiento_tipo == "SALIDA" and $movimiento->movimiento_motivo == "ANULACION" and $movimiento->movimiento_documento == "NOTA DE CRÉDITO"){
                    $resultado [0] = $resultado[0] + round($datos[$count]['can2'],2); 
                    $resultado [1] = $resultado[1] + round($datos[$count]['tot2'],2); 
                    $resultado [2] = $resultado[2] + round($movimiento->movimiento_total,2); 
                }
                $count ++;
            }
        }
        foreach(Orden_Despacho::OrdenesReserva()->join('factura_venta','factura_venta.factura_id','=','orden_despacho.factura_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('movimiento_producto','movimiento_producto.movimiento_id','=','detalle_orden.movimiento_id')
        ->select('movimiento_producto.movimiento_cantidad','movimiento_producto.movimiento_costo_promedio','movimiento_producto.movimiento_total')
        ->where('movimiento_producto.producto_id','=',$producto->producto_id)->where('orden_despacho.orden_reserva','=','1')
        ->where('factura_venta.factura_fecha','>=',$fechaI)->where('factura_venta.factura_fecha','<=',$fechaF)->get() as $movimiento){
            $resultado [0] = $resultado[0] + round($movimiento->movimiento_cantidad,2); 
            $valor = floatval($movimiento->movimiento_costo_promedio) * floatval($movimiento->movimiento_cantidad);
            $resultado [1] = $resultado[1] + round($valor,2); 
            $resultado [2] = $resultado[2] + round($movimiento->movimiento_total,2); 
        }
        if($bandera == 0){
            return $resultado;
        }else{
            return $datos;
        }
    }
}
