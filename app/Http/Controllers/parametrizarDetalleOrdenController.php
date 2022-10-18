<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Parametrizar_Detalle_Orden;
use App\Models\Parametrizar_Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class parametrizarDetalleOrdenController extends Controller
{
    public function index($id){
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=', Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $parametro=Parametrizar_Orden::findOrFail($id);
            $valores=$parametro->valores;

            $data=[
                'parametrizar_id'=>$id,
                'valores'=>$valores,
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.parametrizacionOrden.indexDetalle',$data);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $parametrizacion=new Parametrizar_Detalle_Orden();
            $parametrizacion->parametrizard_descripcion=$request->descripcion;
            $parametrizacion->parametrizard_valor=$request->valor;
            $parametrizacion->parametrizar_id=$request->id;
            $parametrizacion->save();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de valores en parámetro de ordenes -> '.$request->get('descripcion'), $parametrizacion->parametrizard_id, '');
            DB::commit();

            return redirect('/parametrizacionDetalleOrden/'.$request->id)->with('success','El registro se guardó correctamente');
        }
        catch(\Exception $ex){
            return redirect('/parametrizacionDetalleOrden/'.$request->id)->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function edit($id)
    {
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $valor=Parametrizar_Detalle_Orden::findOrFail($id);

            $data=[
                'valor'=>$valor,
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.parametrizacionOrden.editarDetalle',$data);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $parametrizacion=Parametrizar_Detalle_Orden::findOrFail($id);
            $parametrizacion->parametrizard_descripcion=$request->descripcion;
            $parametrizacion->parametrizard_valor=$request->valor;
            $parametrizacion->save();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizando valor de parametro de ordenes -> '.$request->get('descripcion'), $id, '');
            DB::commit();

            return redirect("/parametrizacionDetalleOrden/".$parametrizacion->parametrizar_id)->with('success','El registro se actualizó correctamente');
        }
        catch(\Exception $ex){
            return redirect("/parametrizacionDetalleOrden/".$parametrizacion->parametrizar_id)->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
