<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Ingreso_Bodega;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tarifa_Iva;
use App\Models\Bodega;
use App\Models\Centro_Consumo;
use App\Models\Cuenta;
use App\Models\Cuenta_Pagar;
use App\Models\Detalle_IB;
use App\Models\Movimiento_Producto;
use App\Models\Diario;
use App\Models\Detalle_Diario;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Tipo_MI;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ingresoBodegaController extends Controller
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
            
            $bodega=Ingreso_Bodega::BodegaDistinsc()->select('bodega.bodega_id','bodega.bodega_nombre')->distinct()->get();
            return view('admin.inventario.ingresoBodega.view',['bodega'=>$bodega, 'puntoEmisiones'=>$puntoEmisiones,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $nombre = $request->get('Ddescripcion');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');  
            $consumo = $request->get('Didconsumo'); 
            $auditoria = new generalController();
            $cierre = $auditoria->cierre($request->get('ingreso_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/ingresoBodega/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }      
            /********************cabecera de ingreso de venta ********************/
            $general = new generalController();           
            $ingreso = new Ingreso_Bodega();
            $ingreso->cabecera_ingreso_numero = $request->get('ingreso_serie').substr(str_repeat(0, 9).$request->get('ingreso_numero'), - 9);
            $ingreso->cabecera_ingreso_serie = $request->get('ingreso_serie');
            $ingreso->cabecera_ingreso_secuencial = $request->get('ingreso_numero');
            $ingreso->cabecera_ingreso_fecha = $request->get('ingreso_fecha');
            $ingreso->cabecera_ingreso_motivo = $request->get('ingresomotivo');
            $ingreso->cabecera_ingreso_pago = $request->get('ingreso_tipo_pago');
            $ingreso->cabecera_ingreso_plazo = '0';
            $ingreso->cabecera_ingreso_total = $request->get('idTotal');
            if($request->get('ingreso_comentario')){
                $ingreso->cabecera_ingreso_comentario = $request->get('ingreso_comentario');
            }else{
                $ingreso->cabecera_ingreso_comentario = '';
            }
            $ingreso->cabecera_ingreso_estado = '1';
            $ingreso->bodega_id = $request->get('bodega_id');
            $ingreso->tipo_id = $request->get('tipo');
            $ingreso->user_id = Auth::user()->user_id;
            $ingreso->rango_id = $request->get('rango_id');
            $ingreso->proveedor_id = $request->get('proveedorID');

            /********************cuenta por pagar***************************/
            $cxp = new Cuenta_Pagar();
            $cxp->cuenta_descripcion = 'INGRESO DE BODEGA DE PRODUCTO A PROVEEDOR'.$request->get('buscarProveedor').' CON DOCUMENTO No. '.$ingreso->cabecera_ingreso_numero;
            if($request->get('transaccion_tipo_pago') == 'CREDITO'){
                $cxp->cuenta_tipo ='CREDITO';
                $cxp->cuenta_saldo = $request->get('idTotal');
                $cxp->cuenta_estado = '1';
            }else{
                $cxp->cuenta_tipo = 'OTRO';
                $cxp->cuenta_saldo = 0.00;
                $cxp->cuenta_estado = '2';
            }
            $cxp->cuenta_fecha = $request->get('ingreso_fecha');
            $cxp->cuenta_fecha_inicio = $request->get('ingreso_fecha');
            $cxp->cuenta_fecha_fin = date("Y-m-d",strtotime($request->get('ingreso_fecha')."+ ".$request->get('ingreso_dias_plazo')." days"));
            $cxp->cuenta_monto = $request->get('idTotal');
            $cxp->cuenta_valor_factura = $request->get('idTotal');
            $cxp->proveedor_id = $ingreso->proveedor_id;
            $cxp->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
            $cxp->save();
            $general->registrarAuditoria('Registro de cuenta por pagar de ingreso de bodega de producto  -> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Registro de cuenta por pagar de ingreso de bodega de producto -> '.$ingreso->cabecera_ingreso_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
            /****************************************************************/
            $ingreso->cuentaPagar()->associate($cxp);

            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('ingreso_fecha'),'CIBP');
                $diario->diario_tipo = 'CIBP';
                $diario->diario_fecha = $request->get('ingreso_fecha');
                $diario->diario_referencia = 'COMPROBANTE DE INGRESO DE BODEGA DE PRODUCTO';
                $diario->diario_tipo_documento = 'INGRESO DE BODEGA';
                $diario->diario_numero_documento = $ingreso->cabecera_ingreso_numero;
                $diario->diario_beneficiario = $request->get('buscarProveedor');
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('ingreso_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('ingreso_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE INGRESO DE BODEGA DE PRODUCTO : '.$ingreso->cabecera_ingreso_numero.', '.$request->get('ingreso_comentario');
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de ingreso de bodega de producto -> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Registro de diario de ingreso de bodega de producto -> '.$ingreso->cabecera_ingreso_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                $ingreso->diario()->associate($diario);
            }
            $ingreso->save();
            $general->registrarAuditoria('Registro de ingreso de bodega de producto numero -> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Registro de ingreso de bodega de producto numero -> '.$ingreso->cabecera_ingreso_numero.' con bodega -> '.$request->get('bodega_nombre').' con un total de -> '.$request->get('idTotal')); 
            /********************detalle de factura de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                $detalleIB = new Detalle_IB();
                $detalleIB->detalle_ingreso_cantidad = $cantidad[$i];
                $detalleIB->detalle_ingreso_precio_unitario = $pu[$i];
                $detalleIB->detalle_ingreso_total = $total[$i];  
                $detalleIB->detalle_ingreso_descripcion = $nombre[$i]; 
                $detalleIB->detalle_ingreso_estado = '1';
                $detalleIB->producto_id = $isProducto[$i]; 
                $detalleIB->centro_consumo_id = $consumo[$i]; 
                /******************registro de movimiento de producto******************/
                $movimientoProducto = new Movimiento_Producto();
                $movimientoProducto->movimiento_fecha=$request->get('ingreso_fecha');
                $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                $movimientoProducto->movimiento_precio=$pu[$i];
                $movimientoProducto->movimiento_iva=0;
                $movimientoProducto->movimiento_total=$total[$i];
                $movimientoProducto->movimiento_stock_actual=0;
                $movimientoProducto->movimiento_costo_promedio=0;
                $movimientoProducto->movimiento_documento='INGRESO DE BODEGA';
                $movimientoProducto->movimiento_motivo='COMPRA';
                $movimientoProducto->movimiento_tipo='ENTRADA';
                $movimientoProducto->movimiento_descripcion='INGRESO DE BODEGA No. '.$ingreso->cabecera_ingreso_numero;
                $movimientoProducto->movimiento_estado='1';
                $movimientoProducto->producto_id=$isProducto[$i];
                $movimientoProducto->bodega_id=$ingreso->bodega_id;
                $movimientoProducto->centro_consumo_id=$detalleIB->centro_consumo_id;
                $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                $movimientoProducto->save();
                $general->registrarAuditoria('Registro de movimiento de producto por ingreso de bodega numero -> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Registro de movimiento de producto por factura de venta numero -> '.$ingreso->cabecera_ingreso_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                /*********************************************************************/
                $detalleIB->movimiento()->associate($movimientoProducto);
                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                    /********************detalle de diario de compra********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $total[$i];
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R INGRESO DE BODEGA DE PRODUCTO CON CODIGO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'INGRESO DE BODEGA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($movimientoProducto);       
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;           
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo, $ingreso->cabecera_ingreso_numero, 'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$total[$i]);
                    /**********************************************************************/
                }
                $ingreso->detalles()->save($detalleIB);
                $general->registrarAuditoria('Registro de detalle de ingreso de bodega -> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Registro de detalle de ingreso de bodega -> '.$ingreso->cabecera_ingreso_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);         
            }  
            
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe =0.00;
                $detalleDiario->detalle_haber = $request->get('idTotal');
                $detalleDiario->detalle_tipo_documento = 'INGRESO DE BODEGA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($request->get('transaccion_tipo_pago') == 'CREDITO'){
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first(); 
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                    }
                }else{
                    $cuenta = Tipo_MI::TipoMovimiento($request->get('tipo'))->first();
                    $detalleDiario->cuenta_id = $cuenta->cuenta_id;
                }
                $detalleDiario->detalle_comentario = 'P/R INGRESO DE PRODUCTOS CON PROVEEDOR '.$request->get('buscarProveedor');  
                $detalleDiario->proveedor_id=$request->get('proveedorID');
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$ingreso->cabecera_ingreso_numero, 'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idTotal'));
            }
            $url = '';
            if(Auth::user()->empresa->empresa_llevaContabilidad == '1'){
                $url = $general->pdfDiario($diario);
               
            }
            $url2 = $general->pdfComprobanteIngresoBodega($ingreso);
            DB::commit();
            return redirect('/ingresoBodega/new/'.$request->get('punto_id'))->with('success','ingreso de bodega se registrada exitosamente')->with('diario',$url)->with('pdf2',$url2);
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
        try {
            DB::beginTransaction();
            $ingreso = Ingreso_Bodega::findOrFail($id);
            $auditoria = new generalController();
            $cierre = $auditoria->cierre($ingreso->cabecera_ingreso_fecha,$ingreso->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('ingresoBodega')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach($ingreso->detalles as $detalle){                
                $movimiento=$detalle->movimiento;
                $detalle->movimiento_id=null;
                $detalle->update();              
                $auditoria->registrarAuditoria('Actualizacion del Id a null para la eliminacion del Movimiento  motivo'.$movimiento->movimiento_motivo.' y tipo '.$movimiento->movimiento_tipo.'  relacionado al ingreso de Bodega N°-> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Permiso con id -> '.$id);         
                
                $detalle->delete();
                $auditoria->registrarAuditoria('Eliminacion del Detalle ingreso de Bodega N°-> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Permiso con id -> '.$id);   
            } 
            if (isset($ingreso->diario)) {
                $diario=$ingreso->diario;
                $ingreso->diario_id=null;
                $ingreso->save();
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    $auditoria->registrarAuditoria('Actualizacion del Id a null para la eliminacion del Diario  N°'.$diario->diario_codigo.'  relacionado al ingreso de Bodega N°-> '.$ingreso->cabecera_ingreso_numero, $ingreso->cabecera_ingreso_numero, 'Permiso con id -> '.$id);
                    foreach ($diario->detalles as $diariodetalle) {
                        $diariodetalle->delete();
                        $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario->diario_codigo .'relacionado al ingreso de Bodega N°-> '.$ingreso->cabecera_ingreso_numero.' Con Documento detalle diario '.$diariodetalle->detalle_tipo_documento, $ingreso->cabecera_ingreso_numero, 'Eliminacion del detalle diario con Permiso con id -> '.$id);
                    }
                    $diario->delete();
                    $auditoria->registrarAuditoria('Eliminacion del  diario  N°'.$diario->diario_codigo .'relacionado al ingreso de Bodega N°-> '.$ingreso->cabecera_ingreso_numero, $ingreso->cabecera_ingreso_numero, 'Permiso con id -> '.$id);
                }
            }
            $movimiento->delete();
            $auditoria->registrarAuditoria('Eliminacion del Movimiento  motivo'.$movimiento->movimiento_motivo.' y tipo '.$movimiento->movimiento_tipo.'  relacionado al ingreso de Bodega N°-> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Permiso con id -> '.$id);   
                
            $ntaux= $ingreso;
            foreach ($ntaux->cuentapagar->pagos as $detalle) { 
                    $detalle->delete();
                    $auditoria->registrarAuditoria('Eliminacion del detalle de pago cuentas por pagar  '.$ingreso->cuentapagar->cuenta_descripcion.' con ingreso N°'.$ingreso->cabecera_ingreso_numero ,$ingreso->cabecera_ingreso_numero,'Eliminacion del detalle diario con Permiso con id -> '.$id);  
            }
            foreach ($ingreso->cuentapagar->pagos as $detalle) {
                $detalle->pagoCXP->delete();
                $auditoria->registrarAuditoria('Eliminacion del Pago ceuntas por pagar'.$ingreso->cuentapagar->cuenta_descripcion.' con ingreso N° '.$ingreso->cabecera_ingreso_numero ,$ingreso->cabecera_ingreso_numero,'Eliminacion del detalle diario con Permiso con id -> '.$id);  
            }
            $ntaux->cuenta_id=null;
            $ntaux->save();
            $ingreso->cuentapagar->delete();
            $auditoria->registrarAuditoria('Eliminacion de la cuenta por pagar  con ingreso N°'.$ingreso->cabecera_ingreso_numero ,$ingreso->cabecera_ingreso_numero,'Eliminacion  con Permiso con id -> '.$id);  
            $ingreso->delete(); 
            $auditoria->registrarAuditoria('Eliminacion del ingreso de Bodega N°-> '.$ingreso->cabecera_ingreso_numero,$ingreso->cabecera_ingreso_numero,'Permiso con id -> '.$id);
            DB::commit();
            return redirect('ingresoBodega')->with('success','Datos eliminados exitosamente'); 
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('ingresoBodega')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
    public function nuevo($id){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Ingreso de Bodega')->first();
            $secuencial=1;
            $sucursalp=Punto_Emision::punto($id)->first();
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Ingreso_Bodega::secuencial($rangoDocumento->rango_id)->max('cabecera_ingreso_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                return view('admin.inventario.ingresoBodega.nuevo',['cajaAbierta'=>Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'tipo'=>Tipo_MI::TipoMovimientos()->where('sucursal_id','=',$sucursalp->sucursal_id)->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($id)->get(), 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
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
            $ingreso=Ingreso_Bodega::ingreso($id)->first();
            if($ingreso){
                return view('admin.inventario.ingresoBodega.eliminar',['centros'=>Centro_Consumo::CentroConsumos()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'ingreso'=>$ingreso,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function Presentarvisualizar($id)
    {
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ingreso=Ingreso_Bodega::ingreso($id)->first();
            if($ingreso){
                return view('admin.inventario.ingresoBodega.visualizar',['centros'=>Centro_Consumo::CentroConsumos()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'ingreso'=>$ingreso,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $bodega=Ingreso_Bodega::BodegaDistinsc()->select('bodega.bodega_id','bodega.bodega_nombre')->distinct()->get();
            $ingreso=null;
            
           
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_bodega') == "--TODOS--") {
                $ingreso=Ingreso_Bodega::ingresos()->get();
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_bodega') != "--TODOS--" ) {
                $ingreso=Ingreso_Bodega::ingresosDiferentes($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_bodega'))->get();
                        
            }             
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_bodega') == "--TODOS--" ) {
                $ingreso=Ingreso_Bodega::ingresosFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->get();
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_bodega') != "--TODOS--") {
                $ingreso=Ingreso_Bodega::ingresosBodega($request->get('nombre_bodega'))->get();
            }   
            return view('admin.inventario.ingresoBodega.view', ['nombre_bodega'=>$request->get('nombre_bodega'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'ingreso'=>$ingreso,'bodega'=>$bodega,  'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        
        }catch(\Exception $ex){
            return view('admin.inventario.ingresoBodega.view',['ingreso'=>$ingreso,'bodega'=>$bodega, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin])->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
