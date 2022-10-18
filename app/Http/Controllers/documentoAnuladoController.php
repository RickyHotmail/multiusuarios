<?php

namespace App\Http\Controllers;

use App\Models\Anticipo_Cliente;
use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Concepto_Retencion;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_FV;
use App\Models\Detalle_NC;
use App\Models\Detalle_ND;
use App\Models\Diario;
use App\Models\Documento_Anulado;
use App\Models\Factura_Venta;
use App\Models\Liquidacion_Compra;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Producto;
use App\Models\Nota_Credito;
use App\Models\Nota_Debito;
use App\Models\Pago_CXC;
use App\Models\Prefactura_Venta;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Retencion_Compra;
use App\Models\Tipo_Comprobante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class documentoAnuladoController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.ventas.anularDocumento.nuevo',['bodegas'=>Bodega::Bodegas()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function buscarDocumento(Request $request)
    {
        switch ($request->get('tipoDocumento')) {
            case '1':
                return Factura_Venta::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
            case '2':
                return Nota_Credito::NotaCreditoNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
            case '3':
                return Nota_Debito::NotaDebitoNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
            case '4':
               // return Retencion_Compra::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
            case '5':
               // return Liquidacion_Compra::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
            case '6':
                //return Factura_Venta::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
        }
    }
    public function anular(Request $request){
        try{            
            DB::beginTransaction();
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $general = new generalController();
            switch ($request->get('tipo_documento')) {
                case '1':
                    $factura = Factura_Venta::factura($request->get('doc_id'))->first();
                    $cierre = $general->cierre($factura->factura_fecha,Rango_Documento::rango($factura->rango_id)->first()->puntoEmision->sucursal_id);       
                    if($cierre){
                        return redirect('anularDocumento')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    if(isset($factura->notaDebito->nd_id)){
                        return redirect('anularDocumento')->with('error2','El documento no se puede anular porque tiene notas de debito.');
                    }
                    if(isset($factura->notacredito->nc_id)){
                        return redirect('anularDocumento')->with('error2','El documento no se puede anular porque tiene notas de credito.');
                    }
                    if(is_null($factura)){
                        return redirect('anularDocumento')->with('error2','ERROR -> NO SE ENCONTRO NINGUN DOCUMENTO PARA ANULAR.');
                    }
                    if(is_null($factura->retencion) == false){
                        return redirect('anularDocumento')->with('error2','ERROR -> NO SE PUEDE ANULAR LA FACTURA DE VENTA : La factura tiene registrada una retencion.');
                    }
                    if($factura->cuentaCobrar->cuenta_tipo !='EN EFECTIVO'){
                        if($factura->cuentaCobrar->cuenta_saldo < $factura->cuentaCobrar->cuenta_monto){
                            return redirect('anularDocumento')->with('error2','ERROR -> NO SE PUEDE ANULAR LA FACTURA DE VENTA : La factura tiene pagos.');
                        } 
                        if(Cuenta_Cobrar::CuentaCobrarPagos($factura->cuentaCobrar->cuenta_id)->sum('detalle_pago_valor') > 0){
                            return redirect('anularDocumento')->with('error2','ERROR -> NO SE PUEDE ANULAR LA FACTURA DE VENTA : La factura tiene pagos.');
                        }
                    }
                    $jo=false;
                    if($factura->cuentaCobrar->cuenta_tipo =='EN EFECTIVO'){
                        
                        $cajaAbierta=Arqueo_Caja::ArqueoCajaxid($factura->arqueo_id)->first();
                       
                        if(isset($cajaAbierta->arqueo_id)){
                            $cierre = $general->cierre($cajaAbierta->arqueo_fecha,$cajaAbierta->caja->sucursal_id);
                            if($cierre){
                                return redirect('anularDocumento')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                            }
                            $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($factura->arqueo_id, $factura->diario_id)->first();
                            $movimientoCaja->delete();
                            $jo=true;
                        }else{ 
                            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                            if ($cajaAbierta){
                                /**********************movimiento caja****************************/
                                $movimientoCaja = new Movimiento_Caja();          
                                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                $movimientoCaja->movimiento_hora=date("H:i:s");
                                $movimientoCaja->movimiento_tipo="SALIDA";
                                $movimientoCaja->movimiento_descripcion= 'P/R ANULACION DE FACTURA '.$factura->factura_numero.' DE CLIENTE :'.$factura->cliente->cliente_nombre;
                                $movimientoCaja->movimiento_valor= $factura->factura_total;
                                $movimientoCaja->movimiento_documento="P/R ANULACION DE FACTURA EN EFECTIVO";
                                $movimientoCaja->movimiento_numero_documento= $factura->factura_numero;
                                $movimientoCaja->movimiento_estado = 1;
                                $movimientoCaja->arqueo_id = $cajaAbierta->arqueo_id;                                
                                $movimientoCaja->save();
                                
                                $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($factura->arqueo_id,$factura->diario_id)->first();
                                $movimientoAnterior->diario_id = null;
                                $movimientoAnterior->update();

                                $jo=true;
                            /*********************************************************************/                               
                            }else{
                                $noTienecaja = 'Lo valores en Efectivo no pudieron ser eliminados, porque no dispone de CAJA ABIERTA';                               
                            }
                        }   
                        if($jo){
                            $cuentaCXC = $factura->cuentaCobrar;
                            foreach($cuentaCXC->detallepago as $detalleP){
                                $pago =$detalleP->pagoCXC;
                                $detalleP->delete();
                                $pago->delete();                                
                            }
                        }
                    }else{
                        $jo=true;
                    }
                    if($jo){
                        /*Eliminar detalle de diario*/
                        $cierre = $general->cierre($factura->factura_fecha,Rango_Documento::rango($factura->rango_id)->first()->puntoEmision->sucursal_id);     
                        if($cierre){
                            return redirect('anularDocumento')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                        }
                        $cierre = $general->cierre($request->get('fecha_anulacion'),Rango_Documento::rango($factura->rango_id)->first()->puntoEmision->sucursal_id);          
                        if($cierre){
                            return redirect('anularDocumento')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                        }
                        /*activacion de guia de remision*/
                        foreach($factura->guias as $guia){
                            $guia->factura_id = null;
                            $guia->gr_estado = '1';
                            $guia->update();
                        } 
                        if(Schema::hasTable('prefactura_venta')){
                            if(isset($factura->prefactura)){
                                $prefactu=Prefactura_Venta::findOrFail($factura->prefactura->prefactura_id);
                                $prefactu->factura_id = null;
                                $prefactu->prefactura_estado = '1';
                                $prefactu->update();
                            } 
                        }
                        if(isset($factura->ordenDespacho->orden_id)){
                            $orden = $factura->ordenDespacho;
                            $orden->orden_estado = '1';
                            $orden->factura_id = null;
                            $orden->update();
                        }  
                        /*Eliminar cabecera de diario*/
                        if(isset($factura->diarioCosto->diario_id)){
                            $diarioCosto = $factura->diarioCosto;
                            foreach ($factura->diarioCosto->detalles as $detalle) {
                                $detalle->delete();
                            }
                            $general->registrarAuditoria('Eliminación de detalle de diario de costo ',$request->get('buscarFactura'),'Eliminación de detalle de diario de costo por anulación de factura No. '.$request->get('buscarFactura'));
                            $factura->diario_costo_id = null;
                            $factura->update();
                            $diarioCosto->delete();
                            $general->registrarAuditoria('Eliminación de cabecera de diario de costo ',$request->get('buscarFactura'),'Eliminación de cabecera de diario de costo por anulación de factura No. '.$request->get('buscarFactura'));
                        }
                        foreach ($factura->diario->detalles as $detalle) {
                            $detalle->delete();
                        }
                        $general->registrarAuditoria('Eliminación de detalle de diario',$request->get('buscarFactura'),'Eliminación de detalle de diario por anulación de factura No. '.$request->get('buscarFactura'));
                        $diario = $factura->diario;
                        $factura->diario_id = null;
                        $factura->update();
                        $diario->delete();
                        $general->registrarAuditoria('Eliminación de cabecera de diario ',$request->get('buscarFactura'),'Eliminación de cabecera de diario por anulación de factura No. '.$request->get('buscarFactura'));
                        /*Eliminar cuenta por cobrar*/
                        $cuenta = $factura->cuentaCobrar;
                        $factura->cuenta_id = null;
                       
                        $factura->update();
                        $cuenta->delete();
                        $general->registrarAuditoria('Eliminación de cuenta por cobrar',$request->get('buscarFactura'),'Eliminación de cuenta por cobrar por anulación de factura No. '.$request->get('buscarFactura'));
                        /******************Registrar anulacion de documento********************/ 
                        $docAnulado = new Documento_Anulado();
                        $docAnulado->documento_anulado_fecha = $request->get('fecha_anulacion');
                        $docAnulado->documento_anulado_motivo = $request->get('motivo_anulacion');
                        $docAnulado->documento_anulado_estado = '1';
                        $docAnulado->empresa_id = Auth::user()->empresa_id;
                        $docAnulado->save();
                        $general->registrarAuditoria('Registro de documento anulado',$request->get('buscarFactura'),'Registro de documento anulado -> factura No. '.$request->get('buscarFactura'));
                        /*********************************************************************/
                        /******************Actualizar factura de venta********************/ 
                        $factura->documentoAnulado()->associate($docAnulado);
                        $factura->factura_estado = '2';
                        $factura->arqueo_id = null;
                        $factura->update();
                        $general->registrarAuditoria('Actualización de factura de venta No. '.$request->get('buscarFactura'),$request->get('buscarFactura'),'Actualización de factura de venta No. '.$request->get('buscarFactura').' por motivo de anulación');
                        /*********************************************************************/
                        for ($i = 1; $i < count($cantidad); ++$i){
                            /******************registro de movimiento de producto******************/
                            $movimientoProducto = new Movimiento_Producto();
                            $movimientoProducto->movimiento_fecha=$request->get('fecha_anulacion');
                            $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                            $movimientoProducto->movimiento_precio=$pu[$i];
                            $movimientoProducto->movimiento_iva=$iva[$i];
                            $movimientoProducto->movimiento_total=$total[$i];
                            $movimientoProducto->movimiento_stock_actual=0;
                            $movimientoProducto->movimiento_costo_promedio=0;
                            $movimientoProducto->movimiento_documento='FACTURA DE VENTA';
                            $movimientoProducto->movimiento_motivo='ANULACION';
                            $movimientoProducto->movimiento_tipo='ENTRADA';
                            $movimientoProducto->movimiento_descripcion='ANULACIÓN DE FACTURA DE VENTA No. '.$request->get('buscarFactura');
                            $movimientoProducto->movimiento_estado='1';
                            $movimientoProducto->producto_id=$isProducto[$i];
                            $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                            $movimientoProducto->bodega_id=$factura->bodega_id;
                            $movimientoProducto->save();
                            $general->registrarAuditoria('Registro de movimiento de producto por anulación de factura de venta numero -> '.$request->get('buscarFactura'),$request->get('buscarFactura'),'Registro de movimiento de producto por anulación de factura de venta numero -> '.$request->get('buscarFactura').' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                            /*********************************************************************/
                        }
                    }
                    break;
                case '2':
                    $nc = Nota_Credito::NotaCredito($request->get('doc_id'))->first();
                    if(is_null($nc)){
                        return redirect('anularDocumento')->with('error2','ERROR -> NO SE ENCONTRO NINGUN DOCUMENTO PARA ANULAR.');
                    }
                    $cierre = $general->cierre($nc->nc_fecha,Rango_Documento::rango($nc->rango_id)->first()->puntoEmision->sucursal_id);          
                    if($cierre){
                        return redirect('anularDocumento')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    $cierre = $general->cierre($request->get('fecha_anulacion'),Rango_Documento::rango($nc->rango_id)->first()->puntoEmision->sucursal_id);          
                    if($cierre){
                        return redirect('anularDocumento')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    $anticipoNC  = Anticipo_Cliente::AnticipoClienteDiario($nc->diario_id)->first();                   
                    if($anticipoNC){
                        if($anticipoNC->anticipo_saldo < $anticipoNC->anticipo_valor){
                            return redirect('anularDocumento')->with('error2','ERROR -> NO SE PUEDE ANULAR LA NOTA DE CRÉDITO : La nota de crédito genero un anticipo a un cliente, y el anticipo esta cruzado con una o varias facturas.');
                        }
                        /*Eliminar anticipo de cliente*/
                        $anticipoNC->delete();
                        $general->registrarAuditoria('Eliminación de anticipo de cliente',$request->get('buscarFactura'),'Eliminación de anticipo de cliente generado por nota de crédito No. '.$request->get('buscarFactura'));
                    }
                    $pagoCXC = Pago_CXC::PagoDiario($nc->diario_id)->first();
                    if($pagoCXC){
                        /*Eliminar detalle de pago*/
                        foreach ($pagoCXC->detalles as $detalle) {
                            $detalle->delete();
                        }
                        $general->registrarAuditoria('Eliminación de detalle de pago',$request->get('buscarFactura'),'Eliminación de detalle de pago generado por nota de crédito No. '.$request->get('buscarFactura'));
                        /*Eliminar cabecera de pago*/
                        $pagoCXC->delete();
                        $general->registrarAuditoria('Eliminación de cabecera de pago',$request->get('buscarFactura'),'Eliminación de cabecera de pago generado por nota de crédito No. '.$request->get('buscarFactura'));
                    }
                    /*Eliminar detalle de diario*/
                    foreach ($nc->diario->detalles as $detalle) {
                        $detalle->delete();
                    }
                    $general->registrarAuditoria('Eliminación de detalle de diario',$request->get('buscarFactura'),'Eliminación de detalle de diario por anulación de nota de crédito No. '.$request->get('buscarFactura'));
                    /*Eliminar cabecera de diario*/
                    $diario = $nc->diario;
                    $nc->diario_id = null;
                    $nc->update();
                    $diario->delete();
                    $general->registrarAuditoria('Eliminación de cabecera de diario',$request->get('buscarFactura'),'Eliminación de cabecera de diario por anulación de nota de crédito No. '.$request->get('buscarFactura'));
                    /******************Registrar anulacion de documento********************/ 
                    $docAnulado = new Documento_Anulado();
                    $docAnulado->documento_anulado_fecha = $request->get('fecha_anulacion');
                    $docAnulado->documento_anulado_motivo = $request->get('motivo_anulacion');
                    $docAnulado->documento_anulado_estado = '1';
                    $docAnulado->empresa_id = Auth::user()->empresa_id;
                    $docAnulado->save();
                    $general->registrarAuditoria('Registro de documento anulado',$request->get('buscarFactura'),'Registro de documento anulado -> nota de crédito No. '.$request->get('buscarFactura'));
                    /*********************************************************************/
                    /**********************Actualizar nota de credito********************/ 
                    $nc->documentoAnulado()->associate($docAnulado);
                    $nc->nc_estado = '2';
                    $nc->update();
                    $general->registrarAuditoria('Actualización de nota de crédito No. '.$request->get('buscarFactura'),$request->get('buscarFactura'),'Actualización de nota de crédito No. '.$request->get('buscarFactura').' por motivo de anulación');
                    /*********************************************************************/
                    /*****Actualizar saldo de cuenta por cobrar de factura afectada*******/ 
                    $cuentaCobrarFactura =  $nc->factura->cuentaCobrar;
                    $cuentaCobrarFactura->cuenta_saldo = $cuentaCobrarFactura->cuenta_monto-round(Cuenta_Cobrar::CuentaCobrarPagos($cuentaCobrarFactura->cuenta_id)->sum('detalle_pago_valor'),2);
                    if($cuentaCobrarFactura->cuenta_saldo == 0){
                        $cuentaCobrarFactura->cuenta_estado = '2';
                    }
                    $cuentaCobrarFactura->update();
                    /*********************************************************************/
                    for ($i = 1; $i < count($cantidad); ++$i){
                        /******************registro de movimiento de producto******************/
                        $movimientoProducto = new Movimiento_Producto();
                        $movimientoProducto->movimiento_fecha=$request->get('fecha_anulacion');
                        $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                        $movimientoProducto->movimiento_precio=$pu[$i];
                        $movimientoProducto->movimiento_iva=$iva[$i];
                        $movimientoProducto->movimiento_total=$total[$i];
                        $movimientoProducto->movimiento_stock_actual=0;
                        $movimientoProducto->movimiento_costo_promedio=0;
                        $movimientoProducto->movimiento_documento='NOTA DE CRÉDITO';
                        $movimientoProducto->movimiento_motivo='ANULACION';
                        $movimientoProducto->movimiento_tipo='SALIDA';
                        $movimientoProducto->movimiento_descripcion='ANULACIÓN DE NOTA DE CRÉDITO No. '.$request->get('buscarFactura');
                        $movimientoProducto->movimiento_estado='1';
                        $movimientoProducto->producto_id=$isProducto[$i];
                        $movimientoProducto->bodega_id=$nc->factura->bodega_id;
                        $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                        $movimientoProducto->save();
                        $general->registrarAuditoria('Registro de movimiento de producto por anulación de nota de crédito numero -> '.$request->get('buscarFactura'),$request->get('buscarFactura'),'Registro de movimiento de producto por anulación de nota de crédito numero -> '.$request->get('buscarFactura').' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                        /*********************************************************************/
                    }
                    break;
                case '3':
                    $nd = Nota_Debito::NotaDebito($request->get('doc_id'))->first();
                    if(is_null($nd)){
                        return redirect('anularDocumento')->with('error2','ERROR -> NO SE ENCONTRO NINGUN DOCUMENTO PARA ANULAR.');
                    }
                    if(is_null($nd->retencion) == false){
                        return redirect('anularDocumento')->with('error2','ERROR -> NO SE PUEDE ANULAR LA NOTA DE DÉBITO : La nota de débito tiene registrada una retencion.');
                    }
                    if($nd->cuentaCobrar->cuenta_saldo < $nd->cuentaCobrar->cuenta_monto){
                        return redirect('anularDocumento')->with('error','ERROR -> NO SE PUEDE ANULAR LA NOTA DE DÉBITO : La nota de débito tiene pagos.');
                    }
                    if(Cuenta_Cobrar::CuentaCobrarPagos($nd->cuentaCobrar->cuenta_id)->sum('detalle_pago_valor') > 0){
                        return redirect('anularDocumento')->with('error2','ERROR -> NO SE PUEDE ANULAR LA NOTA DE DÉBITO : La nota de débito tiene pagos.');
                    }

                    $cierre = $general->cierre($nd->nd_fecha_pago,Rango_Documento::rango($nd->rango_id)->first()->puntoEmision->sucursal_id);          
                    if($cierre){
                        return redirect('anularDocumento')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    $cierre = $general->cierre($request->get('fecha_anulacion'),Rango_Documento::rango($nd->rango_id)->first()->puntoEmision->sucursal_id);          
                    if($cierre){
                        return redirect('anularDocumento')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    /*Eliminar detalle de diario*/
                    foreach ($nd->diario->detalles as $detalle) {
                        $detalle->delete();
                    }
                    //return redirect('anularDocumento')->with('error2','ERROR -> kkk');
                    $general->registrarAuditoria('Eliminación de detalle de diario',$request->get('buscarFactura'),'Eliminación de detalle de diario por anulación de nota de débito No. '.$request->get('buscarFactura'));
                    /*Eliminar cabecera de diario*/
                    $diario = $nd->diario;
                    $nd->diario_id = null;
                    $nd->update();
                    $diario->delete();
                    $general->registrarAuditoria('Eliminación de cabecera de diario',$request->get('buscarFactura'),'Eliminación de cabecera de diario por anulación de nota de débito No. '.$request->get('buscarFactura'));
                    /*Eliminar cuenta por cobrar*/
                    $cuenta = $nd->cuentaCobrar;
                    $nd->cuenta_id = null;
                    $nd->update();
                    $cuenta->delete();
                    $general->registrarAuditoria('Eliminación de cuenta por cobrar',$request->get('buscarFactura'),'Eliminación de cuenta por cobrar por anulación de nota de débito No. '.$request->get('buscarFactura'));
                    /******************Registrar anulacion de documento********************/ 
                    $docAnulado = new Documento_Anulado();
                    $docAnulado->documento_anulado_fecha = $request->get('fecha_anulacion');
                    $docAnulado->documento_anulado_motivo = $request->get('motivo_anulacion');
                    $docAnulado->documento_anulado_estado = '1';
                    $docAnulado->empresa_id = Auth::user()->empresa_id;
                    $docAnulado->save();
                    $general->registrarAuditoria('Registro de documento anulado',$request->get('buscarFactura'),'Registro de documento anulado -> nota de débito No. '.$request->get('buscarFactura'));
                    /*********************************************************************/
                    /******************Actualizar factura de venta********************/ 
                    $nd->documentoAnulado()->associate($docAnulado);
                    $nd->nd_estado = '2';
                    $nd->update();
                    $general->registrarAuditoria('Actualización de nota de débito No. '.$request->get('buscarFactura'),$request->get('buscarFactura'),'Actualización de nota de débito No. '.$request->get('buscarFactura').' por motivo de anulación');
                    /*********************************************************************/
                    for ($i = 1; $i < count($cantidad); ++$i){
                        /******************registro de movimiento de producto******************/
                        $movimientoProducto = new Movimiento_Producto();
                        $movimientoProducto->movimiento_fecha=$request->get('fecha_anulacion');
                        $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                        $movimientoProducto->movimiento_precio=$pu[$i];
                        $movimientoProducto->movimiento_iva=$iva[$i];
                        $movimientoProducto->movimiento_total=$total[$i];
                        $movimientoProducto->movimiento_stock_actual=0;
                        $movimientoProducto->movimiento_costo_promedio=0;
                        $movimientoProducto->movimiento_documento='NOTA DE DÉBITO';
                        $movimientoProducto->movimiento_motivo='ANULACION';
                        $movimientoProducto->movimiento_tipo='ENTRADA';
                        $movimientoProducto->movimiento_descripcion='ANULACIÓN DE NOTA DE DÉBITO No. '.$request->get('buscarFactura');
                        $movimientoProducto->movimiento_estado='1';
                        $movimientoProducto->producto_id=$isProducto[$i];
                        $movimientoProducto->bodega_id=$nd->factura->bodega_id;
                        $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                        $movimientoProducto->save();
                        $general->registrarAuditoria('Registro de movimiento de producto por anulación de nota de débito numero -> '.$request->get('buscarFactura'),$request->get('buscarFactura'),'Registro de movimiento de producto por anulación de nota de débito numero -> '.$request->get('buscarFactura').' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                        /*********************************************************************/
                    }
                    break;
                case '4':
                   // return Retencion_Compra::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                    break;
                case '5':
                   // return Liquidacion_Compra::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                    break;
                case '6':
                    //return Factura_Venta::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                    break;
            }
            DB::commit();
            return redirect('anularDocumento')->with('success','Documento anulado exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('anularDocumento')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function detalleDocumento(Request $request){
        switch ($request->get('tipoDocumento')) {
            case '1':
                return Detalle_FV::DetalleFactura($request->get('documento_id'))->get();
                break;
            case '2':
                return Detalle_NC::DetalleNotaCredito($request->get('documento_id'))->get();
                break;
            case '3':
                return Detalle_ND::DetalleNotaDebito($request->get('documento_id'))->get();
                break;
            case '4':
               // return Retencion_Compra::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
            case '5':
               // return Liquidacion_Compra::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
            case '6':
                //return Factura_Venta::FacturaNumero($request->get('buscar'),$request->get('bodega'))->get();
                break;
        }
    }
}
