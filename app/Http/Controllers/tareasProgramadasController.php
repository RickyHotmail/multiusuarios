<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tareas_Programadas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class tareasProgramadasController extends Controller
{
    public function index(){
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $tareas = Tareas_Programadas::tareas()->get();


        return view("admin.tareasProgramadas.index", ["tareas"=>$tareas, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
    }

    public function edit($id){
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $tarea = Tareas_Programadas::findOrFail($id);

        return view("admin.tareasProgramadas.edit", ["tarea"=>$tarea, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
    }

    public function actualizar(Request $request){
        //return $request;

        try{
            DB::beginTransaction();

            $tarea = Tareas_Programadas::findOrFail($request->tarea_id);
            $tarea->tarea_nombre_proceso=$request->tarea_nombre_proceso;
            $tarea->tarea_tipo_tiempo=$request->tarea_tipo_tiempo;
            $tarea->tarea_hora_ejecucion=$request->tarea_hora_ejecucion;
            $tarea->tarea_procedimiento=$request->tarea_procedimiento;
            $tarea->empresa_id=Auth::user()->empresa_id;
            //$tarea->tarea_estado=0;

            
            if ($request->get('tarea_estado') == "on"){   
                $tarea->tarea_estado=1;
            }else{
                $tarea->tarea_estado=0;
            }
            


            $tarea->save();

            
            DB::commit();
            return redirect('tareasProgramadas')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function store(Request $request){
        $tareaNueva=new Tareas_Programadas();
            $tareaNueva->tarea_nombre_proceso=$request->tarea_nombre_proceso;
            $tareaNueva->tarea_tipo_tiempo=$request->tarea_tipo_tiempo;
            $tareaNueva->tarea_hora_ejecucion=$request->tarea_hora_ejecucion;
            $tareaNueva->tarea_procedimiento=$request->tarea_procedimiento;
            $tareaNueva->empresa_id=Auth::user()->empresa_id;
            $tareaNueva->tarea_estado=1;
        $tareaNueva->save();


        return redirect('tareasProgramadas');
    }

    public function create(Request $request){

        return "c".$request;
    }
}
