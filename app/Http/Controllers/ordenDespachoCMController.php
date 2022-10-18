<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Bodega;
use App\Models\Transportista;
use App\Models\Detalle_OD;
use App\Models\Empresa;
use App\Models\Forma_Pago;
use App\Models\Orden_Despacho;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tarifa_Iva;
use App\Models\Vendedor;
use App\Models\Guia_Remision;
use App\Models\Movimiento_Prestamo_Producto;
use App\Models\Movimiento_Producto;
use App\Models\User;
use PDF;
use DateTime;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ordenDespachoCMController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $puntoEmisiones = Punto_Emision::puntos()->get();   
            $clientes = Orden_Despacho::ClientesDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $estados = Orden_Despacho::EstadoDistinsc()->select('orden_estado')->distinct()->get();
            $sucursal = Orden_Despacho::SurcusalDistinsc()->select('sucursal_nombre')->distinct()->get();
            $ordenes=null;
            return view('admin.ventas.ordenesdespacho.viewcm',['sucursal'=>$sucursal,'ordenes'=>$ordenes,'estados'=>$estados,'clientes'=>$clientes, 'puntoEmisiones'=>$puntoEmisiones,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{            
            DB::beginTransaction();
            
            
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            $codigo = $request->get('Dcodigo');
            $cliente=$request->get('buscarCliente');
            $datos=null;
            /********************cabecera de orden de venta ********************/
            $general = new generalController();           
            $orden = new Orden_Despacho();

            $cierre = $general->cierre($request->get('orden_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);  
            if($cierre){
                return redirect('/ordenDespachoCM/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $orden->orden_numero = $request->get('orden_serie').substr(str_repeat(0, 9).$request->get('orden_numero'), - 9);
            $orden->orden_serie = $request->get('orden_serie');
            $orden->orden_secuencial = $request->get('orden_numero');
            $orden->orden_fecha = $request->get('orden_fecha');
            $orden->orden_tipo_pago = $request->get('orden_tipo_pago');
            $orden->orden_dias_plazo = $request->get('orden_dias_plazo');
            $orden->orden_fecha_pago = $request->get('orden_fecha_termino');
            $orden->orden_subtotal = $request->get('idSubtotal');
            $orden->orden_descuento = $request->get('idDescuento');
            $orden->orden_tarifa0 = $request->get('idTarifa0');
            $orden->orden_tarifa12 = $request->get('idTarifa12');
            $orden->orden_iva = $request->get('idIva');
            $orden->orden_total = $request->get('idTotal');
            if($request->get('Inventario')){
                $orden->orden_reserva ='1';
            }else{
                $orden->orden_reserva ='0';
            }
            if($request->get('orden_comentario')){
                $orden->orden_comentario = $request->get('orden_comentario');
            }else{
                $orden->orden_comentario = '';
            }
            $orden->orden_porcentaje_iva = $request->get('orden_porcentaje_iva');          
            $orden->orden_estado = '1';
            $orden->bodega_id = $request->get('bodega_id');
            $orden->cliente_id = $request->get('clienteID');
            $orden->rango_id = $request->get('rango_id'); 
            $orden->vendedor_id = $request->get('vendedor_id'); 
           

            $orden->save();
            $general->registrarAuditoria('Registro de orden de Despacho numero -> '.$orden->orden_numero,$orden->orden_numero,'Registro de orden de Despacho numero -> '.$orden->orden_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal'));
            /*******************************************************************/
            /********************detalle de factura de venta********************/
           
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleOD = new Detalle_OD();
               $datos[$i]['nombre']=$nombre[$i];
               $datos[$i]['cantidad']=$cantidad[$i];
               $datos[$i]['iva']=$iva[$i];
               $datos[$i]['total']=$total[$i];
               $datos[$i]['codigo']=$codigo[$i];
               $datos[$i]['pu']=$pu[$i];
                $detalleOD->detalle_descripcion = $nombre[$i];
                $detalleOD->detalle_cantidad = $cantidad[$i];
                $detalleOD->detalle_precio_unitario = $pu[$i];
                $detalleOD->detalle_descuento = $descuento[$i];
                $detalleOD->detalle_iva = $iva[$i];
                $detalleOD->detalle_total = $total[$i];              
                $detalleOD->detalle_estado = '1';
                $detalleOD->producto_id = $isProducto[$i];

                if ($request->get('Inventario')) {
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('orden_fecha');
                    $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    $movimientoProducto->movimiento_precio=$pu[$i];
                    $movimientoProducto->movimiento_iva=$iva[$i];   
                    $movimientoProducto->movimiento_total=$total[$i];
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='ORDEN DE DESPACHO';
                    $movimientoProducto->movimiento_motivo='VENTA';
                    $movimientoProducto->movimiento_tipo='SALIDA';
                    $movimientoProducto->movimiento_descripcion='ORDEN DE DESPACHO No. '.$orden->orden_numero;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$orden->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por orden de despacho numero -> '.$orden->orden_numero, $orden->orden_numero, 'Registro de movimiento de producto por orden de despacho numero -> '.$orden->orden_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                    $detalleOD->movimiento()->associate($movimientoProducto);
                }
                if ($request->get('Dprestamo')) {
                    $prestamo=$request->get('Dprestamo');
                    if($prestamo[$i]>0){
                        $movimientoprestamoProducto = new Movimiento_Prestamo_Producto();
                        $movimientoprestamoProducto->movimiento_fecha=$request->get('orden_fecha');
                        $movimientoprestamoProducto->movimiento_valor=$prestamo[$i];
                        $movimientoprestamoProducto->movimiento_tipo='SALIDA';
                        $movimientoprestamoProducto->movimiento_descripcion='ORDEN DE DESPACHO No. '.$orden->orden_numero;
                        $movimientoprestamoProducto->movimiento_documento='ORDEN DE DESPACHO';
                        $movimientoprestamoProducto->movimiento_numero_documento=$orden->orden_numero;
                        $movimientoprestamoProducto->movimiento_estado='1';
                        $movimientoprestamoProducto->cliente_id=$request->get('clienteID');
                        $movimientoprestamoProducto->producto_id=$isProducto[$i];
                        $movimientoprestamoProducto->empresa_id=Auth::user()->empresa_id;
                        $movimientoprestamoProducto->save();
                        $general->registrarAuditoria('Registro de movimiento de prestamo de producto por orden de despacho numero -> '.$orden->orden_numero, $orden->orden_numero, 'Registro de movimiento de producto por orden de despacho numero -> '.$orden->orden_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$prestamo[$i]);
                        /*********************************************************************/
                        $detalleOD->movimientop()->associate($movimientoprestamoProducto);
                    }
                   
                }
                $orden->detalles()->save($detalleOD);

                $general->registrarAuditoria('Registro de detalle de orden de Despacho numero -> '.$orden->orden_numero,$orden->orden_numero,'Registro de detalle de orden de Despacho numero -> '.$orden->orden_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
            
            } 
                
            $empresa =  Empresa::empresa()->first();
           
             DB::commit();
            $view =  \View::make('admin.formatosPDF.ordenDespacho', ['orden'=>$orden,'empresa'=>$empresa]);
            $ruta = public_path().'/ordenDespacho/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('orden_fecha'))->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'OD-'.$orden->orden_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            return redirect('/ordenDespachoCM/new/'.$request->get('punto_id'))->with('success','Transaccion registrada y autorizada exitosamente')->with('pdf','ordenDespacho/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('orden_fecha'))->format('d-m-Y').'/'.$nombreArchivo.'.pdf');

        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/ordenDespachoCM/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    public function visualizar($id)
    {
        try{     
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $orden=Orden_Despacho::Orden($id)->first();
            $Accion='Visualizar';
            if($orden){
                return view('admin.ventas.ordenesdespacho.visualizarcm',['Accion'=>$Accion,'orden'=>$orden,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }
    public function Presentardelete($id)
    {
        try{     
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $orden=Orden_Despacho::Orden($id)->first();
            if($orden){
                return view('admin.ventas.ordenesdespacho.eliminarcm',['orden'=>$orden,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{   
            DB::beginTransaction();
            $orden = Orden_Despacho::findOrFail($id);
            $general = new generalController();           
            $cierre = $general->cierre($orden->orden_fecha,$orden->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('listaOrdenesCM')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach ($orden->detalles as $detalles) {
                if($detalles->movimiento){
                    $deta=Detalle_OD::findOrFail($detalles->detalle_id);
                    $deta->movimiento_id=null;
                    $deta->save();
                    
                    $detalles->movimiento->delete();
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Eliminacion del Movimeinto del detalle de la Orden de Despacho N°-> '.$orden->orden_numero,'0','Con Producto Id -> '.$detalles->producto_id.' Con Cantidad'.$detalles->detalle_cantidad.' Con Precio'.$detalles->detalle_precio_unitario);
                    
                   
                   

                }
                if(isset($detalles->movimientop)){
                    $deta=Detalle_OD::findOrFail($detalles->detalle_id);
                    $deta->movimientop_id=null;
                    $deta->save();

                    $detalles->movimientop->delete();
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Eliminacion del Movimeinto prestamo del detalle de la Orden de Despacho N°-> '.$orden->orden_numero,'0','Con Producto Id -> '.$detalles->producto_id.' Con Cantidad'.$detalles->detalle_cantidad.' Con Precio'.$detalles->detalle_precio_unitario);
                
                }
                $detalles->delete();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Eliminacion del detalle de la Orden de Despacho N°-> '.$orden->orden_numero,'0','Con Producto Id -> '.$detalles->producto_id.' Con Cantidad'.$detalles->detalle_cantidad.' Con Precio'.$detalles->detalle_precio_unitario);
            }
            $orden->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de Orden de Despacho N°-> '.$orden->orden_numero,'0','Permiso con id -> '.$id);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('listaOrdenesCM')->with('success','Datos eliminados exitosamente');
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaOrdenesCM')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
