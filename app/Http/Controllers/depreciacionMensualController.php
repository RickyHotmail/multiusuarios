<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activo_Fijo;
use App\Models\Depreciacion_Activo_Fijo;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Venta_Activo;
use DateTime;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;
use SebastianBergmann\Environment\Console;

class depreciacionMensualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $sucursales=Sucursal::sucursales()->get();
        //$activoFijos=Activo_Fijo::ActivoFijos()->get();
        return view('admin.activosFijos.depreciacionMensual.index',
        ['sucursales'=>$sucursales,
        'gruposPermiso'=>$gruposPermiso,         
        'permisosAdmin'=>$permisosAdmin]);
    }
    public function nuevo()
    {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $sucursales=Sucursal::sucursales()->get();
        //$activoFijos=Activo_Fijo::ActivoFijos()->get();
        return view('admin.activosFijos.reporteDepreciacion.nuevo',
        ['sucursales'=>$sucursales,
        'gruposPermiso'=>$gruposPermiso,         
        'permisosAdmin'=>$permisosAdmin]);
    }
    public function consultar(Request $request)
    {   
        if (isset($_POST['buscar'])){
            return $this->buscarActivoDepreciacion($request);
        }
        if (isset($_POST['guardar'])){
            return $this->guardar($request);
        } //reporteDepreciacion  
        if (isset($_POST['buscarReporte'])){
            return $this->reporteDepreciacionActivo($request);
        }
        if (isset($_POST['nuevoIndex'])){
            return $this->nuevo($request);
        }     
    }
    public function buscarActivoDepreciacion(Request $request){
        try{ 
            $activosFijosMatriz = null;           
            $depreAcum = 0;
            $depreciacio_mensual = 0;
            $depreAcumVenta = 0;
            $ventaActivo = 0;
            $ventaActivoAux = 0;
            $valoresActivo = 0 ;        
            $month= $request->get('fechames');
            $aux= date('Y-m-d', strtotime("{$month} + 1 month"));           
            $last_day = date('Y-m-d', strtotime("{$aux} - 1 day"));                       
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $activoFijos=Activo_Fijo::activoFijoxSucursal($request->get('idsucursal'))            
            ->where('activo_fijo.activo_fecha_inicio','<=', $last_day)            
            ->where('activo_fijo.activo_estado','=','1')->get();           
            $count = 1;            
            $diarioexiste = Diario::DiarioDepreciacion("CDAF",date("m", strtotime($last_day)), date("Y", strtotime($last_day)), $request->get('idsucursal'))->first();
            foreach($activoFijos as $activoFijo){  
                $rsDepreAcum = Depreciacion_Activo_Fijo::SumactivoDepreciacion($activoFijo->activo_id, $last_day)->first();              
                $rsDepreAcumVenta = Depreciacion_Activo_Fijo::SumactivoDepreciacion($activoFijo->activo_id, $last_day)->first();
                $rsventaActivo = Venta_Activo::sumaVentaDepre($activoFijo->activo_id, $last_day)->first();
                if(is_null($rsDepreAcum->depreciacionvaloracum)){
                    $depreAcum = 0;
                }else{
                    $depreAcum = $rsDepreAcum->depreciacionvaloracum;                                   
                }               
                if(is_null($rsDepreAcumVenta->depreciacionvaloracum)){
                    $depreAcumVenta = 0;
                }else{
                    $depreAcumVenta = $rsDepreAcumVenta->depreciacionvaloracum;
                }
                if(is_null($rsventaActivo->venta_monto)){
                    $ventaActivo = 0;
                }else{                    
                    $ventaActivo  = $rsventaActivo->venta_monto;
                }
                //$depreAcum = $depreAcum - $depreAcumVenta;
                $ventaActivoAux = floatval($activoFijo->activo_valor) - floatval($ventaActivo);                
                if (date("n", strtotime($activoFijo->activo_fecha_inicio)) == date("n", strtotime($last_day)) && date("Y", strtotime($activoFijo->activo_fecha_inicio)) == date("Y", strtotime($last_day))){
                    $fechaFin = $last_day;
                    $fechaIni = $activoFijo->activo_fecha_inicio;
                    $DiasR = date("t", strtotime($fechaFin)) - date("t", strtotime($fechaIni));  
                    $depreciacio_mensual = floatval($activoFijo->activo_depreciacion_mensual) / intval(30) * intval($DiasR);
                    $valoresActivo = floatval($ventaActivoAux) - floatval($activoFijo->activo_depreciacion_acumulada) - floatval($depreAcum);
                    if($depreciacio_mensual <= $valoresActivo){ 
                        //Tabla de movimientos de caja
                        $activosFijosMatriz[$count]['activo_id'] = $activoFijo->activo_id;        
                        $activosFijosMatriz[$count]['Fecha'] = $activoFijo->activo_fecha_inicio;
                        if(isset($activoFijo->diario->diario_codigo)){
                            $activosFijosMatriz[$count]['Diario'] = $activoFijo->diario->diario_codigo;
                        }else{
                            $activosFijosMatriz[$count]['Diario'] = 'NO DIARIO';
                        }                
                        $activosFijosMatriz[$count]['Producto'] = $activoFijo->producto->producto_nombre;
                        $activosFijosMatriz[$count]['TipoActivo'] = $activoFijo->grupoActivo->grupo_nombre;
                        $activosFijosMatriz[$count]['CuentaDepreciacion'] = $activoFijo->grupoActivo->cuenta_depreciacion;
                        $activosFijosMatriz[$count]['CuentaGasto'] = $activoFijo->grupoActivo->cuenta_gasto;
                        $activosFijosMatriz[$count]['Descripcion'] = $activoFijo->activo_descripcion;
                        $activosFijosMatriz[$count]['Valor'] = $ventaActivoAux;
                        $activosFijosMatriz[$count]['PorcentajeDepreciacion'] = $activoFijo->grupoActivo->grupo_porcentaje;
                        $activosFijosMatriz[$count]['baseDepreciar'] = $activoFijo->activo_base_depreciar;
                        $activosFijosMatriz[$count]['VidaUtil'] = $activoFijo->activo_vida_util;
                        $activosFijosMatriz[$count]['ValorUtil'] = $activoFijo->activo_valor_util;
                        $activosFijosMatriz[$count]['DeprecicacionMensual'] = $activoFijo->activo_depreciacion_mensual;
                        $activosFijosMatriz[$count]['DeprecicacionAnual'] = $activoFijo->activo_depreciacion_anual;
                        $activosFijosMatriz[$count]['DeprecicacionAcumulada'] = floatval($activoFijo->activo_depreciacion_acumulada) + $depreAcum;
                        $activosFijosMatriz[$count]['ValoresLibro'] = $valoresActivo;                        
                        $count = $count + 1;
                       
                    }else{            
                        //Tabla de movimientos de caja 
                        $activosFijosMatriz[$count]['activo_id'] = $activoFijo->activo_id;               
                        $activosFijosMatriz[$count]['Fecha'] = $activoFijo->activo_fecha_inicio;
                        if(isset($activoFijo->diario->diario_codigo)){
                            $activosFijosMatriz[$count]['Diario'] = $activoFijo->diario->diario_codigo;
                        }else{
                            $activosFijosMatriz[$count]['Diario'] = 'NO DIARIO';
                        }  
                        $activosFijosMatriz[$count]['Producto'] = $activoFijo->producto->producto_nombre;
                        $activosFijosMatriz[$count]['TipoActivo'] = $activoFijo->grupoActivo->grupo_nombre;
                        $activosFijosMatriz[$count]['CuentaDepreciacion'] = $activoFijo->grupoActivo->cuenta_depreciacion;
                        $activosFijosMatriz[$count]['CuentaGasto'] = $activoFijo->grupoActivo->cuenta_gasto;
                        $activosFijosMatriz[$count]['Descripcion'] = $activoFijo->activo_descripcion;
                        $activosFijosMatriz[$count]['Valor'] = $ventaActivoAux;
                        $activosFijosMatriz[$count]['PorcentajeDepreciacion'] = $activoFijo->grupoActivo->grupo_porcentaje;
                        $activosFijosMatriz[$count]['baseDepreciar'] = $activoFijo->activo_base_depreciar;
                        $activosFijosMatriz[$count]['VidaUtil'] = $activoFijo->activo_vida_util;
                        $activosFijosMatriz[$count]['ValorUtil'] = $activoFijo->activo_valor_util;
                        $activosFijosMatriz[$count]['DeprecicacionMensual'] = 0;
                        $activosFijosMatriz[$count]['DeprecicacionAnual'] = $activoFijo->activo_depreciacion_anual;
                        $activosFijosMatriz[$count]['DeprecicacionAcumulada'] = floatval($activoFijo->activo_depreciacion_acumulada) + $depreAcum;
                        $activosFijosMatriz[$count]['ValoresLibro'] = $valoresActivo;
                        $count = $count + 1;

                    }
                }else{
                    //$valoresActivo = floatval($ventaActivoAux) - floatval($activoFijo->activo_depreciacion_acumulada) - floatval($depreAcum);
                    $valoresActivo = floatval($activoFijo->activo_base_depreciar) - $ventaActivo - floatval($activoFijo->activo_depreciacion_acumulada) - floatval($depreAcum);                    
                    if(floatval(floatval($activoFijo->activo_depreciacion_mensual)) <= floatval($valoresActivo)){
                        //Tabla de movimientos de caja        
                        $activosFijosMatriz[$count]['activo_id'] = $activoFijo->activo_id;        
                        $activosFijosMatriz[$count]['Fecha'] = $activoFijo->activo_fecha_inicio;
                        if(isset($activoFijo->diario->diario_codigo)){
                            $activosFijosMatriz[$count]['Diario'] = $activoFijo->diario->diario_codigo;
                        }else{
                            $activosFijosMatriz[$count]['Diario'] = 'NO DIARIO';
                        }  
                        $activosFijosMatriz[$count]['Producto'] = $activoFijo->producto->producto_nombre;
                        $activosFijosMatriz[$count]['TipoActivo'] = $activoFijo->grupoActivo->grupo_nombre;
                        $activosFijosMatriz[$count]['CuentaDepreciacion'] = $activoFijo->grupoActivo->cuenta_depreciacion;
                        $activosFijosMatriz[$count]['CuentaGasto'] = $activoFijo->grupoActivo->cuenta_gasto;
                        $activosFijosMatriz[$count]['Descripcion'] = $activoFijo->activo_descripcion;
                        $activosFijosMatriz[$count]['Valor'] = $ventaActivoAux;
                        $activosFijosMatriz[$count]['PorcentajeDepreciacion'] = $activoFijo->grupoActivo->grupo_porcentaje;
                        $activosFijosMatriz[$count]['baseDepreciar'] = $activoFijo->activo_base_depreciar;
                        $activosFijosMatriz[$count]['VidaUtil'] = $activoFijo->activo_vida_util;
                        $activosFijosMatriz[$count]['ValorUtil'] = $activoFijo->activo_valor_util;
                        $activosFijosMatriz[$count]['DeprecicacionMensual'] = $activoFijo->activo_depreciacion_mensual;
                        $activosFijosMatriz[$count]['DeprecicacionAnual'] = $activoFijo->activo_depreciacion_anual;
                        $activosFijosMatriz[$count]['DeprecicacionAcumulada'] =  floatval($activoFijo->activo_depreciacion_acumulada) + $depreAcum;
                        $activosFijosMatriz[$count]['ValoresLibro'] = $valoresActivo;
                        $count = $count + 1;
                    }else{                                          
                        //Tabla de movimientos de caja 
                        $activosFijosMatriz[$count]['activo_id'] = $activoFijo->activo_id;               
                        $activosFijosMatriz[$count]['Fecha'] = $activoFijo->activo_fecha_inicio;
                        if(isset($activoFijo->diario->diario_codigo)){
                            $activosFijosMatriz[$count]['Diario'] = $activoFijo->diario->diario_codigo;
                        }else{
                            $activosFijosMatriz[$count]['Diario'] = 'NO DIARIO';
                        }  
                        $activosFijosMatriz[$count]['Producto'] = $activoFijo->producto->producto_nombre;
                        $activosFijosMatriz[$count]['TipoActivo'] = $activoFijo->grupoActivo->grupo_nombre;
                        $activosFijosMatriz[$count]['CuentaDepreciacion'] = $activoFijo->grupoActivo->cuenta_depreciacion;
                        $activosFijosMatriz[$count]['CuentaGasto'] = $activoFijo->grupoActivo->cuenta_gasto;
                        $activosFijosMatriz[$count]['Descripcion'] = $activoFijo->activo_descripcion;
                        $activosFijosMatriz[$count]['Valor'] = $ventaActivoAux;
                        $activosFijosMatriz[$count]['PorcentajeDepreciacion'] = 0;
                        $activosFijosMatriz[$count]['baseDepreciar'] = 0;
                        $activosFijosMatriz[$count]['VidaUtil'] = 0;
                        $activosFijosMatriz[$count]['ValorUtil'] = 0;
                        $activosFijosMatriz[$count]['DeprecicacionMensual'] = 0;
                        $activosFijosMatriz[$count]['DeprecicacionAnual'] = 0;
                        $activosFijosMatriz[$count]['DeprecicacionAcumulada'] = 0;
                        $activosFijosMatriz[$count]['ValoresLibro'] = 0;
                        $count = $count + 1;                        
                    }

                }
               
            }            
            $sucursalselect = $request->get('idsucursal');
            $fechaselect =  $request->get('fechames');
            $sucursales=Sucursal::sucursales()->get();
            return view('admin.activosFijos.depreciacionMensual.index',
            ['activosFijosMatriz'=>$activosFijosMatriz,
            'sucursales'=>$sucursales,
            'diarioexiste'=>$diarioexiste,
            'sucursalselect'=>$sucursalselect,
            'fechaselect'=>$fechaselect,
            'gruposPermiso'=>$gruposPermiso,         
            'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('depreciacionMensual')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function guardar(Request $request){
        try{ 
            DB::beginTransaction();
            $month= $request->get('fechames');
            $aux= date('Y-m-d', strtotime("{$month} + 1 month"));
            $last_day = date('Y-m-d', strtotime("{$aux} - 1 day")); 
            $general = new generalController();
                     
            $activos = $request->get('activoId');           
            $valores = $request->get('valorId');                          
            $cuentasDebe = [];
            $cuentasHaber = [];
            $countDebe = 0 ;
            $countHaber = 0;
            $tieneDiario = $request->get('diarioID');
            $diarioCod = $request->get('diarioCodigo');
            $diarios = Diario::DiarioCodigo($diarioCod)->first();
            $cierre = $general->cierre($last_day,$request->get('idsucursal'));         
            if($cierre){
                return redirect('depreciacionMensual')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            } 
            $diariosuax = $diarios;
            if(isset($diarios)){
                foreach($diarios->detalles as $detalle){                            
                    $detalle->delete();
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Eliminacion de detalles de diario Activos Fijos: -> '.$diarios->diario_codigo, $tieneDiario, '');
                } 
                $depreciaciondelmes = Depreciacion_Activo_Fijo::DepreciacionActivoxDiario($tieneDiario)->get();
                foreach($depreciaciondelmes as $depreciaciondelme){                            
                    $depreciaciondelme->delete();
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Eliminacion de detalles de diario Activos Fijos: -> '.$diarios->diario_codigo, $tieneDiario, '');
                }               
                $diarios->delete();
                /*Inicio de registro de auditoria */
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Eliminacion de Diario de Acivo Fijo -> '.$tieneDiario,'0','por el valor de'.$diariosuax->diario_codigo);
                /*Fin de registro de auditoria */
             }            
            /**********************asiento diario****************************/
            setlocale(LC_TIME, "es");
            $general = new generalController();
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($last_day,'CDAF');
            $diario->diario_fecha = $last_day;
            $diario->diario_referencia = 'COMPROBANTE DIARIO POR DEPRECIACION DE ACTIVOS FIJOS';
            $diario->diario_tipo_documento = 'DEPRECIACION DE ACTIVOS FIJOS';
            $diario->diario_numero_documento = 0;
            $diario->diario_beneficiario = "SIN BENEFICIARIO";
            $diario->diario_tipo = 'CDAF';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $last_day)->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $last_day)->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DIARIO POR DEPRECIACION DE ACTIVOS FIJOS'.' '.strtoupper(strftime("%B", strtotime($last_day))).'-'.DateTime::createFromFormat('Y-m-d', $last_day)->format('Y');
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $request->get('idsucursal');
            $diario->save();                       
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');            
           
            for ($i = 0; $i < count($activos); ++$i){
                $activo = Activo_Fijo::findOrFail($activos[$i]);
                if($valores[$i]>0){
                    if($i == 0){                    
                        $cuentasDebe[$countDebe][1] = $activo->grupoActivo->cuentaGasto->cuenta_id;
                        $cuentasDebe[$countDebe][2] = floatval($valores[$i]);
                        $cuentasHaber[$countHaber][1] = $activo->grupoActivo->cuentaDepreciacion->cuenta_id;
                        $cuentasHaber[$countHaber][2] = floatval($valores[$i]);
                        $countDebe ++;
                        $countHaber ++;
                    }else{  
                        $encontardoD = false;
                        $posicionD=0;
                        for ($j = 0; $j < count($cuentasDebe); ++$j){
                            if($cuentasDebe[$j][1] == $activo->grupoActivo->cuentaGasto->cuenta_id){
                                $encontardoD = true;
                                $posicionD=$j;
                                break;
                            }
                        }
                        if($encontardoD){
                            $cuentasDebe[$posicionD][2] = floatval($cuentasDebe[$posicionD][2]) + floatval($valores[$i]);
                        }else{
                            $cuentasDebe[$countDebe][1] = $activo->grupoActivo->cuentaGasto->cuenta_id;
                            $cuentasDebe[$countDebe][2] = floatval($valores[$i]);
                            $countDebe ++;                        
                        }
                        $encontardoH = false;
                        $posicionH=0;
                        
                        for ($j = 0; $j < count($cuentasHaber); ++$j){
                            if($cuentasHaber[$j][1] == $activo->grupoActivo->cuentaDepreciacion->cuenta_id){
                                $encontardoH = true;
                                $posicionH=$j;
                                break;
                            }
                        }
                        if($encontardoH){
                            $cuentasHaber[$posicionH][2] = floatval($cuentasHaber[$posicionH][2]) + floatval($valores[$i]);
                        }else{ 
                            $cuentasHaber[$countHaber][1] = $activo->grupoActivo->cuentaDepreciacion->cuenta_id;
                            $cuentasHaber[$countHaber][2] = floatval($valores[$i]);
                            $countHaber ++;                       
                                     
                        }
                    }    
                    $depreciacionesdelmes=Depreciacion_Activo_Fijo::DepreciacionActivoxFechas($last_day, $tieneDiario)->get();
                    if(isset($depreciacionesdelmes->depreciacion_fecha)){
                        foreach($depreciacionesdelmes as $depreciacionme){
                            $depreciacionme->delete();
                        }
                    }            
                    $DepreciacionActivoFijo = new Depreciacion_Activo_Fijo();
                    $DepreciacionActivoFijo->depreciacion_fecha = $last_day;
                    $DepreciacionActivoFijo->depreciacion_valor =  floatval($valores[$i]);
                    $DepreciacionActivoFijo->depreciacion_estado = 1;           
                    $DepreciacionActivoFijo->activo_id = $activos[$i];
                    $DepreciacionActivoFijo->diario()->associate($diario);
                    $DepreciacionActivoFijo->save();
                    /*Inicio de registro de auditoria */
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Registro de Depreciacion de Activo Fijo -> '.$request->get('idDescripcion'),'0','');
                }
               
                /*Fin de registro de auditoria */                
            } 
            /********************detalle de diario de venta********************/
            for ($i = 0; $i < count($cuentasDebe); ++$i){
                //$cuentasDebe[$i][1];
                //$cuentasDebe[$i][2];            
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $cuentasDebe[$i][2];
                $detalleDiario->detalle_haber = 0.00 ;
                $detalleDiario->detalle_comentario = 'COMPROBANTE DIARIO POR DEPRECIACION DE ACTIVOS FIJOS'.' '.strtoupper(strftime("%B", strtotime($last_day))).'-'.DateTime::createFromFormat('Y-m-d', $last_day)->format('Y');
                $detalleDiario->detalle_tipo_documento = 'DEPRECIACION ACTIVO FIJO';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';           
                $detalleDiario->cuenta_id = $cuentasDebe[$i][1];
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del debe con el valor de: -> '.$cuentasDebe[$i][2]);
            }            
            for ($i = 0; $i < count($cuentasHaber); ++$i){
                //$cuentasHaber[$i][1];
                //$cuentasHaber[$i][2];
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = $cuentasHaber[$i][2];
                $detalleDiario->detalle_comentario = 'COMPROBANTE DIARIO POR DEPRECIACION DE ACTIVOS FIJOS'.' '.strtoupper(strftime("%B", strtotime($last_day))).'-'.DateTime::createFromFormat('Y-m-d', $last_day)->format('Y');
                $detalleDiario->detalle_tipo_documento = 'DEPRECIACION ACTIVO FIJO';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cuenta_id = $cuentasHaber[$i][1];
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber con el valor de: -> '.$cuentasHaber[$i][2]);
           } 
           $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('depreciacionMensual')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
         return redirect('depreciacionMensual')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function reporteDepreciacionActivo(Request $request){
        try{
            $activosFijosMatriz = null;           
            $depreAcum = 0;
            $depreAcumaux = 0;
            $depreciacio_mensual = 0;
            $depreAcumVenta = 0;
            $ventaActivo = 0;
            $ventaActivoAux = 0;
            $valoresActivo = 0 ;        
            $last_day= $request->get('fechames');                                  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $activoFijos=Activo_Fijo::activoFijoxSucursal($request->get('idsucursal'))           
            ->where('activo_fijo.activo_fecha_inicio','<=', $last_day)->get();           
            $count = 1;            
            foreach($activoFijos as $activoFijo){  
                $rsDepreAcum = Depreciacion_Activo_Fijo::SumactivoDepreciacion($activoFijo->activo_id, $last_day)->first();
                $rsDepreAcumaux = Depreciacion_Activo_Fijo::SumactivoDepreciacionFechas($activoFijo->activo_id, $request->get('idDesde'), $request->get('fechames'))->first();
                $rsDepreAcumVenta = Depreciacion_Activo_Fijo::SumactivoDepreciacion($activoFijo->activo_id, $last_day)->first();
                $rsventaActivo = Venta_Activo::sumaVentaDepre($activoFijo->activo_id,$request->get('fechames'))->first();
                if(is_null($rsDepreAcum->depreciacionvaloracum)){
                    $depreAcum = 0;
                }else{
                    $depreAcum = $rsDepreAcum->depreciacionvaloracum;                                   
                }
                if(is_null($rsDepreAcumaux->depreciacionvaloracum)){
                    $depreAcumaux = 0;
                }else{
                    $depreAcumaux = $rsDepreAcumaux->depreciacionvaloracum;                                   
                }                 
                if(is_null($rsDepreAcumVenta->depreciacionvaloracum)){
                    $depreAcumVenta = 0;
                }else{
                    $depreAcumVenta = $rsDepreAcumVenta->depreciacionvaloracum;
                }

                if(is_null($rsventaActivo->venta_monto)){
                    $ventaActivo = 0;
                }else{                    
                    $ventaActivo  = $rsventaActivo->venta_monto;
                }
                //$ventaActivoAux = floatval($activoFijo->activo_valor) - floatval($ventaActivo); 
                $fechaFin = $last_day;
                $fechaIni = $activoFijo->activo_fecha_inicio;
                $DiasR = date("t", strtotime($fechaFin)) - date("t", strtotime($fechaIni));  
                $depreciacio_mensual = floatval(floatval($activoFijo->activo_depreciacion_mensual) / intval(30)) * intval($DiasR);
                $ventaActivoAux = floatval($activoFijo->activo_base_depreciar) - floatval($ventaActivo);
                $valoresActivo = floatval($ventaActivoAux) - floatval($activoFijo->activo_depreciacion_acumulada) - floatval($depreAcum);
            if(floatval(floatval($activoFijo->activo_depreciacion_mensual)) <= floatval($valoresActivo)){
            //if($valoresActivo >= $depreciacio_mensual ){ 
                //Tabla de movimientos de caja
                $activosFijosMatriz[$count]['activo_id'] = $activoFijo->activo_id;        
                $activosFijosMatriz[$count]['Fecha'] = $activoFijo->activo_fecha_inicio;
                if(isset($activoFijo->diario->diario_codigo)){
                    $activosFijosMatriz[$count]['Diario'] = $activoFijo->diario->diario_codigo;
                }else{
                    $activosFijosMatriz[$count]['Diario'] = 'NO TIENE DIARIO';
                }
                $activosFijosMatriz[$count]['Producto'] = $activoFijo->producto->producto_nombre;
                $activosFijosMatriz[$count]['TipoActivo'] = $activoFijo->grupoActivo->grupo_nombre;
                $activosFijosMatriz[$count]['CuentaDepreciacion'] = $activoFijo->grupoActivo->cuenta_depreciacion;
                $activosFijosMatriz[$count]['CuentaGasto'] = $activoFijo->grupoActivo->cuenta_gasto;
                $activosFijosMatriz[$count]['Descripcion'] = $activoFijo->activo_descripcion;
                //$activosFijosMatriz[$count]['Valor'] = number_format($ventaActivoAux,2);
                $activosFijosMatriz[$count]['Valor'] = number_format( $activoFijo->activo_depreciacion,2);
                $activosFijosMatriz[$count]['PorcentajeDepreciacion'] = $activoFijo->grupoActivo->grupo_porcentaje;
                $activosFijosMatriz[$count]['baseDepreciar'] = number_format($activoFijo->activo_base_depreciar,2);
                $activosFijosMatriz[$count]['VidaUtil'] = number_format($activoFijo->activo_vida_util,2);
                $activosFijosMatriz[$count]['ValorUtil'] = number_format($activoFijo->activo_valor_util,2);
                $activosFijosMatriz[$count]['DeprecicacionMensual'] = number_format($activoFijo->activo_depreciacion_mensual,2);
                $activosFijosMatriz[$count]['DeprecicacionAnual'] = number_format($activoFijo->activo_depreciacion_anual,2);
                $activosFijosMatriz[$count]['DeprecicacionAcumulada'] = floatval($activoFijo->activo_depreciacion_acumulada) + $depreAcum;
                $activosFijosMatriz[$count]['DeprecicacionHistorica'] = floatval($depreAcumaux);
                $activosFijosMatriz[$count]['ValoresLibro'] = number_format($valoresActivo,2);                        
                $count = $count + 1;
                
            }else{            
                //Tabla de movimientos de caja 
                $activosFijosMatriz[$count]['activo_id'] = $activoFijo->activo_id;               
                $activosFijosMatriz[$count]['Fecha'] = $activoFijo->activo_fecha_inicio;
                if(isset($activoFijo->diario->diario_codigo)){
                    $activosFijosMatriz[$count]['Diario'] = $activoFijo->diario->diario_codigo;
                }else{
                    $activosFijosMatriz[$count]['Diario'] = 'NO DIARIO';
                }  
                $activosFijosMatriz[$count]['Producto'] = $activoFijo->producto->producto_nombre;
                $activosFijosMatriz[$count]['TipoActivo'] = $activoFijo->grupoActivo->grupo_nombre;
                $activosFijosMatriz[$count]['CuentaDepreciacion'] = $activoFijo->grupoActivo->cuenta_depreciacion;
                $activosFijosMatriz[$count]['CuentaGasto'] = $activoFijo->grupoActivo->cuenta_gasto;
                $activosFijosMatriz[$count]['Descripcion'] = $activoFijo->activo_descripcion;
                //$activosFijosMatriz[$count]['Valor'] = number_format($ventaActivoAux,2);
                $activosFijosMatriz[$count]['Valor'] = number_format( $activoFijo->activo_depreciacion,2);
                $activosFijosMatriz[$count]['PorcentajeDepreciacion'] = $activoFijo->grupoActivo->grupo_porcentaje;
                $activosFijosMatriz[$count]['baseDepreciar'] = number_format($activoFijo->activo_base_depreciar,2);
                $activosFijosMatriz[$count]['VidaUtil'] = number_format($activoFijo->activo_vida_util,2);
                $activosFijosMatriz[$count]['ValorUtil'] = number_format($activoFijo->activo_valor_util,2);
                $activosFijosMatriz[$count]['DeprecicacionMensual'] = 0;
                $activosFijosMatriz[$count]['DeprecicacionAnual'] = number_format($activoFijo->activo_depreciacion_anual,2);
                $activosFijosMatriz[$count]['DeprecicacionAcumulada'] = floatval($activoFijo->activo_depreciacion_acumulada) + $depreAcum;
                $activosFijosMatriz[$count]['DeprecicacionHistorica'] = floatval($depreAcumaux);
                $activosFijosMatriz[$count]['ValoresLibro'] = $valoresActivo;
                $count = $count + 1;

                }
            }        
            $sucursalselect = $request->get('idsucursal');
            $fechaselect =  $request->get('fechames');
            $fechaselect2 =  $request->get('idDesde');
            $sucursales=Sucursal::sucursales()->get();
            return view('admin.activosFijos.reporteDepreciacion.index',
            ['activosFijosMatriz'=>$activosFijosMatriz,
            'sucursales'=>$sucursales,
            'sucursalselect'=>$sucursalselect,
            'fechaselect'=>$fechaselect,
            'fechaselect2'=>$fechaselect2,
            'gruposPermiso'=>$gruposPermiso,   
            'tipoPermiso'=>$tipoPermiso,         
            'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('reporteDepreciacion')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
