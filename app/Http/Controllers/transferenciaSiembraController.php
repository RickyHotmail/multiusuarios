<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Piscina;
use App\Models\Siembra;
use App\Models\Transferencia_Siembra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;

class transferenciaSiembraController extends Controller
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
            $Siembras = Transferencia_Siembra::Siembras()->orderBy('piscina.piscina_codigo','asc')->select('piscina.piscina_id','piscina.piscina_codigo')->distinct()->get();
            $Siembras = Transferencia_Siembra::Siembras()->orderBy('transferencia_estado','asc')->select('transferencia_estado')->orderBy('transferencia_estado','asc')->distinct()->get();
            return view('admin.camaronera.transferencia.index',['Siembras'=>$Siembras,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $Siembras = Siembra::Siembras()->get();
            $piscinas = Piscina::PiscinasActiva()->get();
            return view('admin.camaronera.transferencia.nuevo',['siembras'=>$Siembras,'piscinas'=>$piscinas,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $siembraref=Siembra::findOrFail($request->get('idsiembra'));
            $siembraref->siembra_estado='2';
            $siembraref->save();
            
            $Didpiscina=$request->get('Didpiscina');
            $Dsecuencial=$request->get('Dsecuencial');
            $Dcodigo=$request->get('Dcodigo');
            
            $Darea=$request->get('Darea');
            $Dfecha=$request->get('Dfecha');
            $Dvolumen=$request->get('Dvolumen');
            $Dcjuve=$request->get('Dcjuve');
            $Dnumero=$request->get('Dnumero');
            $Dpjuve=$request->get('Dpjuve');
            $Djuve=$request->get('Djuve');
            $Dtrasn=$request->get('Dtrasn');
            $Dljuve=$request->get('Dljuve');
            $Ddensidad=$request->get('Ddensidad');
            $Dscult=$request->get('Dscult');
            $valor=($request->get('CostoN')/$request->get('idSubtotal'));
            for ($i = 1; $i < count($Didpiscina); ++$i) 
            {
                $simbra = new Siembra();
                $simbra->siembra_secuencial = $Dsecuencial[$i]; 
                $simbra->siembra_codigo = $Dcodigo[$i];     
                $simbra->siembra_larvas = $Dnumero[$i];   
                $simbra->siembra_entregas = '1';    
                $simbra->siembra_fecha = $Dfecha[$i]; 
                $simbra->siembra_fecha_costo = $Dfecha[$i];    
                $simbra->siembra_fecha_siembra = $Dfecha[$i]; 
                $simbra->siembra_longitud = $Dljuve[$i]; 
                $simbra->siembra_peso = $Dpjuve[$i]; 
                $simbra->siembra_densidad = $Ddensidad[$i];    
                $simbra->siembra_cultivo =$Dscult[$i]; 
                $simbra->siembra_precio_larva =$valor;   
                $simbra->siembra_estado = '1'; 
                $simbra->siembra_ref_id =$request->get('idsiembra'); 
                $simbra->piscina_id = $Didpiscina[$i];
                $simbra->nauplio_id = $siembraref->nauplio_id; 
                $simbra->laboratorio_id =$siembraref->laboratorio_id; 
                $simbra->siembra_costo_inicial =$valor*$Dnumero[$i]; 
                $simbra->siembra_costo =round($valor*$Dnumero[$i]); 
                $simbra->save();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de Piscina -> '.$simbra->siembra_codigo.' Con numero de larvas -> '.$simbra->siembra_larvas,'0','');

                $piscina=Piscina::findOrFail($Didpiscina[$i]);
                $piscina->piscina_tipo_estado='EN PRODUCCIÓN';
                $piscina->save();
                
                $transferencia = new Transferencia_Siembra();
                $transferencia->transferencia_codigo =  $Dcodigo[$i]; 
                $transferencia->transferencia_area = $Darea[$i];   
                $transferencia->transferencia_fecha = $Dfecha[$i];    
                $transferencia->transferencia_volumen =$Dvolumen[$i];     
                $transferencia->transferencia_cosecha_juvenil = $Dcjuve[$i]; 
                $transferencia->transferencia_numero_juvenil = $Dnumero[$i];     
                $transferencia->transferencia_peso_juvenil = $Dpjuve[$i]; 
                $transferencia->transferencia_juvenil = $Djuve[$i]; 
                $transferencia->transferencia_libras = $Dtrasn[$i];    
                $transferencia->transferencia_longitud = $Dljuve[$i];     
                $transferencia->transferencia_densidad = $Ddensidad[$i];    
                $transferencia->transferencia_cultivo = $Dscult[$i]; 
                $transferencia->transferencia_estado  = 1;
                $transferencia->siembra_id = $request->get('idsiembra');
                $transferencia->siembra_padre_id = $simbra->siembra_id;
                $transferencia->save();
                /*Inicio de registro de auditoria */
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de transferencia con codigo'.$request->get('idCodigo'),'0','');
            }


            
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('transferencia')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('transferencia')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
