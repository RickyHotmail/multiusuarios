<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asignacion_Rol;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Rol_Movimiento;
use App\Models\sucursal;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class asignacionRolController extends Controller
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
            return view('admin.RHCostaMarket.asignacionRoles.index',['sucursales'=>Sucursal::Sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
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
            setlocale(LC_TIME, 'spanish');
            $temp = new DateTime($request->get('fechames').'-01');
           
            $anio = $temp->format('Y');
            $mes=strftime("%B", strtotime($request->get('fechames').'-01'));
            $general = new generalController();
            $cierre = $general->cierre(($request->get('fechames').'-01'),$request->get('sucursal'));          
            if($cierre){
                return redirect('asignacionRol')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $tipo = $request->get('rubro_tipo'); 
            $rubro = $request->get('rubro_id');
            $idEmpleado = $request->get('DID'); 
            $valor = $request->get('Valor');   
            $nombre = $request->get('nombre'); 
            $idmovimiento = $request->get('idalim');   
            $general = new generalController(); 

            for ($i = 1; $i < count($idEmpleado); ++$i) {
                if($idmovimiento[$i]!=0){
                    $movimiento=Rol_Movimiento::findOrFail($idmovimiento[$i]);
                    $movimiento->rol_movimiento_valor = $valor[$i];
                    $movimiento->save();
                }
                else{
                    if ($valor[$i]>0) {
                        $movimiento=new Rol_Movimiento();
                        $movimiento->rol_movimiento_mes = $mes;
                        $movimiento->rol_movimiento_anio = $anio;
                        $movimiento->rol_movimiento_tipo = $tipo;
                        $movimiento->rol_movimiento_valor = $valor[$i];
                        $movimiento->rol_movimiento_estado = '1';
                        $movimiento->empleado_id = $idEmpleado[$i];
                        $movimiento->rubro_id = $rubro;
                        $movimiento->save();
                        $general->registrarAuditoria('Registro de Rol Movimiento de Empleado : -> '.$nombre[$i], '0', 'Con rubro id '.$rubro.' con el valor de '.$valor[$i]);
                    }
                }
            }
            DB::commit();
            return redirect('asignacionRol')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('asignacionRol')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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

    public function presentarEmpleadosRubro(Request $request){
        $compra=null;
        setlocale(LC_TIME, 'spanish');
        $temp = new DateTime($request->get('fecha').'-01');
        $anio = $temp->format('Y');
        $mes=strftime("%B", strtotime($request->get('fecha').'-01'));
        $rubro=Rol_Movimiento::MovimientoRubro($request->get('buscar'),$mes,$anio)->get();
        $empleados=Empleado::EmpleadosBySucursal($request->get('sucursal'))->get();
        $i=0;
        foreach($empleados as $empleado){
            $compra[$i]["idalim"]=0;
            $compra[$i]["idrol"]=0;
            $compra[$i]["ide"]=$empleado->empleado_id;
            $compra[$i]["cedula"]=$empleado->empleado_cedula;
            $compra[$i]["nombre"]=$empleado->empleado_nombre;
            $compra[$i]["valor"]=0.00;
          
            
           if (count($rubro)>0) {
               foreach ($rubro as $rubros) {
                   if ($rubros->empleado_id==$empleado->empleado_id) {
                       $compra[$i]["valor"]=$rubros->rol_movimiento_valor;
                       $compra[$i]["idalim"]=$rubros->rol_movimiento_id;
                       if ($rubros->cabecera_rol_cm_id) {
                           $compra[$i]["idrol"]=$rubros->cabecera_rol_cm_id;
                       }
                   
                 
                   }
               }
           }
            $i++;
        }
        return $compra;
    }
}
