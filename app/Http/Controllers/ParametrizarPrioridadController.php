<?php

namespace App\Http\Controllers;

use App\Models\ParametrizarPrioridad;
use App\Http\Controllers\Controller;
use App\Models\Parametrizar_Prioridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParametrizarPrioridadController extends Controller
{
    public function index()
    {
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $prioridades=Parametrizar_Prioridad::prioridades()->get();

            $data=[
                'prioridades'=>$prioridades,
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.prioridadOrden.index', $data);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $prioridad=new Parametrizar_Prioridad();
            $prioridad->prioridad_descripcion=$request->descripcion;
            $prioridad->prioridad_desde=$request->desde;
            $prioridad->prioridad_hasta=$request->hasta;
            $prioridad->empresa_id=Auth::user()->empresa_id;
            $prioridad->save();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de prioridad de parametrizacaion de ordenes -> '.$request->get('descripcion'), $prioridad->prioridad_id, '');
            DB::commit();

            return  redirect('/prioridadOrden')->with('success','El registro se guardó correctamente');
        }
        catch(\Exception $ex){
            return redirect('/prioridadOrden')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function edit($id)
    {
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $prioridad=Parametrizar_Prioridad::findOrFail($id);

            $data=[
                'prioridad'=>$prioridad,
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.prioridadOrden.editar',$data);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $parametrizacion=Parametrizar_Prioridad::findOrFail($id);
            $parametrizacion->prioridad_descripcion=$request->descripcion;
            $parametrizacion->prioridad_desde=$request->desde;
            $parametrizacion->prioridad_hasta=$request->hasta;
            $parametrizacion->save();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualización de prioridad en parametrización de prioridades de ordenes -> '.$request->get('descripcion'), $id, '');
            DB::commit();

            return redirect('/prioridadOrden')->with('success','El registro se actualizó correctamente');
        }
        catch(\Exception $ex){
            return back()->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
