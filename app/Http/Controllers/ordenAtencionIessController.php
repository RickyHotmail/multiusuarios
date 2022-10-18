<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Proveedor;
use App\Models\Empleado;
use App\Models\Medico;
use App\Models\User;
use App\Models\Documento_Anulado;
use App\Models\Documento_Orden_Atencion;
use App\Models\Documento_Orden_Paciente;
use App\Models\Empresa;
use App\Models\Especialidad;
use App\Models\Forma_Pago;
use App\Models\Medico_Especialidad;
use App\Models\Orden_Atencion;
use App\Models\Paciente;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\Models\Tipo_Dependencia;
use App\Models\Tipo_Seguro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use DateTime;

class ordenAtencionIessController extends Controller
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

            return view('admin.agendamientoCitas.ordenAtencionIess.index', $data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    public function ordenAtencionIessBuscar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ordenesAtencion = Orden_Atencion::OrdenesByFechaSucIess($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal_id'))->get();
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

            return view('admin.agendamientoCitas.ordenAtencionIess.index',$data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }


    public function nuevaOrdenIess()
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
            return view('admin.agendamientoCitas.ordenAtencionIess.nuevaOrdenIess',['cajaAbierta'=>$cajaAbierta,'bodegas'=>Bodega::Bodegas()->get(),'formasPago'=>Forma_Pago::formaPagos()->get(),'seguros'=>Tipo_Seguro::tipos()->get(),'documentos'=>Documento_Orden_Atencion::DocumentosOrdenesAtencion()->get(),'tiposDependencias'=>Tipo_Dependencia::TiposDependencias()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'sucursales'=>$sucursales,'pacientes'=>$pacientes,'especialidades'=>$especialidades,'ordenesAtencion'=>$ordenesAtencion,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try {
            DB::beginTransaction();
           
            $general = new generalController();
            $empresa = Empresa::empresa()->first();
        
            /***************SABER SI SE GENERAR UN ASIENTO DE COSTO****************/
            $cierre = $general->cierre($request->get('fechaCitaID'),$request->get('idSucursal'));
            if ($cierre) {
                return redirect('ordenAtencion')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $ordenAtencion = new Orden_Atencion();
            $ordenAtencion->orden_codigo = $request->get('Codigo');
            $ordenAtencion->orden_numero = $request->get('Codigo').'-'.$request->get('Secuencial');
            $ordenAtencion->orden_secuencial = $request->get('Secuencial');
            $ordenAtencion->orden_reclamo =$request->get('idReclamoNum');
            $ordenAtencion->orden_secuencial_reclamo =$request->get('idReclamoSec');
            $ordenAtencion->orden_fecha = (new DateTime($request->get('fechaCitaID')))->format('Y-m-d');                 //new DateTime($request->get('fechaCitaID')))->format('d-m-Y')
            $ordenAtencion->orden_hora = $request->get('horaCitaID');
            $ordenAtencion->orden_observacion = $request->get('Observacion');
            $ordenAtencion->orden_iess = '1';
            $ordenAtencion->orden_frecuencia = $request->get('tipo_atencion');
            $ordenAtencion->orden_dependencia = $request->get('es_dependiente');
            $ordenAtencion->orden_cedula_afiliado = $request->get('idCedulaAsegurado');
            $ordenAtencion->orden_nombre_afiliado = $request->get('idNombreAsegurado');
            $ordenAtencion->orden_precio = $request->get('IdPrecio');
            $ordenAtencion->orden_cobertura_porcentaje = $request->get('IdCoberturaPorcen');
            $ordenAtencion->orden_cobertura = $request->get('IdCobertura');
            $ordenAtencion->orden_copago = $request->get('IdCopago');
            $mespecialidad=Medico_Especialidad::findOrFail($request->get('idMespecialidad'));
            $ordenAtencion->medico_id = $mespecialidad->medico->medico_id;
            $ordenAtencion->tipod_id = $request->get('IdTipoDependencia');
            $ordenAtencion->entidad_id = $request->get('identidad');
            $ordenAtencion->orden_estado  = 2;
            $ordenAtencion->cliente_id = $request->get('ClienteId');
            $ordenAtencion->sucursal_id = $request->get('idSucursal');
            $ordenAtencion->paciente_id = $request->get('idPaciente');
            $ordenAtencion->especialidad_id = $request->get('especialidad_id');
            $ordenAtencion->producto_id = $request->get('IdCodigo');
            $ordenAtencion->tipo_id = $request->get('idSeguro');
            $ordenAtencion->orden_precio = 0;
            $ordenAtencion->orden_cobertura_porcentaje = 0;
            $ordenAtencion->orden_cobertura = 0;
            $ordenAtencion->orden_copago = 0;
            $ordenAtencion->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de orden de atencion -> '.$request->get('Codigo').' del Paciente -> '.$request->get('idPaciente'), '0', '');
            /*Fin de registro de auditoria */

            $documentos=Documento_Orden_Atencion::DocumentosOrdenesAtencion()->get();
            foreach ($documentos as $documento) {
                $file='file-es'.$documento->documento_id;

                if ($request->file($file)) {
                    $ruta = public_path().'/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime($request->get('fechaCitaID')))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos/DocumentosPersonales';
                    if (!is_dir($ruta)) {
                        mkdir($ruta, 0777, true);
                    }
                    if ($request->file($file)->isValid()) {
                        $name = $documento->documento_nombre.'.'.$request->file($file)->getClientOriginalExtension();
                        $path = $request->file($file)->move($ruta, $name);
                        $documento_orden_paciente=new Documento_Orden_Paciente();
                        //$documen_orden_paciente->docpaciente_url=$name;
                        $documento_orden_paciente->docpaciente_url=$ruta.'/'.$name;
                        $documento_orden_paciente->docpaciente_estado='1';
                        $documento_orden_paciente->orden_id=$ordenAtencion->orden_id;
                        $documento_orden_paciente->documento_id=$documento->documento_id;
                        $documento_orden_paciente->save();
                    }
                }
            }

            $empresa = Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.ordenesAtenciones.ordenAtencionIess', ['orden'=>$ordenAtencion,'empresa'=>$empresa]);
            $ruta = public_path().'/DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime($request->get('fechaCitaID')))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos';
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'Orden de atencion';
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            DB::commit();
            return redirect('ordenAtencionIess')->with('success', 'Datos guardados exitosamente')->with('pdf2', 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.(new DateTime($request->get('fechaCitaID')))->format('d-m-Y').'/'.$ordenAtencion->orden_numero.'/Documentos/'.$nombreArchivo.'.pdf');
          
        }
        catch(\Exception $ex){    
            DB::rollBack();  
            return redirect('ordenAtencionIess')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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

    public function getCitaMedicaDisponible(Request $request){
        $medico_especialidad=$request->get('medico_id');
        $especialidad_id=$request->get('especialidad_id');
        $fecha=$request->get('fecha');

        $medico_id = Medico_Especialidad::mEspecialidad($medico_especialidad)->first()->medico_id;
        $ordenAtencion = Orden_Atencion::ordenCitaDisponibleHora($medico_id, $especialidad_id, $fecha)->get();

        //return $ordenAtencion;

        if(count($ordenAtencion)>0)
            return array('ocupada'=> '1');
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
