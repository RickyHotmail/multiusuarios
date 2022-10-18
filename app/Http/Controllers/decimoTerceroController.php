<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cheque;
use App\Models\Decimo_Tercero;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Rol_Consolidado;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class decimoTerceroController extends Controller
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
            return view('admin.recursosHumanos.decimoTercero.index',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function ver($fecha)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            return view('admin.recursosHumanos.decimoTercero.impresion', ['decimo'=>Decimo_Tercero::ExtraerDecimoTercero($fecha)->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }

    public function imprimir($id)
    {   
        try{
            $decimo=Decimo_Tercero::decimo($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfTercero($decimo);
            return $url;
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function imprimirdiario($id)
    { 
        try{
            $decimo=Decimo_Tercero::decimo($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfDiariourl($decimo->diario);
            return $url;
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
            $datos = null;
            $fechadesde=$request->get('fecha_desde')."-01";
            $fechahasta=$request->get('fecha_hasta')."-31";
            $dt = new DateTime($fechadesde);
            $dth = new DateTime($fechahasta);
            $validacion=Decimo_Tercero::RolFecha($dth->format('d/m/Y'))->get();
            if(!$validacion->isEmpty()){
                return redirect('decimoT')->with('error','Ya se encuentra registrado el decimo tercero dentro de la fecha verifique por favor.');
            }else{
                
                $Roles=Rol_Consolidado::ExtraerDecimoTercero($dt->format('d/m/Y'),$dth->format('d/m/Y'))->groupBy('empleado.empleado_id')->selectRaw('sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_horas_suplementarias) as bonificaciones,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_sueldo) as sueldo,sum(detalle_rol.detalle_rol_dias) as dias,sum(detalle_rol.detalle_rol_decimo_terceroacum) as decimo, empleado.empleado_id,empleado.empleado_nombre,empleado.empleado_cedula')->get();
                $count = 1;   
                foreach ($Roles as $empleado) {
                    $datos[$count]['IDE'] =$empleado->empleado_id;
                    $datos[$count]['nombre'] =$empleado->empleado_nombre;
                    $datos[$count]['cedula'] =$empleado->empleado_cedula;
                    $datos[$count]['dias'] =$empleado->dias;
                    $datos[$count]['sueldo'] =$empleado->sueldo;
                    $datos[$count]['he'] =$empleado->extras;
                    $datos[$count]['bonificaciones'] =$empleado->bonificaciones;
                    $datos[$count]['otros'] =$empleado->otrosingresos;
                    $datos[$count]['decimo'] =$empleado->decimo;
                    $count++;
                }
            
            }
            return view('admin.recursosHumanos.decimoTercero.index',['fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
          
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function enviar(Request $request)
    {
        try{
            
            DB::beginTransaction();
            $urlcheque = '';
            $id = $request->get('ide');
            $valor = $request->get('decimo');
            $nombre = $request->get('nombre');
            $fechadesde=$request->get('fecha_desde')."-01";
            $fechahasta=$request->get('fecha_hasta')."-31";
            $ncheque=($request->get('idNcheque'));
            $dt = new DateTime($fechadesde);
            $dth = new DateTime($fechahasta);
            $general = new generalController();
            
            for ($i = 0; $i < count($id); ++$i) {
                $tipo=Empleado::EmpleadoBusquedaCuenta($id[$i],'decimoTercero')->first();
                $cierre = $general->cierre($request->get('idFechaemision'),$tipo->sucursal_id);          
                if($cierre){
                    return redirect('decimoT')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                } 
                $decimo =new    Decimo_Tercero();
                $decimo->decimo_fecha = $dth->format('d/m/Y');
                $decimo->decimo_fecha_emision = $request->get('idFechaemision');
                $decimo->decimo_tipo = $request->get('idTipo');  
                $decimo->decimo_periodo ='Periodo desde '.DateTime::createFromFormat('Y-m-d', ($dt->format('Y-m-d')))->format('m').' - '.DateTime::createFromFormat('Y-m-d', ($dt->format('Y-m-d')))->format('Y').' hasta '.DateTime::createFromFormat('Y-m-d', ($dth->format('Y-m-d')))->format('m').' - '.DateTime::createFromFormat('Y-m-d', ($dth->format('Y-m-d')))->format('Y');         
                $decimo->decimo_valor = $valor[$i];
                $decimo->decimo_descripcion =  'Pago de decimo tercero acumulado';
                $decimo->empleado_id = $id[$i];      
                $decimo->decimo_estado = 1;   
                
                /*REGISTRO DE DECHQUE*/            
                if ($request->get('idTipo') == 'Cheque'){      
                    $formatter = new NumeroALetras();
                    //echo $formatter->toWords($number, $decimals);
                    $cheque = new Cheque();
                    $cheque->cheque_numero = $ncheque;
                    $cheque->cheque_descripcion =  $request->get('descripcion');
                    $cheque->cheque_beneficiario = $request->get('idBeneficiario');
                    $cheque->cheque_fecha_emision = $request->get('idFechaemision');
                    $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                    $cheque->cheque_valor = $request->get('idValor');
                    $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                    $cheque->cuenta_bancaria_id = $request->get('cuenta_id');      
                    $cheque->cheque_estado = '1';
                    $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                    $cheque->save();
                    $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_id'),$cheque);
                    $ncheque++;
                    $general->registrarAuditoria('Registro de Cheque numero decimo tercero: -> '.$request->get('idNcheque'),'0','Por motivo de: -> '. $decimo->decimo_descripcion.' con el valor de: -> '.$valor[$i]);
                } 
                if ($request->get('idTipo') == 'Transferencia'){       
                    $transferencia = new Transferencia();
                    $transferencia->transferencia_descripcion =  $request->get('descripcion');
                    $transferencia->transferencia_beneficiario = $request->get('nempleado');
                    $transferencia->transferencia_fecha = $request->get('idFechaemision');
                    $transferencia->transferencia_valor = $request->get('idValor');
                    $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');      
                    $transferencia->transferencia_estado = '1';
                    $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                    $transferencia->save();
                    $general->registrarAuditoria('Registro de Transferencia numero: -> '.$request->get('ncuenta'),'0','Por motivo de: -> '. $decimo->decimo_descripcion.' con el valor de: -> '.$valor[$i]);
                }    

              
                /**********************asiento diario****************************/
                $general = new generalController();
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario(($request->get('idFechaemision')),'CPDT');
                $diario->diario_fecha = $request->get('idFechaemision');
                $diario->diario_referencia = 'COMPROBANTE DE PAGO DE DECIMO TERCERO';
                $diario->diario_tipo_documento = 'DECIMO TERCERO';
                $diario->diario_numero_documento = 0;
                $diario->diario_beneficiario = $nombre[$i];    
                $diario->diario_tipo = 'CPDT';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', ($request->get('idFechaemision')))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', ($request->get('idFechaemision')))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE PAGO DE DECIMO TERCERO: '.$nombre[$i];    
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id =  $tipo->sucursal_id;
                $diario->save();
                $decimo->diario()->associate($diario);
                
                $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
                /********************detalle de diario de venta********************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe =  0.00 ;
                $detalleDiario->detalle_haber =$valor[$i];
                $detalleDiario->detalle_comentario = 'PAGO DE DECIMO TERCERO DE EMPLEADO -> '.$id[$i];
                $detalleDiario->detalle_tipo_documento = 'DECIMO TERCERO';
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
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$valor[$i]);
                
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe =$valor[$i];
                $detalleDiario->detalle_haber =  0.00;
                $detalleDiario->detalle_comentario = 'PAGO DE DECIMO TERCERO DE EMPLEADO -> '.$id[$i];
                $detalleDiario->detalle_tipo_documento = 'DECIMO TERCERO';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cuenta_id = $tipo->cuenta_debe;   
                $detalleDiario->empleado_id = $id[$i];        
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$valor[$i]);  
                $decimo->save();
                /*Inicio de registro de auditoria */
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de decimo de Empleado -> '.$request->get('idEmpleado'),'0','Con motivo:'. $decimo->decimo_descripcion);
            } 
            $url = $general->pdfDiario($diario);
            if ($request->get('idTipo') == "Cheque") {
                DB::commit();
                return redirect('decimoTercero/'.$dth->format('d-m-Y'))->with('success','Datos guardados exitosamente')->with('diario',$url)->with('cheque',$urlcheque);
            }  
            DB::commit(); 
            return redirect('decimoTercero/'.$dth->format('d-m-Y'))->with('success','Datos guardados exitosamente')->with('diario',$url);

        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('decimoT')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
