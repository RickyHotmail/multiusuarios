<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_Diario;
use App\Models\Detalle_FV;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Forma_Pago;
use App\Models\Movimiento_Producto;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Proforma;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Orden_Despacho;
use App\Models\Tarifa_Iva;
use App\Models\Vendedor;
use App\Models\Cliente;
use App\Models\Detalle_ND;
use App\Models\Detalle_OD;
use App\Models\Detalle_Pago_CXC;
use App\Models\Guia_Remision;
use App\Models\Movimiento_Caja;
use App\Models\Nota_Debito;
use App\Models\Pago_CXC;
use App\Models\Retencion_Venta;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class facturaVentaController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{            
            DB::beginTransaction();
            $rangoDocumentoorden = null;
            $secuencial=0;
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');

            $Contabilidad = false;
            /***************SABER SI SE GENERAR UN ASIENTO DE COSTO****************/
            $banderaP = false;
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                    $banderaP = true;
                }
            }
            if ($request->get('iddespacho')) {
                $rangoDocumentoorden=Rango_Documento::PuntoRango($request->get('punto_id'), 'Orden de Despacho')->first();
                if($rangoDocumentoorden){
                    $secuencial=$rangoDocumentoorden->rango_inicio;
                    $secuencialAux=Orden_Despacho::secuencial($rangoDocumentoorden->rango_id)->max('orden_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    
                    $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($request->get('punto_id'))->sucursal_id)->get();
                    foreach($puntosEmision as $punto){
                        $rangoDocumentoorden=Rango_Documento::PuntoRango($punto->punto_id, 'Orden de Despacho')->first();
                        if($rangoDocumentoorden){
                            break;
                        }
                    }
                    if($rangoDocumentoorden){
                        $secuencial=$rangoDocumentoorden->rango_inicio;
                        $secuencialAux=Orden_Despacho::secuencial($rangoDocumentoorden->rango_id)->max('orden_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    }else{
                        return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir ordenes de despacho, configueros y vuelva a intentar');
                    }
                }
            }

            /**********************************************************************/
            /********************cabecera de factura de venta ********************/
            $general = new generalController();
            $cierre = $general->cierre($request->get('factura_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/factura/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            if($general->documentos()){
                return redirect('/factura/new/'.$request->get('punto_id'))->with('error2','No puede generar documentos electronicos contrate un plan');
            }
            $docElectronico = new facturacionElectronicaController();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
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
                    if($banderaP){
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
                
                if($cxc->cuenta_estado == '2'){
                    /********************Pago por Venta en efectivo***************************/
                    $pago = new Pago_CXC();
                    $pago->pago_descripcion = 'PAGO EN EFECTIVO';
                    $pago->pago_fecha = $cxc->cuenta_fecha;
                    $pago->pago_tipo = 'PAGO EN EFECTIVO';
                    $pago->pago_valor = $cxc->cuenta_monto;
                    $pago->pago_estado = '1';
                    $pago->arqueo_id = $arqueoCaja->arqueo_id;
                   
                        $pago->diario()->associate($diario);
                    
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
               
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal');
                    $detalleDiario->detalle_haber = 0.00 ;
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    if($request->get('factura_tipo_pago') == 'CREDITO' OR $request->get('factura_tipo_pago') == 'CONTADO'){
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
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    
                
            if ($request->get('idIva') > 0){
                   
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
                /****************************************************************/
                /****************************************************************/
           
                $factura->diario()->associate($diario);
            
            if($arqueoCaja){
                $factura->arqueo_id = $arqueoCaja->arqueo_id;
            }
            $factura->save();
            if(isset($diario)){
                $general->registrarAuditoria('Registro de factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de factura de venta numero -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$factura->factura_autorizacion.' y con codigo de diario -> '.$diario->diario_codigo);
            }else{
                $general->registrarAuditoria('Registro de factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de factura de venta numero -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$factura->factura_autorizacion);
            }
           
            if( $request->get('proformafacturacion')){
                $proforma=Proforma::proforma($request->get('proformafacturacion'))->first();       
                $proforma->proforma_estado='2';
                $proforma->update();
                $general->registrarAuditoria('Actualizacion de Proforma Facturada N° -> '.$request->get('proformafacturacion') .'con factura N° '.$factura->factura_numero ,$request->get('proformafacturacion'),' Aprobada la Proforma N° ' .$request->get('proformafacturacion'));
            }
           
            /*******************************************************************/
            /********************detalle de factura de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                    if($producto->producto_stock < $cantidad[$i]){
                        throw new Exception('Stock insuficiente de productos');
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
                $factura->detalles()->save($detalleFV);
                $general->registrarAuditoria('Registro de detalle de factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de detalle de factura de venta numero -> '.$factura->factura_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $total[$i];
                    $detalleDiario->detalle_comentario = 'P/R VENTA DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'VENTAS')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general=='1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_venta;
                    }
                    
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$producto->cuentaVenta->cuenta_numero.' en el haber por un valor de -> '.$total[$i]);
                    
                    if($banderaP){
                        if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = 0.00;
                            $detalleDiario->detalle_haber = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'INVENTARIO')->first();
                            if($parametrizacionContable->parametrizacion_cuenta_general=='1'){
                                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            }else{
                                
                                $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                            }
                           
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$detalleDiario->detalle_haber);
                            
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_haber = 0.00;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$detalleDiario->detalle_debe);
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
              
                    $movimientoCaja->diario()->associate($diario);
                
                $movimientoCaja->save();
                /*********************************************************************/
            }
            /*******************************************************************/

            if ($request->get('iddespacho')) {
                /********************cabecera de orden de venta ********************/
                $general = new generalController();

                $orden = new Orden_Despacho();
                $orden->orden_numero = $rangoDocumentoorden->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoorden->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $orden->orden_serie = $rangoDocumentoorden->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoorden->puntoEmision->punto_serie;
                $orden->orden_secuencial = $secuencial;
                $orden->orden_fecha = $request->get('factura_fecha');
                $orden->orden_tipo_pago = $request->get('factura_tipo_pago');
                $orden->orden_dias_plazo = $request->get('factura_dias_plazo');
                $orden->orden_fecha_pago = $request->get('factura_fecha_termino');
                $orden->orden_subtotal = $request->get('idSubtotal');
                $orden->orden_descuento = $request->get('idDescuento');
                $orden->orden_tarifa0 = $request->get('idTarifa0');
                $orden->orden_tarifa12 = $request->get('idTarifa12');
                $orden->orden_iva = $request->get('idIva');
                $orden->orden_total = $request->get('idTotal');
                $orden->orden_reserva ='0';
                $orden->orden_comentario = 'Orden de despacho con Factura N° '.$factura->factura_numero;
                $orden->orden_porcentaje_iva = $request->get('factura_porcentaje_iva');
                $orden->orden_estado = '3';
                $orden->bodega_id = $request->get('bodega_id');
                $orden->cliente_id = $request->get('clienteID');
                $orden->rango_id = $rangoDocumentoorden->rango_id;
                $orden->vendedor_id = $request->get('vendedor_id');
                $orden->factura_id =$factura->factura_id;
                $orden->save();
                $general->registrarAuditoria('Registro de orden de Despacho numero -> '.$orden->orden_numero, $orden->orden_numero, 'Registro de orden de Despacho numero -> '.$orden->orden_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal'));
                /*******************************************************************/
                /********************detalle de factura de venta********************/
          
                for ($i = 1; $i < count($cantidad); ++$i) {
                    $detalleOD = new Detalle_OD();
                    $detalleOD->detalle_descripcion = $nombre[$i];
                    $detalleOD->detalle_cantidad = $cantidad[$i];
                    $detalleOD->detalle_precio_unitario = $pu[$i];
                    $detalleOD->detalle_descuento = $descuento[$i];
                    $detalleOD->detalle_iva = $iva[$i];
                    $detalleOD->detalle_total = $total[$i];
                    $detalleOD->detalle_estado = '1';
                    $detalleOD->producto_id = $isProducto[$i];
                    $orden->detalles()->save($detalleOD);
                    $general->registrarAuditoria('Registro de detalle de orden de Despacho numero -> '.$orden->orden_numero, $orden->orden_numero, 'Registro de detalle de orden de Despacho numero -> '.$orden->orden_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                }
            }
            $urlRecibo = '';
            $url = '';
            $urlRecibo = $general->FacturaRecibo($factura,1);
            if(Auth::user()->empresa->empresa_llevaContabilidad== '1'){
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
                return redirect('/factura/new/'.$request->get('punto_id'))->with('success','Factura registrada y autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('d-m-Y').'/'.$factura->factura_xml_nombre.'.pdf')->with('pdf2',$urlRecibo)->with('diario',$url);
            }elseif($factura->factura_emision != 'ELECTRONICA'){
                return redirect('/factura/new/'.$request->get('punto_id'))->with('success','Factura registrada exitosamente')->with('pdf2',$urlRecibo)->with('diario',$url);
            }else{
                return redirect('/factura/new/'.$request->get('punto_id'))->with('success','Factura registrada exitosamente')->with('error2','ERROR SRI--> '.$facturaAux->factura_xml_estado.' : '.$facturaAux->factura_xml_mensaje)->with('pdf2',$urlRecibo)->with('diario',$url);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/factura/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');
    }

    public function nuevo($id){
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Factura')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Factura_Venta::secuencial($rangoDocumento->rango_id)->max('factura_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $general = new generalController();
                if($general->documentos()){
                    return view('admin.ventas.facturas.nuevo',['vendedores'=>Vendedor::Vendedores()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($id)->get(),'formasPago'=>Forma_Pago::formaPagos()->get(), 'cajaAbierta'=>$cajaAbierta, 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin])->with('error2Msg','No puede generar documentos electronicos contrate un plan');
                }
                return view('admin.ventas.facturas.nuevo',['vendedores'=>Vendedor::Vendedores()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($id)->get(),'formasPago'=>Forma_Pago::formaPagos()->get(), 'cajaAbierta'=>$cajaAbierta, 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByNumeroFactura(Request $request){        
       return Factura_Venta::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
    }
    public function buscarByNumeroFacturaRetRecibida(Request $request){        
        if($request->get('tipoDocumento') == '0'){
            return Factura_Venta::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
        }else{
            return Nota_Debito::NotaDebitoNumero($request->get('buscar'),$request->get('bodega'))->get();
        }
        
    }
    public function buscarByNumeroFacturaAnt(Request $request){
        $resultado = [];
        $resultado[0] = Factura_Venta::FacturaNumeroAnt($request->get('buscar'),$request->get('bodega'))->get();
        $resultado[1] = Cuenta_Cobrar::CuentaByNumero($request->get('buscar'))->get();
        return $resultado;
    }
    public function buscarByFactura(Request $request){
        return Factura_Venta::FacturasbyNumero($request->get('buscar'))->get();
    }
    public function buscarByDetalleFactura(Request $request){
        return Detalle_FV::DetalleFactura($request->get('factura_id'))->get();
    }
    public function buscarByDetalleFactura2(Request $request){
        return Detalle_FV::DetalleFacturaTotal($request->get('factura_id'))->get();
    }
    public function buscarByDetalleFacturaRet(Request $request){
        if($request->get('tipoDocumento') == '0'){
            $datos = null;
            $datos[0] = Detalle_FV::DetalleFactura($request->get('factura_id'))->get();
            $datos[1] = Retencion_Venta::RetencionByFactura($request->get('factura_id'))->get();
            $datos[2] = Retencion_Venta::RetencionByFacturaS($request->get('factura_id'))->get();
            $datos[3] ='1';
            return $datos;
        }else{
            $datos = null;
            $datos[0] = Detalle_ND::DetalleNotaDebito($request->get('factura_id'))->get();
            $datos[1] = Retencion_Venta::RetencionByNotaDebito($request->get('factura_id'))->get();
            $datos[2] = Retencion_Venta::RetencionByNotaDebito($request->get('factura_id'))->get();
            $datos[3] ='2';
            return $datos;
        }
    }
    public function buscarByDetalleFacturaRet2(Request $request){
        if($request->get('tipoDocumento') == '0'){
            $datos = null;
            $datos[0] = Detalle_FV::DetalleFacturaTotal($request->get('factura_id'))->get();
            $datos[1] = Retencion_Venta::RetencionByFactura($request->get('factura_id'))->get();
            $datos[2] = Retencion_Venta::RetencionByFacturaS($request->get('factura_id'))->get();
            $datos[3] ='1';
            return $datos;
        }else{
            $datos = null;
            $datos[0] = Detalle_ND::DetalleNotaDebitoTotal($request->get('factura_id'))->get();
            $datos[1] = Retencion_Venta::RetencionByNotaDebito($request->get('factura_id'))->get();
            $datos[2] = Retencion_Venta::RetencionByNotaDebito($request->get('factura_id'))->get();
            $datos[3] ='2';
            return $datos;
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
            $inventarioResevado = false; 
            /********************cabecera de factura de venta ********************/
            $general = new generalController();
            $cierre = $general->cierre($request->get('factura_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/factura/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            if($general->documentos()){
                return redirect('/factura/new/'.$request->get('punto_id'))->with('error2','No puede generar documentos electronicos contrate un plan');
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
                
                if($banderaP){
                    
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
                if($cxc->cuenta_estado == '2'){
                    /********************Pago por Venta de Contado***************************/
                    $pago = new Pago_CXC();
                    $pago->pago_descripcion = 'PAGO EN EFECTIVO';
                    $pago->pago_fecha = $cxc->cuenta_fecha;
                    $pago->pago_tipo = 'PAGO EN EFECTIVO';
                    $pago->pago_valor = $cxc->cuenta_monto;
                    $pago->pago_estado = '1';
                    
                        $pago->diario()->associate($diario);
                    
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
                
                if ($request->get('idIva') > 0){
                    
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
                /****************************************************************/
                /****************************************************************/
            
                $factura->diario()->associate($diario);
              
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
                $factura->detalles()->save($detalleFV);
                $general->registrarAuditoria('Registro de detalle de factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de detalle de factura de venta numero -> '.$factura->factura_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                
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
                
                if($banderaP){
                    if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                        
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

                    $movimientoCaja->diario()->associate($diario);
                
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
                return redirect('/listaGuiasOrdenes')->with('success','Factura registrada y autorizada exitosamente')->with('diario',$url)->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('d-m-Y').'/'.$factura->factura_xml_nombre.'.pdf');
            }elseif($factura->factura_emision != 'ELECTRONICA'){
                return redirect('/listaGuiasOrdenes')->with('success','Factura registrada exitosamente')->with('diario',$url);
            }else{
                return redirect('/listaGuiasOrdenes')->with('success','Factura registrada exitosamente')->with('error2','ERROR SRI--> '.$facturaAux->factura_xml_estado.' : '.$facturaAux->factura_xml_mensaje)->with('diario',$url);
            }
            
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/factura/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
