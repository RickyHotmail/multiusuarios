<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Control_Dia;
use App\Models\Detalle_Control_Dias;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class controlDiasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }
    public function nuevo($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Control de Dias')->first();   
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Control_Dia::secuencial($rangoDocumento->rango_id)->max('control_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $datos = null; 
                return view('admin.recursosHumanos.controlDias.index',['empleados'=>Empleado::EmpleadosControlDias($rangoDocumento->puntoEmision->sucursal_id)->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),  'rangoDocumento'=>$rangoDocumento,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
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
        try{       
            DB::beginTransaction();
            $rango=Rango_Documento::findOrFail($request->get('rango_id'));
            $general = new generalController();
            $cierre = $general->cierre($request->get('fecha'),$rango->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('controldiario/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            if(count(Control_Dia::ControldiaDetalleRol($request->get('empleado_id'),$request->get('mes'),$request->get('anio'))->get())>0){
                return redirect('controldiario/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que esta asignado a un rol');
            } 
            if(count(Control_Dia::ControldiaDetalle($request->get('empleado_id'),$request->get('mes'),$request->get('anio'))->get())>0){
                $control=Control_Dia::ControldiaDetalle($request->get('empleado_id'),$request->get('mes'),$request->get('anio'))->first();
                $control_dia=Control_Dia::findOrFail($control->control_id);
                foreach($control_dia->detalles as $detalle){
                    $detalle->delete();
                }
            }
            else{
                $control_dia = new Control_Dia();
            }
            $control_dia->control_mes = $request->get('mes');
            $control_dia->control_ano = $request->get('anio');

            $control_dia->control_serie = $request->get('control_serie');
            $control_dia->control_secuencial = $request->get('control_numero');

            $control_dia->control_normal = $request->get('DTotalt');
            $control_dia->control_decanso = $request->get('DTotald');
            $control_dia->control_vacaciones = $request->get('DTotalv');
            $control_dia->control_permiso = $request->get('DTotalp');
            $control_dia->control_cosecha = $request->get('DTotalc');
            $control_dia->control_extra = $request->get('DTotalx');
            $control_dia->control_ausente = $request->get('DTotala');


            $control_dia->control_numero = $request->get('control_serie').substr(str_repeat(0, 9).$request->get('control_numero'), - 9);
            $control_dia->rango_id =$request->get('rango_id');
            $dia=$request->get('Dia');
            $control_dia->control_estado = 1;
            $control_dia->control_fecha = $request->get('fecha');
            $control_dia->empleado_id = $request->get('empleado_id');
            $control_dia->save();
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Control de dias con el empleado -> '.$request->get('empleado_id'),'0','');
            
            $count=0;
            $detalle=new Detalle_Control_Dias();
            for ($i = 1; $i <= count($dia); ++$i) {
                $count="control_dia".$i;
                if ($i>31) {
                    $count="control_dia".$i-31;
                }
                if ($dia[$i-1]) {
                    $detalle->$count= $dia[$i-1];
                }
                else{
                    $detalle->$count = '0';
                }
                if($i==31 || $i==62){
                    $detalle->detalle_estado='1';
                   
                    $control_dia->detalles()->save($detalle);
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Registro de Detalle de dias con el empleado -> '.$request->get('empleado_id'),'0','');
                    $detalle=new Detalle_Control_Dias();
                }
            }
           
           
            
            
         
            DB::commit();
            return redirect('controldiario/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('controldiario/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{
            DB::beginTransaction();
            $auditoria=new generalController();
            $controldia=Control_Dia::findOrFail($id);
            foreach($controldia->detalles as $control){
                $control->delete();
                $auditoria->registrarAuditoria('Eliminar el control de Detalle de dias con el empleado -> '.$controldia->empleado->empleado_nombre,$controldia->control_id,'');
            }
            $controldia->delete();
            $auditoria->registrarAuditoria('Eliminar el control de dias con el empleado -> '.$controldia->empleado->empleado_nombre,$controldia->control_id,'');
            DB::commit();
            return redirect('listacontroldia')->with('success','Datos Eliminados exitosamente');
        }
        catch(\Exception $ex){
            DB::rollBack();      
            return redirect('listacontroldia')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByEmpleado($id){
        return Control_Dia::buscarEmpleado($id)->get();
    }
    public function PresentarEmpleado($id){
        return Control_Dia::PresentarEmpleado($id)->get();
    }
    public function cargarControldias(Request $request){
        $datos=null;
        $mes=$request->get('mesrubro');
        $anio=$request->get('aniorubro');
        $contol=Control_Dia::ControldiaDetalle($request->get('buscar'),$mes,$anio)->first();
        $count=2;
        $datos[0]=$contol->control_ano;
        $datos[1]=$contol->control_mes;
        for ($i = 1; $i <= 31; ++$i) {
            $vari='control_dia'.$i;
            $datos[$count]=$contol->$vari;
            $count++;
        }
        return $datos;
    }
}
