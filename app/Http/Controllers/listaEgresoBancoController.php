<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cheque;
use App\Models\Detalle_Diario;
use App\Models\Egreso_Banco;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class listaEgresoBancoController extends Controller
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
            return view('admin.bancos.listaEgresoBanco.index',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $egresoBancos=Egreso_Banco::reporteEgresoBancos()
            ->where('egreso_fecha','>=',$request->get('idDesde'))
            ->where('egreso_fecha','<=',$request->get('idHasta'))        
            ->where('egreso_estado','=','1')->get();
            return view('admin.bancos.listaEgresobanco.index',['fechaI'=>$request->get('idDesde'),'fechaF'=>$request->get('idHasta'),'egresoBancos'=>$egresoBancos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $egresoBanco=Egreso_Banco::egresoBanco($id)->first();
            if($egresoBanco){
                return view('admin.bancos.listaEgresoBanco.ver',['egresoBanco'=>$egresoBanco, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
    public function destroy(Request $request,$id)
    {       
       try{
          DB::beginTransaction();
            $egresoBanco = Egreso_Banco::EgresoBanco($id)->first();
            $general = new generalController();
            $cierre = $general->cierre($egresoBanco->egreso_fecha,Rango_Documento::rango($egresoBanco->rango_id)->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('listaEgresoBanco')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $egresoBancoAux = $egresoBanco;
            if(isset($egresoBancoAux->transferencia_id)){
                $transferencia=$egresoBanco->transferencia;
            }
            if(isset($egresoBancoAux->cheque_id)){
                $cheque=$egresoBanco->cheque;
            }
            $egresoBanco->delete();
            foreach($egresoBancoAux->diario->detalles as $detalle_diario1){                       
                $detallesaux = $detalle_diario1;                   
                $detalle_diario1->delete();
                $general->registrarAuditoria('Eliminacion de detalles numero: -> '.$detallesaux, $id, 'Con id de diario-> '.$detallesaux->diario_id.'Comentario -> '.$detallesaux->detalle_comentario);
            }         
            $diariouax = $egresoBancoAux->diario;
            $diarioAux2 = $diariouax;
            $diariouax->delete();                        
            $general->registrarAuditoria('Eliminacion de Egreso de Banco: -> '.$egresoBancoAux->egreso_id, $id, 'Con el diario-> '.$diariouax->diario_codigo.'Comentario -> '.$diariouax->diario_comentario);
                                  
            $general->registrarAuditoria('Eliminacion de Diario numero: -> '.$diarioAux2->diario_codigo, $id, 'Con id de diario-> '.$diarioAux2->diario_tipo_documento.'Comentario -> '.$diarioAux2->diario_comentario);
            if (isset($cheque)){                       
                $chequeaux =  $cheque;  
                if($request->get('anularChequeID') == 'no'){
                    $cheque->delete();
                    $general->registrarAuditoria('Eliminacion de Cheque numero: -> '.$chequeaux->cheque_numero, $id, 'Con cheque  id -> '.$chequeaux->cheque_id.'Con valor de -> '.$chequeaux->cheque_valor);
                }else{
                    $cheque->cheque_estado = '2';
                    $cheque->update();
                    $general->registrarAuditoria('Anulacion de Cheque numero: -> '.$chequeaux->cheque_numero, $id, 'Con cheque  id -> '.$chequeaux->cheque_numero.'Con valor de -> '.$chequeaux->cheque_valor);
                }                      
                
            }
            if (isset($transferencia)){                       
                $transferenciaaux =  $transferencia;                           
                $transferencia->delete();
                $general->registrarAuditoria('Eliminacion de Transferncia', $id, $transferenciaaux->transferencia_descripcion.'Con valor de -> '.$transferenciaaux->transferencia_valor);
            }
            DB::commit();
            return redirect('listaEgresoBanco')->with('success','Datos Eliminados exitosamente');            
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaEgresoBanco')->with('error',$ex);
        }        
    }
    public function anulacion(Request $request)
    {       
      try{
            DB::beginTransaction();
            $egresoBanco = Egreso_Banco::EgresoBanco($request->get('idegreso'))->first();
            $general = new generalController();
            $cierre = $general->cierre($egresoBanco->egreso_fecha,Rango_Documento::rango($egresoBanco->rango_id)->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('listaEgresoBanco')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $egresoBancoAux = $egresoBanco;
            if(isset($egresoBancoAux->transferencia_id)){
                $transferencia=$egresoBanco->transferencia;
            }
            if(isset($egresoBancoAux->cheque_id)){
                $cheque=$egresoBanco->cheque;
            }
            $egresoBanco->delete();
            foreach($egresoBancoAux->diario->detalles as $detalle_diario1){                       
                $detallesaux = $detalle_diario1;                   
                $detalle_diario1->delete();
                $general->registrarAuditoria('Eliminacion de detalles numero: -> '.$detallesaux,$request->get('idegreso'), 'Con id de diario-> '.$detallesaux->diario_id.'Comentario -> '.$detallesaux->detalle_comentario);
            }         
            $diariouax = $egresoBancoAux->diario;
            $diarioAux2 = $diariouax;
            $diariouax->delete();                        
            $general->registrarAuditoria('Eliminacion de Egreso de Banco: -> '.$egresoBancoAux->egreso_id, $request->get('idegreso'), 'Con el diario-> '.$diariouax->diario_codigo.'Comentario -> '.$diariouax->diario_comentario);
                                  
            $general->registrarAuditoria('Eliminacion de Diario numero: -> '.$diarioAux2->diario_codigo, $request->get('idegreso'), 'Con id de diario-> '.$diarioAux2->diario_tipo_documento.'Comentario -> '.$diarioAux2->diario_comentario);
            if (isset($cheque)){                       
                $cheque->cheque_estado = '2';
                $cheque->update();
                $general->registrarAuditoria('Anulacion de Cheque numero: -> '.$cheque->cheque_numero, $request->get('idegreso'), 'Con cheque  id -> '.$cheque->cheque_numero.'Con valor de -> '.$cheque->cheque_valor);                   
            }
            if (isset($transferencia)){                       
                $transferenciaaux =  $transferencia;                           
                $transferencia->delete();
                $general->registrarAuditoria('Eliminacion de Transferncia', $request->get('idegreso'), $transferenciaaux->transferencia_descripcion.'Con valor de -> '.$transferenciaaux->transferencia_valor);
            }
            DB::commit();
            return redirect('listaEgresoBanco')->with('success','Datos Eliminados exitosamente');            
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaEgresoBanco')->with('error',$ex);
        }   
    }
    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $egresoBanco=Egreso_Banco::egresoBanco($id)->first();
            if($egresoBanco){
                return view('admin.bancos.listaEgresoBanco.eliminar',['egresoBanco'=>$egresoBanco,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function anular($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $egresoBanco=Egreso_Banco::egresoBanco($id)->first();
            if($egresoBanco){
                return view('admin.bancos.listaEgresoBanco.anular',['egresoBanco'=>$egresoBanco,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
