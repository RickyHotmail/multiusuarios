<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Centro_Consumo;
use App\Models\Concepto_Retencion;
use App\Models\Cuenta;
use App\Models\Cuenta_Pagar;
use App\Models\Detalle_Diario;
use App\Models\Detalle_LC;
use App\Models\Detalle_Pago_CXP;
use App\Models\Detalle_RC;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Forma_Pago;
use App\Models\Liquidacion_Compra;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Producto;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Retencion_Compra;
use App\Models\Sustento_Tributario;
use App\Models\Tarifa_Iva;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class liquidacionCompraController extends Controller
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
            /********************detalle de la liquidacion de compra ********************/
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
            /********************cabecera de liquidacion de compra ********************/
            $general = new generalController();
            $docElectronico = new facturacionElectronicaController();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $cierre = $general->cierre($request->get('lc_fecha'),Rango_Documento::rango($request->get('rango_id_lc'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/liquidacionCompra/new/'.$request->get('punto_id_lc'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $lc = new Liquidacion_Compra();
            $lc->lc_fecha = $request->get('lc_fecha');
            $lc->lc_numero = $request->get('lc_serie').substr(str_repeat(0, 9).$request->get('lc_secuencial'), - 9);
            $lc->lc_serie = $request->get('lc_serie');
            $lc->lc_secuencial = $request->get('lc_secuencial');
            $lc->lc_subtotal = $request->get('idSubtotal');
            $lc->lc_descuento = $request->get('idDescuento');
            $lc->lc_tarifa0 = $request->get('idTarifa0');
            $lc->lc_tarifa12 = $request->get('idTarifa12');
            $lc->lc_iva = $request->get('idIva');
            $lc->lc_total = $request->get('idTotal');
            $lc->lc_ivaB = $request->get('IvaBienesID');
            $lc->lc_ivaS = $request->get('IvaServiciosID');
            $lc->lc_dias_plazo = $request->get('lc_dias_plazo');
            $lc->lc_comentario = '';
            $lc->lc_tipo_pago = $request->get('lc_tipo_pago');
            $lc->lc_porcentaje_iva = $request->get('lc_porcentaje_iva');
            $lc->lc_emision = $request->get('tipoDoc_lc');
            $lc->lc_ambiente = 'PRODUCCIÓN';
            $lc->lc_autorizacion = $docElectronico->generarClaveAcceso($lc->lc_numero,$request->get('lc_fecha'),"03");
            $lc->lc_estado = '1';
            $lc->proveedor_id = $request->get('proveedorID');
            $lc->sustento_id = $request->get('sustento_id');
            $lc->forma_pago_id = $request->get('forma_pago_id');
            $lc->rango_id = $request->get('rango_id_lc');
            $valorCXP= $request->get('idTotal')-$request->get('id_total_fuente')-$request->get('id_total_iva');
                /********************cuenta por pagar***************************/
                $cxp = new Cuenta_Pagar();
                $cxp->cuenta_descripcion = 'LIQUIDACIÓN DE COMPRA A PROVEEDOR '.$request->get('buscarProveedor').' CON DOCUMENTO No. '.$lc->lc_numero;
                if($request->get('lc_tipo_pago') == 'CREDITO'){
                    $cxp->cuenta_tipo ='CREDITO';
                    $cxp->cuenta_saldo = $valorCXP;
                    $cxp->cuenta_estado = '1';
                }else{
                    $cxp->cuenta_tipo = 'CONTADO';
                    $cxp->cuenta_saldo = 0.00;
                    $cxp->cuenta_estado = '2';
                }
                $cxp->cuenta_fecha = $request->get('lc_fecha');
                $cxp->cuenta_fecha_inicio = $request->get('lc_fecha');
                $cxp->cuenta_fecha_fin = date("Y-m-d",strtotime($request->get('lc_fecha')."+ ".$request->get('lc_dias_plazo')." days"));
                $cxp->cuenta_monto = $valorCXP;
                $cxp->cuenta_valor_factura = $request->get('idTotal');
                $cxp->proveedor_id = $lc->proveedor_id;
                $cxp->sucursal_id = Rango_Documento::rango($request->get('rango_id_lc'))->first()->puntoEmision->sucursal_id;
                $cxp->save();
                $general->registrarAuditoria('Registro de cuenta por pagar de liquidación de compra -> '.$lc->lc_numero,$lc->lc_numero,'Registro de cuenta por pagar de liquidación de compra -> '.$lc->lc_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                /****************************************************************/
            $lc->cuentaPagar()->associate($cxp);
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('lc_fecha'),'CLCE');
                $diario->diario_tipo = 'CLCE';
                $diario->diario_fecha = $request->get('lc_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE LIQUIDACIÓN DE COMPRA';
                $diario->diario_tipo_documento = 'LIQUIDACIÓN DE COMPRA';
                $diario->diario_numero_documento = $lc->lc_numero;
                $diario->diario_beneficiario = $request->get('buscarProveedor');
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('lc_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('lc_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DIARIO DE LIQUIDACIÓN DE COMPRA : '.$lc->lc_numero;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id_lc'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de compra con LIQUIDACIÓN DE COMPRA -> '.$lc->lc_numero,$lc->lc_numero,'Registro de diario de compra de LIQUIDACIÓN DE COMPRA -> '.$lc->lc_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                /****************************************************************/
            $lc->diario()->associate($diario);
            if($arqueoCaja){
                $lc->arqueo_id = $arqueoCaja->arqueo_id;
            }
            $lc->save();
            $general->registrarAuditoria('Registro de liquidación de compra numero -> '.$lc->lc_numero,$lc->lc_numero,'Registro de liquidación de compra numero -> '.$lc->lc_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
            /****************************************************************/
            /********************detalle de liquidacion de compra********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleLC = new Detalle_LC();
                $detalleLC->detalle_cantidad = $cantidad[$i];
                $detalleLC->detalle_precio_unitario =$pu[$i];
                $detalleLC->detalle_descuento = $descuento[$i];
                $detalleLC->detalle_iva = $iva[$i];
                $detalleLC->detalle_total = $total[$i];
                $detalleLC->detalle_descripcion = $descripcion[$i];
                $detalleLC->detalle_estado = '1';
                $detalleLC->producto_id = $isProducto[$i];
                $detalleLC->bodega_id = $bodega[$i];
                $detalleLC->centro_consumo_id = $cconsumo[$i];
                /******************registro de movimiento de producto******************/
                $movimientoProducto = new Movimiento_Producto();
                $movimientoProducto->movimiento_fecha=$request->get('lc_fecha');
                $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                $movimientoProducto->movimiento_precio=$pu[$i];
                $movimientoProducto->movimiento_iva=$iva[$i];
                $movimientoProducto->movimiento_total=$total[$i];
                $movimientoProducto->movimiento_stock_actual=0;
                $movimientoProducto->movimiento_costo_promedio=0;
                $movimientoProducto->movimiento_documento='LIQUIDACIÓN DE COMPRA';
                $movimientoProducto->movimiento_motivo='COMPRA';
                $movimientoProducto->movimiento_tipo='ENTRADA';
                $movimientoProducto->movimiento_descripcion='LIQUIDACIÓN DE COMPRA No. '.$lc->lc_numero;
                $movimientoProducto->movimiento_estado='1';
                $movimientoProducto->producto_id=$isProducto[$i];
                $movimientoProducto->bodega_id=$detalleLC->bodega_id;
                $movimientoProducto->centro_consumo_id=$detalleLC->centro_consumo_id;
                $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                $movimientoProducto->save();
                $general->registrarAuditoria('Registro de movimiento de producto por liquidación de compra numero -> '.$lc->lc_numero,$lc->lc_numero,'Registro de movimiento de producto por liquidación de compra numero -> '.$lc->lc_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                /****************************************************************/
                /********************detalle de diario de compra********************/
                $producto = Producto::findOrFail($isProducto[$i]);
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $total[$i];
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario = 'P/R LIQUIDACIÓN DE COMPRA DE COMPRA DE PRODUCTO '.$producto->producto_codigo;
                $detalleDiario->detalle_tipo_documento = 'LIQUIDACIÓN DE COMPRA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                if($producto->cuentaInventario){
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                }else{
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_gasto;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$lc->lc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta_id.' por un valor de -> '.$total[$i]);
                /**********************************************************************/
                    
                $detalleLC->movimiento()->associate($movimientoProducto);
                $lc->detalles()->save($detalleLC);
                $general->registrarAuditoria('Registro de detalle de liquidación de compra numero -> '.$request->get('factura_serie').$request->get('factura_numero'),$request->get('factura_serie').$request->get('factura_numero'),'Registro de detalle de liquidación de compra numero -> '.$request->get('factura_serie').$request->get('factura_numero').' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
            }
            /****************************************************************/
            /********************detalle de diario de compra********************/
            if ($request->get('IvaBienesID') > 0){
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('IvaBienesID');
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario = 'P/R IVA PAGADO POR BIENES EN COMPRA';
                $detalleDiario->detalle_tipo_documento = 'LIQUIDACIÓN DE COMPRA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA BIENES COMPRAS')->first();
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$lc->lc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$parametrizacionContable->cuenta->cuenta_numero.' por un valor de -> '.$request->get('IvaBienesID'));
            }
            if ($request->get('IvaServiciosID') > 0){
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('IvaServiciosID');
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario = 'P/R IVA PAGADO POR SERVICIOS EN COMPRAS';
                $detalleDiario->detalle_tipo_documento = 'LIQUIDACIÓN DE COMPRA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA SERVICIOS COMPRAS')->first();
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$lc->lc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$parametrizacionContable->cuenta->cuenta_numero.' por un valor de -> '.$request->get('IvaServiciosID'));
            }
            /****************************************************************/
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
            $retencion->liquidacionCompra()->associate($lc);
            $retencion->save();
            $general->registrarAuditoria('Registro de retencion de compra numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de retencion de compra numero -> '.$retencion->retencion_numero.' y con codigo de diario -> '.$diario->diario_codigo);
            /******************************************************************/
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
                    /********************detalle de diario de compra*******************/
                    $detalleDiario = new Detalle_Diario();
                    $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetF[$i])->first();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $valorF[$i];
                    $detalleDiario->detalle_comentario = 'P/R RETENCION EN LA FUENTE '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                    $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION';
                    $detalleDiario->detalle_numero_documento = $retencion->retencion_numero;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_emitida_cuenta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$lc->lc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorF[$i]);
                    /******************************************************************/
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
                    /********************detalle de diario de compra*******************/
                    $detalleDiario = new Detalle_Diario();
                    $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetI[$i])->first();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $valorI[$i];
                    $detalleDiario->detalle_comentario = 'P/R RETENCION DE IVA '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                    $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION';
                    $detalleDiario->detalle_numero_documento = $retencion->retencion_numero;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_emitida_cuenta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$lc->lc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorI[$i]);
                    /******************************************************************/
            }
            /********************detalle de diario de compra*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber = $valorCXP;
            $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
            $detalleDiario->detalle_tipo_documento = 'LIQUIDACIÓN DE COMPRA';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
            if($request->get('lc_tipo_pago') == 'CREDITO' OR $request->get('lc_tipo_pago') == 'CONTADO' ){
                $detalleDiario->proveedor_id = $request->get('proveedorID');
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                    $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                }
            }else{
                $detalleDiario->detalle_comentario = 'P/R LIQUIDACION DE COMPRA EN EFECTIVO';
                $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$lc->lc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$valorCXP);
            /******************************************************************/
            /******************************************************************/
            if($lc->lc_emision == 'ELECTRONICA'){
                $lcAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlLC($lc),'LC');
                $lc->lc_xml_estado = $lcAux->lc_xml_estado;
                $lc->lc_xml_mensaje =$lcAux->lc_xml_mensaje;
                $lc->lc_xml_respuestaSRI = $lcAux->lc_xml_respuestaSRI;
                if($lcAux->lc_xml_estado == 'AUTORIZADO'){
                    $lc->lc_xml_nombre = $lcAux->lc_xml_nombre;
                    $lc->lc_xml_fecha = $lcAux->lc_xml_fecha;
                    $lc->lc_xml_hora = $lcAux->lc_xml_hora;
                }
                $lc->update();
            }
            /******************************************************************/
            /******************************************************************/
            if($request->get('lc_tipo_pago') == 'EN EFECTIVO'){
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="SALIDA";
                $movimientoCaja->movimiento_descripcion= 'P/R LIQUIDACION DE COMPRA :'.$request->get('buscarProveedor');
                $movimientoCaja->movimiento_valor= $valorCXP;
                $movimientoCaja->movimiento_documento="LIQUIDACION DE COMPRA";
                $movimientoCaja->movimiento_numero_documento= $lc->lc_numero;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                    $movimientoCaja->diario()->associate($diario);
                }
                $movimientoCaja->save();
                /*********************************************************************/
            }
            if($retencion->retencion_emision == 'ELECTRONICA'){
                $retencionAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlRetencion($retencion),'RETENCION');
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
            DB::commit();
            if($retencionAux->retencion_xml_estado == 'AUTORIZADO' and $lcAux->lc_xml_estado == 'AUTORIZADO'){
                return redirect('/liquidacionCompra/new/'.$request->get('punto_id_lc'))->with('success','LIQUIDACIÓN DE COMPRA y RETENCIÓN registradas exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('retencion_fecha'))->format('d-m-Y').'/'.$retencionAux->retencion_xml_nombre.'.pdf')->with('pdf2','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('lc_fecha'))->format('d-m-Y').'/'.$lcAux->lc_xml_nombre.'.pdf');
            }else if($retencionAux->retencion_xml_estado == 'AUTORIZADO' and $lcAux->lc_xml_estado <> 'AUTORIZADO'){
                return redirect('/liquidacionCompra/new/'.$request->get('punto_id_lc'))->with('success','LIQUIDACIÓN DE COMPRA y RETENCIÓN registradas exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('retencion_fecha'))->format('d-m-Y').'/'.$retencionAux->retencion_xml_nombre.'.pdf')->with('error2','ERROR LIQUIDACIÓN DE COMPRA --> '.$lcAux->lc_xml_estado.' : '.$lcAux->lc_xml_mensa);
            }else if($retencionAux->retencion_xml_estado <> 'AUTORIZADO' and $lcAux->lc_xml_estado == 'AUTORIZADO'){
                return redirect('/liquidacionCompra/new/'.$request->get('punto_id_lc'))->with('success','LIQUIDACIÓN DE COMPRA y RETENCIÓN registradas exitosamente')->with('pdf2','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('lc_fecha'))->format('d-m-Y').'/'.$lcAux->lc_xml_nombre.'.pdf')->with('error2','ERROR RETENCIÓN --> '.$retencionAux->retencion_xml_estado.' : '.$retencionAux->retencion_xml_mensaje);
            }else{
                return redirect('/liquidacionCompra/new/'.$request->get('punto_id_lc'))->with('success','LIQUIDACIÓN DE COMPRA y RETENCIÓN registradas exitosamente')->with('error2','ERROR RETENCIÓN --> '.$retencionAux->retencion_xml_estado.' : '.$retencionAux->retencion_xml_mensaje.' ************ ERROR LIQUIDACIÓN DE COMPRA --> '.$lcAux->lc_xml_estado.' : '.$lcAux->lc_xml_mensa);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/liquidacionCompra/new/'.$request->get('punto_id_lc'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{
            DB::beginTransaction();        
            $compras=Liquidacion_Compra::findOrFail($id);
            $general = new generalController();
            $cierre = $general->cierre($compras->lc_fecha,$compras->diario->sucursal_id);          
            if($cierre){
                return redirect('eliminacionComprantes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach($compras->detalles as $detalles){

                $detall=Detalle_LC::findOrFail($detalles->detalle_id);
                $detall->movimiento_id=null;
                $detall->save();
                if (isset($detalles->movimiento)) {
                    $aux = $detalles->movimiento;
                    $detalles->movimiento->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion de Movimiento de producto por factura de venta numero: -> '.$compras->lc_numero, $id, 'Con Producto '.$detalles->producto->producto_nombre.' Con la cantidad de producto de '.$aux->movimiemovimiento_totalnto_cantidad.' y total '.$aux->movimiento_total);
                }
                $aux = $detalles; 
                $detalles->delete(); 
                $general->registrarAuditoria('Eliminacion de detalles de producto por factura de venta numero: -> '.$compras->lc_numero, $id, 'Con Producto '.$aux->producto->producto_nombre.' Con la cantidad de producto de '.$aux->detalle_cantidad.' y total '.$aux->detalle_total);
            }
            if (isset($compras->cuentaPagar)) {
                $lcompra=Liquidacion_Compra::findOrFail($compras->lc_id);
                $lcompra->cuenta_id=null;
                $lcompra->save();
            
                $aux=$compras->cuentaPagar;
                $compras->cuentaPagar->delete();
                $general->registrarAuditoria('Eliminacion de la cuenta por cobrar por factura de venta numero: -> '.$compras->lc_numero, $id, ' Descripcion -> '.$aux->cuenta_descripcion.' Con el monto-> '.$aux->cuenta_monto);
            }
            foreach ($compras->diario->detalles as $detalles) {
                $aux=$detalles;
                $detalles->delete();
                
                $general->registrarAuditoria('Eliminacion de detalle de diario por factura de venta numero: -> '.$compras->lc_numero, $id,  'Con id de diario-> '.$aux->diario_id.'Comentario -> '.$aux->detalle_comentario  );
            }
            


            $lcompra=Liquidacion_Compra::findOrFail($compras->lc_id);
            $lcompra->diario_id=null;
            $lcompra->save();

            $aux=$compras->diario;
            $compras->diario->delete();                        
            $general->registrarAuditoria('Eliminacion de diario por factura de venta numero: -> '.$compras->lc_numero, $id, 'Con el diario-> '.$aux->diario_codigo.'Comentario -> '.$aux->diario_comentario);


            foreach ($compras->retencionCompra->detalles as $detalles) {
                $aux=$detalles;
                $detalles->delete();
                $general->registrarAuditoria('Eliminacion de detalle de la retencion por la liquidacion de compra numero: -> '.$compras->lc_numero, $id,  ' tipo -> '.$aux->detalle_tipo.' con el valor -> '.$aux->detalle_valor  );
            }

            $aux=$compras->retencionCompra;
            $compras->retencionCompra->delete();                        
            $general->registrarAuditoria('Eliminacion de la retencion por la liquidacion de compra numero: -> '.$compras->lc_numero, $id, 'Retencion numero-> '.$aux->retencion_numero);

            $aux=$compras;
            $compras->delete();                         
            $general->registrarAuditoria('Eliminacion de la factura de venta numero: -> '.$compras->lc_numero, $id, 'Con el valor de -> '.$aux->factura_total);
           
            
            DB::commit();
            return redirect('eliminacionComprantes')->with('success','Datos Eliminados exitosamente');
                        
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('eliminacionComprantes')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
       
    }
    public function eliminar($id)
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $compras=Liquidacion_Compra::LiquidacionCompra($id)->get()->first();      
            return view('admin.sri.eliminacionComprabantes.eliminacionliquidacion',['compras'=>$compras, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $rangoDocumentoLC=Rango_Documento::PuntoRango($id,'Liquidación de compra de Bienes o Prestación de servicios')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $secuencialLC=1;
            if($rangoDocumentoLC){
                $secuencialAuxLC=Liquidacion_Compra::secuencial($rangoDocumentoLC->rango_id)->max('lc_secuencial');
                if($secuencialAuxLC){$secuencialLC=$secuencialAuxLC+1;}
                /* secuencial retenciones */
                $rangoDocumento=Rango_Documento::PuntoRango($id,'Comprobante de Retención')->first();
                $secuencial=1;
                if($rangoDocumento){
                    $secuencial=$rangoDocumento->rango_inicio;
                    $secuencialAux=Retencion_Compra::secuencial($rangoDocumento->rango_id)->max('retencion_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                    return view('admin.compras.liquidacionCompra.nuevo',['cajaAbierta'=>$cajaAbierta,'rangoDocumentoLC'=>$rangoDocumentoLC,'secuencialLC'=>substr(str_repeat(0, 9).$secuencialLC, - 9),'rangoDocumento'=>$rangoDocumento,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'bodegas'=>Bodega::BodegasSucursal(Punto_Emision::Punto($id)->first()->sucursal_id)->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'formasPago'=>Forma_Pago::formaPagos()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                }else{
                    return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir retenciones, configueros y vuelva a intentar');
                }
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir liquidacion de compra, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
