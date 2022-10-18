<?php

namespace App\Http\Controllers;


use App\Models\Procedimiento_Especialidad;
use App\Models\Especialidad;
use App\Models\Cliente;
use App\Models\Entidad;
use App\Models\Punto_Emision;
use App\Models\Entidad_Procedimiento;
use App\Http\Controllers\Controller;
use App\Models\Entidad_Aseguradora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class entidadProcedimientoController extends Controller
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
            $clientesAseguradoras = Cliente::ClienteAseguradora()->get();
            return view('admin.agendamientoCitas.entidadProcedimiento.index',['clientesAseguradoras'=>$clientesAseguradoras,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function procedimientos($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cliente=Cliente::cliente($id)->first();
            $especialidades=Especialidad::especialidades()->get();
            $entidades=Entidad_Aseguradora::AseguradorasEntidades($cliente->cliente_id)->get();
            if ($cliente) {
                return view('admin.agendamientoCitas.entidadProcedimiento.procedimientos',['especialidades'=>$especialidades,'entidades'=>$entidades,'cliente'=>$cliente,'PE'=>Punto_Emision::puntos()->get(), 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            } else {
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }

    public function guardarProcedimientos(Request $request, $id){
        try {
            $check = $request->get('PcheckboxEstado');
            $procedimiento = $request->get('Pprocedimiento');
            $Pcodigo = $request->get('Pcodigo');
            $tipo = $request->get('Ptipo');
            $Pcobertura = $request->get('Pcobertura');
            $idEntidad = $request->get('entidad_id');

            
            /*
            $ProcedimientopeAsignados = '';
            for($i = 1; $i < count($check); ++$i) {
                if ($check[$i] == 1) {
                    $entidad_procedimiento = Entidad_Procedimiento::where('entidad_id', '=', $idEntidad)->where('procedimiento_id', '=', $procedimiento[$i])->delete();
                }
            }
            for ($i = 0; $i < count($check); ++$i){
                if ($check[$i] == 1) {
                    if ($Pcobertura[$i] == 0) {
                        $entidad_procedimiento = Entidad_Procedimiento::where('entidad_id', '=', $idEntidad)->where('procedimiento_id', '=', $procedimiento[$i])->delete();
                    }else{
                        $entidad_procedimiento = new Entidad_Procedimiento();
                        $entidad_procedimiento->ep_tipo = $tipo[$i];
                        $entidad_procedimiento->ep_valor = $Pcobertura[$i];
                        $entidad_procedimiento->ep_estado = 1;
                        $entidad_procedimiento->procedimiento_id = $procedimiento[$i];
                        $entidad_procedimiento->entidad_id =  $idEntidad;
                        $entidad_procedimiento->save();
                        $ProcedimientopeAsignados = $ProcedimientopeAsignados . ' - ' . $Pcodigo[$i];
                    }
                }         
            }  
            */

            DB::beginTransaction();
            $ProcedimientopeAsignados = '';
            for($i = 1; $i < count($check); $i++) {
                if ($check[$i] >= 0) {
                    $entidad_procedimiento = Entidad_Procedimiento::where('entidad_id', '=', $idEntidad)->where('procedimiento_id', '=', $procedimiento[$i])->delete();
                }
            }
            for ($i = 1; $i < count($check); $i++){
                if ($check[$i] > 0) {
                    $entidad_procedimiento = new Entidad_Procedimiento();
                    $entidad_procedimiento->ep_tipo = $tipo[$i];
                    $entidad_procedimiento->ep_valor = abs($Pcobertura[$i]);
                    $entidad_procedimiento->ep_estado = 1;
                    $entidad_procedimiento->procedimiento_id = $procedimiento[$i];
                    $entidad_procedimiento->entidad_id =  $idEntidad;
                    $entidad_procedimiento->save();
                    $ProcedimientopeAsignados = $ProcedimientopeAsignados . ' - ' . $Pcodigo[$i];
                }
            }

            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de entidad procedimientos con id -> ' . $idEntidad, '0', 'Los procedimientos asignadas fueron -> ' . $ProcedimientopeAsignados);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('entidadProcedimiento')->with('success', 'Datos guardados exitosamente');
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect('entidadProcedimiento')->with('error', 'Ocurrio un error en el procedimiento. Vuelva a intentar.'.$ex);
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

    public function buscarByEspecialidadId($buscar) {       
        return Procedimiento_Especialidad::procedimientoEspecialidadE($buscar)->get();        
    }

    public function buscarByEntidadId(Request $request){
        return Entidad_Procedimiento::ValorAsignado($request->get('procedimiento'),$request->get('entidad'))->get();
    }
}
