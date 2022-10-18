<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Documento_Anulado;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Retencion_Compra;
use App\Models\Transaccion_Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class anularRetencionesController extends Controller
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
            $rangoDocumentos = Rango_Documento::PuntoRangoNombre('Comprobante de Retención')->get();
            return view('admin.compras.anularRetencion.index',['rangoDocumentos'=>$rangoDocumentos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            DB::beginTransaction();
            $general = new generalController();         
            
            $serie = Rango_Documento::Rango($request->get('idRango'))->first();
            $cierre = $general->cierre($request->get('idFechaRet'),$serie->puntoEmision->sucursal->sucursal_id);
            if ($cierre) {
                return redirect('anularRetencion')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $serieaux = $serie->puntoEmision->sucursal->sucursal_codigo.$serie->puntoEmision->punto_serie;
            $retencionCompra = new Retencion_Compra();
            $retencionCompra->retencion_fecha = $request->get('idFechaRet');
            $retencionCompra->retencion_numero = $serieaux.substr(str_repeat(0, 9).$request->get('idNumero'), - 9);
            $retencionCompra->retencion_serie = $serieaux;
            $retencionCompra->retencion_secuencial = $request->get('idNumero');
            if($request->get('idEmision') == "ELECTRONICA"){
                $retencionCompra->retencion_emision = "ELECTRONICA";
            }
            if($request->get('idEmision') == "FISICA"){
                $retencionCompra->retencion_emision = "FISICA";
            }            
            $retencionCompra->retencion_ambiente = 'PRODUCCIÓN';
            $retencionCompra->retencion_autorizacion = $request->get('idAutorizacion');            

            $docnull=new Documento_Anulado();
            $docnull->documento_anulado_fecha= $request->get('idFechaAnul');
            $docnull->documento_anulado_motivo= 'Por Anulacion de retencion N°'. $serieaux.substr(str_repeat(0, 9).$request->get('idNumero'), - 9);
            $docnull->documento_anulado_estado= '1';
            $docnull->empresa_id=Auth::user()->empresa_id;
            $docnull->save();
            $retencionCompra->dopcumentoanulado()->associate($docnull);
            $retencionCompra->rango_id = $request->get('idRango');
            $retencionCompra->retencion_estado = 0;
            $retencionCompra->save();
            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Anulacion de Retencion #-> '.$serieaux.substr(str_repeat(0, 9).$request->get('idNumero'), - 9),'0','Con autorizacion # -> '.$request->get('idAutorizacion'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('anularRetencion')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('anularRetencion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');
    }
    public function consultar(Request $request){        
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();            
            $fechaDesde = $request->get('idDesde');
            $fechahasta = $request->get('idHasta');
            $retencionCompras = Retencion_Compra::listadoRetencionesAnuladas()
            ->where('retencion_fecha','>=',$request->get('idDesde'))
            ->where('retencion_fecha','<=',$request->get('idHasta'))        
            ->where('retencion_estado','=','0')->get();
            $rangoDocumentos = Rango_Documento::PuntoRangoNombre('Comprobante de Retención')->get();
            return view('admin.compras.anularRetencion.index',['fechaDesde'=>$fechaDesde, 'fechahasta'=>$fechahasta,'rangoDocumentos'=>$rangoDocumentos, 'retencionCompras'=>$retencionCompras,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $auditoria = new generalController();
            $retencionCompra = Retencion_Compra::findOrFail($id);
            $cierre = $auditoria->cierre($retencionCompra->retencion_fecha,$retencionCompra->rangoDocumento->puntoEmision->sucursal_id);
            if ($cierre) {
                return redirect('anularRetencion')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $retencionComprauax = $retencionCompra;
            foreach($retencionCompra->detalles as $detalle){
                $detalle->delete();
                $auditoria->registrarAuditoria('Eliminacion del detalle de la retencion compra por anulacion-> '.$detalle->detalle_tipo.' con base '.$detalle->detalle_base,$retencionComprauax->retencion_numero,'Con procentaje -> '.$detalle->detalle_porcentaje.' con valor '.$detalle->detalle_valor);
           
            }  
            $retencionCompra->delete();
            $documetoAnul = Documento_Anulado::findOrFail($retencionComprauax->documento_anulado_id);
            $documetoAnul->delete();            
            /*Inicio de registro de auditoria */
           
            $auditoria->registrarAuditoria('Eliminacion de Doumento Anulado -> '.$retencionComprauax->retencion_numero,'0','Con # de Autorizacion -> '.$retencionComprauax->retencion_autorizacion);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('anularRetencion')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('anularRetencion')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }
    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $retencionCompra = Retencion_Compra::Retencion($id)->first();
            if($retencionCompra){
                return view('admin.compras.anularRetencion.eliminar',['retencionCompra'=>$retencionCompra, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
