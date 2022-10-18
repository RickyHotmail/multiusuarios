<?php

namespace App\Http\Controllers;

use App\Models\Anticipo_Empleado;
use App\Models\Arqueo_Caja;
use App\Models\Banco;
use App\Models\Caja;
use App\Models\Cuenta_Bancaria;
use App\Models\Descuento_Anticipo_Empleado;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Empresa_Departamento;
use App\Models\Movimiento_Caja;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class descontarAnticipoEmpleadoController extends Controller
{
    public function nuevo(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            return view('admin.recursosHumanos.descontarAnticipo.index',['cajaAbierta'=>$cajaAbierta,'departamentos'=>Empresa_Departamento::Departamentos()->get(),'bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function empleadosDepartamentoAnticipo(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            return view('admin.recursosHumanos.descontarAnticipo.index',['cajaAbierta'=>$cajaAbierta,'empleados'=>Empleado::EmpleadosByDepartamento($request->get('departamento_id'))->get(),'departamentoC'=>$request->get('departamento_id'),'departamentos'=>Empresa_Departamento::Departamentos()->get(),'bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            //return view('admin.cuentasCobrar.pagosCXC.index',['departamentoC'=>$request->get('departamento_id'),'empleados'=>Empleado::EmpleadosByDepartamento($request->get('departamento_id'))->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get(),'sucursales'=>Sucursal::sucursales()->get(),'cajas'=>Caja_Usuario::CajaXusuario(Auth::user()->user_id)->get(),'bancos'=>Banco::bancos()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('descontarAntEmp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function descontar(Request $request){
        try{            
            DB::beginTransaction();
            $general = new generalController();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $empleado = Empleado::empleado($request->get('idEmpleado'))->first();
            $cierre = $general->cierre($request->get('fechaCruce'),$empleado->departamento->sucursal_id);          
            if($cierre){
                return redirect('descontarAntEmp')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            } 
            $selecAnticipos ='';
            $cuentaPago = '';
            if($request->get('radioPago') == 'EFECTIVO'){
                $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                $cuentaPago = $cuentacaja->cuenta_id;
            }
            /**********************asiento diario****************************/
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaCruce'),'CDAE');
            $diario->diario_fecha = $request->get('fechaCruce');
            $diario->diario_referencia = 'COMPROBANTE DE DESCUENTO ANTICIPO DE EMPLEADO';
            $diario->diario_tipo_documento = 'DESCUENTO DE ANTICIPO';
            $diario->diario_numero_documento = '0';
            $diario->diario_beneficiario = $request->get('nombreEmpleado');
            $diario->diario_tipo = 'CDAE';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechaCruce'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE DESCUENTO ANTICIPO DE EMPLEADO: '.$request->get('nombreEmpleado').' CON '.$request->get('radioPago');
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $empleado->departamento->sucursal_id;
            $diario->save();
            /*Inicio de registro de auditoria*/
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
            /*Fin de registro de auditoria*/ 
            /****************************************************************/
            $valAnt = $request->get('Ddescontar');
            for ($i = 0; $i < count($valAnt); ++$i){
                if($request->get('checkAnt'.$i)){
                    if($valAnt[$i] > 0){
                        $anticipo = Anticipo_Empleado::Anticipo($request->get('checkAnt'.$i))->first();                        
                        $selecAnticipos = $selecAnticipos.'-'.$anticipo->anticipo_numero;                       
                        /**********************descuento de anticipo****************************/
                        $descuento =  new Descuento_Anticipo_Empleado();
                        $descuento->descuento_fecha = $request->get('fechaCruce');
                        $descuento->descuento_descripcion = "DESCUENTO DE ANTICIPO A EMPLEADO CON ".$request->get('radioPago');
                        $descuento->descuento_valor = $valAnt[$i];
                        $descuento->descuento_estado = "1";
                        $descuento->anticipo_id = $anticipo->anticipo_id;
                        $descuento->diario()->associate($diario);
                        $descuento->save();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Registro de descuentos de anticipo de empleado -> '.$request->get('nombreEmpleado'),'0','Registro de descuentos de anticipo de empleado -> '.$request->get('nombreEmpleado').' con '.$request->get('radioPago').' por un valor de -> '.$valAnt[$i]);
                        /*Fin de registro de auditoria*/ 
                        /****************************************************************/
                        $anticipo->anticipo_saldo = $anticipo->anticipo_valor- round(Anticipo_Empleado::AnticipoEmpleadoDescuentos($anticipo->anticipo_id)->sum('descuento_valor'),2);
                        if($anticipo->anticipo_saldo == 0){
                            $anticipo->anticipo_estado = '2';
                        }
                        $anticipo->update();
                        /*Inicio de registro de auditoria*/
                        $general->registrarAuditoria('Actualización de anticipo de empleado -> '.$request->get('nombreEmpleado'),'0','Actualización de anticipo de empleado -> '.$request->get('nombreEmpleado').' con '.$request->get('radioPago'));
                        /*Fin de registro de auditoria*/ 
                    }
                }
            }
            /********************detalle de diario cuenta por cobrar*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = $request->get('idDescontar');
            $detalleDiario->detalle_haber = 0.00;
            $detalleDiario->detalle_comentario = 'P/R DESCUENTO DE ANTICIPO DE EMPLEADO CON '.$request->get('radioPago');
            $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE EMPLEADO';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            if($request->get('radioPago') == 'EFECTIVO'){
                $detalleDiario->cuenta_id = $cuentaPago;
            }else{
                $parametrizacionContable=Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_id'))->first();
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,'0','Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idDescontar'));
            /*************************************************************************/
            /********************detalle de diario anticipo cliente*******************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber = $request->get('idDescontar');
            $detalleDiario->detalle_comentario = 'P/R DESCUENTO DE ANTICIPO DE EMPLEADO CON '.$request->get('radioPago');
            $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE EMPLEADO';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->empleado_id = $empleado->empleado_id;
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE EMPLEADO')->first();
            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
            }else{
                $detalleDiario->cuenta_id = $empleado->empleado_cuenta_anticipo;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,'0','Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('idDescontar'));
            /*************************************************************************/
            if($request->get('radioPago') == 'EFECTIVO'){
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="ENTRADA";
                $movimientoCaja->movimiento_descripcion= 'P/R DESCUENTO DE ANTICIPO DE EMPLEADO :'.$request->get('nombreEmpleado');
                $movimientoCaja->movimiento_valor= $request->get('idDescontar');
                $movimientoCaja->movimiento_documento="DESCUENTO DE ANTICIPO DE EMPLEADO EN EFECTIVO";
                $movimientoCaja->movimiento_numero_documento= $selecAnticipos;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                    $movimientoCaja->diario()->associate($diario);
                }
                $movimientoCaja->save();
                /*********************************************************************/
            }
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('descontarAntEmp')->with('success','Anticipo descontado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('descontarAntEmp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
