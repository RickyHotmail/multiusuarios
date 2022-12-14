<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proveedor;
use App\Models\Empleado;
use App\Models\Medico_Especialidad;
use App\Models\Paciente;
use App\Models\Sucursal;
use App\Models\Punto_Emision;
use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Aseguradora_Procedimiento;
use App\Models\Analisis_Laboratorio;
use App\Models\Documento_Cita_Medica;
use App\Models\Documento_Orden_Atencion;
use App\Models\Empresa;
use App\Models\Entidad_Procedimiento;
use App\Models\Procedimiento_Especialidad;
use App\Models\Orden_Atencion;
use App\Models\Medico;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

use function PHPSTORM_META\type;

class ordenAtencionConsolidadoController extends Controller{
    public function index(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $empleados = Empleado::Empleados()->get();
            $proveedores = Proveedor::Proveedores()->get();
            $pacientes = Paciente::Pacientes()->get();
            $medicos = Medico::medicos()->get();

            $rol=User::findOrFail(Auth::user()->user_id)->roles->first();

            $data = [
                "seleccionado"=>0,
                "medicos"=>$medicos,
                "rol"=>$rol,
                'sucursales'=>Sucursal::Sucursales()->get(),
                'empleados'=>$empleados,
                'proveedores'=>$proveedores,
                'pacientes'=>$pacientes,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.agendamientoCitas.ordenAtencion.indexConsolidado', $data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    public function buscar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ordenesAtencion = Orden_Atencion::ordenesByFechaSuc($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal_id'))->get();
            $empleados = Empleado::Empleados()->get();
            $proveedores = Proveedor::Proveedores()->get();        
            $pacientes = Paciente::Pacientes()->get();

            $medicos = Medico::medicos()->get();
            $rol=User::findOrFail(Auth::user()->user_id)->roles->first();

            //return Auth::user()->user_id;

            foreach($ordenesAtencion as $orden){
                $expediente = $orden->expediente;

                if($expediente){
                    $signosVitales=$expediente->signosVitales;
                }
            }

            $data = [
                "seleccionado"=>$request->medico_id,
                "medicos"=>$medicos,
                "rol"=>$rol,
                'fecI'=>$request->get('fecha_desde'),
                'fecF'=>$request->get('fecha_hasta'),
                'sucurslaC'=>$request->get('sucursal_id'),
                'sucursales'=>Sucursal::Sucursales()->get(),
                'ordenesAtencion'=>$ordenesAtencion,
                'empleados'=>$empleados,
                'proveedores'=>$proveedores,
                'pacientes'=>$pacientes,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.agendamientoCitas.ordenAtencion.indexConsolidado',$data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    public function crearArchivoConsolidado(Request $request, $id){
        //return $id;
        $empresa = Empresa::empresa()->first();
        $ordenAtencion=Orden_Atencion::findOrFail($id);

        $documentos=$ordenAtencion->documentos;
        $expediente=$ordenAtencion->expediente;
        $paciente=$ordenAtencion->paciente;

        if($expediente){
            $prescripcion=$expediente->prescripcion;
            $ordenExamen=$expediente->ordenExamen;
            $ordenImagen=$expediente->ordenImagen;
        }

        $listaDocumentos=null;

        if($paciente->documento_paciente!=null)  $listaDocumentos[]=$paciente->documento_paciente;
        if($paciente->paciente_dependiente==1 && $paciente->documento_afiliado!=null)  $listaDocumentos[]=$paciente->documento_afiliado;
        $listaDocumentos[]="DocumentosOrdenAtencion/".$empresa->empresa_ruc.'/'.(new DateTime($ordenAtencion->orden_fecha))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos/Orden de atencion.pdf';

        if(count($documentos)>0){
            foreach($documentos as $doc){
                if($doc->doccita_url!=""){
                    $ext=explode($doc->doccita_url, ".");

                    if($ext[count($ext)-1]=="jpg"  ||  $ext[count($ext)-1]=="png"  ||  $ext[count($ext)-1]=="jpeg")
                        $listaDocumentos[]=$this->crearPdfDocumentoPaciente($ordenAtencion, [$doc->doccita_url], "", 1);
                    else
                        $listaDocumentos[]=$doc->doccita_url;
                }
            }
        }

        //return $listaDocumentos;
        //return $ordenExamen;
        //return $ordenImagen;
        
        if(isset($ordenExamen)){
            $analisisLaboratorio=Analisis_Laboratorio::analisisByOrden($ordenExamen->orden_id)->first();
            if($analisisLaboratorio){
                if($analisisLaboratorio->analisis_estado == 3){
                    $ref = $analisisLaboratorio->orden->orden_id_referencia;
                    $ruta = 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime($ordenAtencion->orden_fecha))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos/Laboratorio/';

                    
                    if (!is_dir(public_path().'/'.$ruta)) {
                        mkdir(public_path().'/'.$ruta, 0777, true);
                    }

                    $ALC=new analisis_LaboratorioController();
                    $result=$ALC->showExamenResults($ref);
                    file_put_contents(public_path().'/'.$ruta.'/resultados_examen.pdf', $result);
                    $listaDocumentos[]=public_path().'/'.$ruta.'/resultados_examen.pdf';
                    //readfile($path);
                    
                }
            }
        }

