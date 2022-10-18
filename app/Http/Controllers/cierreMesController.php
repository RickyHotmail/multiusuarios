<?php

namespace App\Http\Controllers;

use App\Models\Cierre_Mes_Contable;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class cierreMesController extends Controller
{
    public function index()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.cierreMes.index',['sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
        
    }
    public function consultar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cierres = Cierre_Mes_Contable::CierreBySucursal($request->get('sucursal_id'))->get();
            return view('admin.contabilidad.cierreMes.index',['cierres'=>$cierres,'sucurslaC'=>$request->get('sucursal_id'),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('cierreMes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request){
        try{
            DB::beginTransaction();
            $cierre = new Cierre_Mes_Contable();
            $cierre->cierre_ano = $request->get('idAno');
            if($request->get('id_01') == "on"){
                $cierre->cierre_01 = "1";
            }else{
                $cierre->cierre_01 = "0";
            }
            if($request->get('id_02') == "on"){
                $cierre->cierre_02 = "1";
            }else{
                $cierre->cierre_02 = "0";
            }
            if($request->get('id_03') == "on"){
                $cierre->cierre_03 = "1";
            }else{
                $cierre->cierre_03 = "0";
            }
            if($request->get('id_04') == "on"){
                $cierre->cierre_04 = "1";
            }else{
                $cierre->cierre_04 = "0";
            }
            if($request->get('id_05') == "on"){
                $cierre->cierre_05 = "1";
            }else{
                $cierre->cierre_05 = "0";
            }
            if($request->get('id_06') == "on"){
                $cierre->cierre_06 = "1";
            }else{
                $cierre->cierre_06 = "0";
            }
            if($request->get('id_07') == "on"){
                $cierre->cierre_07 = "1";
            }else{
                $cierre->cierre_07 = "0";
            }
            if($request->get('id_08') == "on"){
                $cierre->cierre_08 = "1";
            }else{
                $cierre->cierre_08 = "0";
            }
            if($request->get('id_09') == "on"){
                $cierre->cierre_09 = "1";
            }else{
                $cierre->cierre_09 = "0";
            }
            if($request->get('id_10') == "on"){
                $cierre->cierre_10 = "1";
            }else{
                $cierre->cierre_10 = "0";
            }
            if($request->get('id_11') == "on"){
                $cierre->cierre_11 = "1";
            }else{
                $cierre->cierre_11 = "0";
            }
            if($request->get('id_12') == "on"){
                $cierre->cierre_12 = "1";
            }else{
                $cierre->cierre_12 = "0";
            }
            $cierre->cierre_estado = "1";
            $cierre->sucursal_id = $request->get('sucursal_id2');
            $cierre->save();
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Ingreso de cierre de mes contable -> '.$cierre->cierre_ano,'0','');
            DB::commit();
            return redirect('cierreMes')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cierreMes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function editar($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.cierreMes.editar',['cierre'=>Cierre_Mes_Contable::findOrFail($id),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('cierreMes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function edit(Request $request, $id){
        try{
            DB::beginTransaction();
            $cierre = Cierre_Mes_Contable::findOrFail($id);
            if($request->get('id_01') == "on"){
                $cierre->cierre_01 = "1";
            }else{
                $cierre->cierre_01 = "0";
            }
            if($request->get('id_02') == "on"){
                $cierre->cierre_02 = "1";
            }else{
                $cierre->cierre_02 = "0";
            }
            if($request->get('id_03') == "on"){
                $cierre->cierre_03 = "1";
            }else{
                $cierre->cierre_03 = "0";
            }
            if($request->get('id_04') == "on"){
                $cierre->cierre_04 = "1";
            }else{
                $cierre->cierre_04 = "0";
            }
            if($request->get('id_05') == "on"){
                $cierre->cierre_05 = "1";
            }else{
                $cierre->cierre_05 = "0";
            }
            if($request->get('id_06') == "on"){
                $cierre->cierre_06 = "1";
            }else{
                $cierre->cierre_06 = "0";
            }
            if($request->get('id_07') == "on"){
                $cierre->cierre_07 = "1";
            }else{
                $cierre->cierre_07 = "0";
            }
            if($request->get('id_08') == "on"){
                $cierre->cierre_08 = "1";
            }else{
                $cierre->cierre_08 = "0";
            }
            if($request->get('id_09') == "on"){
                $cierre->cierre_09 = "1";
            }else{
                $cierre->cierre_09 = "0";
            }
            if($request->get('id_10') == "on"){
                $cierre->cierre_10 = "1";
            }else{
                $cierre->cierre_10 = "0";
            }
            if($request->get('id_11') == "on"){
                $cierre->cierre_11 = "1";
            }else{
                $cierre->cierre_11 = "0";
            }
            if($request->get('id_12') == "on"){
                $cierre->cierre_12 = "1";
            }else{
                $cierre->cierre_12 = "0";
            }
            $cierre->cierre_estado = "1";
            $cierre->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de cierre de mes contable -> '.$cierre->cierre_ano,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cierreMes')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cierreMes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');

        }
    }
    public function eliminar($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.cierreMes.eliminar',['cierre'=>Cierre_Mes_Contable::findOrFail($id),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('cierreMes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function elim($id){
        try{
            DB::beginTransaction();
            $cierre = Cierre_Mes_Contable::findOrFail($id);
            $cierre->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de cierre de mes contable -> '.$cierre->cierre_ano,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cierreMes')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cierreMes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');

        }
    }
}
