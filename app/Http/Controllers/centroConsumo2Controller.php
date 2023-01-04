<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Centro_Consumo;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Sustento_Tributario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class centroConsumo2Controller extends Controller{
    public function index(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
           
            $sustentosTributario25 = Sustento_Tributario::Sustentos()->get();
            $secuencialMax=Centro_Consumo::nivel(0)->max('centro_consumo_secuencial');
            $centroCons = Centro_Consumo::CentroConsumos()->select('centro_consumo_id','centro_consumo_numero','centro_consumo_nombre','centro_consumo_padre', 'centro_consumo_secuencial',  'sustento_id',  'empresa_id','centro_consumo_nivel',DB::raw('(select count(*) from detalle_diario where centro_consumo.centro_consumo_id=detalle_diario.centro_consumo_id ) as detallesContable'))->get();
            //return $centroCons;

            
            $secuencial = 1;
            if($secuencialMax){$secuencial=$secuencialMax+1;}
            
            /* return "sec: ".$secuencial; */


            return view('admin.compras.centroConsumoplan.index',[
                'sustentosTributario25'=>$sustentosTributario25,
                'centroCons'=>$centroCons,
                'secuencial'=>$secuencial,
                'PE'=>Punto_Emision::puntos()->get(),
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
                ]
            );
        }catch(\Exception $ex){
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
    public function subir()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
           
            return view('admin.compras.centroConsumoplan.cargar',['PE'=>Punto_Emision::puntos()->get(), 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
    
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function cargarguardar(Request $request){
       
            if($request->file('excelProv')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProv')->getClientOriginalExtension();
                $path = $request->file('excelProv')->move(public_path().'\temp\planconsumo', $name); 
                $array = Excel::toArray(new Centro_Consumo(), $path); 
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][0]);
                    $validacion=Centro_Consumo::NivelPadre($validar)->get();
                    if (count($validacion)==0) {
                        $cuenta = new Centro_Consumo();
                        $cuenta->centro_consumo_numero = $validar;
                        $cuenta->centro_consumo_nombre = $array[0][$i][1];
                        $nume= $validar;
                        $porciones = explode(".", $nume);
                        $padre='';
                        for ($j=0;$j <= count($porciones)-2;$j++) {
                            $padre=$padre.$porciones[$j].'.';
                        }
                        $cuenta->centro_consumo_secuencial =(int)$porciones[count($porciones)-1];
                        $cuenta->centro_consumo_nivel =$array[0][$i][2];
                        if (strlen($nume)>1) {
                            $cpadre=Centro_Consumo::NivelPadre(substr($padre, 0, -1))->first();
                            $cuenta->centro_consumo_padre =$cpadre->centro_consumo_id;
                        } else {
                            $cuenta->centro_consumo_padre =null;
                        }
                        $cuenta->centro_consumo_fecha_ingreso = date('Y-m-d h:i:s');
                        $cuenta->empresa_id = Auth::user()->empresa_id;
                        $cuenta->centro_consumo_estado = 1;
                        $cuenta->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de Centro Consumo -> '.$cuenta->centro_consumo_nombre, '0', 'Numero de la cuenta registrada es -> '.$cuenta->centro_consumo_numero);
                    }
                }
            }
       
        return redirect('plancentroConsumo')->with('success','Datos guardados exitosamente');
       
    }
    public function guardarCentroC2(Request $request, $id){
        //return $id;
        try{            
            DB::beginTransaction();
            $centroCon = new Centro_Consumo();
            $centroCon->centro_consumo_nombre = $request->centro_consumo_nombre;
            $centroCon->centro_consumo_descripcion = "";
            $centroCon->centro_consumo_fecha_ingreso = date('Y-m-d h:i:s');

            $centroCon->centro_consumo_secuencial = $request->cuenta_numero;
            $centroCon->centro_consumo_nivel = $request->cuenta_nivel;
            $centroCon->centro_consumo_numero = $request->cuenta_padre.'.'.$request->cuenta_numero;
            $centroCon->centro_consumo_padre = $id;
            $centroCon->empresa_id = Auth::user()->empresa_id;
            $centroCon->centro_consumo_estado = 1;
            if ($request->get('centro_consumo_descripcion')){
                $centroCon->centro_consumo_descripcion = $request->get('centro_consumo_descripcion');
            }
            if ($centroCon->centro_consumo_nivel == "4"){
                $centroCon->sustento_id = $request->get('idSustento');    
            }
            $centroCon->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de centro consumo -> '.$request->get('centro_consumo_nombre').' de fecha -> '.$request->get('centro_consumo_fecha_ingreso'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('plancentroConsumo')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('plancentroConsumo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function agregarCentro($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $cuentaPadre=Centro_Consumo::findOrFail($id);
            $secuencial = 1;
            $secuencialAux=Centro_Consumo::nivel($id)->max('centro_consumo_secuencial');
            $sustentosTributario25 = Sustento_Tributario::Sustentos()->get();
            
            if($secuencialAux){
                $secuencial=$secuencialAux+1;
            }
            //return $cuentaPadre;
            if($cuentaPadre){
                return view('admin.compras.centroConsumoplan.agregarCuentas',[
                    'cuentaPadre'=>$cuentaPadre,
                    'PE'=>Punto_Emision::puntos()->get(),
                    'sustentosTributario25'=>$sustentosTributario25,
                    'secuencial'=>$secuencial,
                    'tipoPermiso'=>$tipoPermiso,
                    'gruposPermiso'=>$gruposPermiso,
                    'permisosAdmin'=>$permisosAdmin
                ]);
            }else{
                return redirect('/denegado');
            }
         }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //return $request;
        try{            
            DB::beginTransaction();
            $centroCon = new Centro_Consumo();
            $centroCon->centro_consumo_nombre = $request->get('centro_consumo_nombre');
            $centroCon->centro_consumo_fecha_ingreso = date('Y-m-d h:i:s');
            $centroCon->sustento_id = null;
            $centroCon->empresa_id = Auth::user()->empresa_id;
            $centroCon->centro_consumo_estado = 1;
            if ($request->get('centro_consumo_descripcion')){
                $centroCon->centro_consumo_descripcion = $request->get('centro_consumo_descripcion');
            }
            $centroCon->centro_consumo_nivel = $request->cuenta_nivel;
            $centroCon->centro_consumo_secuencial = $request->cuenta_numero;
            $centroCon->centro_consumo_padre = null;
            $centroCon->centro_consumo_numero = $request->cuenta_numero;

            $centroCon->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de centro consumo -> '.$request->get('centro_consumo_nombre').' de fecha -> '.$request->get('centro_consumo_fecha_ingreso'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('plancentroConsumo')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('plancentroConsumo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $centroCon = Centro_Consumo::findOrFail($id);
            if($centroCon){
                return view('admin.compras.centroConsumoplan.ver',['centroCon'=>$centroCon, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
            $centroCon = Centro_Consumo::findOrFail($id);
            $sustentosTributario25 = Sustento_Tributario::Sustentos()->get();
            if($centroCon){
                return view('admin.compras.centroConsumoplan.editar', ['sustentosTributario25'=>$sustentosTributario25,'centroCon'=>$centroCon, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
           
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
            $centroCon = Centro_Consumo::findOrFail($id);
            $centroCon->centro_consumo_nombre = $request->get('centro_consumo_nombre');
            $centroCon->centro_consumo_numero = $request->get('cuenta_padre').'.'.$request->get('cuenta_numero');
            $centroCon->centro_consumo_secuencial = $request->get('cuenta_numero');  
            if ($request->get('centro_consumo_descripcion')){
                $centroCon->centro_consumo_descripcion = $request->get('centro_consumo_descripcion');
            }  
            if ($centroCon->centro_consumo_nivel == "4"){
                $centroCon->sustento_id = $request->get('idSustento');    
            }
            if ($request->get('centro_consumo_estado') == "on"){
                $centroCon->centro_consumo_estado = 1;
            }else{
                $centroCon->centro_consumo_estado = 0;
            }
            $centroCon->save();       
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de centro consumo -> '.$request->get('centro_consumo_nombre').' de fecha -> '.$request->get('centro_consumo_fecha_ingreso'),'0','');
            /*Fin de registro de auditoria */   
            DB::commit();
            return redirect('plancentroConsumo')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('plancentroConsumo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $centroCon = Centro_Consumo::findOrFail($id);
            $centroCon->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de centro consumo -> '.$centroCon->centro_consumo_nombre.' con fecha -> '.$centroCon->centro_consumo_fecha_ingreso,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('plancentroConsumo')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('plancentroConsumo')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $centroCon = Centro_Consumo::centroConsumo($id)->first();
            if($centroCon){
                return view('admin.compras.centroConsumoplan.eliminar',['centroCon'=>$centroCon, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
