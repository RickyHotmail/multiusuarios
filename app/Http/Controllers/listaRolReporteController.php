<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cabecera_Rol_CM;
use App\Models\Empresa;
use App\Models\Rubro;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
class listaRolReporteController extends Controller
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
            $sucursales=Cabecera_Rol_CM::EmpleadosSucursal()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            $rubros=Rubro::Rubrostipos()->get();
            return view('admin.RHCostaMarket.reportesRol.index',['sucursales'=>$sucursales,'rubros'=>$rubros,'empleado'=>$empleado,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
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
            $rubro=$request->get('contador');
            $rol=Cabecera_Rol_CM::Buscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'),$request->get('sucursal'))->get();
            $datos=null;
            $rubros=null;
            $coun=1;
            $count=1;
            if ($rubro) {
                foreach ($rol as $roles) {
                    $datos[$count]["cedula"]=$roles->empleado->empleado_cedula;
                    $datos[$count]["nombre"]=$roles->empleado->empleado_nombre;
                    $total=0;
                    foreach ($roles->detalles as $detalle) {
                        for ($i = 0; $i < count($rubro); ++$i) {
                            if ($detalle->rubro_id==$rubro[$i]) {
                                $total=$total+$detalle->detalle_rol_valor;
                                
                            }
                        }
                    }
                    for ($i = 0; $i < count($rubro); ++$i) {
                            $rub=Rubro::findOrFail($rubro[$i]);
                            if ($rub->rubro_nombre=="decimoTercero") {
                                $total=$total+$roles->cabecera_rol_decimotercero;
                                $total=$total+$roles->cabecera_rol_decimotercero_acumula;
                            } elseif ($rub->rubro_nombre==="decimoCuarto") {
                                $total=$total+$roles->cabecera_rol_decimocuarto;
                                $total=$total+$roles->cabecera_rol_decimocuarto_acumula;
                            } elseif ($rub->rubro_nombre==="fondoReserva") {
                                $total=$total+$roles->cabecera_rol_fondo_reserva;
                                $total=$total+$roles->cabecera_rol_fr_acumula;
                            } elseif ($rub->rubro_nombre==="viaticos") {
                                $total=$total+$roles->cabecera_rol_viaticos;
                            } elseif ($rub->rubro_nombre==="iece") {
                                $total=$total+$roles->cabecera_rol_iece_secap;
                            } elseif ($rub->rubro_nombre==="aportePatronal") {
                                $total=$total+$roles->cabecera_rol_aporte_patronal;
                            } elseif ($rub->rubro_nombre==="vacacion") {
                                $total=$total+$roles->cabecera_rol_vacaciones;
                            }
                    }
                    $datos[$count]["total"]=$total;
                    $count++;
                    
                }
                for ($i = 0; $i < count($rubro); ++$i) {
                    $rub=Rubro::findOrFail($rubro[$i]);
                    $rubros[$coun]=$rub->rubro_descripcion;
                    $coun++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/roles/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.rolesCM.reporte', ['rubros'=>$rubros,'datos'=>$datos,'desde'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'ReproteRol';
            return PDF::loadHTML($view)->save('roles/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        
        
        
    }
    public function enviar(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $empleado=Cabecera_Rol_CM::EmpleadosRol()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            $rubros=Rubro::Rubros()->get();
            $empleado=Cabecera_Rol_CM::Buscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'))->get();
            return view('admin.RHCostaMarket.reportesRol.index',['rubros'=>$rubros,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_empleado'=>$request->get('nombre_empleado'),'empleado'=>$empleado,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscar(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $empleado=Cabecera_Rol_CM::EmpleadosRol()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            $rubros=Rubro::Rubros()->get();

            return view('admin.RHCostaMarket.reportesRol.index',['rubros'=>$rubros,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_empleado'=>$request->get('nombre_empleado'),'empleado'=>$empleado,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
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
