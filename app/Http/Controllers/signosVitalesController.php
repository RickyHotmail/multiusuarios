<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\Orden_Atencion;
use Illuminate\Http\Request;
use App\Models\Punto_Emision;
use App\Models\Signos_Vitales;
use App\Models\Paciente;
use App\Models\Proveedor;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Signos_Vitales_Especialidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class signosVitalesController extends Controller
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
            $ordenes=Orden_Atencion::SignosVitales()->get();           
            return view('admin.citasMedicas.signosVitales.index',['ordenes'=>$ordenes,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function nuevoSigno($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();     
            $orden = Orden_Atencion::findOrFail($id); 
            $signosvitales = Signos_Vitales_Especialidad::SignoVital($orden->especialidad_id)->get();
            return view('admin.citasMedicas.signosVitales.nuevoSignosV',['signosvitales'=>$signosvitales,'ordenAtencion'=>$orden, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            /************Signos Vitales**************/
           $valor=$request->get('valor');
           $nombre=$request->get('nombre');
           $tipo=$request->get('tipo');
           $medida=$request->get('medida');
           $ide=$request->get('ide');

            $expediente = new Expediente();
            $expediente->expediente_observacion = " ";
            $expediente->expediente_proxima = date("Y-m-d H:i:s");
            $expediente->expediente_estado = 1;
            $expediente->orden_id = $request->get('idorden');
            $expediente->save();  
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Expediente -> '.$request->get('signo_id').' de la orden de atencion -> '.$request->get('orden_id'),'0','');
           
            for ($i = 0; $i < count($valor); ++$i) {
                $signoVital = new Signos_Vitales();
                $signoVital->signo_nombre  =$nombre[$i];  
                $signoVital->signo_valor  =$valor[$i];
                $signoVital->signo_medida  =$medida[$i];
                $signoVital->signo_tipo  =$tipo[$i];
                $signoVital->signo_estado  = 1;             
                $signoVital->expediente_id=$expediente->expediente_id;
                $signoVital->save();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de signos vitales -> '.$signoVital->signo_nombre .' con valor -> '.$signoVital->signo_valor,'0','');   
            }
            

            /************Cambio de estado*********/
            $ordenAtencion = Orden_Atencion::findOrFail($request->get('idorden'));  
            $ordenAtencion->orden_estado ="3";
            $ordenAtencion->save();

            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de Orden de Atencion a estado 3 de la orden de atencion -> '.$ordenAtencion->orden_numero,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('signosVitales')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('signosVitales')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $orden = Orden_Atencion::findOrFail($id); 
            $signosvitales = Signos_Vitales_Especialidad::SignoVital($orden->especialidad_id)->get();

            //return $signosvitales;
            return view('admin.citasMedicas.signosVitales.editar',['signosvitales'=>$signosvitales,'orden'=>$orden, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function editarSignosVitales($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();     
            $orden = Orden_Atencion::findOrFail($id);
            //$signosvitales = Signos_Vitales_Especialidad::SignoVital($orden->especialidad_id)->get();
            return view('admin.citasMedicas.signosVitales.editar',['orden'=>$orden, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function actualizarSignosOrdenAtencion(Request $request)
    {
        
        try{
            DB::beginTransaction();

            $ordenIess=0;

            for($i=0; $i<count($request->signo_id); $i++){
                $signoVital = Signos_Vitales::findOrFail($request->signo_id[$i]);

                if($signoVital){
                    $signoVital->signo_valor=$request->signo_valor[$i];
                    $signoVital->save();
                }
                else{
                    $signoVital=new Signos_Vitales();
                    $signoVital->signo_estado=1;
                    $signoVital->expediente_id=$request->expediente[$i];
                    $signoVital->signo_tipo=$request->tipo[$i];
                    $signoVital->signo_medida=$request->signo_medida[$i];
                    $signoVital->signo_nombre=$request->signo_nombre[$i];
                    $signoVital->signo_valor=$request->signo_valor[$i];
                    $signoVital->save();
                }

                $signoVital->detalleExpediente;
            }

            if($signoVital->detalleExpediente){
                if($signoVital->detalleExpediente->ordenAtencion)
                    $ordenIess=$signoVital->detalleExpediente->ordenAtencion->orden_iess;
            }


            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de signos vitales con expediente de la orden de atencion -> '.$request->orden_id,'0','');
            /*Fin de registro de auditoria */
            DB::commit();

            if($ordenIess)
                return redirect('ordenAtencionIess')->with('success','Datos guardados exitosamente');
            else
                return redirect('ordenAtencion')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();

            if($ordenIess)
                return redirect('ordenAtencionIess')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
            else
                return redirect('ordenAtencion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');

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
            $signoVital = Signos_Vitales::findOrFail($id);   
            $signoVital->signo_pas = $request->get('signo_pas');
            $signoVital->signo_pad = $request->get('signo_pad');
            $signoVital->signo_fc = $request->get('signo_fc');
            $signoVital->signo_fr = $request->get('signo_fr');
            $signoVital->signo_temp = $request->get('signo_temp');
            $signoVital->signo_peso = $request->get('signo_peso');                
            $signoVital->signo_estatura = $request->get('signo_estatura');
            $signoVital->signo_imc = $request->get('signo_imc');
            $signoVital->signo_satO2 = $request->get('signo_satO2');
            $signoVital->signo_cefalico = $request->get('signo_cefalico');
            $signoVital->signo_toraxico = $request->get('signo_toraxico');
            $signoVital->signo_abdominal = $request->get('signo_abdominal');
            $signoVital->signo_inspiracion = $request->get('signo_inspiracion');
            $signoVital->signo_esperacion = $request->get('signo_esperacion'); 
            if ($request->get('idEstado') == "on"){
                $signoVital->signo_estado ="1";
            }else{
                $signoVital->signo_estado ="0";
            }        
            $signoVital->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de signos vitales -> '.$request->get('signo_id').' de la orden de atencion -> '.$request->get('orden_id'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('signosVitales')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('signosVitales')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        //
    }
}
