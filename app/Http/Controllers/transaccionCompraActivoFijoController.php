<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activo_Fijo;
use App\Models\Anticipo_Proveedor;
use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Centro_Consumo;
use App\Models\Concepto_Retencion;
use App\Models\Cuenta_Pagar;
use App\Models\Descuento_Anticipo_Proveedor;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Pago_CXP;
use App\Models\Detalle_RC;
use App\Models\Detalle_TC;
use App\Models\Diario;
use App\Models\Documento_Anulado;
use App\Models\Empresa;
use App\Models\Firma_Electronica;
use App\Models\Grupo_Activo;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Producto;
use App\Models\Pago_CXP;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Retencion_Compra;
use App\Models\Sustento_Tributario;
use App\Models\Tarifa_Iva;
use App\Models\Tipo_Comprobante;
use App\Models\Transaccion_Compra;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class transaccionCompraActivoFijoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/denegado');
    }
    public function nuevo($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $rangoDocumento=Rango_Documento::PuntoRango($id,'Comprobante de Retención')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Retencion_Compra::secuencial($rangoDocumento->rango_id)->max('retencion_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $firmaElectronica = Firma_Electronica::firma()->first();
                $pubKey =Crypt::decryptString($firmaElectronica->firma_pubKey);
                $data=openssl_x509_parse($pubKey,true);
                return view('admin.compras.transaccionCompraActivoFijo.index',['activos'=>Grupo_Activo::GrupoxSucursal($rangoDocumento->puntoEmision->sucursal_id)->get(),'caduca'=>$data['validTo_time_t'],'cajaAbierta'=>$cajaAbierta,'rangoDocumento'=>$rangoDocumento,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'bodegas'=>Bodega::bodegasSucursal($id)->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'comprobantes'=>Tipo_Comprobante::tipos()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir retenciones, configueros y vuelva a intentar');
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
    public function store(Request $request)
    {
        try{            
            DB::beginTransaction();
            $compraAux = Transaccion_Compra::TransaccionDuplicada($request->get('transaccion_serie').substr(str_repeat(0, 9).$request->get('transaccion_secuencial'), - 9),$request->get('tipo_comprobante_id'),$request->get('proveedorID'))->first();
            if(isset($compraAux->transaccion_id)){
                throw new Exception('Ese documento ya se encuentra registrado en el sistema.');
            }
            $valorCXP= $request->get('idTotal')-$request->get('id_total_fuente')-$request->get('id_total_iva');
            /********************detalle de la compra ********************/
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            $bodega = $request->get('Dbodega');
            $cconsumo = $request->get('Dcconsumo');
            $descripcion = $request->get('Ddescripcion');
            /****************************************************************/
            /***********************detalle de la retencion **********************/
            $baseF = $request->get('DbaseRF');
            $idRetF = $request->get('DRFID');
            $porcentajeF = $request->get('DporcentajeRF');
            $valorF = $request->get('DvalorRF');

            $baseI = $request->get('DbaseRI');
            $idRetI = $request->get('DRIID');
            $porcentajeI = $request->get('DporcentajeRI');
            $valorI = $request->get('DvalorRI');
            /*********************************************************************/
            /********************cabecera de transaccion de compra ********************/
            $general = new generalController();
            $docElectronico = new facturacionElectronicaController();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $transaccion = new Transaccion_Compra();
            $general = new generalController();
            $cierre = $general->cierre($request->get('transaccion_fecha'),Punto_Emision::Punto($request->get('punto_id'))->first()->sucursal_id);          
            if($cierre){
                return redirect('/transaccionCompra/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cierre = $general->cierre($request->get('transaccion_inventario'),Punto_Emision::Punto($request->get('punto_id'))->first()->sucursal_id);          
            if($cierre){
                return redirect('/transaccionCompra/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $facManual='';
            if($request->get('manualID')=='on'){
                $facManual = substr($request->get('serieFacManual'),0,3).'-'.substr($request->get('serieFacManual'),3,3).'-'.substr(str_repeat(0, 9).$request->get('scuencialFacManual'), - 9);
            }
            $transaccion->transaccion_fecha = $request->get('transaccion_fecha');
            $transaccion->transaccion_caducidad = $request->get('transaccion_caducidad');
            $transaccion->transaccion_impresion = $request->get('transaccion_impresion');
            $transaccion->transaccion_vencimiento = $request->get('transaccion_vencimiento');
            $transaccion->transaccion_inventario = $request->get('transaccion_inventario');
            $transaccion->transaccion_numero = $request->get('transaccion_serie').substr(str_repeat(0, 9).$request->get('transaccion_secuencial'), - 9);
            $transaccion->transaccion_serie = $request->get('transaccion_serie');
            $transaccion->transaccion_secuencial = $request->get('transaccion_secuencial');
            $transaccion->transaccion_subtotal = $request->get('idSubtotal');
            $transaccion->transaccion_descuento = $request->get('idDescuento');
            $transaccion->transaccion_tarifa0 = $request->get('idTarifa0');
            $transaccion->transaccion_tarifa12 = $request->get('idTarifa12');
            $transaccion->transaccion_iva = $request->get('idIva');
            $transaccion->transaccion_total = $request->get('idTotal');
            $transaccion->transaccion_ivaB = $request->get('IvaBienesID');
            $transaccion->transaccion_ivaS = $request->get('IvaServiciosID');
            $transaccion->transaccion_dias_plazo = $request->get('transaccion_dias_plazo');
            $transaccion->transaccion_descripcion = $request->get('transaccion_descripcion');
            $transaccion->transaccion_tipo_pago = $request->get('transaccion_tipo_pago');
            $transaccion->transaccion_porcentaje_iva = $request->get('transaccion_porcentaje_iva');
            $transaccion->transaccion_autorizacion = $request->get('transaccion_autorizacion');
            $transaccion->transaccion_estado = '1';
            $transaccion->proveedor_id = $request->get('proveedorID');
            $transaccion->tipo_comprobante_id = $request->get('tipo_comprobante_id');
            $transaccion->sustento_id = $request->get('sustento_id');
            $transaccion->sucursal_id = Punto_Emision::Punto($request->get('punto_id'))->first()->sucursal_id;
            
            $tipoComprobante = Tipo_Comprobante::tipo($request->get('tipo_comprobante_id'))->first();
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                /********************cuenta por pagar***************************/
                $cxp = new Cuenta_Pagar();
                $cxp->cuenta_descripcion = strtoupper($tipoComprobante->tipo_comprobante_nombre).' DE COMPRA A PROVEEDOR '.$request->get('buscarProveedor').' CON DOCUMENTO No. '.$transaccion->transaccion_numero;
                if($request->get('transaccion_tipo_pago') == 'CREDITO' or $request->get('transaccion_tipo_pago') == 'CONTADO'){
                    $cxp->cuenta_tipo =$request->get('transaccion_tipo_pago');
                    $cxp->cuenta_saldo = $valorCXP;
                    $cxp->cuenta_estado = '1';
                }else{
                    $cxp->cuenta_tipo = $request->get('transaccion_tipo_pago');
                    $cxp->cuenta_saldo = 0.00;
                    $cxp->cuenta_estado = '2';
                }
                $cxp->cuenta_fecha = $request->get('transaccion_fecha');
                $cxp->cuenta_fecha_inicio = $request->get('transaccion_fecha');
                $cxp->cuenta_fecha_fin = date("Y-m-d",strtotime($request->get('transaccion_fecha')."+ ".$request->get('transaccion_dias_plazo')." days"));
                $cxp->cuenta_monto = $valorCXP;
                $cxp->cuenta_valor_factura = $request->get('idTotal');
                $cxp->proveedor_id = $transaccion->proveedor_id;
                $cxp->sucursal_id = $transaccion->sucursal_id;
                $cxp->save();
                $general->registrarAuditoria('Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero,$transaccion->transaccion_numero,'Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                /****************************************************************/
                $transaccion->cuentaPagar()->associate($cxp);
            }
                /**********************asiento diario****************************/
            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                $diario = new Diario();
                $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero;
                if ($tipoComprobante->tipo_comprobante_codigo == '01') {
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'), 'CFCR');
                    $diario->diario_tipo = 'CFCR';
                } elseif ($tipoComprobante->tipo_comprobante_codigo == '04') {
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'), 'CNCR');
                    $diario->diario_tipo = 'CNCR';
                    if ($request->get('manualID')=='on') {
                        $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero.' CON FACTURA : '.$facManual;
                    } else {
                        $facturaAux = Transaccion_Compra::Transaccion($request->get('factura_id'))->first();
                        $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero.' CON FACTURA : '.$facturaAux->transaccion_numero;
                    }
                } elseif ($tipoComprobante->tipo_comprobante_codigo == '05') {
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'), 'CNDR');
                    $diario->diario_tipo = 'CNDR';
                    if ($request->get('manualID')=='on') {
                        $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero.' CON FACTURA : '.$facManual;
                    } else {
                        $facturaAux = Transaccion_Compra::Transaccion($request->get('factura_id'))->first();
                        $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero.' CON FACTURA : '.$facturaAux->transaccion_numero;
                    }
                } else {
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'), 'CTCR');
                    $diario->diario_tipo = 'CTCR';
                }
                $diario->diario_fecha = $request->get('transaccion_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' DE COMPRA';
                $diario->diario_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $diario->diario_numero_documento = $transaccion->transaccion_numero;
                $diario->diario_beneficiario = $request->get('buscarProveedor');
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('transaccion_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('transaccion_fecha'))->format('Y');
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = $transaccion->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de compra con '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' -> '.$transaccion->transaccion_numero, $transaccion->transaccion_numero, 'Registro de diario de compra de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                /****************************************************************/
            }
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                if($cxp->cuenta_estado == '2'){
                    /********************Pago por compra en efectivo***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = 'PAGO EN EFECTIVO';
                    $pago->pago_fecha = $cxp->cuenta_fecha;
                    $pago->pago_tipo = 'PAGO EN EFECTIVO';
                    $pago->pago_valor = $cxp->cuenta_monto;
                    $pago->pago_estado = '1';
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $pago->diario()->associate($diario);
                    }
                    if($arqueoCaja){
                        $pago->arqueo_id = $arqueoCaja->arqueo_id;
                    }
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = 'PAGO EN EFECTIVO';
                    $detallePago->detalle_pago_valor = $cxp->cuenta_monto; 
                    $detallePago->detalle_pago_cuota = 1;
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_pagar_id = $cxp->cuenta_id; 
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();
                /****************************************************************/
                }
                if ($tipoComprobante->tipo_comprobante_codigo == '05') {
                    if($request->get('manualID')=='on'){
                        $transaccion->transaccion_factura_manual = $facManual;
                        $transaccion->transaccion_autorizacion_manual = $request->get('autorizacionFacManual');
                    }else{
                        $facturaAux = Transaccion_Compra::Transaccion($request->get('factura_id'))->first(); 
                        $transaccion->transaccion_id_f = $facturaAux->transaccion_id;
                    }
                }
            }
            if($tipoComprobante->tipo_comprobante_codigo == '04'){
                if($request->get('manualID')=='on'){
                    $transaccion->transaccion_factura_manual = $facManual;
                    $transaccion->transaccion_autorizacion_manual = $request->get('autorizacionFacManual');
                }else{
                    $facturaAux = Transaccion_Compra::Transaccion($request->get('factura_id'))->first(); 
                    $cxpAux = $facturaAux->cuentaPagar;
                    $transaccion->transaccion_id_f = $facturaAux->transaccion_id;
                }
                if($request->get('manualID')=='on'){
                    $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($request->get('punto_id'), 'Anticipo de Proveedor')->first();
                    if($rangoDocumentoAnticipo){
                        $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                        $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal($transaccion->sucursal_id)->get();
                        foreach($puntosEmision as $punto){
                            $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Proveedor')->first();
                            if($rangoDocumentoAnticipo){
                                break;
                            }
                        }
                        if($rangoDocumentoAnticipo){
                            $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                            $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                            if($secuencialAux){$secuencial=$secuencialAux+1;}
                        }else{
                            throw new Exception('No tiene punto de emision de anticipo de proveedor.');
                        }
                    }
                    $anticipoProveedor = new Anticipo_Proveedor();
                    $anticipoProveedor->anticipo_numero = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                    $anticipoProveedor->anticipo_serie = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie;
                    $anticipoProveedor->anticipo_secuencial = $secuencial;
                    $anticipoProveedor->anticipo_fecha = $transaccion->transaccion_fecha;
                    $anticipoProveedor->anticipo_tipo = 'NOTA DE CRÉDITO';      
                    $anticipoProveedor->anticipo_documento = $transaccion->transaccion_numero;     
                    $anticipoProveedor->anticipo_motivo = $transaccion->transaccion_descripcion;
                    $anticipoProveedor->anticipo_valor = $request->get('idTotal');  
                    $anticipoProveedor->anticipo_saldo = $request->get('idTotal');   
                    $anticipoProveedor->proveedor_id = $transaccion->proveedor_id;
                    $anticipoProveedor->anticipo_estado = 1; 
                    $anticipoProveedor->rango_id = $rangoDocumentoAnticipo->rango_id;
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $anticipoProveedor->diario()->associate($diario);
                    }
                    $anticipoProveedor->save();
                    $general->registrarAuditoria('Registro de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        /********************detalle de diario de venta********************/
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $request->get('idTotal');
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE PROVEEDOR';
                        $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->proveedor_id = $request->get('proveedorID');
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
                        if ($parametrizacionContable->parametrizacion_cuenta_general == '1') {
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        } else {
                            $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
                        }
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo, $transaccion->transaccion_numero, 'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    }
                }elseif($cxpAux->cuenta_saldo == 0){
                    $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($request->get('punto_id'), 'Anticipo de Proveedor')->first();
                    if($rangoDocumentoAnticipo){
                        $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                        $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal($transaccion->sucursal_id)->get();
                        foreach($puntosEmision as $punto){
                            $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Proveedor')->first();
                            if($rangoDocumentoAnticipo){
                                break;
                            }
                        }
                        if($rangoDocumentoAnticipo){
                            $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                            $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                            if($secuencialAux){$secuencial=$secuencialAux+1;}
                        }else{
                            throw new Exception('No tiene punto de emision de anticipo de proveedor.');
                        }
                    }
                    $anticipoProveedor = new Anticipo_Proveedor();
                    $anticipoProveedor->anticipo_numero = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                    $anticipoProveedor->anticipo_serie = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie;
                    $anticipoProveedor->anticipo_secuencial = $secuencial;
                    $anticipoProveedor->anticipo_fecha = $transaccion->transaccion_fecha;
                    $anticipoProveedor->anticipo_tipo = 'NOTA DE CRÉDITO';      
                    $anticipoProveedor->anticipo_documento = $transaccion->transaccion_numero;     
                    $anticipoProveedor->anticipo_motivo = $transaccion->transaccion_descripcion;
                    $anticipoProveedor->anticipo_valor = $request->get('idTotal');  
                    $anticipoProveedor->anticipo_saldo = $request->get('idTotal');   
                    $anticipoProveedor->proveedor_id = $facturaAux->proveedor_id;
                    $anticipoProveedor->anticipo_estado = 1; 
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $anticipoProveedor->diario()->associate($diario);
                    }
                    $anticipoProveedor->rango_id = $rangoDocumentoAnticipo->rango_id;
                    $anticipoProveedor->save();
                    $general->registrarAuditoria('Registro de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        /********************detalle de diario de venta********************/
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $request->get('idTotal');
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE PROVEEDOR';
                        $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->proveedor_id = $request->get('proveedorID');
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
                        if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        }else{
                            $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
                        }
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                        /*******************************************************************/
                    }
                }else if(round($cxpAux->cuenta_saldo,2) >= round($transaccion->transaccion_total,2)){
                    /********************Pago por Nota de Credito***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero;
                    $pago->pago_fecha = $request->get('transaccion_fecha');
                    $pago->pago_tipo = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $pago->pago_valor = $request->get('idTotal');
                    $pago->pago_estado = '1';
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $pago->diario()->associate($diario);
                    }
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero; 
                    $detallePago->detalle_pago_valor = $request->get('idTotal'); 
                    $detallePago->detalle_pago_cuota = Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->count()+1; 
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_pagar_id = $cxpAux->cuenta_id; 
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();

                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux->transaccionCompra->transaccion_id)->sum('descuento_valor'),2);
                    if(round($cxpAux->cuenta_saldo,2) == 0){
                        $cxpAux->cuenta_estado = '2';
                    }else{
                        $cxpAux->cuenta_estado = '1';
                    }
                    $cxpAux->update();
                    /****************************************************************/
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        /********************detalle de diario de venta********************/
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $request->get('idTotal');
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                        $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->proveedor_id = $request->get('proveedorID');
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                        if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        }else{
                            $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                        }
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                        /*******************************************************************/
                    }
                }else{
                    /********************Anticipo a proveedor**************************/
                    $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($request->get('punto_id'), 'Anticipo de Proveedor')->first();
                    if($rangoDocumentoAnticipo){
                        $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                        $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal($transaccion->sucursal_id)->get();
                        foreach($puntosEmision as $punto){
                            $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Proveedor')->first();
                            if($rangoDocumentoAnticipo){
                                break;
                            }
                        }
                        if($rangoDocumentoAnticipo){
                            $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                            $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                            if($secuencialAux){$secuencial=$secuencialAux+1;}
                        }else{
                            throw new Exception('No tiene punto de emision de anticipo de proveedor.');
                        }
                    }
                    $anticipoProveedor = new Anticipo_Proveedor();
                    $anticipoProveedor->anticipo_numero = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                    $anticipoProveedor->anticipo_serie = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie;
                    $anticipoProveedor->anticipo_secuencial = $secuencial;
                    $anticipoProveedor->anticipo_fecha = $transaccion->transaccion_fecha;
                    $anticipoProveedor->anticipo_tipo = 'NOTA DE CRÉDITO';      
                    $anticipoProveedor->anticipo_documento = $transaccion->transaccion_numero;     
                    $anticipoProveedor->anticipo_motivo = $transaccion->transaccion_descripcion;
                    $anticipoProveedor->anticipo_valor = $request->get('idTotal') - $cxpAux->cuenta_saldo; 
                    $anticipoProveedor->anticipo_saldo = $request->get('idTotal') - $cxpAux->cuenta_saldo;
                    $anticipoProveedor->proveedor_id = $facturaAux->proveedor_id;
                    $anticipoProveedor->anticipo_estado = 1; 
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $anticipoProveedor->diario()->associate($diario);
                    }
                    $anticipoProveedor->rango_id = $rangoDocumentoAnticipo->rango_id;
                    $anticipoProveedor->save();
                    $general->registrarAuditoria('Registro de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        /********************detalle de diario de venta********************/
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $request->get('idTotal') - $cxpAux->cuenta_saldo;
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE PROVEEDOR';
                        $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->proveedor_id = $request->get('proveedorID');
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
                        if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        }else{
                            $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
                        }
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal') - $cxpAux->cuenta_saldo);
                    }
                    /*******************************************************************/
                    /********************Pago por Nota de Credito***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero;
                    $pago->pago_fecha = $request->get('transaccion_fecha');
                    $pago->pago_tipo = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $pago->pago_valor = $cxpAux->cuenta_saldo;
                    $pago->pago_estado = '1';
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $pago->diario()->associate($diario);
                    }
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero; 
                    $detallePago->detalle_pago_valor = $cxpAux->cuenta_saldo; 
                    $detallePago->detalle_pago_cuota = Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->count()+1; 
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_pagar_id = $cxpAux->cuenta_id; 
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();

                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux->transaccionCompra->transaccion_id)->sum('descuento_valor'),2);
                    if(round($cxpAux->cuenta_saldo,2) == 0){
                        $cxpAux->cuenta_estado = '2';
                    }else{
                        $cxpAux->cuenta_estado = '1';
                    }
                    $cxpAux->update();
                    /****************************************************************/
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        /********************detalle de diario de venta********************/
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $cxpAux->cuenta_saldo;
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                        $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->proveedor_id = $request->get('proveedorID');
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                        if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        }else{
                            $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                        }
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$cxpAux->cuenta_saldo);
                        /*******************************************************************/
                    }
                }
            }
            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                $transaccion->diario()->associate($diario);
            }
            if($arqueoCaja){
                $transaccion->arqueo_id = $arqueoCaja->arqueo_id;
            }
            $transaccion->save();
            $general->registrarAuditoria('Registro de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero,$transaccion->transaccion_numero,'Registro de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
            /****************************************************************/
            /********************detalle de transaccion de compra********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleTC = new Detalle_TC();
                $detalleTC->detalle_cantidad = $cantidad[$i];
                $detalleTC->detalle_precio_unitario =$pu[$i];
                $detalleTC->detalle_descuento = $descuento[$i];
                $detalleTC->detalle_iva = $iva[$i];
                $detalleTC->detalle_total = $total[$i];
                $detalleTC->detalle_descripcion = $descripcion[$i];
                $detalleTC->detalle_estado = '1';
                $detalleTC->producto_id = $isProducto[$i];
                $detalleTC->bodega_id = $bodega[$i];
                $detalleTC->centro_consumo_id = $cconsumo[$i];
                /******************registro de movimiento de producto******************/
                $movimientoProducto = new Movimiento_Producto();
                $movimientoProducto->movimiento_fecha=$request->get('transaccion_inventario');
               
                $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                $movimientoProducto->movimiento_precio=$pu[$i];
                $movimientoProducto->movimiento_iva=$iva[$i];
                $movimientoProducto->movimiento_total=$total[$i];
                $movimientoProducto->movimiento_stock_actual=0;
                $movimientoProducto->movimiento_costo_promedio=0;
                $movimientoProducto->movimiento_documento=strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $movimientoProducto->movimiento_motivo='COMPRA';
                if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                    $movimientoProducto->movimiento_tipo='ENTRADA';
                }else{
                    $movimientoProducto->movimiento_tipo='SALIDA';
                }
                $movimientoProducto->movimiento_descripcion=strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero.' POR '.$request->get('transaccion_descripcion');
                $movimientoProducto->movimiento_estado='1';
                $movimientoProducto->producto_id=$isProducto[$i];
                $movimientoProducto->bodega_id=$detalleTC->bodega_id;
                $movimientoProducto->centro_consumo_id=$detalleTC->centro_consumo_id;
                $movimientoProducto->empresa_id=Auth::user()->empresa_id;
               
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero,$transaccion->transaccion_numero,'Registro de movimiento de producto por '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                
              
                
                /****************************************************************/
                /********************detalle de diario de compra********************/
                $producto = Producto::findOrFail($isProducto[$i]);
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    $detalleDiario = new Detalle_Diario();
                    if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                        $detalleDiario->detalle_debe = $total[$i];
                        $detalleDiario->detalle_haber = 0.00;
                    }else{
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $total[$i];
                    }
                    $detalleDiario->detalle_comentario = 'P/R '.$descripcion[$i];
                    $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                   
                  
                        $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    
                    if($producto->producto_compra_venta == '3'){
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                    }else{
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_gasto;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta_id.' por un valor de -> '.$total[$i]);
                    /**********************************************************************/
                }  
               
               
                $detalleTC->movimiento()->associate($movimientoProducto);
               
                $transaccion->detalles()->save($detalleTC);
                $general->registrarAuditoria('Registro de detalle de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$request->get('factura_serie').$request->get('factura_numero'),$request->get('factura_serie').$request->get('factura_numero'),'Registro de detalle de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' numero -> '.$request->get('factura_serie').$request->get('factura_numero').' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
            }
            /****************************************************************/
            $sustentoTributario = Sustento_Tributario::findOrFail($request->get('sustento_id'));
            /********************detalle de diario de compra********************/
            if ($request->get('IvaBienesID') > 0){
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    $detalleDiario = new Detalle_Diario();
                    if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                        $detalleDiario->detalle_debe = $request->get('IvaBienesID');
                        $detalleDiario->detalle_haber = 0.00;
                    }else{
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $request->get('IvaBienesID');
                    }
                    $detalleDiario->detalle_comentario = 'P/R IVA PAGADO POR BIENES EN COMPRA';
                    $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    if($sustentoTributario->sustento_credito == '1'){
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS PRODUCCION')->first();
                    }else{
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS GASTO')->first();
                    }                
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('IvaBienesID'));
                }
            }
            if ($request->get('IvaServiciosID') > 0){
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    $detalleDiario = new Detalle_Diario();
                    if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                        $detalleDiario->detalle_debe = $request->get('IvaServiciosID');
                        $detalleDiario->detalle_haber = 0.00;
                    }else{
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $request->get('IvaServiciosID');
                    }
                    $detalleDiario->detalle_comentario = 'P/R IVA PAGADO POR SERVICIOS EN COMPRAS';
                    $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    if($sustentoTributario->sustento_credito == '1'){
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS PRODUCCION')->first();
                    }else{
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS GASTO')->first();
                    }
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('IvaServiciosID'));
                }
            }
            /****************************************************************/
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                /************************Retencion de compra********************/
                $retencion = new Retencion_Compra();
                $retencion->retencion_fecha = $request->get('retencion_fecha');
                $retencion->retencion_numero = $request->get('retencion_serie').substr(str_repeat(0, 9).$request->get('retencion_secuencial'), - 9);
                $retencion->retencion_serie = $request->get('retencion_serie');
                $retencion->retencion_secuencial = $request->get('retencion_secuencial');
                $retencion->retencion_emision = $request->get('tipoDoc');
                $retencion->retencion_ambiente = 'PRODUCCIÓN';
                $retencion->retencion_autorizacion = $docElectronico->generarClaveAcceso($retencion->retencion_numero,$request->get('retencion_fecha'),"07");
                $retencion->retencion_estado = '1';
                $retencion->rango_id = $request->get('rango_id');
                $retencion->transaccionCompra()->associate($transaccion);
                $retencion->save();
                $general->registrarAuditoria('Registro de retencion de compra numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de retencion de compra numero -> '.$retencion->retencion_numero.' y con codigo de diario -> '.$diario->diario_codigo);
                /******************************************************************/
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    $diario->diario_comentario = $diario->diario_comentario.' RET: '.$retencion->retencion_numero;
                    $diario->update();
                }
                /********************Detalle retencion de compra*******************/
                for ($i = 1; $i < count($baseF); ++$i){
                        $detalleRC = new Detalle_RC();
                        $detalleRC->detalle_tipo = 'FUENTE';
                        $detalleRC->detalle_base = $baseF[$i];
                        $detalleRC->detalle_porcentaje = $porcentajeF[$i];
                        $detalleRC->detalle_valor = $valorF[$i];
                        $detalleRC->detalle_asumida = '0';
                        $detalleRC->detalle_estado = '1';
                        $detalleRC->concepto_id = $idRetF[$i];
                        $retencion->detalles()->save($detalleRC);
                        $general->registrarAuditoria('Registro de detalle de retencion de compra numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de detalle de retencion de compra, con base imponible -> '.$baseF[$i].' porcentaje -> '.$porcentajeF[$i].' valor de retencion -> '.$valorF[$i]);
                        if($valorF[$i] > 0){
                            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                                /********************detalle de diario de compra*******************/
                                $detalleDiario = new Detalle_Diario();
                                $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetF[$i])->first();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = $valorF[$i];
                                $detalleDiario->detalle_comentario = 'P/R RETENCION EN LA FUENTE '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE COMPRA';
                                $detalleDiario->detalle_numero_documento = $retencion->retencion_numero;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_emitida_cuenta;
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorF[$i]);
                                /******************************************************************/
                            }
                        }
                }
                for ($i = 1; $i < count($baseI); ++$i){
                    $detalleRC = new Detalle_RC();
                    $detalleRC->detalle_tipo = 'IVA';
                    $detalleRC->detalle_base = $baseI[$i];
                    $detalleRC->detalle_porcentaje = $porcentajeI[$i];
                    $detalleRC->detalle_valor = $valorI[$i];
                    $detalleRC->detalle_asumida = '0';
                    $detalleRC->detalle_estado = '1';
                    $detalleRC->concepto_id = $idRetI[$i];
                    $retencion->detalles()->save($detalleRC);
                    $general->registrarAuditoria('Registro de detalle de retencion de compra numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de detalle de retencion de compra, con base imponible -> '.$baseI[$i].' porcentaje -> '.$porcentajeI[$i].' valor de retencion -> '.$valorI[$i]);
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        /********************detalle de diario de compra*******************/
                        $detalleDiario = new Detalle_Diario();
                        $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetI[$i])->first();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $valorI[$i];
                        $detalleDiario->detalle_comentario = 'P/R RETENCION DE IVA '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                        $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE COMPRA';
                        $detalleDiario->detalle_numero_documento = $retencion->retencion_numero;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_emitida_cuenta;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorI[$i]);
                        /******************************************************************/
                    }
                }
            }
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    /********************detalle de diario de compra*******************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $valorCXP;
                    $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                    if($request->get('transaccion_tipo_pago') == 'CREDITO' OR $request->get('transaccion_tipo_pago') == 'CONTADO'){
                        $detalleDiario->proveedor_id = $request->get('proveedorID');
                        $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                        if($parametrizacionContable->parametrizacion_cuenta_general == '1'){                       
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        }else{
                            $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                        }
                    }else{
                        $detalleDiario->detalle_comentario = 'P/R COMPRA EN EFECTIVO';
                        $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                        $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$valorCXP);
                }
                if($request->get('transaccion_tipo_pago') == 'EN EFECTIVO'){
                    /**********************movimiento caja****************************/
                    $movimientoCaja = new Movimiento_Caja();          
                    $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                    $movimientoCaja->movimiento_hora=date("H:i:s");
                    $movimientoCaja->movimiento_tipo="SALIDA";
                    $movimientoCaja->movimiento_descripcion= 'P/R FACTURA DE COMPRA :'.$request->get('buscarProveedor');
                    $movimientoCaja->movimiento_valor= $valorCXP;
                    $movimientoCaja->movimiento_documento="FACTURA DE COMPRA";
                    $movimientoCaja->movimiento_numero_documento= $transaccion->transaccion_numero;
                    $movimientoCaja->movimiento_estado = 1;
                    $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                    if(Auth::user()->empresa->empresa_contabilidad== '1'){
                        $movimientoCaja->diario()->associate($diario);
                    }
                    $movimientoCaja->save();
                    /*********************************************************************/
                }
                /******************************************************************/
                /********************retencion electronica************************/
                if($retencion->retencion_emision == 'ELECTRONICA'){
                    $retencionAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlRetencionV2($retencion),'RETENCION');
                    $retencion->retencion_xml_estado = $retencionAux->retencion_xml_estado;
                    $retencion->retencion_xml_mensaje =$retencionAux->retencion_xml_mensaje;
                    $retencion->retencion_xml_respuestaSRI = $retencionAux->retencion_xml_respuestaSRI;
                    if($retencionAux->retencion_xml_estado == 'AUTORIZADO'){
                        $retencion->retencion_xml_nombre = $retencionAux->retencion_xml_nombre;
                        $retencion->retencion_xml_fecha = $retencionAux->retencion_xml_fecha;
                        $retencion->retencion_xml_hora = $retencionAux->retencion_xml_hora;
                    }
                    $retencion->update();
                }
                /******************************************************************/
            }
            $grupo=Grupo_Activo::grupo($request->get('activo_id'))->first();


            $activoFijo = new Activo_Fijo();
            $activoFijo->activo_fecha_inicio = $request->get('transaccion_fecha');
            $activoFijo->activo_fecha_documento = $request->get('transaccion_fecha');
            $activoFijo->activo_descripcion = $request->get('idDescripcion');
            $activoFijo->activo_valor = $request->get('idSubtotal');
            $activoFijo->activo_valor2 = $request->get('idSubtotal');
            $activoFijo->activo_vida_util = $request->get('idVidaUtil');
            $activoFijo->activo_depreciacion = $grupo->grupo_porcentaje;
            $activoFijo->activo_valor_util=round(($request->get('idSubtotal')*$request->get('idVidaUtil'))/100,2);
            $activoFijo->activo_base_depreciar = $request->get('idSubtotal')-$activoFijo->activo_valor_util;
            $activoFijo->activo_depreciacion_anual =round(($activoFijo->activo_base_depreciar*$grupo->grupo_porcentaje)/100,2);
            $activoFijo->activo_depreciacion_mensual=round($activoFijo->activo_depreciacion_anual/12,2);
            $activoFijo->activo_depreciacion_acumulada = 0;
            $activoFijo->activo_estado = 1;
            $activoFijo->grupo_id = $request->get('activo_id');
            $activoFijo->producto_id = $isProducto[1];
            $activoFijo->proveedor_id = $request->get('proveedorID');
            $activoFijo->transaccionCompra()->associate($transaccion);
            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                $activoFijo->diario()->associate($diario);
            }
                                
                                     
            $activoFijo->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Activo Fijo -> '.$request->get('idDescripcion').' Con Transaccion compra '.$transaccion->transaccion_numero,'0','');
            if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                $url = $general->pdfDiario($diario);
            }     
            DB::commit();
            if($tipoComprobante->tipo_comprobante_codigo == '04'){
                return redirect('/transaccionCActivoFijo/new/'.$request->get('punto_id'))->with('success','Transaccion registrada exitosamente')->with('diario',$url);
            }else if($retencion->retencion_xml_estado == 'AUTORIZADO' and $tipoComprobante->tipo_comprobante_codigo <> '04'){
                return redirect('/transaccionCActivoFijo/new/'.$request->get('punto_id'))->with('success','Transaccion registrada y autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $retencion->retencion_fecha)->format('d-m-Y').'/'.$retencion->retencion_xml_nombre.'.pdf')->with('diario',$url);
            }else{
                return redirect('/transaccionCActivoFijo/new/'.$request->get('punto_id'))->with('success','Transaccion registrada exitosamente')->with('error2','ERROR SRI--> '.$retencionAux->retencion_xml_estado.' : '.$retencionAux->retencion_xml_mensaje)->with('diario',$url);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/transaccionCActivoFijo/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{
            DB::beginTransaction();
            $transaccion=Transaccion_Compra::findOrFail($id);
            $numero=$request->get('transaccion_serie').substr(str_repeat(0, 9).$request->get('transaccion_secuencial'), - 9);
            $numeroante=$transaccion->transaccion_serie.substr(str_repeat(0, 9). $transaccion->transaccion_numero , - 9);
            if($numeroante!=$numero){
                $compraAux = Transaccion_Compra::TransaccionDuplicadaActualizar($request->get('transaccion_serie').substr(str_repeat(0, 9).$request->get('transaccion_secuencial'), - 9),$request->get('tipo_comprobante_id'),$request->get('proveedorID'),$id)->first();
                if(isset($compraAux->transaccion_id)){
                    throw new Exception('Ese documento ya se encuentra registrado en el sistema.');
                }
            }
            
            $tipoComprobante = Tipo_Comprobante::tipo($request->get('tipo_comprobante_id'))->first();

            $baseF = $request->get('DbaseRF');
            $idRetF = $request->get('DRFID');
            $porcentajeF = $request->get('DporcentajeRF');
            $valorF = $request->get('DvalorRF');

            $baseI = $request->get('DbaseRI');
            $idRetI = $request->get('DRIID');
            $porcentajeI = $request->get('DporcentajeRI');
            $valorI = $request->get('DvalorRI');
            $docElectronico = new facturacionElectronicaController();

            $transaccion=Transaccion_Compra::findOrFail($id);
            $banderaOrdenRecepcion =  true;
            foreach($transaccion->ordenrecepcion as $ordenRecepcion){
                $banderaOrdenRecepcion = false;
            }
            $general = new generalController();
            $cierre = $general->cierre($transaccion->transaccion_fecha,$transaccion->diario->sucursal_id);          
            if($cierre){
                return redirect('listatransaccionCompra')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cierre = $general->cierre($transaccion->transaccion_inventario,$transaccion->diario->sucursal_id);          
            if($cierre){
                return redirect('listatransaccionCompra')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $tipoComprobante = Tipo_Comprobante::tipo($transaccion->tipo_comprobante_id)->first();
            $general = new generalController();
            if (isset($transaccion->activo)) {
                $activo=Activo_Fijo::findOrFail($transaccion->activo->activo_id);
                $activo->delete();
                $general->registrarAuditoria('Eliminacion de Activo Fijo -> '.$activo->activo_descripcion,'0','');
            }
            if($tipoComprobante->tipo_comprobante_codigo == '04'){
                if(isset($transaccion->diario->anticipoproveedor)){
                    $anticipo=Anticipo_Proveedor::findOrFail($transaccion->diario->anticipoproveedor->anticipo_id);
                    $anticipo->delete();
                    $general->registrarAuditoria('Eliminacion de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                }
                
                if (isset($transaccion->diario->pagocuentapagar->detalles)) {
                    foreach ($transaccion->diario->pagocuentapagar->detalles as $pagos) {
                        $detalle=Detalle_Pago_CXP::findOrFail($pagos->detalle_pago_id);
                        $detalle->delete();
                        $general->registrarAuditoria('Eliminacion del detalle de la cuenta por pagar  -> '.$transaccion->transaccion_numero, $transaccion->transaccion_numero, 'Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                    }
                    $cxcobrar=Pago_CXP::findOrFail($transaccion->diario->pagocuentapagar->pago_id);
                    $cxcobrar->delete();
                    $general->registrarAuditoria('Eliminacion de cuenta por pagar  -> '.$transaccion->transaccion_numero, $transaccion->transaccion_numero, 'Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                
                    $cxpAux=Cuenta_Pagar::findOrFail($transaccion->facturaModificar->cuenta_id);
                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux->transaccionCompra->transaccion_id)->sum('descuento_valor'),2);
                    if(round($cxpAux->cuenta_saldo,2) == 0){
                        $cxpAux->cuenta_estado = '2';
                    }else{
                        $cxpAux->cuenta_estado = '1';
                    }
                    $cxpAux->update();
                }
                
                
            }
            $tipoComprobante = Tipo_Comprobante::tipo($request->get('tipo_comprobante_id'))->first();
            if ($tipoComprobante->tipo_comprobante_codigo <> '04') {
                if ($transaccion->transaccion_tipo_pago=='EN EFECTIVO') {
                    if (isset($transaccion->diario->pagocuentapagar)) {
                        foreach ($transaccion->diario->pagocuentapagar->detalles as $pagos) {
                            $detalle=Detalle_Pago_CXP::findOrFail($pagos->detalle_pago_id);
                            $detalle->delete();
                            $general->registrarAuditoria('Eliminacion del detalle de pago de cuenta por pagar por Transaccion compra:-> '.$transaccion->transaccion_numero, $transaccion->transaccion_numero, 'Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                        }
                    
                        $cxcobrar=Pago_CXP::findOrFail($transaccion->diario->pagocuentapagar->pago_id);
                        $cxcobrar->delete();
                        $general->registrarAuditoria('Eliminacion de pago de cuenta por pagar por Transaccion compra:-> '.$transaccion->transaccion_numero, $transaccion->transaccion_numero, 'Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                    }
                }
              
                if (isset($transaccion->cuentaPagar)) {
                    $ntransaccion=Transaccion_Compra::findOrFail($transaccion->transaccion_id);
                    $ntransaccion->cuenta_id=null;
                    $ntransaccion->save();

                    $cxpAux=Cuenta_Pagar::findOrFail($transaccion->cuenta_id);
                    $cxpAux->delete();
                    $general->registrarAuditoria('Eliminacion de cuenta por pagar por Transaccion compra:-> '.$transaccion->transaccion_numero, $transaccion->transaccion_numero, 'Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                }
                
            }
            foreach ($transaccion->diario->detalles as $detalles) {
                $aux=$detalles;
                $detalles->delete();
           
                $general->registrarAuditoria('Eliminacion de detalle de diario por Transaccion compra numero: -> '.$transaccion->transaccion_numero, $id, 'Con id de diario-> '.$aux->diario_id.'Comentario -> '.$aux->detalle_comentario);
            }
            foreach ($transaccion->detalles as $detalles) {
                $detall=Detalle_TC::findOrFail($detalles->detalle_id);
                if (isset($detalles->movimiento)) {
                    $detall->movimiento_id=null;
                    $detall->save();

                    $aux = $detalles->movimiento;
                    $detalles->movimiento->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion de Movimiento de producto por Transaccion compra: -> '.$transaccion->transaccion_numero, $id, 'Con Producto '.$detalles->producto->producto_nombre.' Con la cantidad de producto de '.$aux->movimiemovimiento_totalnto_cantidad.' y total '.$aux->movimiento_total);
                }
            
                $aux = $detalles;
                $detalles->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion de detalles de producto por Transaccion compra: -> '.$transaccion->transaccion_numero, $id, 'Con Producto '.$aux->producto->producto_nombre.' Con la cantidad de producto de '.$aux->detalle_cantidad.' y total '.$aux->detalle_total);
            }
            $ntransaccion=Transaccion_Compra::findOrFail($transaccion->transaccion_id);
            
            if ($tipoComprobante->tipo_comprobante_codigo <> '04') {
                $retencion=Retencion_Compra::findOrFail($ntransaccion->retencionCompra->retencion_id);
                if ($request->get('editret')=="on") {
                    if ($retencion->retencion_numero!=$request->get('retencion_serie').substr(str_repeat(0, 9).$request->get('retencion_secuencial'), - 9)) {
                        $docnull=new Documento_Anulado();
                        $docnull->documento_anulado_fecha= $request->get('retencion_fecha');
                        $docnull->documento_anulado_motivo= 'Por la creacion de una nueva retencion de la transaccion compra N°'. $ntransaccion->transaccion_numero;
                        $docnull->documento_anulado_estado= '1';
                        $docnull->empresa_id=$ntransaccion->sucursal->empresa_id;
                        $docnull->save();

                    
                        $retencion->transaccion_id=null;
                        $retencion->retencion_estado='0';
                        $retencion->documento_anulado_id;
                        $retencion->dopcumentoanulado()->associate($docnull);
                        $retencion->save();
                    }
                    else{
                        foreach($retencion->detalles as $detalle){
                            $detalle->delete();
                            $general->registrarAuditoria('Eliminacion del detalle de la retencion con la trasaccion de compra numero'.$transaccion->transaccion_numero, $id, '');
                        }
                        $retencion->delete();
                        $general->registrarAuditoria('Eliminacion de la retnecion Numero'.$retencion->retencion_numero.'de la trasaccion de compra numero'.$transaccion->transaccion_numero, $id, '');
                    }

                }
            }
            $aux=$ntransaccion->diario_id;
            $ntransaccion->diario_id = null;
            if ($request->get('editret')=="on") {
                $ntransaccion->transaccion_id_f = null;
            }
            $ntransaccion->save();
            $tdiario=Diario::findOrFail($aux);
            foreach($tdiario->detalles as $detalles){
                $detalles->delete();
                $general->registrarAuditoria('Eliminacion del detalle del diario de la trasaccion de compra numero'.$transaccion->transaccion_numero, $id, '');
            }
            $tdiario->delete();
            $general->registrarAuditoria('Eliminacion del diario de la trasaccion de compra numero'.$transaccion->transaccion_numero, $id, '');
            

            $valorCXP= ($request->get('idTotal'))-($request->get('id_total_fuente'))-($request->get('id_total_iva'));
            /********************detalle de la compra ********************/
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            $cuentaContable = $request->get('Dcuenta');
            $bodega = $request->get('Dbodega');
            $cconsumo = $request->get('Dcconsumo');
            $descripcion = $request->get('Ddescripcion');

            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $facManual='';
            if($request->get('manualID')=='on'){
                $facManual = substr($request->get('serieFacManual'),0,3).'-'.substr($request->get('serieFacManual'),3,3).'-'.substr(str_repeat(0, 9).$request->get('scuencialFacManual'), - 9);
            }
            /****************************************************************/
             /*VERIFICAR TOTAL DE IVA DE DETALLE CON EL TOTAL DE IVA DE LA FACTURA*/
             $totalDetalleIva = 0;
             for ($i = 1; $i < count($cantidad); ++$i){
                 $totalDetalleIva = floatval($totalDetalleIva) + floatval($iva[$i]);
             }
             $squareVat = round($totalDetalleIva,2) == round(floatval($request->get('idIva')),2) ? false : true;
             /*******/
            /********************cabecera de transaccion de compra ********************/
            $transaccion=Transaccion_Compra::findOrFail($id);
            $transaccion->transaccion_fecha = $request->get('transaccion_fecha');
            $transaccion->transaccion_caducidad = $request->get('transaccion_caducidad');
            $transaccion->transaccion_impresion = $request->get('transaccion_impresion');
            $transaccion->transaccion_vencimiento = $request->get('transaccion_vencimiento');
            $transaccion->transaccion_inventario = $request->get('transaccion_inventario');
            $transaccion->transaccion_numero = $request->get('transaccion_serie').substr(str_repeat(0, 9).$request->get('transaccion_secuencial'), - 9);
            $transaccion->transaccion_serie = $request->get('transaccion_serie');
            $transaccion->transaccion_subtotal = $request->get('idSubtotal');
            $transaccion->transaccion_descuento = $request->get('idDescuento');
            $transaccion->transaccion_tarifa0 = $request->get('idTarifa0');
            $transaccion->transaccion_tarifa12 = $request->get('idTarifa12');
            $transaccion->transaccion_iva = $request->get('idIva');
            $transaccion->transaccion_total = $request->get('idTotal');
            $transaccion->transaccion_ivaB = $request->get('IvaBienesID');
            $transaccion->transaccion_ivaS = $request->get('IvaServiciosID');
            $transaccion->transaccion_dias_plazo = $request->get('transaccion_dias_plazo');
            $transaccion->transaccion_descripcion = $request->get('transaccion_descripcion');
            $transaccion->transaccion_tipo_pago = $request->get('transaccion_tipo_pago');
            $transaccion->transaccion_porcentaje_iva = $request->get('transaccion_porcentaje_iva');
            $transaccion->transaccion_autorizacion = $request->get('transaccion_autorizacion');
            $transaccion->transaccion_estado = '1';
            $transaccion->proveedor_id = $request->get('proveedorID');
            $transaccion->transaccion_factura_manual = null;
            $transaccion->transaccion_autorizacion_manual = null;
            $transaccion->tipo_comprobante_id = $request->get('tipo_comprobante_id');
            $transaccion->sustento_id = $request->get('sustento_id');
            if ($tipoComprobante->tipo_comprobante_codigo <> '04') {
                $cxp = new Cuenta_Pagar();
                $cxp->cuenta_descripcion = strtoupper($tipoComprobante->tipo_comprobante_nombre).' DE COMPRA A PROVEEDOR '.$request->get('buscarProveedor').' CON DOCUMENTO No. '.$transaccion->transaccion_numero;
                if($request->get('transaccion_tipo_pago') == 'CREDITO' or $request->get('transaccion_tipo_pago') == 'CONTADO'){
                    $cxp->cuenta_tipo =$request->get('transaccion_tipo_pago');
                    $cxp->cuenta_saldo = $valorCXP;
                    $cxp->cuenta_estado = '1';
                }else{
                    $cxp->cuenta_tipo = $request->get('transaccion_tipo_pago');
                    $cxp->cuenta_saldo = 0.00;
                    $cxp->cuenta_estado = '2';
                }
                $cxp->cuenta_fecha = $request->get('transaccion_fecha');
                $cxp->cuenta_fecha_inicio = $request->get('transaccion_fecha');
                $cxp->cuenta_fecha_fin = date("Y-m-d",strtotime($request->get('transaccion_fecha')."+ ".$request->get('transaccion_dias_plazo')." days"));
                $cxp->cuenta_monto = $valorCXP;
                $cxp->cuenta_valor_factura = $request->get('idTotal');
                $cxp->proveedor_id = $transaccion->proveedor_id;
                $cxp->sucursal_id = $transaccion->sucursal_id;
                $cxp->save();
                $general->registrarAuditoria('Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero,$transaccion->transaccion_numero,'Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                /****************************************************************/
                $transaccion->cuentaPagar()->associate($cxp);
            }
                $diario = new Diario();
                $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero;
                if($tipoComprobante->tipo_comprobante_codigo == '01'){
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'),'CFCR');
                    $diario->diario_tipo = 'CFCR';
                }else if($tipoComprobante->tipo_comprobante_codigo == '04'){
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'),'CNCR');
                    $diario->diario_tipo = 'CNCR';
                    if($request->get('manualID')=='on'){
                        $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero.' CON FACTURA : '.$facManual;
                    }else{
                        $facturaAux = Transaccion_Compra::Transaccionpunto($request->get('factura_id'))->first(); 
                        $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero.' CON FACTURA : '.$facturaAux->transaccion_numero;
                    } 
                }else if($tipoComprobante->tipo_comprobante_codigo == '05'){
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'),'CNDR');
                    $diario->diario_tipo = 'CNDR';
                    if($request->get('manualID')=='on'){
                        $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero.' CON FACTURA : '.$facManual;
                    }else{
                        $facturaAux = Transaccion_Compra::Transaccionpunto($request->get('factura_id'))->first(); 
                        $diario->diario_comentario = 'COMPROBANTE DIARIO DE COMPRA DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero.' CON FACTURA : '.$facturaAux->transaccion_numero;
                    }
                }else{
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'),'CTCR');
                    $diario->diario_tipo = 'CTCR';
                }
                $diario->diario_fecha = $request->get('transaccion_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' DE COMPRA';
                $diario->diario_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $diario->diario_numero_documento = $transaccion->transaccion_numero;
                $diario->diario_beneficiario = $request->get('buscarProveedor');
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('transaccion_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('transaccion_fecha'))->format('Y');
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = $transaccion->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de compra con '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' -> '.$transaccion->transaccion_numero,$transaccion->transaccion_numero,'Registro de diario de compra de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                /****************************************************************/
            if ($tipoComprobante->tipo_comprobante_codigo <> '04') {
                if ($cxp->cuenta_estado == '2') {
                    /********************Pago por compra en efectivo***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = 'PAGO EN EFECTIVO';
                    $pago->pago_fecha = $cxp->cuenta_fecha;
                    $pago->pago_tipo = 'PAGO EN EFECTIVO';
                    $pago->pago_valor = $cxp->cuenta_monto;
                    $pago->pago_estado = '1';
                    $pago->diario()->associate($diario);
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = 'PAGO EN EFECTIVO';
                    $detallePago->detalle_pago_valor = $cxp->cuenta_monto;
                    $detallePago->detalle_pago_cuota = 1;
                    $detallePago->detalle_pago_estado = '1';
                    $detallePago->cuenta_pagar_id = $cxp->cuenta_id;
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();
                    /****************************************************************/
                }
                if ($tipoComprobante->tipo_comprobante_codigo == '05') {
                    if($request->get('manualID')=='on'){
                        $transaccion->transaccion_factura_manual = $facManual;
                        $transaccion->transaccion_autorizacion_manual = $request->get('autorizacionFacManual');
                    }else{
                        $facturaAux = Transaccion_Compra::Transaccionpunto($request->get('factura_id'))->first(); 
                        $transaccion->transaccion_id_f = $facturaAux->transaccion_id;
                    }
                }
            }
            if($tipoComprobante->tipo_comprobante_codigo == '04'){
                if($request->get('manualID')=='on'){
                    $transaccion->transaccion_factura_manual = $facManual;
                    $transaccion->transaccion_autorizacion_manual = $request->get('autorizacionFacManual');
                }else{
                    $facturaAux = Transaccion_Compra::Transaccionpunto($request->get('factura_id'))->first(); 
                    $cxpAux = $facturaAux->cuentaPagar;
                    $transaccion->transaccion_id_f = $facturaAux->transaccion_id;
                }
                if($request->get('manualID')=='on'){
                    $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($request->get('punto_id'), 'Anticipo de Proveedor')->first();
                    if($rangoDocumentoAnticipo){
                        $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                        $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal($transaccion->sucursal_id)->get();
                        foreach($puntosEmision as $punto){
                            $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Proveedor')->first();
                            if($rangoDocumentoAnticipo){
                                break;
                            }
                        }
                        if($rangoDocumentoAnticipo){
                            $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                            $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                            if($secuencialAux){$secuencial=$secuencialAux+1;}
                        }else{
                            throw new Exception('No tiene punto de emision de anticipo de proveedor.');
                        }
                    }
                    $anticipoProveedor = new Anticipo_Proveedor();
                    $anticipoProveedor->anticipo_numero = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                    $anticipoProveedor->anticipo_serie = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie;
                    $anticipoProveedor->anticipo_secuencial = $secuencial;
                    $anticipoProveedor->anticipo_fecha = $transaccion->transaccion_fecha;
                    $anticipoProveedor->anticipo_tipo = 'NOTA DE CRÉDITO';      
                    $anticipoProveedor->anticipo_documento = $transaccion->transaccion_numero;     
                    $anticipoProveedor->anticipo_motivo = $transaccion->transaccion_descripcion;
                    $anticipoProveedor->anticipo_valor = $request->get('idTotal');  
                    $anticipoProveedor->anticipo_saldo = $request->get('idTotal');   
                    $anticipoProveedor->proveedor_id = $transaccion->proveedor_id;
                    $anticipoProveedor->anticipo_estado = 1; 
                    $anticipoProveedor->rango_id = $rangoDocumentoAnticipo->rango_id;
                    $anticipoProveedor->diario()->associate($diario);
                    $anticipoProveedor->save();
                    $general->registrarAuditoria('Registro de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal');
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                }elseif(round($cxpAux->cuenta_saldo,2) == 0){
                    $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($facturaAux->punto_id, 'Anticipo de Proveedor')->first();
                    if($rangoDocumentoAnticipo){
                        $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                        $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($facturaAux->punto_id)->sucursal_id)->get();
                        foreach($puntosEmision as $punto){
                            $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Proveedor')->first();
                            if($rangoDocumentoAnticipo){
                                break;
                            }
                        }
                        if($rangoDocumentoAnticipo){
                            $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                            $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                            if($secuencialAux){$secuencial=$secuencialAux+1;}
                        }else{
                            throw new Exception('No tiene punto de emision de anticipo de proveedor.');
                        }
                    }

                    $anticipoProveedor = new Anticipo_Proveedor();
                    $anticipoProveedor->anticipo_numero = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                    $anticipoProveedor->anticipo_serie = $rangoDocumentoAnticipo->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoAnticipo->puntoEmision->punto_serie;
                    $anticipoProveedor->anticipo_secuencial = $secuencial;

                    $anticipoProveedor->anticipo_fecha = $transaccion->transaccion_fecha;
                    $anticipoProveedor->anticipo_tipo = 'NOTA DE CRÉDITO';      
                    $anticipoProveedor->anticipo_documento = $transaccion->transaccion_numero;     
                    $anticipoProveedor->anticipo_motivo = $transaccion->transaccion_descripcion;
                    $anticipoProveedor->anticipo_valor = $request->get('idTotal');  
                    $anticipoProveedor->anticipo_saldo = $request->get('idTotal');   
                    $anticipoProveedor->proveedor_id = $facturaAux->proveedor_id;
                    $anticipoProveedor->anticipo_estado = 1; 
                    $anticipoProveedor->rango_id = $rangoDocumentoAnticipo->rango_id;
                    $anticipoProveedor->diario()->associate($diario);
                    $anticipoProveedor->save();
                    $general->registrarAuditoria('Registro de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal');
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                }else if(round($cxpAux->cuenta_saldo,2) >= round($transaccion->transaccion_total,2)){
                    /********************Pago por Nota de Credito***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero;
                    $pago->pago_fecha = $request->get('transaccion_fecha');
                    $pago->pago_tipo = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $pago->pago_valor = $request->get('idTotal');
                    $pago->pago_estado = '1';
                    $pago->diario()->associate($diario);
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero; 
                    $detallePago->detalle_pago_valor = $request->get('idTotal'); 
                    $detallePago->detalle_pago_cuota = Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->count()+1; 
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_pagar_id = $cxpAux->cuenta_id; 
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();

                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux->transaccionCompra->transaccion_id)->sum('descuento_valor'),2);
                    if(round($cxpAux->cuenta_saldo,2) == 0){
                        $cxpAux->cuenta_estado = '2';
                    }else{
                        $cxpAux->cuenta_estado = '1';
                    }
                    $cxpAux->update();
                    /****************************************************************/
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal');
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                }else{
                    /********************Anticipo a proveedor**************************/
                    $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($facturaAux->punto_id, 'Anticipo de Proveedor')->first();
                    if($rangoDocumentoAnticipo){
                        $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                        $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($facturaAux->punto_id)->sucursal_id)->get();
                        foreach($puntosEmision as $punto){
                            $rangoDocumentoAnticipo=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Proveedor')->first();
                            if($rangoDocumentoAnticipo){
                                break;
                            }
                        }
                        if($rangoDocumentoAnticipo){
                            $secuencial=$rangoDocumentoAnticipo->rango_inicio;
                            $secuencialAux=Anticipo_Proveedor::secuencial($rangoDocumentoAnticipo->rango_id)->max('anticipo_secuencial');
                            if($secuencialAux){$secuencial=$secuencialAux+1;}
                        }else{
                            throw new Exception('No tiene punto de emision de anticipo de proveedor.');
                        }
                    }
                    $anticipoProveedor = new Anticipo_Proveedor();
                    $anticipoProveedor->anticipo_fecha = $transaccion->transaccion_fecha;
                    $anticipoProveedor->anticipo_tipo = 'NOTA DE CRÉDITO';      
                    $anticipoProveedor->anticipo_documento = $transaccion->transaccion_numero;     
                    $anticipoProveedor->anticipo_motivo = $transaccion->transaccion_descripcion;
                    $anticipoProveedor->anticipo_valor = $request->get('idTotal') - $cxpAux->cuenta_saldo; 
                    $anticipoProveedor->anticipo_saldo = $request->get('idTotal') - $cxpAux->cuenta_saldo;
                    $anticipoProveedor->proveedor_id = $facturaAux->proveedor_id;
                    $anticipoProveedor->anticipo_estado = 1; 
                    $anticipoProveedor->rango_id = $rangoDocumentoAnticipo->rango_id;
                    $anticipoProveedor->diario()->associate($diario);
                    $anticipoProveedor->save();
                    $general->registrarAuditoria('Registro de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal') - $cxpAux->cuenta_saldo;
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal') - $cxpAux->cuenta_saldo);
                    /*******************************************************************/
                    /********************Pago por Nota de Credito***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero;
                    $pago->pago_fecha = $request->get('transaccion_fecha');
                    $pago->pago_tipo = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $pago->pago_valor = $cxpAux->cuenta_saldo;
                    $pago->pago_estado = '1';
                    $pago->diario()->associate($diario);
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero; 
                    $detallePago->detalle_pago_valor = $cxpAux->cuenta_saldo; 
                    $detallePago->detalle_pago_cuota = Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->count()+1; 
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_pagar_id = $cxpAux->cuenta_id; 
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();

                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux->transaccionCompra->transaccion_id)->sum('descuento_valor'),2);
                    if(round($cxpAux->cuenta_saldo,2) == 0){
                        $cxpAux->cuenta_estado = '2';
                    }else{
                        $cxpAux->cuenta_estado = '1';
                    }
                    $cxpAux->update();
                    /****************************************************************/
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $cxpAux->cuenta_saldo;
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$cxpAux->cuenta_saldo);
                    /*******************************************************************/
                }
            }
            $transaccion->diario()->associate($diario);
            $transaccion->save();
            $general->registrarAuditoria('Registro de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero.' con sustento tributario '.Sustento_Tributario::sustento($request->get('sustento_id'))->first()->sustento_codigo,$transaccion->transaccion_numero,'Registro de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
            /****************************************************************/
            /********************detalle de transaccion de compra********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleTC = new Detalle_TC();
                $detalleTC->detalle_cantidad = $cantidad[$i];
                $detalleTC->detalle_precio_unitario =$pu[$i];
                $detalleTC->detalle_descuento = $descuento[$i];
                $detalleTC->detalle_iva = $iva[$i];
                $detalleTC->detalle_total = $total[$i];
                $detalleTC->detalle_descripcion = $descripcion[$i];
                $detalleTC->detalle_estado = '1';
                $detalleTC->producto_id = $isProducto[$i];
                $detalleTC->bodega_id = $bodega[$i];
                $detalleTC->centro_consumo_id = $cconsumo[$i];
                if($banderaOrdenRecepcion){
                    /******************registro de movimiento de producto******************/
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('transaccion_inventario');
                    $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    $movimientoProducto->movimiento_precio=$pu[$i];
                    $movimientoProducto->movimiento_iva=$iva[$i];
                    $movimientoProducto->movimiento_total=$total[$i];
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento=strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $movimientoProducto->movimiento_motivo='COMPRA';
                    if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                        $movimientoProducto->movimiento_tipo='ENTRADA';
                    }else{
                        $movimientoProducto->movimiento_tipo='SALIDA';
                    }
                    $movimientoProducto->movimiento_descripcion=strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero.' POR '.$request->get('transaccion_descripcion');
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$detalleTC->bodega_id;
                    $movimientoProducto->centro_consumo_id=$detalleTC->centro_consumo_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero,$transaccion->transaccion_numero,'Registro de movimiento de producto por '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                        
                    
                   /****************************************************************/
                }
                
                /********************detalle de diario de compra********************/
                $producto = Producto::findOrFail($isProducto[$i]);
                $detalleDiario = new Detalle_Diario();
                if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                    $detalleDiario->detalle_debe = $total[$i];
                    $detalleDiario->detalle_haber = 0.00;
                }else{
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $total[$i];
                }
                $detalleDiario->detalle_comentario = 'P/R '.$descripcion[$i];
                $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($banderaOrdenRecepcion){
                   
                        $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                   
                    if($producto->producto_compra_venta == '3'){
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                    }else{
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_gasto;
                    }
                }else{
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'MERCADERIA POR RECEPTAR')->first();
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }
               
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta_id.' por un valor de -> '.$total[$i]);
                /**********************************************************************/
                if($banderaOrdenRecepcion){
                   
                  
                            $detalleTC->movimiento()->associate($movimientoProducto);
                        
                }
                $transaccion->detalles()->save($detalleTC);
                $general->registrarAuditoria('Registro de detalle de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$request->get('factura_serie').$request->get('factura_numero'),$request->get('factura_serie').$request->get('factura_numero'),'Registro de detalle de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' numero -> '.$request->get('factura_serie').$request->get('factura_numero').' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
            }
            /****************************************************************/
            $sustentoTributario = Sustento_Tributario::findOrFail($request->get('sustento_id'));
            /********************detalle de diario de compra********************/
            if ($request->get('IvaBienesID') > 0){
                $detalleDiario = new Detalle_Diario();
                if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                    if($squareVat){
                        $detalleDiario->detalle_debe = floatval($request->get('IvaBienesID')) + (floatval($request->get('idIva')) - floatval($totalDetalleIva));
                        $squareVat = false;
                    }else{
                        $detalleDiario->detalle_debe = $request->get('IvaBienesID');
                    }                    
                    $detalleDiario->detalle_haber = 0.00;
                }else{
                    $detalleDiario->detalle_debe = 0.00;
                    if($squareVat){
                        $detalleDiario->detalle_haber = floatval($request->get('IvaBienesID')) + (floatval($request->get('idIva')) - floatval($totalDetalleIva));
                        $squareVat = false;
                    }else{
                        $detalleDiario->detalle_haber = $request->get('IvaBienesID');
                    }
                }
                $detalleDiario->detalle_comentario = 'P/R IVA PAGADO POR BIENES EN COMPRA';
                $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($sustentoTributario->sustento_credito == '1'){
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS PRODUCCION')->first();
                }else{
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS GASTO')->first();
                }
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('IvaBienesID'));
            }
            if ($request->get('IvaServiciosID') > 0){
                $detalleDiario = new Detalle_Diario();
                if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                    if($squareVat){
                        $detalleDiario->detalle_debe = floatval($request->get('IvaServiciosID')) + (floatval($request->get('idIva')) - floatval($totalDetalleIva));
                        $squareVat = false;
                    }else{
                        $detalleDiario->detalle_debe = $request->get('IvaServiciosID');
                    }
                    $detalleDiario->detalle_haber = 0.00;
                }else{
                    $detalleDiario->detalle_debe = 0.00;
                    if($squareVat){
                        $detalleDiario->detalle_haber = floatval($request->get('IvaServiciosID')) + (floatval($request->get('idIva')) - floatval($totalDetalleIva));
                        $squareVat = false;
                    }else{
                        $detalleDiario->detalle_haber = $request->get('IvaServiciosID');
                    }
                }
                $detalleDiario->detalle_comentario = 'P/R IVA PAGADO POR SERVICIOS EN COMPRAS';
                $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($sustentoTributario->sustento_credito == '1'){
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS PRODUCCION')->first();
                }else{
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS GASTO')->first();
                }
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('IvaServiciosID'));
            }
            
            if ($tipoComprobante->tipo_comprobante_codigo <> '04') {
                if ($request->get('editret')=="on") {
                    /************************Retencion de compra********************/
                    $retencion = new Retencion_Compra();
                    $retencion->retencion_fecha = $request->get('retencion_fecha');
                    $retencion->retencion_numero = $request->get('retencion_serie').substr(str_repeat(0, 9).$request->get('retencion_secuencial'), - 9);
                    $retencion->retencion_serie = $request->get('retencion_serie');
                    $retencion->retencion_secuencial = $request->get('retencion_secuencial');
                    $retencion->retencion_emision = $request->get('tipoDoc');
                    $retencion->retencion_ambiente = 'PRODUCCIÓN';
                    $retencion->retencion_autorizacion = $docElectronico->generarClaveAcceso($retencion->retencion_numero, $request->get('retencion_fecha'), "07");
                    $retencion->retencion_estado = '1';
                    $retencion->rango_id = $request->get('rango_id');
                    $retencion->transaccionCompra()->associate($transaccion);
                    $retencion->save();
                    $general->registrarAuditoria('Registro de retencion de compra numero -> '.$retencion->retencion_numero, $retencion->retencion_numero, 'Registro de retencion de compra numero -> '.$retencion->retencion_numero.' y con codigo de diario -> '.$diario->diario_codigo);
                    /******************************************************************/
                }
                $diario->diario_comentario = $diario->diario_comentario.' RET: '.$retencion->retencion_numero;
                $diario->update();
                    /********************Detalle retencion de compra*******************/
                for ($i = 1; $i < count($baseF); ++$i) {
                    if ($request->get('editret')=="on") {
                        $detalleRC = new Detalle_RC();
                        $detalleRC->detalle_tipo = 'FUENTE';
                        $detalleRC->detalle_base = $baseF[$i];
                        $detalleRC->detalle_porcentaje = $porcentajeF[$i];
                        $detalleRC->detalle_valor = $valorF[$i];
                        $detalleRC->detalle_asumida = '0';
                        $detalleRC->detalle_estado = '1';
                        $detalleRC->concepto_id = $idRetF[$i];
                        $retencion->detalles()->save($detalleRC);
                        $general->registrarAuditoria('Registro de detalle de retencion de compra numero -> '.$retencion->retencion_numero, $retencion->retencion_numero, 'Registro de detalle de retencion de compra, con base imponible -> '.$baseF[$i].' porcentaje -> '.$porcentajeF[$i].' valor de retencion -> '.$valorF[$i]);
                    }
                    if ($valorF[$i] > 0) {
                        /********************detalle de diario de compra*******************/
                        $detalleDiario = new Detalle_Diario();
                        $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetF[$i])->first();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $valorF[$i];
                        $detalleDiario->detalle_comentario = 'P/R RETENCION EN LA FUENTE '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                        $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE COMPRA';
                        $detalleDiario->detalle_numero_documento = $retencion->retencion_numero;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_emitida_cuenta;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo, $transaccion->transaccion_numero, 'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorF[$i]);
                        /******************************************************************/
                    }
                }
                for ($i = 1; $i < count($baseI); ++$i) {
                    if ($request->get('editret')=="on") {
                        $detalleRC = new Detalle_RC();
                        $detalleRC->detalle_tipo = 'IVA';
                        $detalleRC->detalle_base = $baseI[$i];
                        $detalleRC->detalle_porcentaje = $porcentajeI[$i];
                        $detalleRC->detalle_valor = $valorI[$i];
                        $detalleRC->detalle_asumida = '0';
                        $detalleRC->detalle_estado = '1';
                        $detalleRC->concepto_id = $idRetI[$i];
                        $retencion->detalles()->save($detalleRC);
                        $general->registrarAuditoria('Registro de detalle de retencion de compra numero -> '.$retencion->retencion_numero, $retencion->retencion_numero, 'Registro de detalle de retencion de compra, con base imponible -> '.$baseI[$i].' porcentaje -> '.$porcentajeI[$i].' valor de retencion -> '.$valorI[$i]);
                    }
                    /********************detalle de diario de compra*******************/
                    $detalleDiario = new Detalle_Diario();
                    $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetI[$i])->first();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $valorI[$i];
                    $detalleDiario->detalle_comentario = 'P/R RETENCION DE IVA '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                    $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE COMPRA';
                    $detalleDiario->detalle_numero_documento = $retencion->retencion_numero;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_emitida_cuenta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo, $transaccion->transaccion_numero, 'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorI[$i]);
                    /******************************************************************/
                }
            }
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                /********************detalle de diario de compra*******************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = $valorCXP;
                $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                if($request->get('transaccion_tipo_pago') == 'CREDITO' OR $request->get('transaccion_tipo_pago') == 'CONTADO'){
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){                       
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                    }
                }else{
                    $detalleDiario->detalle_comentario = 'P/R COMPRA EN EFECTIVO';
                    $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                    $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$valorCXP);
                if($request->get('transaccion_tipo_pago') == 'EN EFECTIVO'){
                    /**********************movimiento caja****************************/
                    $movimientoCaja = new Movimiento_Caja();          
                    $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                    $movimientoCaja->movimiento_hora=date("H:i:s");
                    $movimientoCaja->movimiento_tipo="SALIDA";
                    $movimientoCaja->movimiento_descripcion= 'P/R FACTURA DE COMPRA :'.$request->get('buscarProveedor');
                    $movimientoCaja->movimiento_valor= $valorCXP;
                    $movimientoCaja->movimiento_documento="FACTURA DE COMPRA";
                    $movimientoCaja->movimiento_numero_documento= $transaccion->transaccion_numero;
                    $movimientoCaja->movimiento_estado = 1;
                    $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                    if(Auth::user()->empresa->empresa_contabilidad== '1'){
                        $movimientoCaja->diario()->associate($diario);
                    }
                    $movimientoCaja->save();
                    /*********************************************************************/
                }
                if ($request->get('editret')=="on") {
                    /******************************************************************/
                    /********************retencion electronica************************/
                    if ($retencion->retencion_emision == 'ELECTRONICA') {
                        $retencionAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlRetencionV2($retencion), 'RETENCION');
                        $retencion->retencion_xml_estado = $retencionAux->retencion_xml_estado;
                        $retencion->retencion_xml_mensaje =$retencionAux->retencion_xml_mensaje;
                        $retencion->retencion_xml_respuestaSRI = $retencionAux->retencion_xml_respuestaSRI;
                        if ($retencionAux->retencion_xml_estado == 'AUTORIZADO') {
                            $retencion->retencion_xml_nombre = $retencionAux->retencion_xml_nombre;
                            $retencion->retencion_xml_fecha = $retencionAux->retencion_xml_fecha;
                            $retencion->retencion_xml_hora = $retencionAux->retencion_xml_hora;
                        }
                        $retencion->update();
                    }
                }
                /******************************************************************/
            }
            $grupo=Grupo_Activo::grupo($request->get('activo_id'))->first();


            $activoFijo = new Activo_Fijo();
            $activoFijo->activo_fecha_inicio = $request->get('transaccion_fecha');
            $activoFijo->activo_fecha_documento = $request->get('transaccion_fecha');
            $activoFijo->activo_descripcion = $request->get('idDescripcion');
            $activoFijo->activo_valor = $request->get('idSubtotal');
            $activoFijo->activo_valor2 = $request->get('idSubtotal');
            $activoFijo->activo_vida_util = $request->get('idVidaUtil');
            $activoFijo->activo_depreciacion = $grupo->grupo_porcentaje;
            $activoFijo->activo_valor_util=round(($request->get('idSubtotal')*$request->get('idVidaUtil'))/100,2);
            $activoFijo->activo_base_depreciar = $request->get('idSubtotal')-$activoFijo->activo_valor_util;
            $activoFijo->activo_depreciacion_anual =round(($activoFijo->activo_base_depreciar*$grupo->grupo_porcentaje)/100,2);
            $activoFijo->activo_depreciacion_mensual=round($activoFijo->activo_depreciacion_anual/12,2);
            $activoFijo->activo_depreciacion_acumulada = 0;
            $activoFijo->activo_estado = 1;
            $activoFijo->grupo_id = $request->get('activo_id');
            $activoFijo->producto_id = $isProducto[1];
            $activoFijo->proveedor_id = $request->get('proveedorID');
            $activoFijo->transaccionCompra()->associate($transaccion);
                
            $activoFijo->diario()->associate($diario);   
         //   $url = " ";
            $url = $general->pdfDiario($diario);
            $activoFijo->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Activo Fijo -> '.$request->get('idDescripcion').' Con Transaccion compra '.$transaccion->transaccion_numero,'0','');
         
            DB::commit();
            if ($request->get('editret')=="on") {
                if($tipoComprobante->tipo_comprobante_codigo == '04'){
                    return redirect('listatransaccionCompra')->with('success','Transaccion registrada exitosamente')->with('diario',$url);
                }else if($retencion->retencion_xml_estado == 'AUTORIZADO' and $tipoComprobante->tipo_comprobante_codigo <> '04'){
                    return redirect('listatransaccionCompra')->with('success','Transaccion registrada y autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $retencion->retencion_fecha)->format('d-m-Y').'/'.$retencion->retencion_xml_nombre.'.pdf')->with('diario',$url);
                }else{
                    return redirect('listatransaccionCompra')->with('success','Transaccion registrada exitosamente')->with('error2','ERROR SRI--> '.$retencionAux->retencion_xml_estado.' : '.$retencionAux->retencion_xml_mensaje)->with('diario',$url);
                }
            }
            else{
                return redirect('listatransaccionCompra')->with('success', 'Transaccion registrada exitosamente')->with('diario',$url);
            }
       }
        catch(\Exception $ex){
            DB::rollBack();      
            return redirect('listatransaccionCompra')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
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
