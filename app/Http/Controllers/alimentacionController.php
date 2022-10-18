<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alimentacion;
use App\Models\Empleado;
use App\Models\Transaccion_Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class alimentacionController extends Controller
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
            $proveedor=Transaccion_Compra::ProveedorDistinsc()->select('proveedor.proveedor_id','proveedor_nombre')->distinct()->get();
           
            return view('admin.recursosHumanos.alimentacion.index',['proveedor'=>$proveedor,'empleado'=>Empleado::empleados()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $general = new generalController();
           
            $idEmpleado = $request->get('IDE');   
            $idEmpleado = $request->get('DID');   
            $valor = $request->get('Valor');   
            $nombre = $request->get('nombre'); 
            $alimentar = $request->get('idalim');   
            $general = new generalController(); 

            for ($i = 1; $i < count($idEmpleado); ++$i) {
                if($alimentar[$i]!=0){
                    $alimentacion =Alimentacion::findOrFail($alimentar[$i]);
                    $alimentacion->alimentacion_valor = $valor[$i];
                    $alimentacion->save();
                }
                else{
                    if ($valor[$i]>0) {
                        $alimentacion =new Alimentacion();
                        $alimentacion->alimentacion_fecha = $request->get('transaccion_fecha');
                        $alimentacion->alimentacion_valor = $valor[$i];
                        $alimentacion->alimentacion_estado = '1';
                        $alimentacion->empleado_id = $idEmpleado[$i];
                        $alimentacion->transaccion_id = $request->get('transaccion_id');
                        $alimentacion->save();
                        $general->registrarAuditoria('Registro de Alimentacion de Empleado : -> '.$nombre[$i], '0', 'Alimentacion del empleado '.$nombre[$i].' con el valor de '.$valor[$i]);
                    }
                }
            }
            DB::commit();
            return redirect('alimentacion')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('alimentacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function buscarByEmpleado($id){
        return Alimentacion::buscarEmpleado($id)->get();
    }
    public function buscarByAlimentacion($id){
        return Alimentacion::roloperativo($id)->get();
    }
}
