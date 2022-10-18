<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Categoria_Producto;
use App\Models\Empresa;
use App\Models\Movimiento_Producto;
use App\Models\Producto;
use App\Models\Punto_Emision;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Excel;
use App\NEOPAGUPA\ViewExcel;

class kardexController extends Controller
{
    public function nuevo()
    {
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.inventario.kardex.index',['productos'=>Producto::productos()->where('producto_compra_venta','=','3')->get(),'categorias'=>Categoria_Producto::categorias()->get(),'bodegas'=>Bodega::bodegas()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {   
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['excel'])){
            return $this->excel($this->datos($this->datosProcesar($request)),$request->get('radioKardex'));
            //return $this->excel($this->datos($request,$request->get('radioKardex')),$request->get('radioKardex'));
        }
        if (isset($_POST['pdf'])){
            return $this->pdf($this->datos($request,$request->get('radioKardex')),$request->get('radioKardex'),$request->get('fecha_desde'),$request->get('fecha_hasta'));
        }
    }
    public function datosProcesar(Request $request){
        try{
            $datos = null;
            $count = 1;
            $saldo_cero = 0;
            if ($request->get('saldo_cero') == "on"){
                $saldo_cero = 1; 
            }
            if($request->get('productoID') == '0'){
                if($request->get('categoriaID') == '0'){
                    $productos = Producto::productos()->where('producto_compra_venta','=','3')->get();
                }else{
                    $productos = Producto::productos()->join('categoria_producto','categoria_producto.categoria_id','=','producto.categoria_id')->where('producto_compra_venta','=','3')->where('producto.categoria_id','=',$request->get('categoriaID'))->get();
                }
            }else{
                $productos = Producto::producto($request->get('productoID'))->get();
            }
            foreach($productos as $producto){
                if($request->get('radioKardex') == '1'){
                    $datos[$count]['cod'] = $producto->producto_codigo;
                    $datos[$count]['nom'] = $producto->producto_nombre;
                    $datos[$count]['fec'] = '';
                    if($request->get('bodegaID') == '0'){
                        $datos[$count]['can1'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,$request->get('fecha_hasta'))->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_cantidad');
                        $datos[$count]['tot1'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,$request->get('fecha_hasta'))->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_total');
                    }else{
                        $datos[$count]['can1'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,$request->get('fecha_hasta'))->where('movimiento_tipo','=','ENTRADA')->where('bodega_id','=',$request->get('bodegaID'))->sum('movimiento_cantidad');
                        $datos[$count]['tot1'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,$request->get('fecha_hasta'))->where('movimiento_tipo','=','ENTRADA')->where('bodega_id','=',$request->get('bodegaID'))->sum('movimiento_total');
                    }
                    $datos[$count]['pre1'] = 0;
                    if($datos[$count]['can1'] > 0 && $datos[$count]['tot1'] > 0){
                        $datos[$count]['pre1'] = floatval($datos[$count]['tot1'])/floatval($datos[$count]['can1']);
                    }
                    if($request->get('bodegaID') == '0'){
                        $datos[$count]['can2'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,$request->get('fecha_hasta'))->where('movimiento_tipo','=','SALIDA')->sum('movimiento_cantidad');
                        $datos[$count]['tot2'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,$request->get('fecha_hasta'))->where('movimiento_tipo','=','SALIDA')->sum('movimiento_total');;
                    }else{
                        $datos[$count]['can2'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,$request->get('fecha_hasta'))->where('movimiento_tipo','=','SALIDA')->where('bodega_id','=',$request->get('bodegaID'))->sum('movimiento_cantidad');
                        $datos[$count]['tot2'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,$request->get('fecha_hasta'))->where('movimiento_tipo','=','SALIDA')->where('bodega_id','=',$request->get('bodegaID'))->sum('movimiento_total');;
                    }
                    $datos[$count]['pre2'] = '0';
                    if($datos[$count]['can2'] > 0 && $datos[$count]['tot2'] > 0){
                        $datos[$count]['pre2'] = floatval($datos[$count]['tot2'])/floatval($datos[$count]['can2']);
                    }
                    $datos[$count]['can3'] = floatval($datos[$count]['can1'])-floatval($datos[$count]['can2']);
                    $datos[$count]['pre3'] = floatval($datos[$count]['can3'])*round(floatval($datos[$count]['pre1']),2);
                    $datos[$count]['tot3'] = floatval($datos[$count]['tot2']) - (floatval($datos[$count]['can2'])*round(floatval($datos[$count]['pre1']),2));
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['tra'] = '';
                    $datos[$count]['ref'] = '';
                    $datos[$count]['dia'] = '';
                    $datos[$count]['cos'] = '';
                    $datos[$count]['des'] = '';
                    $datos[$count]['bod'] = '';
                    $datos[$count]['col'] = '3';
                    $count ++;
                    if($datos[$count-1]['can3'] == 0  && $saldo_cero == 0){
                        array_pop($datos);
                        $count = $count - 1;
                    }
                }else{
                    $datos[$count]['cod'] = $producto->producto_codigo;
                    $datos[$count]['nom'] = $producto->producto_nombre;
                    $datos[$count]['fec'] = '';
                    $datos[$count]['can1'] = '';
                    $datos[$count]['pre1'] = '';
                    $datos[$count]['tot1'] = '';
                    $datos[$count]['can2'] = '';
                    $datos[$count]['pre2'] = '';
                    $datos[$count]['tot2'] = '';
                    $datos[$count]['can3'] = '';
                    $datos[$count]['pre3'] = '';
                    $datos[$count]['tot3'] = '';
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['tra'] = '';
                    $datos[$count]['ref'] = '';
                    $datos[$count]['dia'] = '';
                    $datos[$count]['cos'] = '';
                    $datos[$count]['des'] = '';
                    $datos[$count]['bod'] = '';
                    $datos[$count]['col'] = '0';
                    $count ++;
                    /***********************SALDO ANTERIOR***********************/
                    $datos[$count]['cod'] = '';
                    $datos[$count]['nom'] = '';
                    $datos[$count]['fec'] = '';
                    $datos[$count]['can1'] = '';
                    $datos[$count]['pre1'] = '';
                    $datos[$count]['tot1'] = '';
                    $datos[$count]['can2'] = '';
                    $datos[$count]['pre2'] = '';
                    $datos[$count]['tot2'] = '';
                    if($request->get('bodegaID') == '0'){
                        $datos[$count]['can3'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($request->get('fecha_desde')."- 1 days")) )->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_cantidad')-Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($request->get('fecha_desde')."- 1 days")) )->where('movimiento_tipo','=','SALIDA')->sum('movimiento_cantidad');
                    }else{
                        $datos[$count]['can3'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($request->get('fecha_desde')."- 1 days")) )->where('movimiento_tipo','=','ENTRADA')->where('bodega_id','=',$request->get('bodegaID'))->sum('movimiento_cantidad')-Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($request->get('fecha_desde')."- 1 days")) )->where('movimiento_tipo','=','SALIDA')->where('bodega_id','=',$request->get('bodegaID'))->sum('movimiento_cantidad');
                    }
                    $datos[$count]['pre3'] = '';
                    $datos[$count]['tot3'] = '';
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['tra'] = '';
                    $datos[$count]['ref'] = '';
                    $datos[$count]['dia'] = '';
                    $datos[$count]['cos'] = '';
                    $datos[$count]['des'] = '';
                    $datos[$count]['bod'] = '';
                    $datos[$count]['col'] = '1';
                    $count ++;
                    /***********************************************************/
                    if($request->get('bodegaID') == '0'){
                        $movimientos = Movimiento_Producto::MovProductoByFecha($producto->producto_id,$request->get('fecha_desde'),$request->get('fecha_hasta'))->orderBy('movimiento_fecha','asc')->get();
                    }else{
                        $movimientos = Movimiento_Producto::MovProductoByFecha($producto->producto_id,$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('bodega_id','=',$request->get('bodegaID'))->orderBy('movimiento_fecha','asc')->get();
                    }
                    foreach($movimientos as $movimiento){
                        $datos[$count]['cod'] = '';
                        $datos[$count]['nom'] = '';
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
                            $datos[$count]['pre2'] = $movimiento->movimiento_precio;
                            $datos[$count]['tot2'] = $movimiento->movimiento_total;
                        }
                    
                        $datos[$count]['can3'] = floatval($datos[$count-1]['can3'])+floatval($datos[$count]['can1'])-floatval($datos[$count]['can2']);
                        $datos[$count]['pre3'] = '';
                        $datos[$count]['tot3'] = '';
                        $datos[$count]['doc'] = $movimiento->movimiento_documento;
                        $datos[$count]['num'] = '';
                        $datos[$count]['ref'] = '';
                        $datos[$count]['dia'] = '';
                        $datos[$count]['cos'] = '';
                        if($movimiento->detalle_fv){
                            $datos[$count]['num'] = $movimiento->detalle_fv->facturaVenta->factura_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_fv->facturaVenta->cliente->cliente_nombre;
                            if($movimiento->detalle_fv->facturaVenta->diario){
                                $datos[$count]['dia'] = $movimiento->detalle_fv->facturaVenta->diario->diario_codigo;
                            }
                            if($movimiento->detalle_fv->facturaVenta->diarioCosto){
                                $datos[$count]['cos'] = $movimiento->detalle_fv->facturaVenta->diarioCosto->diario_codigo;
                            }
                        }
                        if($movimiento->detalle_nc){
                            $datos[$count]['num'] = $movimiento->detalle_nc->notaCredito->factura->factura_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_nc->notaCredito->factura->cliente->cliente_nombre;
                            if($movimiento->detalle_nc->notaCredito->diario){
                                $datos[$count]['dia'] = $movimiento->detalle_nc->notaCredito->diario->diario_codigo;
                            }
                            if($movimiento->detalle_nc->notaCredito->diarioCosto){
                                $datos[$count]['cos'] = $movimiento->detalle_nc->notaCredito->diarioCosto->diario_codigo;
                            }
                        }
                        if($movimiento->detalle_nd){
                            $datos[$count]['num'] = $movimiento->detalle_nd->notaDebito->factura->factura_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_nd->notaDebito->factura->cliente->cliente_nombre;
                            if($movimiento->detalle_nd->notaDebito->diario){
                                $datos[$count]['dia'] = $movimiento->detalle_nd->notaDebito->diario->diario_codigo;
                            }
                        }
                        if($movimiento->detalle_tc){
                            $datos[$count]['num'] = $movimiento->detalle_tc->transaccionCompra->transaccion_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_tc->transaccionCompra->proveedor->proveedor_nombre;
                            if($movimiento->detalle_tc->transaccionCompra->diario){
                                $datos[$count]['dia'] = $movimiento->detalle_tc->transaccionCompra->diario->diario_codigo;
                            }
                        }
                        if($movimiento->detalle_lc){
                            $datos[$count]['num'] = $movimiento->detalle_lc->liquidacionCompra->lc_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_lc->liquidacionCompra->proveedor->proveedor_nombre;
                            if($movimiento->detalle_lc->liquidacionCompra->diario){
                                $datos[$count]['dia'] = $movimiento->detalle_lc->liquidacionCompra->diario->diario_codigo;
                            }
                        }
                        if($movimiento->detalle_eb){
                            $datos[$count]['num'] = $movimiento->detalle_eb->egresobodega->cabecera_egreso_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_eb->egresobodega->cabecera_egreso_destinatario;
                            if($movimiento->detalle_eb->egresobodega->diario){
                                $datos[$count]['dia'] = $movimiento->detalle_eb->egresobodega->diario->diario_codigo;
                            }
                        }
                        if($movimiento->detalle_ib){
                            $datos[$count]['num'] = $movimiento->detalle_ib->Ingresobodega->cabecera_ingreso_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_ib->Ingresobodega->proveedor->proveedor_nombre;
                            if($movimiento->detalle_ib->Ingresobodega->diario){
                                $datos[$count]['dia'] = $movimiento->detalle_ib->Ingresobodega->diario->diario_codigo;
                            }
                        }
                        if($movimiento->detalle_od){
                            $datos[$count]['num'] = $movimiento->detalle_od->ordenDespacho->orden_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_od->ordenDespacho->cliente->cliente_nombre;
                        }
                        if($movimiento->detalle_or){
                            $datos[$count]['num'] = $movimiento->detalle_or->ordenDespacho->ordenr_numero;
                            $datos[$count]['ref'] = $movimiento->detalle_or->ordenDespacho->proveedor->proveedor_nombre;
                        }
                        $datos[$count]['tra'] = $movimiento->movimiento_motivo;
                        $datos[$count]['des'] = $movimiento->movimiento_descripcion;
                        $datos[$count]['bod'] = $movimiento->bodega->bodega_nombre;
                        $datos[$count]['col'] = '2';
                        $count ++;
                    }
                    if($datos[$count-1]['can3'] == 0  && $saldo_cero == 0){
                        array_pop($datos);
                        $count = $count - 1;
                        array_pop($datos);
                        $count = $count - 1;
                    }
                }
            }
            return $datos;
        }catch(\Exception $ex){
            return redirect('kardex')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $saldo_cero = 0;
            if ($request->get('saldo_cero') == "on"){
                $saldo_cero = 1; 
            }
            $datos = $this->datosProcesar($request);
            return view('admin.inventario.kardex.index',['tipo'=>$request->get('radioKardex'),'saldo_cero'=>$saldo_cero,'datos'=>$datos,'categoriaC'=>$request->get('categoriaID'),'fDesde'=>$request->get('fecha_desde'),'fHasta'=>$request->get('fecha_hasta'),'productoC'=>$request->get('productoID'),'categoriaC'=>$request->get('categoriaID'),'bodegaC'=>$request->get('bodegaID'),'productos'=>Producto::productos()->get(),'categorias'=>Categoria_Producto::categorias()->get(),'bodegas'=>Bodega::bodegas()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('kardex')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function datos($datosProcesados){
        try{
            $datos = null;
            $count = 1;
          /*  $nom = $request->get('idNom');
            $fec = $request->get('idFec');
            $can1 = $request->get('idCan1');
            $pre1 = $request->get('idPre1');
            $tot1 = $request->get('idTot1');
            $can2 = $request->get('idCan2');
            $pre2 = $request->get('idPre2');
            $tot2 = $request->get('idTot2');
            $can3 = $request->get('idCan3');
            $pre3 = $request->get('idPre3');
            $tot3 = $request->get('idTot3');
            $doc = $request->get('idDoc');
            $num = $request->get('idNum');
            $tra = $request->get('idTra');
            $ref = $request->get('idRef');
            $dia = $request->get('idDia');
            $cos = $request->get('idCos');
            $des = $request->get('idDes');
            $bod = $request->get('idBod');
            $col = $request->get('idCol');*/
            if($datosProcesados){
                for ($i = 1; $i <= count($datosProcesados); ++$i){
                    $datos[$count]['cod'] = $datosProcesados[$i]['cod'];
                    $datos[$count]['nom'] = $datosProcesados[$i]['nom'];
                    $datos[$count]['fec'] = $datosProcesados[$i]['fec'];
                    $datos[$count]['can1'] = $datosProcesados[$i]['can1'];
                    $datos[$count]['pre1'] = $datosProcesados[$i]['pre1'];
                    $datos[$count]['tot1'] = $datosProcesados[$i]['tot1'];
                    $datos[$count]['can2'] = $datosProcesados[$i]['can2'];
                    $datos[$count]['pre2'] = $datosProcesados[$i]['pre2'];
                    $datos[$count]['tot2'] = $datosProcesados[$i]['tot2'];
                    $datos[$count]['can3'] = $datosProcesados[$i]['can3'];
                    $datos[$count]['pre3'] = $datosProcesados[$i]['pre3'];
                    $datos[$count]['tot3'] = $datosProcesados[$i]['tot3'];
                    $datos[$count]['doc'] = $datosProcesados[$i]['doc'];
                    $datos[$count]['num'] = $datosProcesados[$i]['num'];
                    $datos[$count]['tra'] = $datosProcesados[$i]['tra'];
                    $datos[$count]['ref'] = $datosProcesados[$i]['ref'];
                    $datos[$count]['dia'] = $datosProcesados[$i]['dia'];
                    $datos[$count]['cos'] = $datosProcesados[$i]['cos'];
                    $datos[$count]['des'] = $datosProcesados[$i]['des'];
                    $datos[$count]['bod'] = $datosProcesados[$i]['bod'];
                    $datos[$count]['col'] = $datosProcesados[$i]['col'];
                    $count ++;
                }
            }
            return $datos;
        }catch(\Exception $ex){
            return redirect('kardex')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf($datos,$tipo,$dese,$hasta){
        try{            
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.kardex', ['datos'=>$datos,'tipo'=>$tipo,'desde'=>DateTime::createFromFormat('Y-m-d', $dese)->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $hasta)->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'KARDEX '.DateTime::createFromFormat('Y-m-d', $dese)->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $hasta)->format('d-m-Y');
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('kardex')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excel($datos,$tipo){
        try{   
            $datos['tipo'][count($datos)+1] = $tipo;
            return Excel::download(new ViewExcel('admin.formatosExcel.kardex',$datos), 'NEOPAGUPA  Sistema Contable.xls');
        }catch(\Exception $ex){
            return redirect('kardex')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
