<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Categoria_Producto;
use App\Models\Empresa;
use App\Models\Movimiento_Producto;
use App\Models\Producto;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Excel;
use App\NEOPAGUPA\ViewExcel;
use DateTime;

class kardexCostoController extends Controller
{
    public function nuevo()
    {
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.inventario.kardexCosto.index',['productos'=>Producto::productos()->where('producto_compra_venta','=','3')->get(),'categorias'=>Categoria_Producto::categorias()->get(),'bodegas'=>Bodega::bodegas()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            return $this->excel($this->datos($request));
        }
        if (isset($_POST['pdf'])){
            $sin_fecha = 0;
            if ($request->get('sin_fecha') == "on"){
                $sin_fecha = 1; 
            }
            return $this->pdf($this->datos($request),$sin_fecha,$request->get('fecha_desde'),$request->get('fecha_hasta'));
        }
    }
    public function buscar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            $totalE = 0;
            $totalS = 0;
            $sin_fecha = 0;
            if ($request->get('sin_fecha') == "on"){
                $sin_fecha = 1; 
            }
            $producto = Producto::producto($request->get('productoID'))->first();
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
                $datos[$count]['can3'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($request->get('fecha_desde')."- 1 days")) )->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_cantidad')-Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($request->get('fecha_desde')."- 1 days")) )->where('movimiento_tipo','=','SALIDA')->sum('movimiento_cantidad');
                $costoPromedio = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($request->get('fecha_desde')."- 1 days")))->orderBy('movimiento_fecha','desc')->orderBy('movimiento_id','desc')->first();
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
            if($sin_fecha == 1){
                $movimientos = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y")."-".date("m")."-".date("d"))->orderBy('movimiento_fecha','asc')->orderBy('movimiento_id','asc')->get();
            }else{
                $movimientos = Movimiento_Producto::MovProductoByFecha($producto->producto_id,$request->get('fecha_desde'),$request->get('fecha_hasta'))->orderBy('movimiento_fecha','asc')->orderBy('movimiento_id','asc')->get();
            }
            foreach($movimientos as $movimiento){
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
                $totalE = $totalE + floatval($datos[$count]['tot1']);
                $totalS = $totalS + floatval($datos[$count]['tot2']);
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
                    if(isset($movimiento->detalle_od->ordenDespacho->Factura->diario)){
                        $datos[$count]['dia'] = $movimiento->detalle_od->ordenDespacho->Factura->diario->diario_codigo;
                    }
                    if(isset($movimiento->detalle_od->ordenDespacho->Factura->diarioCosto)){
                        $datos[$count]['cos'] = $movimiento->detalle_od->ordenDespacho->Factura->diarioCosto->diario_codigo;
                    }
                }
                if($movimiento->detalle_or){
                    $datos[$count]['num'] = $movimiento->detalle_or->ordenDespacho->ordenr_numero;
                    $datos[$count]['ref'] = $movimiento->detalle_or->ordenDespacho->proveedor->proveedor_nombre;
                }
                $datos[$count]['tra'] = $movimiento->movimiento_motivo;
                $datos[$count]['des'] = $movimiento->movimiento_descripcion;
                if($bandera2){
                    $count ++;
                }
            }
            return view('admin.inventario.kardexCosto.index',['datos'=>$datos,'totalE'=>$totalE,'totalS'=>$totalS,'sin_fecha'=>$sin_fecha,'fDesde'=>$request->get('fecha_desde'),'fHasta'=>$request->get('fecha_hasta'),'productoC'=>$request->get('productoID'),'categoriaC'=>$request->get('categoriaID'),'bodegaC'=>$request->get('bodegaID'),'productos'=>Producto::productos()->get(),'categorias'=>Categoria_Producto::categorias()->get(),'bodegas'=>Bodega::bodegas()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('kardexCosto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function datos(Request $request){
        try{
            $datos = null;
            $count = 1;
            $doc = $request->get('idDoc');
            $num = $request->get('idNum');
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
            $cos = $request->get('idCos');
            $dia = $request->get('idDia');
            $tra = $request->get('idTra');
            $ref = $request->get('idRef');
            $des = $request->get('idDes');
            if($doc){
                for ($i = 0; $i < count($doc); ++$i){
                    $datos[$count]['doc'] = $doc[$i];
                    $datos[$count]['num'] = $num[$i];
                    $datos[$count]['fec'] = $fec[$i];
                    $datos[$count]['can1'] = $can1[$i];
                    $datos[$count]['pre1'] = $pre1[$i];
                    $datos[$count]['tot1'] = $tot1[$i];
                    $datos[$count]['can2'] = $can2[$i];
                    $datos[$count]['pre2'] = $pre2[$i];
                    $datos[$count]['tot2'] = $tot2[$i];
                    $datos[$count]['can3'] = $can3[$i];
                    $datos[$count]['pre3'] = $pre3[$i];
                    $datos[$count]['tot3'] = $tot3[$i];
                    $datos[$count]['cos'] = $cos[$i];
                    $datos[$count]['dia'] = $dia[$i];
                    $datos[$count]['tra'] = $tra[$i];
                    $datos[$count]['ref'] = $ref[$i];
                    $datos[$count]['des'] = $des[$i];
                    $count ++;
                }
            }
            return $datos;
        }catch(\Exception $ex){
            return redirect('kardexCosto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf($datos,$sin_fecha,$dese,$hasta){
        try{            
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            if($sin_fecha == 0){
                $view =  \View::make('admin.formatosPDF.kardexCosto', ['datos'=>$datos,'sin_fecha'=>$sin_fecha,'desde'=>DateTime::createFromFormat('Y-m-d', $dese)->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $hasta)->format('d/m/Y'),'empresa'=>$empresa]);
                $nombreArchivo = 'KARDEX COSTO '.DateTime::createFromFormat('Y-m-d', $dese)->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $hasta)->format('d-m-Y');
            }else{
                $view =  \View::make('admin.formatosPDF.kardexCosto', ['datos'=>$datos,'sin_fecha'=>$sin_fecha,'hasta'=>DateTime::createFromFormat('Y-m-d', date("Y")."-".date("m")."-".date("d"))->format('d/m/Y'),'empresa'=>$empresa]);
                $nombreArchivo = 'KARDEX COSTO '.' AL '.DateTime::createFromFormat('Y-m-d', date("Y")."-".date("m")."-".date("d"))->format('d-m-Y');
            }
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('kardexCosto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excel($datos){
        try{   
            return Excel::download(new ViewExcel('admin.formatosExcel.kardexCosto',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('kardexCosto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
