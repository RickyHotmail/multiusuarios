<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria_Producto;
use App\Models\Centro_Consumo;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Rol_Consolidado;
use App\Models\Tipo_Empleado;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

use PDF;
use App\NEOPAGUPA\ViewExcel;
use Excel;

class contabilizacionMensualController extends Controller
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
            return view('admin.recursosHumanos.contabilizacionMensual.nuevo',[ 'fechames'=>null,'consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){

            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function buscarrol($fechadesde,$fechahasta){       
        return Rol_Consolidado::buscarrolContabilisado($fechadesde,$fechahasta)->groupBy('empleado.empleado_id')->selectRaw('sum(detalle_rol.detalle_rol_total_dias) as sueldo,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.empleado_id,empleado.empleado_nombre')->get(); 
    }
    public function buscartiporol($fechadesde,$fechahasta){       
        return Rol_Consolidado::buscarrolContabilisadotipo($fechadesde,$fechahasta)->groupBy('empleado.tipo_id')->selectRaw('sum(detalle_rol.detalle_rol_total_dias) as sueldo,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.tipo_id,tipo_empleado.tipo_descripcion')->get(); 
    }
    public function tipopempleado()
    {
        return Tipo_Empleado::Tipos()->get();
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
        if (isset($_POST['guardarID'])){
            return $this->guardarId($request);
        }
        if (isset($_POST['extraerID'])){
          
            return $this->extraerId($request);
        }
        if (isset($_POST['pdf'])){
          
            return $this->pdf($request);
        }
        if (isset($_POST['excel'])){
          
            return $this->excel($request);
        }
        
    }
    public function excel(Request $request){
        try{ 
            $matriz=null;
            
            $existe=0;
            $rol=Rol_Consolidado::buscarrolContabilisado($request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario_contabilizacion_id','=',null)->where('diario_contabilizacion_beneficios_id','=',null)->groupBy('cabecera_rol_total_anticipos')->groupBy('empleado.empleado_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->groupBy('cabecera_rol.cabecera_rol_id')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.empleado_id,empleado.empleado_nombre,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula,cabecera_rol.cabecera_rol_id as cabecera_id')->get(); 
            $tipo=Rol_Consolidado::buscarrolContabilisadotipo($request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario_contabilizacion_id','=',null)->where('diario_contabilizacion_beneficios_id','=',null)->groupBy('cabecera_rol_total_anticipos')->groupBy('tipo_empleado.tipo_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos,tipo_empleado.tipo_id,tipo_empleado.tipo_descripcion,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula')->get(); 
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
            $datos[1]=$rol;
            $datos[2]=$matriz;

            $datos[3]=$request->get('fecha_desde');
            $datos[4]=$request->get('fecha_hasta');
            return Excel::download(new ViewExcel('admin.formatosExcel.rolcontabilizado',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('contabilizacionMensual')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request){
        try{ 
            $datos=null;
            
            $existe=0;
            $rol=Rol_Consolidado::buscarrolContabilisado($request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario_contabilizacion_id','=',null)->where('diario_contabilizacion_beneficios_id','=',null)->groupBy('cabecera_rol_total_anticipos')->groupBy('empleado.empleado_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->groupBy('cabecera_rol.cabecera_rol_id')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.empleado_id,empleado.empleado_nombre,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula,cabecera_rol.cabecera_rol_id as cabecera_id')->get(); 
            $tipo=Rol_Consolidado::buscarrolContabilisadotipo($request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario_contabilizacion_id','=',null)->where('diario_contabilizacion_beneficios_id','=',null)->groupBy('cabecera_rol_total_anticipos')->groupBy('tipo_empleado.tipo_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos,tipo_empleado.tipo_id,tipo_empleado.tipo_descripcion,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula')->get(); 
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
            return redirect('contabilizacionMensual')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarId(Request $request){
       
    try{ 
        DB::beginTransaction();
        setlocale(LC_TIME, 'spanish');
        $general = new generalController();

        $idrol = $request->get('rol');
        $idempleado = $request->get('empleadoid');
        $idtipo = $request->get('idtipo');
        $tipo = $request->get('tipo');

        $vsueldo = $request->get('vsueldo');
        $vextsalud = $request->get('vextsalud');
        $vpatronal = $request->get('vpatronal');
        $vextras = $request->get('vextras');
        $vleysal = $request->get('vleysal');
        $vvacaciones = $request->get('vvacaciones');
        $vvacacionespag = $request->get('vvacacionespag');
        $vtransporte = $request->get('vtransporte');
        $vppqq = $request->get('vppqq');
       
        $votrabonifi = $request->get('votrabonifi');
        $vhipoteca = $request->get('vhipoteca');
       
        $votrosingresos = $request->get('votrosingresos');
        $vcomisariato = $request->get('vcomisariato');
        
        $vasumido = $request->get('vasumido');

        $vcuartoacu = $request->get('vcuartoacu');
        $vterceroacu = $request->get('vterceroacu');
        $vfondoacumula = $request->get('vfondoacumula');

        $vfondo_reserva = $request->get('vfondo_reserva');
        $vcuarto = $request->get('vcuarto');
        $vtercero = $request->get('vtercero');

        $vingresos = $request->get('vingresos');
        $vpersonal = $request->get('vpersonal');
        $viecesecap = $request->get('viecesecap');
        $vanticipo = $request->get('vanticipo');
        $vliquido_pagar = $request->get('vliquido_pagar');
        $vimpu_renta = $request->get('vimpu_renta');
        $vmultas = $request->get('vmultas');
        $votrosegre = $request->get('votrosegre');
        $vegresos = $request->get('vegresos');

        
        $anticipos = $request->get('anticipos');
        $fechadesde=$request->get('fecha_hasta');
        $cierre = $general->cierre($fechadesde,$request->get('sucursal'));                   
        if($cierre){
            return redirect('contabilizacionMensual')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
        }
        $diariocontabilizado = new Diario();
        $diariocontabilizado->diario_codigo = $general->generarCodigoDiario($fechadesde, 'CCMR');
        $diariocontabilizado->diario_fecha = $fechadesde;
        $diariocontabilizado->diario_referencia = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES';
        $diariocontabilizado->diario_tipo_documento = 'CONTABILIZACION MENSUAL';
        $diariocontabilizado->diario_tipo = 'CCMR';
        $diariocontabilizado->diario_secuencial = substr($diariocontabilizado->diario_codigo, 8);
        $diariocontabilizado->diario_mes = DateTime::createFromFormat('Y-m-d', $fechadesde)->format('m');
        $diariocontabilizado->diario_ano = DateTime::createFromFormat('Y-m-d', $fechadesde)->format('Y');
        $temp1 = new DateTime($fechadesde);
        $monthName = strftime('%B', $temp1->getTimestamp());
        $anio = $temp1->format('Y');
        $diariocontabilizado->diario_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES DE EMPLEADOS DEL MES DE '.$monthName.' '.$anio;
        $diariocontabilizado->diario_numero_documento = 0;
        $diariocontabilizado->diario_beneficiario ="COMPROBANTE DE CONTABILIZACION MENSUAL DE EMPLEADOS";
        $diariocontabilizado->diario_cierre = '0';
        $diariocontabilizado->diario_estado = '1';
        
        $diariocontabilizado->empresa_id = Auth::user()->empresa_id;
        $diariocontabilizado->sucursal_id =  $request->get('sucursal');
        $diariocontabilizado->save();
        $general->registrarAuditoria('Registro de diario Contabilizado de rol de Empleado', '0', '');

        $diariobeneficios = new Diario();
        $diariobeneficios->diario_codigo = $general->generarCodigoDiario($fechadesde, 'CCMR');
        $diariobeneficios->diario_fecha = $fechadesde;
        $diariobeneficios->diario_referencia = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES';
        $diariobeneficios->diario_tipo_documento = 'CONTABILIZACION MENSUAL';
        $diariobeneficios->diario_tipo = 'CCMR';
        $diariobeneficios->diario_secuencial = substr($diariobeneficios->diario_codigo, 8);
        $diariobeneficios->diario_mes = DateTime::createFromFormat('Y-m-d', $fechadesde)->format('m');
        $diariobeneficios->diario_ano = DateTime::createFromFormat('Y-m-d', $fechadesde)->format('Y');
        $temp1 = new DateTime($fechadesde);
        $monthName = strftime('%B', $temp1->getTimestamp());
        $anio = $temp1->format('Y');
        $diariobeneficios->diario_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES DE EMPLEADOS DEL MES DE '.$monthName.' '.$anio;
        $diariobeneficios->diario_numero_documento = 0;
        $diariobeneficios->diario_beneficiario ="COMPROBANTE DE CONTABILIZACION MENSUAL DE EMPLEADOS";
        $diariobeneficios->diario_cierre = '0';
        $diariobeneficios->diario_estado = '1';
        
        $diariobeneficios->empresa_id = Auth::user()->empresa_id;
        $diariobeneficios->sucursal_id =  $request->get('sucursal');
        $diariobeneficios->save();
        $general->registrarAuditoria('Registro de diario Contabilizado de rol de Empleado', '0', '');
        $matriz=null;
        $matriz2=null;
        $activadoranti=true;
        $activador=true;
        $count=1;
        $count2=1;
            for ($j = 0; $j < count($anticipos); ++$j) {
            $rolid=Rol_Consolidado::FindOrFail($idrol[$j]);
                $rolid->diariocontabilizacion()->associate($diariocontabilizado);
               $rolid->diariocontabilizacionbeneficios()->associate($diariobeneficios);
               $rolid->save();
            if (floatval($anticipos[$j])>0) {
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first(); 
                $emplea=Empleado::findOrFail($idempleado[$j]); 
                if($parametrizacionContable->parametrizacion_cuenta_general == '0'){
                    $activadoranti=false;
                    if($matriz==null){
                        $matriz[$count]["idcuenta"]= $emplea->empleado_cuenta_anticipo;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($anticipos[$j]);
                        $count++;
                    }
                    else{
                        $matriz[$count]["idcuenta"]= $emplea->empleado_cuenta_anticipo;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($anticipos[$j]);
                        $count++;
                    }
                }      
                 
            }
        }
        for ($i = 0; $i < count($vsueldo); ++$i) {        
            if($activadoranti==true){
                if (floatval($vanticipo[$i])>0) {
                    $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'anticipos')->first();
                    if($matriz==null){
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["haber"]= floatval($vanticipo[$i]);
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["debe"]=0;
                        $count++;
                    } 
                
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0){
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vanticipo[$i]);
                            $activador=false;
                        }
                    }
                    if($activador==true){
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vanticipo[$i]);
                        $count++;
                    }
                }
            }
            if (floatval($vsueldo[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'sueldos')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($vsueldo[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0){
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($vsueldo[$i]);
                            $activador=false;
                        }
                    }
                    if($activador==true){
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($vsueldo[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }    
            }
            if (floatval($vliquido_pagar[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'sueldos')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["haber"]= floatval($vliquido_pagar[$i]);
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vliquido_pagar[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vliquido_pagar[$i]);
                        $count++;
                    }
                }
            }
            if (floatval($votrosingresos[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'otrosIngresos')->first();
                if ($matriz==null) {
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($votrosingresos[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                } else {
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0) {
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($votrosingresos[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($votrosingresos[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            
            if (floatval($vtercero[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'decimoTercero')->first();
                if ($matriz==null) {
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($vtercero[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                } else {
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0) {
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($vtercero[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($vtercero[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            if (floatval($vcuarto[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'decimoCuarto')->first();
                if ($matriz==null) {
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($vcuarto[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                } else {
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0) {
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($vcuarto[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($vcuarto[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            if (floatval($vfondo_reserva[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'fondoReserva')->first();
                if ($matriz==null) {
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($vfondo_reserva[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                } else {
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0) {
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($vfondo_reserva[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($vfondo_reserva[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            if (floatval($vextras[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'horasExtras')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($vextras[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0) {
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($vextras[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($vextras[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            if (floatval($vvacaciones[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'vacacion')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($vvacaciones[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0) {
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($vvacaciones[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($vvacaciones[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            if (floatval($vtransporte[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'viaticos')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($vtransporte[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0) {
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($vtransporte[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($vtransporte[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            if (floatval($votrabonifi[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'otrosBonificaciones')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($votrabonifi[$i]);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0) {
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($votrabonifi[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($votrabonifi[$i]);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            if (floatval($vextsalud[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'extSalud')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["haber"]= floatval($vextsalud[$i]);
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vextsalud[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vextsalud[$i]);
                        $count++;
                    }
                }
            }
            if (floatval($vleysal[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'leysalud')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($vleysal[$i]);
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vleysal[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vleysal[$i]);
                        $count++;
                    }
                }
            }
            if (floatval($vppqq[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'prestamosQuirografarios')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($vppqq[$i]);
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vppqq[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vppqq[$i]);
                        $count++;
                    }
                }
            }
            if (floatval($vhipoteca[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'prestamosHipotecarios')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($vhipoteca[$i]);
                    $count++;
                }
                else{
                $activador=true;
                for ($k = 1; $k <= count($matriz); ++$k) {
                    if($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0){
                        $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vhipoteca[$i]);
                        $activador=false;
                    }
                }
                if($activador==true){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($vhipoteca[$i]);
                    $count++;
                }
            
                }
            }
            if (floatval($vcomisariato[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'comisariato')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($vcomisariato[$i]);
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vcomisariato[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vcomisariato[$i]);
                        $count++;
                    }
                }
                
            }
            
            if (floatval($vmultas[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'multas')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($vmultas[$i]);
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vmultas[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vmultas[$i]);
                        $count++;
                    }
                }
            }
            if (floatval($vasumido[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'iessAsumido')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($vasumido[$i]);
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vasumido[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vasumido[$i]);
                        $count++;
                    }
                }
               
            }
            
            if (floatval($vimpu_renta[$i])>0) {
                
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'impuestoRenta')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($vimpu_renta[$i]);
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vimpu_renta[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vimpu_renta[$i]);
                        $count++;
                    }
                }
            }
            if (floatval($votrosegre[$i])>0) {
                $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'otrosEgresos')->first();
                if ($matriz==null) {
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                    $matriz[$count]["debe"]= 0;
                    $matriz[$count]["tipo"]= 'HABER';
                    $matriz[$count]["haber"]=floatval($votrosegre[$i]);
                    $count++;
                } else {
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k) {
                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($votrosegre[$i]);
                            $activador=false;
                        }
                    }
                    if ($activador==true) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($votrosegre[$i]);
                        $count++;
                    }
                }
            }
    
                if (floatval($vpersonal[$i])>0) {
                    $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'aportePersonal')->first();
                    if ($matriz==null) {
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz[$count]["debe"]= 0;
                        $matriz[$count]["tipo"]= 'HABER';
                        $matriz[$count]["haber"]=floatval($vpersonal[$i]);
                        $count++;
                    } else {
                        $activador=true;
                        for ($k = 1; $k <= count($matriz); ++$k) {
                            if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                                $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($vpersonal[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                            $matriz[$count]["debe"]= 0;
                            $matriz[$count]["tipo"]= 'HABER';
                            $matriz[$count]["haber"]=floatval($vpersonal[$i]);
                            $count++;
                        }
                    }
                }
                
        

               


                
                ///////////////////////////Provisiones///////////////////////////////
                if (floatval($vpatronal[$i])>0) {
                    $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'aportePatronal')->first();
                    if ($matriz2==null) {
                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz2[$count2]["debe"]= 0;
                        $matriz2[$count2]["tipo"]= 'HABER';
                        $matriz2[$count2]["haber"]=floatval($vpatronal[$i]);
                        $count2++;

                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz2[$count2]["debe"]=floatval($vpatronal[$i]);
                        $matriz2[$count2]["tipo"]= 'DEBE';
                        $matriz2[$count2]["haber"]=0;
                        $count2++;
                    } else {
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz2[$k]["haber"]>0) {
                                $matriz2[$k]["haber"]=  $matriz2[$k]["haber"]+floatval($vpatronal[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                            $matriz2[$count2]["debe"]= 0;
                            $matriz2[$count2]["tipo"]= 'HABER';
                            $matriz2[$count2]["haber"]=floatval($vpatronal[$i]);
                            $count2++;
                        }
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz2[$k]["debe"]>0) {
                                $matriz2[$k]["debe"]=  $matriz2[$k]["debe"]+floatval($vpatronal[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                            $matriz2[$count2]["debe"]= floatval($vpatronal[$i]);
                            $matriz2[$count2]["tipo"]= 'DEBE';
                            $matriz2[$count2]["haber"]=0;
                            $count2++;
                        }
                    }                
                }
                if (floatval($vfondoacumula[$i])>0) {
                    $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'fondoReserva')->first();
                    if ($matriz2==null) {
                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz2[$count2]["debe"]= 0;
                        $matriz2[$count2]["tipo"]= 'HABER';
                        $matriz2[$count2]["haber"]=floatval($vfondoacumula[$i]);
                        $count2++;

                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz2[$count2]["debe"]=floatval($vfondoacumula[$i]);
                        $matriz2[$count2]["tipo"]= 'DEBE';
                        $matriz2[$count2]["haber"]=0;
                        $count2++;
                    } else {
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz2[$k]["haber"]>0) {
                                $matriz2[$k]["haber"]=  $matriz2[$k]["haber"]+floatval($vfondoacumula[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                            $matriz2[$count2]["debe"]= 0;
                            $matriz2[$count2]["tipo"]= 'HABER';
                            $matriz2[$count2]["haber"]=floatval($vfondoacumula[$i]);
                            $count2++;
                        }
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz2[$k]["debe"]>0) {
                                $matriz2[$k]["debe"]=  $matriz2[$k]["debe"]+floatval($vfondoacumula[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                            $matriz2[$count2]["debe"]= floatval($vfondoacumula[$i]);
                            $matriz2[$count2]["tipo"]= 'DEBE';
                            $matriz2[$count2]["haber"]=0;
                            $count2++;
                        }
                    }           
                }
                if (floatval($vterceroacu[$i])>0) {
                    $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'decimoTercero')->first();
                    if ($matriz2==null) {
                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz2[$count2]["debe"]= 0;
                        $matriz2[$count2]["tipo"]= 'HABER';
                        $matriz2[$count2]["haber"]=floatval($vterceroacu[$i]);
                        $count2++;

                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz2[$count2]["debe"]=floatval($vterceroacu[$i]);
                        $matriz2[$count2]["tipo"]= 'DEBE';
                        $matriz2[$count2]["haber"]=0;
                        $count2++;
                    } else {
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz2[$k]["haber"]>0) {
                                $matriz2[$k]["haber"]=  $matriz2[$k]["haber"]+floatval($vterceroacu[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                            $matriz2[$count2]["debe"]= 0;
                            $matriz2[$count2]["tipo"]= 'HABER';
                            $matriz2[$count2]["haber"]=floatval($vterceroacu[$i]);
                            $count2++;
                        }
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz2[$k]["debe"]>0) {
                                $matriz2[$k]["debe"]=  $matriz2[$k]["debe"]+floatval($vterceroacu[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                            $matriz2[$count2]["debe"]= floatval($vterceroacu[$i]);
                            $matriz2[$count2]["tipo"]= 'DEBE';
                            $matriz2[$count2]["haber"]=0;
                            $count2++;
                        }
                    }           
                }
                if (floatval($vcuartoacu[$i])>0) {
                    $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'decimoCuarto')->first();
                    if ($matriz2==null) {
                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz2[$count2]["debe"]= 0;
                        $matriz2[$count2]["tipo"]= 'HABER';
                        $matriz2[$count2]["haber"]=floatval($vcuartoacu[$i]);
                        $count2++;

                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz2[$count2]["debe"]=floatval($vcuartoacu[$i]);
                        $matriz2[$count2]["tipo"]= 'DEBE';
                        $matriz2[$count2]["haber"]=0;
                        $count2++;
                    } else {
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz2[$k]["haber"]>0) {
                                $matriz2[$k]["haber"]=  $matriz2[$k]["haber"]+floatval($vcuartoacu[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                            $matriz2[$count2]["debe"]= 0;
                            $matriz2[$count2]["tipo"]= 'HABER';
                            $matriz2[$count2]["haber"]=floatval($vcuartoacu[$i]);
                            $count2++;
                        }
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz2[$k]["debe"]>0) {
                                $matriz2[$k]["debe"]=  $matriz2[$k]["debe"]+floatval($vcuartoacu[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                            $matriz2[$count2]["debe"]= floatval($vcuartoacu[$i]);
                            $matriz2[$count2]["tipo"]= 'DEBE';
                            $matriz2[$count2]["haber"]=0;
                            $count2++;
                        }
                    }           
                }
                if (floatval($viecesecap[$i])>0) {
                    $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'iece')->first();
                    if ($matriz2==null) {
                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz2[$count2]["debe"]= 0;
                        $matriz2[$count2]["tipo"]= 'HABER';
                        $matriz2[$count2]["haber"]=floatval($viecesecap[$i]);
                        $count2++;

                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz2[$count2]["debe"]=floatval($viecesecap[$i]);
                        $matriz2[$count2]["tipo"]= 'DEBE';
                        $matriz2[$count2]["haber"]=0;
                        $count2++;
                    } else {
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz2[$k]["haber"]>0) {
                                $matriz2[$k]["haber"]=  $matriz2[$k]["haber"]+floatval($viecesecap[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                            $matriz2[$count2]["debe"]= 0;
                            $matriz2[$count2]["tipo"]= 'HABER';
                            $matriz2[$count2]["haber"]=floatval($viecesecap[$i]);
                            $count2++;
                        }
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz2[$k]["debe"]>0) {
                                $matriz2[$k]["debe"]=  $matriz2[$k]["debe"]+floatval($viecesecap[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                            $matriz2[$count2]["debe"]= floatval($viecesecap[$i]);
                            $matriz2[$count2]["tipo"]= 'DEBE';
                            $matriz2[$count2]["haber"]=0;
                            $count2++;
                        }
                    }           
                }
                if (floatval($vvacacionespag[$i])>0) {
                    $tipo=Tipo_Empleado::TipoEmpleadoBusquedaCuenta($idtipo[$i], 'vacacion')->first();
                    if ($matriz2==null) {
                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                        $matriz2[$count2]["debe"]= 0;
                        $matriz2[$count2]["tipo"]= 'HABER';
                        $matriz2[$count2]["haber"]=floatval($vvacacionespag[$i]);
                        $count2++;

                        $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz2[$count2]["debe"]=floatval($vvacacionespag[$i]);
                        $matriz2[$count2]["tipo"]= 'DEBE';
                        $matriz2[$count2]["haber"]=0;
                        $count2++;
                    } else {
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz2[$k]["haber"]>0) {
                                $matriz2[$k]["haber"]=  $matriz2[$k]["haber"]+floatval($vvacacionespag[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_haber;
                            $matriz2[$count2]["debe"]= 0;
                            $matriz2[$count2]["tipo"]= 'HABER';
                            $matriz2[$count2]["haber"]=floatval($vvacacionespag[$i]);
                            $count2++;
                        }
                        $activador=true;
                        for ($k = 1; $k <= count($matriz2); ++$k) {
                            if ($matriz2[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz2[$k]["debe"]>0) {
                                $matriz2[$k]["debe"]=  $matriz2[$k]["debe"]+floatval($vvacacionespag[$i]);
                                $activador=false;
                            }
                        }
                        if ($activador==true) {
                            $matriz2[$count2]["idcuenta"]= $tipo->cuenta_debe;
                            $matriz2[$count2]["debe"]= floatval($vvacacionespag[$i]);
                            $matriz2[$count2]["tipo"]= 'DEBE';
                            $matriz2[$count2]["haber"]=0;
                            $count2++;
                        }
                    }           
                }
            

            }
             ///////////////////////////Egresos///////////////////////////////
             for ($k = 1; $k <= count($matriz); ++$k)  {
                if($matriz[$k]["tipo"]=="DEBE"){
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe =  $matriz[$k]["debe"];
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario =  'Pago del Rol del '.$request->get('fecha_desde').' al '.$request->get('fecha_hasta');
                    $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                    $detalleDiario->detalle_numero_documento = $diariocontabilizado->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';          
                    $detalleDiario->cuenta_id = $matriz[$k]["idcuenta"];
                   
                    $diariocontabilizado->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diariocontabilizado->diario_codigo, '0', 'En la cuenta del Debe -> '.$matriz[$k]["idcuenta"].' con el valor de: -> '. $matriz[$k]["debe"]);
    
                }
                if($matriz[$k]["tipo"]=="HABER"){
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe =  0.00;
                    $detalleDiario->detalle_haber =  $matriz[$k]["haber"];
                    $detalleDiario->detalle_comentario =  'Pago del Rol del '.$request->get('fecha_desde').' al '.$request->get('fecha_hasta');
                    $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                    $detalleDiario->detalle_numero_documento = $diariocontabilizado->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';          
                    $detalleDiario->cuenta_id = $matriz[$k]["idcuenta"];
                   
                    $diariocontabilizado->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diariocontabilizado->diario_codigo, '0', 'En la cuenta del Haber -> '.$matriz[$k]["idcuenta"].' con el valor de: -> '. $matriz[$k]["haber"]);
    
                }
            }
            for ($k = 1; $k <= count($matriz2); ++$k)  {
                if($matriz2[$k]["tipo"]=="DEBE"){
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe =  $matriz2[$k]["debe"];
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario =  'Pago del Rol del '.$request->get('fecha_desde').' al '.$request->get('fecha_hasta');
                    $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                    $detalleDiario->detalle_numero_documento = $diariobeneficios->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';          
                    $detalleDiario->cuenta_id = $matriz2[$k]["idcuenta"];
                   
                    $diariobeneficios->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diariobeneficios->diario_codigo, '0', 'En la cuenta del Debe -> '.$matriz2[$k]["idcuenta"].' con el valor de: -> '. $matriz2[$k]["debe"]);
    
                }
                if($matriz2[$k]["tipo"]=="HABER"){
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe =  0.00;
                    $detalleDiario->detalle_haber =  $matriz2[$k]["haber"];
                    $detalleDiario->detalle_comentario =  'Pago del Rol del '.$request->get('fecha_desde').' al '.$request->get('fecha_hasta');
                    $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                    $detalleDiario->detalle_numero_documento = $diariobeneficios->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';          
                    $detalleDiario->cuenta_id = $matriz2[$k]["idcuenta"];
                   
                    $diariobeneficios->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diariobeneficios->diario_codigo, '0', 'En la cuenta del Haber -> '.$matriz2[$k]["idcuenta"].' con el valor de: -> '. $matriz2[$k]["haber"]);
    
                }
            }
           
            $url3 = $general->pdfDiario($diariocontabilizado);
            $url2 = $general->pdfDiario($diariobeneficios);

            DB::commit();
            return redirect('contabilizacionMensual')->with('success','Datos guardados exitosamente')->with('pdf2', $url2)->with('pdf', $url3);
        }catch(\Exception $ex){
            return redirect('contabilizacionMensual')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function extraerId(Request $request){
      try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();   
            $datos=null;
            
            $existe=0;
            $rol=Rol_Consolidado::buscarrolContabilisado($request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario_contabilizacion_id','=',null)->where('diario_contabilizacion_beneficios_id','=',null)->groupBy('cabecera_rol_total_anticipos')->groupBy('empleado.empleado_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->groupBy('cabecera_rol.cabecera_rol_id')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos, empleado.empleado_id,empleado.empleado_nombre,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula,cabecera_rol.cabecera_rol_id as cabecera_id')->get(); 
            $tipo=Rol_Consolidado::buscarrolContabilisadotipo($request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario_contabilizacion_id','=',null)->where('diario_contabilizacion_beneficios_id','=',null)->groupBy('cabecera_rol_total_anticipos')->groupBy('tipo_empleado.tipo_id')->groupBy('cabecera_rol_iesspatronal')->groupBy('cabecera_rol_iesspersonal')->groupBy('cabecera_rol.cabecera_rol_fr_acumula')->selectRaw('sum(detalle_rol.detalle_rol_liquido_pagar) as liquido_pagar,sum(detalle_rol.detalle_rol_aporte_iecesecap) as iecesecap,sum(detalle_rol.detalle_rol_fondo_reserva) as fondo_reserva,sum(detalle_rol.detalle_rol_decimo_cuarto) as cuarto,sum(detalle_rol.detalle_rol_decimo_tercero) as tercero,sum(detalle_rol.detalle_rol_decimo_terceroacum) as terceroacum,sum(detalle_rol.detalle_rol_decimo_cuartoacum) as cuartoacum,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_iess_asumido) as asumido,sum(detalle_rol.detalle_rol_aporte_patronal) as aporte,sum(detalle_rol.detalle_rol_total_anticipo) as anticipo,sum(detalle_rol.detalle_rol_impuesto_renta) as impu_renta,sum(detalle_rol.detalle_rol_total_dias) as sueldos,sum(detalle_rol.detalle_rol_otros_ingresos) as otrosingresos,sum(detalle_rol.detalle_rol_total_comisariato) as comisariato,sum(detalle_rol.detalle_rol_valor_he) as extras,sum(detalle_rol.detalle_rol_transporte) as transporte,sum(detalle_rol.detalle_rol_otra_bonificacion) as otrabonifi,sum(detalle_rol.detalle_rol_total_ingreso) as ingresos,sum(detalle_rol.detalle_rol_ext_salud) as extsalud,sum(detalle_rol.detalle_rol_ley_sol) as leysal,sum(detalle_rol.detalle_rol_iess) as iess,sum(detalle_rol.detalle_rol_vacaciones) as vacaciones,sum(detalle_rol.detalle_rol_vacaciones_anticipadas) as vacacionespag,sum(detalle_rol.detalle_rol_prestamo_quirografario) as ppqq,sum(detalle_rol.detalle_rol_prestamo_hipotecario) as hipoteca,sum(detalle_rol.detalle_rol_prestamo) as prestamos,sum(detalle_rol.detalle_rol_multa) as multas,sum(detalle_rol.detalle_rol_otros_egresos) as otrosegre,sum(detalle_rol.detalle_rol_total_egreso) as egresos,tipo_empleado.tipo_id,tipo_empleado.tipo_descripcion,cabecera_rol_iesspatronal as patronal,cabecera_rol_iesspersonal as personal,cabecera_rol_total_anticipos as anticipos_total,cabecera_rol.cabecera_rol_fr_acumula as fondoacumula')->get(); 
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
           
            return view('admin.recursosHumanos.contabilizacionMensual.nuevo',['sucursal'=>$sucursal_id,'fechames'=>$request->get('fechames'),'datos'=>$datos,'rol'=>$rol,'consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('contabilizacionMensual')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function store(Request $request)
    {
        //
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
