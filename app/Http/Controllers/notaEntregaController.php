<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_Diario;
use App\Models\Detalle_NE;
use App\Models\Detalle_Pago_CXC;
use App\Models\Diario;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Producto;
use App\Models\Nota_Entrega;
use App\Models\Pago_CXC;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class notaEntregaController extends Controller
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
            $clientes = Nota_Entrega::Clientes()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $notaentrega = null;
            $sucursales = Nota_Entrega::Sucursales()->select('sucursal_nombre')->distinct()->get();
            return view('admin.ventas.notaEntrega.index',['notaentrega'=>$notaentrega,'sucursal'=>$sucursales,'clientes'=>$clientes, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
   
    public function nuevo($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Nota de Entrega')->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Nota_Entrega::secuencial($rangoDocumento->rango_id)->max('nt_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;
                }
                return view('admin.ventas.notaEntrega.nuevo',
                    ['clientes'=>Cliente::Clientes()->get(),
                    'cajaAbierta'=>Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                    'bodegas'=>Bodega::bodegasSucursal($id)->get(),
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'gruposPermiso'=>$gruposPermiso, 
                    'permisosAdmin'=>$permisosAdmin]
                );
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir nts de venta, configueros y vuelva a intentar');
            }
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
        return redirect('/denegado');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function busqueda(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $clientes = Nota_Entrega::NotaEntregas()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $nentrega=null;
            
            $sucursales = Nota_Entrega::Sucursales()->select('sucursal_nombre')->distinct()->get();
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $nentrega=Nota_Entrega::TodosDiferentes($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_cliente'),$request->get('sucursal'))->get();     
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') == "--TODOS--"  && $request->get('sucursal') == "--TODOS--") {
                $nentrega=Nota_Entrega::NotaEntregas()->get();
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $nentrega=Nota_Entrega::Fecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->get();
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $nentrega=Nota_Entrega::buscarCliente($request->get('nombre_cliente'))->get();             
               
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $nentrega=Nota_Entrega::buscarSucursal($request->get('sucursal'))->get();              
            }  

            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('sucursal') == "--TODOS--") {
                $nentrega=Nota_Entrega::buscarFechaCliente($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal'))->get();              
            }   
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_cliente') == "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $nentrega=Nota_Entrega::buscarFechaSucursal($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal'))->get();              
            } 
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_cliente') != "--TODOS--" && $request->get('sucursal') != "--TODOS--") {
                $nentrega=Nota_Entrega::buscarClienteSucursal($request->get('nombre_cliente'),$request->get('sucursal'))->get();              
            }
            return view('admin.ventas.notaEntrega.index',['idsucursal'=>$request->get('sucursal'),'notaentrega'=>$nentrega,'sucursal'=>$sucursales,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_cliente'=>$request->get('nombre_cliente'),'clientes'=>$clientes,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimir($id)
    {
        
        $nota=Nota_Entrega::NotaEntrega($id)->get()->first();
        $general = new generalController();
        $url = $general->NotaEntrega($nota,0);
        return $url;
    }

    public function imprimirRecibo($id)
    {
        $nota=Nota_Entrega::NotaEntrega($id)->get()->first();
        $general = new generalController();
        $url = $general->NotaEntregaRecibo($nota,0);
        return $url;
    }

    public function store(Request $request)
    {
        try{            
            DB::beginTransaction();
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            /********************cabecera de nt de venta ********************/
            $general = new generalController();   
            $cierre = $general->cierre($request->get('nt_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/notaentrega/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            } 
            $banderaP = false;
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                if($producto->producto_tipo == '1'){
                    $banderaP = true;
                }
            }
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();       
            $nt = new Nota_Entrega();
            $nt->nt_numero = $request->get('nt_serie').substr(str_repeat(0, 9).$request->get('nt_numero'), - 9);
            $nt->nt_serie = $request->get('nt_serie');
            $nt->nt_secuencial = $request->get('nt_numero');
            $nt->nt_fecha = $request->get('nt_fecha');
            $nt->nt_total = $request->get('idTotal');
            $nt->nt_tipo_pago = $request->get('nt_tipo_pago');
            if($request->get('nt_comentario')){
                $nt->nt_comentario = $request->get('nt_comentario');
            }else{
                $nt->nt_comentario = '';
            }
            $nt->nt_estado = '1';
            $nt->bodega_id = $request->get('bodega_id');
            $nt->cliente_id = $request->get('clienteID');
            $nt->rango_id = $request->get('rango_id');             
            /*******************************************************************/
                /********************cuenta por cobrar***************************/
                $cxc = new Cuenta_Cobrar();
                $cxc->cuenta_descripcion = 'NOTA DE ENTREGA No. '.$nt->nt_numero;
                if($request->get('nt_tipo_pago') == 'CREDITO' or $request->get('nt_tipo_pago') == 'CONTADO'){
                    $cxc->cuenta_tipo =$request->get('nt_tipo_pago');
                    $cxc->cuenta_saldo = $request->get('idTotal');
                    $cxc->cuenta_estado = '1';
                }else{
                    $cxc->cuenta_tipo = $request->get('nt_tipo_pago');
                    $cxc->cuenta_saldo = 0.00;
                    $cxc->cuenta_estado = '2';
                }
                $cxc->cuenta_fecha = $request->get('nt_fecha');
                $cxc->cuenta_fecha_inicio = $request->get('nt_fecha');
                $cxc->cuenta_fecha_fin = $request->get('nt_fecha');
                $cxc->cuenta_monto = $request->get('idTotal');
                $cxc->cuenta_valor_factura = $request->get('idTotal');
                $cxc->cliente_id = $request->get('clienteID');
                $cxc->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $cxc->save();
                $general->registrarAuditoria('Registro de cuenta por cobrar de Nota entrega -> '.$nt->nt_numero,$nt->nt_numero,'Registro de cuenta por cobrar de Nota entrega -> '.$nt->nt_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal'));
                /****************************************************************/
            $nt->cuentaCobrar()->associate($cxc);
            if(Auth::user()->empresa->empresa_contabilidad== '1'){
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('nt_fecha'),'CNEE');
                $diario->diario_fecha = $request->get('nt_fecha');
                $diario->diario_referencia = 'COMPROBANTE DE NOTA DE ENTREGA EMITIDA';
                $diario->diario_tipo_documento = 'NOTA DE ENTREGA';
                $diario->diario_numero_documento = $nt->nt_numero;
                $diario->diario_beneficiario = $request->get('buscarCliente');
                $diario->diario_tipo = 'CNEE';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('nt_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('nt_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE NOTA DE ENTREGA EMITIDA: '.$nt->nt_numero;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de nota de entrega -> '.$nt->nt_numero,$nt->nt_numero,'Registro de diario de  nota de entrega -> '.$nt->nt_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                $nt->diario()->associate($diario);
                if($banderaP){
                    /**********************asiento diario de costo ****************************/
                    $diarioC = new Diario();
                    $diarioC->diario_codigo = $general->generarCodigoDiario($request->get('nt_fecha'),'CCVP');
                    $diarioC->diario_fecha = $request->get('nt_fecha');
                    $diarioC->diario_referencia = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO';
                    $diarioC->diario_tipo_documento = 'NOTA DE ENTREGA';
                    $diarioC->diario_numero_documento = $nt->nt_numero;
                    $diarioC->diario_beneficiario = $request->get('buscarCliente');
                    $diarioC->diario_tipo = 'CCVP';
                    $diarioC->diario_secuencial = substr($diarioC->diario_codigo, 8);
                    $diarioC->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('nt_fecha'))->format('m');
                    $diarioC->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('nt_fecha'))->format('Y');
                    $diarioC->diario_comentario = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO CON NOTA DE ENTREGA: '.$nt->nt_numero;
                    $diarioC->diario_cierre = '0';
                    $diarioC->diario_estado = '1';
                    $diarioC->empresa_id = Auth::user()->empresa_id;
                    $diarioC->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                    $diarioC->save();
                    $general->registrarAuditoria('Registro de diario de costo de venta de factura -> '.$nt->nt_numero,$nt->nt_numero,'Registro de diario de costo de venta de factura -> '.$nt->nt_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diarioC->diario_codigo);
                    /************************************************************************/
                    $nt->diarioCosto()->associate($diarioC);
                }
            }
            if($cxc->cuenta_estado == '2'){
                /********************Pago por Venta de Contado***************************/
                $pago = new Pago_CXC();
                $pago->pago_descripcion = 'PAGO EN EFECTIVO';
                $pago->pago_fecha = $cxc->cuenta_fecha;
                $pago->pago_tipo = 'PAGO EN EFECTIVO';
                $pago->pago_valor = $cxc->cuenta_monto;
                $pago->pago_estado = '1';
                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                    $pago->diario()->associate($diario);
                }
                $pago->save();

                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = 'PAGO EN EFECTIVO';
                $detallePago->detalle_pago_valor = $cxc->cuenta_monto; 
                $detallePago->detalle_pago_cuota = 1;
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxc->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();
                /****************************************************************/
            }
            if($arqueoCaja){
                $nt->arqueo_id = $arqueoCaja->arqueo_id;
            }
            $nt->save();
            $general->registrarAuditoria('Registro de nota de entrega numero -> '.$nt->nt_numero,$nt->nt_numero,'Registro de nota de entrega numero -> '.$nt->nt_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal'));
            /****************************************************************/
            if(Auth::user()->empresa->empresa_contabilidad== '1'){
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('idTotal');
                $detalleDiario->detalle_haber = 0.00 ;
                $detalleDiario->detalle_tipo_documento = 'NOTA DE ENTREGA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($request->get('nt_tipo_pago') == 'CREDITO' or $request->get('nt_tipo_pago') == 'CONTADO'){
                    $detalleDiario->cliente_id = $request->get('clienteID');
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($request->get('clienteID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                    }
                }else{
                    $detalleDiario->detalle_comentario = 'P/R VENTA EN EFECTIVO';
                    $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                    $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nt->nt_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
            }
            /********************detalle de nt de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleNE = new Detalle_NE();
                $detalleNE->detalle_cantidad = $cantidad[$i];
                $detalleNE->detalle_precio_unitario = $pu[$i];
                $detalleNE->detalle_total = $total[$i];              
                $detalleNE->detalle_estado = '1';
                $detalleNE->producto_id = $isProducto[$i];               
            
                /******************registro de movimiento de producto******************/
                $movimientoProducto = new Movimiento_Producto();
                $movimientoProducto->movimiento_fecha=$request->get('nt_fecha');
                $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                $movimientoProducto->movimiento_precio=$pu[$i];
                $movimientoProducto->movimiento_iva=0;
                $movimientoProducto->movimiento_total=$total[$i];
                $movimientoProducto->movimiento_stock_actual=0;
                $movimientoProducto->movimiento_costo_promedio=0;
                $movimientoProducto->movimiento_documento='NOTA DE ENTREGA';
                $movimientoProducto->movimiento_motivo='VENTA';
                $movimientoProducto->movimiento_tipo='SALIDA';
                $movimientoProducto->movimiento_descripcion='NOTA DE ENTREGA No. '.$nt->nt_numero;
                $movimientoProducto->movimiento_estado='1';
                $movimientoProducto->producto_id=$isProducto[$i];
                $movimientoProducto->bodega_id=$nt->bodega_id;
                $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                $movimientoProducto->save();
                $general->registrarAuditoria('Registro de movimiento de producto por NOTA DE ENTREGA numero -> '.$nt->nt_numero,$nt->nt_numero,'Registro de movimiento de producto por NOTA DE ENTREGA numero -> '.$nt->nt_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                /*********************************************************************/
                $detalleNE->movimiento()->associate($movimientoProducto);
                $nt->detalle()->save($detalleNE);
                $general->registrarAuditoria('Registro de detalle de nota de entrega numero -> '.$nt->nt_numero,$nt->nt_numero,'Registro de detalle de nota de entrega numero -> '.$nt->nt_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                    $producto = Producto::findOrFail($isProducto[$i]);
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $total[$i];
                    $detalleDiario->detalle_comentario = 'P/R VENTA DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE ENTREGA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_venta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nt->nt_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$producto->cuentaVenta->cuenta_numero.' en el haber por un valor de -> '.$total[$i]);
                    
                    if($banderaP){
                        if($producto->producto_tipo == '1'){
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = 0.00;
                            $detalleDiario->detalle_haber = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'NOTA DE ENTREGA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$nt->nt_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$detalleDiario->detalle_haber);
                            
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_haber = 0.00;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $parametrizacionContable = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$nt->nt_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$detalleDiario->detalle_debe);
                        }
                    }
                }

            }
            if($request->get('nt_tipo_pago') == 'EN EFECTIVO'){
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="ENTRADA";
                $movimientoCaja->movimiento_descripcion= 'P/R NOTA DE ENTREGA DE CLIENTE '.$request->get('buscarCliente');
                $movimientoCaja->movimiento_valor= $request->get('idTotal');
                $movimientoCaja->movimiento_documento="NOTA DE ENTREGA";
                $movimientoCaja->movimiento_numero_documento= $nt->nt_numero;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                    $movimientoCaja->diario()->associate($diario);
                }
                $movimientoCaja->save();
                /*********************************************************************/
            }
            $url = '';
            if(Auth::user()->empresa->empresa_llevaContabilidad== '1'){
                $url = $general->pdfDiario($diario);
            }
            $urlNota = $general->NotaEntrega($nt,1);
            DB::commit();
            return redirect('/notaentrega/new/'.$request->get('punto_id'))->with('success','NOTA DE ENTREGA registrada exitosamente')->with('diario',$url)->with('pdf',$urlNota);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/notaentrega/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect('/denegado');
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
        return redirect('/denegado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $auditoria = new generalController();      
            $nentrega=Nota_Entrega::findOrFail($id);
            $general = new generalController();
            $cxcAu=0;
            $cierre = $general->cierre($nentrega->nt_fecha,$nentrega->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('notaentrega')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            
            $ntaux= $nentrega;
            if (isset($nentrega->diariocosto)) {
                foreach ($nentrega->diariocosto->detalles as $diariodetalle) {
                    $diariodetalle->delete();

                    $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$nentrega->diario->diario_codigo.' con nota de entrega N°'.$nentrega->nt_numero, $nentrega->nt_numero, 'Eliminacion del detalle diario con Permiso con id -> '.$id);
                }
            }
           
            if (isset($nentrega->diario)) {
                foreach ($nentrega->diario->detalles as $diariodetalle) {
                    
                    $diariodetalle->delete();

                    $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$nentrega->diario->diario_codigo.' con nota de entrega N°'.$nentrega->nt_numero, $nentrega->nt_numero, 'Eliminacion del detalle diario con Permiso con id -> '.$id);
                }
                if (isset($nentrega->diario->pagocuentaCobrar)) {
                    foreach ($nentrega->diario->pagocuentaCobrar->detalles as $detalle) {
                        $cxcAu=($detalle->cuentaCobrar->cuenta_id);
                        $aux=$detalle;
                        $detalle->delete();
                        $general->registrarAuditoria('Eliminacion del detalle del pago de la cuenta por cobrar  de  Nota de entrega  numero: -> '.$nentrega->nc_numero, $id, ' Descripcion -> '.$aux->detalle_pago_descripcion.' Con el valor-> '.$aux->detalle_pago_valor);
                    }
    
    
                    $aux=$nentrega->diario->pagocuentaCobrar;
                    $nentrega->diario->pagocuentaCobrar->delete();
                    $general->registrarAuditoria('Eliminacion del pago de la cuenta por cobrar por Nota de entrega numero: -> '.$nentrega->nc_numero, $id, 'Tipo '.$aux->cuenta_tipo.' con Descripcion -> '.$aux->cuenta_descripcion.' Con el valor-> '.$aux->cuenta_monto);
                    $nentrega->diario->movimientocaja->delete();
                    $general->registrarAuditoria('Eliminacion del Movimiento Caja por Nota de entrega numero: -> '.$nentrega->nc_numero, $id, 'Tipo '.$aux->cuenta_tipo.' con Descripcion -> '.$aux->cuenta_descripcion.' Con el valor-> '.$aux->cuenta_monto);
                }
               

                $ntaux->diario_id=null;
                $ntaux->save();
                $nentrega->diario->delete();
                $auditoria->registrarAuditoria('Eliminacion del diario con nota de entrega N°'.$nentrega->nt_numero, $nentrega->nt_numero, 'Eliminacion  con Permiso con id -> '.$id);
            }
            foreach ($nentrega->detalle as $detalle) {
                $deta=Detalle_NE::findOrFail($detalle->detalle_id);
                $deta->delete();
                $auditoria->registrarAuditoria('Eliminacion del detalle de Nota de entrega de nota de entrega N°'.$nentrega->nt_numero ,$nentrega->nt_numero,'Eliminacion de cantidad de producto'.$detalle->detalle_cantidad.' del movimiento de nota de entrega con id -> '.$id);
                
                $detalle->movimiento->delete();
                $auditoria->registrarAuditoria('Eliminacion del Movimiento de producto'.$detalle->producto->producto_nombre.' con nota de entrega N° '.$nentrega->nt_numero ,$nentrega->nt_numero,'Eliminacion de cantidad de producto'.$detalle->detalle_cantidad.' del movimiento de nota de entrega con id -> '.$id);  

                
            }

            if ($nentrega->nt_tipo_pago=="EN EFECTIVO") {
                foreach ($nentrega->cuentaCobrar->detallepago as $detalle) {
                    $detalle->pagoCXC->delete();
                    $auditoria->registrarAuditoria('Eliminacion del Pago ceuntas por cobrar'.$nentrega->cuentaCobrar->cuenta_descripcion.' con nota de entrega N° '.$nentrega->nt_numero, $nentrega->nt_numero, 'Eliminacion del detalle diario con Permiso con id -> '.$id);
                }
                foreach ($nentrega->cuentaCobrar->detallepago as $detalle) {
                    $detalle->delete();
                    $auditoria->registrarAuditoria('Eliminacion del detalle de pago cuentas por cobrar  '.$nentrega->cuentaCobrar->cuenta_descripcion.' con nota de entrega N°'.$nentrega->nt_numero, $nentrega->nt_numero, 'Eliminacion del detalle diario con Permiso con id -> '.$id);
                }
            }
           
            $ntaux=Nota_Entrega::findOrFail($id);
            $ntaux->cuenta_id=NULL;
            $ntaux->save();
           
            $nentrega->cuentaCobrar->delete();
            
            $auditoria->registrarAuditoria('Eliminacion de la cuenta por cobrar  con nota de entrega N°'.$nentrega->nt_numero ,$nentrega->nt_numero,'Eliminacion  con Permiso con id -> '.$id);  
            $nentrega->delete();
            $auditoria->registrarAuditoria('Eliminacion nota de entrega N°'.$nentrega->nt_numero ,$nentrega->nt_numero,'Eliminacion con id -> '.$id);
            DB::commit();
            return redirect('notaentrega')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            return redirect('notaentrega')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function delete($id)
    {        
       
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $nentrega=Nota_Entrega::NotaEntrega($id)->get()->first();
            return view('admin.ventas.notaEntrega.eliminar',['nentrega'=>$nentrega,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('notaentrega')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function visualizar($id)
    {        
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $nentrega=Nota_Entrega::NotaEntrega($id)->get()->first();
            return view('admin.ventas.notaEntrega.ver',['nentrega'=>$nentrega,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('notaentrega')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
}
