<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Banco;
use App\Models\Caja;
use App\Models\Caja_Usuario;
use App\Models\Cuenta;
use App\Models\Cuenta_Bancaria;
use App\Models\Deposito;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Movimiento_Caja;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class depositoCajaController extends Controller
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
            $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $cajas = Caja::cajas()->get();
            return view('admin.caja.depositoCaja.index',['cajas'=>$cajas,'cajasxusuario'=>$cajasxusuario, 'sucursales'=>Sucursal::sucursales()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            $sucursalCaja = Caja::Caja($request->get('caja_id'))->first();        
            $banco = Banco::banco($request->get('banco_id'))->first();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();      
            $general = new generalController();
            $cierre = $general->cierre($request->get('idFecha'),$sucursalCaja->sucursal_id);          
            if($cierre){
                return redirect('depositoCaja')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }    
            try{           
                DB::beginTransaction();
                $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                $cuentaBanco=Cuenta_Bancaria::CuentaBanco($request->get('cuenta_id'))->first();
                $deposito = new Deposito();
                $deposito->deposito_fecha = $request->get('idFecha');
                $deposito->deposito_tipo = 'DEPOSITO EN EFECTIVO';           
                $deposito->deposito_numero = 0;
                $deposito->deposito_valor = $request->get('idValor');  
                $deposito->deposito_descripcion = $request->get('idMensaje');
                $deposito->deposito_estado = 1;            
                $deposito->cuenta_bancaria_id = $request->get('cuenta_id');
                $deposito->empresa_id = Auth::user()->empresa->empresa_id; 
                $deposito->save();
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="SALIDA";
                $movimientoCaja->movimiento_descripcion= $request->get('idMensaje');
                $movimientoCaja->movimiento_valor= $request->get('idValor');
                $movimientoCaja->movimiento_documento="DEPOSITO DE CAJA";
                $movimientoCaja->movimiento_numero_documento= 0;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
            
                /**********************asiento diario****************************/
                $general = new generalController();
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CECA');
                $diario->diario_fecha = $request->get('idFecha');
                $diario->diario_referencia = 'DEPOSITO DE CAJA ';
                $diario->diario_tipo_documento = 'DEPOSITO';
                $diario->diario_numero_documento = 0;
                $diario->diario_beneficiario = 0;
                $diario->diario_tipo = 'CECA';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
                $diario->diario_comentario = 'DEPOSITO DE CAJA AL BANCO .: '.$banco->bancoLista->banco_lista_nombre;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = $sucursalCaja->sucursal_id;
                $diario->save();
                $movimientoCaja->diario()->associate($diario);            
                
                $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'Tipo de Diario -> '.$diario->diario_referencia.'');
                /********************detalle de diario de venta********************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('idValor');
                $detalleDiario->detalle_haber = 0.00 ;
                $detalleDiario->detalle_comentario = 'CUENTA DE BANCO  ';
                $detalleDiario->detalle_tipo_documento = 'DEPOSITO';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';           
                $detalleDiario->deposito()->associate($deposito);
                $detalleDiario->cuenta_id = $cuentaBanco->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta de Banco -> '.$request->get('idCuentaContable').' con el valor de: -> '.$request->get('idValor'));
                
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = $request->get('idValor');
                $detalleDiario->detalle_comentario = 'CUENTA DE CAJA  '.$deposito->deposito_descripcion;
                $detalleDiario->detalle_tipo_documento = 'DEPOSITO';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;           
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta de caja -> '.$request->get('cuenta_caja').' con el valor de: -> '.$request->get('idValor'));
                
                $movimientoCaja->save();
                /*Inicio de registro de auditoria */
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de Deposito de Caja al banco-> '.$banco->bancoLista->banco_lista_nombre,  $diario->diario_codigo,'Con por el valor de .:'.$request->get('idValor'));
                /*Fin de registro de auditoria */
                $url = $general->pdfDiario($diario);
                DB::commit();
                return redirect('depositoCaja')->with('success','Datos guardados exitosamente')->with('diario',$url);
            }catch(\Exception $ex){
                DB::rollBack();
                return redirect('depositoCaja')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('depositoCaja')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
}
