<?php

namespace App\Http\Controllers;

use App\Models\Anticipo_Proveedor;
use App\Models\Cuenta_Pagar;
use App\Models\Descuento_Anticipo_Proveedor;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Parametrizacion_Contable;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Transaccion_Compra;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class descontarAnticipoProveedorController extends Controller
{
    public function nuevo(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasPagar.descontarAnticipo.index',['proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function descontar(Request $request){
        if($request->get('tipoDocumento') == 'factura'){
            return $this->descontarFactura($request);
        }else{
            return $this->descontarCXP($request);
        }
    }
    public function descontarCXP(Request $request){
        try{            
            DB::beginTransaction();
            $general = new generalController();
            $cxpAux = Cuenta_Pagar::Cuenta($request->get('factura_id'))->first();
            $cierre = $general->cierre($request->get('fechaCruce'),$cxpAux->sucursal_id);          
            if($cierre){
                return redirect('descontarAntPro')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }

            /**********************asiento diario****************************/
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaCruce'),'CDAP');
            $diario->diario_fecha = $request->get('fechaCruce');
            $diario->diario_referencia = 'COMPROBANTE DE DESCUENTO ANTICIPO DE PROVEEDOR';
            $diario->diario_tipo_documento = 'DESCUENTO DE ANTICIPO';
            $diario->diario_numero_documento = substr($cxpAux->cuenta_descripcion, 39);
            $diario->diario_beneficiario = $request->get('nombreProveedor');
            $diario->diario_tipo = 'CDAP';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE DESCUENTO ANTICIPO DE PROVEEDOR: '.$request->get('nombreProveedor').' CON FACTURA NUMERO: '.substr($cxpAux->cuenta_descripcion, 39);
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $cxpAux->sucursal_id;
            $diario->save();
            /*Inicio de registro de auditoria*/
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
            /*Fin de registro de auditoria*/ 
            /****************************************************************/
            $valAnt = $request->get('ADescontar');
            for ($i = 0; $i < count($valAnt); ++$i){
                if($request->get('checkAnt'.$i)){
                    if($valAnt[$i] > 0){
                        $anticipo = Anticipo_Proveedor::AnticipoProveedor($request->get('checkAnt'.$i))->first();
                        /**********************descuento de anticipo****************************/
                        $descuento =  new Descuento_Anticipo_Proveedor();
                        $descuento->descuento_fecha = $request->get('fechaCruce');
                        $descuento->descuento_valor = $valAnt[$i];
                        $descuento->descuento_estado = "1";
                        $descuento->anticipo_id = $anticipo->anticipo_id;
                        $descuento->descuento_descripcion = substr($cxpAux->cuenta_descripcion, 39);
                        $descuento->diario()->associate($diario);
                        $descuento->save();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Registro de descuentos de anticipo de proveedor -> '.$request->get('nombreProveedor'),'0','Registro de descuentos de anticipo de proveedor -> '.$request->get('nombreProveedor').' con factura -> '.$request->get('buscarFactura').' por un valor de -> '.$valAnt[$i]);
                        /*Fin de registro de auditoria*/ 
                        /****************************************************************/
                        if(is_null($anticipo->anticipo_documento)){
                            $anticipo->anticipo_saldo = $anticipo->anticipo_saldo - round($valAnt[$i],2);
                        }else{
                            $anticipo->anticipo_saldo = $anticipo->anticipo_valor-round(Anticipo_Proveedor::AnticipoClienteDescuentos($anticipo->anticipo_id)->sum('descuento_valor'),2);
                        }
                        
                        if($anticipo->anticipo_saldo == 0){
                            $anticipo->anticipo_estado = '2';
                        }else{
                            $anticipo->anticipo_estado = '1';
                        }
                        $anticipo->update();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Actualización de anticipo de proveedor -> '.$request->get('nombreProveedor'),'0','Actualización de anticipo de proveedor -> '.$request->get('nombreProveedor').' con factura -> '.$request->get('buscarFactura'));
                        /*Fin de registro de auditoria*/ 
                    }
                }
            }
            $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor') - Descuento_Anticipo_Proveedor::DescuentosAnticipoByCXP(substr($cxpAux->cuenta_descripcion, 39))->sum('descuento_valor'),2);
            if($cxpAux->cuenta_saldo == 0){
                $cxpAux->cuenta_estado = '2';
            }else{
                $cxpAux->cuenta_estado = '1';
            }
            $cxpAux->update();
            /*Inicio de registro de auditoria*/
            $general->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor -> '.$request->get('nombreProveedor'),'0','Actualizacion de cuenta por pagar de proveedor -> '.$request->get('nombreProveedor').' con factura -> '.$request->get('buscarFactura'));
            /*Fin de registro de auditoria*/ 
            /********************detalle de diario cuenta por cobrar*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = $request->get('idSeleccionado');
            $detalleDiario->detalle_haber = 0.00;
            $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
            $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE PROVEEDOR';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->proveedor_id = $request->get('IdProveedor');
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
            }else{
                $parametrizacionContable = Proveedor::findOrFail($request->get('IdProveedor'));
                $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,substr($cxpAux->cuenta_descripcion, 39),'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idSeleccionado'));
            /*************************************************************************/
            /********************detalle de diario anticipo cliente*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber = $request->get('idSeleccionado');
            $detalleDiario->detalle_comentario = 'P/R DESCUENTO DE ANTICIPO DE PROVEEDOR';
            $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE PROVEEDOR';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->proveedor_id = $request->get('IdProveedor');
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
            }else{
                $parametrizacionContable = Proveedor::findOrFail($request->get('IdProveedor'));
                $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,substr($cxpAux->cuenta_descripcion, 39),'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idSeleccionado'));
            /*************************************************************************/
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('descontarAntPro')->with('success','Anticipo descontado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('descontarAntPro')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function descontarFactura(Request $request){
        try{            
            DB::beginTransaction();
            $general = new generalController();
            $factura =  Transaccion_Compra::Transaccion($request->get('factura_id'))->first();
            $cxpAux = $factura->cuentaPagar;
            $cierre = $general->cierre($request->get('fechaCruce'),$factura->sucursal_id);          
            if($cierre){
                return redirect('descontarAntPro')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $totalAnt = 0;
            /**********************asiento diario****************************/
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaCruce'),'CDAP');
            $diario->diario_fecha = $request->get('fechaCruce');
            $diario->diario_referencia = 'COMPROBANTE DE DESCUENTO ANTICIPO DE PROVEEDOR';
            $diario->diario_tipo_documento = 'DESCUENTO DE ANTICIPO';
            $diario->diario_numero_documento = $factura->transaccion_numero;
            $diario->diario_beneficiario = $request->get('nombreProveedor');
            $diario->diario_tipo = 'CDAP';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE DESCUENTO ANTICIPO DE PROVEEDOR: '.$request->get('nombreProveedor').' CON FACTURA NUMERO: '.$factura->transaccion_numero;
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $factura->sucursal_id;
            $diario->save();
            /*Inicio de registro de auditoria*/
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
            /*Fin de registro de auditoria*/ 
            /****************************************************************/
            $valAnt = $request->get('ADescontar');
            for ($i = 0; $i < count($valAnt); ++$i){
                if($request->get('checkAnt'.$i)){
                    if($valAnt[$i] > 0){
                        $anticipo = Anticipo_Proveedor::AnticipoProveedor($request->get('checkAnt'.$i))->first();
                        /**********************descuento de anticipo****************************/
                        $totalAnt = $totalAnt + floatval($valAnt[$i]);
                        $descuento =  new Descuento_Anticipo_Proveedor();
                        $descuento->descuento_fecha = $request->get('fechaCruce');
                        $descuento->descuento_valor = $valAnt[$i];
                        $descuento->descuento_estado = "1";
                        $descuento->anticipo_id = $anticipo->anticipo_id;
                        $descuento->transaccion_id = $factura->transaccion_id;
                        $descuento->diario()->associate($diario);
                        $descuento->save();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Registro de descuentos de anticipo de proveedor -> '.$request->get('nombreProveedor'),'0','Registro de descuentos de anticipo de proveedor -> '.$request->get('nombreProveedor').' con factura -> '.$request->get('buscarFactura').' por un valor de -> '.$valAnt[$i]);
                        /*Fin de registro de auditoria*/ 
                        /****************************************************************/
                        if(is_null($anticipo->anticipo_documento)){
                            $anticipo->anticipo_saldo = $anticipo->anticipo_saldo - round($valAnt[$i],2);
                        }else{
                            $anticipo->anticipo_saldo = $anticipo->anticipo_valor-round(Anticipo_Proveedor::AnticipoClienteDescuentos($anticipo->anticipo_id)->sum('descuento_valor'),2);
                        }
                        
                        if($anticipo->anticipo_saldo == 0){
                            $anticipo->anticipo_estado = '2';
                        }else{
                            $anticipo->anticipo_estado = '1';
                        }
                        $anticipo->update();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Actualización de anticipo de proveedor -> '.$request->get('nombreProveedor'),'0','Actualización de anticipo de proveedor -> '.$request->get('nombreProveedor').' con factura -> '.$request->get('buscarFactura'));
                        /*Fin de registro de auditoria*/ 
                    }
                }
            }
            $cxpAux->cuenta_saldo = $cxpAux->cuenta_saldo - round($totalAnt,2);
            if($cxpAux->cuenta_saldo == 0){
                $cxpAux->cuenta_estado = '2';
            }else{
                $cxpAux->cuenta_estado = '1';
            }
            $cxpAux->update();
            /*Inicio de registro de auditoria*/
            $general->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor -> '.$request->get('nombreProveedor'),'0','Actualizacion de cuenta por pagar de proveedor -> '.$request->get('nombreProveedor').' con factura -> '.$request->get('buscarFactura'));
            /*Fin de registro de auditoria*/ 
            /********************detalle de diario cuenta por cobrar*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = $request->get('idSeleccionado');
            $detalleDiario->detalle_haber = 0.00;
            $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
            $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE PROVEEDOR';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->proveedor_id = $request->get('IdProveedor');
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
            }else{
                $parametrizacionContable = Proveedor::findOrFail($request->get('IdProveedor'));
                $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idSeleccionado'));
            /*************************************************************************/
            /********************detalle de diario anticipo cliente*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber = $request->get('idSeleccionado');
            $detalleDiario->detalle_comentario = 'P/R DESCUENTO DE ANTICIPO DE PROVEEDOR';
            $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE PROVEEDOR';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->proveedor_id = $request->get('IdProveedor');
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
            }else{
                $parametrizacionContable = Proveedor::findOrFail($request->get('IdProveedor'));
                $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idSeleccionado'));
            /*************************************************************************/
            $url ='';
            if (Auth::user()->empresa->empresa_llevaContabilidad== '1') {
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            return redirect('descontarAntPro')->with('success','Anticipo descontado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('descontarAntPro')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
