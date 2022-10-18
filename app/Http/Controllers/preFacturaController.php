<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_Diario;
use App\Models\Detalle_FV;
use App\Models\Detalle_Pago_CXC;
use App\Models\Detalle_PFV;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Forma_Pago;
use App\Models\Guia_Remision;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Prestamo_Producto;
use App\Models\Movimiento_Producto;
use App\Models\Orden_Despacho;
use App\Models\Pago_CXC;
use App\Models\Parametrizacion_Contable;
use App\Models\Prefactura_Venta;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tarifa_Iva;
use App\Models\User;
use App\Models\Vendedor;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class preFacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function listar()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $clientes=Prefactura_Venta::prefacturacion()->orderBy('cliente.cliente_nombre','desc')->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $estados=Prefactura_Venta::prefacturacion()->orderBy('prefactura_estado','desc')->select('prefactura_estado')->distinct()->get();

            return view('admin.ventas.prefactura.index',['clientes'=>$clientes,'estados'=>$estados,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
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
    public function eliminar($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            
            $prefactura=Prefactura_Venta::findOrFail($id);
            $coun=1; 
            $datos=null; 
            foreach($prefactura->detalles as $detalle){
                $datos[$coun]['guia_id'] = $detalle->guia->gr_id;
                $datos[$coun]['guia_fecha'] = $detalle->guia->gr_fecha;
                $datos[$coun]['guia_numero'] = $detalle->guia->gr_numero;
                $datos[$coun]['transportista'] = $detalle->guia->Transportista->transportista_nombre;
                $datos[$coun]['placa'] = $detalle->guia->gr_placa;
                $datos[$coun]['producto_id'] = $detalle->producto_id;
                $datos[$coun]['detalle_cantidad'] = $detalle->detalle_cantidad;
                $datos[$coun]['detalle_descripcion'] = $detalle->detalle_descripcion;
                $datos[$coun]['detalle_precio_unitario'] = $detalle->detalle_precio_unitario;
                $datos[$coun]['detalle_descuento'] = $detalle->detalle_descuento;
                $datos[$coun]['detalle_iva'] = $detalle->detalle_iva;
                $datos[$coun]['detalle_total'] =$detalle->detalle_total;         
                $datos[$coun]['producto_codigo'] = $detalle->producto->producto_codigo;
                $datos[$coun]['producto_stock'] = $detalle->producto->producto_stock;
                $coun++;
            } 

        $guias=Prefactura_Venta::Guias($id)->orderBy('guia_remision.gr_numero','desc')->select('guia_remision.gr_id','guia_remision.gr_numero')->distinct()->get();
        return view('admin.ventas.prefactura.anular',['prefactura'=>$prefactura,'datos'=>$datos,'guias'=>$guias,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function anular(Request $request){
        try {    
            DB::beginTransaction();
            
            $auditoria = new generalController();
            $prefactura=Prefactura_Venta::findOrFail($request->get('prefactura_id'));
            $cierre = $auditoria->cierre($prefactura->prefactura_fecha,Rango_Documento::rango($prefactura->rango_id)->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('listaprefactura')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $prefactura->prefactura_estado = '0';
            $prefactura->update();
            $auditoria->registrarAuditoria('Anulacion de Prefactura -> '.$prefactura->prefactura_numero,$prefactura->prefactura_numero,'');
                          
            DB::commit();
            return redirect('listaprefactura')->with('success','Datos anulados exitosamente');
        }
        catch(\Exception $ex){   
            DB::rollBack();       
            return redirect('listaprefactura')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accion(Request $request)
    {
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['generar'])){
          
            return $this->generar($request);
 
        }
    }
    public function buscar(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $clientes=Prefactura_Venta::prefacturacion()->orderBy('cliente.cliente_id','desc')->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $estados=Prefactura_Venta::prefacturacion()->orderBy('prefactura_estado','desc')->select('prefactura_estado')->distinct()->get();
            $prefactura=Prefactura_Venta::buscar($request->get('nombre_cliente'),$request->get('estado'),$request->get('fecha_todo'),$request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('descripcion'))->get();
           
            return view('admin.ventas.prefactura.index',['prefacturas'=>$prefactura,'fecha_todo'=>$request->get('fecha_todo'),'valorestados'=>$request->get('estado'),'valor_cliente'=>$request->get('nombre_cliente'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'clientes'=>$clientes,'estados'=>$estados,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarfactura(Request $request)
    {
        try{            
            DB::beginTransaction();
            $guia = $request->get('Dguias');
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            $prefactura=$request->get('prefactura_id');
            $inventarioResevado = false; 
            /********************cabecera de factura de venta ********************/
            $general = new generalController();
            $cierre = $general->cierre($request->get('factura_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('listaprefactura')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $banderaP = false;
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                    $banderaP = true;
                }
            }
            $docElectronico = new facturacionElectronicaController();
            $arqueoCaja=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            if($request->get('factura_tipo_pago') == 'EN EFECTIVO'){
                if(isset($arqueoCaja->arqueo_id) == false){
                    throw new Exception('No puede guardar la factura porque la forma de pago es en efectivo y usted no tiene una caja abierta.');
                }
            }
            $factura = new Factura_Venta();
            $factura->factura_numero = $request->get('factura_serie').substr(str_repeat(0, 9).$request->get('factura_numero'), - 9);
            $factura->factura_serie = $request->get('factura_serie');
            $factura->factura_secuencial = $request->get('factura_numero');
            $factura->factura_fecha = $request->get('factura_fecha');
            $factura->factura_lugar = $request->get('factura_lugar');
            $factura->factura_tipo_pago = $request->get('factura_tipo_pago');
            $factura->factura_dias_plazo = $request->get('factura_dias_plazo');
            $factura->factura_fecha_pago = $request->get('factura_fecha_termino');
            $factura->factura_subtotal = $request->get('idSubtotal');
            $factura->factura_descuento = $request->get('idDescuento');
            $factura->factura_tarifa0 = $request->get('idTarifa0');
            $factura->factura_tarifa12 = $request->get('idTarifa12');
            $factura->factura_iva = $request->get('idIva');
            $factura->factura_total = $request->get('idTotal');
            if($request->get('factura_comentario')){
                $factura->factura_comentario = $request->get('factura_comentario');
            }else{
                $factura->factura_comentario = '';
            }
            $factura->factura_porcentaje_iva = $request->get('factura_porcentaje_iva');
            $factura->factura_emision = $request->get('tipoDoc');
            $factura->factura_ambiente = 'PRODUCCIÓN';
            $factura->factura_autorizacion = $docElectronico->generarClaveAcceso($factura->factura_numero,$request->get('factura_fecha'),"01");
            $factura->factura_estado = '1';
            $factura->bodega_id = $request->get('bodega_id');
            $factura->cliente_id = $request->get('clienteID');
            $factura->forma_pago_id = $request->get('forma_pago_id');
            $factura->rango_id = $request->get('rango_id');
            $factura->vendedor_id = $request->get('vendedor_id');
                /********************cuenta por cobrar***************************/
                $cxc = new Cuenta_Cobrar();
                $cxc->cuenta_descripcion = 'VENTA CON FACTURA No. '.$factura->factura_numero;
                if($request->get('factura_tipo_pago') == 'CREDITO' or $request->get('factura_tipo_pago') == 'CONTADO'){
                    $cxc->cuenta_tipo =$request->get('factura_tipo_pago');
                    $cxc->cuenta_saldo = $request->get('idTotal');
                    $cxc->cuenta_estado = '1';
                }else{
                    $cxc->cuenta_tipo = $request->get('factura_tipo_pago');
                    $cxc->cuenta_saldo = 0.00;
                    $cxc->cuenta_estado = '2';
                }
                $cxc->cuenta_fecha = $request->get('factura_fecha');
                $cxc->cuenta_fecha_inicio = $request->get('factura_fecha');
                $cxc->cuenta_fecha_fin = $request->get('factura_fecha_termino');
                $cxc->cuenta_monto = $request->get('idTotal');
                $cxc->cuenta_valor_factura = $request->get('idTotal');
                $cxc->cliente_id = $request->get('clienteID');
                $cxc->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $cxc->save();
                $general->registrarAuditoria('Registro de cuenta por cobrar de factura -> '.$factura->factura_numero,$factura->factura_numero,'Registro de cuenta por cobrar de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$factura->factura_autorizacion);
                /****************************************************************/
            $factura->cuentaCobrar()->associate($cxc);
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    /**********************asiento diario****************************/
                    $diario = new Diario();
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('factura_fecha'),'CFVE');
                    $diario->diario_fecha = $request->get('factura_fecha');
                    $diario->diario_referencia = 'COMPROBANTE DIARIO DE FACTURA DE VENTA';
                    $diario->diario_tipo_documento = 'FACTURA';
                    $diario->diario_numero_documento = $factura->factura_numero;
                    $diario->diario_beneficiario = $request->get('buscarCliente');
                    $diario->diario_tipo = 'CFVE';
                    $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                    $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('m');
                    $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('Y');
                    $diario->diario_comentario = 'COMPROBANTE DIARIO DE FACTURA: '.$factura->factura_numero;
                    $diario->diario_cierre = '0';
                    $diario->diario_estado = '1';
                    $diario->empresa_id = Auth::user()->empresa_id;
                    $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                    $diario->save();
                    $general->registrarAuditoria('Registro de diario de venta de factura -> '.$factura->factura_numero,$factura->factura_numero,'Registro de diario de venta de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                    /****************************************************************/
                }
                if($banderaP){
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        /**********************asiento diario de costo ****************************/
                        $diarioC = new Diario();
                        $diarioC->diario_codigo = $general->generarCodigoDiario($request->get('factura_fecha'),'CCVP');
                        $diarioC->diario_fecha = $request->get('factura_fecha');
                        $diarioC->diario_referencia = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO';
                        $diarioC->diario_tipo_documento = 'FACTURA';
                        $diarioC->diario_numero_documento = $factura->factura_numero;
                        $diarioC->diario_beneficiario = $request->get('buscarCliente');
                        $diarioC->diario_tipo = 'CCVP';
                        $diarioC->diario_secuencial = substr($diarioC->diario_codigo, 8);
                        $diarioC->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('m');
                        $diarioC->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('Y');
                        $diarioC->diario_comentario = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO CON FACTURA: '.$factura->factura_numero;
                        $diarioC->diario_cierre = '0';
                        $diarioC->diario_estado = '1';
                        $diarioC->empresa_id = Auth::user()->empresa_id;
                        $diarioC->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                        $diarioC->save();
                        $general->registrarAuditoria('Registro de diario de costo de venta de factura -> '.$factura->factura_numero,$factura->factura_numero,'Registro de diario de costo de venta de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diarioC->diario_codigo);
                        /************************************************************************/
                        $factura->diarioCosto()->associate($diarioC);
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
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
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
                    /***********
                     * *****************************************************/
                }
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal');
                    $detalleDiario->detalle_haber = 0.00 ;
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    if ($request->get('factura_tipo_pago') == 'CREDITO' or $request->get('factura_tipo_pago') == 'CONTADO') {
                        $detalleDiario->cliente_id = $request->get('clienteID');
                        $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                        if ($parametrizacionContable->parametrizacion_cuenta_general == '1') {
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        } else {
                            $parametrizacionContable = Cliente::findOrFail($request->get('clienteID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                        }
                    } else {
                        $detalleDiario->detalle_comentario = 'P/R VENTA EN EFECTIVO';
                        $cuentacaja=Caja::caja($arqueoCaja->caja_id)->first();
                        $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo, $factura->factura_numero, 'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                }
                if ($request->get('idIva') > 0){
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $request->get('idIva') ;
                        $detalleDiario->detalle_comentario = 'P/R IVA COBRADO EN VENTA';
                        $detalleDiario->detalle_tipo_documento = 'FACTURA';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA VENTAS')->first();
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$request->get('idIva'));
                    }
                }
                /****************************************************************/
                /****************************************************************/
            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                $factura->diario()->associate($diario);
            }   
            if($arqueoCaja){
                $factura->arqueo_id = $arqueoCaja->arqueo_id;
            }
           
            
            $factura->save();
           
            if(isset($diario)){
                $general->registrarAuditoria('Registro de factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de factura de venta numero -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$factura->factura_autorizacion.' y con codigo de diario -> '.$diario->diario_codigo);
            }else{
                $general->registrarAuditoria('Registro de factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de factura de venta numero -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$factura->factura_autorizacion);
            }
            /*******************************************************************/
            
            $prefac=Prefactura_Venta::findOrFail($prefactura);
            $prefac->factura()->associate($factura);
            $prefac->save();
            
            for ($k = 0; $k < count($guia); ++$k) {
                $guias = Guia_Remision::findOrFail($guia[$k]);      
                $guias->Factura()->associate($factura);
                $guias->gr_estado='2';
                $guias->update();
                $orden=Orden_Despacho::OrdenGuia($guia[$k])->get();
                for ($j = 0; $j < count($orden); ++$j) {
                    $ordene= Orden_Despacho::findOrFail($orden[$j]["orden_id"]);
                    $ordene->Factura()->associate($factura);
                    $ordene->orden_estado="3";
                    if($ordene->orden_reserva == '1' ){
                        $inventarioResevado = true;
                    }
                    if($ordene->orden_reserva == '0' and $inventarioResevado == true){
                        throw new Exception('Hay ordenes con reserva de inventario y hay ordenes sin reserva de inventario, verifique la informacion antes de facturar');
                    }
                    $ordene->update();
                    $general->registrarAuditoria('Actualizacion de Orden de despacho -> '.$orden[$j]["orden_numero"],$orden[$j]["orden_numero"],'Actualizacion de Orden de despacho -> '.$orden[$j]["orden_numero"].' con Guia de remision -> '.$guias->gr_numero.' con Factura  -> '.$factura->factura_numero);        
                }
                $general->registrarAuditoria('Actualizacion de Guia de Remision -> '.$guias->gr_numero,$guias->gr_numero,'Actualizacion de Guia de Remision -> '.$guias->gr_numero.' con Factura -> '.$factura->factura_numero);
            }
            /********************detalle de factura de venta********************/
            $movimientoProducto_id = null;
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                if($inventarioResevado == false){
                    if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                        if($producto->producto_stock < $cantidad[$i]){
                            throw new Exception('Stock insuficiente de productos');
                        }
                    }
                }
                $detalleFV = new Detalle_FV();
                $detalleFV->detalle_cantidad = $cantidad[$i];
                $detalleFV->detalle_precio_unitario = $pu[$i];
                $detalleFV->detalle_descuento = $descuento[$i];
                $detalleFV->detalle_iva = $iva[$i];
                $detalleFV->detalle_total = $total[$i];
                $detalleFV->detalle_descripcion = $nombre[$i];
                $detalleFV->detalle_estado = '1';
                $detalleFV->producto_id = $isProducto[$i];
                if($inventarioResevado == false){
                    /******************registro de movimiento de producto******************/
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('factura_fecha');
                    $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    $movimientoProducto->movimiento_precio=$pu[$i];
                    $movimientoProducto->movimiento_iva=$iva[$i];
                    $movimientoProducto->movimiento_total=$total[$i];
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='FACTURA DE VENTA';
                    $movimientoProducto->movimiento_motivo='VENTA';
                    $movimientoProducto->movimiento_tipo='SALIDA';
                    $movimientoProducto->movimiento_descripcion='FACTURA DE VENTA No. '.$factura->factura_numero;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$factura->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de movimiento de producto por factura de venta numero -> '.$factura->factura_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                
                    $detalleFV->movimiento()->associate($movimientoProducto);
                }else{
                    $movimientoProducto_id = null;
                    for ($k = 0; $k < count($guia); ++$k) {
                        $guias = Guia_Remision::findOrFail($guia[$k]);      
                        $guias->Factura()->associate($factura);
                        $guias->gr_estado='2';
                        $guias->update();
                        $orden=Orden_Despacho::OrdenGuia($guia[$k])->get();
                        for ($j = 0; $j < count($orden); ++$j) {
                            $ordene= Orden_Despacho::findOrFail($orden[$j]["orden_id"]);
                            foreach($ordene->detalles as $detalleOrdenDespacho){
                                if($producto->producto_id == $detalleOrdenDespacho->producto_id and $cantidad[$i] == $detalleOrdenDespacho->detalle_cantidad){
                                    $movimientoProducto_id = $detalleOrdenDespacho->movimiento->movimiento_id;
                                }
                            }
                        }
                    }
                }
                $prefactura=Prefactura_Venta::findOrFail($request->get('prefactura_id'));
                $prefactura->prefactura_estado='2';
                $prefactura->save();
                
                $factura->detalles()->save($detalleFV);
                $general->registrarAuditoria('Registro de detalle de factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de detalle de factura de venta numero -> '.$factura->factura_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $total[$i];
                    $detalleDiario->detalle_comentario = 'P/R VENTA DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    if($inventarioResevado == false){
                        $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    }else{
                        $detalleDiario->movimiento_id = $movimientoProducto_id;
                    }
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_venta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$producto->cuentaVenta->cuenta_numero.' en el haber por un valor de -> '.$total[$i]);
                }
                if($banderaP){
                    if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = 0.00;
                            if($inventarioResevado == false){
                                $detalleDiario->detalle_haber = $movimientoProducto->movimiento_costo_promedio;
                            }else{
                                $detalleDiario->detalle_haber = $producto->producto_precio_costo;
                            }
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                            if($inventarioResevado == false){
                                $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            }else{
                                $detalleDiario->movimiento_id = $movimientoProducto_id;
                            }
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$detalleDiario->detalle_haber);
                        
                            $detalleDiario = new Detalle_Diario();
                            if($inventarioResevado == false){
                                $detalleDiario->detalle_debe = $movimientoProducto->movimiento_costo_promedio;
                            }else{
                                $detalleDiario->detalle_debe = $producto->producto_precio_costo;
                            }
                            $detalleDiario->detalle_haber = 0.00;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            if($inventarioResevado == false){
                                $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            }else{
                                $detalleDiario->movimiento_id = $movimientoProducto_id;
                            }
                            $parametrizacionContable = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$detalleDiario->detalle_debe);
                        }
                    }
                }
            }
            if($request->get('factura_tipo_pago') == 'EN EFECTIVO'){
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="ENTRADA";
                $movimientoCaja->movimiento_descripcion= 'P/R FACTURA DE VENTA :'.$request->get('buscarCliente');
                $movimientoCaja->movimiento_valor= $request->get('idTotal');
                $movimientoCaja->movimiento_documento="FACTURA DE VENTA";
                $movimientoCaja->movimiento_numero_documento= $factura->factura_numero;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                    $movimientoCaja->diario()->associate($diario);
                }
                $movimientoCaja->save();
                /*********************************************************************/
            }
            $url='';
            if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                /*******************************************************************/
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            if($factura->factura_emision == 'ELECTRONICA'){
                $facturaAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlFactura($factura),'FACTURA');
                $factura->factura_xml_estado = $facturaAux->factura_xml_estado;
                $factura->factura_xml_mensaje = $facturaAux->factura_xml_mensaje;
                $factura->factura_xml_respuestaSRI = $facturaAux->factura_xml_respuestaSRI;
                if($facturaAux->factura_xml_estado == 'AUTORIZADO'){
                    $factura->factura_xml_nombre = $facturaAux->factura_xml_nombre;
                    $factura->factura_xml_fecha = $facturaAux->factura_xml_fecha;
                    $factura->factura_xml_hora = $facturaAux->factura_xml_hora;
                }
                $factura->update();
            }
            if($facturaAux->factura_xml_estado == 'AUTORIZADO'){
                return redirect('/listaprefactura')->with('success','Factura registrada y autorizada exitosamente')->with('diario',$url)->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('d-m-Y').'/'.$factura->factura_xml_nombre.'.pdf');
            }elseif($factura->factura_emision != 'ELECTRONICA'){
                return redirect('/listaprefactura')->with('success','Factura registrada exitosamente')->with('diario',$url);
            }else{
                return redirect('/listaprefactura')->with('success','Factura registrada exitosamente')->with('error2','ERROR SRI--> '.$facturaAux->factura_xml_estado.' : '.$facturaAux->factura_xml_mensaje)->with('diario',$url);
            }
            
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaprefactura')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function store(Request $request)
    {
        try{            
            DB::beginTransaction();
            $sacos = 0;
            

            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            $guia = $request->get('Didguia');
            $cliente=$request->get('buscarCliente');

            /********************cabecera de prefactura de venta ********************/
            $general = new generalController();           
            $prefactura = new Prefactura_Venta();
            $cierre = $general->cierre($request->get('factura_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/prefacturaDespacho/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $prefactura->prefactura_numero = $request->get('factura_serie').substr(str_repeat(0, 9).$request->get('factura_numero'), - 9);
            $prefactura->prefactura_serie = $request->get('factura_serie');
            $prefactura->prefactura_secuencial = $request->get('factura_numero');
            $prefactura->prefactura_fecha = $request->get('factura_fecha');
            $prefactura->prefactura_tipo = $request->get('factura_tipo');
            $prefactura->prefactura_aguaje = $request->get('factura_aguaje');
            $prefactura->prefactura_tipo_pago = $request->get('factura_tipo_pago');
            $prefactura->prefactura_dias_plazo = $request->get('factura_dias_plazo');
            $prefactura->prefactura_fecha_pago = $request->get('factura_fecha_termino');
            $prefactura->prefactura_lugar = $request->get('factura_lugar');
            $prefactura->prefactura_subtotal = $request->get('idSubtotal');
            $prefactura->prefactura_descuento = $request->get('idDescuento');
            $prefactura->prefactura_tarifa0 = $request->get('idTarifa0');
            $prefactura->prefactura_tarifa12 = $request->get('idTarifa12');
            $prefactura->prefactura_iva = $request->get('idIva');
            $prefactura->prefactura_total = $request->get('idTotal');      
            if($request->get('factura_comentario')){
                $prefactura->prefactura_comentario = $request->get('factura_comentario');
            }else{
                $prefactura->prefactura_comentario = '';
            }
            $prefactura->prefactura_porcentaje_iva = $request->get('factura_porcentaje_iva');          
            $prefactura->prefactura_estado = '1';
            $prefactura->bodega_id = $request->get('bodega_id');
            $prefactura->cliente_id = $request->get('clienteID');
            $prefactura->rango_id = $request->get('rango_id'); 
            $prefactura->forma_pago_id = $request->get('forma_pago_id'); 
            for ($i = 1; $i < count($cantidad); ++$i) {
               $producto=Producto::findOrFail($isProducto[$i]);

               if($producto->grupo->grupo_nombre=='HIELERA' || $producto->grupo->grupo_nombre=='INSUMOS ACUICOLAS'){
                    $sacos=$sacos+$cantidad[$i];
               }
            }
            $prefactura->prefactura_total_sacos = $sacos; 

            $prefactura->save();
            $general->registrarAuditoria('Registro de PreFactura numero -> '.$prefactura->prefactura_numero,$prefactura->prefactura_numero,'Registro de prefactura de Despacho numero -> '.$prefactura->prefactura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal'));
            /*******************************************************************/
            /********************detalle de factura de venta********************/
           
            for ($i = 1; $i < count($cantidad); ++$i){
                $detallepf = new Detalle_PFV();
                $detallepf->gr_id = $guia[$i];
                $detallepf->detalle_descripcion = $nombre[$i];
                $detallepf->detalle_cantidad = $cantidad[$i];
                $detallepf->detalle_precio_unitario = $pu[$i];
                $detallepf->detalle_descuento = $descuento[$i];
                $detallepf->detalle_iva = $iva[$i];
                $detallepf->detalle_total = $total[$i];              
                $detallepf->detalle_estado = '1';
                $detallepf->producto_id = $isProducto[$i];
                $prefactura->detalles()->save($detallepf);
                $general->registrarAuditoria('Registro de detalle de prefactura numero -> '.$prefactura->prefactura_numero,$prefactura->prefactura_numero,'Registro de detalle de prefactura de Despacho numero -> '.$prefactura->prefactura_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);   
            } 
            
            $url='';
            $url=$general->pdfPrefactura($prefactura);
            DB::commit();
            return redirect('listaGuiasOrdenesPrefactura')->with('success','Transaccion registrada y autorizada exitosamente')->with('pdf',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaGuiasOrdenesPrefactura')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimir($id){ 
        try{ 
            $prefactura = Prefactura_Venta::findOrFail($id);
            $empresa =  Empresa::empresa()->first();
            $saco=0;
            $i=1;
            $movi=null;
   
            $datos=Prefactura_Venta::totales($id)->groupBy('producto.producto_id')->selectRaw('sum(detalle_cantidad) as sum, producto_nombre, producto.producto_id')->get();
            foreach($prefactura->detalles as $detalle){
                    $movimientostotal=Movimiento_Prestamo_Producto::totalproducto($prefactura->cliente_id,$detalle->producto_id,$prefactura->prefactura_fecha)->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_valor')-Movimiento_Prestamo_Producto::totalproducto($prefactura->cliente_id,$detalle->producto_id,$prefactura->prefactura_fecha)->where('movimiento_tipo','=','SALIDA')->sum('movimiento_valor');
                    if($movimientostotal != 0){
                        if($movi==null){
                            $movi[$i]['total']=$movimientostotal;
                            $movi[$i]['descripcion']=$detalle->producto->producto_nombre;
                            $i++;
                           
                        }
                        else{
                            for ($j = 1; $j <= count($movi); ++$j)  {
                                if($movi[$j]['descripcion']!=$detalle->producto->producto_nombre){
                                    $movi[$i]['total']=$movimientostotal;
                                    $movi[$i]['descripcion']=$detalle->producto->producto_nombre;
                                    $i++;
                                }
                            }  
                        }
                       
                           
                    }

                
                $guia=Guia_Remision::findOrFail($detalle->gr_id);
                
                foreach($guia->ordenes as $orden){

                    foreach($orden->detalles as $detalleo){
                        
                        if($detalleo->movimientop){
                            $saco=$saco+$detalleo->movimientop->movimiento_valor;
                        }
                    }
                }
            }
           
            if($prefactura->prefactura_tipo =='MES Y AÑO'){
                $view =  \View::make('admin.formatosPDF.prefacutracionmuelle', ['datos'=> $datos,'empresa'=> $empresa,'prefactura'=> $prefactura]);
            }else{
                $view =  \View::make('admin.formatosPDF.prefacturacion', ['movi'=> $movi,'saco'=> $saco,'empresa'=> $empresa,'prefactura'=> $prefactura]);
            }
            

            $ruta = public_path().'/prefacturacion/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $prefactura->prefactura_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'PFV-'.$prefactura->prefactura_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->stream('guia.pdf');
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function facturar($id)
    {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $usuario=User::Usuario(Auth::user()->user_id)->get()->first();
        $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
        $prefactura=Prefactura_Venta::findOrFail($id);
        $puntoemeision = $prefactura->rangoDocumento->puntoEmision;
        $banderaStock = '1';
        $coun = 1;    
        $datos = null;    
        $inventarioResevado = false; 
        foreach($prefactura->detalles as $detalle){
            $ordenes= Orden_Despacho::OrdenGuia($detalle->guia->gr_id)->get();
            foreach($ordenes as $orden) {
                $ordene= Orden_Despacho::findOrFail($orden->orden_id);
                if($ordene->orden_reserva == '1' ){
                    $inventarioResevado = true;
                }
                if($ordene->orden_reserva == '0' and $inventarioResevado == true){
                    throw new Exception('Hay ordenes con reserva de inventario y hay ordenes sin reserva de inventario, verifique la informacion antes de facturar');
                }
            }
            $productoGuia = Producto::findOrFail($detalle->producto_id);
            if($inventarioResevado == false){
                if($productoGuia->producto_tipo == '1' and $productoGuia->producto_compra_venta == '3'){
                    if($productoGuia->producto_stock < $detalle->detalle_cantidad){
                        $banderaStock = '0';
                    }
                }
            }

            $datos[$coun]['producto_id'] = $detalle->producto_id;
            $datos[$coun]['detalle_cantidad'] = $detalle->detalle_cantidad;
            $datos[$coun]['detalle_descripcion'] = $detalle->detalle_descripcion;
            $datos[$coun]['detalle_precio_unitario'] = $detalle->detalle_precio_unitario;
            $datos[$coun]['detalle_descuento'] = $detalle->detalle_descuento;
            $datos[$coun]['detalle_iva'] = $detalle->detalle_iva;
            $datos[$coun]['detalle_total'] =$detalle->detalle_total;         
            $datos[$coun]['producto_codigo'] = $detalle->producto->producto_codigo;
            $datos[$coun]['producto_stock'] = $detalle->producto->producto_stock;
            $coun++;
        } 
          
        $guias=Prefactura_Venta::Guias($id)->orderBy('guia_remision.gr_numero','desc')->select('guia_remision.gr_id','guia_remision.gr_numero')->distinct()->get();
        

        $rangoDocumento=Rango_Documento::PuntoRango($puntoemeision->punto_id, 'Factura')->first();
            $secuencial=1;      
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Factura_Venta::secuencial($rangoDocumento->rango_id)->max('factura_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;

                }
                return view('admin.ventas.prefactura.facturar',
                ['prefactura'=>$prefactura,
                'banderaStock'=>$banderaStock,
                'datos'=>$datos,
                'guias'=>$guias,
                'vendedores'=>Vendedor::Vendedores()->get(),
                'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                'cajaAbierta'=>$cajaAbierta,
                'formasPago'=>Forma_Pago::formaPagos()->get(), 
                'bodegas'=>Bodega::bodegasSucursal($puntoemeision->punto_id)->get(),
                'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                'PE'=>Punto_Emision::puntos()->get(),
                'rangoDocumento'=>$rangoDocumento,
                'gruposPermiso'=>$gruposPermiso, 
                'tipoPermiso'=>$tipoPermiso,
                'permisosAdmin'=>$permisosAdmin]
                    );
            }else{
                $puntosEmision = Punto_Emision::PuntoxSucursal($puntoemeision->sucursal_id)->get();
                foreach($puntosEmision as $punto){
                    $rangoDocumento=Rango_Documento::PuntoRango($punto->punto_id, 'Factura')->first();
                    if($rangoDocumento){
                        $puntoemeision = $punto;
                        break;
                    }
                }
                if($rangoDocumento){
                    $secuencial=$rangoDocumento->rango_inicio;
                    $secuencialAux=Factura_Venta::secuencial($rangoDocumento->rango_id)->max('factura_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                    return view('admin.ventas.prefactura.facturar',
                    ['prefactura'=>$prefactura,
                    'banderaStock'=>$banderaStock,
                    'datos'=>$datos,
                    'guias'=>$guias,
                    'vendedores'=>Vendedor::Vendedores()->get(),
                    'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                    'cajaAbierta'=>$cajaAbierta,
                    'formasPago'=>Forma_Pago::formaPagos()->get(), 
                    'bodegas'=>Bodega::bodegasSucursal($puntoemeision->punto_id)->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'tipoPermiso'=>$tipoPermiso,
                    'gruposPermiso'=>$gruposPermiso, 
                    'permisosAdmin'=>$permisosAdmin]
                        );
                }else{
                    return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
                }
            }
            
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
        //
    }
}
