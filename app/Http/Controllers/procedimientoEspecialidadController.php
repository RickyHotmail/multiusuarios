<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Especialidad;
use App\Models\Procedimiento_Especialidad;
use App\Models\Punto_Emision;
use App\Http\Controllers\Controller;
use App\Models\Aseguradora_Procedimiento;
use App\Models\Detalle_Examen;
use App\Models\Entidad_Procedimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class procedimientoEspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $especialidades=Especialidad::especialidades()->get();

            if($request->get('especialidad_id')!=null)
                $procedimientoEspecialidades=Procedimiento_Especialidad::procedimientoEspecialidadbyEspecialidad($request->especialidad_id)->get();
            else
                $procedimientoEspecialidades=Procedimiento_Especialidad::procedimientoEspecialidadbyEspecialidad($especialidades[0]->especialidad_id)->get();

            $productos=Producto::productosG()->get();

            $data=[
                'procedimientoEspecialidades'=>$procedimientoEspecialidades,
                'productos'=>$productos, 'especialidades'=>$especialidades,
                'PE'=>Punto_Emision::puntos()->get(),
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            if($request->get('especialidad_id')!=null)$data["seleccionada"]=$request->especialidad_id;

            return view('admin.agendamientoCitas.procedimientoEspecialidad.index', $data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function especialidad($id)
    {
        try{
            $gruposPermiso = DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
            $permisosAdmin = DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
            $especialidades=Especialidad::especialidades()->get();
            $producto=Producto::producto($id)->first();
            $aseguradoras = Aseguradora_Procedimiento::AseguradoraProcedimientos()->get();
            $procedimientoEspecialidad=Procedimiento_Especialidad::procedimientoEspecialidades()->get();
            if($producto) {
                return view('admin.agendamientoCitas.procedimientoEspecialidad.especialidad', ['aseguradoras' => $aseguradoras, 'producto' => $producto, 'procedimientoEspecialidad' => $procedimientoEspecialidad, 'especialidades' => $especialidades, 'PE' => Punto_Emision::puntos()->get(), 'gruposPermiso' => $gruposPermiso, 'permisosAdmin' => $permisosAdmin]);
            } else {
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function actualizarPorEspecialidad(Request $request){
        try{
            DB::beginTransaction();

            $procedimientos=Procedimiento_Especialidad::procedimientoEspecialidadE($request->especialidad_id)->get();
            $borrado=array();
            $agregado=array();
            $saltado=array();

            $auditoria = new generalController();
            
            foreach($procedimientos as $procedimiento){
                $encontro=false;
                
                if(isset($request->producto)){
                    foreach($request->producto as $prod=>$valor){
                        if($prod==$procedimiento->producto_id){
                            $encontro=true;
                            break;
                        }
                    }
                }

                if(!$encontro){
                    $aseguradora_procedimiento=$procedimiento->aseguradoraprocedimientos;
                    $entidad_procedimiento=$procedimiento->entidadprocedimientos;

                    $borrado[]=$aseguradora_procedimiento;
                    $borrado[]=$entidad_procedimiento;
                    foreach($aseguradora_procedimiento as $ap){
                        echo 'prod: '.$ap->procedimiento->producto_id.'  esp: '.$ap->procedimiento->especialidad_id.'   cli '.$ap->cliente->cliente_nombre.'<br>';
                        $auditoria->registrarAuditoria('Se borró Registro Aseg Proced de table procedimiento_especialidad ',$request->especialidad_id,'El registro fué -> ' . $ap->procedimientoA_id);
                        $ap->delete();
                    }

                    foreach($entidad_procedimiento as $ep){
                        echo 'prod: '.$ep->procedimiento->producto_id.'  esp: '.$ep->procedimiento->especialidad_id.'   cli '.$ep->entidad->entidad_nombre.'<br>';
                        $auditoria->registrarAuditoria('Se borró Registro Ent Proced de table procedimiento_especialidad ',$request->especialidad_id,'El registro fué -> ' . $ap->procedimientoA_id);
                        $ep->delete();
                    }

                    $procedimiento->delete();
                }
            }

            if(isset($request->producto)){
                foreach($request->producto as $prod=>$valor){
                    $encontro=false;

                    foreach($procedimientos as $procedimiento){
                        if($procedimiento->producto_id==$prod){
                            $encontro=true;
                            break;
                        }
                    }
                    
                    if(!$encontro){
                        $nuevo_procedimiento=new Procedimiento_Especialidad();
                        $nuevo_procedimiento->producto_id=$prod;
                        $nuevo_procedimiento->especialidad_id=$request->especialidad_id;
                        $nuevo_procedimiento->procedimiento_estado=1;
                        $nuevo_procedimiento->save();

                        echo json_encode($nuevo_procedimiento);

                        $auditoria->registrarAuditoria('Se guardó Registro procedimiento_especialidad ',$request->especialidad_id,'El registro fué -> ' . $nuevo_procedimiento->procedimiento_id);
                    }
                }
            }

        
            //$auditoria = new generalController();
            //$auditoria->registrarAuditoria('Actualizacion de procedimientos especialidad en grupo ', $request->especialidad_id, 'Las especialidades borradas  fueron -> ' . json_encode($borrado).'\n Los procedimientos añadidos fueron '.json_encode($agregado));
            DB::commit();
            return redirect('procedimientoEspecialidad')->with('success', 'Datos guardados exitosamente');
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect('procedimientoEspecialidad')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function guardarEspecialidades(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $EspeAsignadas = '';      
            $especialidades = Especialidad::especialidades()->get();
            $procedimientos = Procedimiento_Especialidad::procedimientoProducto($id)->get();
            $aseguradoras = Aseguradora_Procedimiento::AseguradoraProcedimientos()->get();

            foreach ($procedimientos as $procedimientoE) {
                $existe = false;
                foreach ($aseguradoras as $aseguradora) {
                    if($aseguradora->procedimiento_id == $procedimientoE->procedimiento_id){
                        $existe = true;
                    }
                }
                if(!$existe) {
                    $procedimiento = Procedimiento_Especialidad::where('procedimiento_id', '=',  $procedimientoE->procedimiento_id)->delete();
                }
            }

            foreach ($especialidades as $especialidad) {
                if ($request->get($especialidad->especialidad_id) == "on") {
                    $procedimiento = new Procedimiento_Especialidad;
                    $procedimiento->procedimiento_estado = 1;
                    $procedimiento->especialidad_id = $especialidad->especialidad_id;
                    $procedimiento->producto_id = $id;
                    $procedimiento->save();
                    $EspeAsignadas = $EspeAsignadas . ' - ' . $especialidad->especialidad_nombre;
                }
            }
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de productos con id -> ' . $id, '0', 'Las especialidades asignadas  fueron -> ' . $EspeAsignadas);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('procedimientoEspecialidad')->with('success', 'Datos guardados exitosamente');
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect('procedimientoEspecialidad')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('/denegado');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect('/denegado');
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
        return redirect('/denegado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect('/denegado');
    }
    public function buscarBy(Request $request){
        $datos=null;
        $proce=Procedimiento_Especialidad::ProcedimientoProductoEspecialidad($request->get('buscar'),$request->get('especialidad'))->first();
        $ase=Aseguradora_Procedimiento::ProcedimientosAsignados($proce->procedimiento_id,$request->get('Aseguradora'))->first();
        $entid=Entidad_Procedimiento::ValorAsignadoproducto($proce->procedimiento_id,$request->get('entidad'),$request->get('buscar'))->first();
        $datos[0]=$ase;
        $datos[1]=$entid;
        return $datos;
    }

    public function buscarEspecialidadProducto(Request $request){
        $datos=null;
        $proce=Procedimiento_Especialidad::ProcedimientoProductoEspecialidad($request->get('buscar'),$request->get('especialidad'))->first();
        //$ase=Aseguradora_Procedimiento::ProcedimientosAsignados($proce->procedimiento_id,$request->get('Aseguradora'))->first();
        //$entid=Entidad_Procedimiento::ValorAsignadoproducto($proce->procedimiento_id,$request->get('entidad'),$request->get('buscar'))->first();
        //$datos[0]=$ase;
        //$datos[1]=$entid;
        return $datos;
    }

    public function buscarByClienteEntidadEspecialidad(Request $request){
        return DB::select(DB::raw('select e.especialidad_nombre, pe.especialidad_id, p.producto_id, p.producto_codigo, p.producto_nombre, p.producto_precio_costo, pe.procedimiento_id,'.
                                '(select coalesce("procedimientoA_valor", 0) from aseguradora_procedimiento where procedimiento_id=pe.procedimiento_id and cliente_id='.$request->cliente.' limit 1) as valor,'.
                                '(select ep_tipo from entidad_procedimiento where procedimiento_id=pe.procedimiento_id and entidad_id='.$request->entidad.' limit 1) as ep_tipo,'.
                                '(select ep_valor from entidad_procedimiento where procedimiento_id=pe.procedimiento_id and entidad_id='.$request->entidad.' limit 1) as ep_valor '.
   
                                'from especialidad as e, producto as p, procedimiento_especialidad as pe '.
                                
                                'where pe.especialidad_id=e.especialidad_id '.
                                'and e.especialidad_id='.$request->especialidad.' '.
                                'and p.producto_id=pe.producto_id'));
    }
}
