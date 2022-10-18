<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use DateTime;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class mayorClientesController extends Controller
{
    public function nuevo()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.mayorClientes.index',['cuentas'=>Cuenta::CuentasMovimiento()->get(),'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
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
            $datos = null;
            $count = 1;
            $debe = 0;
            $haber = 0;
            $datos[$count]['fec'] = 'Saldo Anterior';
            $datos[$count]['doc'] = '';
            $datos[$count]['num'] = '';
            $datos[$count]['deb'] = '';
            $datos[$count]['hab'] = '';
            $datos[$count]['act'] = Detalle_Diario::MayorClienteAntCuenta($request->get('clienteID'),$request->get('fecha_desde'),'0',$request->get('cuenta_id'))->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo;
            if($datos[$count]['act'] == ''){
                $datos[$count]['act'] = 0;
            }
            $datos[$count]['dia'] = '';
            $datos[$count]['com'] = '';
            $datos[$count]['suc'] = '';
            $datos[$count]['tot'] = '1';
            $count ++;
            foreach(Detalle_Diario::MayorClienteCuenta($request->get('clienteID'),$request->get('fecha_desde'),$request->get('fecha_hasta'),'0',$request->get('cuenta_id'))->get() as $detalle){
                $datos[$count]['fec'] = $detalle->diario->diario_fecha;
                $datos[$count]['doc'] = $detalle->detalle_tipo_documento;
                $datos[$count]['num'] = '';
                $datos[$count]['deb'] = $detalle->detalle_debe;
                $datos[$count]['hab'] = $detalle->detalle_haber;
                $datos[$count]['act'] = doubleval($datos[$count-1]['act']) + doubleval($datos[$count]['deb']) - doubleval($datos[$count]['hab']);;
                $datos[$count]['dia'] = $detalle->diario->diario_codigo;
                $datos[$count]['com'] = 'Decripcion: '.$detalle->detalle_comentario.' '.'Comentario: '.$detalle->diario->diario_comentario; 
                $datos[$count]['suc'] = $detalle->diario->sucursal->sucursal_nombre;
                $datos[$count]['tot'] = '0';
                $debe = $debe + doubleval($datos[$count]['deb']);
                $haber = $haber + doubleval($datos[$count]['hab']);
                $count ++;
            }
            if($debe > 0 or $haber >0){
                $datos[$count]['fec'] = '';
                $datos[$count]['doc'] = '';
                $datos[$count]['num'] = '';
                $datos[$count]['deb'] = $debe;
                $datos[$count]['hab'] = $haber;
                $datos[$count]['act'] = '';
                $datos[$count]['dia'] = '';
                $datos[$count]['com'] = '';
                $datos[$count]['suc'] = '';
                $datos[$count]['tot'] = '2';
            }
            if( $datos[$count-1]['tot'] == '1' ){
                array_pop($datos);
                $count = $count - 1;
            }
            return view('admin.contabilidad.mayorClientes.index',['cuentaC'=>$request->get('cuenta_id'),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'clienteC'=>$request->get('clienteID'),'fDesde'=>$request->get('fecha_desde'),'fHasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('mayorClientes')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request){
        try{            
            $datos = null;
            $count = 1;
            $fec = $request->get('idFec');
            $doc = $request->get('idDoc');
            $num = $request->get('idNum');
            $deb = $request->get('idDeb');
            $hab = $request->get('idHab');
            $act = $request->get('idAct');
            $dia = $request->get('idDia');
            $com = $request->get('idCom');
            $suc = $request->get('idSuc');
            $tot = $request->get('idTot');
            if($fec){
                for ($i = 0; $i < count($fec); ++$i){
                    $datos[$count]['fec'] = $fec[$i];
                    $datos[$count]['doc'] = $doc[$i];
                    $datos[$count]['num'] = $num[$i];
                    $datos[$count]['deb'] = $deb[$i];
                    $datos[$count]['hab'] = $hab[$i];
                    $datos[$count]['act'] = $act[$i];
                    $datos[$count]['dia'] = $dia[$i];
                    $datos[$count]['com'] = $com[$i];
                    $datos[$count]['suc'] = $suc[$i];
                    $datos[$count]['tot'] = $tot[$i];
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.mayorclientes', ['datos'=>$datos,'desde'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'MAYOR DE CLIENTES '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
            //return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->stream($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('mayorClientes')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
