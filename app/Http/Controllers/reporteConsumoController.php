<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cabecera_Rol_CM;
use App\Models\Centro_Consumo;
use App\Models\Empresa;
use App\Models\Movimiento_Producto;
use App\Models\Punto_Emision;
use App\Models\Rol_Movimiento;
use App\Models\Tipo_Movimiento_Empleado;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use App\NEOPAGUPA\ViewExcel;
use Excel;

class reporteConsumoController extends Controller
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
            return view('admin.compras.reporteCentroConsumoDetalle.index',['CentroConsumos'=>Centro_Consumo::CentroConsumos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
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
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $matriz = null;
            $fechas = null;
            $dias = null;
            $count = 1;
            $totales = 0;
            setlocale(LC_TIME, "es");
            $inicio = new DateTime($request->get('idDesde'));
            $fin = new DateTime($request->get('idHasta'));
            while ($inicio <= $fin) {
                 $inicio->format('m-Y');
                $fe='01-'.$inicio->format('m-Y');
                $dia = date('t', strtotime($fe));
                $fechas[$count]['fecha'] = strftime("%B", strtotime($fe)).'-'.$inicio->format('Y');
                $dias[$count]['fecha'] =  $dia;
                $count ++;
                $inicio->modify('+ 1 month');
            }            
            $count = 1;
            $movmientos=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(movimiento_total) as suma,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->get(); 
            $movmientoscat=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_producto.categoria_id')->selectRaw('sum(movimiento_total) as suma,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_rol.categoria_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->orderby('categoria')->get(); 
            foreach ($movmientos as $centro) {
                $matriz[$count]['fec'] = ''; 
                $matriz[$count]['doc'] = $centro->centro; 
                $matriz[$count]['cat'] = ''; 
                for ($i = 1; $i <= count($fechas); ++$i){
                    $matriz[$count][$fechas[$i]['fecha']]=0;
                }
                $matriz[$count]['cos'] = $centro->suma; 
                $matriz[$count]['por'] = 0; 
                $matriz[$count]['tot'] = '1';
                $totales=$totales+$centro->suma; 
                $count ++;
                foreach ($movmientoscat as $centrocat) {
                    if($centro->centro_id==$centrocat->centro_id){
                        $matriz[$count]['fec'] = ''; 
                        $matriz[$count]['doc'] = $centrocat->centro; 
                        $matriz[$count]['cat'] = $centrocat->categoria;
                        for ($i = 1; $i <= count($fechas); ++$i){
                            $matriz[$count][$fechas[$i]['fecha']]=0;
                        }
                        $matriz[$count]['cos'] = $centrocat->suma; 
                        $matriz[$count]['por'] = 0; 
                        $matriz[$count]['tot'] = '2';
                        $count ++;
                    }
                }
            }
            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'TOTAL COSTOS BRUTOS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '3';
            $count ++;

            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'COSTO HECTAREA DIA TOTAL'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '4';

            $count ++;
            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'LARVA'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '5';

            $count ++;
            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'BALANCEADO'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '5';

            $count ++;
            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'MEJORAS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '5';

            $count ++;
            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'ACTIVOS FIJOS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '5';

            $count ++;
            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'TOTAL COSTOS NO INCLUIDOS EN COSTO/HA/DIA'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '6';

            $count ++;
            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'COSTO HECTAREA DIA TOTAL'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '7';

            $count ++;
            $matriz[$count]['fec'] = ''; 
            $matriz[$count]['doc'] = ''; 
            $matriz[$count]['cat'] = 'COSTO HECTAREA DIA PROMEDIO '.count($fechas).' MESES'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $matriz[$count][$fechas[$i]['fecha']]=0;
            }
            $matriz[$count]['cos'] = 0; 
            $matriz[$count]['por'] = 0; 
            $matriz[$count]['tot'] = '8';


            $movmientos=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('movimiento_fecha')->selectRaw('sum(movimiento_total) as suma,movimiento_fecha as fecha,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('cabecera_rol_fecha')->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,cabecera_rol_fecha as fecha,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->get();       
            for ($j = 1; $j <= count($matriz); ++$j){
                if ($matriz[$j]['tot']=='1'){
                foreach ($movmientos as $centro) {
                        if ($matriz[$j]['doc']==$centro->centro) {
                            $inicio = new DateTime($centro->fecha);
                            $inicio=strftime("%B", strtotime($centro->fecha)).'-'.$inicio->format('Y');
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                               
                                if($fechas[$i]['fecha']==$inicio){
                                    $matriz[$j][$inicio]=$matriz[$j][$inicio]+round($centro->suma,2); 
                                }
                                $total=$total+$matriz[$j][$fechas[$i]['fecha']];  
                                $matriz[$j]['por']=round(($total/$totales)*100,2);
                                $matriz[$j]['cos']=$total;
                            }
                        }
                    }
                }
            }
            $movmientoscat=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('movimiento_producto.movimiento_fecha')->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_producto.categoria_id')->selectRaw('sum(movimiento_total) as suma,movimiento_fecha as fecha,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('cabecera_rol_fecha')->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_rol.categoria_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,cabecera_rol_fecha as fecha,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->orderby('categoria')->get(); 
            for ($j = 1; $j <= count($matriz); ++$j){
                if ($matriz[$j]['tot']=='2'){
                foreach ($movmientoscat as $centro) {
                        if ($matriz[$j]['doc']==$centro->centro && $matriz[$j]['cat']==$centro->categoria) {
                            $inicio = new DateTime($centro->fecha);
                            $inicio=strftime("%B", strtotime($centro->fecha)).'-'.$inicio->format('Y');
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                               
                                if($fechas[$i]['fecha']==$inicio){
                                    $matriz[$j][$inicio]=$matriz[$j][$inicio]+round($centro->suma,2); 
                                }
                                $total=$total+$matriz[$j][$fechas[$i]['fecha']];
                                $matriz[$j]['por']=round(($total/$totales)*100,2);
                                $matriz[$j]['cos']=$total;
                            }
                        }
                    }
                }
            }
           
            for ($k = 1; $k <= count($matriz); ++$k) {
                if ($matriz[$k]['tot']=='3') {
                    for ($j = 1; $j <= count($matriz); ++$j) {
                        if ($matriz[$j]['tot']=='1') {
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $matriz[$k][$fechas[$i]['fecha']]=$matriz[$k][$fechas[$i]['fecha']]+$matriz[$j][$fechas[$i]['fecha']];
                                $total=$total+$matriz[$k][$fechas[$i]['fecha']];
                            }
                            $matriz[$k]['por']=round(($total/$totales)*100,2);
                            $matriz[$k]['cos']=$total;
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($matriz); ++$k) {
                if ($matriz[$k]['tot']=='4') {
                    for ($j = 1; $j <= count($matriz); ++$j) {
                        if ($matriz[$j]['tot']=='3') {
                            $total=0;
                            for ($i = 1; $i <= count($dias); ++$i) {
                                $matriz[$k][$fechas[$i]['fecha']]=round($matriz[$j][$fechas[$i]['fecha']]/$dias[$i]['fecha']/$request->get('hectareas'),2);
                                $total=$total+$dias[$i]['fecha'];
                            }
                            $matriz[$k]['cos']=round($totales/($total)/$request->get('hectareas'),2);
                            $matriz[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($matriz); ++$k) {
                if ($matriz[$k]['tot']=='5') {
                    for ($j = 1; $j <= count($matriz); ++$j) {
                        if ($matriz[$j]['tot']=='1') {
                            if ($matriz[$k]['cat']==$matriz[$j]['doc']) {
                                $total=0;
                                for ($i = 1; $i <= count($fechas); ++$i) {
                                    $matriz[$k][$fechas[$i]['fecha']]=$matriz[$k][$fechas[$i]['fecha']]+$matriz[$j][$fechas[$i]['fecha']];
                                    $total=$total+$matriz[$k][$fechas[$i]['fecha']];
                                }
                                $matriz[$k]['cos']=$total;
                                $matriz[$k]['por']='0';
                            }
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($matriz); ++$k) {
                if ($matriz[$k]['tot']=='6') {
                    for ($j = 1; $j <= count($matriz); ++$j) {
                        $total=0;
                        if ($matriz[$j]['tot']=='5') {
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $matriz[$k][$fechas[$i]['fecha']]=$matriz[$k][$fechas[$i]['fecha']]+$matriz[$j][$fechas[$i]['fecha']];
                                $total=$total+$matriz[$k][$fechas[$i]['fecha']];
                            }
                            $matriz[$k]['cos']=$total;
                            $matriz[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($matriz); ++$k) {
                $total=0;
                $stotal=0;
                if ($matriz[$k]['tot']=='7') {
                    for ($j = 1; $j <= count($matriz); ++$j) {
                       
                        if ($matriz[$j]['tot']=='6') {
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $matriz[$k][$fechas[$i]['fecha']]=round($matriz[$j][$fechas[$i]['fecha']]/$dias[$i]['fecha']/$request->get('hectareas'),2);
                                $total=$total+$dias[$i]['fecha'];
                            }

                            $matriz[$k]['cos']=round($matriz[$j]['cos']/$total/$request->get('hectareas'),2);
                            $matriz[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($matriz); ++$k) {
                if ($matriz[$k]['tot']=='8') {
                    
                    $ttotal=0;
                    $tstotal=0;
                        for ($i = 1; $i <= count($fechas); ++$i) {
                            $total=0;
                            for ($j = 1; $j <= count($matriz); ++$j) {
                                if ($matriz[$j]['tot']=='4') {
                                    $total=$total+$matriz[$j][$fechas[$i]['fecha']];
                                    $ttotal=$matriz[$j]['cos'];
                                }
                                if ($matriz[$j]['tot']=='7') {
                                    $stotal=$total-$matriz[$j][$fechas[$i]['fecha']];
                                    $tstotal=$matriz[$j]['cos'];
                                }
                            }
                            $matriz[$k][$fechas[$i]['fecha']]=$stotal;
                        }

                        $matriz[$k]['cos']=$ttotal-$tstotal;
                        $matriz[$k]['por']='0';
                    
                }
            }
            $datos=NULL;
            $datos[1]=$dias;

            $datos[2]=$fechas;

            $datos[3]=$matriz;
            $datos[4]=$request->get('hectareas');
           
            return Excel::download(new ViewExcel('admin.formatosExcel.centroconsumoimpresion',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('listaConsumo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $fechas = null;
            $dias = null;
            $count = 1;
            $totales = 0;
            setlocale(LC_TIME, "es");
            $inicio = new DateTime($request->get('idDesde'));
            $fin = new DateTime($request->get('idHasta'));
            while ($inicio <= $fin) {
                 $inicio->format('m-Y');
                $fe='01-'.$inicio->format('m-Y');
                $dia = date('t', strtotime($fe));
                $fechas[$count]['fecha'] = strftime("%B", strtotime($fe)).'-'.$inicio->format('Y');
                $dias[$count]['fecha'] =  $dia;
                $count ++;
                $inicio->modify('+ 1 month');
            }            
            $count = 1;
            $movmientos=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(movimiento_total) as suma,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->get(); 
            $movmientoscat=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_producto.categoria_id')->selectRaw('sum(movimiento_total) as suma,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_rol.categoria_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->orderby('categoria')->get(); 
            foreach ($movmientos as $centro) {
                $datos[$count]['fec'] = ''; 
                $datos[$count]['doc'] = $centro->centro; 
                $datos[$count]['cat'] = ''; 
                for ($i = 1; $i <= count($fechas); ++$i){
                    $datos[$count][$fechas[$i]['fecha']]=0;
                }
                $datos[$count]['cos'] = $centro->suma; 
                $datos[$count]['por'] = 0; 
                $datos[$count]['tot'] = '1';
                $totales=$totales+$centro->suma; 
                $count ++;
                foreach ($movmientoscat as $centrocat) {
                    if($centro->centro_id==$centrocat->centro_id){
                        $datos[$count]['fec'] = ''; 
                        $datos[$count]['doc'] = $centrocat->centro; 
                        $datos[$count]['cat'] = $centrocat->categoria;
                        for ($i = 1; $i <= count($fechas); ++$i){
                            $datos[$count][$fechas[$i]['fecha']]=0;
                        }
                        $datos[$count]['cos'] = $centrocat->suma; 
                        $datos[$count]['por'] = 0; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                }
            }
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'TOTAL COSTOS BRUTOS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '3';
            $count ++;

            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'COSTO HECTAREA DIA TOTAL'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '4';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'LARVA'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '5';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'BALANCEADO'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '5';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'MEJORAS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '5';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'ACTIVOS FIJOS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '5';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'TOTAL COSTOS NO INCLUIDOS EN COSTO/HA/DIA'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '6';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'COSTO HECTAREA DIA TOTAL'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '7';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'COSTO HECTAREA DIA PROMEDIO '.count($fechas).' MESES'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '8';


            $movmientos=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('movimiento_fecha')->selectRaw('sum(movimiento_total) as suma,movimiento_fecha as fecha,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('cabecera_rol_fecha')->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,cabecera_rol_fecha as fecha,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->get();       
            for ($j = 1; $j <= count($datos); ++$j){
                if ($datos[$j]['tot']=='1'){
                foreach ($movmientos as $centro) {
                        if ($datos[$j]['doc']==$centro->centro) {
                            $inicio = new DateTime($centro->fecha);
                            $inicio=strftime("%B", strtotime($centro->fecha)).'-'.$inicio->format('Y');
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                               
                                if($fechas[$i]['fecha']==$inicio){
                                    $datos[$j][$inicio]=$datos[$j][$inicio]+round($centro->suma,2); 
                                }
                                $total=$total+$datos[$j][$fechas[$i]['fecha']];  
                                $datos[$j]['por']=round(($total/$totales)*100,2);
                                $datos[$j]['cos']=$total;
                            }
                        }
                    }
                }
            }
            $movmientoscat=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('movimiento_producto.movimiento_fecha')->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_producto.categoria_id')->selectRaw('sum(movimiento_total) as suma,movimiento_fecha as fecha,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('cabecera_rol_fecha')->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_rol.categoria_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,cabecera_rol_fecha as fecha,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->orderby('categoria')->get(); 
            for ($j = 1; $j <= count($datos); ++$j){
                if ($datos[$j]['tot']=='2'){
                foreach ($movmientoscat as $centro) {
                        if ($datos[$j]['doc']==$centro->centro && $datos[$j]['cat']==$centro->categoria) {
                            $inicio = new DateTime($centro->fecha);
                            $inicio=strftime("%B", strtotime($centro->fecha)).'-'.$inicio->format('Y');
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                               
                                if($fechas[$i]['fecha']==$inicio){
                                    $datos[$j][$inicio]=$datos[$j][$inicio]+round($centro->suma,2); 
                                }
                                $total=$total+$datos[$j][$fechas[$i]['fecha']];
                                $datos[$j]['por']=round(($total/$totales)*100,2);
                                $datos[$j]['cos']=$total;
                            }
                        }
                    }
                }
            }
           
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='3') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                        if ($datos[$j]['tot']=='1') {
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $datos[$k][$fechas[$i]['fecha']]=$datos[$k][$fechas[$i]['fecha']]+$datos[$j][$fechas[$i]['fecha']];
                                $total=$total+$datos[$k][$fechas[$i]['fecha']];
                            }
                            $datos[$k]['por']=round(($total/$totales)*100,2);
                            $datos[$k]['cos']=$total;
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='4') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                        if ($datos[$j]['tot']=='3') {
                            $total=0;
                            for ($i = 1; $i <= count($dias); ++$i) {
                                $datos[$k][$fechas[$i]['fecha']]=round($datos[$j][$fechas[$i]['fecha']]/$dias[$i]['fecha']/$request->get('hectareas'),2);
                                $total=$total+$dias[$i]['fecha'];
                            }
                            $datos[$k]['cos']=round($totales/($total)/$request->get('hectareas'),2);
                            $datos[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='5') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                        if ($datos[$j]['tot']=='1') {
                            if ($datos[$k]['cat']==$datos[$j]['doc']) {
                                $total=0;
                                for ($i = 1; $i <= count($fechas); ++$i) {
                                    $datos[$k][$fechas[$i]['fecha']]=$datos[$k][$fechas[$i]['fecha']]+$datos[$j][$fechas[$i]['fecha']];
                                    $total=$total+$datos[$k][$fechas[$i]['fecha']];
                                }
                                $datos[$k]['cos']=$total;
                                $datos[$k]['por']='0';
                            }
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='6') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                        $total=0;
                        if ($datos[$j]['tot']=='5') {
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $datos[$k][$fechas[$i]['fecha']]=$datos[$k][$fechas[$i]['fecha']]+$datos[$j][$fechas[$i]['fecha']];
                                $total=$total+$datos[$k][$fechas[$i]['fecha']];
                            }
                            $datos[$k]['cos']=$total;
                            $datos[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                $total=0;
                $stotal=0;
                if ($datos[$k]['tot']=='7') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                       
                        if ($datos[$j]['tot']=='6') {
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $datos[$k][$fechas[$i]['fecha']]=round($datos[$j][$fechas[$i]['fecha']]/$dias[$i]['fecha']/$request->get('hectareas'),2);
                                $total=$total+$dias[$i]['fecha'];
                            }

                            $datos[$k]['cos']=round($datos[$j]['cos']/$total/$request->get('hectareas'),2);
                            $datos[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='8') {
                    
                    $ttotal=0;
                    $tstotal=0;
                        for ($i = 1; $i <= count($fechas); ++$i) {
                            $total=0;
                            for ($j = 1; $j <= count($datos); ++$j) {
                                if ($datos[$j]['tot']=='4') {
                                    $total=$total+$datos[$j][$fechas[$i]['fecha']];
                                    $ttotal=$datos[$j]['cos'];
                                }
                                if ($datos[$j]['tot']=='7') {
                                    $stotal=$total-$datos[$j][$fechas[$i]['fecha']];
                                    $tstotal=$datos[$j]['cos'];
                                }
                            }
                            $datos[$k][$fechas[$i]['fecha']]=$stotal;
                        }

                        $datos[$k]['cos']=$ttotal-$tstotal;
                        $datos[$k]['por']='0';
                    
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.centroconsumoimpresion', ['hectarea'=>$request->get('hectareas'),'dias'=>$dias,'fechas'=>$fechas,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'empresa'=>$empresa]);
            $nombreArchivo = 'centroconsumodetalle';
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');

        }catch(\Exception $ex){
            return redirect('listaConsumo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    
    public function buscar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $fechas = null;
            $dias = null;
            $count = 1;
            $totales = 0;
            setlocale(LC_TIME, "es");
            $inicio = new DateTime($request->get('idDesde'));
            $fin = new DateTime($request->get('idHasta'));
            while ($inicio <= $fin) {
                 $inicio->format('m-Y');
                $fe='01-'.$inicio->format('m-Y');
                $dia = date('t', strtotime($fe));
                $fechas[$count]['fecha'] = strftime("%B", strtotime($fe)).'-'.$inicio->format('Y');
                $dias[$count]['fecha'] =  $dia;
                $count ++;
                $inicio->modify('+ 1 month');
            }            
            $count = 1;
            $movmientos=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(movimiento_total) as suma,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->get(); 
            $movmientoscat=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_producto.categoria_id')->selectRaw('sum(movimiento_total) as suma,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_rol.categoria_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->orderby('categoria')->get(); 
            foreach ($movmientos as $centro) {
                $datos[$count]['fec'] = ''; 
                $datos[$count]['doc'] = $centro->centro; 
                $datos[$count]['cat'] = ''; 
                for ($i = 1; $i <= count($fechas); ++$i){
                    $datos[$count][$fechas[$i]['fecha']]=0;
                }
                $datos[$count]['cos'] = $centro->suma; 
                $datos[$count]['por'] = 0; 
                $datos[$count]['tot'] = '1';
                $totales=$totales+$centro->suma; 
                $count ++;
                foreach ($movmientoscat as $centrocat) {
                    if($centro->centro_id==$centrocat->centro_id){
                        $datos[$count]['fec'] = ''; 
                        $datos[$count]['doc'] = $centrocat->centro; 
                        $datos[$count]['cat'] = $centrocat->categoria;
                        for ($i = 1; $i <= count($fechas); ++$i){
                            $datos[$count][$fechas[$i]['fecha']]=0;
                        }
                        $datos[$count]['cos'] = $centrocat->suma; 
                        $datos[$count]['por'] = 0; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                }
            }
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'TOTAL COSTOS BRUTOS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '3';
            $count ++;

            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'COSTO HECTAREA DIA TOTAL'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '4';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'LARVA'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '5';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'BALANCEADO'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '5';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'MEJORAS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '5';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'ACTIVOS FIJOS'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '5';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'TOTAL COSTOS NO INCLUIDOS EN COSTO/HA/DIA'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '6';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'COSTO HECTAREA DIA TOTAL'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '7';

            $count ++;
            $datos[$count]['fec'] = ''; 
            $datos[$count]['doc'] = ''; 
            $datos[$count]['cat'] = 'COSTO HECTAREA DIA PROMEDIO '.count($fechas).' MESES'; 
            for ($i = 1; $i <= count($fechas); ++$i){
                $datos[$count][$fechas[$i]['fecha']]=0;
            }
            $datos[$count]['cos'] = 0; 
            $datos[$count]['por'] = 0; 
            $datos[$count]['tot'] = '8';


            $movmientos=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('movimiento_fecha')->selectRaw('sum(movimiento_total) as suma,movimiento_fecha as fecha,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('cabecera_rol_fecha')->groupBy('centro_consumo.centro_consumo_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,cabecera_rol_fecha as fecha,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->get();       
            for ($j = 1; $j <= count($datos); ++$j){
                if ($datos[$j]['tot']=='1'){
                foreach ($movmientos as $centro) {
                        if ($datos[$j]['doc']==$centro->centro) {
                            $inicio = new DateTime($centro->fecha);
                            $inicio=strftime("%B", strtotime($centro->fecha)).'-'.$inicio->format('Y');
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                               
                                if($fechas[$i]['fecha']==$inicio){
                                    $datos[$j][$inicio]=$datos[$j][$inicio]+round($centro->suma,2); 
                                }
                                $total=$total+$datos[$j][$fechas[$i]['fecha']];  
                                $datos[$j]['por']=round(($total/$totales)*100,2);
                                $datos[$j]['cos']=$total;
                            }
                        }
                    }
                }
            }
            $movmientoscat=Movimiento_Producto::MovimientoComprasByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('centro_consumo.centro_consumo_id')->groupBy('movimiento_producto.movimiento_fecha')->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_producto.categoria_id')->selectRaw('sum(movimiento_total) as suma,movimiento_fecha as fecha,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id')->union(Cabecera_Rol_CM::MovimientoRolesByCC($request->get('idCentroc'),$request->get('idDesde'),$request->get('idHasta'))->groupBy('cabecera_rol_fecha')->groupBy('centro_consumo.centro_consumo_id')->groupBy('categoria_rol.categoria_id')->selectRaw('sum(detalle_rol_cm.detalle_rol_valor) as suma,cabecera_rol_fecha as fecha,categoria_nombre as categoria,centro_consumo_nombre as centro,centro_consumo.centro_consumo_id as centro_id'))->orderby('centro')->orderby('categoria')->get(); 
            for ($j = 1; $j <= count($datos); ++$j){
                if ($datos[$j]['tot']=='2'){
                foreach ($movmientoscat as $centro) {
                        if ($datos[$j]['doc']==$centro->centro && $datos[$j]['cat']==$centro->categoria) {
                            $inicio = new DateTime($centro->fecha);
                            $inicio=strftime("%B", strtotime($centro->fecha)).'-'.$inicio->format('Y');
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                               
                                if($fechas[$i]['fecha']==$inicio){
                                    $datos[$j][$inicio]=$datos[$j][$inicio]+round($centro->suma,2); 
                                }
                                $total=$total+$datos[$j][$fechas[$i]['fecha']];
                                $datos[$j]['por']=round(($total/$totales)*100,2);
                                $datos[$j]['cos']=$total;
                            }
                        }
                    }
                }
            }
           
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='3') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                        if ($datos[$j]['tot']=='1') {
                            $total=0;
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $datos[$k][$fechas[$i]['fecha']]=$datos[$k][$fechas[$i]['fecha']]+$datos[$j][$fechas[$i]['fecha']];
                                $total=$total+$datos[$k][$fechas[$i]['fecha']];
                            }
                            $datos[$k]['por']=round(($total/$totales)*100,2);
                            $datos[$k]['cos']=$total;
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='4') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                        if ($datos[$j]['tot']=='3') {
                            $total=0;
                            for ($i = 1; $i <= count($dias); ++$i) {
                                $datos[$k][$fechas[$i]['fecha']]=round($datos[$j][$fechas[$i]['fecha']]/$dias[$i]['fecha']/$request->get('hectareas'),2);
                                $total=$total+$dias[$i]['fecha'];
                            }
                            $datos[$k]['cos']=round($totales/($total)/$request->get('hectareas'),2);
                            $datos[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='5') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                        if ($datos[$j]['tot']=='1') {
                            if ($datos[$k]['cat']==$datos[$j]['doc']) {
                                $total=0;
                                for ($i = 1; $i <= count($fechas); ++$i) {
                                    $datos[$k][$fechas[$i]['fecha']]=$datos[$k][$fechas[$i]['fecha']]+$datos[$j][$fechas[$i]['fecha']];
                                    $total=$total+$datos[$k][$fechas[$i]['fecha']];
                                }
                                $datos[$k]['cos']=$total;
                                $datos[$k]['por']='0';
                            }
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='6') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                        $total=0;
                        if ($datos[$j]['tot']=='5') {
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $datos[$k][$fechas[$i]['fecha']]=$datos[$k][$fechas[$i]['fecha']]+$datos[$j][$fechas[$i]['fecha']];
                                $total=$total+$datos[$k][$fechas[$i]['fecha']];
                            }
                            $datos[$k]['cos']=$total;
                            $datos[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                $total=0;
                $stotal=0;
                if ($datos[$k]['tot']=='7') {
                    for ($j = 1; $j <= count($datos); ++$j) {
                       
                        if ($datos[$j]['tot']=='6') {
                            for ($i = 1; $i <= count($fechas); ++$i) {
                                $datos[$k][$fechas[$i]['fecha']]=round($datos[$j][$fechas[$i]['fecha']]/$dias[$i]['fecha']/$request->get('hectareas'),2);
                                $total=$total+$dias[$i]['fecha'];
                            }

                            $datos[$k]['cos']=round($datos[$j]['cos']/$total/$request->get('hectareas'),2);
                            $datos[$k]['por']='0';
                        }
                    }
                }
            }
            for ($k = 1; $k <= count($datos); ++$k) {
                if ($datos[$k]['tot']=='8') {
                    
                    $ttotal=0;
                    $tstotal=0;
                        for ($i = 1; $i <= count($fechas); ++$i) {
                            $total=0;
                            for ($j = 1; $j <= count($datos); ++$j) {
                                if ($datos[$j]['tot']=='4') {
                                    $total=$total+$datos[$j][$fechas[$i]['fecha']];
                                    $ttotal=$datos[$j]['cos'];
                                }
                                if ($datos[$j]['tot']=='7') {
                                    $stotal=$total-$datos[$j][$fechas[$i]['fecha']];
                                    $tstotal=$datos[$j]['cos'];
                                }
                            }
                            $datos[$k][$fechas[$i]['fecha']]=$stotal;
                        }

                        $datos[$k]['cos']=$ttotal-$tstotal;
                        $datos[$k]['por']='0';
                    
                }
            }
           
            return view('admin.compras.reporteCentroConsumoDetalle.index',['hectarea'=>$request->get('hectareas'),'dias'=>$dias,'fechas'=>$fechas,'cc'=>$request->get('idCentroc'),'fecI'=>$request->get('idDesde'),'fecF'=>$request->get('idHasta'),'datos'=>$datos,'CentroConsumos'=>Centro_Consumo::CentroConsumos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listaConsumo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function generar(Request $request){
        
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
