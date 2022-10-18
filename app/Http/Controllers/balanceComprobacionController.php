<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use PDF;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class balanceComprobacionController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.balances.comprobacion',['cuentas'=>Cuenta::Cuentas()->get(),'cuentaFinal'=>Cuenta::CuentasDesc()->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
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
            $datos = null;
            $totDebe = 0;
            $totHaber = 0;
            foreach(Cuenta::CuentasRango($request->get('cuenta_inicio'),$request->get('cuenta_fin'))->get() as $cuenta){
                $datos[$count]['numero'] = $cuenta->cuenta_numero;
                $datos[$count]['nombre'] = $cuenta->cuenta_nombre;
                $datos[$count]['saldoAnt'] = Detalle_Diario::SaldoAnteriorCuenta($cuenta->cuenta_id,$request->get('fecha_desde'))->select(DB::raw('SUM(detalle_debe) - SUM(detalle_haber) as saldo'))->first()->saldo;
                $datos[$count]['debe'] = Detalle_Diario::SaldoActualCuentaByFecha($cuenta->cuenta_id,$request->get('fecha_desde'),$request->get('fecha_hasta'))->sum('detalle_debe');
                $datos[$count]['haber'] = Detalle_Diario::SaldoActualCuentaByFecha($cuenta->cuenta_id,$request->get('fecha_desde'),$request->get('fecha_hasta'))->sum('detalle_haber');
                $valor = $datos[$count]['saldoAnt'] + $datos[$count]['debe'] - $datos[$count]['haber'];
                $totDebe = $totDebe + $datos[$count]['debe'];
                $totHaber = $totHaber + $datos[$count]['haber'];
                if($valor > 0){
                    $datos[$count]['deudor'] = $valor;
                    $datos[$count]['acreedor'] = 0;
                }else{
                    $datos[$count]['deudor'] = 0;
                    $datos[$count]['acreedor'] = $valor;
                }   
                if($datos[$count]['saldoAnt'] <> 0 or $datos[$count]['debe'] <> 0 or $datos[$count]['haber'] <> 0 or $datos[$count]['deudor'] <> 0 or $datos[$count]['acreedor'] <> 0){
                    $count ++;
                }else{
                    array_pop($datos);
                }
            }
            return view('admin.contabilidad.balances.comprobacion',['ini'=>$request->get('cuenta_inicio'),'fin'=>$request->get('cuenta_fin'),'fDesde'=>$request->get('fecha_desde'),'fHasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'totDebe'=>$totDebe,'totHaber'=>$totHaber,'cuentas'=>Cuenta::Cuentas()->get(),'cuentaFinal'=>Cuenta::CuentasDesc()->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('balanceComprobacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request){
        try{            
            $datos = null;
            $count = 1;
            $totAnt = 0;
            $totDeu = 0;
            $totAcr = 0;
            $totDebe = 0;
            $totHaber = 0;
            $num = $request->get('idNum');
            $nom = $request->get('idNom');
            $sal = $request->get('idSal');
            $deb = $request->get('idDeb');
            $hab = $request->get('idHab');
            $deu = $request->get('idDeu');
            $acr = $request->get('idAcr');
            if($num){
                for ($i = 0; $i < count($num); ++$i){
                    $datos[$count]['numero'] = $num[$i];
                    $datos[$count]['nombre'] = $nom[$i];
                    $datos[$count]['saldoAnt'] = $sal[$i];
                    $datos[$count]['debe'] = $deb[$i];
                    $datos[$count]['haber'] = $hab[$i];
                    $datos[$count]['deudor'] = $deu[$i];
                    $datos[$count]['acreedor'] = $acr[$i];
                    $totDebe = $totDebe + $datos[$count]['debe'];
                    $totHaber = $totHaber + $datos[$count]['haber'];
                    $totAnt = $totAnt + $datos[$count]['saldoAnt'];
                    $totDeu = $totDeu + $datos[$count]['deudor'];
                    $totAcr =  $totAcr + $datos[$count]['acreedor'];
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.balances.pdfComprobacion', ['totAnt'=>$totAnt,'totDeu'=>$totDeu,'totAcr'=>$totAcr,'totDebe'=>$totDebe,'totHaber'=>$totHaber,'datos'=>$datos,'desde'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'BALANCE COMPROBACION '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
            //return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->stream($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('balanceComprobacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
