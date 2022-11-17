<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\GrupoPer;
use App\Models\Parametrizacion_Grupo_Permiso;
use App\Models\Parametrizacion_Permiso;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Rol_Permiso;
use App\Models\Tipo_Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class permisoGeneralController extends Controller{
    public function index(){
        $grupos=Parametrizacion_Grupo_Permiso::grupos()->get();
        //return $grupos;

        return view('admin.seguridad.rol.permisosgenerales.index', [
            'grupos'=>$grupos
        ]);
    }

    public function editarGrupo(Request $request, $id){
        $grupoGeneral=Parametrizacion_Grupo_Permiso::findOrFail($id);
        $permisos=Permiso::permisos()->orderBy('tipo_id')->get();
        $permisosParametrizacion=Parametrizacion_Permiso::parametrizacionesPermiso($id)->get();
        $grupos=GrupoPer::grupos()->get();

        return view('admin.seguridad.rol.permisosgenerales.editar', [
            'parmetrizacionGrupoId'=>$id,
            'grupos'=>$grupos,
            'permisos'=>$permisos,
            'grupoGeneral'=>$grupoGeneral,
            'permisosParam'=>$permisosParametrizacion
        ]);
    }

    public function guardarPermisosGrupo(Request $request){
        try{
            DB::beginTransaction();

            $permisos=Parametrizacion_Permiso::parametrizacionesPermiso($request->grupoId)->get();
            foreach($permisos as $permiso){
                $permiso->delete();
            }

            $c=0;
            if(isset($request->permiso)){
                foreach($request->permiso as $perm){
                    echo 'c: '.$c++.'<br>';
                    $parametrizacionPermiso=new Parametrizacion_Permiso();
                    $parametrizacionPermiso->permiso_id=$perm;
                    $parametrizacionPermiso->parametrizaciong_id=$request->grupoId;
                    $parametrizacionPermiso->save();
                }
            }

            /* foreach($permisos as $permiso){
                $empresas=Empresa::empresas()->get();
                foreach($empresas as $e){

                }
            } */

            

            DB::commit();
            return redirect('gestionPermisos')->with('success','Datos guardados exitosamente');
        }
        catch(\Exception $ex){
            DB::rollback();
            
            return $ex->getMessage();
        }
    }
    

    public function store(Request $request){
        ini_set('max_execution_time', 500);

        try{
            DB::beginTransaction();
            $grupoPermiso=new Parametrizacion_Grupo_Permiso();
            $grupoPermiso->parametrizaciong_nombre=$request->idNombre;
            $grupoPermiso->save();
            

            DB::commit();
            
            return redirect('gestionPermisos')->with('success','Datos guardados exitosamente');
        }
        catch(\Exception $ex){
            DB::rollback();
            return back()->with('error','Error: '.$ex->getMessage());
        }
    }

    public function actualizarPermisosAdministrador(){
        $parmetrizacionesP=Parametrizacion_Permiso::parametrizacionesPermiso()->get();

        $roles=DB::select(DB::raw("select * from rol where rol_nombre='Administrador' and empresa_id>1"));

        foreach($roles as $rol){
            $result=DB::select(DB::raw("delete from rol_permiso where rol_id=$rol->rol_id"));

            foreach($parmetrizacionesP as $param){
                if($param->parametrizacionp_facturacion==1){
                    $rolPermiso=new Rol_Permiso();
                    $rolPermiso->permiso_id=$param->permiso_id;
                    $rolPermiso->rol_id=$rol->rol_id;
                    $rolPermiso->save();
                }
            }
        }

        return 'ok';
    }
}
