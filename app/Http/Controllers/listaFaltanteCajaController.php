<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Faltante_Caja;
use App\Models\Movimiento_Caja;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listaFaltanteCajaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $fechaDesde = $request->get('idDesde');
            $fechahasta = $request->get('idHasta');
            return view('admin.caja.listaFaltanteCaja.index',
            ['fechahasta'=>$fechahasta,'fechaDesde'=>$fechaDesde,'gruposPermiso'=>$gruposPermiso, 
            'PE'=>Punto_Emision::puntos()->get(),
            'permisosAdmin'=>$permisosAdmin]); 
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
        return redirect('/denegado');
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
            $fechaDesde = $request->get('idDesde');
            $fechahasta = $request->get('idHasta');
            $faltanteCajas=Faltante_Caja::faltantes()
            ->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)
            ->where('faltante_fecha','>=',$request->get('idDesde'))
            ->where('faltante_fecha','<=',$request->get('idHasta'))        
            ->where('faltante_estado','=','1')->get();
            return view('admin.caja.listaFaltanteCaja.index',['fechahasta'=>$fechahasta,'fechaDesde'=>$fechaDesde,'faltanteCajas'=>$faltanteCajas,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $faltanteCaja=Faltante_Caja::faltante($id)->first();
            if($faltanteCaja){
                return view('admin.caja.listaFaltanteCaja.ver',['faltanteCaja'=>$faltanteCaja, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
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
        return redirect('/denegado');
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
        return redirect('/denegado');
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
            $faltanteCaja=Faltante_Caja::faltante($id)->first();
            $general = new generalController();
            $cierre = $general->cierre($faltanteCaja->faltante_fecha,Rango_Documento::rango($faltanteCaja->rango_id)->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('listaFaltanteCaja')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $arqueoCaja=Arqueo_Caja::arqueoCajaxid($faltanteCaja->arqueo_id)->first();
            $movimientoCaja=Movimiento_Caja::movimientoCajaxarqueo($faltanteCaja->arqueo_id, $faltanteCaja->diario_id)->first();            
            if($arqueoCaja){ 
                       
                foreach($faltanteCaja->diario->detalles as $detalle_diario1){
                        $detallesaux = $detalle_diario1; 
                        $detalle_diario1->delete();
                        $general1 = new generalController();
                        $general1->registrarAuditoria('Eliminacion de detalles numero: -> '.$detallesaux, $id, 'Con id de diario-> '.$detallesaux->diario_id.'Comentario -> '.$detallesaux->detalle_comentario);
                    } 

                    //elimina movimiento de caja
                    $movimientoCajaux =  $movimientoCaja;                   
                    $movimientoCaja->delete();
                    $general3 = new generalController();
                    $general3->registrarAuditoria('Eliminacion de Movimiento de Caja: -> '.$movimientoCajaux->movimiento_numero, $id, 'Con el diario-> '.$movimientoCajaux->diario->diario_codigo.'Comentario -> '.$movimientoCajaux->diario->diario_comentario);
                       
                    //elimina faltante de caja
                    $faltanteCajaux =  $faltanteCaja;
                    $diariouax = $faltanteCaja->diario;
                    $faltanteCaja->delete();
                    $general3 = new generalController();
                    $general3->registrarAuditoria('Eliminacion de Faltante de Caja: -> '.$faltanteCajaux->faltante_id, $id, 'Con el diario-> '.$diariouax->diario_codigo.'Comentario -> '.$diariouax->diario_comentario);
                    
                     
                    //elimina diario
                    $diarioAux2 = $diariouax;   
                    //quitar referencia diario_id al falntante de caja       
                    $diariouax->delete();
                    $general2 = new generalController();
                    $general2->registrarAuditoria('Eliminacion de Diario numero: -> '.$diarioAux2->diario_codigo, $id, 'Con id de diario-> '.$diarioAux2->diario_tipo_documento.'Comentario -> '.$diarioAux2->diario_comentario);
                    DB::commit();
                    return redirect('listaFaltanteCaja')->with('success','Datos Eliminados exitosamente');
            }else{
                return redirect('listaFaltanteCaja')->with('error','El registro no pudo ser borrado, La caja actual se encuentra cerrada');
            }
            
         }catch(\Exception $ex){
              DB::rollBack();
            return redirect('listaFaltanteCaja')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }
    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $faltanteCaja=Faltante_Caja::faltante($id)->first();
        
            if($faltanteCaja){
                return view('admin.caja.listaFaltanteCaja.eliminar',['faltanteCaja'=>$faltanteCaja,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }
}
