<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Cheque;
use App\Models\Cuenta;
use App\Models\Cuenta_Bancaria;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use App\Models\Tipo_Empleado_Parametrizacion;
use App\Models\Transferencia;
use App\Models\Vacacion;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class vacacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
    }
    public function imprimir()
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $vacacion=Vacacion::vacaciones()->get();
            $estados=Vacacion::Estados()->select('vacacion_estado')->distinct()->get();
            $empleado=Vacacion::Estados()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            return view('admin.recursosHumanos.vacacion.view',['fecha_desde'=>null,'fecha_hasta'=>null,'fecha_todo'=>null,'nombre_empleado'=>null,'estadoactual'=>null,'estados'=>$estados,'empleado'=>$empleado,'vacacion'=>$vacacion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $empleadovacaciones=Vacacion::vacaciones()->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Vacaciones')->first();   
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Vacacion::secuencial($rangoDocumento->rango_id)->max('vacacion_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $datos = null; 
                return view('admin.recursosHumanos.vacacion.index',['empleadovacaciones'=>$empleadovacaciones,'empleados'=>Empleado::EmpleadosRolSucursal($rangoDocumento->puntoEmision->sucursal_id)->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),  'rangoDocumento'=>$rangoDocumento,'sucursales'=>Sucursal::Sucursales()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
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
        //
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
            $urlcheque = '';
            $empleado = Empleado::EmpleadoById($request->get('idEmpleado'))->first();
            $banco=Banco::findOrFail($request->get('banco_id'));
            $cuentabanco=Cuenta_Bancaria::findOrFail($request->get('cuenta_id'));
            $vacacion = new Vacacion();
            $general = new generalController();
            $cierre = $general->cierre($request->get('idFecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('vacacion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
          

            $vacacion->vacacion_numero = $request->get('vacacion_serie').substr(str_repeat(0, 9).$request->get('vacacion_numero'), - 9);
            $vacacion->vacacion_serie = $request->get('vacacion_serie');
            $vacacion->vacacion_secuencial =$request->get('vacacion_numero');
            $vacacion->rango_id =$request->get('rango_id');

            $vacacion->vacacion_fecha = $request->get('idFecha');
            $vacacion->vacacion_tipo = $request->get('idTipo');            
            $vacacion->vacacion_valor = $request->get('idValor');
            if ( $request->get('idMensaje')) {
                $vacacion->vacacion_descripcion =  $request->get('idMensaje');
            }
            else{
                $vacacion->vacacion_descripcion = ' ';
            }
            $vacacion->empleado_id = $request->get('idEmpleado');        
            $vacacion->vacacion_estado = 1;   
            //$tipo=Empleado::EmpleadoBusquedaCuenta($request->get('idEmpleado'),'vacacion')->first();
            $tipo = Tipo_Empleado_Parametrizacion::TipoEmpleadoBusquedaCuenta($empleado->tipo_id,'vacacion')->first();
            /**********************asiento diario****************************/
            $general = new generalController();
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CEPV');
            $diario->diario_fecha = $request->get('idFecha');
            $diario->diario_referencia = 'COMPROBANTE DE EGRESO PAGO DE VACACIONES';
            if ($request->get('idTipo') == 'Transferencia'){      
                $diario->diario_tipo_documento = 'TRANSFERENCIA'; 
                $diario->diario_numero_documento = 0;
            }
            if ($request->get('idTipo') == 'Cheque'){      
                $diario->diario_tipo_documento = 'CHEQUE'; 
                $diario->diario_numero_documento = $request->get('idNcheque');
            }
           
            $diario->diario_beneficiario = $empleado->empleado_nombre;
            $diario->diario_tipo = 'CEPV';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE EGRESO PAGO DE VACACIONES: '.$empleado->empleado_nombre;
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id =  Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
            $diario->save();
            $vacacion->diario()->associate($diario);
            
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
            
            /*REGISTRO DE CHEQUE*/            
            if ($request->get('idTipo') == 'Cheque'){      
                $formatter = new NumeroALetras();
                //echo $formatter->toWords($number, $decimals);
                $cheque = new Cheque();
                $cheque->cheque_numero = $request->get('idNcheque');
                $cheque->cheque_descripcion =  $request->get('descripcion');
                $cheque->cheque_beneficiario = $request->get('idBeneficiario');
                $cheque->cheque_fecha_emision = $request->get('idFecha');
                $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                $cheque->cheque_valor = $request->get('idValor');
                $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                $cheque->cuenta_bancaria_id = $request->get('cuenta_id');      
                $cheque->cheque_estado = '1';
                $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                $cheque->save();
                $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_id'),$cheque);
                $general->registrarAuditoria('Registro de Cheque  pago vacaciones  numero: -> '.$request->get('idNcheque'),'0','Por motivo de: -> '. $vacacion->vacacion_descripcion.' con el valor de: -> '.$request->get('idValor'));
            }
            /*REGISTRO DE TRSNFERENCIA*/          
            if ($request->get('idTipo') == 'Transferencia'){       
                $transferencia = new Transferencia();
                
                $transferencia->transferencia_descripcion =  $request->get('descripcion');
                $transferencia->transferencia_beneficiario = $request->get('nempleado');
                $transferencia->transferencia_fecha = $request->get('idFecha');
                $transferencia->transferencia_valor = $request->get('idValor');
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');      
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de Transferencia pago vacaciones numero: -> '.$request->get('ncuenta'),'0','Por motivo de: -> '. $vacacion->vacacion_descripcion.' con el valor de: -> '.$request->get('idValor'));
            }

            /********************detalle de diario de venta********************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe =  0.00 ;
            $detalleDiario->detalle_haber =$request->get('idValor');
            $detalleDiario->detalle_comentario = 'PAGO DE VACACIONES DE EMPLEADO '.$empleado->empleado_nombre;
            if ($request->get('idTipo') == 'Transferencia') {
                $detalleDiario->detalle_comentario = $banco->bancoLista->banco_lista_nombre.' Con Cuenta # '.$cuentabanco->cuenta_bancaria_numero;
            
            }
            if ($request->get('idTipo') == 'Cheque') {
                $detalleDiario->detalle_comentario = $banco->bancoLista->banco_lista_nombre.' Con Cuenta # '.$cuentabanco->cuenta_bancaria_numero.' Con Cheque #'.($request->get('idNcheque'));
            }
            $detalleDiario->detalle_tipo_documento = 'VACACIONES';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';           
            $detalleDiario->cuenta_id = $request->get('idCuentaContable');
            if ($request->get('idTipo') == 'Cheque'){      
                $detalleDiario->cheque()->associate($cheque);
            }
            if ($request->get('idTipo') == 'Transferencia'){      
                $detalleDiario->transferencia()->associate($transferencia);
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$request->get('idValor'));
            
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe =$request->get('idValor');
            $detalleDiario->detalle_haber =  0.00;
            $detalleDiario->detalle_comentario = 'VACACIONES DE EMPLEADO '.$empleado->empleado_nombre;
            $detalleDiario->detalle_tipo_documento = 'VACACIONES';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->cuenta_id = $tipo->cuenta_haber;   
            $detalleDiario->empleado_id = $request->get('idEmpleado');         
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del debe -> '.$tipo->cuenta_haber.' con el valor de: -> '.$request->get('idValor'));
                        
            $vacacion->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de vacacion de Empleado -> '.$request->get('idEmpleado'),'0','Con motivo:'. $vacacion->vacacion_descripcion);
            /*Fin de registro de auditoria */
            $url = $general->pdfDiario($diario);
            if ($request->get('idTipo') == 'Cheque') {
                DB::commit();
                return redirect('vacacion/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('diario',$url)->with('cheque',$urlcheque);;
            }
            DB::commit();
            return redirect('vacacion/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('vacacion/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function buscar(Request $request)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $vacacion=null;
            $esta_quin=Vacacion::Estados()->select('vacacion_estado')->distinct()->get();
            $empleado=Vacacion::Estados()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_empleado') == "--TODOS--" && $request->get('estados') == "--TODOS--") {
                $vacacion=Vacacion::Estados()->get();
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_empleado') != "--TODOS--" && $request->get('estados') != "--TODOS--") {
                $vacacion=Vacacion::vacacionesDiferente($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'),$request->get('estados'))->get();  
            }             
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_empleado') == "--TODOS--" && $request->get('estados') == "--TODOS--") {
                $vacacion=Vacacion::vacacionesfecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->get();
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_empleado') != "--TODOS--" && $request->get('estados') == "--TODOS--") {
                $vacacion=Vacacion::vacacionesEmpleado($request->get('nombre_empleado'))->get();
                            
            } 
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_empleado') == "--TODOS--" && $request->get('estados') != "--TODOS--") {
                $vacacion=Vacacion::vacacionesestado($request->get('estados'))->get();               
            }   
            return view('admin.recursosHumanos.vacacion.view',['fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_empleado'=>$request->get('nombre_empleado'),'estadoactual'=>$request->get('estados'),'estados'=>$esta_quin,'empleado'=>$empleado,'vacacion'=>$vacacion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function anulacion(Request $request)
    {
        try{
            DB::beginTransaction();
            $vacacion=Vacacion::findOrFail($request->get('idvacacion'));
            $general = new generalController();
            $cierre = $general->cierre($vacacion->vacacion_fecha,$vacacion->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('lvacaciones')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach ($vacacion->diario->detalles as $i) {
                if (isset($i->cheque)) {
                    $chequeAux = $i->cheque;
                }          
                if (isset($i->transferencia)) {
                    $transferenciaAux = $i->transferencia;
                }
                $i->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion del detalle diario tipo documento numero: -> '.$i->detalle_tipo_documento.'con empleado '.$vacacion->empleado->emepleado_nombre, $request->get('idvacacion'),'Con vacacion  id -> '.$i.'con codigo de diario'.$vacacion->diario->diario_codigo);
            }  
            if(isset($chequeAux)){
                $cheque=Cheque::findOrFail($chequeAux->cheque_id);
                $cheque->cheque_estado='2';
                $cheque->save();
                $general->registrarAuditoria('Anulacion de Cheque numero: -> '.$chequeAux->cheque_numero, $chequeAux->cheque_id, 'Con vacacion  id -> '.$request->get('idvacacion').'Con valor de -> '.$chequeAux->cheque_valor);
            } 
            if(isset($transferenciaAux)){
                $transferenciaAux->delete();
                $general->registrarAuditoria('Eliminacion de transferencia numero: -> '.$transferenciaAux->transferencia_numero, $request->get('idvacacion'), 'Con vacacion  id -> '.$request->get('idvacacion').'Con valor de -> '.$transferenciaAux->transferencia_valor);
            }   
            $vacacion->delete();
            $general = new generalController();
            $general->registrarAuditoria('Eliminacion de la vacacion: -> '.$request->get('idvacacion').'con empleado '.$vacacion->empleado->emepleado_nombre, $request->get('idvacacion'), 'Con vacacion  id -> '.$request->get('idvacacion'));     
            $vacacion->diario->delete();
            $general = new generalController();
            $general->registrarAuditoria('Eliminacion del diario tipo documento numero: -> '.$vacacion->diario->diario_codigo.'con empleado '.$vacacion->empleado->emepleado_nombre, $request->get('idvacacion'), 'Con vacacion  id -> '.$request->get('idvacacion'));  
            DB::commit();
            return redirect('lvacaciones')->with('success','Datos Anulados exitosamente');
        }catch(\Exception $ex){
            return redirect('lvacaciones')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $vacacion=Vacacion::findOrFail($id);
            $general = new generalController();
            $cierre = $general->cierre($vacacion->vacacion_fecha,$vacacion->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('lvacaciones')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach ($vacacion->diario->detalles as $i) {
                if (isset($i->cheque)) {
                    $chequeAux = $i->cheque;
                }          
                if (isset($i->transferencia)) {
                    $transferenciaAux = $i->transferencia;
                }
                $i->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion del detalle diario tipo documento numero: -> '.$i->detalle_tipo_documento.'con empleado '.$vacacion->empleado->emepleado_nombre, $id,'Con vacacion  id -> '.$i.'con codigo de diario'.$vacacion->diario->diario_codigo);
            }  
            if(isset($chequeAux)){
                $chequeAux->delete();
                $general->registrarAuditoria('Eliminacion de Cheque numero: -> '.$chequeAux->cheque_numero, $id, 'Con vacacion  id -> '.$id.'Con valor de -> '.$chequeAux->cheque_valor);
            } 
            if(isset($transferenciaAux)){
                $transferenciaAux->delete();
                $general->registrarAuditoria('Eliminacion de transferencia numero: -> '.$transferenciaAux->transferencia_numero, $id, 'Con vacacion  id -> '.$id.'Con valor de -> '.$transferenciaAux->transferencia_valor);
            }   
            $vacacion->delete();
            $general = new generalController();
            $general->registrarAuditoria('Eliminacion de la vacacion: -> '.$id.'con empleado '.$vacacion->empleado->emepleado_nombre, $id, 'Con vacacion  id -> '.$id);     
            $vacacion->diario->delete();
            $general = new generalController();
            $general->registrarAuditoria('Eliminacion del diario tipo documento numero: -> '.$vacacion->diario->diario_codigo.'con empleado '.$vacacion->empleado->emepleado_nombre, $id, 'Con vacacion  id -> '.$id);  
            DB::commit();
            return redirect('lvacaciones')->with('success','Datos Eliminados exitosamente');
        }catch(\Exception $ex){
            return redirect('lvacaciones')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function delete($id)
    {        
        try {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
        $vacacion=Vacacion::vacacion($id)->get()->first();
        $transferencia=null;
        $cheque=null;
            foreach ($vacacion->diario->detalles as $i) {
               
                if (isset($i->cheque_id)) {
                    $cheque=Cheque::findOrFail($i->cheque_id);
                }
                if (isset($i->transferencia_id)) {
                    $transferencia=Transferencia::findOrFail($i->transferencia_id);
                }
            }
     
        return view('admin.recursosHumanos.vacacion.eliminar',['transferencia'=>$transferencia,'cheque'=>$cheque,'vacacion'=>$vacacion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('vacacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function anular($id)
    {        
        try {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
        $vacacion=Vacacion::vacacion($id)->get()->first();
        $transferencia=null;
        $cheque=null;
            foreach ($vacacion->diario->detalles as $i) {
               
                if (isset($i->cheque_id)) {
                    $cheque=Cheque::findOrFail($i->cheque_id);
                }
                if (isset($i->transferencia_id)) {
                    $transferencia=Transferencia::findOrFail($i->transferencia_id);
                }
            }
     
        return view('admin.recursosHumanos.vacacion.anular',['transferencia'=>$transferencia,'cheque'=>$cheque,'vacacion'=>$vacacion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('vacacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function ver($id)
    {        
        try {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
        $vacacion=Vacacion::vacacion($id)->get()->first();
        $transferencia=null;
        $cheque=null;
            foreach ($vacacion->diario->detalles as $i) {
               
                if (isset($i->cheque_id)) {
                    $cheque=Cheque::findOrFail($i->cheque_id);
                }
                if (isset($i->transferencia_id)) {
                    $transferencia=Transferencia::findOrFail($i->transferencia_id);
                }
            }
        return view('admin.recursosHumanos.vacacion.ver',['transferencia'=>$transferencia,'cheque'=>$cheque,'vacacion'=>$vacacion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('vacacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function imprimirdiario($id)
    {
        try {
            $vacacion=Vacacion::vacacion($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfDiariourl($vacacion->diario);
            return $url;
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByEmpleado($id){
        return Vacacion::vacacionesbuscarEmpleado($id)->get();
    }
}
