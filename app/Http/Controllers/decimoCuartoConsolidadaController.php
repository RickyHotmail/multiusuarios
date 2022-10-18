<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Cheque;
use App\Models\Cuenta;
use App\Models\Decimo_Cuarto;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class decimoCuartoConsolidadaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    
    public function nuevo($id){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Decimo')->first();
            if($rangoDocumento){
            return view('admin.recursosHumanos.decimoCuarto.index',['empleados'=>Empleado::EmpleadosRolSucursal($rangoDocumento->puntoEmision->sucursal_id)->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'bancos'=>Banco::bancos()->get(),  'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]); 
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
        if (isset($_POST['enviar'])){
            return $this->enviar($request);
        }
        if (isset($_POST['extraer'])){
          
            return $this->extraer($request);
 
        }
    }
    public function extraer(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($request->get('punto_id'), 'Rol')->first();   
            $datos = null;
            $fechadesde=$request->get('fecha_desde')."-01";
            $fechahasta=$request->get('fecha_hasta')."-31";
            $dt = new DateTime($fechadesde);
            $dth = new DateTime($fechahasta);
            /*
                $Roles=Rol_Consolidado::ExtraerDecimoCuarto($dt->format('d/m/Y'),$dth->format('d/m/Y'))->groupBy('empleado.empleado_id')->selectRaw('sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_horas_suplementarias) as bonificaciones,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_sueldo) as sueldo,sum(detalle_rol.detalle_rol_dias) as dias,sum(detalle_rol.detalle_rol_decimo_terceroacum) as decimo, empleado.empleado_id,empleado.empleado_nombre,empleado.empleado_cedula')->get();
                if(count($Roles)==0){
                    $Roles=Cabecera_Rol_CM::ExtraerDecimoCuarto($dt->format('d/m/Y'),$dth->format('d/m/Y'),$request->get('sucursal_id'))->groupBy('empleado.empleado_id','rubro.rubro_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as rubrovalor, rubro.rubro_nombre, rubro.rubro_id,empleado.empleado_id,empleado.empleado_nombre,empleado.empleado_cedula')->get();
                }
            */
            $Roles=Empleado::EmpleadosBySucursal($request->get('sucursal_id'))->get();
            $count = 1;
            $emplead=Decimo_Cuarto::ExtraerDecimoCuarto($fechadesde,$request->get('sucursal_id'))->get();
          
            foreach ($Roles as $empleado) {
                $activa=false;
                foreach ($emplead as $emplea) {
                    if($emplea->empleado_id==$empleado->empleado_id){
                        $activa=true;
                    }
                }
                if ($activa==false) {
                    $datos[$count]['count'] =$count-1;
                    $datos[$count]['IDE'] =$empleado->empleado_id;
                    $datos[$count]['nombre'] =$empleado->empleado_nombre;
                    $datos[$count]['cedula'] =$empleado->empleado_cedula;
                    /*
                    $datos[$count]['dias'] =$empleado->dias;
                    $datos[$count]['sueldo'] =$empleado->sueldo;
                    $datos[$count]['he'] =$empleado->extras;
                    $datos[$count]['bonificaciones'] =$empleado->bonificaciones;
                    $datos[$count]['otros'] =$empleado->otrosingresos;
                    */
                    $datos[$count]['decimo'] =0;
                    $count++;
                }
            }
        
            
            return view('admin.recursosHumanos.decimoCuarto.index',['rangoDocumento'=>$rangoDocumento,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function enviar(Request $request)
    {
        
        try {
            DB::beginTransaction();
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
          
           
            $urlcheque = '';
            $total=0;
            $datos=null;
            $contador = $request->get('contador');
            $id = $request->get('ide');
            $valor = $request->get('decimo');
            
            $nombre = $request->get('nombre');
            $fechadesde=$request->get('fecha_desde')."-01";
            
            $fechahasta=$request->get('fecha_hasta')."-01";
            
            $dt = new DateTime($fechadesde);
            $dth = new DateTime($fechahasta);
            $fechahasta=($dth->format('Y-m-t'));
            $general = new generalController();
            $cierre = $general->cierre($request->get('idFechaemision'), $request->get('sucursal_id'));
            setlocale(LC_TIME, "es");
            if ($cierre) {
                return redirect('decimoC/new/'.$request->get('punto_id'))->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $COUNT=1;
            if ($request->get('idTipo') == 'Cheque') {
                $ncheque=$request->get('idNcheque');
            }
            if ($request->get('idTipo') == 'Transferencia') {
                $general = new generalController();
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario(($request->get('idFechaemision')), 'CPDC');
                $diario->diario_fecha = $request->get('idFechaemision');
                $diario->diario_referencia = 'COMPROBANTE DE EMISION DE DECIMO CUARTO DE EMPLEADOS';
                
                $diario->diario_tipo_documento = 'TRANSFERENCIA BANCARIA';
                $diario->diario_numero_documento = 0;
                $diario->diario_beneficiario = "DECIMO CUARTO CONSOLIDADO DE EMPELADOS";
                $diario->diario_tipo = 'CPDC';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFechaemision'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFechaemision'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE EMISION DEL DECIMO CUARTO CONSOLIDADO DE EMPLEADOS';
    
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id =  $request->get('sucursal_id');
                $diario->save();
            }
            for ($i = 0; $i < count($contador); ++$i) {
                if ($valor[$contador[$i]]>0) {
                    $empleado=Empleado::findOrFail($id[$contador[$i]]);
                    $decimo =new Decimo_Cuarto();
                    $decimo->decimo_fecha_emision = $request->get('idFechaemision');
                    $decimo->decimo_fecha = $dt->format('d/m/Y');
                    $decimo->decimo_tipo = $request->get('idTipo');
                    $decimo->decimo_periodo ='Periodo desde '.DateTime::createFromFormat('Y-m-d', ($dt->format('Y-m-d')))->format('m').' - '.DateTime::createFromFormat('Y-m-d', ($dt->format('Y-m-d')))->format('Y').' hasta '.DateTime::createFromFormat('Y-m-d', ($dth->format('Y-m-d')))->format('m').' - '.DateTime::createFromFormat('Y-m-d', ($dth->format('Y-m-d')))->format('Y');
                    $decimo->decimo_valor = $valor[$contador[$i]];
                    $decimo->decimo_descripcion =  'Pago de decimo cuarto acumulado';
                    $decimo->empleado_id = $id[$contador[$i]];
                    $decimo->decimo_estado = 1;
                    $total=$total+$valor[$contador[$i]];
                    $tipo=Empleado::EmpleadoBusquedaCuenta($id[$contador[$i]], 'decimoCuarto')->first();
               
                    /*REGISTRO DE DECHQUE*/
               
                    if ($request->get('idTipo') == 'Cheque') {
                        $formatter = new NumeroALetras();
                        $cheque = new Cheque();
                        $cheque->cheque_numero = $ncheque;
                        $cheque->cheque_descripcion =  'PAGO DE DECIMO CUARTO: '.$nombre[$contador[$i]];
                        $cheque->cheque_beneficiario = $nombre[$contador[$i]];
                        $cheque->cheque_fecha_emision = $request->get('idFechaemision');
                        $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                        $cheque->cheque_valor = $valor[$contador[$i]];
                        $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                        $cheque->cuenta_bancaria_id = $request->get('cuenta_id');
                        $cheque->cheque_estado = '1';
                        $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                   
                        $cheque->save();
                     
                        $general->registrarAuditoria('Registro de Cheque numero de decimo cuarto: -> '.$request->get('idNcheque'), '0', 'Por motivo de: -> '.$decimo->decimo_descripcion.' con el valor de: -> '.$valor[$contador[$i]]);
                       

                        /**********************asiento diario****************************/
                        $general = new generalController();
                        $diario = new Diario();
                        $diario->diario_codigo = $general->generarCodigoDiario(($request->get('idFechaemision')), 'CPDC');
                        $diario->diario_fecha = $request->get('idFechaemision');
                        $diario->diario_referencia = 'COMPROBANTE DE PAGO DE DECIMO CUARTO';
                        $diario->diario_tipo_documento = 'DECIMO CUARTO';

                        $diario->diario_numero_documento =$ncheque;
                        $diario->diario_beneficiario = $nombre[$i];
                        $diario->diario_tipo = 'CPDC';
                        $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                        $diario->diario_mes = DateTime::createFromFormat('Y-m-d', ($request->get('idFechaemision')))->format('m');
                        $diario->diario_ano = DateTime::createFromFormat('Y-m-d', ($request->get('idFechaemision')))->format('Y');
                        $diario->diario_comentario = 'COMPROBANTE DE EMISION DEL DECIMO CUARTO DEL EMPLEADO '.$nombre[$contador[$i]];
                        $diario->diario_cierre = '0';
                        $diario->diario_estado = '1';
                        $diario->empresa_id = Auth::user()->empresa_id;
                        $diario->sucursal_id =  $tipo->sucursal_id;
                   
                        $diario->save();
                   
                   
                        $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, '0', 'Tipo de Diario -> '.$diario->diario_referencia.'');
                        /********************detalle de diario de venta********************/

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =  0.00 ;
                        $detalleDiario->detalle_haber =$valor[$contador[$i]];
                        $detalleDiario->detalle_comentario = 'CON CHEQUE N° '.$ncheque;
                        $detalleDiario->detalle_tipo_documento = 'DECIMO CUARTO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $request->get('idCuentaContable');
                        $detalleDiario->cheque()->associate($cheque);
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$valor[$contador[$i]]);
                    
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =$valor[$contador[$i]];
                        $detalleDiario->detalle_haber =  0.00;
                        $detalleDiario->detalle_comentario = 'P/R PAGO DEL DECIMO CUARTO DESDE '.strtoupper(strftime("%B", strtotime($fechadesde))).' '.DateTime::createFromFormat('Y-m-d', $fechadesde)->format('Y').' HASTA '.strtoupper(strftime("%B", strtotime($fechahasta))).' '.DateTime::createFromFormat('Y-m-d', $fechahasta)->format('Y');
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_tipo_documento = 'DECIMO CUARTO';
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $id[$i];
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$valor[$contador[$i]]);
                       
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de decimo de Empleado -> '.$request->get('idEmpleado'), '0', 'Con motivo:'. $decimo->decimo_descripcion);
                       
                        $datos[$COUNT]["nombre"]=$nombre[$contador[$i]];
                        $datos[$COUNT]["fecha"]=$decimo->decimo_fecha;
                        $datos[$COUNT]["tipo"]=$decimo->decimo_tipo;
                        $datos[$COUNT]["valor"]=$decimo->decimo_valor;
                        $datos[$COUNT]["descripcion"]=$decimo->decimo_descripcion;
                        $datos[$COUNT]["diario"]=$diario->diario_id;
                        $datos[$COUNT]["cheque"]=$cheque->cheque_id;
                        $datos[$COUNT]["ncheque"]=$ncheque;
                        $ncheque++;
                       
                        $decimo->diario()->associate($diario);
                        $decimo->save();
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de Decimo Cuarto de Empleado -> '.$empleado->empleado_nombre, '0', 'Con Valor:'. $valor[$contador[$i]]);
                        $datos[$COUNT]["id"]=$decimo->decimo_id;
                        $COUNT++;
                    }
                    if ($request->get('idTipo') == 'Transferencia') {
                        $datos[$COUNT]["nombre"]=$nombre[$contador[$i]];
                        $datos[$COUNT]["fecha"]=$decimo->decimo_fecha;
                        $datos[$COUNT]["tipo"]=$decimo->decimo_tipo;
                        $datos[$COUNT]["valor"]=$decimo->decimo_valor;
                        $datos[$COUNT]["descripcion"]=$decimo->decimo_descripcion;
                        $datos[$COUNT]["diario"]=$diario->diario_id;
                        $datos[$COUNT]["cheque"]='';
                        $datos[$COUNT]["ncheque"]='';
                
                        
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =$valor[$contador[$i]];
                        $detalleDiario->detalle_haber =  0.00;
                        $detalleDiario->detalle_comentario = 'TRANSFERENCIA AL EMPLEADO '.$nombre[$contador[$i]].' CUENTA No '.$empleado->empleado_cuenta_numero;
                        
                        $detalleDiario->detalle_tipo_documento = 'DECIMO CUARTO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $id[$contador[$i]];
                        $diario->detalles()->save($detalleDiario);
                    
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$valor[$i]);
                        $decimo->diario()->associate($diario);
                        $decimo->save();
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de Decimo Cuarto de Empleado -> '.$empleado->empleado_nombre, '0', 'Con Valor:'. $valor[$contador[$i]]);
                        $datos[$COUNT]["id"]=$decimo->decimo_id;
                        $COUNT++;
                    }
                }
            }
           
           
            if ($request->get('idTipo') == 'Transferencia') {
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion = 'PAGO DE DECIMO CUARTO CONSOLIDADO';
                $transferencia->transferencia_beneficiario ='PAGO DE DECIMO CUARTO CONSOLIDADO';
                $transferencia->transferencia_fecha = $request->get('idFechaemision');
                $transferencia->transferencia_valor = $total;
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de Transferencia decimo cuarto numero: -> '.$request->get('ncuenta'), '0', 'Por motivo de: -> '. $decimo->decimo_descripcion.' con el valor de: -> '.$total);


               
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe =  0.00 ;
                $detalleDiario->detalle_haber =$total;
                setlocale(LC_TIME, "es");
                $detalleDiario->detalle_comentario = 'P/R PAGO DEL DECIMO CUARTO DESDE '.strtoupper(strftime("%B", strtotime($fechadesde))).' '.DateTime::createFromFormat('Y-m-d', $fechadesde)->format('Y').' HASTA '.strtoupper(strftime("%B", strtotime($fechahasta))).' '.DateTime::createFromFormat('Y-m-d', $fechahasta)->format('Y');
                $detalleDiario->detalle_tipo_documento = 'DECIMO CUARTO';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cuenta_id = $request->get('idCuentaContable');
                $detalleDiario->transferencia()->associate($transferencia);
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$total);
                $url = $general->pdfDiarioEgreso($diario);
                DB::commit(); 
                return view('admin.recursosHumanos.decimoCuarto.impresion', ['datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin])->with('success', 'Datos guardados exitosamente')->with('diario', $url);
            }
            if ($request->get('idTipo') == 'Cheque') {
                DB::commit(); 
                return view('admin.recursosHumanos.decimoCuarto.impresion', ['datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin])->with('success', 'Datos guardados exitosamente');
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('decimoC/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function destroy($id)
    {
        //
    }
}
