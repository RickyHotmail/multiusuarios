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
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_Diario;
use App\Models\Detalle_FV;
use App\Models\Detalle_Pago_CXC;
use App\Models\Diario;
use App\Models\Documento_Cita_Medica;
use App\Models\Documento_Orden_Atencion;
use App\Models\Empresa;
use App\Models\Entidad_Procedimiento;
use App\Models\Procedimiento_Especialidad;
use App\Models\Especialidad;
use App\Models\Factura_Venta;
use App\Models\Forma_Pago;
use App\Models\HorarioFijo;
use App\Models\Movimiento_Producto;
use App\Models\Orden_Atencion;
use App\Models\Pago_CXC;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Medico;
use App\Models\Orden_Examen;
use App\Models\Rango_Documento;
use App\Models\Tarifa_Iva;
use App\Models\Tipo_Dependencia;
use App\Models\Tipo_Seguro;
use App\Models\Vendedor;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

use function PHPSTORM_META\type;

class ordenAtencionController extends Controller
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

            return view('admin.agendamientoCitas.ordenAtencion.index', $data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    public function ordenAtencionBuscar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ordenesAtencion = Orden_Atencion::ordenesByFechaSucParticulares($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal_id'))->get();
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

            return view('admin.agendamientoCitas.ordenAtencion.index',$data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    public function indexEditar()
    {
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

            return view('admin.agendamientoCitas.ordenAtencion.indexEditar', $data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    public function ordenAtencionBuscarEditar(Request $request){
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

            return view('admin.agendamientoCitas.ordenAtencion.indexEditar',$data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    public function indexConsolidado()
    {
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

    public function ordenAtencionBuscarConsolidado(Request $request){
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

        if($paciente->documento_paciente!=null)
            $listaDocumentos[]=$this->crearPdfDocumentoPaciente($ordenAtencion, [$paciente->documento_paciente], "Paciente", 0);

        if($paciente->paciente_dependiente==1){
            if($paciente->documento_afiliado!=null)  
                $listaDocumentos[]=$this->crearPdfDocumentoPaciente($ordenAtencion, [$paciente->documento_afiliado], "Afiliado", 0);
        }

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

        
        if(isset($ordenExamen)){
            /*comprobar si hay un analisis en */
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

        if($ordenAtencion->orden_id==149){
            if(isset($ordenImagen)){

                /*comprobar si hay un analisis en */
                if($ordenImagen->detalleImagen){
                    foreach($ordenImagen->detalleImagen as $det){
                        if($det->detalle_estado==2){
                            $path = public_path().'/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime($ordenAtencion->orden_fecha))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos/Imagenes/imagen_resultado'.$det->detalle_id.'_1.pdf';
                            $listaDocumentos[]=$path;
                        }
                    }
                }
            }
        }
    
        

        if(isset($prescripcion)){
            if($prescripcion->prescripcion_documento!="")
                $listaDocumentos[]=$this->crearPdfDocumentoPaciente($ordenAtencion, [$prescripcion->prescripcion_documento], "", 1);
        }
        
        $listaDocumentos[]=$this->crearInformeIndividualPdf($ordenAtencion);

        //include 'PDFMerger.php';

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

            ///////////////diagnóstico///////////////////////////////
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

    public function nuevaOrden()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $sucursales=Sucursal::Sucursales()->get();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first(); 
            $pacientes = Paciente::Pacientes()->get();
            $especialidades = Especialidad::Especialidades()->get();
            $ordenesAtencion = Orden_Atencion::Ordenes()->get();
            $secuencial=1;
            $secuencialAux = Orden_Atencion::Ordenes()->max('orden_secuencial');
            if($secuencialAux){
                $secuencial=$secuencialAux+1;
            }

            return view('admin.agendamientoCitas.ordenAtencion.nuevaOrden',['cajaAbierta'=>$cajaAbierta,'bodegas'=>Bodega::Bodegas()->get(),'formasPago'=>Forma_Pago::formaPagos()->get(),'seguros'=>Tipo_Seguro::tipos()->get(),'documentos'=>Documento_Orden_Atencion::DocumentosOrdenesAtencion()->get(),'tiposDependencias'=>Tipo_Dependencia::TiposDependencias()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'sucursales'=>$sucursales,'pacientes'=>$pacientes,'especialidades'=>$especialidades,'ordenesAtencion'=>$ordenesAtencion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try {
            DB::beginTransaction();
            $empresa = Empresa::findOrFail(Auth::user()->empresa_id);
        
            /***************SABER SI SE GENERAR UN ASIENTO DE COSTO****************/

            $dateNew = $request->get('idFechaFac'); 
            $banderaP = false;
        
            $producto = Producto::findOrFail($request->get('IdCodigo'));
            if ($producto->producto_tipo == '1') {
                $banderaP = true;
            }
            $general = new generalController();
            
            if(isset($request->checkFacturar)){
                $cierre = $general->cierre($dateNew,$request->get('idSucursal'));
                if ($cierre) {
                    return redirect('ordenAtencion')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
                /********************cabecera de factura de venta ********************/
                $docElectronico = new facturacionElectronicaController();
                $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
                $puntoEmision = Punto_Emision::PuntoSucursalUser($request->get('idSucursal'), Auth::user()->user_id)->first();
                $factura = new Factura_Venta();

                $rangoDocumento=Rango_Documento::PuntoRango($puntoEmision->punto_id, 'Factura')->first();
                $secuencial=1;
                if ($rangoDocumento) {
                    $secuencialAux=Factura_Venta::secuencial($rangoDocumento->rango_id)->max('factura_secuencial');
                    if ($secuencialAux) {
                        $secuencial=$secuencialAux+1;
                    }
                }

                $factura->factura_numero = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $factura->factura_serie = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie;
                $factura->factura_secuencial = $secuencial;
                $factura->rango_id = $rangoDocumento->rango_id;

                $factura->factura_fecha =$dateNew;
                $factura->factura_lugar = $request->get('factura_lugar');
                $factura->factura_tipo_pago = $request->get('factura_tipo_pago');
                $factura->factura_dias_plazo = 0;
                $factura->factura_fecha_pago = $dateNew;
                $factura->factura_subtotal = $request->get('IdCopago');
                $factura->factura_descuento = $request->get('IdDescuento');
                $factura->factura_tarifa0 = $request->get('IdCopago');
                $factura->factura_tarifa12 = 0;
                $factura->factura_iva = 0;
                $factura->factura_total = $request->get('IdCopago');
                $factura->factura_comentario = 'ORDEN DE ATENCION N° '. $request->get('Codigo').'-'.$request->get('Secuencial');
                ;
                $factura->factura_porcentaje_iva = 12;
                $factura->factura_emision = $request->get('tipoDoc');
                $factura->factura_ambiente = 'PRODUCCIÓN';
                $factura->factura_autorizacion = $docElectronico->generarClaveAcceso($factura->factura_numero, $request->get('idFechaFac'), "01");
                $factura->factura_estado = '1';
                $factura->bodega_id = $request->get('bodega_id');
                $factura->cliente_id = $request->get('clienteID');
                $factura->forma_pago_id = $request->get('forma_pago_id');
                /********************cuenta por cobrar***************************/

                    
                $cxc = new Cuenta_Cobrar();
                $cxc->cuenta_descripcion = 'VENTA CON FACTURA No. '.$factura->factura_numero;
                if ($request->get('factura_tipo_pago') == 'CREDITO' or $request->get('factura_tipo_pago') == 'CONTADO') {
                    $cxc->cuenta_tipo =$request->get('factura_tipo_pago');
                    $cxc->cuenta_saldo = $request->get('IdCopago');
                    $cxc->cuenta_estado = '1';
                } else {
                    $cxc->cuenta_tipo = $request->get('factura_tipo_pago');
                    $cxc->cuenta_saldo = 0.00;
                    $cxc->cuenta_estado = '2';
                }

                $cxc->cuenta_fecha = $dateNew;
                $cxc->cuenta_fecha_inicio = $dateNew;
                $cxc->cuenta_fecha_fin = $dateNew;
                $cxc->cuenta_monto = $request->get('IdCopago');
                $cxc->cuenta_valor_factura = $request->get('IdCopago');
                $cxc->cliente_id = $request->get('clienteID');
                $cxc->sucursal_id = Rango_Documento::rango($rangoDocumento->rango_id)->first()->puntoEmision->sucursal_id;
                $cxc->save();
                $general->registrarAuditoria('Registro de cuenta por cobrar de factura -> '.$factura->factura_numero, $factura->factura_numero, 'Registro de cuenta por cobrar de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('IdCopago').' con clave de acceso -> '.$factura->factura_autorizacion);
                /****************************************************************/
                $factura->cuentaCobrar()->associate($cxc);

                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                    /**********************asiento diario****************************/
                    $diario = new Diario();
                    $diario->diario_codigo = $general->generarCodigoDiario($dateNew, 'CFVE');
                    $diario->diario_fecha = $dateNew;
                    $diario->diario_referencia = 'COMPROBANTE DIARIO DE FACTURA DE VENTA';
                    $diario->diario_tipo_documento = 'FACTURA';
                    $diario->diario_numero_documento = $factura->factura_numero;
                    $diario->diario_beneficiario = $request->get('buscarCliente');
                    $diario->diario_tipo = 'CFVE';
                    $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                    $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $dateNew)->format('m');
                    $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $dateNew)->format('Y');
                    $diario->diario_comentario = 'COMPROBANTE DIARIO DE FACTURA: '.$factura->factura_numero;
                    $diario->diario_cierre = '0';
                    $diario->diario_estado = '1';
                    $diario->empresa_id = Auth::user()->empresa_id;
                    $diario->sucursal_id = Rango_Documento::rango($rangoDocumento->rango_id)->first()->puntoEmision->sucursal_id;
                    $diario->save();
                    $general->registrarAuditoria('Registro de diario de venta de factura -> '.$factura->factura_numero, $factura->factura_numero, 'Registro de diario de venta de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('IdCopago').' y con codigo de diario -> '.$diario->diario_codigo);
                    /****************************************************************/
                    
                    if ($banderaP) {
                        /**********************asiento diario de costo ****************************/
                        $diarioC = new Diario();
                        $diarioC->diario_codigo = $general->generarCodigoDiario($dateNew, 'CCVP');
                        $diarioC->diario_fecha = $dateNew;
                        $diarioC->diario_referencia = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO';
                        $diarioC->diario_tipo_documento = 'FACTURA';
                        $diarioC->diario_numero_documento = $factura->factura_numero;
                        $diarioC->diario_beneficiario = $request->get('buscarCliente');
                        $diarioC->diario_tipo = 'CCVP';
                        $diarioC->diario_secuencial = substr($diarioC->diario_codigo, 8);
                        $diarioC->diario_mes = DateTime::createFromFormat('Y-m-d', $dateNew)->format('m');
                        $diarioC->diario_ano = DateTime::createFromFormat('Y-m-d', $dateNew)->format('Y');
                        $diarioC->diario_comentario = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO CON FACTURA: '.$factura->factura_numero;
                        $diarioC->diario_cierre = '0';
                        $diarioC->diario_estado = '1';
                        $diarioC->empresa_id = Auth::user()->empresa_id;
                        $diarioC->sucursal_id = Rango_Documento::rango($rangoDocumento->rango_id)->first()->puntoEmision->sucursal_id;
                        $diarioC->save();
                        $general->registrarAuditoria('Registro de diario de costo de venta de factura -> '.$factura->factura_numero, $factura->factura_numero, 'Registro de diario de costo de venta de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('IdCopago').' y con codigo de diario -> '.$diarioC->diario_codigo);
                    
                        $factura->diarioCosto()->associate($diarioC);
                    }
                }

                if ($cxc->cuenta_estado == '2') {
                    /********************Pago por Venta en efectivo***************************/
                    $pago = new Pago_CXC();
                    $pago->pago_descripcion = 'PAGO EN EFECTIVO';
                    $pago->pago_fecha = $cxc->cuenta_fecha;
                    $pago->pago_tipo = 'PAGO EN EFECTIVO';
                    $pago->pago_valor = $cxc->cuenta_monto;
                    $pago->pago_estado = '1';
                    if(Auth::user()->empresa->empresa_contabilidad== '1'){
                        $pago->diario()->associate($diario);
                    }
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXC();
                    $detallePago->detalle_pago_descripcion = 'PAGO EN EFECTIVO';
                    $detallePago->detalle_pago_valor = $cxc->cuenta_monto;
                    $detallePago->detalle_pago_cuota = 1;
                    $detallePago->detalle_pago_estado = '1';
                    $detallePago->cuenta_id = $cxc->cuenta_id;
                    $detallePago->pagoCXC()->associate($pago);
                    $detallePago->save();
                    
                }

                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('IdCopago');
                    $detalleDiario->detalle_haber = 0.00 ;
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    if ($request->get('factura_tipo_pago') == 'CONTADO') {
                        $detalleDiario->cliente_id = $request->get('clienteID');
                        $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                        if ($parametrizacionContable->parametrizacion_cuenta_general == '1') {
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        } else {
                            $parametrizacionContable = Cliente::findOrFail($request->get('clienteID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                        }
                    } else {
                        $Caja=Caja::findOrFail($request->get('caja_id'));
                        $detalleDiario->cuenta_id = $Caja->cuenta_id;
                    }
                    
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo, $factura->factura_numero, 'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('IdCopago'));
                }


                
                if ($request->get('idIva') > 0){
                    if(Auth::user()->empresa->empresa_contabilidad== '1'){
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $request->get('idIva') ;
                        $detalleDiario->detalle_comentario = 'P/R IVA COBRADO EN VENTA';
                        $detalleDiario->detalle_tipo_documento = 'FACTURA';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id,'IVA VENTAS')->first();
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$parametrizacionContable->cuenta->cuenta_numero.' en el haber por un valor de -> '.$request->get('idIva'));
                    }
                }
                /****************************************************************/
                /****************************************************************/
                
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    $factura->diario()->associate($diario);
                }

                $factura->save();
                $general->registrarAuditoria('Registro de factura de venta numero -> '.$factura->factura_numero, $factura->factura_numero, 'Registro de factura de venta numero -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('IdCopago').' con clave de acceso -> '.$factura->factura_autorizacion.' y con codigo de diario -> '.$diario->diario_codigo);
                
        

                /*******************************************************************/
                /********************detalle de factura de venta********************/
                $detalleFV = new Detalle_FV();
                $detalleFV->detalle_cantidad = 1;
                $detalleFV->detalle_precio_unitario = $request->get('IdCopago');
                $detalleFV->detalle_descuento = $request->get('IdDescuento');
                $detalleFV->detalle_iva = 0;
                $detalleFV->detalle_total = $request->get('IdCopago');
                $detalleFV->detalle_descripcion = $request->get('nombreP');
                $detalleFV->detalle_estado = '1';
                $detalleFV->producto_id = $request->get('IdCodigo');
                    /******************registro de movimiento de producto******************/
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$dateNew;
                    $movimientoProducto->movimiento_cantidad=1;
                    $movimientoProducto->movimiento_precio=$request->get('IdCopago');
                    $movimientoProducto->movimiento_iva=0;
                    $movimientoProducto->movimiento_total=$request->get('IdCopago');
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='FACTURA DE VENTA';
                    $movimientoProducto->movimiento_motivo='VENTA';
                    $movimientoProducto->movimiento_tipo='SALIDA';
                    $movimientoProducto->movimiento_descripcion='FACTURA DE VENTA No. '.$factura->factura_numero;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$request->get('IdCodigo');
                    $movimientoProducto->bodega_id=$factura->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por factura de venta numero -> '.$factura->factura_numero, $factura->factura_numero, 'Registro de movimiento de producto por factura de venta numero -> '.$factura->factura_numero.' producto de nombre -> '.$request->get('nombreP').' con la cantidad de -> 1 con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                $detalleFV->movimiento()->associate($movimientoProducto);
                $factura->detalles()->save($detalleFV);
                $general->registrarAuditoria('Registro de detalle de factura de venta numero -> '.$factura->factura_numero, $factura->factura_numero, 'Registro de detalle de factura de venta numero -> '.$factura->factura_numero.' producto de nombre -> '.$request->get('nombreP').' con la cantidad de -> 1 a un precio unitario de -> '.$request->get('IdCopago'));
                if (Auth::user()->empresa->empresa_contabilidad == '1') {
                    $producto = Producto::findOrFail($request->get('IdCodigo'));
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber =$request->get('IdCopago');
                    $detalleDiario->detalle_comentario = 'P/R VENTA DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_venta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo, $factura->factura_numero, 'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$producto->cuentaVenta->cuenta_numero.' en el haber por un valor de -> '.$request->get('IdCopago'));
                    
                    if ($banderaP) {
                        if ($producto->producto_tipo == '1') {
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = 0.00;
                            $detalleDiario->detalle_haber = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo, $factura->factura_numero, 'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$detalleDiario->detalle_haber);
                            
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_haber = 0.00;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $parametrizacionContable = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo, $factura->factura_numero, 'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$detalleDiario->detalle_debe);
                        }
                    }
                }
            
                /*******************************************************************/
                if($factura->factura_emision == 'ELECTRONICA'){
                    $facturaAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlFactura($factura), 'FACTURA');
                    $factura->factura_xml_estado = $facturaAux->factura_xml_estado;
                    $factura->factura_xml_mensaje = $facturaAux->factura_xml_mensaje;
                    $factura->factura_xml_respuestaSRI = $facturaAux->factura_xml_respuestaSRI;
                    if ($facturaAux->factura_xml_estado == 'AUTORIZADO') {
                        $factura->factura_xml_nombre = $facturaAux->factura_xml_nombre;
                        $factura->factura_xml_fecha = $facturaAux->factura_xml_fecha;
                        $factura->factura_xml_hora = $facturaAux->factura_xml_hora;
                    }
                    $factura->update();
                }
            }
       
            $ordenAtencion = new Orden_Atencion();
            $cierre = $general->cierre($dateNew,$request->get('idSucursal'));

            if ($cierre) {
                return redirect('ordenAtencion')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }

            $ordenAtencion->orden_codigo = $request->get('Codigo');
            $ordenAtencion->orden_numero = $request->get('Codigo').'-'.$request->get('Secuencial');
            $ordenAtencion->orden_secuencial = $request->get('Secuencial');
            $ordenAtencion->orden_reclamo =$request->get('idReclamoNum');
            $ordenAtencion->orden_secuencial_reclamo =$request->get('idReclamoSec');
            $ordenAtencion->orden_fecha =$request->get('fechaCitaID');
            $ordenAtencion->orden_hora = $request->get('horaCitaID');
            $ordenAtencion->orden_observacion = $request->get('Observacion');

            $ordenAtencion->orden_iess = '0';
            $ordenAtencion->orden_frecuencia = $request->get('tipo_atencion');
            $ordenAtencion->orden_dependencia = $request->get('es_dependiente');
            $ordenAtencion->orden_cedula_afiliado = $request->get('idCedulaAsegurado');
            $ordenAtencion->orden_nombre_afiliado = $request->get('idNombreAsegurado');
            $ordenAtencion->orden_precio = $request->get('IdPrecio');
            $ordenAtencion->orden_cobertura_porcentaje = $request->get('IdCoberturaPorcen');
            $ordenAtencion->orden_cobertura = $request->get('IdCobertura');
            $ordenAtencion->orden_descuento = $request->get('IdDescuentoPorcentaje');
            $ordenAtencion->orden_copago = $request->get('IdCopago');

            if(isset($request->checkFacturar)) $ordenAtencion->factura_id = $factura->factura_id;
            
            $mespecialidad=Medico_Especialidad::findOrFail($request->get('idMespecialidad'));
            $ordenAtencion->medico_id = $mespecialidad->medico->medico_id;

            $ordenAtencion->tipod_id = $request->get('IdTipoDependencia');
            $ordenAtencion->entidad_id = $request->get('identidad');

            $ordenAtencion->orden_estado  = 2;
            $ordenAtencion->cliente_id = $request->get('clienteID');
            $ordenAtencion->sucursal_id = $request->get('idSucursal');
            $ordenAtencion->paciente_id = $request->get('idPaciente');
            $ordenAtencion->especialidad_id = $request->get('especialidad_id');
            $ordenAtencion->producto_id = $request->get('IdCodigo');
            $ordenAtencion->tipo_id = $request->get('idSeguro');
            $ordenAtencion->save();

            //guardar documentos paciente
            $paciente=Paciente::findOrFail($ordenAtencion->paciente_id);

            if($request->file('file-documento-paciente')!=null){
                $fotoDir=$this->crearDocumento($paciente,  $request->file('file-documento-paciente'), "paciente");
                $paciente->documento_paciente=$fotoDir;
                $paciente->save();
            }

            if($paciente->paciente_dependiente==1){
                if($request->file('file-documento-afiliado')!=null){
                    $fotoDir=$this->crearDocumento($paciente,  $request->file('file-documento-afiliado'), "afiliado");
                    $paciente->documento_afiliado=$fotoDir;
                    $paciente->save();
                }
            }

            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de orden de atencion -> '.$request->get('Codigo').' del Paciente -> '.$request->get('idPaciente'), '0', '');
            /*Fin de registro de auditoria */

            $documento=Documento_Orden_Atencion::DocumentosOrdenesAtencion()->get();
            foreach ($documento as $documentos) {
                $file='file-es'.$documentos->documento_id;
                
                if ($request->file($file)) {
                    $ruta = public_path().'/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime("$dateNew"))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos/DocumentosPesonales';
                    if (!is_dir($ruta)) {
                        mkdir($ruta, 0777, true);
                    }
                    if ($request->file($file)->isValid()) {
                        $name = $documentos->documento_nombre.'.'.$request->file($file)->getClientOriginalExtension();
                        $path = $request->file($file)->move($ruta, $name);

                        $documentos_cita_medica=new Documento_Cita_Medica();
                            $documentos_cita_medica->doccita_nombre=$name;
                            $documentos_cita_medica->doccita_url=$path;
                            $documentos_cita_medica->doccita_estado='1';
                            $documentos_cita_medica->orden_id=$ordenAtencion->orden_id;
                        $documentos_cita_medica->save();
                    }
                }
            }

            $empresa = Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.ordenesAtenciones.ordenAtencion', ['orden'=>$ordenAtencion,'empresa'=>$empresa]);
            $ruta = public_path().'/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime("$dateNew"))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos';
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'Orden de atencion';
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            
            
            ///* descomentar
            //if(isset($request->checkFacturar)) $url = $general->pdfDiario($diario);
            //*/
            DB::commit();
            
            $redirect=redirect('ordenAtencion')->with('success', 'Datos guardados exitosamente, Factura registrada y autorizada exitosamente');
            if(isset($request->checkFacturar)) $redirect->with('diario', $general->pdfDiario($diario));
            $redirect->with('pdf2', '/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime("$dateNew"))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos/'.$nombreArchivo.'.pdf');


            if (isset($facturaAux->factura_xml_estado)){
                if ($facturaAux->factura_xml_estado = 'AUTORIZADO') 
                    $redirect->with('pdf', 'documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.(new DateTime("$dateNew"))->format('d-m-Y').'/'.$factura->factura_xml_nombre.'.pdf');
                else
                    $redirect->with('error2', 'ERROR --> '.$facturaAux->factura_xml_estado.' : '.$facturaAux->factura_xml_mensaje);
            }
            
            return $redirect;
        }
        catch(\Exception $ex){    
            DB::rollBack();  
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
                    throw new \Exception("El archivo no es válido");
            }
            else
                throw new \Exception("No se seleccionó el documento");
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



    public function imprimirorden($id)
    {
        try{     
            $orden=Orden_Atencion::Orden($id)->get()->first();
            $empresa = Empresa::empresa()->first();
            $ruta = public_path().'/'.$empresa->empresa_ruc.'/DocumentosOrdenAtencion/'.DateTime::createFromFormat('Y-m-d', $orden->orden_fecha)->format('d-m-Y').'/'.$orden->orden_numero.'/Documentos';
            echo "$ruta";
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'Orden de atencion';
            $view =  \View::make('admin.formatosPDF.ordenesAtenciones.ordenAtencion', ['orden'=>$orden,'empresa'=>$empresa]);
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->stream('ordenAtencion.pdf');
        }
        catch(\Exception $ex){      
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
            $sucursales=Sucursal::Sucursales()->get();
            $especialidades = Especialidad::Especialidades()->get();
            $pacientes = Paciente::Pacientes()->get();  
            $empleados = Empleado::Empleados()->get();
            $proveedores = Proveedor::Proveedores()->get();
            $ordenAtencion=Orden_Atencion::Orden($id)->first();
            $secuencial = $ordenAtencion->orden_secuencial;
            if($ordenAtencion){
                return view('admin.agendamientoCitas.ordenAtencion.ver',['empleados'=>$empleados,'proveedores'=>$proveedores,'ordenAtencion'=>$ordenAtencion,'pacientes'=>$pacientes,'especialidades'=>$especialidades,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'sucursales'=>$sucursales, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            
            $sucursales=Sucursal::Sucursales()->get();
            $pacientes = Paciente::Pacientes()->get();    
            $especialidades = Especialidad::Especialidades()->get();
            $ordenAtencion = Orden_Atencion::Orden($id)->first();
            $secuencial = $ordenAtencion->orden_secuencial;
            if($ordenAtencion){
                return view('admin.agendamientoCitas.ordenAtencion.editar',['secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'ordenAtencion'=>$ordenAtencion, 'sucursales'=>$sucursales,'pacientes'=>$pacientes,'especialidades'=>$especialidades,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $ordenAtencion = Orden_Atencion::findOrFail($id); 
            $general = new generalController();
            $cierre = $general->cierre($ordenAtencion->orden_fecha,$ordenAtencion->sucursal_id);          
            if($cierre){
                return redirect('ordenAtencion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }  
            $ordenAtencion->orden_codigo = $request->get('Codigo');
            $ordenAtencion->orden_numero = $request->get('Codigo').'-'.$request->get('Secuencial');
            $ordenAtencion->orden_secuencial = $request->get('Secuencial');
            $ordenAtencion->orden_fecha = $request->get('Fecha');
            $ordenAtencion->orden_hora = $request->get('Hora');
            $ordenAtencion->orden_observacion = $request->get('Observacion');                      
            if ($request->get('idEstado') == "on"){
                $ordenAtencion->orden_estado ="1";
            }else{
                $ordenAtencion->orden_estado ="0";
            }
            $ordenAtencion->sucursal_id = $request->get('idSucursal');
            $ordenAtencion->paciente_id = $request->get('idPaciente');
            $ordenAtencion->mespecialidad_id = $request->get('idMespecialidad');           
            $ordenAtencion->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de orden de atencion -> '.$request->get('Codigo').' del Paciente -> '.$request->get('idPaciente'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('ordenAtencion')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('ordenAtencion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');
    }

    public function buscarByDia($mespecialidad_id){
        return HorarioFijo::HorarioDia($mespecialidad_id)->get();
    }

    public function buscarByFecha($buscar){
        return Orden_Atencion::OrdenFecha($buscar)->get();
    }
    
    

    public function buscarAseguradoraProcedimiento(Request $request){
        $datos = null;
        $aseguradoraProcedimiento = Aseguradora_Procedimiento::AseguradoraProcedimientoById($request->get('procedimientoA_id'))->first();
        $datos[0]=$aseguradoraProcedimiento;
        $datos[1]=Entidad_Procedimiento::ValorAsignado($aseguradoraProcedimiento->procedimiento_id,$request->get('entidad_id'))->first();

        return $datos;
    }
    public function secuencialReclamo($aseguradora){
        $final=null;
        $orden =Cliente::Cliente($aseguradora)->first(); 
        $datos = Orden_Atencion::where('cliente_id','=',$aseguradora)->max('orden_secuencial_reclamo')+1;  
        $anulada=substr(str_repeat(0, 9).$datos, - 9);
        $final[0]=$datos;
        $final[1] = $orden->cliente_abreviatura.'-'.$anulada;
     return $final;
    }


    public function getCitaMedicaDisponible(Request $request){
        $medico_id=$request->get('medico_id');
        $especialidad_id=$request->get('especialidad_id');
        $fecha1=$request->get('fecha1');
        $fecha2=$request->get('fecha2');

        
        
        $ordenAtencion = Orden_Atencion::ordenCitaDisponible($medico_id, $especialidad_id, $fecha1, $fecha2)->get();
        //return $ordenAtencion;

        if(count($ordenAtencion)>0)
            return array(['ocupada'=> '1']);
        else
            return array(['ocupada'=> '0']);

    }

    public function getOrdenesMedico(Request $request){
        $medico_id=$request->get('medico_id');
        $especialidad_id=$request->get('especialidad_id');
        $fecha1=$request->get('fecha1');
        $fecha2=$request->get('fecha2');

        
        
        $ordenAtencion = Orden_Atencion::ordenCitaDisponible($medico_id, $especialidad_id, $fecha1, $fecha2)->get();
        return $ordenAtencion;
    }
}
