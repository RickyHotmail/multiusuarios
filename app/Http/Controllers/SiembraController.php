<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Laboratorio_Camaronera;
use App\Models\Piscina;
use App\Models\Punto_Emision;
use App\Models\Siembra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiembraController extends Controller
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
            $siembra = Siembra::Siembras()->get();
            $piscinas = Piscina::Piscinas()->get();
            $laboratorios = Laboratorio_Camaronera::Laboratorios()->get();
            return view('admin.camaronera.siembra.index',['siembras'=>$siembra,'laboratorios'=>$laboratorios,'piscinas'=>$piscinas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $siembra = new Siembra();
            $siembra->siembra_codigo = $request->get('idCodigo');
            $siembra->siembra_secuencial = $request->get('idSecuencial');
            $siembra->siembra_larvas = $request->get('idLarvas');
            $siembra->siembra_entregas = $request->get('idEntregas');
            $siembra->siembra_fecha = $request->get('Fecha');
            $siembra->siembra_fecha_costo = $request->get('FechaCosto');
            $siembra->siembra_fecha_siembra = $request->get('idInicio');
            $siembra->siembra_longitud = $request->get('idLongitud');
            $siembra->siembra_peso = $request->get('idPeso');
            $siembra->siembra_densidad = $request->get('idDensidad');
            $siembra->siembra_cultivo = $request->get('idCultivo');
            $siembra->siembra_precio_larva = $request->get('idPrecio');
            $siembra->siembra_precio_larva = $request->get('idPrecio');
            $siembra->siembra_costo_inicial=$request->get('idLarvas')*$request->get('idPrecio');
            $siembra->siembra_estado = 1;
            $siembra->laboratorio_id = $request->get('idLaboratorio');
            $siembra->nauplio_id = $request->get('idNauplio');
            $siembra->piscina_id = $request->get('idPiscina');
            $siembra->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Piscina -> '.$request->get('idCodigo').' Con numero de larvas -> '.$request->get('idLarvas'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('siembra')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('siembra')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $piscinas = Piscina::Piscinas()->get();
            $siembra = Siembra::findOrFail($id);
            return view('admin.camaronera.siembra.index',['siembra'=>$siembra,'piscinas'=>$piscinas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminar($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $siembra = Siembra::findOrFail($id);
            return view('admin.camaronera.siembra.index',['siembra'=>$siembra, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
        try{            
            DB::beginTransaction();
            $siembra = Siembra::findOrFail($id);
            $siembra->siembra_secuencial = $request->get('idSecuencial');             
            $siembra->siembra_codigo = $request->get('idCodigo');
            $siembra->siembra_larvas = $request->get('idLarvas');
            $siembra->siembra_procedencia = $request->get('idProcedencia');
            $siembra->siembra_laboratorio = $request->get('idLaboratorio');
            $siembra->siembra_entregas = $request->get('idEntregas');
            $siembra->siembra_fecha = $request->get('Fecha');
            $siembra->siembra_fecha_costo = $request->get('FechaCosto');
            $siembra->siembra_fecha_siembra = $request->get('idInicio');
            $siembra->siembra_longitud = $request->get('idLongitud');
            $siembra->siembra_peso = $request->get('idPeso');
            $siembra->siembra_densidad = $request->get('idDensidad');
            $siembra->siembra_cultivo = $request->get('idCultivo');
            $siembra->siembra_naupilo = $request->get('idNauplio');
            $siembra->siembra_precio_larva = $request->get('idPrecio');
            $siembra->siembra_costo_inicial=$request->get('idLarvas')*$request->get('idPrecio');
            $siembra->siembra_costo=$request->get('idLarvas')*$request->get('idPrecio');
            $siembra->piscina_id = $request->get('idPiscina');
            $siembra->save();

            $piscina=Piscina::findOrFail($request->get('idPiscina'));
            $piscina->piscina_tipo_estado='EN PRODUCCIÓN';
            $piscina->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizar de la siembra  con codigo -> '.$request->get('idCodigo').' Con numero de larvas -> '.$request->get('idLarvas'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('siembra')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('siembra')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
            $siembra = Siembra::findOrFail($id);
            $siembra->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminar La Siembra -> '.$siembra->siembra_codigo.' Con numero de larvas -> '.$siembra->siembra_larvas,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('siembra')->with('success','Datos guardados Eliminados');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('siembra')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarBySiembra($id){
        $piscina=Piscina::findOrFail($id);
        $siembra=Siembra::Piscinas($id)->max('siembra_secuencial');
        $sec=1;
        if($siembra){
            $sec=$siembra;
            $sec=$sec+1;
        }
        $datos[0]=$piscina->piscina_codigo.'.'.$sec;
        $datos[1]=$sec;
        $datos[2]=$piscina->piscina_volumen_agua;
        $datos[3]=$piscina->piscina_espejo_agua;
        return $datos;
    } 
    public function buscarBytransferencia($id){
        $piscina=Piscina::findOrFail($id);
        $siembra=Siembra::Piscinas($id)->max('siembra_secuencial');
        $sec=1;
        if($siembra){
            $sec=$siembra;
            $sec=$sec+1;
        }
        $datos[0]=$piscina->piscina_codigo.'.'.$sec;
        $datos[1]=$sec;
        return $datos;
    } 

    public function buscarBy($buscar){    
        return Siembra::Siembra($buscar)->get();
    }
    public function buscarByM($buscar){    
         $data= Siembra::findOrFail($buscar);
         $datos[0]='BIFASICO';
         if(isset($data->siembrapadre)){
            $data2=Siembra::findOrFail($data->siembra_ref_id);
            $datos[0]='BIFASICO';
         }
         if(isset($data2->siembrapadre)){
            $datos[0]='TRIIFASICO';
         }

         return $datos;
    }
}
