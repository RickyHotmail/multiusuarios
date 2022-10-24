<?php

namespace App\Http\Controllers;

use App\Models\Anticipo_Cliente;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Cuenta_Cobrar;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Factura_Venta;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class descontarAnticipoClienteController extends Controller
{
    public function nuevo(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasCobrar.descontarAnticipo.index',['bodegas'=>Bodega::Bodegas()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function descontar(Request $request){
        if($request->get('tipoDocumento') == 'factura'){
            return $this->descontarFactura($request);
        }else{
            return $this->descontarCXC($request);
        }
    }
    public function descontarCXC(Request $request){
        try{            
            DB::beginTransaction();
            $general = new generalController();
            $cxcAux = Cuenta_Cobrar::Cuenta($request->get('factura_id'))->first();
            $cierre = $general->cierre($request->get('fechaCruce'),$cxcAux->sucursal_id);          
            if($cierre){
                return redirect('descontarAntCli')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            } 
            $totalAnt = 0;
            /**********************asiento diario****************************/
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaCruce'),'CDAC');
            $diario->diario_fecha = $request->get('fechaCruce');
            $diario->diario_referencia = 'COMPROBANTE DE DESCUENTO ANTICIPO DE CLIENTE';
            $diario->diario_tipo_documento = 'DESCUENTO DE ANTICIPO';
            $diario->diario_numero_documento = substr($cxcAux->cuenta_descripcion, 38);
            $diario->diario_beneficiario = $request->get('nombreCliente');
            $diario->diario_tipo = 'CDAC';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE DESCUENTO ANTICIPO DE CLIENTE: '.$request->get('nombreCliente').' CON FACTURA NUMERO: '.substr($cxcAux->cuenta_descripcion, 38);
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $cxcAux->sucursal_id;
            $diario->save();
            /*Inicio de registro de auditoria*/
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
            /*Fin de registro de auditoria*/ 
            /****************************************************************/
            $valAnt = $request->get('ADescontar');
            for ($i = 0; $i < count($valAnt); ++$i){
                if($request->get('checkAnt'.$i)){
                    if($valAnt[$i] > 0){
                        $anticipo = Anticipo_Cliente::Anticipo($request->get('checkAnt'.$i))->first();
                        /**********************descuento de anticipo****************************/
                        $totalAnt = $totalAnt + floatval($valAnt[$i]);
                        $descuento =  new Descuento_Anticipo_Cliente();
                        $descuento->descuento_fecha = $request->get('fechaCruce');
                        $descuento->descuento_valor = $valAnt[$i];
                        $descuento->descuento_estado = "1";
                        $descuento->anticipo_id = $anticipo->anticipo_id;
                        $descuento->descuento_descripcion = substr($cxcAux->cuenta_descripcion, 38);
                        $descuento->diario()->associate($diario);
                        $descuento->save();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Registro de descuentos de anticipo de cliente -> '.$request->get('nombreCliente'),'0','Registro de descuentos de anticipo de cliente -> '.$request->get('nombreCliente').' con factura -> '.$request->get('buscarFactura').' por un valor de -> '.$valAnt[$i]);
                        /*Fin de registro de auditoria*/ 
                        /****************************************************************/
                        if(is_null($anticipo->anticipo_documento)){
                            $anticipo->anticipo_saldo = $anticipo->anticipo_saldo - round($valAnt[$i],2);
                        }else{
                            $anticipo->anticipo_saldo = $anticipo->anticipo_valor-round(Anticipo_Cliente::AnticipoClienteDescuentos($anticipo->anticipo_id)->sum('descuento_valor'),2);
                        }
                        if($anticipo->anticipo_saldo == 0){
                            $anticipo->anticipo_estado = '2';
                        }else{
                            $anticipo->anticipo_estado = '1';
                        }
                        $anticipo->update();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Actualización de anticipo de cliente -> '.$request->get('nombreCliente'),'0','Actualización de anticipo de cliente -> '.$request->get('nombreCliente').' con factura -> '.$request->get('buscarFactura'));
                        /*Fin de registro de auditoria*/ 
                    }
                }
            }
            $cxcAux->cuenta_saldo = $cxcAux->cuenta_saldo - round($totalAnt,2);
            if($cxcAux->cuenta_saldo == 0){
                $cxcAux->cuenta_estado = '2';
            }else{
                $cxcAux->cuenta_estado = '1';
            }
            $cxcAux->update();
            /*Inicio de registro de auditoria*/
            $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$request->get('buscarFactura'));
            /*Fin de registro de auditoria*/ 
            /********************detalle de diario anticipo cliente*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = $request->get('idSeleccionado');
            $detalleDiario->detalle_haber = 0.00;
            $detalleDiario->detalle_comentario = 'P/R DESCUENTO DE ANTICIPO DE CLIENTE';
            $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE CLIENTE';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->cliente_id = $request->get('idCliente');
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
            }else{
                $parametrizacionContable = Cliente::findOrFail($request->get('idCliente'));
                $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,substr($cxcAux->cuenta_descripcion, 38),'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idSeleccionado'));
            /*************************************************************************/
            /********************detalle de diario cuenta por cobrar*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00 ;
            $detalleDiario->detalle_haber = $request->get('idSeleccionado');
            $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
            $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE CLIENTE';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->cliente_id = $request->get('idCliente');
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
            }else{
                $parametrizacionContable = Cliente::findOrFail($request->get('idCliente'));
                $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,substr($cxcAux->cuenta_descripcion, 38),'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idSeleccionado'));
            /*************************************************************************/
            $url ='';
            if(Auth::user()->empresa->empresa_contabilidad== '1'){
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            return redirect('descontarAntCli')->with('success','Anticipo descontado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('descontarAntCli')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function descontarFactura(Request $request){
        try{            
            DB::beginTransaction();
            $general = new generalController();
            $factura =  Factura_Venta::factura($request->get('factura_id'))->first();
            $cxcAux = $factura->cuentaCobrar;       
            $cierre = $general->cierre($request->get('fechaCruce'),$factura->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('descontarAntCli')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            } 
            
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaCruce'),'CDAC');
                $diario->diario_fecha = $request->get('fechaCruce');
                $diario->diario_referencia = 'COMPROBANTE DE DESCUENTO ANTICIPO DE CLIENTE';
                $diario->diario_tipo_documento = 'DESCUENTO DE ANTICIPO';
                $diario->diario_numero_documento = $factura->factura_numero;
                $diario->diario_beneficiario = $request->get('nombreCliente');
                $diario->diario_tipo = 'CDAC';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE DESCUENTO ANTICIPO DE CLIENTE: '.$request->get('nombreCliente').' CON FACTURA NUMERO: '.$factura->factura_numero;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = $factura->rangoDocumento->puntoEmision->sucursal_id;
                $diario->save();
                /*Inicio de registro de auditoria*/
                $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
                /*Fin de registro de auditoria*/ 
                /****************************************************************/
            
            $valAnt = $request->get('ADescontar');
            for ($i = 0; $i < count($valAnt); ++$i){
                if($request->get('checkAnt'.$i)){
                    if($valAnt[$i] > 0){
                        $anticipo = Anticipo_Cliente::Anticipo($request->get('checkAnt'.$i))->first();
                        /**********************descuento de anticipo****************************/
                        $descuento =  new Descuento_Anticipo_Cliente();
                        $descuento->descuento_fecha = $request->get('fechaCruce');
                        $descuento->descuento_valor = $valAnt[$i];
                        $descuento->descuento_estado = "1";
                        $descuento->anticipo_id = $anticipo->anticipo_id;
                        $descuento->factura_id = $factura->factura_id;
                        
                            $descuento->diario()->associate($diario);
                        
                        $descuento->save();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Registro de descuentos de anticipo de cliente -> '.$request->get('nombreCliente'),'0','Registro de descuentos de anticipo de cliente -> '.$request->get('nombreCliente').' con factura -> '.$request->get('buscarFactura').' por un valor de -> '.$valAnt[$i]);
                        /*Fin de registro de auditoria*/ 
                        /****************************************************************/
                        if(is_null($anticipo->anticipo_documento)){
                            $anticipo->anticipo_saldo = $anticipo->anticipo_saldo - round($valAnt[$i],2);
                        }else{
                            $anticipo->anticipo_saldo = $anticipo->anticipo_valor-round(Anticipo_Cliente::AnticipoClienteDescuentos($anticipo->anticipo_id)->sum('descuento_valor'),2);
                        }
                        if($anticipo->anticipo_saldo == 0){
                            $anticipo->anticipo_estado = '2';
                        }else{
                            $anticipo->anticipo_estado = '1';
                        }
                        $anticipo->update();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Actualización de anticipo de cliente -> '.$request->get('nombreCliente'),'0','Actualización de anticipo de cliente -> '.$request->get('nombreCliente').' con factura -> '.$request->get('buscarFactura'));
                        /*Fin de registro de auditoria*/ 
                    }
                }
            }
            $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($factura->factura_id)->sum('descuento_valor'),2);
            if($cxcAux->cuenta_saldo == 0){
                $cxcAux->cuenta_estado = '2';
            }else{
                $cxcAux->cuenta_estado = '1';
            }
            $cxcAux->update();
            /*Inicio de registro de auditoria*/
            $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$request->get('buscarFactura'));
            /*Fin de registro de auditoria*/ 
            
                /********************detalle de diario anticipo cliente*******************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('idSeleccionado');
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario = 'P/R DESCUENTO DE ANTICIPO DE CLIENTE';
                $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE CLIENTE';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cliente_id = $request->get('idCliente');
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = Cliente::findOrFail($request->get('idCliente'));
                    $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idSeleccionado'));
                /*************************************************************************/
                /********************detalle de diario cuenta por cobrar*******************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00 ;
                $detalleDiario->detalle_haber = $request->get('idSeleccionado');
                $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE CLIENTE';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cliente_id = $request->get('idCliente');
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = Cliente::findOrFail($request->get('idCliente'));
                    $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idSeleccionado'));
                /*************************************************************************/
            
            $url ='';
            if(Auth::user()->empresa->empresa_llevaContabilidad== '1'){
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            return redirect('descontarAntCli')->with('success','Anticipo descontado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('descontarAntCli')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}