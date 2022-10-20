<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GrupoPer;
use App\Models\Parametrizacion_Permiso;
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
            DB::commit();

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
                    $permiso=Parametrizacion_Permiso::findOrFail($perm);

                    if($permiso){
                        $permiso->parametrizacionp_general=1;
                        $permiso->save();
                    }
                }
            }

            if(isset($request->permiso_2)){
                foreach($request->permiso_2 as $perm){
                    $permiso=Parametrizacion_Permiso::findOrFail($perm);

                    if($permiso){
                        $permiso->parametrizacionp_medico=1;
                        $permiso->save();
                    }
                }
            }

            if(isset($request->permiso_3)){
                foreach($request->permiso_3 as $perm){
                    $permiso=Parametrizacion_Permiso::findOrFail($perm);

                    if($permiso){
                        $permiso->parametrizacionp_camaronero=1;
                        $permiso->save();
                    }
                }
            }

            if(isset($request->permiso_4)){
                foreach($request->permiso_4 as $perm){
                    $permiso=Parametrizacion_Permiso::findOrFail($perm);

                    if($permiso){
                        $permiso->parametrizacionp_facturacion=1;
                        $permiso->save();
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
}
