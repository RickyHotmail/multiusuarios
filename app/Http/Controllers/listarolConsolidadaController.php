<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Punto_Emision;
use App\Models\Rol_Consolidado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listarolConsolidadaController extends Controller
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
            $empleado=Rol_Consolidado::EmpleadosRol()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            return view('admin.recursosHumanos.listarRol.index',['fecha_desde'=>null,'fecha_hasta'=>null,'fecha_todo'=>null,'nombre_empleado'=>null,'empleado'=>$empleado,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
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
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $empleado=Rol_Consolidado::EmpleadosRol()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            $rol=null;
            $datos=null;
            $count=1;
        if ($request->get('fecha_todo') != "on" && $request->get('nombre_empleado') != "--TODOS--" ) {
                $rol=Rol_Consolidado::RolesBuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'))->select('cabecera_rol.cabecera_rol_id','cabecera_rol.cabecera_rol_tipo','empleado.empleado_nombre','cabecera_rol.cabecera_rol_fecha','cabecera_rol.cabecera_rol_total_dias','cabecera_rol.cabecera_rol_sueldo','cabecera_rol.cabecera_rol_pago')->distinct()->get();
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_empleado') == "--TODOS--" ) {
                $rol=Rol_Consolidado::Roles()->select('cabecera_rol.cabecera_rol_id','empleado.empleado_nombre','cabecera_rol.cabecera_rol_tipo','cabecera_rol.cabecera_rol_fecha','cabecera_rol.cabecera_rol_total_dias','cabecera_rol.cabecera_rol_sueldo','cabecera_rol.cabecera_rol_pago')->distinct()->get();
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_empleado') == "--TODOS--") {
                $rol=Rol_Consolidado::RolBusquedaFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('cabecera_rol.cabecera_rol_id','cabecera_rol.cabecera_rol_tipo','empleado.empleado_nombre','cabecera_rol.cabecera_rol_fecha','cabecera_rol.cabecera_rol_total_dias','cabecera_rol.cabecera_rol_sueldo','cabecera_rol.cabecera_rol_pago')->distinct()->get();
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_empleado') != "--TODOS--" ) {
                $rol=Rol_Consolidado::RolBusquedaEmpleado($request->get('nombre_empleado'))->select('cabecera_rol.cabecera_rol_id','cabecera_rol.cabecera_rol_tipo','empleado.empleado_nombre','cabecera_rol.cabecera_rol_fecha','cabecera_rol.cabecera_rol_total_dias','cabecera_rol.cabecera_rol_sueldo','cabecera_rol.cabecera_rol_pago')->distinct()->get();
                            
            }   
          
            foreach($rol as $x){
                $datos[$count]["idrol"]=$x->cabecera_rol_id;
                $datos[$count]["tipo"]=$x->cabecera_rol_tipo;
                $datos[$count]["nombre"]=$x->empleado_nombre;
                $datos[$count]["fecha"]=$x->cabecera_rol_fecha;
                $datos[$count]["dias"]=$x->cabecera_rol_total_dias;
                $datos[$count]["sueldo"]=$x->cabecera_rol_sueldo;
                $datos[$count]["pago"]=$x->cabecera_rol_pago;
                $datos[$count]["cheque"]=0;
                $aux=Rol_Consolidado::findOrFail($x->cabecera_rol_id);
                foreach($aux->diariopago->detalles as $detall){
                    if(isset($detall->cheque)){
                        $datos[$count]["cheque"]=1;
                    }
                    
                }
                $count++;
            }
            
            return view('admin.recursosHumanos.listarRol.index',['datos'=>$datos,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_empleado'=>$request->get('nombre_empleado'),'empleado'=>$empleado,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
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
