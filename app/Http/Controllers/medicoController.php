<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\Empleado;
use App\Models\Proveedor;
use App\Models\Medico_Especialidad;
use App\Models\Punto_Emision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\HorarioFijo;
use App\Models\Orden_Atencion;
use Illuminate\Http\Request;
use SebastianBergmann\Environment\Console;

class medicoController extends Controller
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
            $medicos = Medico::medicos()->get();
            $empleados = Empleado::empleados()->get();
            $empleadoME = Empleado::empleadoME()->get();
            $especialidades = Especialidad::especialidades()->get();
            $proveedores = Proveedor::proveedores()->get();
            $usuarios = User::Usuarios()->get();
            return view('admin.agendamientoCitas.medico.index',['usuarios'=>$usuarios,'medicos'=>$medicos, 'empleados'=>$empleados, 'empleadoME'=>$empleadoME, 'especialidades'=>$especialidades, 'proveedores'=>$proveedores, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
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
        return redirect('/denegado');
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
            DB::beginTransaction();
            $medico = new Medico();
            $medico->empresa_id = Auth::user()->empresa_id;
            $temp = '';
            if($request->get('proveedor_id') != ''){
                $medico->proveedor_id = $request->get('proveedor_id');
                $temp = Proveedor::findOrFail($request->get('proveedor_id'))->proveedor_nombre;
            }else{
                $medico->empleado_id = $request->get('empleado_id');
                $temp = Empleado::findOrFail($request->get('empleado_id'))->empleado_nombre;
            }            
            $medico->medico_estado = 1; 
            $medico->user_id = $request->get('usuario_id');           
            $medico->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Medico -> '.$temp,'0','');
            /*Fin de registro de auditoria */            
            DB::commit();
            return redirect('medico')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $medico = Medico::medico($id)->first();
            $usuarios = User::Usuarios()->get();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            if($medico){
                return view('admin.agendamientoCitas.medico.ver',['medico'=>$medico,'usuarios'=>$usuarios, 'mespecialidadM'=>$mespecialidadM,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
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
            $medico = Medico::medico($id)->first();
            $horariosFijo = HorarioFijo::horarios()->get();
            if($medico){
                return view('admin.agendamientoCitas.medico.editar', ['medico'=>$medico,'horariosFijo'=>$horariosFijo, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
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
            $medico = Medico::findOrFail($id);                  
            if ($request->get('medico_estado') == "on"){
                $medico->medico_estado = 1;
            }else{
                $medico->medico_estado = 0;
            }    
            if($medico->proveedor){
                $temp = $medico->proveedor->proveedor_nombre;
            }
            if($medico->empleado){
                $temp = $medico->empleado->empleado_nombre;
            }
            $medico->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de estado de medico -> '.$temp,'0','');
            /*Fin de registro de auditoria */   
            DB::commit();
            return redirect('medico')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('medico')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $medico=Medico::findOrFail($id);  
            $temp = '';
            if($medico->proveedor){
                $temp = $medico->proveedor->proveedor_nombre;
            }
            if($medico->empleado){
                $temp = $medico->empleado->empleado_nombre;
            }
            $medico->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de medico -> '.$temp,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('medico')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('medico')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function delete($id)
    {        
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medico = Medico::medico($id)->first();
            $horariosFijo = HorarioFijo::horarios()->get();
            if($medico){
                return view('admin.agendamientoCitas.medico.eliminar', ['medico'=>$medico,'horariosFijo'=>$horariosFijo, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('medico')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }    
    }
    public function medicoEspecialidad($id){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medico = Medico::medico($id)->first();
            if($medico){
                return view('admin.agendamientoCitas.medico.especialidades', ['medico'=>Medico::medico($id)->first(),'especialidades'=>Especialidad::Especialidades()->get(), 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('medico')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function medicoEspecialidadGuardar(Request $request){
        try {
            DB::beginTransaction();
            $especialidadesAsignados='';
            $medico=Medico::findOrFail($request->get('medico_id'));  
            $temp = '';
            if($medico->proveedor){
                $temp = $medico->proveedor->proveedor_nombre;
            }
            if($medico->empleado){
                $temp = $medico->empleado->empleado_nombre;
            }
            $medicoEspecialidad=Medico_Especialidad::where('medico_id','=',$request->get('medico_id'))->get();
            $tempEsp = '';
            foreach($medicoEspecialidad as $me){
                if(count($me->horarios) > 0){
                    $tempEsp = $tempEsp.' - '.$me->especialidad->especialidad_nombre;
                }else{
                    $me->delete();
                }
            }
            $especialidades=Especialidad::Especialidades()->get();
            foreach ($especialidades as $especialidad) {  
                if($request->get($especialidad->especialidad_id) == "on"){
                    $medEspe=Medico_Especialidad::where('medico_id','=',$medico->medico_id)->where('especialidad_id','=',$especialidad->especialidad_id)->first();
                    if(!$medEspe){
                        $medicoEspecialidad= new Medico_Especialidad();
                        $medicoEspecialidad->especialidad_id=$especialidad->especialidad_id;
                        $medicoEspecialidad->medico_id=$medico->medico_id;
                        $medicoEspecialidad->save();
                        $especialidadesAsignados=$especialidadesAsignados.'-'.$especialidad->especialidad_nombre;
                    }
                }
            }
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de especialidades de medico -> '.$temp,'0','Las especialidades asignadas fueron -> '.$especialidadesAsignados);
            /*Fin de registro de auditoria */
            DB::commit();
            if($tempEsp==''){
                return redirect('medico')->with('success','Datos guardados exitosamente');
            }else{
                return redirect('medico')->with('success','Datos guardados exitosamente')->with('error2','Las siguiente especialidades no fueron modificados porque tienen horario asignado. '.$tempEsp);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('medico')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarHorarioByEspecialidad($id){
        return HorarioFijo::HorarioByEspecialidad($id)->get();
    }
    public function horario($id){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medico = Medico::medico($id)->first();
            if($medico){
                return view('admin.agendamientoCitas.medico.horario', ['medico'=>Medico::medico($id)->first(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('medico')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    function horarioGuardar(Request $request){
        try {
            DB::beginTransaction();
            $mespecialidad=Medico_Especialidad::findOrFail($request->get('mespecialidad_id'));  
            $temp = '';
            if($mespecialidad->medico->proveedor){
                $temp = $mespecialidad->medico->proveedor->proveedor_nombre;
            }
            if($mespecialidad->medico->empleado){
                $temp = $mespecialidad->medico->empleado->empleado_nombre;
            }
            $horario=HorarioFijo::where('mespecialidad_id','=',$request->get('mespecialidad_id'))->delete();
            $dias = $request->get('timeDia');
            $inicio = $request->get('timeIni');
            $fin = $request->get('timeFin');
            for ($i = 0; $i < count($dias); ++$i){
                $horario = new HorarioFijo();
                $horario->horario_dia = $dias[$i];
                $horario->horario_hora_inicio = $inicio[$i];
                $horario->horario_hora_fin = $fin[$i];
                $horario->horario_estado = '1';
                $horario->mespecialidad_id = $mespecialidad->mespecialidad_id;
                $horario->save();
            }
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de horario de medico -> '.$temp,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('medico')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('medico')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByEspecialidad($buscar){
        try {
        return Medico_Especialidad::MedicosEspecialidad($buscar)
        ->select('medico_especialidad.mespecialidad_id',
        DB::raw('(SELECT empleado_nombre FROM empleado  WHERE medico.empleado_id =  empleado.empleado_id) as empleado_nombre'),
        DB::raw('(SELECT proveedor_nombre FROM proveedor WHERE medico.proveedor_id =  proveedor.proveedor_id) as proveedor_nombre')
        )->get();
    }catch(\Exception $ex){
        return $ex->getMessage();
    }
    }
}
