<?php

namespace App\Http\Controllers;

use App\Models\Anticipo_Cliente;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Cuenta_Cobrar;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Detalle_NC;
use App\Models\Detalle_Pago_CXC;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Forma_Pago;
use App\Models\Movimiento_Producto;
use App\Models\Nota_Credito;
use App\Models\Pago_CXC;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tarifa_Iva;
use App\Models\Vendedor;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class notaCreditoController extends Controller
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
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            /***************SABER SI SE GENERAR UN ASIENTO DE COSTO****************/
            $banderaP = false;
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                if($producto->producto_tipo == '1'){
                    $banderaP = true;
                }
            }
            /**********************************************************************/
            /********************cabecera de factura de venta ********************/
            $general = new generalController();
            $docElectronico = new facturacionElectronicaController();
            $nc = new Nota_Credito();
            $cierre = $general->cierre($request->get('nc_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $nc->nc_numero = $request->get('nc_serie').substr(str_repeat(0, 9).$request->get('nc_numero'), - 9);
            $nc->nc_serie = $request->get('nc_serie');
            $nc->nc_secuencial = $request->get('nc_numero');
            $nc->nc_fecha = $request->get('nc_fecha');
            $nc->nc_subtotal = $request->get('idSubtotal');
            $nc->nc_descuento = $request->get('idDescuento');
            $nc->nc_tarifa0 = $request->get('idTarifa0');
            $nc->nc_tarifa12 = $request->get('idTarifa12');
            $nc->nc_iva = $request->get('idIva');
            $nc->nc_total = $request->get('idTotal');
            if($request->get('nc_comentario')){
                $nc->nc_comentario = $request->get('nc_comentario');
            }else{
                $nc->nc_comentario = '';
            }
            $nc->nc_porcentaje_iva = $request->get('idTarifaIva');
            $nc->nc_emision = $request->get('tipoDoc');
            $nc->nc_ambiente = 'PRODUCCIÓN';
            $nc->nc_autorizacion = $docElectronico->generarClaveAcceso($nc->nc_numero,$request->get('nc_fecha'),"04");
            $nc->nc_estado = '1';
            $nc->factura_id = $request->get('factura_id');
            $nc->rango_id = $request->get('rango_id');

 
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('nc_fecha'),'CNCE');
                $diario->diario_fecha = $request->get('nc_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE NOTA DE CRÉDITO DE VENTA';
                $diario->diario_tipo_documento = 'NOTA DE CRÉDITO';
                $diario->diario_numero_documento = $nc->nc_numero;
                $diario->diario_beneficiario = $request->get('nombreCliente');
                $diario->diario_tipo = 'CNCE';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DIARIO DE NOTA DE CRÉDITO: '.$nc->nc_numero;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de venta de nota de crédito -> '.$nc->nc_numero,$nc->nc_numero,'Registro de diario de venta de nota de crédito -> '.$nc->nc_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                /*******************************************************************/
                if($banderaP and $nc->nc_comentario == 'DEVOLUCION'){
                    /**********************asiento diario de costo ****************************/
                    $diarioC = new Diario();
                    $diarioC->diario_codigo = $general->generarCodigoDiario($request->get('nc_fecha'),'CCVP');
                    $diarioC->diario_fecha = $request->get('nc_fecha');
                    $diarioC->diario_referencia = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO';
                    $diarioC->diario_tipo_documento = 'NOTA DE CRÉDITO';
                    $diarioC->diario_numero_documento = $nc->nc_numero;
                    $diarioC->diario_beneficiario = $request->get('nombreCliente');
                    $diarioC->diario_tipo = 'CCVP';
                    $diarioC->diario_secuencial = substr($diarioC->diario_codigo, 8);
                    $diarioC->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('m');
                    $diarioC->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('Y');
                    $diarioC->diario_comentario = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO CON NOTA DE CRÉDITO: '.$nc->nc_numero;
                    $diarioC->diario_cierre = '0';
                    $diarioC->diario_estado = '1';
                    $diarioC->empresa_id = Auth::user()->empresa_id;
                    $diarioC->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                    $diarioC->save();
                    $general->registrarAuditoria('Registro de diario de costo de venta de nota de crédito -> '.$nc->nc_numero,$nc->nc_numero,'Registro de diario de costo de venta de nota de crédito -> '.$nc->nc_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diarioC->diario_codigo);
                    /************************************************************************/
                    $nc->diarioCosto()->associate($diarioC);
                }            
                $nc->diario()->associate($diario);
            
            $nc->save();
           
                $general->registrarAuditoria('Registro de nota de crédito de venta numero -> '.$nc->nc_numero,$nc->nc_numero,'Registro de nota de crédito de venta numero -> '.$nc->nc_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$nc->nc_autorizacion.' y con codigo de diario -> '.$diario->diario_codigo);
            
            /*******************************************************************/
            /********************Pago por Nota de Credito***************************/
            $facturaAux = Factura_Venta::Factura($request->get('factura_id'))->first(); 
            $cxcAux = $facturaAux->cuentaCobrar;
            if($cxcAux->cuenta_saldo == 0){
                $rangoDocumentoRetencion=Rango_Documento::PuntoRango($facturaAux->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                if($rangoDocumentoRetencion){
                    $secuencial=$rangoDocumentoRetencion->rango_inicio;
                    $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($facturaAux->rangoDocumento->punto_id)->sucursal_id)->get();
                    foreach($puntosEmision as $punto){
                        $rangoDocumentoRetencion=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Cliente')->first();
                        if($rangoDocumentoRetencion){
                            break;
                        }
                    }
                    if($rangoDocumentoRetencion){
                        $secuencial=$rangoDocumentoRetencion->rango_inicio;
                        $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    }else{
                        return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir anticipos de clientes, configueros y vuelva a intentar');
                    }
                }
                /********************Anticipo por Retencion de Venta***************************/
                $anticipoCliente = new Anticipo_Cliente();
                $anticipoCliente->anticipo_numero = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $anticipoCliente->anticipo_serie = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie;
                $anticipoCliente->anticipo_secuencial = $secuencial;
                $anticipoCliente->anticipo_fecha = $request->get('nc_fecha');
                $anticipoCliente->anticipo_tipo = 'NOTA DE CRÉDITO';      
                $anticipoCliente->anticipo_documento = $nc->nc_numero;      
                $anticipoCliente->anticipo_motivo = $request->get('nc_comentario');
                $anticipoCliente->anticipo_valor = $request->get('idTotal');  
                $anticipoCliente->anticipo_saldo = $request->get('idTotal');   
                $anticipoCliente->cliente_id = $facturaAux->cliente_id;
                $anticipoCliente->rango_id = $rangoDocumentoRetencion->rango_id;
                $anticipoCliente->anticipo_estado = 1; 
               
                    $anticipoCliente->diario()->associate($diario);
                
                $anticipoCliente->save();
                $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$request->get('idCliente'),'0','Con motivo: Nota de Crédito');
                
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $request->get('idTotal');
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE CLIENTE';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                
            }else if($cxcAux->cuenta_saldo >= $nc->nc_total){
                /********************Pago por Nota de Credito***************************/
                $pago = new Pago_CXC();
                $pago->pago_descripcion = $nc->nc_comentario. ' POR NOTA DE CRÉDITO '.' No. '.$nc->nc_numero;
                $pago->pago_fecha = $request->get('nc_fecha');
                $pago->pago_tipo = 'NOTA DE CRÉDITO';
                $pago->pago_valor = $request->get('idTotal');
                $pago->pago_estado = '1';
                
                    $pago->diario()->associate($diario);
                
                $pago->save();

                $general->registrarAuditoria('Registro de pago a Cliente -> '.$request->get('nombreCliente'),'0','Pago de factura No. '.$facturaAux->factura_numero.' con motivo: Nota de Crédito').' No. '.$nc->nc_numero; 

                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = $nc->nc_comentario. ' POR NOTA DE CRÉDITO '.' No. '.$nc->nc_numero; 
                $detallePago->detalle_pago_valor = $request->get('idTotal'); 
                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1; 
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();

                $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$request->get('nombreCliente'),'0','Detalle de pago de factura No. '.$facturaAux->factura_numero.' con motivo: Nota de Crédito').' No. '.$nc->nc_numero; 

                $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($facturaAux->factura_id)->sum('descuento_valor'),2);
                if($cxcAux->cuenta_saldo == 0){
                    $cxcAux->cuenta_estado = '2';
                }else{
                    $cxcAux->cuenta_estado = '1';
                }
                $cxcAux->update();
                /*Inicio de registro de auditoria*/
                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$facturaAux->factura_numero);
                /*Fin de registro de auditoria*/ 
               
                /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $request->get('idTotal');
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                
            }else{
                $rangoDocumentoRetencion=Rango_Documento::PuntoRango($facturaAux->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                if($rangoDocumentoRetencion){
                    $secuencial=$rangoDocumentoRetencion->rango_inicio;
                    $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($facturaAux->rangoDocumento->punto_id)->sucursal_id)->get();
                    foreach($puntosEmision as $punto){
                        $rangoDocumentoRetencion=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Cliente')->first();
                        if($rangoDocumentoRetencion){
                            break;
                        }
                    }
                    if($rangoDocumentoRetencion){
                        $secuencial=$rangoDocumentoRetencion->rango_inicio;
                        $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    }else{
                        return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir anticipos de clientes, configueros y vuelva a intentar');
                    }
                }
                /********************Anticipo por Retencion de Venta***************************/
                $anticipoCliente = new Anticipo_Cliente();
                $anticipoCliente->anticipo_numero = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $anticipoCliente->anticipo_serie = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie;
                $anticipoCliente->anticipo_secuencial = $secuencial;
                $anticipoCliente->anticipo_fecha = $request->get('nc_fecha');
                $anticipoCliente->anticipo_tipo = 'NOTA DE CRÉDITO';      
                $anticipoCliente->anticipo_documento = $nc->nc_numero;      
                $anticipoCliente->anticipo_motivo = $request->get('nc_comentario');
                $anticipoCliente->anticipo_valor = $request->get('idTotal') - $cxcAux->cuenta_saldo;  
                $anticipoCliente->anticipo_saldo = $request->get('idTotal') - $cxcAux->cuenta_saldo;  
                $anticipoCliente->cliente_id = $facturaAux->cliente_id;
                $anticipoCliente->rango_id = $rangoDocumentoRetencion->rango_id;
                $anticipoCliente->anticipo_estado = 1; 
                
                    $anticipoCliente->diario()->associate($diario);
                
                $anticipoCliente->save();
                $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$request->get('nombreCliente'),'0','Con motivo: Nota de Crédito');
                
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $request->get('idTotal')- $cxcAux->cuenta_saldo;  
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE CLIENTE';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                
                /*******************************************************************/
                /********************Pago por Nota de Credito***************************/
                $pago = new Pago_CXC();
                $pago->pago_descripcion = $nc->nc_comentario. ' POR NOTA DE CRÉDITO '.' No. '.$nc->nc_numero;
                $pago->pago_fecha = $request->get('nc_fecha');
                $pago->pago_tipo = 'NOTA DE CRÉDITO';
                $pago->pago_valor = $cxcAux->cuenta_saldo;
                $pago->pago_estado = '1';
                
                    $pago->diario()->associate($diario);
                
                $pago->save();

                $general->registrarAuditoria('Registro de pago a Cliente -> '.$request->get('nombreCliente'),'0','Pago de factura No. '.$facturaAux->factura_numero.' con motivo: Nota de Crédito').' No. '.$nc->nc_numero; 

                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = $nc->nc_comentario. ' POR NOTA DE CRÉDITO '.' No. '.$nc->nc_numero; 
                $detallePago->detalle_pago_valor = $cxcAux->cuenta_saldo;
                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1; 
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();

                $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$request->get('nombreCliente'),'0','Detalle de pago de factura No. '.$facturaAux->factura_numero.' con motivo: Nota de Crédito').' No. '.$nc->nc_numero; 

                $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($facturaAux->factura_id)->sum('descuento_valor'),2);
                if($cxcAux->cuenta_saldo == 0){
                    $cxcAux->cuenta_estado = '2';
                }else{
                    $cxcAux->cuenta_estado = '1';
                }
                $cxcAux->update();
                /*Inicio de registro de auditoria*/
                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$facturaAux->factura_numero);
                /*Fin de registro de auditoria*/ 
                
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $cxcAux->cuenta_saldo;
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                
            }
            /****************************************************************/
            
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                /********************detalle de factura de venta********************/
                $detalleNC = new Detalle_NC();
                $detalleNC->detalle_cantidad = $cantidad[$i];
                $detalleNC->detalle_precio_unitario = $pu[$i];
                $detalleNC->detalle_descuento = $descuento[$i];
                $detalleNC->detalle_iva = $iva[$i];
                $detalleNC->detalle_total = $total[$i];
                $detalleNC->detalle_estado = '1';
                $detalleNC->producto_id = $isProducto[$i];
                    /******************registro de movimiento de producto******************/
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('nc_fecha');
                    if($nc->nc_comentario == 'DESCUENTO'){
                        $movimientoProducto->movimiento_cantidad=0;
                    }else{
                        $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    }
                    $movimientoProducto->movimiento_precio=$pu[$i];
                    $movimientoProducto->movimiento_iva=$iva[$i];
                    $movimientoProducto->movimiento_total=$total[$i];
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='NOTA DE CRÉDITO';
                    $movimientoProducto->movimiento_motivo='VENTA';
                    $movimientoProducto->movimiento_tipo='ENTRADA';
                    $movimientoProducto->movimiento_descripcion='NOTA DE CRÉDITO No. '.$nc->nc_numero.' POR '.$nc->nc_comentario;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$nc->factura->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por nota de crédito de venta numero -> '.$nc->nc_numero,$nc->nc_numero,'Registro de movimiento de producto por nota de crédito de venta numero -> '.$nc->nc_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                $detalleNC->movimiento()->associate($movimientoProducto);
                $nc->detalles()->save($detalleNC);
                $general->registrarAuditoria('Registro de detalle de nota de crédito de venta numero -> '.$nc->nc_numero,$nc->nc_numero,'Registro de detalle de nota de crédito de venta numero -> '.$nc->nc_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                /*******************************************************************/
                
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $total[$i];
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R NOTA DE CRÉDITO DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_venta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$producto->cuentaVenta->cuenta_numero.' en el haber por un valor de -> '.$total[$i]);
                    /*******************************************************************/
                
                if($banderaP and $nc->nc_comentario == 'DEVOLUCION'){
                    if($producto->producto_tipo == '1'){
                        
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_haber = 0.00;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR NOTA DE CRÉDITO DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$detalleDiario->detalle_debe);
                            
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = 0.00;
                            $detalleDiario->detalle_haber = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR NOTA DE CRÉDITO DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $parametrizacionContable = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$detalleDiario->detalle_haber);
                        
                    }
                }
            }
            /********************detalle de diario de venta********************/
            if ($request->get('idIva') > 0){
                
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idIva');
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R IVA POR NOTA DE CRÉDITO';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA VENTAS')->first();
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$request->get('idIva'));
                
            }
            $url ='';
            /****************************************************************/
            if(Auth::user()->empresa->empresa_llevaContabilidad== '1'){
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            if($nc->nc_emision == 'ELECTRONICA'){
                $ncAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlNotaCredito($nc),'NC');
                $nc->nc_xml_estado = $ncAux->nc_xml_estado;
                $nc->nc_xml_mensaje = $ncAux->nc_xml_mensaje;
                $nc->nc_xml_respuestaSRI = $ncAux->nc_xml_respuestaSRI;
                if($ncAux->nc_xml_estado == 'AUTORIZADO'){
                    $nc->nc_xml_nombre = $ncAux->nc_xml_nombre;
                    $nc->nc_xml_fecha = $ncAux->nc_xml_fecha;
                    $nc->nc_xml_hora = $ncAux->nc_xml_hora;
                }
                $nc->update();
            }
            if($ncAux->nc_xml_estado == 'AUTORIZADO'){
                return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('success','NOTA DE CRÉDITO registrada y autorizada exitosamente')->with('diario',$url)->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('d-m-Y').'/'.$nc->nc_xml_nombre.'.pdf');
            }if($nc->nc_emision == 'ELECTRONICA'){
                return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('success','NOTA DE CRÉDITO registrada exitosamente')->with('diario',$url);
            }else{
                return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('success','NOTA DE CRÉDITO registrada exitosamente')->with('diario',$url)->with('error2','ERROR SRI--> '.$ncAux->nc_xml_estado.' : '.$ncAux->nc_xml_mensaje);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar2(Request $request)
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
            /***************SABER SI SE GENERAR UN ASIENTO DE COSTO****************/
            
            /**********************************************************************/
            /********************cabecera de factura de venta ********************/
            $general = new generalController();
            $docElectronico = new facturacionElectronicaController();
            $nc = new Nota_Credito();
            $cierre = $general->cierre($request->get('nc_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $nc->nc_numero = $request->get('nc_serie').substr(str_repeat(0, 9).$request->get('nc_numero'), - 9);
            $nc->nc_serie = $request->get('nc_serie');
            $nc->nc_secuencial = $request->get('nc_numero');
            $nc->nc_fecha = $request->get('nc_fecha');
            $nc->nc_subtotal = $request->get('idSubtotal');
            $nc->nc_descuento = $request->get('idDescuento');
            $nc->nc_tarifa0 = $request->get('idTarifa0');
            $nc->nc_tarifa12 = $request->get('idTarifa12');
            $nc->nc_iva = $request->get('idIva');
            $nc->nc_total = $request->get('idTotal');
            if($request->get('nc_comentario')){
                $nc->nc_comentario = $request->get('nc_comentario');
            }else{
                $nc->nc_comentario = '';
            }
            $nc->nc_porcentaje_iva = $request->get('idTarifaIva');
            $nc->nc_emision = $request->get('tipoDoc');
            $nc->nc_ambiente = 'PRODUCCIÓN';
            $nc->nc_autorizacion = $docElectronico->generarClaveAcceso($nc->nc_numero,$request->get('nc_fecha'),"04");
            $nc->nc_estado = '1';
            $nc->factura_id = $request->get('factura_id');
            $nc->bodega_id = $request->get('bodega_id');
            $nc->rango_id = $request->get('rango_id');

           
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('nc_fecha'),'CNCE');
                $diario->diario_fecha = $request->get('nc_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE NOTA DE CRÉDITO DE VENTA';
                $diario->diario_tipo_documento = 'NOTA DE CRÉDITO';
                $diario->diario_numero_documento = $nc->nc_numero;
                $diario->diario_beneficiario = $request->get('nombreCliente');
                $diario->diario_tipo = 'CNCE';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DIARIO DE NOTA DE CRÉDITO: '.$nc->nc_numero;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de venta de nota de crédito -> '.$nc->nc_numero,$nc->nc_numero,'Registro de diario de venta de nota de crédito -> '.$nc->nc_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                /*******************************************************************/
                if($nc->nc_comentario == 'DEVOLUCION'){
                    /**********************asiento diario de costo ****************************/
                    $diarioC = new Diario();
                    $diarioC->diario_codigo = $general->generarCodigoDiario($request->get('nc_fecha'),'CCVP');
                    $diarioC->diario_fecha = $request->get('nc_fecha');
                    $diarioC->diario_referencia = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO';
                    $diarioC->diario_tipo_documento = 'NOTA DE CRÉDITO';
                    $diarioC->diario_numero_documento = $nc->nc_numero;
                    $diarioC->diario_beneficiario = $request->get('nombreCliente');
                    $diarioC->diario_tipo = 'CCVP';
                    $diarioC->diario_secuencial = substr($diarioC->diario_codigo, 8);
                    $diarioC->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('m');
                    $diarioC->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('Y');
                    $diarioC->diario_comentario = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO CON NOTA DE CRÉDITO: '.$nc->nc_numero;
                    $diarioC->diario_cierre = '0';
                    $diarioC->diario_estado = '1';
                    $diarioC->empresa_id = Auth::user()->empresa_id;
                    $diarioC->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                    $diarioC->save();
                    $general->registrarAuditoria('Registro de diario de costo de venta de nota de crédito -> '.$nc->nc_numero,$nc->nc_numero,'Registro de diario de costo de venta de nota de crédito -> '.$nc->nc_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diarioC->diario_codigo);
                    /************************************************************************/
                    $nc->diarioCosto()->associate($diarioC);
                }            
                $nc->diario()->associate($diario);
            
            $nc->save();
            
                $general->registrarAuditoria('Registro de nota de crédito de venta numero -> '.$nc->nc_numero,$nc->nc_numero,'Registro de nota de crédito de venta numero -> '.$nc->nc_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$nc->nc_autorizacion.' y con codigo de diario -> '.$diario->diario_codigo);
            
            /*******************************************************************/
            /********************Pago por Nota de Credito***************************/
            $facturaAux = Factura_Venta::Factura($request->get('factura_id'))->first(); 
            $cxcAux = $facturaAux->cuentaCobrar;
            if($cxcAux->cuenta_saldo == 0){
                $rangoDocumentoRetencion=Rango_Documento::PuntoRango($facturaAux->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                if($rangoDocumentoRetencion){
                    $secuencial=$rangoDocumentoRetencion->rango_inicio;
                    $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($facturaAux->rangoDocumento->punto_id)->sucursal_id)->get();
                    foreach($puntosEmision as $punto){
                        $rangoDocumentoRetencion=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Cliente')->first();
                        if($rangoDocumentoRetencion){
                            break;
                        }
                    }
                    if($rangoDocumentoRetencion){
                        $secuencial=$rangoDocumentoRetencion->rango_inicio;
                        $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    }else{
                        return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir anticipos de clientes, configueros y vuelva a intentar');
                    }
                }
                /********************Anticipo por Retencion de Venta***************************/
                $anticipoCliente = new Anticipo_Cliente();
                $anticipoCliente->anticipo_numero = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $anticipoCliente->anticipo_serie = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie;
                $anticipoCliente->anticipo_secuencial = $secuencial;
                $anticipoCliente->anticipo_fecha = $request->get('nc_fecha');
                $anticipoCliente->anticipo_tipo = 'NOTA DE CRÉDITO';      
                $anticipoCliente->anticipo_documento = $nc->nc_numero;      
                $anticipoCliente->anticipo_motivo = $request->get('nc_comentario');
                $anticipoCliente->anticipo_valor = $request->get('idTotal');  
                $anticipoCliente->anticipo_saldo = $request->get('idTotal');   
                $anticipoCliente->cliente_id = $facturaAux->cliente_id;
                $anticipoCliente->rango_id = $rangoDocumentoRetencion->rango_id;
                $anticipoCliente->anticipo_estado = 1; 
                
                    $anticipoCliente->diario()->associate($diario);
                
                $anticipoCliente->save();
                $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$request->get('idCliente'),'0','Con motivo: Nota de Crédito');
                
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $request->get('idTotal');
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE CLIENTE';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                
            }else if($cxcAux->cuenta_saldo >= $nc->nc_total){
                /********************Pago por Nota de Credito***************************/
                $pago = new Pago_CXC();
                $pago->pago_descripcion = $nc->nc_comentario. ' POR NOTA DE CRÉDITO '.' No. '.$nc->nc_numero;
                $pago->pago_fecha = $request->get('nc_fecha');
                $pago->pago_tipo = 'NOTA DE CRÉDITO';
                $pago->pago_valor = $request->get('idTotal');
                $pago->pago_estado = '1';
                
                    $pago->diario()->associate($diario);
                
                $pago->save();

                $general->registrarAuditoria('Registro de pago a Cliente -> '.$request->get('nombreCliente'),'0','Pago de factura No. '.$facturaAux->factura_numero.' con motivo: Nota de Crédito').' No. '.$nc->nc_numero; 

                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = $nc->nc_comentario. ' POR NOTA DE CRÉDITO '.' No. '.$nc->nc_numero; 
                $detallePago->detalle_pago_valor = $request->get('idTotal'); 
                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1; 
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();

                $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$request->get('nombreCliente'),'0','Detalle de pago de factura No. '.$facturaAux->factura_numero.' con motivo: Nota de Crédito').' No. '.$nc->nc_numero; 

                $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($facturaAux->factura_id)->sum('descuento_valor'),2);
                if($cxcAux->cuenta_saldo == 0){
                    $cxcAux->cuenta_estado = '2';
                }else{
                    $cxcAux->cuenta_estado = '1';
                }
                $cxcAux->update();
                /*Inicio de registro de auditoria*/
                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$facturaAux->factura_numero);
                /*Fin de registro de auditoria*/ 
                
                /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $request->get('idTotal');
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                
            }else{
                $rangoDocumentoRetencion=Rango_Documento::PuntoRango($facturaAux->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                if($rangoDocumentoRetencion){
                    $secuencial=$rangoDocumentoRetencion->rango_inicio;
                    $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($facturaAux->rangoDocumento->punto_id)->sucursal_id)->get();
                    foreach($puntosEmision as $punto){
                        $rangoDocumentoRetencion=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Cliente')->first();
                        if($rangoDocumentoRetencion){
                            break;
                        }
                    }
                    if($rangoDocumentoRetencion){
                        $secuencial=$rangoDocumentoRetencion->rango_inicio;
                        $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    }else{
                        return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir anticipos de clientes, configueros y vuelva a intentar');
                    }
                }
                /********************Anticipo por Retencion de Venta***************************/
                $anticipoCliente = new Anticipo_Cliente();
                $anticipoCliente->anticipo_numero = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $anticipoCliente->anticipo_serie = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie;
                $anticipoCliente->anticipo_secuencial = $secuencial;
                $anticipoCliente->anticipo_fecha = $request->get('nc_fecha');
                $anticipoCliente->anticipo_tipo = 'NOTA DE CRÉDITO';      
                $anticipoCliente->anticipo_documento = $nc->nc_numero;      
                $anticipoCliente->anticipo_motivo = $request->get('nc_comentario');
                $anticipoCliente->anticipo_valor = $request->get('idTotal') - $cxcAux->cuenta_saldo;  
                $anticipoCliente->anticipo_saldo = $request->get('idTotal') - $cxcAux->cuenta_saldo;  
                $anticipoCliente->cliente_id = $facturaAux->cliente_id;
                $anticipoCliente->rango_id = $rangoDocumentoRetencion->rango_id;
                $anticipoCliente->anticipo_estado = 1; 
                
                    $anticipoCliente->diario()->associate($diario);
                
                $anticipoCliente->save();
                $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$request->get('nombreCliente'),'0','Con motivo: Nota de Crédito');
                
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $request->get('idTotal')- $cxcAux->cuenta_saldo;  
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE CLIENTE';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                
                /*******************************************************************/
                /********************Pago por Nota de Credito***************************/
                $pago = new Pago_CXC();
                $pago->pago_descripcion = $nc->nc_comentario. ' POR NOTA DE CRÉDITO '.' No. '.$nc->nc_numero;
                $pago->pago_fecha = $request->get('nc_fecha');
                $pago->pago_tipo = 'NOTA DE CRÉDITO';
                $pago->pago_valor = $cxcAux->cuenta_saldo;
                $pago->pago_estado = '1';
                
                    $pago->diario()->associate($diario);
                
                $pago->save();

                $general->registrarAuditoria('Registro de pago a Cliente -> '.$request->get('nombreCliente'),'0','Pago de factura No. '.$facturaAux->factura_numero.' con motivo: Nota de Crédito').' No. '.$nc->nc_numero; 

                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = $nc->nc_comentario. ' POR NOTA DE CRÉDITO '.' No. '.$nc->nc_numero; 
                $detallePago->detalle_pago_valor = $cxcAux->cuenta_saldo;
                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1; 
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();

                $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$request->get('nombreCliente'),'0','Detalle de pago de factura No. '.$facturaAux->factura_numero.' con motivo: Nota de Crédito').' No. '.$nc->nc_numero; 

                $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($facturaAux->factura_id)->sum('descuento_valor'),2);
                if($cxcAux->cuenta_saldo == 0){
                    $cxcAux->cuenta_estado = '2';
                }else{
                    $cxcAux->cuenta_estado = '1';
                }
                $cxcAux->update();
                /*Inicio de registro de auditoria*/
                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$facturaAux->factura_numero);
                /*Fin de registro de auditoria*/ 
               
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $cxcAux->cuenta_saldo;
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                
            }
            /****************************************************************/
            
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                /********************detalle de factura de venta********************/
                $detalleNC = new Detalle_NC();
                $detalleNC->detalle_cantidad = $cantidad[$i];
                $detalleNC->detalle_precio_unitario = $pu[$i];
                $detalleNC->detalle_descuento = $descuento[$i];
                $detalleNC->detalle_iva = $iva[$i];
                $detalleNC->detalle_total = $total[$i];
                $detalleNC->detalle_estado = '1';
                $detalleNC->producto_id = $isProducto[$i];
                    /******************registro de movimiento de producto******************/
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('nc_fecha');
                    if($nc->nc_comentario == 'DESCUENTO'){
                        $movimientoProducto->movimiento_cantidad=0;
                    }else{
                        $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    }
                    $movimientoProducto->movimiento_precio=$pu[$i];
                    $movimientoProducto->movimiento_iva=$iva[$i];
                    $movimientoProducto->movimiento_total=$total[$i];
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='NOTA DE CRÉDITO';
                    $movimientoProducto->movimiento_motivo='VENTA';
                    $movimientoProducto->movimiento_tipo='ENTRADA';
                    $movimientoProducto->movimiento_descripcion='NOTA DE CRÉDITO No. '.$nc->nc_numero.' POR '.$nc->nc_comentario;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$nc->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por nota de crédito de venta numero -> '.$nc->nc_numero,$nc->nc_numero,'Registro de movimiento de producto por nota de crédito de venta numero -> '.$nc->nc_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                $detalleNC->movimiento()->associate($movimientoProducto);
                $nc->detalles()->save($detalleNC);
                $general->registrarAuditoria('Registro de detalle de nota de crédito de venta numero -> '.$nc->nc_numero,$nc->nc_numero,'Registro de detalle de nota de crédito de venta numero -> '.$nc->nc_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                /*******************************************************************/
                
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $total[$i];
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R NOTA DE CRÉDITO DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_venta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$producto->cuentaVenta->cuenta_numero.' en el haber por un valor de -> '.$total[$i]);
                    /*******************************************************************/
                
                if($nc->nc_comentario == 'DEVOLUCION'){
                    if($producto->producto_tipo == '1'){
                        
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_haber = 0.00;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR NOTA DE CRÉDITO DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$detalleDiario->detalle_debe);
                            
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = 0.00;
                            $detalleDiario->detalle_haber = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR NOTA DE CRÉDITO DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $parametrizacionContable = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$detalleDiario->detalle_haber);
                        
                    }
                }
            }
            /********************detalle de diario de venta********************/
            if ($request->get('idIva') > 0){
                
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idIva');
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R IVA POR NOTA DE CRÉDITO';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA VENTAS')->first();
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nc->nc_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$request->get('idIva'));
                
            }
            $url ='';
            /****************************************************************/
            if(Auth::user()->empresa->empresa_llevaContabilidad== '1'){
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            if($nc->nc_emision == 'ELECTRONICA'){
                $ncAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlNotaCredito($nc),'NC');
                $nc->nc_xml_estado = $ncAux->nc_xml_estado;
                $nc->nc_xml_mensaje = $ncAux->nc_xml_mensaje;
                $nc->nc_xml_respuestaSRI = $ncAux->nc_xml_respuestaSRI;
                if($ncAux->nc_xml_estado == 'AUTORIZADO'){
                    $nc->nc_xml_nombre = $ncAux->nc_xml_nombre;
                    $nc->nc_xml_fecha = $ncAux->nc_xml_fecha;
                    $nc->nc_xml_hora = $ncAux->nc_xml_hora;
                }
                $nc->update();
            }
           
            if($ncAux->nc_xml_estado == 'AUTORIZADO'){
                return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('success','NOTA DE CRÉDITO registrada y autorizada exitosamente')->with('diario',$url)->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('nc_fecha'))->format('d-m-Y').'/'.$nc->nc_xml_nombre.'.pdf');
            }if($nc->nc_emision == 'ELECTRONICA'){
                return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('success','NOTA DE CRÉDITO registrada exitosamente')->with('diario',$url);
            }else{
                return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('success','NOTA DE CRÉDITO registrada exitosamente')->with('diario',$url)->with('error2','ERROR SRI--> '.$ncAux->nc_xml_estado.' : '.$ncAux->nc_xml_mensaje);
            }
            
           
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/notaCredito/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $credito=Nota_Credito::findOrFail($id);
            
            $general = new generalController();
           
            $cierre = $general->cierre($credito->nc_fecha,$credito->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('eliminacionComprantes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cxcAu=0;
            foreach ($credito->detalles as $detalles) {
                $detall=Detalle_NC::findOrFail($detalles->detalle_id);
                if (isset($detalles->movimiento)) {
                    $detall->movimiento_id=null;
                    $detall->save();

                    $aux = $detalles->movimiento;
                    $detalles->movimiento->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion de Movimiento de producto por Nota de credito numero: -> '.$credito->nc_numero, $id, 'Con Producto '.$detalles->producto->producto_nombre.' Con la cantidad de producto de '.$aux->movimiemovimiento_totalnto_cantidad.' y total '.$aux->movimiento_total);
                }
            
                $aux = $detalles;
                $detalles->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion de detalles de producto de  Nota de credito numero: -> '.$credito->nc_numero, $id, 'Con Producto '.$aux->producto->producto_nombre.' Con la cantidad de producto de '.$aux->detalle_cantidad.' y total '.$aux->detalle_total);
            }
        
            if (isset($credito->diario->pagocuentaCobrar)) {
                foreach ($credito->diario->pagocuentaCobrar->detalles as $detalle) {
                    $cxcAu=($detalle->cuentaCobrar->cuenta_id);
                    $aux=$detalle;
                    $detalle->delete();
                    $general->registrarAuditoria('Eliminacion del detalle del pago de la cuenta por cobrar  de  Nota de credito  numero: -> '.$credito->nc_numero, $id, ' Descripcion -> '.$aux->detalle_pago_descripcion.' Con el valor-> '.$aux->detalle_pago_valor);
                }


                $aux=$credito->diario->pagocuentaCobrar;
                $credito->diario->pagocuentaCobrar->delete();
                $general->registrarAuditoria('Eliminacion del pago de la cuenta por cobrar por Nota de credito numero: -> '.$credito->nc_numero, $id, 'Tipo '.$aux->cuenta_tipo.' con Descripcion -> '.$aux->cuenta_descripcion.' Con el valor-> '.$aux->cuenta_monto);
            }
            if (isset($credito->diario->anticipo)) {
                $aux=$credito->diario->anticipo;
                $credito->diario->anticipo->delete();
                $general->registrarAuditoria('Eliminacion de la cuenta por aniticpo por Nota de credito numero: -> '.$credito->nc_numero, $id, 'Tipo '.$aux->anticipo_tipo.' Con el valor-> '.$aux->anticipo_valor);
            }
            $cxcAux=Cuenta_Cobrar::findOrFail($cxcAu);
            $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxcAux->facturaVenta->factura_id)->sum('descuento_valor'),2);
            if($cxcAux->cuenta_saldo == 0){
                $cxcAux->cuenta_estado = '2';
            }else{
                $cxcAux->cuenta_estado = '1';
            }
            $cxcAux->update();

            if (isset($credito->diario)) {
                foreach ($credito->diario->detalles as $detalles) {
                    $aux=$detalles;
                    $detalles->delete();
               
                    $general->registrarAuditoria('Eliminacion de detalle de diario por Nota de credito numero: -> '.$credito->nc_numero, $id, 'Con id de diario-> '.$aux->diario_id.'Comentario -> '.$aux->detalle_comentario);
                }

                $ncredito=Nota_Credito::findOrFail($credito->nc_id);
                $ncredito->diario_id=null;
                $ncredito->save();

                $aux = $credito->diario;
                $credito->diario->delete();
                $general->registrarAuditoria('Eliminacion de diario por Nota de credito numero: -> '.$credito->nc_numero, $id, 'Con el diario-> '.$aux->diario_codigo.'Comentario -> '.$aux->diario_comentario);
            }
            if (isset($credito->diarioCosto)) {
                foreach ($credito->diarioCosto->detalles as $detalles) {
                    $aux=$detalles;
                    $detalles->delete();
               
                    $general->registrarAuditoria('Eliminacion de detalle de diario por Nota de credito numero: -> '.$credito->nc_numero, $id, 'Con id de diario-> '.$aux->diario_id.'Comentario -> '.$aux->detalle_comentario);
                }
                $ncredito=Nota_Credito::findOrFail($credito->nc_id);
                $ncredito->diario_costo_id=null;
                $ncredito->save();
        
                $aux = $credito->diarioCosto;
                $credito->diarioCosto->delete();
           
                $general->registrarAuditoria('Eliminacion de diario por Nota de credito numero: -> '.$credito->nc_numero, $id, 'Con el diario-> '.$aux->diario_codigo.'Comentario -> '.$aux->diario_comentario);
            }

            $aux=$credito;
            $credito->delete();
            $general->registrarAuditoria('Eliminacion de la Nota de credito numero: -> '.$credito->nc_numero, $id, 'Con el valor de -> '.$aux->nc_total);

            DB::commit();
            return redirect('eliminacionComprantes')->with('success', 'Datos Eliminados exitosamente');
        
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('eliminacionComprantes')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
       
    }

    public function nuevo($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Nota de crédito')->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Nota_Credito::secuencial($rangoDocumento->rango_id)->max('nc_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                return view('admin.ventas.notasCredito.nuevo',['secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($id)->get(), 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir Notas de crédito, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }
    public function new($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Nota de crédito')->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Nota_Credito::secuencial($rangoDocumento->rango_id)->max('nc_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                return view('admin.ventas.notasCredito.index',['vendedores'=>Vendedor::Vendedores()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'bodegas'=>Bodega::bodegasSucursal($id)->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($id)->get(), 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir Notas de crédito, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }
}
