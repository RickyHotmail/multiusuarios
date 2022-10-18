<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anticipo_Empleado;
use App\Models\Banco;
use App\Models\Cheque;
use App\Models\Cuenta;
use App\Models\Cuenta_Bancaria;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Quincena;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class quincenaConsolidadaController extends Controller
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
            $datos = null; 

        return view('admin.recursosHumanos.quincenaConsolidada.view',[ 'datos'=>$datos,'sucursales'=>Sucursal::Sucursales()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
           
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Quincena')->first();   
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Quincena::secuencial($rangoDocumento->rango_id)->max('quincena_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $datos = null; 
                return view('admin.recursosHumanos.quincenaConsolidada.view',['secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),  'rangoDocumento'=>$rangoDocumento,'datos'=>$datos,'sucursales'=>Sucursal::Sucursales()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        if (isset($_POST['generar'])){
            return $this->generar($request);
        }
        if (isset($_POST['extraer'])){
          
            return $this->extraer($request);
 
        }
       
    }
    public function eliminar(Request $request){
        try{
            DB::beginTransaction();
            $idquince=$request->get('idquincena');
            for ($i = 0; $i < count($idquince); ++$i) {
                $quincena=Quincena::findOrFail($idquince[$i]);
                if(isset($quincena->rol)){
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un rol');
                }
                if(count($quincena->decuento)>0){
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un rol');
                }
            }
            for ($i = 0; $i < count($idquince); ++$i) {
                $quincena=Quincena::findOrFail($idquince[$i]);
                $id=$idquince[$i];
                $diario=Diario::findOrFail($quincena->diario_id);
                $general = new generalController();
                $cierre = $general->cierre($quincena->quincena_fecha,$diario->sucursal_id);
                if ($cierre) {
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
                
                $quincena->diario_id=null;
                $quincena->save();

                
                $quincena->delete();
                $general->registrarAuditoria('Eliminacion de la quincena: -> '.$id.'con empleado '.$quincena->empleado->emepleado_nombre, $id, 'Con quincena  id -> '.$id);
       
         
            }
            foreach ($diario->detalles as $i) {
                  
                if (isset($i->transferencia_id)) {
                    $detalle=Detalle_Diario::findOrFail($i->detalle_id);
                
                    $transferenciaAux=Transferencia::findOrFail($i->transferencia_id);

                    $detalle->transferencia_id=null;
                    $detalle->save();

                    $transferenciaAux->delete();
                    $general->registrarAuditoria('Eliminacion de Cheque numero: -> '.$transferenciaAux->transferencia_numero, $transferenciaAux->transferencia_id, 'Con Diario '.$diario->diario_id.' Con valor de -> '.$transferenciaAux->transferencia_valor);
                }
        

                $i->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion del detalle diario tipo documento numero: -> '.$i->detalle_tipo_documento,$diario->diario_id,'con codigo de diario'.$diario->diario_codigo);
            }
            $diario->delete();
            $general->registrarAuditoria('Eliminacion de Dario de quincena consolidada con empleado '.$quincena->empleado->emepleado_nombre, 0, '');
        
            DB::commit();
            return redirect('lquincena')->with('success','Datos Eliminados exitosamente');
        }catch(\Exception $ex){
            return redirect('lquincena')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function eliminarconsolidada(Request $request){
        try{
            DB::beginTransaction();
            $idquince=$request->get('idquincena');
            for ($i = 0; $i < count($idquince); ++$i) {
                $quincena=Quincena::findOrFail($idquince[$i]);
                if(isset($quincena->rol)){
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un rol');
                }
                if(isset($quincena->rolcm)){
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un rol');
                }
                if(count($quincena->decuento)>0){
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un descuento de quincena');
                }
            }
            for ($i = 0; $i < count($idquince); ++$i) {
                $quincena=Quincena::findOrFail($idquince[$i]);
                $id=$idquince[$i];
                $diario=Diario::findOrFail($quincena->diario_id);
                $general = new generalController();
                $cierre = $general->cierre($quincena->quincena_fecha,$diario->sucursal_id);
                if ($cierre) {
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
                
                $quincena->diario_id=null;
                $quincena->save();

                
                $quincena->delete();
                $general->registrarAuditoria('Eliminacion de la quincena: -> '.$id.'con empleado '.$quincena->empleado->emepleado_nombre, $id, 'Con quincena  id -> '.$id);
       
         
            }
            foreach ($diario->detalles as $i) {
                  
                if (isset($i->transferencia_id)) {
                    $detalle=Detalle_Diario::findOrFail($i->detalle_id);
                
                    $transferenciaAux=Transferencia::findOrFail($i->transferencia_id);

                    $detalle->transferencia_id=null;
                    $detalle->save();

                    $transferenciaAux->delete();
                    $general->registrarAuditoria('Eliminacion de Cheque numero: -> '.$transferenciaAux->transferencia_numero, $transferenciaAux->transferencia_id, 'Con Diario '.$diario->diario_id.' Con valor de -> '.$transferenciaAux->transferencia_valor);
                }
        

                $i->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion del detalle diario tipo documento numero: -> '.$i->detalle_tipo_documento,$diario->diario_id,'con codigo de diario'.$diario->diario_codigo);
            }
            $diario->delete();
            $general->registrarAuditoria('Eliminacion de Dario de quincena consolidada con empleado '.$quincena->empleado->emepleado_nombre, 0, '');
        
            DB::commit();
            return redirect('lquincena')->with('success','Datos Eliminados exitosamente');
        }catch(\Exception $ex){
            return redirect('lquincena')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }

    public function generar(Request $request)
    {
        try{
            DB::beginTransaction();
            $idEmpleado = $request->get('idquincena'); 
            $contador = $request->get('contador');
            $nombre = $request->get('Dnombre');
            $Squincena = $request->get('quincena');
          
            $general = new generalController();
            $fecha=$request->get('fecha')."-01";
            $dth = new DateTime($fecha);
            $fechafin=($dth->format('Y-m-t'));
            $total=0;
      
            $cierre = $general->cierre($fecha,$request->get('sucursal_id'));
            if($cierre){
                return redirect('quincenaConsolidada/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $urlcheque = '';
            $datos=null;
            for ($i = 0; $i < count($contador); ++$i) {
                if(count(Quincena::buscarquincena($idEmpleado[$contador[$i]],$fecha,$fechafin)->get())>0){
                    return redirect('quincenaConsolidada/new/'.$request->get('punto_id'))->with('error2','Ya existe la quincena de un empleado');
                }
            }
            $banco=Banco::findOrFail($request->get('banco_id'));
            $cuentabanco=Cuenta_Bancaria::findOrFail($request->get('cuenta_id'));
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
                
          
            if ($request->get('idTipo') == 'Cheque') {
            $numero=$request->get('idNcheque');
            }
            if ($request->get('idTipo') == 'Transferencia') {
                $general = new generalController();
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaactual'), 'CEQE');
                $diario->diario_fecha = $request->get('fechaactual');
                $diario->diario_referencia = 'COMPROBANTE DE EMISION DE QUINCENAS DE EMPLEADOS';
            
                $diario->diario_tipo_documento = 'TRANSFERENCIA BANCARIA';
                $diario->diario_numero_documento = 0;
            
                
          
                $diario->diario_beneficiario = "QUINCENA CONSOLIDADA DE EMPELADOS";
                $diario->diario_tipo = 'CEQE';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $fecha)->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE EMISION DE QUINCENAS CONSOLIDADAS DE EMPLEADOS';

                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id =  $request->get('sucursal_id');
                $diario->save();
            }
            $COUNT=1;
            for ($i = 0; $i < count($contador); ++$i) {
                $empleado = Empleado::EmpleadoById($idEmpleado[$contador[$i]])->first();
                $rangoDocumento=Rango_Documento::PuntoRango($request->get('punto_id'), 'Quincena')->first();   
                $secuencial=1;
                if ($rangoDocumento) {
                    $secuencial=$rangoDocumento->rango_inicio;
                    $secuencialAux=Quincena::secuencial($rangoDocumento->rango_id)->max('quincena_secuencial');
                    if ($secuencialAux) {
                        $secuencial=$secuencialAux+1;
                    }
                }

                $quincena = new Quincena();
                $quincena->quincena_fecha = $fecha;
                $quincena->quincena_tipo = $request->get('idTipo');
                $quincena->quincena_valor =$Squincena[$contador[$i]];
                $quincena->quincena_saldo = $Squincena[$contador[$i]];
                $total=$total+$Squincena[$contador[$i]];
                $quincena->quincena_descripcion =  'Quincena de Empleado : '.$nombre[$contador[$i]];
                $quincena->empleado_id =$idEmpleado[$contador[$i]];

                $quincena->quincena_numero = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $quincena->quincena_serie = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie;
                $quincena->quincena_secuencial = $secuencial;

                $quincena->rango_id = $rangoDocumento->rango_id;
                $quincena->quincena_estado = 1;
                /*REGISTRO DE CHQUE*/

                $datos[$COUNT]["cheque"]=0;
                if ($request->get('idTipo') == 'Cheque') {
                   
                    $general = new generalController();
                    $diario = new Diario();
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaactual'), 'CEQE');
                    $diario->diario_fecha = $request->get('fechaactual');
                    $diario->diario_referencia = 'COMPROBANTE DE EMISION DE QUINCENA DE EMPLEADO';
                
                    $diario->diario_tipo_documento = 'CHEQUE';
                    $diario->diario_numero_documento =$numero;
                                    
                    $diario->diario_beneficiario = $nombre[$contador[$i]];
                    $diario->diario_tipo = 'CEQE';
                    $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                    $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $fecha)->format('m');
                    $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
                    $diario->diario_comentario = 'COMPROBANTE DE EMISION DE QUINCENA  DE EMPLEADO '.$nombre[$contador[$i]];
    
                    $diario->diario_cierre = '0';
                    $diario->diario_estado = '1';
                    $diario->empresa_id = Auth::user()->empresa_id;
                    $diario->sucursal_id =  $request->get('sucursal_id');
                    $diario->save();

                    $formatter = new NumeroALetras();
                    $cheque = new Cheque();
                    $cheque->cheque_numero = $numero;
                    $cheque->cheque_descripcion =  'Quincena de Empleado : '.$nombre[$contador[$i]];
                    $cheque->cheque_beneficiario =  $nombre[$contador[$i]];
                    $cheque->cheque_fecha_emision = $request->get('fechaactual');
                    $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                    $cheque->cheque_valor = $Squincena[$contador[$i]];
                    $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                    $cheque->cuenta_bancaria_id = $request->get('cuenta_id');
                    $cheque->cheque_estado = '1';
                    $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                    $cheque->save();
                    $datos[$COUNT]["cheque"]=$cheque->cheque_id;
                    $numero=$numero+1;
                    $general->registrarAuditoria('Registro de Cheque numero: -> '.$request->get('idNcheque'), '0', 'Por motivo de: -> '. $quincena->quincena_descripcion.' con el valor de: -> '.$Squincena[$contador[$i]]);
                }
                /*REGISTRO DE TRANSFERENCIA*/
                
                $tipo=Empleado::EmpleadoBusquedaCuenta($idEmpleado[$contador[$i]],'quincena')->first();
                /**********************asiento diario****************************/
               
                   
                

                $quincena->diario()->associate($diario);
                $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, '0', 'Tipo de Diario -> '.$diario->diario_referencia.'');
                /********************detalle de diario de venta********************/
                if ($request->get('idTipo') == 'Cheque') {
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe =  0.00 ;
                    $detalleDiario->detalle_haber =$Squincena[$contador[$i]];
                   
                    $detalleDiario->detalle_comentario = 'Con Cheque No '.($numero-1);
               
                    $detalleDiario->detalle_tipo_documento = 'QUINCENA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $request->get('idCuentaContable');
                    $detalleDiario->cheque()->associate($cheque);
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$Squincena[$contador[$i]]);
            
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe =$Squincena[$contador[$i]];
                    $detalleDiario->detalle_haber =  0.00;
                    setlocale(LC_TIME, "es");
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE QUINCENA DE '.strtoupper(strftime("%B", strtotime($fecha))).' '.DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
                    $detalleDiario->detalle_tipo_documento = 'QUINCENA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                    $detalleDiario->empleado_id = $idEmpleado[$contador[$i]];
                    $diario->detalles()->save($detalleDiario);
                    
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Squincena[$contador[$i]]);

                }
                if ($request->get('idTipo') == 'Transferencia') {
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe =$Squincena[$contador[$i]];
                    $detalleDiario->detalle_haber =  0.00;
                    $detalleDiario->detalle_comentario = 'TRANSFERENCIA AL EMPLEADO '.$nombre[$contador[$i]].' CUENTA No '.$empleado->empleado_cuenta_numero;;
                    $detalleDiario->detalle_tipo_documento = 'QUINCENA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                    $detalleDiario->empleado_id = $idEmpleado[$contador[$i]];
                    $diario->detalles()->save($detalleDiario);
                    
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Squincena[$contador[$i]]);
                }
                
                $quincena->save();
                /*Inicio de registro de auditoria */
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de Quincena de Empleado -> '.$idEmpleado[$contador[$i]], '0', 'Con motivo:'. $quincena->quincena_descripcion);
                /*Fin de registro de auditoria */

                $datos[$COUNT]["id"]=$quincena->quincena_id;
                $datos[$COUNT]["nombre"]=$nombre[$contador[$i]];
                $datos[$COUNT]["fecha"]=$quincena->quincena_fecha;
                $datos[$COUNT]["valor"]=$quincena->quincena_valor;
                $datos[$COUNT]["tipo"]=$quincena->quincena_tipo;
                $datos[$COUNT]["descripcion"]=$quincena->quincena_descripcion;
                $datos[$COUNT]["estado"]=$quincena->quincena_estado;
                $COUNT++;
            }
            if ($request->get('idTipo') == 'Transferencia') {
                   

                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion =  'Quincena Consolida De empleados';
                $transferencia->transferencia_beneficiario =  'Quincena Consolida De empleados';
                $transferencia->transferencia_fecha = $request->get('idFechaCheque');
                $transferencia->transferencia_valor = $total;
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de Transferencia numero: -> '.$request->get('ncuenta'), '0', 'Por motivo de: -> '.$transferencia->transferencia_descripcion.' con el valor de: -> '.$total);
                
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe =  0.00 ;
                $detalleDiario->detalle_haber =$total;
                setlocale(LC_TIME, "es");
                $detalleDiario->detalle_comentario = 'P/R ANTICIPOS DE QUINCENAS DE '.strtoupper(strftime("%B", strtotime($fecha))).' '.DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
                $detalleDiario->detalle_tipo_documento = 'QUINCENA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cuenta_id = $request->get('idCuentaContable');
                $detalleDiario->transferencia()->associate($transferencia);
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$total);
                $url = $general->pdfDiarioEgreso($diario);
                DB::commit();
                return redirect('quincenaConsolidada/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('diario',$url);

            }
            DB::commit();
            return view('admin.recursosHumanos.quincenaConsolidada.impresion',['datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin])->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('quincenaConsolidada/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
   
    public function extraer(Request $request)
    {
        try {
            $fechainicio=($request->get('fecha').-'01');

            $fechafin=(date("Y-m-t", strtotime($fechainicio)));
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            $boole=1;
            $general = new generalController();
            $cierre = $general->cierre($request->get('fecha'),$request->get('sucursal_id'));
            
            if ($cierre) {
                return redirect('quincenaConsolidada')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }

            $rangoDocumento=Rango_Documento::PuntoRango($request->get('punto_id'), 'Quincena')->first();   
            $secuencial=1;
            if ($rangoDocumento) {
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Quincena::secuencial($rangoDocumento->rango_id)->max('quincena_secuencial');
                if ($secuencialAux) {
                    $secuencial=$secuencialAux+1;
                }
            }
           
            foreach (Empleado::EmpleadosRolSucursal($request->get('sucursal_id'))->get() as $empleado) {
                $boole=1;
                foreach (Quincena::BuscarQuincenasFecha($fechainicio,$fechafin)->get() as $quincena) {
                    if ($quincena->empleado_id == $empleado->empleado_id) {
                        $boole=0;
                    }
                } 
                if ($boole==1) {
                    $datos[$count]['count'] =$count-1;
                    $datos[$count]['ID'] =$empleado->empleado_id;
                    $datos[$count]['Dcedula'] =$empleado->empleado_cedula;
                    $datos[$count]['Dnombre'] =$empleado->empleado_nombre;
                    $datos[$count]['Dsueldo'] =$empleado->empleado_sueldo;
                    $datos[$count]['DCuenta'] =0;
                    if($empleado->empleado_cuenta_numero){
                        if (floatval($empleado->empleado_cuenta_numero)>0) {
                            $datos[$count]['DCuenta'] =1;
                        }
                    }
                    if ($empleado->empleado_quincena==0 || $empleado->empleado_quincena==null) {
                        $datos[$count]['quincena'] =round($empleado->empleado_sueldo/2,2);
                    }else{
                        $datos[$count]['quincena'] =$empleado->empleado_quincena;
                    }
                

                    $count++;
                }
            }
        
            return view('admin.recursosHumanos.quincenaConsolidada.view',['fecha'=>$request->get('fecha'),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),  'rangoDocumento'=>$rangoDocumento,'sucursales'=>Sucursal::sucursales()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'bancos'=>Banco::bancos()->get(),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
