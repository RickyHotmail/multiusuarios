<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cabecera_Rol_CM;
use App\Models\Detalle_Rol_CM;
use App\Models\Punto_Emision;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class modificarRolController extends Controller
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
            $empleado=Cabecera_Rol_CM::EmpleadosRol()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            return view('admin.RHCostaMarket.modificarRol.index',['fecha_desde'=>null,'fecha_hasta'=>null,'nombre_empleado'=>null,'empleado'=>$empleado,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
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
        if (isset($_POST['guardar'])){
            return $this->guardar($request);
        }
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
    }
    public function buscar(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $empleado=Cabecera_Rol_CM::EmpleadosRol()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            $rol=null;
            $datos=null;
            $count=1;
            if ($request->get('nombre_empleado') != "--TODOS--" ) {
                $rol=Cabecera_Rol_CM::RolesBuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'))->select('cabecera_rol_cm.cabecera_rol_id','cabecera_rol_cm.cabecera_rol_tipo','empleado.empleado_nombre','cabecera_rol_cm.cabecera_rol_fecha','cabecera_rol_cm.cabecera_rol_total_dias','cabecera_rol_cm.cabecera_rol_sueldo','cabecera_rol_cm.cabecera_rol_pago')->distinct()->get();
            }
            
            if ($request->get('nombre_empleado') == "--TODOS--") {
                $rol=Cabecera_Rol_CM::RolBusquedaFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('cabecera_rol_cm.cabecera_rol_id','cabecera_rol_cm.cabecera_rol_tipo','empleado.empleado_nombre','cabecera_rol_cm.cabecera_rol_fecha','cabecera_rol_cm.cabecera_rol_total_dias','cabecera_rol_cm.cabecera_rol_sueldo','cabecera_rol_cm.cabecera_rol_pago')->distinct()->get();
            }
               
          
            foreach($rol as $x){
                $bandera=false;
                $roles=Cabecera_Rol_CM::findOrFail($x->cabecera_rol_id);
                foreach($roles->detalles as $detalle){
                    if($detalle->detalle_rol_tipo!=null){
                        $bandera=true;
                    }
                }
                if ($bandera==false) {
                    $datos[$count]["count"]=($count-1);
                    $datos[$count]["idrol"]=$x->cabecera_rol_id;
                    $datos[$count]["tipo"]=$x->cabecera_rol_tipo;
                    $datos[$count]["nombre"]=$x->empleado_nombre;
                    $datos[$count]["fecha"]=$x->cabecera_rol_fecha;
                    $datos[$count]["dias"]=$x->cabecera_rol_total_dias;
                    $datos[$count]["sueldo"]=$x->cabecera_rol_sueldo;
                    $datos[$count]["pago"]=$x->cabecera_rol_pago;
                    $count++;
                }
            }
            
            return view('admin.RHCostaMarket.modificarRol.index',['datos'=>$datos,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_empleado'=>$request->get('nombre_empleado'),'empleado'=>$empleado,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
        }
        catch(\Exception $ex){      
            return redirect('modificacionRoles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request)
    {
        try{
            DB::beginTransaction();
            $idrol = $request->get('idrol'); 
            $fechafin=null;
            $fechaini=null;
            $contador = $request->get('contador');
            for ($i = 0; $i < count($contador); ++$i) {
                $rol=Cabecera_Rol_CM::findOrFail($idrol[$contador[$i]]);
                foreach($rol->detalles as $detalle){
                    $deta=Detalle_Rol_CM::findOrFail($detalle->detalle_rol_id);
                    $detalle->detalle_rol_tipo = 'PAGADO';
                    $deta->save();
                    $fechaini=$detalle->detalle_rol_fecha_inicio;
                    $fechafin=$detalle->detalle_rol_fecha_fin;
                }
                if($rol->cabecera_rol_fr_acumula>0){
                    $rubro=Rubro::existe('fondoReserva')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_fr_acumula;
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_tipo = 'ACUMULADO';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_fondo_reserva>0){
                    $rubro=Rubro::existe('fondoReserva')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_fondo_reserva;
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_tipo = 'PAGADO';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_decimotercero>0){
                    $rubro=Rubro::existe('decimoTercero')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_decimotercero;
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_tipo = 'PAGADO';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_decimocuarto>0){
                    $rubro=Rubro::existe('decimoCuarto')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_decimocuarto;
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_tipo = 'PAGADO';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_decimotercero_acumula>0){
                    $rubro=Rubro::existe('decimoTercero')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_decimotercero_acumula;
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_tipo = 'ACUMULADO';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_decimocuarto_acumula>0){
                    $rubro=Rubro::existe('decimoCuarto')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_decimocuarto_acumula;
                    $detalle->detalle_rol_tipo = 'ACUMULADO';
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_viaticos>0){
                    $rubro=Rubro::existe('viaticos')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_viaticos;
                    $detalle->detalle_rol_tipo = 'PAGADO';
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_iece_secap>0){
                    $rubro=Rubro::existe('iece')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_iece_secap;
                    $detalle->detalle_rol_tipo = 'PAGADO';
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_aporte_patronal>0){
                    $rubro=Rubro::existe('aportePatronal')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_aporte_patronal;
                    $detalle->detalle_rol_tipo = 'PAGADO';
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }
                if($rol->cabecera_rol_vacaciones>0){
                    $rubro=Rubro::existe('vacacion')->first();
                    $detalle= new Detalle_Rol_CM();
                    $detalle->detalle_rol_fecha_inicio = $fechaini;
                    $detalle->detalle_rol_fecha_fin = $fechafin;
                    $detalle->detalle_rol_descripcion = $rubro->rubro_descripcion;
                    $detalle->detalle_rol_valor = $rol->cabecera_rol_vacaciones;
                    $detalle->detalle_rol_tipo = 'PAGADO';
                    $detalle->detalle_rol_contabilizado = '1';
                    $detalle->detalle_rol_estado = '1';
                    $detalle->rubro_id = $rubro->rubro_id;
                    $rol->detalles()->save($detalle);
                }

                $rol->save();
                
            }
            DB::commit();
            return redirect('modificacionRoles')->with('success','Datos guardados exitosamente');
        }
        catch(\Exception $ex){ 
            DB::rollBack();     
            return redirect('modificacionRoles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
}
