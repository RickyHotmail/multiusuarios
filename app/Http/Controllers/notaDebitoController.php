<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_Diario;
use App\Models\Detalle_ND;
use App\Models\Detalle_Pago_CXC;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Forma_Pago;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Producto;
use App\Models\Nota_Debito;
use App\Models\Pago_CXC;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tarifa_Iva;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class notaDebitoController extends Controller
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
            /********************cabecera de nota de debito ********************/
            $general = new generalController();
            $docElectronico = new facturacionElectronicaController();
            $nd = new Nota_Debito();
           
            $cierre = $general->cierre($request->get('nd_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/notaDebito/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $nd->nd_numero = $request->get('nd_serie').substr(str_repeat(0, 9).$request->get('nd_numero'), - 9);
            $nd->nd_serie = $request->get('nd_serie');
            $nd->nd_secuencial = $request->get('nd_numero');
            $nd->nd_fecha = $request->get('nd_fecha');
            $nd->nd_tipo_pago = $request->get('nd_tipo_pago');
            $nd->nd_dias_plazo = $request->get('nd_dias_plazo');
            $nd->nd_fecha_pago = date("Y-m-d",strtotime($request->get('nd_fecha')."+ ".$request->get('nd_dias_plazo')." days"));
            $nd->nd_subtotal = $request->get('idSubtotal');
            $nd->nd_descuento = $request->get('idDescuento');
            $nd->nd_tarifa0 = $request->get('idTarifa0');
            $nd->nd_tarifa12 = $request->get('idTarifa12');
            $nd->nd_iva = $request->get('idIva');
            $nd->nd_total = $request->get('idTotal');
            $nd->nd_motivo = $request->get('nd_motivo');
            if($request->get('nd_comentario')){
                $nd->nd_comentario = $request->get('nd_comentario');
            }else{
                $nd->nd_comentario = '';
            }
            $nd->nd_porcentaje_iva = $request->get('nd_porcentaje_iva');
            $nd->nd_emision = $request->get('tipoDoc');
            $nd->nd_ambiente = 'PRODUCCIÓN';
            $nd->nd_autorizacion = $docElectronico->generarClaveAcceso($nd->nd_numero,$request->get('nd_fecha'),"05");
            $nd->nd_estado = '1';
            $nd->forma_pago_id = $request->get('forma_pago_id');
            $nd->factura_id = $request->get('factura_id');
            $nd->rango_id = $request->get('rango_id');
                /********************cuenta por cobrar***************************/
                $cxc = new Cuenta_Cobrar();
                $cxc->cuenta_descripcion = 'NOTA DE DÉBITO No. '.$nd->nd_numero;
                if($request->get('nd_tipo_pago') == 'CREDITO' or $request->get('nd_tipo_pago') == 'CONTADO'){
                    $cxc->cuenta_tipo = $request->get('nd_tipo_pago');
                    $cxc->cuenta_saldo = $request->get('idTotal');
                    $cxc->cuenta_estado = '1';
                }else{
                    $cxc->cuenta_tipo = $request->get('nd_tipo_pago');
                    $cxc->cuenta_saldo = 0.00;
                    $cxc->cuenta_estado = '2';
                }
                $cxc->cuenta_fecha = $request->get('nd_fecha');
                $cxc->cuenta_fecha_inicio = $request->get('nd_fecha');
                $cxc->cuenta_fecha_fin = $nd->nd_fecha_pago;
                $cxc->cuenta_monto = $request->get('idTotal');
                $cxc->cuenta_valor_factura = $request->get('idTotal');
                $cxc->cliente_id = $request->get('clienteID');
                $cxc->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $cxc->save();
                $general->registrarAuditoria('Registro de cuenta por cobrar de nota de debito -> '.$nd->nd_numero,$nd->nd_numero,'Registro de cuenta por cobrar de nota de debito -> '.$nd->nd_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$nd->nd_autorizacion);
                /****************************************************************/
            $nd->cuentaCobrar()->associate($cxc);

                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('nd_fecha'),'CNDE');
                $diario->diario_fecha = $request->get('nd_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE NOTA DE DÉBITO';
                $diario->diario_tipo_documento = 'NOTA DE DÉBITO';
                $diario->diario_numero_documento = $nd->nd_numero;
                $diario->diario_beneficiario = $request->get('nombreCliente');
                $diario->diario_tipo = 'CNDE';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('nd_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('nd_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DIARIO DE NOTA DE DÉBITO: '.$nd->nd_numero;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de venta de nota de debito -> '.$nd->nd_numero,$nd->nd_numero,'Registro de diario de venta de nota de debito -> '.$nd->nd_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                /*******************************************************************/
                $nd->diario()->associate($diario);
          
            if($arqueoCaja){
                $nd->arqueo_id = $arqueoCaja->arqueo_id;
            }
            $nd->save();
           
                $general->registrarAuditoria('Registro de nota de debito de venta numero -> '.$nd->nd_numero, $nd->nd_numero, 'Registro de nota de debito de venta numero -> '.$nd->nd_numero.' con cliente -> '.$request->get('nombreCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$nd->nd_autorizacion.' y con codigo de diario -> '.$diario->diario_codigo);
            
            /*******************************************************************/
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
                /****************************************************************/
            }
            
                /********************detalle de diario de nota de debito********************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('idTotal');
                $detalleDiario->detalle_haber = 0.00 ;
                $detalleDiario->detalle_tipo_documento = 'NOTA DE DÉBITO';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($request->get('nd_tipo_pago') == 'CREDITO' OR $request->get('nd_tipo_pago') == 'CONTADO'){
                    $facturaAux = Factura_Venta::Factura($request->get('factura_id'))->first(); 
                    $detalleDiario->cliente_id = $facturaAux->cliente_id;
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($facturaAux->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                    }
                }else{
                    $detalleDiario->detalle_comentario = 'P/R NOTA DEBITO EN EFECTIVO';
                    $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                    $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nd->nd_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                /*******************************************************************/
            
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                /********************detalle de nota de debito********************/
                $detalleND = new Detalle_ND();
                $detalleND->detalle_cantidad = $cantidad[$i];
                $detalleND->detalle_precio_unitario = $pu[$i];
                $detalleND->detalle_descuento = $descuento[$i];
                $detalleND->detalle_iva = $iva[$i];
                $detalleND->detalle_total = $total[$i];
                $detalleND->detalle_estado = '1';
                $detalleND->producto_id = $isProducto[$i];
                    /******************registro de movimiento de producto******************/
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('nd_fecha');
                    $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    $movimientoProducto->movimiento_precio=$pu[$i];
                    $movimientoProducto->movimiento_iva=$iva[$i];
                    $movimientoProducto->movimiento_total=$total[$i];
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='NOTA DE DÉBITO';
                    $movimientoProducto->movimiento_motivo='VENTA';
                    $movimientoProducto->movimiento_tipo='SALIDA';
                    $movimientoProducto->movimiento_descripcion='NOTA DE DÉBITO No. '.$nd->nd_numero.' POR '.$nd->nd_motivo;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$nd->factura->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por nota de debito de venta numero -> '.$nd->nd_numero,$nd->nd_numero,'Registro de movimiento de producto por nota de debito de venta numero -> '.$nd->nd_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                $detalleND->movimiento()->associate($movimientoProducto);
                $nd->detalles()->save($detalleND);
                $general->registrarAuditoria('Registro de detalle de nota de debito de venta numero -> '.$nd->nd_numero,$nd->nd_numero,'Registro de detalle de nota de debito de venta numero -> '.$nd->nd_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                /*******************************************************************/
               
                    /********************detalle de diario de nota de debito********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $total[$i];
                    $detalleDiario->detalle_comentario = 'P/R NOTA DE DÉBITO DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE DÉBITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'VENTAS')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{

                        $detalleDiario->cuenta_id = $producto->producto_cuenta_venta;
                    }
 
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nd->nd_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$total[$i]);
                    /*******************************************************************/
                
            }
            /********************detalle de diario de nota de debito********************/
            if ($request->get('idIva') > 0){
              
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $request->get('idIva') ;
                    $detalleDiario->detalle_comentario = 'P/R IVA POR NOTA DE DÉBITO';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE DÉBITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA VENTAS')->first();
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$nd->nd_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$request->get('idIva'));
                
            }
            if($request->get('nd_tipo_pago') == 'EN EFECTIVO'){
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="ENTRADA";
                $movimientoCaja->movimiento_descripcion= 'P/R NOTA DE DEBITO DE CLIENTE :'.$request->get('nombreCliente');
                $movimientoCaja->movimiento_valor= $request->get('idTotal');
                $movimientoCaja->movimiento_documento="NOTA DE DEBITO";
                $movimientoCaja->movimiento_numero_documento= $nd->nd_numero;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                
                    $movimientoCaja->diario()->associate($diario);
                
                $movimientoCaja->save();
                /*********************************************************************/
            }
            /****************************************************************/
            $url = '';
            if(Auth::user()->empresa->empresa_llevaContabilidad== '1'){
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            if($nd->nd_emision == 'ELECTRONICA'){
                $ndAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlNotaDebito($nd),'ND');
                $nd->nd_xml_estado = $ndAux->nd_xml_estado;
                $nd->nd_xml_mensaje = $ndAux->nd_xml_mensaje;
                $nd->nd_xml_respuestaSRI = $ndAux->nd_xml_respuestaSRI;
                if($ndAux->nd_xml_estado == 'AUTORIZADO'){
                    $nd->nd_xml_nombre = $ndAux->nd_xml_nombre;
                    $nd->nd_xml_fecha = $ndAux->nd_xml_fecha;
                    $nd->nd_xml_hora = $ndAux->nd_xml_hora;
                }
                $nd->update();
            }
            if($ndAux->nd_xml_estado == 'AUTORIZADO'){
                return redirect('/notaDebito/new/'.$request->get('punto_id'))->with('success','NOTA DE DÉBITO registrada y autorizada exitosamente')->with('diario',$url)->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('nd_fecha'))->format('d-m-Y').'/'.$nd->nd_xml_nombre.'.pdf');
            }elseif($nd->nd_emision == 'ELECTRONICA'){
                return redirect('/notaDebito/new/'.$request->get('punto_id'))->with('success','NOTA DE DÉBITO registrada exitosamente')->with('diario',$url);
            }else{
                return redirect('/notaDebito/new/'.$request->get('punto_id'))->with('success','NOTA DE DÉBITO registrada exitosamente')->with('diario',$url)->with('error2','ERROR SRI--> '.$ndAux->nd_xml_estado.' : '.$ndAux->nd_xml_mensaje);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/notaDebito/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $debito=Nota_Debito::findOrFail($id);

            $general = new generalController();
            $cierre = $general->cierre($debito->nd_fecha,$debito->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('eliminacionComprantes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach($debito->detalles as $detalles){

                $detall=Detalle_ND::findOrFail($detalles->detalle_id);
                $detall->movimiento_id=null;
                $detall->save();
                if (isset($detalles->movimiento)) {
                    $aux = $detalles->movimiento;
                    $detalles->movimiento->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion de Movimiento de producto por factura de venta numero: -> '.$debito->nd_numero, $id, 'Con Producto '.$detalles->producto->producto_nombre.' Con la cantidad de producto de '.$aux->movimiemovimiento_totalnto_cantidad.' y total '.$aux->movimiento_total);
                }
                
                $aux = $detalles; 
                $detalles->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion de detalles de producto por factura de venta numero: -> '.$debito->nd_numero, $id, 'Con Producto '.$aux->producto->producto_nombre.' Con la cantidad de producto de '.$aux->detalle_cantidad.' y total '.$aux->detalle_total);


            }
            $ndebito=Nota_Debito::findOrFail($debito->nd_id);
            $ndebito->cuenta_id=null;
            $ndebito->save();
            
            if (isset($debito->cuentaCobrar)) {
                if (isset($debito->cuentaCobrar->detallepago)) {
                    foreach ($debito->cuentaCobrar->detallepago as $detalle) {
                        $cuenta=Detalle_Pago_CXC::findOrFail($detalle->detalle_pago_id);
                        $cuenta->pago_id=null;
                        $cuenta->cuenta_id=null;
                        $cuenta->save();
                

                        $aux=$detalle->pagoCXC;
                        $detalle->pagoCXC->delete();
                        $general->registrarAuditoria('Eliminacion del detalle del pago de la cuenta por cobrar por factura de venta numero: -> '.$debito->nd_numero, $id, ' Descripcion -> '.$aux->detalle_pago_descripcion.' Con el valor-> '.$aux->detalle_pago_valor);

                
                        $aux=$detalle;
                        $detalle->delete();
                        $general->registrarAuditoria('Eliminacion de la cuenta por cobrar  por factura de venta numero: -> '.$debito->nd_numero, $id, 'Tipo '.$aux->cuenta_tipo.' con Descripcion -> '.$aux->cuenta_descripcion.' Con el valor-> '.$aux->cuenta_monto);
                    }
                }
                $aux=$debito->cuentaCobrar;
                $debito->cuentaCobrar->delete();
 
                $general->registrarAuditoria('Eliminacion de la cuenta por cobrar por factura de venta numero: -> '.$debito->nd_numero, $id, ' Descripcion -> '.$aux->cuenta_descripcion.' Con el monto-> '.$aux->cuenta_monto);
            }
            if (isset($debito->diario)) {
                foreach ($debito->diario->detalles as $detalles) {
                    $aux=$detalles;
                    $detalles->delete();
                 
                    $general->registrarAuditoria('Eliminacion de detalle de diario por factura de venta numero: -> '.$debito->nd_numero, $id, 'Con id de diario-> '.$aux->diario_id.'Comentario -> '.$aux->detalle_comentario);
                }
                $ndebito=Nota_Debito::findOrFail($debito->nd_id);
                $ndebito->diario_id=null;
                $ndebito->save();

                $debito->diario->delete();
                
                $general->registrarAuditoria('Eliminacion de diario por factura de venta numero: -> '.$debito->nd_numero, $id, 'Con el diario-> '.$aux->diario_codigo.'Comentario -> '.$aux->diario_comentario);
            }
            if(isset($debito->diarioCosto)){
                foreach ($debito->diarioCosto->detalles as $detalles) {
                    $aux=$detalles;
                    $detalles->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion de detalle de diario por factura de venta numero: -> '.$debito->nd_numero, $id,  'Con id de diario-> '.$aux->diario_id.'Comentario -> '.$aux->detalle_comentario  );
                }
                $ndebito=Nota_Debito::findOrFail($debito->nd_id);
                $ndebito->diario_costo_id=null;
                $ndebito->save();
            
                $aux = $debito->diarioCosto;
                $debito->diarioCosto->delete();                        
              
                $general->registrarAuditoria('Eliminacion de diario por factura de venta numero: -> '.$debito->nd_numero, $id, 'Con el diario-> '.$aux->diario_codigo.'Comentario -> '.$aux->diario_comentario);
            } 

            $aux=$debito;
            $debito->delete();                        
           
            $general->registrarAuditoria('Eliminacion de la factura de venta numero: -> '.$debito->nd_numero, $id, 'Con el valor de -> '.$aux->nd_total);

            DB::commit();
            return redirect('eliminacionComprantes')->with('success','Datos Eliminados exitosamente');
                        
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
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Nota de débito')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();

            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Nota_Debito::secuencial($rangoDocumento->rango_id)->max('nd_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                return view('admin.ventas.notasDebito.nuevo',['tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($id)->get(),'formasPago'=>Forma_Pago::formaPagos()->get(),'cajaAbierta'=>$cajaAbierta, 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir Notas de débito, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
}
