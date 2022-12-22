<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cierre_Mes_Contable;
use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class cierreAnualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
    }
    public function nuevo()
    {
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.cierreAnual.index',['sucursales'=>Sucursal::sucursales()->get(),'niveles'=>Cuenta::CuentasNivel()->distinct('cuenta_nivel')->get(),'cuentas'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','asc')->get(),'cuentaFinal'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','desc')->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function nuevofinanciero()
    {
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.cierreAnual.indexfinanciero',['resultado'=>Parametrizacion_Contable ::ParametrizacionByNombreFinanciero('RESULTADOS DEL EJERCICIO')->first()->cuenta_id,'niveles'=>Cuenta::CuentasNivel()->distinct('cuenta_nivel')->get(),'sucursales'=>Sucursal::sucursales()->get(),'cuentas'=>Cuenta::CuentasFinanciero()->orderBy('cuenta_numero','asc')->get(),'cuentaFinal'=>Cuenta::CuentasFinanciero()->orderBy('cuenta_numero','desc')->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
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
    public function consultar(Request $request)
    {   
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['guardar'])){
            return $this->guardarresutados($request);
        }
        if (isset($_POST['buscarfinanciero'])){
            return $this->buscarfinanciero($request);
        }
        if (isset($_POST['guardarfinanciero'])){
            return $this->guardarfinanciero($request);
        }
    }
    public function buscar(Request $request){
        try{   
            
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $count2 = 2;
            if($request->get('sucursal_id') == 0){
                $sucursales=Sucursal::sucursales()->get();
                $cantSucursal = Sucursal::sucursales()->count('sucursal_id');
            }else{
                $sucursales=Sucursal::sucursal($request->get('sucursal_id'))->get();
                $cantSucursal = 1;
            }
            
            $datos = null;
           
            $totIng = 0;
            $totEgr = 0;
            $activador=false;
            foreach($sucursales as $sucursal){
                $resultado[1]['haber'.$sucursal->sucursal_id]= abs(Detalle_Diario::SaldoActualByFecha('4',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo) 
                + abs(Detalle_Diario::SaldoActualByFecha('5',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo) 
                +  abs(Detalle_Diario::SaldoActualByFecha('6',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo);
                $resultado[1]['debe'.$sucursal->sucursal_id]= abs(Detalle_Diario::SaldoActualByFecha('4',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo) 
                + abs(Detalle_Diario::SaldoActualByFecha('5',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo) 
                +  abs(Detalle_Diario::SaldoActualByFecha('6',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo);
                if( round($resultado[1]['haber'.$sucursal->sucursal_id],2) <> 0){
                    $activador=true;
                }
                if( round($resultado[1]['debe'.$sucursal->sucursal_id],2) <> 0){
                    $activador=true;
                }

            }
            if($activador==false){  
                return redirect('cierreContable')->with('error2','NO CONTIENE NINGUN MOVIMIENTO LA SUCURSAL VERIFIQUE POR FAVOR.');
            }
            $count = 1;
            foreach(Cuenta::CuentasRango($request->get('cuenta_inicio'),$request->get('cuenta_fin'))->where('cuenta_nivel','<=',$request->get('nivel'))->get() as $cuenta){
                $datos[$count]['cuenta'] = $cuenta->cuenta_id;
                $datos[$count]['numero'] = $cuenta->cuenta_numero;
                $datos[$count]['nombre'] = $cuenta->cuenta_nombre;
                $datos[$count]['nivel'] = $cuenta->cuenta_nivel;  
                $activador=false;
                foreach ($sucursales as $sucursal) {
                    $datos[$count]['haber'.$sucursal->sucursal_id] = Detalle_Diario::SaldoActualByFechaCuenta($cuenta->cuenta_id, $request->get('fecha_desde'), $request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo;
                    $datos[$count]['debe'.$sucursal->sucursal_id] = Detalle_Diario::SaldoActualByFechaCuenta($cuenta->cuenta_id, $request->get('fecha_desde'), $request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo;   
                    $datos[$count]['sucursal']=$sucursal->sucursal_id;
                    if( round($datos[$count]['haber'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                    if( round($datos[$count]['debe'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                }
                if($activador==true){  
                    $count ++;
                }else{
                    array_pop($datos);
                }
                
            }
            $activador=false;
            $activador2=false;
            foreach($sucursales as $sucursal){
                if(round($resultado[1]['debe'.$sucursal->sucursal_id],2)>=round($resultado[1]['haber'.$sucursal->sucursal_id],2)){
                    $parametrizacion=Sucursal::SucursalByContable($sucursal->sucursal_id,'UTILIDA DEL EJERCICIO')->first();
                    $datos[$count]['cuenta'] = $parametrizacion->cuenta_id;
                    $datos[$count]['numero'] = $parametrizacion->cuenta_numero;
                    $datos[$count]['nombre'] = $parametrizacion->cuenta_nombre;
                    $datos[$count]['nivel'] = 0;
                    $datos[$count]['haber'.$sucursal->sucursal_id] = round($resultado[1]['debe'.$sucursal->sucursal_id],2)-round($resultado[1]['haber'.$sucursal->sucursal_id],2);
                    $datos[$count]['debe'.$sucursal->sucursal_id] = 0; 
                    $activador2=true;
                    if( round($datos[$count]['haber'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                    if( round($datos[$count]['debe'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                  
                }
            }
            if ($activador2==true) {
                if ($activador==true) {
                    $count ++;
                } else {
                    array_pop($datos);
                }
            }
            $activador=false;
            $activador3=false;
            foreach($sucursales as $sucursal){  
                if(round($resultado[1]['debe'.$sucursal->sucursal_id],2)<=round($resultado[1]['haber'.$sucursal->sucursal_id],2)){
                    $parametrizacion=Sucursal::SucursalByContable($sucursal->sucursal_id,'PERDIDA DEL EJERCICIO')->first();
                    $datos[$count]['cuenta'] = $parametrizacion->cuenta_id;
                    $datos[$count]['numero'] = $parametrizacion->cuenta_numero;
                    $datos[$count]['nombre'] = $parametrizacion->cuenta_nombre;
                    $datos[$count]['nivel'] = 0;
                 
                    $datos[$count]['debe'.$sucursal->sucursal_id] = round($resultado[1]['haber'.$sucursal->sucursal_id],2)-round($resultado[1]['debe'.$sucursal->sucursal_id],2);
                    $datos[$count]['haber'.$sucursal->sucursal_id] = 0; 
                    $activador3=true;
                    if( round($datos[$count]['haber'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                    if( round($datos[$count]['debe'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                }  
            }
            if ($activador3==true) {
                if ($activador==true) {
                    $count ++;
                } else {
                    array_pop($datos);
                }
            }
            
            $datos[$count]['cuenta'] = '0';
            $datos[$count]['numero'] = '';
            $datos[$count]['nombre'] = 'RESULTADO';
            $datos[$count]['nivel'] = 0;
            foreach($sucursales as $sucursal){
                if ($activador3==true || $activador2==true) {
                    $datos[$count]['haber'.$sucursal->sucursal_id] = $resultado[1]['haber'.$sucursal->sucursal_id]+$datos[$count-1]['haber'.$sucursal->sucursal_id];
                    $datos[$count]['debe'.$sucursal->sucursal_id] = $resultado[1]['debe'.$sucursal->sucursal_id]+$datos[$count-1]['debe'.$sucursal->sucursal_id];
                }
                if ($activador3==true && $activador2==true) {
                    $datos[$count]['haber'.$sucursal->sucursal_id] = $resultado[1]['haber'.$sucursal->sucursal_id]+$datos[$count-2]['haber'.$sucursal->sucursal_id]+$datos[$count-1]['haber'.$sucursal->sucursal_id];
                    $datos[$count]['debe'.$sucursal->sucursal_id] = $resultado[1]['debe'.$sucursal->sucursal_id]+$datos[$count-2]['haber'.$sucursal->sucursal_id]+$datos[$count-1]['debe'.$sucursal->sucursal_id];
                }
               
            }
          
            
            return view('admin.contabilidad.cierreAnual.index',['sucuralesC'=>$sucursales,'sucursalC'=>$request->get('sucursal_id'),'cantSucursal'=>$cantSucursal,'sucursales'=>Sucursal::sucursales()->get(),'asientoCierreC'=>$request->get('asiento_cierre'),'ini'=>$request->get('cuenta_inicio'),'fin'=>$request->get('cuenta_fin'),'totIng'=>$totIng,'totEgr'=>$totEgr,'nivelC'=>$request->get('nivel'),'niveles'=>Cuenta::CuentasNivel()->distinct('cuenta_nivel')->get(),'sucursales'=>Sucursal::sucursales()->get(),'fDesde'=>$request->get('fecha_desde'),'fHasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'cuentas'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','asc')->get(),'cuentaFinal'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','desc')->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('cierreContable')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarfinanciero(Request $request){
        try{   
            
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $count2 = 2;
            if($request->get('sucursal_id') == 0){
                $sucursales=Sucursal::sucursales()->get();
                $cantSucursal = Sucursal::sucursales()->count('sucursal_id');
            }else{
                $sucursales=Sucursal::sucursal($request->get('sucursal_id'))->get();
                $cantSucursal = 1;
            }
            
            $datos = null;
           
            $totIng = 0;
            $totEgr = 0;
            $activador=false;
            foreach($sucursales as $sucursal){
                $resultado[1]['haber'.$sucursal->sucursal_id]= abs(Detalle_Diario::SaldoActualByFecha('1',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo) 
                + abs(Detalle_Diario::SaldoActualByFecha('2',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo) 
                +  abs(Detalle_Diario::SaldoActualByFecha('3',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo);
                $resultado[1]['debe'.$sucursal->sucursal_id]= abs(Detalle_Diario::SaldoActualByFecha('1',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo) 
                + abs(Detalle_Diario::SaldoActualByFecha('2',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo) 
                +  abs(Detalle_Diario::SaldoActualByFecha('3',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo);
                if( round($resultado[1]['haber'.$sucursal->sucursal_id],2) <> 0){
                    $activador=true;
                }
                if( round($resultado[1]['debe'.$sucursal->sucursal_id],2) <> 0){
                    $activador=true;
                }

            }
            if($activador==false){  
                return redirect('cierreContable')->with('error2','NO CONTIENE NINGUN MOVIMIENTO LA SUCURSAL VERIFIQUE POR FAVOR.');
            }
            $count = 1;
            foreach(Cuenta::CuentasRango($request->get('cuenta_inicio'),$request->get('cuenta_fin'))->where('cuenta_nivel','<=',$request->get('nivel'))->get() as $cuenta){
                $datos[$count]['cuenta'] = $cuenta->cuenta_id;
                $datos[$count]['numero'] = $cuenta->cuenta_numero;
                $datos[$count]['nombre'] = $cuenta->cuenta_nombre;
                $datos[$count]['nivel'] = $cuenta->cuenta_nivel;  
                $activador=false;
                foreach ($sucursales as $sucursal) {
                    $datos[$count]['haber'.$sucursal->sucursal_id] = Detalle_Diario::SaldoActualByFechaCuenta($cuenta->cuenta_id, $request->get('fecha_desde'), $request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo;
                    $datos[$count]['debe'.$sucursal->sucursal_id] = Detalle_Diario::SaldoActualByFechaCuenta($cuenta->cuenta_id, $request->get('fecha_desde'), $request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo;   
                    $datos[$count]['sucursal']=$sucursal->sucursal_id;
                    if( round($datos[$count]['haber'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                    if( round($datos[$count]['debe'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                }
                if($activador==true){  
                    $count ++;
                }else{
                    array_pop($datos);
                }
                
            }
            $activador=false;
            $activador2=false;
            foreach($sucursales as $sucursal){
                if(round($resultado[1]['debe'.$sucursal->sucursal_id],2)>=round($resultado[1]['haber'.$sucursal->sucursal_id],2)){
                    $parametrizacion=Sucursal::SucursalByContable($sucursal->sucursal_id,'UTILIDADES ACUMULADAS')->first();
                    $datos[$count]['cuenta'] = $parametrizacion->cuenta_id;
                    $datos[$count]['numero'] = $parametrizacion->cuenta_numero;
                    $datos[$count]['nombre'] = $parametrizacion->cuenta_nombre;
                    $datos[$count]['nivel'] = 0;
                    $datos[$count]['haber'.$sucursal->sucursal_id] = round($resultado[1]['debe'.$sucursal->sucursal_id],2)-round($resultado[1]['haber'.$sucursal->sucursal_id],2);
                    $datos[$count]['debe'.$sucursal->sucursal_id] = 0; 
                    $activador2=true;
                    if( round($datos[$count]['haber'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                    if( round($datos[$count]['debe'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                  
                }
            }
            if ($activador2==true) {
                if ($activador==true) {
                    $count ++;
                } else {
                    array_pop($datos);
                }
            }
            $activador=false;
            $activador3=false;
            foreach($sucursales as $sucursal){  
                if(round($resultado[1]['debe'.$sucursal->sucursal_id],2)<=round($resultado[1]['haber'.$sucursal->sucursal_id],2)){
                    $parametrizacion=Sucursal::SucursalByContable($sucursal->sucursal_id,'PERDIDAS ACUMULADAS')->first();
                    $datos[$count]['cuenta'] = $parametrizacion->cuenta_id;
                    $datos[$count]['numero'] = $parametrizacion->cuenta_numero;
                    $datos[$count]['nombre'] = $parametrizacion->cuenta_nombre;
                    $datos[$count]['nivel'] = 0;
                 
                    $datos[$count]['debe'.$sucursal->sucursal_id] = round($resultado[1]['haber'.$sucursal->sucursal_id],2)-round($resultado[1]['debe'.$sucursal->sucursal_id],2);
                    $datos[$count]['haber'.$sucursal->sucursal_id] = 0; 
                    $activador3=true;
                    if( round($datos[$count]['haber'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                    if( round($datos[$count]['debe'.$sucursal->sucursal_id],2) <> 0){
                        $activador=true;
                    }
                }  
            }
            if ($activador3==true) {
                if ($activador==true) {
                    $count ++;
                } else {
                    array_pop($datos);
                }
            }
            
            $datos[$count]['cuenta'] = '0';
            $datos[$count]['numero'] = '';
            $datos[$count]['nombre'] = 'RESULTADO';
            $datos[$count]['nivel'] = 0;
            foreach($sucursales as $sucursal){
                if ($activador3==true || $activador2==true) {
                    $datos[$count]['haber'.$sucursal->sucursal_id] = $resultado[1]['haber'.$sucursal->sucursal_id]+$datos[$count-1]['haber'.$sucursal->sucursal_id];
                    $datos[$count]['debe'.$sucursal->sucursal_id] = $resultado[1]['debe'.$sucursal->sucursal_id]+$datos[$count-1]['debe'.$sucursal->sucursal_id];
                }
                if ($activador3==true && $activador2==true) {
                    $datos[$count]['haber'.$sucursal->sucursal_id] = $resultado[1]['haber'.$sucursal->sucursal_id]+$datos[$count-2]['haber'.$sucursal->sucursal_id]+$datos[$count-1]['haber'.$sucursal->sucursal_id];
                    $datos[$count]['debe'.$sucursal->sucursal_id] = $resultado[1]['debe'.$sucursal->sucursal_id]+$datos[$count-2]['haber'.$sucursal->sucursal_id]+$datos[$count-1]['debe'.$sucursal->sucursal_id];
                }
               
            }
          
            
            return view('admin.contabilidad.cierreAnual.indexfinanciero',['sucuralesC'=>$sucursales,'sucursalC'=>$request->get('sucursal_id'),'cantSucursal'=>$cantSucursal,'sucursales'=>Sucursal::sucursales()->get(),'asientoCierreC'=>$request->get('asiento_cierre'),'ini'=>$request->get('cuenta_inicio'),'fin'=>$request->get('cuenta_fin'),'totIng'=>$totIng,'totEgr'=>$totEgr,'nivelC'=>$request->get('nivel'),'niveles'=>Cuenta::CuentasNivel()->distinct('cuenta_nivel')->get(),'sucursales'=>Sucursal::sucursales()->get(),'fDesde'=>$request->get('fecha_desde'),'fHasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'cuentas'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','asc')->get(),'cuentaFinal'=>Cuenta::CuentasResultado()->orderBy('cuenta_numero','desc')->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);          
        }catch(\Exception $ex){
            return redirect('cierreFinanciero')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarresutados(Request $request){
        try{
            $general = new generalController();
            if($request->get('sucursal_id') == 0){
                $sucursales=Sucursal::sucursales()->get();
                $cantSucursal = Sucursal::sucursales()->count('sucursal_id');
            }else{
                $sucursales=Sucursal::sucursal($request->get('sucursal_id'))->get();
                $cantSucursal = 1;
            }
            $contDiarios = 0;
            foreach($sucursales as $sucursal){
                
                $activador=false;
                $resultado[1]['haber'.$sucursal->sucursal_id]= abs(Detalle_Diario::SaldoActualByFecha('4',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo) 
                + abs(Detalle_Diario::SaldoActualByFecha('5',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo) 
                +  abs(Detalle_Diario::SaldoActualByFecha('6',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo);
                $resultado[1]['debe'.$sucursal->sucursal_id]= abs(Detalle_Diario::SaldoActualByFecha('4',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo) 
                + abs(Detalle_Diario::SaldoActualByFecha('5',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo) 
                +  abs(Detalle_Diario::SaldoActualByFecha('6',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo);
                if( round($resultado[1]['haber'.$sucursal->sucursal_id],2) <> 0){
                    $activador=true;
                }
                if( round($resultado[1]['debe'.$sucursal->sucursal_id],2) <> 0){
                    $activador=true;
                }
                if ($activador==true) {
                    $sucurs='s'.$sucursal->sucursal_id;
                    $sucurs=$request->get($sucurs);
                    $debe='debe'.$sucursal->sucursal_id;
                    $debe=$request->get($debe);
                    $haber='haber'.$sucursal->sucursal_id;
                    $haber=$request->get($haber);
                    $cuenta='Cuenta'.$sucursal->sucursal_id;
                    $cuenta=$request->get($cuenta);
                   
                    $diario = new Diario();
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('fecha_hasta'), 'CDCR');
                    $diario->diario_fecha = $request->get('fecha_hasta');
                    $diario->diario_referencia = 'COMPROBANTE DE DIARIO DE CIERRE DE ESTADO DE RESULTADOS';
                    $diario->diario_tipo_documento = 'COMPROBANTE DE DIARIO DE CIERRE';
                    $diario->diario_numero_documento = 0;
                    $diario->diario_beneficiario = '';
                    $diario->diario_tipo = 'CDCR';
                    $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                    $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('m');
                    $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
                    $diario->diario_comentario = 'COMPROBANTE DE DIARIO DE CIERRE DE RESULTADOS DEL AÑO '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
                    $diario->diario_cierre = '0';
                    $diario->diario_estado = '1';
                    $diario->empresa_id = Auth::user()->empresa_id;
                    $diario->sucursal_id = $sucursal->sucursal_id;
                    $diario->save();
                    $diarios[$contDiarios] = $diario;
                    $contDiarios ++;
                    $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, '0', 'Tipo de Diario -> '.$diario->diario_referencia.'');
                    
                    $cierre=Cierre_Mes_Contable::CierreAnioSucursal(DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y'),$sucursal->sucursal_id)->first();
                    if($cierre){
                        $cierre=Cierre_Mes_Contable::findOrFail($cierre->cierre_id);
                    }else{
                        $cierre = new Cierre_Mes_Contable();
                    }
                    $cierre->cierre_ano = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
                    $cierre->cierre_01 = "1";
                    $cierre->cierre_02 = "1";
                    $cierre->cierre_03 = "1";
                    $cierre->cierre_04 = "1";
                    $cierre->cierre_05 = "1";
                    $cierre->cierre_06 = "1";
                    $cierre->cierre_07 = "1";
                    $cierre->cierre_08 = "1";
                    $cierre->cierre_09 = "1";
                    $cierre->cierre_10 = "1";
                    $cierre->cierre_11 = "1";
                    $cierre->cierre_12 = "1";
                    $cierre->cierre_estado = "1";
                    $cierre->sucursal_id = $sucursal->sucursal_id;
                    $cierre->save();
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Actualizacion de cierre de mes contable -> '.$cierre->cierre_ano.' Con Sucursal '.$sucursal->sucursal_nombre,'0','');
                    
                    foreach(Diario::DiariosCierre($request->get('fecha_desde'),$request->get('fecha_hasta'),$sucursal->sucursal_id)->get() as $detalle){
                        $diarioaux=Diario::findOrFail($detalle->diario_id);
                        $diarioaux->diario_cierre='1';
                        $diarioaux->diariocierre()->associate($diario);
                        $diarioaux->save();
                        
                    }
                    for ($i = 0; $i < count($sucurs); ++$i) {
                        if($cuenta[$i]!=0){
                            $activador=false;
                            if (floatval($debe[$i]) <> 0) {
                                $activador=true;
                            }
                            if (floatval($haber[$i]) <> 0) {
                                $activador=true;
                            }
                            if ($activador==true) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = $debe[$i];
                                $detalleDiario->detalle_haber =$haber[$i];
                                $detalleDiario->detalle_comentario = 'P/R CIERRE ANUAL CONTABLE DE RESULTADOS';
                                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE DIARIO DE CIERRE DE ESTADO DE RESULTADOS';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuenta[$i];
                            
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$cuenta[$i].' con el valor de: -> '.$haber[$i]);
                    
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe =  $debe[$i];
                                $detalleDiario->detalle_haber =$haber[$i];
                                $detalleDiario->detalle_comentario = 'P/R CIERRE ANUAL CONTABLE DE RESULTADOS';
                                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE DIARIO DE CIERRE DE ESTADO DE RESULTADOS';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuenta[$i];
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$cuenta[$i].' con el valor de: -> '.$debe[$i]);
                            }
                        }  
                    }
                } 
            } 
            
            $url = $general->pdfVariosDiario($diarios, $request->get('fecha_hasta'));
                        
            return redirect('cierreContable')->with('success','Pago realizado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            return redirect('cierreContable')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarfinanciero(Request $request){
        try{
            $general = new generalController();
            $year=DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
            $fecha='01-01-'.($year+1);
            if($request->get('sucursal_id') == 0){
                $sucursales=Sucursal::sucursales()->get();
                $cantSucursal = Sucursal::sucursales()->count('sucursal_id');
            }else{
                $sucursales=Sucursal::sucursal($request->get('sucursal_id'))->get();
                $cantSucursal = 1;
            }
            $contDiarios = 0;
            foreach($sucursales as $sucursal){
                
                $activador=false;
                $resultado[1]['haber'.$sucursal->sucursal_id]= abs(Detalle_Diario::SaldoActualByFecha('1',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo) 
                + abs(Detalle_Diario::SaldoActualByFecha('2',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo) 
                +  abs(Detalle_Diario::SaldoActualByFecha('3',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_debe) as saldo'))->first()->saldo);
                $resultado[1]['debe'.$sucursal->sucursal_id]= abs(Detalle_Diario::SaldoActualByFecha('1',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo) 
                + abs(Detalle_Diario::SaldoActualByFecha('2',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo) 
                +  abs(Detalle_Diario::SaldoActualByFecha('3',$request->get('fecha_desde'),$request->get('fecha_hasta'))->where('diario.sucursal_id','=',$sucursal->sucursal_id)->select(DB::raw('SUM(detalle_haber) as saldo'))->first()->saldo);
                if( round($resultado[1]['haber'.$sucursal->sucursal_id],2) <> 0){
                    $activador=true;
                }
                if( round($resultado[1]['debe'.$sucursal->sucursal_id],2) <> 0){
                    $activador=true;
                }
                if ($activador==true) {
                    $sucurs='s'.$sucursal->sucursal_id;
                    $sucurs=$request->get($sucurs);
                    $debe='debe'.$sucursal->sucursal_id;
                    $debe=$request->get($debe);
                    $haber='haber'.$sucursal->sucursal_id;
                    $haber=$request->get($haber);
                    $cuenta='Cuenta'.$sucursal->sucursal_id;
                    $cuenta=$request->get($cuenta);
                   
                    $diario = new Diario();
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('fecha_hasta'), 'CDCR');
                    $diario->diario_fecha = $request->get('fecha_hasta');
                    $diario->diario_referencia = 'COMPROBANTE DE DIARIO DE CIERRE DE ESTADO FINANCIERO';
                    $diario->diario_tipo_documento = 'COMPROBANTE DE DIARIO DE CIERRE';
                    $diario->diario_numero_documento = 0;
                    $diario->diario_beneficiario = '';
                    $diario->diario_tipo = 'CDCR';
                    $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                    $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('m');
                    $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
                    $diario->diario_comentario = 'COMPROBANTE DE DIARIO DE CIERRE DE RESULTADOS DEL AÑO '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
                    $diario->diario_cierre = '0';
                    $diario->diario_estado = '1';
                    $diario->empresa_id = Auth::user()->empresa_id;
                    $diario->sucursal_id = $sucursal->sucursal_id;
                    $diario->save();
                    $diarios[$contDiarios] = $diario;
                    $contDiarios ++;
                    $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, '0', 'Tipo de Diario -> '.$diario->diario_referencia.'');
                    $cierre=Cierre_Mes_Contable::CierreAnioSucursal(DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y'),$sucursal->sucursal_id)->first();
                    if($cierre){
                        $cierre=Cierre_Mes_Contable::findOrFail($cierre->cierre_id);
                    }else{
                        $cierre = new Cierre_Mes_Contable();
                    }

                    $cierre->cierre_ano = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
                    $cierre->cierre_01 = "1";
                    $cierre->cierre_02 = "1";
                    $cierre->cierre_03 = "1";
                    $cierre->cierre_04 = "1";
                    $cierre->cierre_05 = "1";
                    $cierre->cierre_06 = "1";
                    $cierre->cierre_07 = "1";
                    $cierre->cierre_08 = "1";
                    $cierre->cierre_09 = "1";
                    $cierre->cierre_10 = "1";
                    $cierre->cierre_11 = "1";
                    $cierre->cierre_12 = "1";
                    $cierre->cierre_estado = "1";
                    $cierre->sucursal_id = $sucursal->sucursal_id;
                    $cierre->save();
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Actualizacion de cierre de mes contable -> '.$cierre->cierre_ano.' Con Sucursal '.$sucursal->sucursal_nombre,'0','');
                    
                    foreach(Diario::DiariosCierre($request->get('fecha_desde'),$request->get('fecha_hasta'),$sucursal->sucursal_id)->get() as $detalle){
                        $diarioaux=Diario::findOrFail($detalle->diario_id);
                        $diarioaux->diario_cierre='1';
                        $diarioaux->diariodinanciero()->associate($diario);
                        $diarioaux->save();
                        
                    }
                    for ($i = 0; $i < count($sucurs); ++$i) {
                        if($cuenta[$i]!=0){
                            $activador=false;
                            if (floatval($debe[$i]) <> 0) {
                                $activador=true;
                            }
                            if (floatval($haber[$i]) <> 0) {
                                $activador=true;
                            }
                            if ($activador==true) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = $debe[$i];
                                $detalleDiario->detalle_haber =$haber[$i];
                                $detalleDiario->detalle_comentario = 'P/R CIERRE ANUAL CONTABLE DE RESULTADOS';
                                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE DIARIO DE CIERRE DE RESULTADOS';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuenta[$i];
                            
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$cuenta[$i].' con el valor de: -> '.$haber[$i]);
                    
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe =  $debe[$i];
                                $detalleDiario->detalle_haber =$haber[$i];
                                $detalleDiario->detalle_comentario = '';
                                $detalleDiario->detalle_tipo_documento = 'P/R CIERRE ANUAL CONTABLE DE RESULTADOS';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuenta[$i];
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$cuenta[$i].' con el valor de: -> '.$debe[$i]);
                            }
                        }  
                    }
                } 
                if ($activador==true) {
                    $sucurs='s'.$sucursal->sucursal_id;
                    $sucurs=$request->get($sucurs);
                    $debe='debe'.$sucursal->sucursal_id;
                    $debe=$request->get($debe);
                    $haber='haber'.$sucursal->sucursal_id;
                    $haber=$request->get($haber);
                    $cuenta='Cuenta'.$sucursal->sucursal_id;
                    $cuenta=$request->get($cuenta);

                    $diario = new Diario();
                    $diario->diario_codigo = $general->generarCodigoDiario($fecha, 'CDCO');
                    $diario->diario_fecha = $fecha;
                    $diario->diario_referencia = 'COMPROBANTE DIARIO CONTABLE';
                    $diario->diario_tipo_documento = 'COMPROBANTE DE DIARIO DE CIERRE';
                    $diario->diario_numero_documento = 0;
                    $diario->diario_beneficiario = '';
                    $diario->diario_tipo = 'CDCO';
                    $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                    $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $fecha)->format('m');
                    $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
                    $diario->diario_comentario = 'COMPROBANTE DE DIARIO CONTABLE DEL AÑO '.DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
                    $diario->diario_cierre = '0';
                    $diario->diario_estado = '1';
                    $diario->empresa_id = Auth::user()->empresa_id;
                    $diario->sucursal_id = $sucursal->sucursal_id;
                    $diario->save();
                    $diarios[$contDiarios] = $diario;
                    $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, '0', 'Tipo de Diario -> '.$diario->diario_referencia.'');
                    for ($i = 0; $i < count($sucurs); ++$i) {
                        if($cuenta[$i]!=0){
                            $activador=false;
                            if (floatval($debe[$i]) <> 0) {
                                $activador=true;
                            }
                            if (floatval($haber[$i]) <> 0) {
                                $activador=true;
                            }
                            if ($activador==true) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = $debe[$i];
                                $detalleDiario->detalle_haber =$haber[$i];
                                $detalleDiario->detalle_comentario = 'P/R CIERRE ANUAL CONTABLE DE RESULTADOS';
                                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE DIARIO DE CIERRE DE RESULTADOS';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuenta[$i];
                                
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$cuenta[$i].' con el valor de: -> '.$haber[$i]);
                    
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe =  $debe[$i];
                                $detalleDiario->detalle_haber =$haber[$i];
                                $detalleDiario->detalle_comentario = '';
                                $detalleDiario->detalle_tipo_documento = 'P/R CIERRE ANUAL CONTABLE DE RESULTADOS';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuenta[$i];
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$cuenta[$i].' con el valor de: -> '.$debe[$i]);
                            }
                        }  
                    }
                }
            } 
            
            foreach ($sucursales as $sucursal) {
               
            }

            $url = $general->pdfVariosDiario($diarios, $request->get('fecha_hasta'));
                        
            return redirect('cierreFinanciero')->with('success','Pago realizado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            return redirect('cierreFinanciero')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
