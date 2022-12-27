<?php

namespace App\Http\Controllers;

use App\Models\Centro_Consumo2;
use App\Http\Controllers\Controller;
use App\Models\Punto_Emision;
use App\Models\Sustento_Tributario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class centroConsumo2Controller extends Controller{
    public function index(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $centroCons = Centro_Consumo2::centroConsumos()->get();
            $sustentosTributario25 = Sustento_Tributario::Sustentos()->get();
            $secuencialMax=Centro_Consumo2::nivel(0)->max('centroc2_secuencial');
            //return $centroCons;

            
            $secuencial = 1;
            if($secuencialMax){$secuencial=$secuencialMax+1;}
            
            /* return "sec: ".$secuencial; */


            return view('admin.compras.centroConsumo2.index',[
                'sustentosTributario25'=>$sustentosTributario25,
                'centroCons'=>$centroCons,
                'secuencial'=>$secuencial,
                'PE'=>Punto_Emision::puntos()->get(),
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
                ]
            );
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

    public function agregarCentro($id){
        //try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $cuentaPadre=Centro_Consumo2::findOrFail($id);
            $secuencial = 1;
            $secuencialAux=Centro_Consumo2::nivel($id)->max('centroc2_secuencial');

            if($secuencialAux){
                $secuencial=$secuencialAux+1;
            }

            //return $cuentaPadre;
            if($cuentaPadre){
                return view('admin.compras.centroConsumo2.agregarCuentas',[
                    'cuentaPadre'=>$cuentaPadre,
                    'PE'=>Punto_Emision::puntos()->get(),
                    'secuencial'=>$secuencial,
                    'tipoPermiso'=>$tipoPermiso,
                    'gruposPermiso'=>$gruposPermiso,
                    'permisosAdmin'=>$permisosAdmin
                ]);
            }else{
                return redirect('/denegado');
            }
        /* }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        } */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //return $request;
        try{            
            DB::beginTransaction();
            $centroCon = new Centro_Consumo2();
            $centroCon->centroc2_nombre = $request->get('centro_consumo_nombre');
            $centroCon->centroc2_descripcion = $request->get('centro_consumo_descripcion');
            $centroCon->centroc2_fecha_ingreso = date('Y-m-d h:i:s');
            //$centroCon->sustento_id = null;

            $centroCon->centroc2_nivel = $request->cuenta_nivel;
            $centroCon->centroc2_secuencial = $request->cuenta_numero;
            $centroCon->empresa_id = Auth::user()->empresa_id;
            $centroCon->centroc2_estado = 1;
            $centroCon->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de centro consumo -> '.$request->get('centro_consumo_nombre').' de fecha -> '.$request->get('centro_consumo_fecha_ingreso'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('centroConsumo2')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('centroConsumo2')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function guardarCentroC2(Request $request, $id){
        //return $id;
        try{            
            DB::beginTransaction();
            $centroCon = new Centro_Consumo2();
            $centroCon->centroc2_nombre = $request->centro_consumo_nombre;
            $centroCon->centroc2_descripcion = "";
            $centroCon->centroc2_fecha_ingreso = date('Y-m-d h:i:s');
            //$centroCon->sustento_id = null;

            $centroCon->centroc2_nivel = $request->cuenta_nivel;
            $centroCon->centroc2_secuencial = $request->cuenta_padre.'.'.$request->cuenta_numero;
            $centroCon->centroc2_padre_id = $id;
            $centroCon->empresa_id = Auth::user()->empresa_id;
            $centroCon->centroc2_estado = 1;
            $centroCon->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de centro consumo -> '.$request->get('centro_consumo_nombre').' de fecha -> '.$request->get('centro_consumo_fecha_ingreso'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('centroConsumo2')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('centroConsumo2')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $centroCon = Centro_Consumo2::findOrFail($id);
            if($centroCon){
                return view('admin.compras.centroConsumo2.ver',['centroCon'=>$centroCon, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $centroCon = Centro_Consumo2::findOrFail($id);
            $sustentosTributario25 = Sustento_Tributario::Sustentos()->get();
            if($centroCon){
                return view('admin.compras.centroConsumo.editar', ['sustentosTributario25'=>$sustentosTributario25,'centroCon'=>$centroCon, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
        try{            
            DB::beginTransaction();
            $centroCon = Centro_Consumo::findOrFail($id);
            $centroCon->centro_consumo_nombre = $request->get('centro_consumo_nombre');
            $centroCon->centro_consumo_descripcion = $request->get('centro_consumo_descripcion');
            $centroCon->centro_consumo_fecha_ingreso = $request->get('centro_consumo_fecha_ingreso');  
            $centroCon->sustento_id = $request->get('idSustento');        
            if ($request->get('centro_consumo_estado') == "on"){
                $centroCon->centro_consumo_estado = 1;
            }else{
                $centroCon->centro_consumo_estado = 0;
            }
            $centroCon->save();       
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de centro consumo -> '.$request->get('centro_consumo_nombre').' de fecha -> '.$request->get('centro_consumo_fecha_ingreso'),'0','');
            /*Fin de registro de auditoria */   
            DB::commit();
            return redirect('centroConsumo')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('centroConsumo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $centroCon = Centro_Consumo::findOrFail($id);
            $centroCon->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de centro consumo -> '.$centroCon->centro_consumo_nombre.' con fecha -> '.$centroCon->centro_consumo_fecha_ingreso,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('centroConsumo')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('centroConsumo')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $centroCon = Centro_Consumo::centroConsumo($id)->first();
            if($centroCon){
                return view('admin.compras.centroConsumo.eliminar',['centroCon'=>$centroCon, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
