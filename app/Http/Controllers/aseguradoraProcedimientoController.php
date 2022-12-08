<?php

namespace App\Http\Controllers;

use App\Models\Procedimiento_Especialidad;
use App\Models\Tipo_Cliente;
use App\Models\Cliente;
use App\Models\Especialidad;
use App\Models\Aseguradora_Procedimiento;
use App\Models\Punto_Emision;
use App\Http\Controllers\Controller;
use App\Models\Orden_Atencion;
use Dompdf\FrameDecorator\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\Cast\Object_;

class aseguradoraProcedimientoController extends Controller
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
            $aseguradoraProcedimientos=Aseguradora_Procedimiento::aseguradoraProcedimientos()->get();
            $clienteAseguradoras = Cliente::ClienteAseguradora()->get();
            $procedimientoEspecialidades=Procedimiento_Especialidad::procedimientoEspecialidades()->get();
            $especialidades=Especialidad::especialidades()->get();
            return view('admin.agendamientoCitas.aseguradoraProcedimiento.index',['especialidades'=>$especialidades, 'procedimientoEspecialidad'=>$procedimientoEspecialidades, 'clienteAseguradoras'=>$clienteAseguradoras, 'aseguradoraProcedimientos'=>$aseguradoraProcedimientos,'PE'=>Punto_Emision::puntos()->get(), 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }

    public function procedimiento($id)
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        
            $cliente=Cliente::cliente($id)->first();
            $especialidades=Especialidad::especialidades()->get();
            if ($cliente) {
                return view('admin.agendamientoCitas.aseguradoraProcedimiento.procedimiento',['especialidades'=>$especialidades, 'cliente'=>$cliente,'PE'=>Punto_Emision::puntos()->get(), 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            } else {
                return redirect('/denegado');
            }  
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }      
    }

    public function guardarProcedimiento(Request $request, $id){
        //return $request;
        try{
            DB::beginTransaction();
            $procedimiento = $request->get('Pprocedimiento');
            $check = $request->get('Pcheckbox');
            $Pcosto = $request->get('Pcosto');
            $PcodigoT = $request->get('PcodigoT');
            
            $idProducto = $request->get('ide');
            $auditoria = new generalController();


            $asegProcedEsp = Aseguradora_Procedimiento::aseguradoraProcedimientoEspecialidad($request->cliente_id, $request->especialidad_id)->get();

            
            foreach ($asegProcedEsp as $APE){
                $encontro=false;
                for ($i = 0; $i < count($check); ++$i) {
                    //echo $idProducto[$check[$i]].'   '.$APE->producto_id.'<br>';
                    if($idProducto[$check[$i]]==$APE->producto_id){
                        $encontro=true;
                        break;
                    }
                }

                if(!$encontro){
                    /* $aseguradoras = Procedimiento_Especialidad::ProcedimientoProductoEspecialidad($idProducto[$i],$request->get('especialidad_id'))->first();
                    $procedimientos=Aseguradora_Procedimiento::ProcedimientosAsignados($aseguradoras->procedimiento_id, $request->get('cliente_id'))->get();
                    */

                    echo 'borrado '.$APE->procedimientoA_id.', '.$APE->procedimientoA_codigo.', '.$APE->procedimientoA_valor.', '.$APE->procedimiento_id.', '.$APE->cliente_id.'<br>';
                    $procedimiento=$APE;

                    $APE->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Aseg. Procedimientos objeto '.$APE->procedimientoA_id.', '.$APE->procedimientoA_codigo.', '.$APE->procedimientoA_valor.', '.$APE->procedimiento_id.', '.$APE->cliente_id, $procedimiento->procedimientoA_id, '');
                }
            }

            //return $idProducto[$check[$i]];
            
            for($i=0; $i<count($check); ++$i){
                $aseguradora = Procedimiento_Especialidad::ProcedimientoProductoEspecialidad($idProducto[$check[$i]],$request->get('especialidad_id'))->first();
                $Aprocedimiento=Aseguradora_Procedimiento::ProcedimientosAsignados($aseguradora->procedimiento_id, $request->get('cliente_id'))->first();

                if(!$Aprocedimiento) $Aprocedimiento = new Aseguradora_Procedimiento();
                    $Aprocedimiento->procedimientoA_valor = $Pcosto[$check[$i]];
                    $Aprocedimiento->procedimientoA_codigo = $PcodigoT[$check[$i]];
                    $Aprocedimiento->procedimientoA_estado = 1;
                    $Aprocedimiento->procedimiento_id = $aseguradora->procedimiento_id;
                    $Aprocedimiento->cliente_id =  $request->get('cliente_id');
                    $Aprocedimiento->save();
                    $auditoria->registrarAuditoria('Registro de procedimientos con id -> ' .$aseguradora->procedimiento_id.' Con cliente id'.$request->get('cliente_id'), '0', 'Los procedimientos con Codigo-> ' . $PcodigoT[$check[$i]].' Con costo -> ' . $Pcosto[$check[$i]]);           

                    //echo 'agregado '.$aseguradora_procedimiento.'<br>';
                
                //else
                    //echo 'si existe '.$procedimiento.'<br>';


                /* foreach ($procedimientos as $procedimiento){
                    $encontro=false;

                    if(isset($request->Pcheckbox)){
                        
        
                        for ($i = 0; $i < count($check); ++$i) {

                        }
                    }

                    $procedimientoA_id=$procedimiento->procedimientoA_id;
                    $procedimientoA_codigo=$procedimiento->procedimientoA_codigo;

                    echo json_encode($procedimientos).'<br><br>';

                    if(!$encontro){
                        $procedimiento->delete();
                        $auditoria->registrarAuditoria('Eliminacion de procedimientos con id -> ' . $procedimientoA_id.' y codigo '.$procedimientoA_codigo, $aseguradoras->procedimiento_id, '');
                    }
                } */
            }

            //return 1000000;

            /*
            if(isset($request->Pcheckbox)){
                $check = $request->get('Pcheckbox');

                for ($i = 0; $i < count($check); ++$i) {
                    $aseguradoras = Procedimiento_Especialidad::ProcedimientoProductoEspecialidad($idProducto[$check[$i]],$request->get('especialidad_id'))->first();
                    $aseguradora_procedimiento = new Aseguradora_Procedimiento();
                    $aseguradora_procedimiento->procedimientoA_valor = $Pcosto[$check[$i]];
                    $aseguradora_procedimiento->procedimientoA_codigo = $PcodigoT[$check[$i]];
                    $aseguradora_procedimiento->procedimientoA_estado = 1;
                    $aseguradora_procedimiento->procedimiento_id = $aseguradoras->procedimiento_id;
                    $aseguradora_procedimiento->cliente_id =  $request->get('cliente_id');
                    $aseguradora_procedimiento->save();
                    $auditoria->registrarAuditoria('Registro de procedimientos con id -> ' .$aseguradoras->procedimiento_id.' Con cliente id'.$request->get('cliente_id'), '0', 'Los procedimientos con Codigo-> ' . $PcodigoT[$check[$i]].' Con costo -> ' . $Pcosto[$check[$i]]);           
                }
            } */

            DB::commit();
            return redirect('aseguradoraProcedimiento')->with('success', 'Datos guardados exitosamente');
        }
        catch(\Exception $ex){
            DB::rollback();
            return redirect('aseguradoraProcedimiento')->with('error', 'Ocurrió un error, vuelva a intentarlo '.$ex->getMessage());
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

    public function buscarByNombre($buscar) {       
        return Procedimiento_Especialidad::procedimientoEspecialidadE($buscar)->get();        
    }

    public function buscarByClienteId(Request $request){
        return Aseguradora_Procedimiento::ProcedimientosAsignados($request->get('procedimiento'),$request->get('aseguradora'))->get();
    }

    public function buscarByClienteEspecialidad(Request $request){
        return DB::select(DB::raw('select e.especialidad_nombre, pe.especialidad_id, p.producto_id, p.producto_codigo, p.producto_nombre, p.producto_precio_costo, '.
                                '(select "procedimientoA_id" from aseguradora_procedimiento where procedimiento_id=pe.procedimiento_id and cliente_id='.$request->cliente.') as procedimientoa_id,'.
                                '(select "procedimientoA_codigo" from aseguradora_procedimiento where procedimiento_id=pe.procedimiento_id and cliente_id='.$request->cliente.') as codigo,'.
                                '(select coalesce("procedimientoA_valor", 0) from aseguradora_procedimiento where procedimiento_id=pe.procedimiento_id and cliente_id='.$request->cliente.') as valor '.
   
                                'from especialidad as e, producto as p, procedimiento_especialidad as pe '.
                                
                                'where pe.especialidad_id=e.especialidad_id '.
                                'and e.especialidad_id='.$request->especialidad.' '.
                                'and p.producto_id=pe.producto_id'));
    }
}
