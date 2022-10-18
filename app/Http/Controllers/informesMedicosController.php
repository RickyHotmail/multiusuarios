<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aseguradora_Procedimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Especialidad;
use App\Models\Orden_Atencion;
use App\Models\Procedimiento_Especialidad;
use App\Models\sucursal;
use App\NEOPAGUPA\MultipleViewExcel;
use PDF;
use Excel;
use App\NEOPAGUPA\ViewExcel;
use DateTime;

class informesMedicosController extends Controller
{
    public function produccionMensual(){
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $especialidades = Especialidad::especialidades()->get();
        $sucursales = sucursal::sucursales()->get();

        $data=[
            'tipoPermiso'=>$tipoPermiso,
            'permisosAdmin'=>$permisosAdmin,
            'gruposPermiso'=>$gruposPermiso,
            'especialidades'=>$especialidades,
            'sucursales'=>$sucursales
        ];

        return view('admin/citasMedicas/informes/produccionMensualIndex', $data);

    }

    public function generarProduccionMensual(Request $request){
        try{   
            $sucursal=Sucursal::findOrFail($request->sucursal);

            if(isset($request->incluirTodasEspecialidades)) $especialidades=Especialidad::especialidades()->get();
            if(!isset($request->incluirTodasEspecialidades)) $especialidades=Especialidad::especialidad($request->especialidad)->get();
            $arrayDatos=[];

            foreach($especialidades as $especialidad){
                $ordenes=Orden_Atencion::ordenesByFechaSucEsp($request->fecha_desde, $request->fecha_hasta, $sucursal->sucursal_id, $especialidad->especialidad_id)->get();
                
                if($ordenes){
                    foreach($ordenes as $orden){
                        $expediente=$orden->expediente;
                        $producto=$orden->producto;
                        $paciente=$orden->paciente;
                        $especialidad=$orden->especialidad;
                        $tipoSeguro=$orden->tipoSeguro;


                        $procedimientoEspecialidad=Procedimiento_Especialidad::procedimientoProductoEspecialidad($producto->producto_id, $especialidad->especialidad_id)->first();
                        $procedimientoAseguradora=Aseguradora_Procedimiento::procedimientosAsignados($procedimientoEspecialidad->procedimiento_id, $orden->cliente_id)->first();
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

                            if($ordenExamen){
                                $detalleExamen=$ordenExamen->detalle;

                                foreach($detalleExamen as $detalle){
                                    $examen=$detalle->examen;

                                    if($examen){
                                        $productoExamen=$examen->producto;
                                    }
                                }
                            }
                        }
                    }
                }
                $arrayDatos[$especialidad->especialidad_nombre]=$ordenes;
            }

            //return $arrayDatos;
            
            return Excel::download(new MultipleViewExcel('admin.formatosExcel.produccionMensual', $arrayDatos), 'NEOPAGUPA - Reporte de Producción Mensual.xls');
        }catch(\Exception $ex){
            return redirect('/informesMedicos/produccionMensual')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