        if(isset($ordenImagen)){
            if($ordenImagen->detalleImagen){
                foreach($ordenImagen->detalleImagen as $det){
                    if($det->detalle_estado==2){
                        $path = public_path().'/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime($ordenAtencion->orden_fecha))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos/Imagenes/imagen_resultado'.$det->detalle_id.'_1.pdf';
                        $listaDocumentos[]=$path;
                    }
                }
            }
        }
    
        

        if(isset($prescripcion)){
            if($prescripcion->prescripcion_documento!="")
                $listaDocumentos[]=$this->crearPdfDocumentoPaciente($ordenAtencion, [$prescripcion->prescripcion_documento], "", 1);
        }
        
        $listaDocumentos[]=$this->crearInformeIndividualPdf($ordenAtencion);
        $pdf = new \Clegginabox\PDFMerger\PDFMerger;
        $cantidad=0;

        foreach($listaDocumentos as $ld){
            if(file_exists($ld)){
                $file_parts = pathinfo($ld);
                if($file_parts['extension']=="pdf" || $file_parts['extension']=="PDF"){
                    $cantidad++;
                    $pdf->addPDF($ld, 'all');
                }
                //echo $ld.'<br>';
            }
            //else
                //echo 'no existe:   '.$ld.'<br>';
        }

