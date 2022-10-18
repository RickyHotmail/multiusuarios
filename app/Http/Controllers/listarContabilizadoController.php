<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cabecera_Rol_CM;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Rol_Consolidado;
use App\Models\Tipo_Empleado;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use App\NEOPAGUPA\ViewExcel;
use Excel;

class listarContabilizadoController extends Controller
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
            return view('admin.recursosHumanos.contabilizacionMensual.index',[ 'diarios'=>null,'fechames'=>null,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){

            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
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

    public function extraer(Request $request)
    {
        if (isset($_POST['pdf'])){
            return $this->pdf($request);
        }
        if (isset($_POST['excel'])){
            return $this->excel($request);
        }
    }
    public function pdf(Request $request){
        try{ 
            $datos=null;
            
            $existe=0;
            $rol=Rol_Consolidado::buscarrolContabilisadodiario($request->get('diario'))->groupBy('cabecera_rol_total_anticipos')->groupBy('empleado.empleado_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->groupBy('cabecera_rol.cabecera_rol_id')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.empleado_id,empleado.empleado_nombre,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula,cabecera_rol.cabecera_rol_id as cabecera_id')->get(); 
            $tipo=Rol_Consolidado::buscarrolContabilisadotipodiario($request->get('diario'))->groupBy('cabecera_rol_total_anticipos')->groupBy('tipo_empleado.tipo_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos,tipo_empleado.tipo_id,tipo_empleado.tipo_descripcion,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula')->get(); 
            
            $tipos=Tipo_Empleado::Tipos()->get();
            $count=1;
            foreach($tipos as $tip){
                $sucursal_id=$tip->sucursal_id;
            }
           
            
            foreach($tipo as $roles){
                foreach ($tipos as $tiposroles) {    
                    if($tiposroles->tipo_id==$roles->tipo_id){
                        $existe=0;  
                        if ($datos!=null) {
                            for ($i = 1; $i <= count($datos); $i++) {
                                if ($datos[$i]["tipo"]==$tiposroles->tipo_descripcion) {
                                    $datos[$i]["sueldos"]=$datos[$i]["sueldos"]+ $roles->sueldos;
                                    $datos[$i]["vacacionesacu"]=$datos[$i]["vacacionesacu"]+$roles->vacacionespag;
                                    $datos[$i]["otrosingresos"]=$datos[$i]["otrosingresos"]+ $roles->otrosingresos;
                                    $datos[$i]["transporte"]=$datos[$i]["transporte"]+ $roles->transporte;
                                    $datos[$i]["extras"]=$datos[$i]["extras"]+ $roles->extras;
                                    $datos[$i]["otrabonifi"]=$datos[$i]["otrabonifi"]+ $roles->otrabonifi;
                                    $datos[$i]["ingresos"]=$datos[$i]["ingresos"]+ $roles->ingresos;
                                    $datos[$i]["extsalud"]=$datos[$i]["extsalud"]+ $roles->extsalud;
                                    $datos[$i]["leysal"]=$datos[$i]["leysal"]+ $roles->leysal;
                                    $datos[$i]["vacaciones"]=$datos[$i]["vacaciones"]+ $roles->vacaciones;
                                    $datos[$i]["comisariato"]=$datos[$i]["comisariato"]+ $roles->comisariato;
                                    $datos[$i]["ppqq"]=$datos[$i]["ppqq"]+ $roles->ppqq;
                                    $datos[$i]["hipoteca"]=$datos[$i]["hipoteca"]+ $roles->hipoteca;
                                    $datos[$i]["multas"]=$datos[$i]["multas"]+ $roles->multas;
                                    $datos[$i]["asumido"]=$datos[$i]["asumido"]+ $roles->asumido;
                                    $datos[$i]["personal"]=$datos[$i]["personal"]+ $roles->iess;
                                    //$datos[$i]["personal"]=$datos[$i]["personal"]+ $roles->personal;
                                    $datos[$i]["patronal"]=$datos[$i]["patronal"]+ $roles->patronal;
                                    $datos[$i]["anticipo"]=$datos[$i]["anticipo"]+ $roles->anticipo;
                                    $datos[$i]["impu_renta"]=$datos[$i]["impu_renta"]+ $roles->impu_renta;
                                    $datos[$i]["otrosegre"]=$datos[$i]["otrosegre"]+ $roles->otrosegre;
                                    $datos[$i]["egresos"]=$datos[$i]["egresos"]+ $roles->egresos;
                                    $datos[$i]["terceroacu"]=$datos[$i]["terceroacu"]+ $roles->terceroacum;
                                    $datos[$i]["tercero"]=$datos[$i]["tercero"]+ $roles->tercero;
                                    $datos[$i]["cuarto"]=$datos[$i]["cuarto"]+ $roles->cuarto;
                                    $datos[$i]["cuartoACU"]=$datos[$i]["cuartoacu"]+ $roles->cuartoacum;
                                    $datos[$i]["fondo_reservaacu"]=$datos[$i]["fondo_reservaacu"]+ $roles->fondoacumula;
                                    $datos[$i]["fondo_reserva"]=$datos[$i]["fondo_reserva"]+ $roles->fondo_reserva;
                                    $datos[$i]["iecesecap"]=$datos[$i]["iecesecap"]+ $roles->iecesecap;
                                    $datos[$i]["liquido_pagar"]=$datos[$i]["liquido_pagar"]+ $roles->liquido_pagar;
                                    $existe=1;
                                }
                            }
                        }
                        
                        if($existe==0){
                            $datos[$count]["idtipo"]=$tiposroles->tipo_id;
                            $datos[$count]["tipo"]=$tiposroles->tipo_descripcion;
                            $datos[$count]["sueldos"]=$roles->sueldos;
                            $datos[$count]["otrosingresos"]=$roles->otrosingresos;
                            $datos[$count]["vacacionesacu"]=$roles->vacacionespag;
                            $datos[$count]["transporte"]=$roles->transporte;
                            $datos[$count]["extras"]=$roles->extras;
                            $datos[$count]["otrabonifi"]= $roles->otrabonifi;
                            $datos[$count]["ingresos"]=$roles->ingresos;
                            $datos[$count]["extsalud"]= $roles->extsalud;
                            $datos[$count]["leysal"]=$roles->leysal;
                            $datos[$count]["vacaciones"]= $roles->vacaciones;
                            $datos[$count]["comisariato"]= $roles->comisariato;
                            $datos[$count]["ppqq"]= $roles->ppqq;
                            $datos[$count]["hipoteca"]=$roles->hipoteca;
                            $datos[$count]["multas"]=$roles->multas;
                            $datos[$count]["asumido"]=$roles->asumido;
                            $datos[$count]["aporte"]=$roles->aporte;
                            $datos[$count]["personal"]=$roles->iess;
                            //$datos[$count]["personal"]=$roles->personal;
                            $datos[$count]["patronal"]=$roles->patronal;
                            $datos[$count]["anticipo"]=$roles->anticipo;
                            $datos[$count]["impu_renta"]=$roles->impu_renta;
                            $datos[$count]["otrosegre"]=$roles->otrosegre;
                            $datos[$count]["egresos"]=$roles->egresos;
                            $datos[$count]["tercero"]=$roles->tercero;
                            $datos[$count]["cuarto"]=$roles->cuarto;
                            $datos[$count]["terceroacu"]=$roles->terceroacum;
                            $datos[$count]["cuartoacu"]=$roles->cuartoacum;
                            $datos[$count]["fondo_reservaacu"]=$roles->fondoacumula;
                            $datos[$count]["fondo_reserva"]=$roles->fondo_reserva;
                            $datos[$count]["iecesecap"]=$roles->iecesecap;
                            $datos[$count]["liquido_pagar"]=$roles->liquido_pagar;
                            $count++;
                        }
                        
                    }
                }    
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.rolcontabilizado', ['rol'=>$rol,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'empresa'=>$empresa]);
            $nombreArchivo = 'reporterolcontabilizado';
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');

        }catch(\Exception $ex){
            return redirect('listarContabilizado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excel(Request $request){
        try{ 
            $matriz=null;
            
            $existe=0;
            $rol=Rol_Consolidado::buscarrolContabilisadodiario($request->get('diario'))->groupBy('cabecera_rol_total_anticipos')->groupBy('empleado.empleado_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->groupBy('cabecera_rol.cabecera_rol_id')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.empleado_id,empleado.empleado_nombre,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula,cabecera_rol.cabecera_rol_id as cabecera_id')->get(); 
            $tipo=Rol_Consolidado::buscarrolContabilisadotipodiario($request->get('diario'))->groupBy('cabecera_rol_total_anticipos')->groupBy('tipo_empleado.tipo_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos,tipo_empleado.tipo_id,tipo_empleado.tipo_descripcion,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula')->get(); 
            $tipos=Tipo_Empleado::Tipos()->get();
            $count=1;
            foreach($tipos as $tip){
                $sucursal_id=$tip->sucursal_id;
            }
           
            
            foreach($tipo as $roles){
                foreach ($tipos as $tiposroles) {    
                    if($tiposroles->tipo_id==$roles->tipo_id){
                        $existe=0;  
                        if ($matriz!=null) {
                            for ($i = 1; $i <= count($matriz); $i++) {
                               
                                if ($matriz[$i]["tipo"]==$tiposroles->tipo_descripcion) {
                                    
                                    $matriz[$i]["sueldos"]=$matriz[$i]["sueldos"]+ $roles->sueldos;
                                    $matriz[$i]["vacacionesacu"]=$matriz[$i]["vacacionesacu"]+$roles->vacacionespag;
                                    $matriz[$i]["otrosingresos"]=$matriz[$i]["otrosingresos"]+ $roles->otrosingresos;
                                    $matriz[$i]["transporte"]=$matriz[$i]["transporte"]+ $roles->transporte;
                                    $matriz[$i]["extras"]=$matriz[$i]["extras"]+ $roles->extras;
                                    $matriz[$i]["otrabonifi"]=$matriz[$i]["otrabonifi"]+ $roles->otrabonifi;
                                    $matriz[$i]["ingresos"]=$matriz[$i]["ingresos"]+ $roles->ingresos;
                                    $matriz[$i]["extsalud"]=$matriz[$i]["extsalud"]+ $roles->extsalud;
                                    $matriz[$i]["leysal"]=$matriz[$i]["leysal"]+ $roles->leysal;
                                    $matriz[$i]["vacaciones"]=$matriz[$i]["vacaciones"]+ $roles->vacaciones;
                                    $matriz[$i]["comisariato"]=$matriz[$i]["comisariato"]+ $roles->comisariato;
                                    $matriz[$i]["ppqq"]=$matriz[$i]["ppqq"]+ $roles->ppqq;
                                    $matriz[$i]["hipoteca"]=$matriz[$i]["hipoteca"]+ $roles->hipoteca;
                                    $matriz[$i]["multas"]=$matriz[$i]["multas"]+ $roles->multas;
                                    $matriz[$i]["asumido"]=$matriz[$i]["asumido"]+ $roles->asumido;
                                    $matriz[$i]["personal"]=$matriz[$i]["personal"]+ $roles->iess;
                                    //$matriz[$i]["personal"]=$matriz[$i]["personal"]+ $roles->personal;
                                    $matriz[$i]["patronal"]=$matriz[$i]["patronal"]+ $roles->patronal;
                                    $matriz[$i]["anticipo"]=$matriz[$i]["anticipo"]+ $roles->anticipo;
                                    $matriz[$i]["impu_renta"]=$matriz[$i]["impu_renta"]+ $roles->impu_renta;
                                    $matriz[$i]["otrosegre"]=$matriz[$i]["otrosegre"]+ $roles->otrosegre;
                                    $matriz[$i]["egresos"]=$matriz[$i]["egresos"]+ $roles->egresos;
                                    $matriz[$i]["terceroacu"]=$matriz[$i]["terceroacu"]+ $roles->terceroacum;
                                    $matriz[$i]["tercero"]=$matriz[$i]["tercero"]+ $roles->tercero;
                                    $matriz[$i]["cuarto"]=$matriz[$i]["cuarto"]+ $roles->cuarto;
                                    $matriz[$i]["cuartoACU"]=$matriz[$i]["cuartoacu"]+ $roles->cuartoacum;
                                    $matriz[$i]["fondo_reservaacu"]=$matriz[$i]["fondo_reservaacu"]+ $roles->fondoacumula;
                                    $matriz[$i]["fondo_reserva"]=$matriz[$i]["fondo_reserva"]+ $roles->fondo_reserva;
                                    $matriz[$i]["iecesecap"]=$matriz[$i]["iecesecap"]+ $roles->iecesecap;
                                    $matriz[$i]["liquido_pagar"]=$matriz[$i]["liquido_pagar"]+ $roles->liquido_pagar;
                                    $existe=1;
                                }
                            }
                        }
                        
                        if($existe==0){
                            $matriz[$count]["idtipo"]=$tiposroles->tipo_id;
                            $matriz[$count]["tipo"]=$tiposroles->tipo_descripcion;
                            $matriz[$count]["sueldos"]=$roles->sueldos;
                            $matriz[$count]["otrosingresos"]=$roles->otrosingresos;
                            $matriz[$count]["vacacionesacu"]=$roles->vacacionespag;
                            $matriz[$count]["transporte"]=$roles->transporte;
                            $matriz[$count]["extras"]=$roles->extras;
                            $matriz[$count]["otrabonifi"]= $roles->otrabonifi;
                            $matriz[$count]["ingresos"]=$roles->ingresos;
                            $matriz[$count]["extsalud"]= $roles->extsalud;
                            $matriz[$count]["leysal"]=$roles->leysal;
                            $matriz[$count]["vacaciones"]= $roles->vacaciones;
                            $matriz[$count]["comisariato"]= $roles->comisariato;
                            $matriz[$count]["ppqq"]= $roles->ppqq;
                            $matriz[$count]["hipoteca"]=$roles->hipoteca;
                            $matriz[$count]["multas"]=$roles->multas;
                            $matriz[$count]["asumido"]=$roles->asumido;
                            $matriz[$count]["aporte"]=$roles->aporte;
                            
                            $matriz[$count]["personal"]=$roles->iess;
                           // $matriz[$count]["personal"]=$roles->personal;
                            $matriz[$count]["patronal"]=$roles->patronal;
                            $matriz[$count]["anticipo"]=$roles->anticipo;
                            $matriz[$count]["impu_renta"]=$roles->impu_renta;
                            $matriz[$count]["otrosegre"]=$roles->otrosegre;
                            $matriz[$count]["egresos"]=$roles->egresos;
                            $matriz[$count]["tercero"]=$roles->tercero;
                            $matriz[$count]["cuarto"]=$roles->cuarto;
                            $matriz[$count]["terceroacu"]=$roles->terceroacum;
                            $matriz[$count]["cuartoacu"]=$roles->cuartoacum;
                            $matriz[$count]["fondo_reservaacu"]=$roles->fondoacumula;
                            $matriz[$count]["fondo_reserva"]=$roles->fondo_reserva;
                            $matriz[$count]["iecesecap"]=$roles->iecesecap;
                            $matriz[$count]["liquido_pagar"]=$roles->liquido_pagar;
                            $count++;
                        }
                        
                    }
                }    
            }
            foreach(Rol_Consolidado::buscarrolContabilisadodiario($request->get('diario'))->get() as $roles){
               $fecha= $roles->cabecera_rol_fecha;
            }
            
            $datos[1]=$rol;
            $datos[2]=$matriz;
            $fechaComoEntero = strtotime($fecha);
            $fechaComoEntero =date("Y", $fechaComoEntero).'-'.date("m", $fechaComoEntero).'-01';
            $datos[3]=$fechaComoEntero;
            $L = new DateTime( $fechaComoEntero); 
            $datos[4]=$L->format( 'Y-m-t' );      
            return Excel::download(new ViewExcel('admin.formatosExcel.rolcontabilizado',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('listarContabilizado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function store(Request $request)
    {
        try{
            
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $roles=Rol_Consolidado::buscarrolContabilisado($request->get('fecha_desde'),$request->get('fecha_hasta'))->get();
          
            $matriz=null;
            $count=1;
            foreach($roles as $rol){
                if( $matriz==null){
                    if(isset($rol->diario_contabilizacion_id))
                    {
                        $matriz[$count]['fecha']=$rol->diariocontabilizacion->diario_fecha;
                        $matriz[$count]['pago_id']=$rol->diariocontabilizacion->diario_id;
                        $matriz[$count]['provisiones_id']=$rol->diariocontabilizacion->diario_fecha;
                        $matriz[$count]['pago_numero']=$rol->diariocontabilizacion->diario_codigo;
                        $matriz[$count]['provisiones_numero']=$rol->diariocontabilizacionbeneficios->diario_codigo;
                        $count++;   
                    }
                }
                else{
                    for ($i = 1; $i <= count($matriz); ++$i) {
                        if($matriz[$i]['pago_id']!=$rol->diariocontabilizacion->diario_id){
                            $matriz[$count]['fecha']=$rol->diariocontabilizacion->diario_fecha;
                            $matriz[$count]['pago_id']=$rol->diariocontabilizacion->diario_id;
                            $matriz[$count]['provisiones_id']=$rol->diariocontabilizacion->diario_fecha;
                            $matriz[$count]['pago_numero']=$rol->diariocontabilizacion->diario_codigo;
                            $matriz[$count]['provisiones_numero']=$rol->diariocontabilizacionbeneficios->diario_codigo;
                            $count++;  
                        }
                    }
                }
                
            }
           
            return view('admin.recursosHumanos.contabilizacionMensual.index',['fechames'=>$request->get('fechames'),'rol'=>$matriz,'fecha_todo'=>$request->get('fecha_todo'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'descripcion'=>$request->get('descripcion'), 'gruposPermiso'=>$gruposPermiso,'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){    
           
            return redirect('listarContabilizado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
        $datos=null; 
        $existe=0;
        $rol=Rol_Consolidado::buscarrolContabilisadodiario($id)->groupBy('cabecera_rol_total_anticipos')->groupBy('empleado.empleado_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->groupBy('cabecera_rol.cabecera_rol_id')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.empleado_id,empleado.empleado_nombre,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula,cabecera_rol.cabecera_rol_id as cabecera_id')->get(); 
        $tipo=Rol_Consolidado::buscarrolContabilisadotipodiario($id)->groupBy('cabecera_rol_total_anticipos')->groupBy('tipo_empleado.tipo_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos,tipo_empleado.tipo_id,tipo_empleado.tipo_descripcion,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula')->get(); 
            $tipos=Tipo_Empleado::Tipos()->get();
            $count=1;
            
            foreach($tipo as $roles){
                foreach ($tipos as $tiposroles) {    
                    if($tiposroles->tipo_id==$roles->tipo_id){
                        $existe=0;  
                        if ($datos!=null) {
                            for ($i = 1; $i <= count($datos); $i++) {
                                if ($datos[$i]["tipo"]==$tiposroles->tipo_descripcion) {
                                    $datos[$i]["sueldos"]=$datos[$i]["sueldos"]+ $roles->sueldos;
                                    $datos[$i]["vacacionesacu"]=$datos[$i]["vacacionesacu"]+$roles->vacacionespag;
                                    $datos[$i]["otrosingresos"]=$datos[$i]["otrosingresos"]+ $roles->otrosingresos;
                                    $datos[$i]["transporte"]=$datos[$i]["transporte"]+ $roles->transporte;
                                    $datos[$i]["extras"]=$datos[$i]["extras"]+ $roles->extras;
                                    $datos[$i]["otrabonifi"]=$datos[$i]["otrabonifi"]+ $roles->otrabonifi;
                                    $datos[$i]["ingresos"]=$datos[$i]["ingresos"]+ $roles->ingresos;
                                    $datos[$i]["extsalud"]=$datos[$i]["extsalud"]+ $roles->extsalud;
                                    $datos[$i]["leysal"]=$datos[$i]["leysal"]+ $roles->leysal;
                                    $datos[$i]["vacaciones"]=$datos[$i]["vacaciones"]+ $roles->vacaciones;
                                    $datos[$i]["comisariato"]=$datos[$i]["comisariato"]+ $roles->comisariato;
                                    $datos[$i]["ppqq"]=$datos[$i]["ppqq"]+ $roles->ppqq;
                                    $datos[$i]["hipoteca"]=$datos[$i]["hipoteca"]+ $roles->hipoteca;
                                    $datos[$i]["multas"]=$datos[$i]["multas"]+ $roles->multas;
                                    $datos[$i]["asumido"]=$datos[$i]["asumido"]+ $roles->asumido;
                                    $datos[$i]["personal"]=$datos[$i]["personal"]+ $roles->iess;
                                   // $datos[$i]["personal"]=$datos[$i]["personal"]+ $roles->personal;
                                    $datos[$i]["patronal"]=$datos[$i]["patronal"]+ $roles->patronal;
                                    $datos[$i]["anticipo"]=$datos[$i]["anticipo"]+ $roles->anticipo;
                                    $datos[$i]["impu_renta"]=$datos[$i]["impu_renta"]+ $roles->impu_renta;
                                    $datos[$i]["otrosegre"]=$datos[$i]["otrosegre"]+ $roles->otrosegre;
                                    $datos[$i]["egresos"]=$datos[$i]["egresos"]+ $roles->egresos;
                                    $datos[$i]["terceroacu"]=$datos[$i]["terceroacu"]+ $roles->terceroacum;
                                    $datos[$i]["tercero"]=$datos[$i]["tercero"]+ $roles->tercero;
                                    $datos[$i]["cuarto"]=$datos[$i]["cuarto"]+ $roles->cuarto;
                                    $datos[$i]["cuartoACU"]=$datos[$i]["cuartoacu"]+ $roles->cuartoacum;
                                    $datos[$i]["fondo_reservaacu"]=$datos[$i]["fondo_reservaacu"]+ $roles->fondoacumula;
                                    $datos[$i]["fondo_reserva"]=$datos[$i]["fondo_reserva"]+ $roles->fondo_reserva;
                                    $datos[$i]["iecesecap"]=$datos[$i]["iecesecap"]+ $roles->iecesecap;
                                    $datos[$i]["liquido_pagar"]=$datos[$i]["liquido_pagar"]+ $roles->liquido_pagar;
                                    $existe=1;
                                }
                            }
                        }
                        
                        if($existe==0){
                            $datos[$count]["idtipo"]=$tiposroles->tipo_id;
                            $datos[$count]["tipo"]=$tiposroles->tipo_descripcion;
                            $datos[$count]["sueldos"]=$roles->sueldos;
                            $datos[$count]["otrosingresos"]=$roles->otrosingresos;
                            $datos[$count]["vacacionesacu"]=$roles->vacacionespag;
                            $datos[$count]["transporte"]=$roles->transporte;
                            $datos[$count]["extras"]=$roles->extras;
                            $datos[$count]["otrabonifi"]= $roles->otrabonifi;
                            $datos[$count]["ingresos"]=$roles->ingresos;
                            $datos[$count]["extsalud"]= $roles->extsalud;
                            $datos[$count]["leysal"]=$roles->leysal;
                            $datos[$count]["vacaciones"]= $roles->vacaciones;
                            $datos[$count]["comisariato"]= $roles->comisariato;
                            $datos[$count]["ppqq"]= $roles->ppqq;
                            $datos[$count]["hipoteca"]=$roles->hipoteca;
                            $datos[$count]["multas"]=$roles->multas;
                            $datos[$count]["asumido"]=$roles->asumido;
                            $datos[$count]["aporte"]=$roles->aporte;
                            $datos[$count]["personal"]=$roles->iess;
                            //$datos[$count]["personal"]=$roles->personal;
                            $datos[$count]["patronal"]=$roles->patronal;
                            $datos[$count]["anticipo"]=$roles->anticipo;
                            $datos[$count]["impu_renta"]=$roles->impu_renta;
                            $datos[$count]["otrosegre"]=$roles->otrosegre;
                            $datos[$count]["egresos"]=$roles->egresos;
                            $datos[$count]["tercero"]=$roles->tercero;
                            $datos[$count]["cuarto"]=$roles->cuarto;
                            $datos[$count]["terceroacu"]=$roles->terceroacum;
                            $datos[$count]["cuartoacu"]=$roles->cuartoacum;
                            $datos[$count]["fondo_reservaacu"]=$roles->fondoacumula;
                            $datos[$count]["fondo_reserva"]=$roles->fondo_reserva;
                            $datos[$count]["iecesecap"]=$roles->iecesecap;
                            $datos[$count]["liquido_pagar"]=$roles->liquido_pagar;
                            $count++;
                        }
                        
                    }
                }    
            }
            return view('admin.recursosHumanos.contabilizacionMensual.view',['id'=>$id,'datos'=>$datos,'rol'=>$rol,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listarContabilizado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminar($id)
    {
        try{
           
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
        $datos=null; 
        $existe=0;
        $rol=Rol_Consolidado::buscarrolContabilisadodiario($id)->groupBy('cabecera_rol_total_anticipos')->groupBy('empleado.empleado_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->groupBy('cabecera_rol.cabecera_rol_id')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.empleado_id,empleado.empleado_nombre,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula,cabecera_rol.cabecera_rol_id as cabecera_id')->get(); 
        $tipo=Rol_Consolidado::buscarrolContabilisadotipodiario($id)->groupBy('cabecera_rol_total_anticipos')->groupBy('tipo_empleado.tipo_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos,tipo_empleado.tipo_id,tipo_empleado.tipo_descripcion,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula')->get(); 
            $tipos=Tipo_Empleado::Tipos()->get();
            $count=1;
            
            foreach($tipo as $roles){
                foreach ($tipos as $tiposroles) {    
                    if($tiposroles->tipo_id==$roles->tipo_id){
                        $existe=0;  
                        if ($datos!=null) {
                            for ($i = 1; $i <= count($datos); $i++) {
                                if ($datos[$i]["tipo"]==$tiposroles->tipo_descripcion) {
                                    $datos[$i]["sueldos"]=$datos[$i]["sueldos"]+ $roles->sueldos;
                                    $datos[$i]["vacacionesacu"]=$datos[$i]["vacacionesacu"]+$roles->vacacionespag;
                                    $datos[$i]["otrosingresos"]=$datos[$i]["otrosingresos"]+ $roles->otrosingresos;
                                    $datos[$i]["transporte"]=$datos[$i]["transporte"]+ $roles->transporte;
                                    $datos[$i]["extras"]=$datos[$i]["extras"]+ $roles->extras;
                                    $datos[$i]["otrabonifi"]=$datos[$i]["otrabonifi"]+ $roles->otrabonifi;
                                    $datos[$i]["ingresos"]=$datos[$i]["ingresos"]+ $roles->ingresos;
                                    $datos[$i]["extsalud"]=$datos[$i]["extsalud"]+ $roles->extsalud;
                                    $datos[$i]["leysal"]=$datos[$i]["leysal"]+ $roles->leysal;
                                    $datos[$i]["vacaciones"]=$datos[$i]["vacaciones"]+ $roles->vacaciones;
                                    $datos[$i]["comisariato"]=$datos[$i]["comisariato"]+ $roles->comisariato;
                                    $datos[$i]["ppqq"]=$datos[$i]["ppqq"]+ $roles->ppqq;
                                    $datos[$i]["hipoteca"]=$datos[$i]["hipoteca"]+ $roles->hipoteca;
                                    $datos[$i]["multas"]=$datos[$i]["multas"]+ $roles->multas;
                                    $datos[$i]["asumido"]=$datos[$i]["asumido"]+ $roles->asumido;
                                    $datos[$i]["personal"]=$datos[$i]["personal"]+ $roles->iess;
                                   // $datos[$i]["personal"]=$datos[$i]["personal"]+ $roles->personal;
                                    $datos[$i]["patronal"]=$datos[$i]["patronal"]+ $roles->patronal;
                                    $datos[$i]["anticipo"]=$datos[$i]["anticipo"]+ $roles->anticipo;
                                    $datos[$i]["impu_renta"]=$datos[$i]["impu_renta"]+ $roles->impu_renta;
                                    $datos[$i]["otrosegre"]=$datos[$i]["otrosegre"]+ $roles->otrosegre;
                                    $datos[$i]["egresos"]=$datos[$i]["egresos"]+ $roles->egresos;
                                    $datos[$i]["terceroacu"]=$datos[$i]["terceroacu"]+ $roles->terceroacum;
                                    $datos[$i]["tercero"]=$datos[$i]["tercero"]+ $roles->tercero;
                                    $datos[$i]["cuarto"]=$datos[$i]["cuarto"]+ $roles->cuarto;
                                    $datos[$i]["cuartoACU"]=$datos[$i]["cuartoacu"]+ $roles->cuartoacum;
                                    $datos[$i]["fondo_reservaacu"]=$datos[$i]["fondo_reservaacu"]+ $roles->fondoacumula;
                                    $datos[$i]["fondo_reserva"]=$datos[$i]["fondo_reserva"]+ $roles->fondo_reserva;
                                    $datos[$i]["iecesecap"]=$datos[$i]["iecesecap"]+ $roles->iecesecap;
                                    $datos[$i]["liquido_pagar"]=$datos[$i]["liquido_pagar"]+ $roles->liquido_pagar;
                                    $existe=1;
                                }
                            }
                        }
                        
                        if($existe==0){
                            $datos[$count]["idtipo"]=$tiposroles->tipo_id;
                            $datos[$count]["tipo"]=$tiposroles->tipo_descripcion;
                            $datos[$count]["sueldos"]=$roles->sueldos;
                            $datos[$count]["otrosingresos"]=$roles->otrosingresos;
                            $datos[$count]["vacacionesacu"]=$roles->vacacionespag;
                            $datos[$count]["transporte"]=$roles->transporte;
                            $datos[$count]["extras"]=$roles->extras;
                            $datos[$count]["otrabonifi"]= $roles->otrabonifi;
                            $datos[$count]["ingresos"]=$roles->ingresos;
                            $datos[$count]["extsalud"]= $roles->extsalud;
                            $datos[$count]["leysal"]=$roles->leysal;
                            $datos[$count]["vacaciones"]= $roles->vacaciones;
                            $datos[$count]["comisariato"]= $roles->comisariato;
                            $datos[$count]["ppqq"]= $roles->ppqq;
                            $datos[$count]["hipoteca"]=$roles->hipoteca;
                            $datos[$count]["multas"]=$roles->multas;
                            $datos[$count]["asumido"]=$roles->asumido;
                            $datos[$count]["aporte"]=$roles->aporte;
                            $datos[$count]["personal"]=$roles->iess;
                            //$datos[$count]["personal"]=$roles->personal;
                            $datos[$count]["patronal"]=$roles->patronal;
                            $datos[$count]["anticipo"]=$roles->anticipo;
                            $datos[$count]["impu_renta"]=$roles->impu_renta;
                            $datos[$count]["otrosegre"]=$roles->otrosegre;
                            $datos[$count]["egresos"]=$roles->egresos;
                            $datos[$count]["tercero"]=$roles->tercero;
                            $datos[$count]["cuarto"]=$roles->cuarto;
                            $datos[$count]["terceroacu"]=$roles->terceroacum;
                            $datos[$count]["cuartoacu"]=$roles->cuartoacum;
                            $datos[$count]["fondo_reservaacu"]=$roles->fondoacumula;
                            $datos[$count]["fondo_reserva"]=$roles->fondo_reserva;
                            $datos[$count]["iecesecap"]=$roles->iecesecap;
                            $datos[$count]["liquido_pagar"]=$roles->liquido_pagar;
                            $count++;
                        }
                        
                    }
                }    
            }
            return view('admin.recursosHumanos.contabilizacionMensual.eliminar',['id'=>$id,'datos'=>$datos,'rol'=>$rol,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listarContabilizado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
        try{
            
            $roles=Rol_Consolidado::buscardiario($id)->get();
           
            foreach($roles as $rol){
                $diario=Diario::findOrFail($rol->diario_contabilizacion_id);
                $diariobene=Diario::findOrFail($rol->diario_contabilizacion_beneficios_id);
                $auditoria = new generalController();
                $cierre = $auditoria->cierre($diario->diario_fecha,$diario->sucursal_id);          
                if($cierre){
                    return redirect('listarContabilizado')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
                $role=Rol_Consolidado::findOrFail($rol->cabecera_rol_id);
                $role->diario_contabilizacion_id=null;
                $role->diario_contabilizacion_beneficios_id=null;
                $role->save();
            }
            $general = new generalController();
            foreach($diario->detalles as $detalle){
                $deta=Detalle_Diario::findOrFail($detalle->detalle_id);
                $deta->delete();
                $general->registrarAuditoria('Eliminar detalle Diario de Roles de Pago Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,' Tipo de Diario -> '.$diario->diario_referencia.'');
            }
            foreach($diariobene->detalles as $detalle){
                $deta=Detalle_Diario::findOrFail($detalle->detalle_id);
                $deta->delete();
                $general->registrarAuditoria('Eliminar detalle Diario de Roles de Pago Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,' Tipo de Diario -> '.$diario->diario_referencia.'');
            }
            $diario->delete();
            $general->registrarAuditoria('Eliminar de Diario de Roles de Pago Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'Tipo de Diario -> '.$diario->diario_referencia.'');
            $diariobene->delete();  
            $general->registrarAuditoria('Eliminar de Diario de Beneficios Diario codigo: -> '.$diariobene->diario_codigo, $diariobene->diario_codigo,'Tipo de Diario -> '.$diariobene->diario_referencia.'');
            return redirect('listarContabilizado')->with('success','Datos eliminados exitosamente');
         
        }catch(\Exception $ex){
           
            return redirect('listarContabilizado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
}
