<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Punto_Emision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class auditoriaController extends Controller
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
            $usuarios=User::Usuarios()->get();
            return view('admin.seguridad.auditoria.index',['usuarios'=>$usuarios,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function verDocumento(Request $request, $id){
        $auditoria=Auditoria::findOrFail($id);
        $data = base64_decode($auditoria->auditoria_documento_respaldo);
        header('Content-Type: application/pdf');
        echo $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $valor=$request->get('idDescripcion');
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $usuarios=User::Usuarios()->get();
            if($request->get('usuario')=="--TODOS--"){
                $auditoria=DB::table('auditoria')->join('users', 'users.user_id', '=', 'auditoria.user_id')
                ->where('empresa_id', '=', Auth::user()->empresa_id)
                ->where('auditoria_fecha', '>=', $request->get('idDesde'))
                ->where('auditoria_fecha', '<=', $request->get('idHasta'))
                ->where(function ($query) use ($valor) {
                    $query->where(DB::raw('lower(auditoria_descripcion)'), 'like', '%'.strtolower($valor).'%')
                        ->orwhere(DB::raw('lower(auditoria_maquina)'), 'like', '%'.strtolower($valor).'%')
                        ->orwhere(DB::raw('lower(auditoria_numero_documento)'), 'like', '%'.strtolower($valor).'%')
                        ->orwhere(DB::raw('lower(auditoria_adicional)'), 'like', '%'.strtolower($valor).'%');
                })->get();  
            }
            else{
                $auditoria=DB::table('auditoria')->join('users', 'users.user_id', '=', 'auditoria.user_id')
            ->where('empresa_id', '=', Auth::user()->empresa_id)
            ->where('auditoria_fecha', '>=', $request->get('idDesde'))
            ->where('auditoria_fecha', '<=', $request->get('idHasta'))
            ->where('auditoria.user_id', '=', $request->get('usuario'))
            ->where(function ($query) use ($valor) {
                $query->where(DB::raw('lower(auditoria_descripcion)'), 'like', '%'.strtolower($valor).'%')
                    ->orwhere(DB::raw('lower(auditoria_maquina)'), 'like', '%'.strtolower($valor).'%')
                    ->orwhere(DB::raw('lower(auditoria_numero_documento)'), 'like', '%'.strtolower($valor).'%')
                    ->orwhere(DB::raw('lower(auditoria_adicional)'), 'like', '%'.strtolower($valor).'%');
            })->get();
            }

            return view('admin.seguridad.auditoria.index',['des'=>$request->get('idDescripcion'),'usuarioC'=>$request->get('usuario'),'fecI'=>$request->get('idDesde'),'fecF'=>$request->get('idHasta'),'usuarios'=>$usuarios,'auditoria'=>$auditoria, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('auditoria')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