        if($cantidad>0){
            //echo 'creado con '.$cantidad.' archivo/s';
            $pdf->merge('download', 'informe_consolidado_'.$ordenAtencion->orden_numero.'.pdf'); // REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
        }else
            return "no hay archivos en la lista";
    }

    private function crearPdfDocumentoPaciente($atencion, $imagenes, $titulo, $tamanio){
        $empresa = Empresa::empresa()->first();
        $fecha = (new DateTime("$atencion->orden_fecha"))->format('d-m-Y');

        
        $view =  \View::make('admin.formatosPDF.ordenesAtenciones.paciente.ordenAtencionDocumentoPacientePdf', ['titulo'=>strtoupper($titulo), 'imagenes'=>$imagenes, 'tamanio'=> $tamanio]);
        $ruta = 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$fecha.'/'.$atencion->orden_numero.'/Documentos';
        if (!is_dir(public_path().'/'.$ruta)) {
            mkdir(public_path().'/'.$ruta, 0777, true);
        }
        $nombreArchivo = 'ANEXOS_DOCUMENTO_'.strtoupper($titulo);
        PDF::loadHTML($view)->save(public_path().'/'.$ruta.'/'.$nombreArchivo.'.pdf');

        //borrar las imagenes creadas arriba
        //foreach($imagenes as $foto){
        //    $result = Storage::delete(public_path().'/'.$foto['ruta'].'/'.$foto['nombre']);
        //}

        return $ruta.'/'.$nombreArchivo.'.pdf';
    }

    public function crearInformeIndividualPdf($ordenAtencion){
        $empresa = Empresa::empresa()->first();
        $fecha = (new DateTime("$ordenAtencion->orden_fecha"))->format('d-m-Y');

        //return $request;
        //try{
            //$sucursal=Sucursal::findOrFail($request->sucursal_id);
            $orden=Orden_Atencion::findOrFail($ordenAtencion->orden_id);

            $expediente=$orden->expediente;
            $producto=$orden->producto;
            $paciente=$orden->paciente;
            $especialidad=$orden->especialidad;
            $tipoSeguro=$orden->tipoSeguro;

            $procedimientoEspecialidad=Procedimiento_Especialidad::procedimientoProductoEspecialidad($producto->producto_id, $especialidad->especialidad_id)->first();
            $procedimientoAseguradora=Aseguradora_Procedimiento::procedimientosAsignados($procedimientoEspecialidad->procedimiento_id, $orden->paciente->cliente_id)->first();
            $datos[$orden->orden_id][$producto->producto_id]=$procedimientoAseguradora;

            if($paciente){
                $dependencia=$paciente->tipoDependencia;
            }

            ///////////////diagn??stico///////////////////////////////
            if($expediente){
                $diagnostico=$expediente->diagnostico;

                if($diagnostico){
                    $diagDetalle=$diagnostico->detallediagnostico;

                    foreach($diagDetalle as $detalle){
                        $detalle->enfermedad;
                    }
                }

                $ordenExamen=$expediente->ordenExamen;
                $ordenImagen=$expediente->ordenImagen;
                $prescripcion=$expediente->prescripcion;

                if($ordenExamen){
                    $detalleExamen=$ordenExamen->detalle;

                    foreach($detalleExamen as $detalle){
                        $examen=$detalle->examen;
                        
                            
                        if($examen){
                            $productoExamen=$examen->producto;
                            $examen->tipoExamen;
                            $procEspe=Procedimiento_Especialidad::procedimientoProductoEspecialidad($productoExamen->producto_id, $especialidad->especialidad_id)->first();

                            if($procEspe){
                                $procAseg=Aseguradora_Procedimiento::procedimientosAsignados($procEspe->procedimiento_id, $orden->paciente->cliente_id)->first();
                                $datos['detalle_examen'][$detalle->detalle_id] = $procAseg;

                                
                            }
                        }
                    }
                }
                
                
                if($ordenImagen){
                    $detalleImagen=$ordenImagen->detalleImagen;
                    
                    foreach($detalleImagen as $detalle){
                        $imagen=$detalle->imagen;
                        
                        if($imagen){
                            $productoImagen=$imagen->producto;
                            $imagen->tipoImagen;

                            $procEspe=Procedimiento_Especialidad::procedimientoProductoEspecialidad($productoImagen->producto_id, $especialidad->especialidad_id)->first();

                            if($procEspe){
                                $procAseg=Aseguradora_Procedimiento::procedimientosAsignados($procEspe->procedimiento_id, $orden->paciente->cliente_id)->first();
                                $datos['detalle_imagen'][$detalle->detalle_id] = $procAseg;
                            }
                        }
                    }
                }

                if($prescripcion){
                    //return 'si presc';
                    $detalleprescipcion=$prescripcion->presMedicamento;

                    foreach($detalleprescipcion as $detalle){
                        $medicamento=$detalle->medicamento;
                        
                            
                        if($medicamento){
                            $productoMedic=$medicamento->producto;
                            $medicamento->tipoMedicamento;

                            $procEspe=Procedimiento_Especialidad::procedimientoProductoEspecialidad($productoMedic->producto_id, $especialidad->especialidad_id)->first();

                            if($procEspe){
                                $procAseg=Aseguradora_Procedimiento::procedimientosAsignados($procEspe->procedimiento_id, $orden->paciente->cliente_id)->first();
                                $datos['detalle_medicamento'][$detalle->prescripcionM_id] = $procAseg;
                            }
                        }
                    }
                }
                //else
                    //return 'no presc';

                //if($orden->orden_secuencial==78)
                //    return $expediente;
            }

            
            $datos['orden']= $orden;

            //return $datos;
            //return  $datos['ordenes'];

            return $orden;

            $view =  \View::make('admin.formatosPDF.ordenesAtenciones.ordenIndividualPlano', $datos);

            $ruta = 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$fecha.'/'.$orden->orden_numero.'/Documentos';
            PDF::loadHTML($view)->setPaper('a4', 'landscape')->save(public_path().'/'.$ruta.'/informeOrdenIndividual.pdf');

            return $ruta.'/informeOrdenIndividual.pdf';
            //return Excel::download(new ViewExcel('admin.formatosExcel.individualPlano', $datos), 'NEOPAGUPA  Sistema Contable.xls');
        //}catch(\Exception $ex){
            return redirect('informehistoricoplano')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        //}
    }

    public function verificarDocumentosOrden(Request $request){
        $fecha_desde=date('Y-m-01');
        $fecha_actual=date('Y-m-d');

        if($request->get('fecha_desde')!=null && $request->get('fecha_hasta')!=null){
            $fecha_desde=$request->get('fecha_desde');
            $fecha_actual=$request->get('fecha_hasta');
        }

        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ordenesAtencion = Orden_Atencion::OrdenesByFechaSuc($fecha_desde, $fecha_actual, $request->get('sucursal_id'))->get();
            $documentos=Documento_Orden_Atencion::documentosOrdenesAtencion()->get();
            $medicos = Medico::medicos()->get();

            foreach($ordenesAtencion as $orden){
                $orden->documentos;
                $expediente = $orden->expediente;

                if($expediente){
                    $signosVitales=$expediente->signosVitales;
                }
            }

            $data = [
                'fecI'=>$fecha_desde,
                'fecF'=>$fecha_actual,
                'medicos'=>$medicos,
                'documentos'=>$documentos,
                "seleccionado"=>$request->medico_id,
                'sucurslaC'=>$request->get('sucursal_id'),
                'sucursales'=>Sucursal::Sucursales()->get(),
                'ordenesAtencion'=>$ordenesAtencion,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.agendamientoCitas.ordenAtencion.verificarOrdenDocumentos',$data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }


    public function subirDocumentoOrden(Request $request){
        try{
            DB::beginTransaction();

            $ordenAtencion=Orden_Atencion::findOrFail($request->orden_id);
            $auditoria = new generalController();
            $docCita=Documento_Cita_Medica::documentoCita($request->orden_id, $request->documento_id)->get();
            
            foreach($docCita as $doc){
                $url_ant=$doc->doccita_url;
                $doc->delete();
                $auditoria->registrarAuditoria('Borrado documento cita medica para actualizacion documento #'.$request->get('documento_id'), $request->get('orden_id'), 'con url: '.$url_ant);
            }
            
            if ($request->file('documento')) {
                $empresa = Empresa::findOrFail(Auth::user()->empresa_id);

                $ruta ='DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$ordenAtencion->orden_fecha.'/'.$ordenAtencion->orden_numero.'/Documentos/DocumentosPesonales';
                if (!is_dir(public_path().'/'.$ruta)) {
                    mkdir(public_path().'/'.$ruta, 0777, true);
                }
                if ($request->file('documento')->isValid()) {
                    $documento=Documento_Orden_Atencion::findOrFail($request->documento_id);

                    $name = $documento->documento_nombre.'.'.$request->file('documento')->getClientOriginalExtension();
                    $path = $request->file('documento')->move(public_path().$ruta, $name);

                    $documentos_cita_medica=new Documento_Cita_Medica();
                    $documentos_cita_medica->doccita_nombre=$name;
                    $documentos_cita_medica->doccita_url=$ruta.'/'.$name;
                    $documentos_cita_medica->doccita_estado='1';
                    $documentos_cita_medica->orden_id=$request->orden_id;
                    $documentos_cita_medica->documento_id=$request->documento_id;
                    $documentos_cita_medica->save();

                    $auditoria->registrarAuditoria('Actualizado documento cita medica documento #'.$request->get('documento_id'), $request->get('orden_id'), 'con url: '.$ruta.'/'.$name);
                    DB::commit();
                    return json_encode(array("result"=>"OK", "documento"=>$ruta.'/'.$name));
                }
                else
                    throw new \Exception("El archivo no es v??lido");
            }
            else
                throw new \Exception("No se seleccion?? el documento");
        }
        catch(\Exception $e){
            DB::rollBack();
            return json_encode(array("result"=>"FAIL","mensaje"=> $e->getMessage()));
        }
    }

    private function crearDocumento($paciente, $imagen, $tipo){
        $imagenes=[];
        $empresa = Empresa::empresa()->first();

        $ruta = 'DocumentosPacientes/'.$empresa->empresa_ruc.'/'.$paciente->paciente_id;
        $extension = $imagen->extension();

        if ($imagen) {
            if (!is_dir(public_path().'/'.$ruta)) mkdir(public_path().'/'.$ruta, 0777, true);

            $name = 'documento_'.$tipo.'.'.$extension;
            $path = $imagen->move(public_path().'/'.$ruta, $name);
        
            return $ruta.'/'.$name;
        }
        else
            return null;
    }
}
