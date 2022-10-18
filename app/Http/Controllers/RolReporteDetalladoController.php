<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cabecera_Rol_CM;
use App\Models\Empresa;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use App\NEOPAGUPA\ViewExcel;
use Excel;
class RolReporteDetalladoController extends Controller
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
            $rubros=Rubro::Rubrostipos()->get();
            $sucursales=Cabecera_Rol_CM::EmpleadosSucursal()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            $ingre=count(Rubro::Rubrotipo('2')->get());
            $egre=count(Rubro::Rubrotipo('1')->get());
            $bene=count(Rubro::Rubrotipo('3')->get());
            $otro=count(Rubro::Rubrotipo('4')->get());
            return view('admin.RHCostaMarket.reportesRol.indexdetalle',['sucursales'=>$sucursales,'ingre'=>$ingre,'egre'=>$egre,'bene'=>$bene,'otros'=>$otro,'rubros'=>$rubros,'empleado'=>$empleado,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
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
        if (isset($_POST['pdf'])){
            return $this->pdf($request);
        }
        if (isset($_POST['excel'])){
            return $this->excel($request);
        }
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
    }
    public function excel(Request $request){
        try{ 
            $rol=Cabecera_Rol_CM::Buscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'),$request->get('sucursal'))->get();
            $sucursales=Cabecera_Rol_CM::EmpleadosSucursal()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            $matriz=null;
            $datos=null;
            $count=1;
            
            $ingre=count(Rubro::Rubrotipo('2')->get());
            $egre=count(Rubro::Rubrotipo('1')->get());
            $bene=count(Rubro::Rubrotipo('3')->get());
            $otro=count(Rubro::Rubrotipo('4')->get());
            $rubros=Rubro::Rubrostipos()->get();
            foreach ($rol as $roles) {
                $matriz[$count]["cedula"]=$roles->empleado->empleado_cedula;
                $matriz[$count]["nombre"]=$roles->empleado->empleado_nombre;
                foreach ($rubros as $rubro) {
                    $matriz[$count]["tipo"]=$rubro->rubro_tipo;
                    $nombre=$rubro->rubro_nombre;
                    $matriz[$count][$nombre]=0;
                    foreach ($roles->detalles as $detalle) {
                        if ($detalle->rubro_id==$rubro->rubro_id) {
                            $matriz[$count][$nombre]=$detalle->detalle_rol_valor;
                        }
                    }

                }
                $matriz[$count]["fondoReserva"]=0.00;
                $matriz[$count]["decimoTercero"]=0.00;
    
                $matriz[$count]["decimoCuarto"]=0.00;
                if ($roles->cabecera_rol_fr_acumula>0) {
                    $matriz[$count]["EfondoReserva"]='Acumulado';
                    $matriz[$count]["fondoReserva"]=$roles->cabecera_rol_fr_acumula;
                }
                if ($roles->cabecera_rol_fondo_reserva>0) {
                    $matriz[$count]["EfondoReserva"]='Pagado';
                    $matriz[$count]["fondoReserva"]=$roles->cabecera_rol_fondo_reserva;
                }
                if ($roles->cabecera_rol_decimotercero_acumula>0) {
                    $matriz[$count]["EdecimoTercero"]='Acumulado';
                    $matriz[$count]["decimoTercero"]=$roles->cabecera_rol_decimotercero_acumula;
                }
                if ($roles->cabecera_rol_decimotercero>0) {
                    $matriz[$count]["EdecimoTercero"]='Pagado';
                    $matriz[$count]["decimoTercero"]=$roles->cabecera_rol_decimotercero;
                }
                if ($roles->cabecera_rol_decimocuarto_acumula>0) {
                    $matriz[$count]["EdecimoCuarto"]='Acumulado';
                    $matriz[$count]["decimoCuarto"]=$roles->cabecera_rol_decimocuarto_acumula;
                }
                if ($roles->cabecera_rol_decimocuarto>0) {
                    $matriz[$count]["EdecimoCuarto"]='Pagado';
                    $matriz[$count]["decimoCuarto"]=$roles->cabecera_rol_decimocuarto;
                }
    
                $matriz[$count]["totalingresos"]=$roles->cabecera_rol_total_ingresos;
                $matriz[$count]["totalegresos"]=$roles->cabecera_rol_total_egresos;
                
                $matriz[$count]["Eviaticos"]='Pagado';
                $matriz[$count]["viaticos"]=$roles->cabecera_rol_viaticos;
                $matriz[$count]["Eiece"]='Pagado';
                $matriz[$count]["iece"]=$roles->cabecera_rol_iece_secap;
                $matriz[$count]["EaportePatronal"]='Pagado';
                $matriz[$count]["aportePatronal"]=$roles->cabecera_rol_aporte_patronal;
                $matriz[$count]["Evacacion"]='Pagado';
                $matriz[$count]["vacacion"]=$roles->cabecera_rol_vacaciones;
                $matriz[$count]["total"]=$roles->cabecera_rol_pago;
   
                $count++;
            } 
            
            $datos[1]=$rubros;
            $datos[2]=$matriz;

            $datos[3]=$ingre;
            $datos[4]=$egre;
            $datos[5]=$bene;
            $datos[6]=$otro;

            return Excel::download(new ViewExcel('admin.formatosExcel.roldetallado',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('rolreporteDetallado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request){
        try{ 
            $ingre=count(Rubro::Rubrotipo('2')->get());
            $egre=count(Rubro::Rubrotipo('1')->get());
            $bene=count(Rubro::Rubrotipo('3')->get());
            $otro=count(Rubro::Rubrotipo('4')->get());
            $rol=Cabecera_Rol_CM::Buscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'),$request->get('sucursal'))->get();
            $datos=null;
            $count=1;  
            $rubros=Rubro::Rubrostipos()->get();
            foreach ($rol as $roles) {
                $datos[$count]["cedula"]=$roles->empleado->empleado_cedula;
                $datos[$count]["nombre"]=$roles->empleado->empleado_nombre;
                foreach ($rubros as $rubro) {
                    $datos[$count]["tipo"]=$rubro->rubro_tipo;
                    $nombre=$rubro->rubro_nombre;
                    $datos[$count][$nombre]=0;
                    foreach ($roles->detalles as $detalle) {
                        if ($detalle->rubro_id==$rubro->rubro_id) {
                            $datos[$count][$nombre]=$detalle->detalle_rol_valor;
                        }
                    }

                }
                $datos[$count]["fondoReserva"]=0.00;
                $datos[$count]["decimoTercero"]=0.00;
    
                $datos[$count]["decimoCuarto"]=0.00;
                if ($roles->cabecera_rol_fr_acumula>0) {
                    $datos[$count]["EfondoReserva"]='Acumulado';
                    $datos[$count]["fondoReserva"]=$roles->cabecera_rol_fr_acumula;
                }
                if ($roles->cabecera_rol_fondo_reserva>0) {
                    $datos[$count]["EfondoReserva"]='Pagado';
                    $datos[$count]["fondoReserva"]=$roles->cabecera_rol_fondo_reserva;
                }
                if ($roles->cabecera_rol_decimotercero_acumula>0) {
                    $datos[$count]["EdecimoTercero"]='Acumulado';
                    $datos[$count]["decimoTercero"]=$roles->cabecera_rol_decimotercero_acumula;
                }
                if ($roles->cabecera_rol_decimotercero>0) {
                    $datos[$count]["EdecimoTercero"]='Pagado';
                    $datos[$count]["decimoTercero"]=$roles->cabecera_rol_decimotercero;
                }
                if ($roles->cabecera_rol_decimocuarto_acumula>0) {
                    $datos[$count]["EdecimoCuarto"]='Acumulado';
                    $datos[$count]["decimoCuarto"]=$roles->cabecera_rol_decimocuarto_acumula;
                }
                if ($roles->cabecera_rol_decimocuarto>0) {
                    $datos[$count]["EdecimoCuarto"]='Pagado';
                    $datos[$count]["decimoCuarto"]=$roles->cabecera_rol_decimocuarto;
                }

                $datos[$count]["totalingresos"]=$roles->cabecera_rol_total_ingresos;
                $datos[$count]["totalegresos"]=$roles->cabecera_rol_total_egresos;
                
                $datos[$count]["Eviaticos"]='Pagado';
                $datos[$count]["viaticos"]=$roles->cabecera_rol_viaticos;
                $datos[$count]["Eiece"]='Pagado';
                $datos[$count]["iece"]=$roles->cabecera_rol_iece_secap;
                $datos[$count]["EaportePatronal"]='Pagado';
                $datos[$count]["aportePatronal"]=$roles->cabecera_rol_aporte_patronal;
                $datos[$count]["Evacacion"]='Pagado';
                $datos[$count]["vacacion"]=$roles->cabecera_rol_vacaciones;
                $datos[$count]["total"]=$roles->cabecera_rol_pago;
                $count++;
            } 
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.rolesCM.roldetallado', ['ingre'=>$ingre,'egre'=>$egre,'bene'=>$bene,'otros'=>$otro,'rubros'=>$rubros,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'empresa'=>$empresa]);
            $nombreArchivo = 'reporteroldetallado';
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');

        }catch(\Exception $ex){
            return redirect('rolreporteDetallado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    
    public function buscar(Request $request)
    { 
        try{
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
        $sucursales=Cabecera_Rol_CM::EmpleadosSucursal()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
        $rol=Cabecera_Rol_CM::Buscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'),$request->get('sucursal'))->get();
        $datos=null;
        $count=1;
        $empleado=Cabecera_Rol_CM::EmpleadosRol()->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
       
        $ingre=count(Rubro::Rubrotipo('2')->get());
        $egre=count(Rubro::Rubrotipo('1')->get());
        $bene=count(Rubro::Rubrotipo('3')->get());
        $otro=count(Rubro::Rubrotipo('4')->get());
        $rubros=Rubro::Rubrostipos()->get();
        foreach ($rol as $roles) {
            $datos[$count]["cedula"]=$roles->empleado->empleado_cedula;
            $datos[$count]["nombre"]=$roles->empleado->empleado_nombre;
            
            foreach ($rubros as $rubro) {
                $datos[$count]["tipo"]=$rubro->rubro_tipo;
                $nombre=$rubro->rubro_nombre;
                $datos[$count][$nombre]=0;
                foreach ($roles->detalles as $detalle) {
                    if ($detalle->rubro_id==$rubro->rubro_id) {
                        $datos[$count][$nombre]=$detalle->detalle_rol_valor;
                    }
                }

            }
            $datos[$count]["fondoReserva"]=0.00;
            $datos[$count]["decimoTercero"]=0.00;

            $datos[$count]["decimoCuarto"]=0.00;

            $datos[$count]["totalingresos"]=$roles->cabecera_rol_total_ingresos;
            $datos[$count]["totalegresos"]=$roles->cabecera_rol_total_egresos;
            if ($roles->cabecera_rol_fr_acumula>0) {
                $datos[$count]["EfondoReserva"]='Acumulado';
                $datos[$count]["fondoReserva"]=$roles->cabecera_rol_fr_acumula;
            }
            if ($roles->cabecera_rol_fondo_reserva>0) {
                $datos[$count]["EfondoReserva"]='Pagado';
                $datos[$count]["fondoReserva"]=$roles->cabecera_rol_fondo_reserva;
            }
            if ($roles->cabecera_rol_decimotercero_acumula>0) {
                $datos[$count]["EdecimoTercero"]='Acumulado';
                $datos[$count]["decimoTercero"]=$roles->cabecera_rol_decimotercero_acumula;
            }
            if ($roles->cabecera_rol_decimotercero>0) {
                $datos[$count]["EdecimoTercero"]='Pagado';
                $datos[$count]["decimoTercero"]=$roles->cabecera_rol_decimotercero;
            }
            if ($roles->cabecera_rol_decimocuarto_acumula>0) {
                $datos[$count]["EdecimoCuarto"]='Acumulado';
                $datos[$count]["decimoCuarto"]=$roles->cabecera_rol_decimocuarto_acumula;
            }
            if ($roles->cabecera_rol_decimocuarto>0) {
                $datos[$count]["EdecimoCuarto"]='Pagado';
                $datos[$count]["decimoCuarto"]=$roles->cabecera_rol_decimocuarto;
            }

            $datos[$count]["totalingresos"]=$roles->cabecera_rol_total_ingresos;
            $datos[$count]["totalegresos"]=$roles->cabecera_rol_total_egresos;
            
            $datos[$count]["Eviaticos"]='Pagado';
            $datos[$count]["viaticos"]=$roles->cabecera_rol_viaticos;
            $datos[$count]["Eiece"]='Pagado';
            $datos[$count]["iece"]=$roles->cabecera_rol_iece_secap;
            $datos[$count]["EaportePatronal"]='Pagado';
            $datos[$count]["aportePatronal"]=$roles->cabecera_rol_aporte_patronal;
            $datos[$count]["Evacacion"]='Pagado';
            $datos[$count]["vacacion"]=$roles->cabecera_rol_vacaciones;
            $datos[$count]["total"]=$roles->cabecera_rol_pago;
            $count++;
        } 
        
        return view('admin.RHCostaMarket.reportesRol.indexdetalle',['sucursales'=>$sucursales,'ingre'=>$ingre,'egre'=>$egre,'bene'=>$bene,'otros'=>$otro,'datos'=>$datos,'rubros'=>$rubros,'sucursalid'=>$request->get('sucursal'),'fechadesde'=>$request->get('fecha_desde'),'fechahasta'=>$request->get('fecha_hasta'),'nombre_empleado'=>$request->get('nombre_empleado'),'empleado'=>$empleado,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
        }catch(\Exception $ex){
            return redirect('rolreporteDetallado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
