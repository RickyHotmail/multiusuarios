<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Analisis_Laboratorio;
use App\Models\Arqueo_Caja;
use App\Models\Aseguradora_Procedimiento;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Entidad_Procedimiento;
use App\Models\Especialidad;
use App\Models\Factura_Venta;
use App\Models\Forma_Pago;
use App\Models\Orden_Examen;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\User;
use App\Models\Procedimiento_Especialidad;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use App\Models\Tarifa_Iva;
use App\Models\Tipo_Seguro;
use App\Models\Vendedor;
use App\Models\Tipo_Examen;
use App\Models\Examen;
use App\Models\Detalle_Examen;
use App\Models\Orden_Atencion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ordenExamenEditarController extends Controller{
    public function index(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();         
            $ordenesExamenes=Orden_Examen::OrdenExamenesHOY()->select('orden_examen.orden_id as orden_examen_id', 'orden_atencion.orden_id', 'orden_fecha','orden_codigo','orden_numero', 'paciente_apellidos','paciente_nombres','orden_otros','orden_examen.orden_estado', 'expediente.expediente_id')->get();

            $medicos = Medico::medicos()->get();
            $rol=User::findOrFail(Auth::user()->user_id)->roles->first();

            if($ordenesExamenes){
                foreach($ordenesExamenes as $exa)
                    $exa->expediente->ordenatencion;
            }

            $data = [
                "medicos"=>$medicos,
                "rol"=>$rol,
                'sucursales'=>Sucursal::Sucursales()->get(),
                'ordenesExamenes'=>$ordenesExamenes,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];
           
            return view('admin.laboratorio.ordenesExamen.indexEditar', $data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    
    public function buscar(Request $request){
        //try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ordenesExamenes = Orden_Examen::OrdenesByFechaSuc($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal_id')
                                    )->select('orden_examen.orden_id as orden_examen_id', 'orden_atencion.orden_id', 'orden_fecha','orden_codigo','orden_numero', 'paciente_apellidos','paciente_nombres','orden_otros','orden_examen.orden_estado', 'expediente.expediente_id'
                                    )->orderBy('orden_numero','desc'
                                    )->get();
            
            //return $ordenesExamenes;
            $medicos = Medico::medicos()->get();
            $rol=User::findOrFail(Auth::user()->user_id)->roles->first();

            if($ordenesExamenes){
                foreach($ordenesExamenes as $exa){
                    if($exa->expediente)
                        $exa->expediente->ordenatencion;
                }
            }

            $data=[
                "medicos"=>$medicos,
                "rol"=>$rol,
                'fecI'=>$request->get('fecha_desde'),
                'fecF'=>$request->get('fecha_hasta'),
                'sucurslaC'=>$request->get('sucursal_id'),
                'sucursales'=>Sucursal::Sucursales()->get(),
                'ordenesExamenes'=>$ordenesExamenes,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.laboratorio.ordenesExamen.indexEditar', $data);
        /* }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  */
    }
    
    public function create()
    {
        //
    }


    public function store(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ordenesExamenes = Orden_Examen::OrdenesByFechaSuc($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal_id'))
                ->select('orden_examen.orden_id as orden_examen_id', 'orden_atencion.orden_id', 'orden_fecha','orden_codigo','orden_numero', 'paciente_apellidos','paciente_nombres','orden_otros','orden_examen.orden_estado', 'expediente.expediente_id')
                ->orderBy('orden_numero','asc')
                ->get();
            
            $medicos = Medico::medicos()->get();
            $rol=User::findOrFail(Auth::user()->user_id)->roles->first();

            if($ordenesExamenes){
                foreach($ordenesExamenes as $exa){
                    if($exa->expediente)
                        $exa->expediente->ordenatencion;
                }
            }

            $data=[
                "medicos"=>$medicos,
                "rol"=>$rol,
                'fecI'=>$request->get('fecha_desde'),
                'fecF'=>$request->get('fecha_hasta'),
                'sucurslaC'=>$request->get('sucursal_id'),
                'sucursales'=>Sucursal::Sucursales()->get(),
                'ordenesExamenes'=>$ordenesExamenes,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            return view('admin.laboratorio.ordenesExamen.index', $data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }

    public function show($id){
        try{
            $orden = Orden_Examen::OrdenExamen($id)->first();
            $orden->orden_estado='2';
            $orden->save();
            $analisis=new Analisis_Laboratorio();
            $puntoEmision = Punto_Emision::PuntoSucursalUser($orden->sucursal_id,Auth::user()->user_id)->first();
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($puntoEmision->punto_id, 'laboratorio')->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Analisis_Laboratorio::secuencial($rangoDocumento->rango_id)->max('analisis_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                return view('admin.laboratorio.ordenesExamen.facturarOrden',['ordenAtencion'=>$orden,'clienteO'=>Cliente::ClientesByCedula($orden->paciente->paciente_cedula)->first(),'vendedores'=>Vendedor::Vendedores()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($puntoEmision->punto_id)->get(),'formasPago'=>Forma_Pago::formaPagos()->get(), 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
            $general = new generalController();
            $url = $general->LaboratorioAnalisis($orden);
            return redirect('ordenesExamen')->with('success','Analisis Preatendido exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function edit(Request $request, $idExamen){
        $ordenExamen=[];
        if(str_ends_with($idExamen, '_000')){
            $id=substr($idExamen,0,-4);
            $ordenAtencion=Orden_Atencion::findOrFail($id);
        }
        else{
            $ordenExamen=Orden_Examen::find($idExamen);
            $ordenAtencion=$ordenExamen->expediente->ordenatencion;
        }

        $tipoExamenes = Tipo_Examen::TipoExamenes()->get();
        //$productos = Producto::Productos()->get();
        
        
        $expediente=$ordenAtencion->expediente;
        //return $expediente;
        //$ordenAtencion=$expediente->ordenatencion;
        $examenes = Examen::buscarProductosProcedimiento($ordenAtencion->paciente_id, $ordenAtencion->especialidad_id)->get();
        $detalleExamen=[];

        if($ordenExamen){
            $detalleExamen=$ordenExamen->detalle;

            foreach($detalleExamen as $detalle){
                $examen=$detalle->examen;
                $producto=$examen->producto;
            }
        }
        
        $data = [
            'examenesDetalle' => $detalleExamen,
            'examenes' => $examenes,
            'ordenExamen'=>$ordenExamen,
            'tipoExamenes'=>$tipoExamenes,
            'ordenAtencion'=>$ordenAtencion
            //'productos'=>$productos
        ];

        return view('admin.citasMedicas.examen.editar', $data);
    }

    public function update(Request $request, $id){
        try {
            DB::beginTransaction();
            $auditoria = new generalController();

            if(str_ends_with($id,'_000')){
                $idOrdenAtencion=substr($id, 0,-4);
                $ordenAtencion=Orden_Atencion::findOrFail($idOrdenAtencion);
                $ordenExamen=new Orden_Examen();
                $ordenExamen->expediente_id=$ordenAtencion->expediente->expediente_id;
                $ordenExamen->orden_estado=1;
                $ordenExamen->save();
            }
            else
                $ordenExamen=Orden_Examen::findOrFail($id);


            $detalleExamen=$ordenExamen->detalle;

            $borrados="";

            foreach($detalleExamen as $detalle){
                $borrados.=$detalle->examen_id.',';
                $detalle->delete();
            }
            $auditoria->registrarAuditoria('Eliminación de Examenes por modificación con expediente -> ' .   $ordenExamen->expediente->expediente_id, $ordenExamen->orden_id, 'Con examenes Ids '.$borrados);


            $laboratorio = $request->get('laboratorio');

            
            if($laboratorio){
                for ($i = 0; $i < count($laboratorio); ++$i) {
                    $detalleExamen = new Detalle_Examen();
                    $detalleExamen->detalle_estado="1";
                    $detalleExamen->examen_id=$laboratorio[$i];
                    $detalleExamen->orden_id=$ordenExamen->orden_id;
                    $detalleExamen->save();
                }
            }
            
            $ordenExamen->orden_otros=$request->otros_examenes;
            $ordenExamen->save();

            $auditoria->registrarAuditoria('Modificación de Examenes con expediente -> ' .   $ordenExamen->expediente->expediente_id, $ordenExamen->orden_id, 'Con examenes Ids '.json_encode($laboratorio));

            DB::commit();
            return $redirect = redirect('ordenExamenEditar')->with('success', 'Datos guardados exitosamente');
        }
        catch(\Exception $e){
            DB::rollBack();
            return $redirect = redirect('ordenExamenEditar')->with('error', 'Ocurrió un error al actualizar: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        //
    }
}
