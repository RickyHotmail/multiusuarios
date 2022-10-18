<?php

namespace App\Http\Controllers;
use App\Models\Empresa;
use App\Models\Orden_Despacho;
use App\Models\Punto_Emision; 
use PDF;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class reporteOrdenesDespachoController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $clientes = Orden_Despacho::ClientesDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $estados = Orden_Despacho::EstadoDistinsc()->select('orden_estado')->distinct()->get();
            $sucursal = Orden_Despacho::SurcusalDistinsc()->select('sucursal_nombre')->distinct()->get();
            return view('admin.ventas.ordenesdespacho.reporte',['sucursal'=>$sucursal,'estados'=>$estados,'clientes'=>$clientes,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        if (isset($_POST['pdf'])){
            return $this->pdf($request);
        }
    }
    public function buscar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $valor_cliente=$request->get('nombre_cliente');
            $valor_producto=$request->get('nombre_producto');
            $ordenes=null;
            $sucursal = Orden_Despacho::SurcusalDistinsc()->select('sucursal_nombre')->distinct()->get();
            $clientes = Orden_Despacho::ClientesDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $estados = Orden_Despacho::EstadoDistinsc()->select('orden_estado')->distinct()->get();

            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('estados') == "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoTodos()->distinct()->get();
               
         
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('estados') != "--TODOS--" && $request->get('sucursal') != "--TODOS--") {   
                 $ordenes=Orden_Despacho::BuscarDetalleProductoDiferentes($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('estados'),$request->get('nombre_cliente'),$request->get('sucursal'))->distinct()->get();

                
                            
            }             
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('estados') == "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->distinct()->get();   
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('estados') == "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoCliente($request->get('nombre_cliente'))->distinct()->get();

                 
                     
            } 
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('estados') != "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoEstado($request->get('estados'))->distinct()->get();

                
                            
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('estados') == "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoSucurasal($request->get('sucursal'))->distinct()->get();

            } 
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('estados') != "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoEstadoSucursal($request->get('estados'),$request->get('sucursal'))->distinct()->get();

            } 
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('estados') != "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoEstadoCliente($request->get('estados'),$request->get('nombre_cliente'))->distinct()->get();

                            
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('estados') != "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoFechaEstado($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('estados'))->distinct()->get();

                
                       
            } 
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('estados') != "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoFechaEstadoCliente($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('estados'),$request->get('nombre_cliente'))->distinct()->get();

                
            
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('estados') != "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoFechaEstadoSucursal($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('estados'),$request->get('sucursal'))->distinct()->get();

            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('estados') == "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoFechaClienteSucursal($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_cliente'),$request->get('sucursal'))->distinct()->get();

                       
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('estados') == "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoFechaCliente($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_cliente'))->distinct()->get();

                        
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('estados') == "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoFechaSucursal($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal'))->distinct()->get();

                        
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('estados') != "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoEstadoClienteSucursal($request->get('estados'),$request->get('nombre_cliente'),$request->get('sucursal'))->distinct()->get();

                

              
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('estados') == "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $ordenes=Orden_Despacho::BuscarDetalleProductoClienteSucursal($request->get('nombre_cliente'),$request->get('sucursal'))->distinct()->get();

                

               
            }
                
            
            return view('admin.ventas.ordenesdespacho.reporte',['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'nombre_cliente'=>$request->get('nombre_cliente'),'idestado'=>$request->get('estados'),'fecha_todo'=>$request->get('fecha_todo'),'datos'=>$ordenes,'estados'=>$estados,'clientes'=>$clientes,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request){
        try{
            $datos = null;
            $count = 1;
            $orden_numero = $request->get('orden_numero');
            $orden_fecha = $request->get('orden_fecha');
            $producto_codigo = $request->get('producto_codigo');
            $detalle_descripcion = $request->get('detalle_descripcion');
            $detalle_cantidad = $request->get('detalle_cantidad');
            $precio = $request->get('precio');
            $iva = $request->get('iva');
            $sub12 = $request->get('sub12');
            $sub0 = $request->get('sub0');
            $total = $request->get('total');
            $cliente_nombre = $request->get('cliente_nombre');
            $libras = $request->get('libras');
            $kilos = $request->get('kilos');
            $tm = $request->get('tm');
            $factura = $request->get('factura');  
            $orden_comentario = $request->get('orden_comentario');
            $embarcacion = $request->get('embarcacion');

            if($orden_numero){
                for ($i = 0; $i < count($orden_numero); ++$i){

                    $datos[$count]['orden_numero'] = $orden_numero[$i];
                    $datos[$count]['orden_fecha'] = $orden_fecha[$i];
                    $datos[$count]['producto_codigo'] = $producto_codigo[$i];
                    $datos[$count]['detalle_descripcion'] = $detalle_descripcion[$i];
                    $datos[$count]['detalle_cantidad'] = $detalle_cantidad[$i];
                    $datos[$count]['precio'] = $precio[$i];
                    $datos[$count]['iva'] = $iva[$i];
                    $datos[$count]['sub0'] = $sub0[$i];
                    $datos[$count]['sub12'] = $sub12[$i];
                    $datos[$count]['total'] = $total[$i];                        
                    $datos[$count]['cliente_nombre'] = $cliente_nombre[$i];
                    $datos[$count]['libras'] = $libras[$i];
                    $datos[$count]['kilos'] = $kilos[$i];
                    $datos[$count]['tm'] = $tm[$i];          
                    $datos[$count]['factura'] = $factura[$i];        
                    $datos[$count]['orden_comentario'] = $orden_comentario[$i];
                    $datos[$count]['embarcacion'] = $embarcacion[$i];
                    $count ++;         
                }
            }    
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }          
            $view =  \View::make('admin.formatosPDF.ordenesdedespacho', ['producto'=>$request->get('nombre_producto'),'cliente'=>$request->get('nombre_cliente'),'datos'=>$datos,'desde'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'ORDENDES DE DESPACHO '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
           
       
    }
}
