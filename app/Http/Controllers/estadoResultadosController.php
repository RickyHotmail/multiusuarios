<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use PDF;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class estadoResultadosController extends Controller
{
    public function nuevo()
    {
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.balances.resultados',['niveles'=>Cuenta::CuentasNivel()->distinct('cuenta_nivel')->get(),'sucursales'=>Sucursal::sucursales()->get(),'cuentas'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','asc')->get(),'cuentaFinal'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','desc')->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {   
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['pdf'])){
            return $this->pdf($request);
        }
    }
    public function buscar(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $count2 = 1;
            if($request->get('sucursal_id') == 0){
                $sucursales=Sucursal::sucursales()->get();
                $cantSucursal = Sucursal::sucursales()->count('sucursal_id');
            }else{
                $sucursales=Sucursal::sucursal($request->get('sucursal_id'))->get();
                $cantSucursal = 1;
            }
            $datos = null;
            $resultado = null;
            $totIng = 0;
            $totEgr = 0;
            foreach($sucursales as $sucursal){
                $totIng = $totIng + Detalle_Diario::SaldoActualByFecha('4',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo;
                $totEgr = $totEgr + Detalle_Diario::SaldoActualByFecha('5',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo+Detalle_Diario::SaldoActualByFecha('6',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo;
                $resultado[$count]= abs(Detalle_Diario::SaldoActualByFecha('4',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo) 
                - abs(Detalle_Diario::SaldoActualByFecha('5',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo) 
                -  abs(Detalle_Diario::SaldoActualByFecha('6',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo);
                $count ++;
            }
            $count = 1;
            $tot = 0;
            foreach(Cuenta::CuentasRango($request->get('cuenta_inicio'),$request->get('cuenta_fin'))->where('cuenta_nivel','<=',$request->get('nivel'))->get() as $cuenta){
                $datos[$count]['numero'] = $cuenta->cuenta_numero;
                $datos[$count]['nombre'] = $cuenta->cuenta_nombre;
                $datos[$count]['nivel'] = $cuenta->cuenta_nivel;
                $count2=1;
                $tot = 0;
                foreach($sucursales as $sucursal){
                    $datos[$count][$count2] = Detalle_Diario::SaldoActualByFecha($cuenta->cuenta_numero,$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo;
                    $tot = $tot + $datos[$count][$count2];
                    $count2 ++;
                }
                $datos[$count]['total'] = $tot;
                if(round($datos[$count]['total'],2) <> 0 ){
                    $count ++;
                }else{
                    array_pop($datos);
                }
            }
            $datos[$count]['numero'] = '';
            $datos[$count]['nombre'] = 'RESULTADO';
            $datos[$count]['nivel'] = '';
            for($i=1;$i <=count($resultado);$i++){
                $datos[$count][$i] = $resultado[$i];
            }
            $datos[$count]['total'] = abs($totIng) - abs($totEgr);
            return view('admin.contabilidad.balances.resultados',['asientoCierreC'=>$request->get('asiento_cierre'),'ini'=>$request->get('cuenta_inicio'),'fin'=>$request->get('cuenta_fin'),'totIng'=>$totIng,'totEgr'=>$totEgr,'nivelC'=>$request->get('nivel'),'sucursalC'=>$request->get('sucursal_id'),'niveles'=>Cuenta::CuentasNivel()->distinct('cuenta_nivel')->get(),'sucursales'=>Sucursal::sucursales()->get(),'sucuralesC'=>$sucursales,'cantSucursal'=>$cantSucursal,'fDesde'=>$request->get('fecha_desde'),'fHasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'cuentas'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','asc')->get(),'cuentaFinal'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','desc')->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('estadoResultados')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request){
        try{            
            $datos = null;
            $count = 1;
            $totIng = $request->get('totIng');
            $totEgr = $request->get('totEgr');
            $total = $request->get('tot');
            $num = $request->get('idNum');
            $nom = $request->get('idNom');
            $niv = $request->get('idNiv');
            $tot = $request->get('idTot');
            if($num){
                for ($i = 0; $i < count($num); ++$i){
                    if($nom[$i] != 'RESULTADO'){
                        $datos[$count]['numero'] = $num[$i];
                        $datos[$count]['nombre'] = $nom[$i];
                        $datos[$count]['nivel'] = $niv[$i];
                        $datos[$count]['total'] = $tot[$i];
                        $count ++;
                    }
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.balances.pdfResultados', ['totIng'=>$totIng,'totEgr'=>$totEgr,'total'=>$total,'datos'=>$datos,'desde'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'ESTADO DE RESULTADOS '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
            //return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->stream($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('estadoResultados')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
