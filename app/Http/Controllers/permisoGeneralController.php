<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GrupoPer;
use App\Models\Parametrizacion_Permiso;
use App\Models\Rol;
use App\Models\Rol_Permiso;
use App\Models\Tipo_Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class permisoGeneralController extends Controller{
    public function index(){
        ini_set('max_execution_time', 500);

        $permisos=Parametrizacion_Permiso::parametrizacionesPermiso()->get();
        $grupos=GrupoPer::grupos()->get();
        //$tipoGrupos=Tipo_Grupo::permisos()->get();

        
        return view('admin.seguridad.rol.permisosgenerales', [
            'grupos'=>$grupos,
            'permisos'=>$permisos
        ]);
    }

    public function store(Request $request){
        ini_set('max_execution_time', 500);
        //return $request;

        try{
            DB::beginTransaction();

            $permisos=Parametrizacion_Permiso::parametrizacionesPermiso()->get();

            foreach($permisos as $permiso){
                $permiso->parametrizacionp_general=0;
                $permiso->parametrizacionp_medico=0;
                $permiso->parametrizacionp_camaronero=0;
                $permiso->parametrizacionp_facturacion=0;
                $permiso->save();
            }

            

            if(isset($request->permiso_1)){
                foreach($request->permiso_1 as $perm){
                    $permiso1=Parametrizacion_Permiso::findOrFail($perm);

                    if($permiso1){
                        $permiso1->parametrizacionp_general=1;
                        $permiso1->save();
                    }
                }
            }

            if(isset($request->permiso_2)){
                foreach($request->permiso_2 as $perm2){
                    $permiso2=Parametrizacion_Permiso::findOrFail($perm2);

                    if($permiso2){
                        $permiso2->parametrizacionp_medico=1;
                        $permiso2->save();
                    }
                }
            }

            if(isset($request->permiso_3)){
                foreach($request->permiso_3 as $perm3){
                    $permiso3=Parametrizacion_Permiso::findOrFail($perm3);

                    if($permiso3){
                        $permiso3->parametrizacionp_camaronero=1;
                        $permiso3->save();
                    }
                }
            }

            if(isset($request->permiso_4)){
                foreach($request->permiso_4 as $perm4){
                    $permiso4=Parametrizacion_Permiso::findOrFail($perm4);

                    if($permiso4){
                        $permiso4->parametrizacionp_facturacion=1;
                        $permiso4->save();
                    }
                }
            }

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
    
    public function actualizarPermisosMedico($ruc){
        $rol=DB::select(DB::raw("
            select * 
            from 
                rol inner join empresa on empresa.empresa_id=rol.empresa_id
            where 
                empresa_ruc='$ruc' 
                and rol_nombre='Administrador'
        "));
        
        $result=DB::select(DB::raw("delete from rol_permiso where rol_id=".$rol[0]->rol_id));
        


        $parmetrizacionesP=Parametrizacion_Permiso::parametrizacionesPermiso()->get();
        foreach($parmetrizacionesP as $param){
            if($param->parametrizacionp_medico==1){
                $rolPermiso=new Rol_Permiso();
                $rolPermiso->permiso_id=$param->permiso_id;
                $rolPermiso->rol_id=$rol[0]->rol_id;
                $rolPermiso->save();
            }
        }

        return 'ok';
    }
}
