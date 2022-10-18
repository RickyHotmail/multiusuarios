<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Expediente;
use App\Models\Producto;
use App\Models\Medico;
use App\Models\Medico_Especialidad;
use App\Models\Orden_Atencion;
use App\Models\Paciente;
use App\Models\Prescripcion;
use App\Models\Prescripcion_Medicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Punto_Emision;
use PDF;
use DateTime;

use Maatwebsite\Excel\Facades\Excel;

class atencionRecetasController extends Controller
{
    public function index(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }
            
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            $prescripciones = Prescripcion::prescripcionesPaciente()->get();
            $pacientes = Paciente::pacientes()->get();

            return view('admin.citasMedicas.farmacia.index',['medico'=>$medico, 'mespecialidadM'=>$mespecialidadM,'prescripciones'=>$prescripciones, 'pacientes'=>$pacientes, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function entregarPrescripcion($orden){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }
            
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();


            DB::beginTransaction();
            $prescripcionD = Prescripcion::prescripcionDetalle($orden)->get();
            $ordenAtencion = Orden_Atencion::findOrfail($orden);
            $expediente = Expediente::findByOrden($ordenAtencion->orden_id)->first();

            //return $prescripcionD;

            $pr = Prescripcion::findByExpediente($expediente->expediente_id)->first();

            $prescripcion = Prescripcion::findOrFail($pr->prescripcion_id);
            $prescripcion->prescripcion_estado=2;
            $prescripcion->save();
            

            $empresa = Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.ordenesAtenciones.farmacia.receta', ['ordenAtencion'=>$ordenAtencion, 'prescripcionD'=>$prescripcionD, 'empresa'=>$empresa]);
            $ruta = public_path().'/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$ordenAtencion->orden_fecha.'/'.$ordenAtencion->orden_numero.'/Documentos';
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'Prescripcion';
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            DB::commit();
            return redirect('receta/'.$ordenAtencion->orden_id)->with('success', 'Datos Actualizados exitosamente')->with('pdf2', 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$ordenAtencion->orden_fecha.'/'.$ordenAtencion->orden_numero.'/Documentos/'.$nombreArchivo.'.pdf');    
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function subirDocumentoEscaneado(Request $request){
        try{
            DB::beginTransaction();
            $prescripcion = Prescripcion::findOrFail($request->prescripcion_id);
            $expediente=$prescripcion->expediente;
            $orden = $expediente->ordenatencion;
            $prescripcion->prescripcion_documento= $this->crearDocumento($request->documento, $orden);
            $prescripcion->save();

            DB::commit();
            return json_encode(array("result"=>"OK", "prescripcion_documento"=>$prescripcion->prescripcion_documento));
        }
        catch(\Exception $e){
            DB::rollBack();
            return json_encode(array("result"=>"FAIL"));
        }
    }

    public function excelPrescripcion(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        
            return view('admin.inventario.producto.cargarExcelPrescripcion',['datos'=>null, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function cargarguardar(Request $request){
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();


        try{
            $mensaje='';            

            if($request->file('excelProd')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProd')->getClientOriginalExtension();
                $path = $request->file('excelProd')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Producto, $path);   
                
                
                for ($i=1; $i<count($array[0]); $i++){
                    //echo $array[0][$i][2].' - '.$array[0][$i][3];
                    $Excel_date = $array[0][$i][0]; 
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $Excel_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $fecha_despacho = gmdate("Y-m-d", $unix_date);


                    $productoOrden=Producto::productosByNombre($array[0][$i][3]
                    )->join("medicamento", "medicamento.producto_id", "producto.producto_id"
                    )->join("prescripcion_medicamento", "prescripcion_medicamento.medicamento_id", "medicamento.medicamento_id"
                    )->join("prescripcion", "prescripcion.prescripcion_id", "prescripcion_medicamento.prescripcion_id"
                    )->join("expediente","expediente.expediente_id","prescripcion.expediente_id"
                    )->join("orden_atencion", "orden_atencion.orden_id", "expediente.orden_id"
                    )->where(DB::raw("upper(concat(paciente.paciente_apellidos,' ', paciente.paciente_nombres))"),'like', "%".strtoupper($array[0][$i][2])."%"
                    )->where('orden_atencion.orden_fecha','=',$fecha_despacho
                    )->join("paciente", "paciente.paciente_id", "orden_atencion.paciente_id")->get();

                    //DB::enableQueryLog();
                    //dd(DB::getQueryLog());

                    //return $productoOrden;
                    
                    $det=null;

                    if($productoOrden){
                        foreach($productoOrden as $ordenP)
                            $det[]=array($ordenP->orden_id, $ordenP->orden_numero);
                    }
                    $array[0][$i][count($array[0][$i])]=$det;
                }

                //return $productoOrden;



                $data = [
                    "datos"=>$array,
                    'gruposPermiso'=>$gruposPermiso,
                    'permisosAdmin'=>$permisosAdmin
                ];
                
                return view('admin.inventario.producto.cargarExcelPrescripcion', $data);
            }
            DB::commit();
            
            return redirect('excelPrescripcion')->with('success','Datos guardados exitosamente')->with('error2','Algunos Datos no se registraron codigo repetido: '.' '.$mensaje);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('excelPrescripcion')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function exelPrescripcionGuardar(Request $request){
        //return $request;

        try{
            DB::beginTransaction();
            if(count($request->med)>0){
                $no_registrados="";
                for($i=0; $i<count($request->med); $i++){
                    $productoOrden=Producto::productosByNombre($request->med[$i]
                        )->join("medicamento", "medicamento.producto_id", "producto.producto_id"
                        )->join("prescripcion_medicamento", "prescripcion_medicamento.medicamento_id", "medicamento.medicamento_id"
                        )->join("prescripcion", "prescripcion.prescripcion_id", "prescripcion_medicamento.prescripcion_id"
                        )->join("expediente","expediente.expediente_id","prescripcion.expediente_id"
                        )->join("orden_atencion", "orden_atencion.orden_id", "expediente.orden_id"
                        )->where(DB::raw("upper(concat(paciente.paciente_apellidos,' ', paciente.paciente_nombres))"),'like', "%".strtoupper($request->cli[$i])."%"
                        )->where('orden_atencion.orden_fecha','=',$request->fec[$i]
                        )->join("paciente", "paciente.paciente_id", "orden_atencion.paciente_id")->first();

                    if($productoOrden){
                        $prescripcion=Prescripcion::findOrFail($productoOrden->prescripcion_id);
                        $detalles=$prescripcion->presMedicamento;

                        if($detalles){
                            foreach($detalles as $det){
                                $medicamento=$det->medicamento;

                                if($medicamento->producto->producto_nombre==$request->med[$i]){
                                    echo 'entre en el if';
                                    $det->porcentaje_utilidad=$request->p_u[$i];
                                    $det->save();

                                    break;
                                }
                            }
                        }
                        else
                            $no_registrados.=$request->med[$i].',';
                    }
                    else
                        $no_registrados.=$request->med[$i].',';
                }
            }
            else
                return redirect('excelPrescripcion')->with('success','No se realizó ninguna acción');

            if(strlen($no_registrados)>0){
                DB::rollBack();
                return redirect('excelPrescripcion')->with('success','Datos almacenados con éxito'.(strlen($no_registrados))>0 ? ', no se guardaron los siguientes registros '.$no_registrados:'');
            }
            else{
                DB::commit();
                return redirect('excelPrescripcion')->with('success','Datos almacenados con éxito');
            }
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('excelPrescripcion')->with("error2", "Ocurrió un problema al actualizar. Error: ".$ex->getMessage());
        }
    }

    private function crearDocumento($imagen, $atencion){
        $empresa = Empresa::empresa()->first();
        $fecha = (new DateTime("$atencion->orden_fecha"))->format('d-m-Y');

        
        $ruta = 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$fecha.'/'.$atencion->orden_numero.'/Documentos/Prescripcion';

        $extension = $imagen->extension();

        if ($imagen) {
            if (!is_dir(public_path().'/'.$ruta)) mkdir(public_path().'/'.$ruta, 0777, true);

            $name = 'documento.'.$extension;
            $path = $imagen->move(public_path().'/'.$ruta, $name);
        
            return $ruta.'/'.$name;
        }
        else
            return null;
    }

    public function imprimirPrescripcion($orden){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }
            
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            $prescripcionD = Prescripcion::prescripcionDetalle($orden)->get();
            $ordenAtencion = Orden_Atencion::findOrfail($orden);
 
            

            $empresa = Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.ordenesAtenciones.farmacia.receta', ['ordenAtencion'=>$ordenAtencion, 'prescripcionD'=>$prescripcionD, 'empresa'=>$empresa]);
            $ruta = public_path().'/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$ordenAtencion->orden_fecha.'/'.$ordenAtencion->orden_numero.'/Documentos';
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'Prescripcion';
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            DB::commit();
            return redirect('DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$ordenAtencion->orden_fecha.'/'.$ordenAtencion->orden_numero.'/Documentos/'.$nombreArchivo.'.pdf');    
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function buscarPrescripcion(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }
            
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();

            $prescripciones = Prescripcion::prescripcionesBusqueda($request)->get();
            $pacientes = Paciente::pacientes()->get();

            return view('admin.citasMedicas.farmacia.index',['medico'=>$medico, 'mespecialidadM'=>$mespecialidadM,'prescripciones'=>$prescripciones, 'pacienteID'=>$request->pacienteID, 'fDesde' => $request->fecha_desde, 'fHasta' => $request->fecha_hasta, 'fechasI'=>$request->incluirFechas, 'prescripcionE'=>$request->estado, 'pacientes'=>$pacientes, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function showPrescripcion($orden){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }
            
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            $prescripcionDet = Prescripcion::prescripcionDetalle($orden)->get();
            $ordenAtencion = Orden_Atencion::findOrfail($orden);

            $expediente = Expediente::findByOrden($ordenAtencion->orden_id)->first();
            $prescripcionCab = Prescripcion::findByExpediente($expediente->expediente_id)->first();
            //return $ordenAtencion."  <br><br><br>".$prescripcionD;

            return view('admin.citasMedicas.farmacia.receta',['medico'=>$medico, 'mespecialidadM'=>$mespecialidadM, 'ordenAtencion'=>$ordenAtencion, 'prescripcionCab'=>$prescripcionCab, 'prescripcionDet'=>$prescripcionDet, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
