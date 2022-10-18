<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cheque;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Quincena;
use App\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listarquincenaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $quincena=null;
            $estados=Quincena::Estados()->select('quincena_estado')->distinct()->get();
            $empleado=Quincena::EmpleadoQuincena()->orderBy('empleado_nombre','asc')->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            $sucursales=Quincena::EmpleadoQuincena()->orderBy('sucursal_nombre','asc')->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            return view('admin.recursosHumanos.quincena.view',['sucursalid'=>null,'fecha_desde'=>null,'fecha_hasta'=>null,'fecha_todo'=>null,'nombre_empleado'=>null,'estadoactual'=>null,'sucursales'=>$sucursales,'estados'=>$estados,'empleado'=>$empleado,'quincena'=>$quincena,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        
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
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $quincena=null;
            $esta_quin=Quincena::Estados()->select('quincena_estado')->distinct()->get();
            $empleado=Quincena::EmpleadoQuincena()->orderBy('empleado_nombre','asc')->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->get();
            $sucursales=Quincena::EmpleadoQuincena()->orderBy('sucursal_nombre','asc')->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            $quincena=null;   
            $count=1;   
            $quincen=Quincena::QuincenasDiferente($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_empleado'),$request->get('estados'),$request->get('sucursal'))->get();
            foreach($quincen as $quince){
                $quincena[$count]["id"]=$quince->quincena_id;
                $quincena[$count]["pago"]=$quince->quincena_tipo;
                $quincena[$count]["numero"]=$quince->quincena_numero;
                $quincena[$count]["fecha"]=$quince->quincena_fecha;
                $quincena[$count]["valor"]=$quince->quincena_valor;
                $quincena[$count]["descripcion"]=$quince->quincena_descripcion;
                $quincena[$count]["estado"]=$quince->quincena_estado;
                $quincena[$count]["empleado"]=$quince->empleado->empleado_nombre;
                $quincena[$count]["rol"]=$quince->cabecera_rol_id;
                $quincena[$count]["rolcm"]=$quince->cabecera_rol_cm_id;
                $quincena[$count]["idcheque"]=0;
                $quincena[$count]["consolidado"]=0;
                if($quince->quincena_tipo=="Cheque" ){
                    foreach($quince->diario->detalles as $i){
                        if($i->cheque){
                            $quincena[$count]["idcheque"]=$i->cheque->cheque_id;   
                        }
                    }
                }
                $validacion=Quincena::Validacion($quince->diario_id)->get();
                if(count($validacion)>1){
                    $quincena[$count]["consolidado"]=$quince->quincena_id;
                }
                $count++;
            }
                        
            
            return view('admin.recursosHumanos.quincena.view',['sucursales'=>$sucursales,'sucursalid'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_empleado'=>$request->get('nombre_empleado'),'estadoactual'=>$request->get('estados'),'estados'=>$esta_quin,'empleado'=>$empleado,'quincena'=>$quincena,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            
            $quincena=Quincena::findOrFail($id);
           
            $transferencia=null;
            $cheque=null;
            foreach ($quincena->diario->detalles as $i) {
                   
                if (isset($i->cheque_id)) {
                    $cheque=Cheque::findOrFail($i->cheque_id);
                }
                if (isset($i->transferencia_id)) {
                    $transferencia=Transferencia::findOrFail($i->transferencia_id);
                }
            }
    
            return view('admin.recursosHumanos.quincena.ver',['transferencia'=>$transferencia,'cheque'=>$cheque,'quincena'=>$quincena,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('lquincena')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function delete($id)
    {        
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $transferencia=null;
            $cheque=null;
            $quincena=Quincena::Quincena($id)->get()->first();
       
            $quincenaaux=Quincena::findOrFail($id);
            if(isset($quincenaaux->rolcm)){
                return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un rol');
            }
            if(count($quincenaaux->decuento)>0){
                return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un rol');
            }
            
           
            $transferencia=null;
            $cheque=null;
            foreach ($quincena->diario->detalles as $i) {
                if (isset($i->cheque_id)) {
                    $cheque=Cheque::findOrFail($i->cheque_id);
                }
                if (isset($i->transferencia_id)) {
                    $transferencia=Transferencia::findOrFail($i->transferencia_id);
                }
            }
        
        
        
            return view('admin.recursosHumanos.quincena.eliminar', ['transferencia'=>$transferencia,'cheque'=>$cheque,'quincena'=>$quincena,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        
            

        }catch(\Exception $ex){
            return redirect('lquincena')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function deleteconsolidada($id)
    {        
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $transferencia=null;
            
            $quincena=Quincena::findOrFail($id);
            $quincenas=Quincena::Validacion($quincena->diario_id)->get();
            foreach($quincenas as $detalle){
                if(isset($detalle->rolcm)){
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un rol');
                }
                if(isset($detalle->rol)){
                    return redirect('lquincena')->with('error2', 'No puede realizar la operacion por que pertenece a un rol');
                }
            }
            foreach ($quincena->diario->detalles as $i) {
                if (isset($i->transferencia_id)) {
                    $transferencia=Transferencia::findOrFail($i->transferencia_id);
                }
            }
            return view('admin.recursosHumanos.quincenaConsolidada.eliminar', ['transferencia'=>$transferencia,'quincenas'=>$quincenas,'quincena'=>$quincena,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        
            

        }catch(\Exception $ex){
            return redirect('lquincena')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function anular($id)
    {        
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
           
            $quincena=Quincena::Quincena($id)->get()->first();
            $transferencia=null;
            $cheque=null;
                foreach ($quincena->diario->detalles as $i) {
                   
                    if (isset($i->cheque_id)) {
                        $cheque=Cheque::findOrFail($i->cheque_id);
                    }
                    if (isset($i->transferencia_id)) {
                        $transferencia=Transferencia::findOrFail($i->transferencia_id);
                    }
                }
         
            
           
            return view('admin.recursosHumanos.quincena.anular',['transferencia'=>$transferencia,'cheque'=>$cheque,'quincena'=>$quincena,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('lquincena')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $quincena=Quincena::findOrFail($id);
            $diario=Diario::findOrFail($quincena->diario_id);
            $general = new generalController();
            $cierre = $general->cierre($quincena->quincena_fecha,$diario->sucursal_id);          
            if($cierre){
                return redirect('lquincena')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $diariocoun=Quincena::Validacion($quincena->diario_id)->get();
            if (count($diariocoun)==1) {
                foreach ($quincena->diario->detalles as $i) {
                
                    if (isset($i->cheque_id)) {
                       
                        $detalle=Detalle_Diario::findOrFail($i->detalle_id);
                      
                        $chequeAux=Cheque::findOrFail($i->cheque_id);
                       
                        $detalle->cheque_id=null;
                        $detalle->save();
                        
                        $chequeAux->delete();
                        $general->registrarAuditoria('Eliminacion de Cheque numero: -> '.$chequeAux->cheque_numero, $id, 'Con quincena  id -> '.$id.'Con valor de -> '.$chequeAux->cheque_valor);         
                    }          
                    if (isset($i->transferencia_id)) {
                        $detalle=Detalle_Diario::findOrFail($i->detalle_id);
                        
                        $transferenciaAux=Transferencia::findOrFail($i->transferencia_id);
    
                        $detalle->transferencia_id=null;
                        $detalle->save();
    
                        $transferenciaAux->delete();
                        $general->registrarAuditoria('Eliminacion de Transferencia numero: -> '.$transferenciaAux->transferencia_numero, $id, 'Con quincena  id -> '.$id.'Con valor de -> '.$transferenciaAux->transferencia_valor);  
                    }
                   
    
                    $i->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion del detalle diario tipo documento numero: -> '.$i->detalle_tipo_documento.'con empleado '.$quincena->empleado->emepleado_nombre, $id,'Con quincena  id -> '.$i.'con codigo de diario'.$quincena->diario->diario_codigo);
                   
                }    
                $quincena->diario_id=null;
                $quincena->save();
    
                $diario->delete();
                $general->registrarAuditoria('Eliminacion de Dario: -> '.$id.'con empleado '.$quincena->empleado->emepleado_nombre, $id, 'Con quincena  id -> '.$id);     
              
                $quincena->delete();
                $general->registrarAuditoria('Eliminacion de la quincena: -> '.$id.'con empleado '.$quincena->empleado->emepleado_nombre, $id, 'Con quincena  id -> '.$id);     
                $quincena->diario->delete();
                $general->registrarAuditoria('Eliminacion del diario tipo documento numero: -> '.$quincena->diario->diario_codigo.'con empleado '.$quincena->empleado->emepleado_nombre, $id, 'Con quincena  id -> '.$id);  
                DB::commit();
                return redirect('lquincena')->with('success','Datos Eliminados exitosamente');
            } 
            else{
                    $detalle=Detalle_Diario::Empleadodiario($quincena->diario_id,$quincena->empleado_id)->first();
                  
                    $detalleaux=Detalle_Diario::findOrFail($detalle->detalle_id);
                    $detalleaux->delete();

                    foreach ($quincena->diario->detalles as $i) {
                        if (isset($i->transferencia_id)) {
                           
                            $transferenciaAux=Transferencia::findOrFail($i->transferencia_id);
                            $transferenciaAux->transferencia_valor=$transferenciaAux->transferencia_valor-$detalle->detalle_debe;
                            $transferenciaAux->save();
                            $detalleaux=Detalle_Diario::findOrFail($i->detalle_id);
                            $detalleaux->detalle_haber=$detalleaux->detalle_haber-$detalle->detalle_debe;
                            $detalleaux->save();
                           
                        }
                    }
                    $general->registrarAuditoria('Eliminacion de Transferencia numero: -> '.$transferenciaAux->transferencia_numero, $id, 'Con quincena  id -> '.$id.'Con valor de -> '.$transferenciaAux->transferencia_valor); 
                    $quincena->diario_id=null;              
                    $quincena->save();
                    $quincena->delete();
                    $general->registrarAuditoria('Eliminacion de la quincena: -> '.$id.'con empleado '.$quincena->empleado->emepleado_nombre, $id, 'Con quincena  id -> '.$id); 
                    $url = $general->pdfDiarioEgreso($diario);       
                    DB::commit();
                    return redirect('lquincena')->with('success','Datos Eliminados exitosamente')->with('diario',$url);
            }  
        }catch(\Exception $ex){
            return redirect('lquincena')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function anulacion(Request $request)
    {
        try{
            DB::beginTransaction();
            $quincena=Quincena::findOrFail($request->get('idquincena'));
            $diario=Diario::findOrFail($quincena->diario_id);
            $general = new generalController();
            $cierre = $general->cierre($quincena->quincena_fecha,$diario->sucursal_id);          
            if($cierre){
                return redirect('lquincena')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach ($quincena->diario->detalles as $i) {
                
                if (isset($i->cheque_id)) {
                   
                    $detalle=Detalle_Diario::findOrFail($i->detalle_id);
                  
                    $chequeAux=Cheque::findOrFail($i->cheque_id);
                   
                    $detalle->cheque_id=null;
                    $detalle->save();
                    $chequeAux->cheque_estado='2';
                    $chequeAux->save();
                    $general->registrarAuditoria('Anulacion de Cheque numero: -> '.$chequeAux->cheque_numero, $request->get('idquincena'), 'Con quincena  id -> '.$request->get('idquincena').'Con valor de -> '.$chequeAux->cheque_valor);         
                }          
              
               

                $i->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion del detalle diario tipo documento numero: -> '.$i->detalle_tipo_documento.'con empleado '.$quincena->empleado->emepleado_nombre, $request->get('idquincena'),'Con quincena  id -> '.$i.'con codigo de diario'.$quincena->diario->diario_codigo);
               
            }    
            $quincena->diario_id=null;
            $quincena->save();

            $diario->delete();
            $general->registrarAuditoria('Eliminacion de Dario: -> '.$request->get('idquincena').'con empleado '.$quincena->empleado->emepleado_nombre, $request->get('idquincena'), 'Con quincena  id -> '.$request->get('idquincena'));     
          
            $quincena->delete();
            $general->registrarAuditoria('Eliminacion de la quincena: -> '.$request->get('idquincena').'con empleado '.$quincena->empleado->emepleado_nombre, $request->get('idquincena'), 'Con quincena  id -> '.$request->get('idquincena'));     
            $quincena->diario->delete();
            $general->registrarAuditoria('Eliminacion del diario tipo documento numero: -> '.$quincena->diario->diario_codigo.'con empleado '.$quincena->empleado->emepleado_nombre, $request->get('idquincena'), 'Con quincena  id -> '.$request->get('idquincena'));  
        
           
            DB::commit();
            return redirect('lquincena')->with('success','Datos Eliminados exitosamente');
        }catch(\Exception $ex){
            return redirect('lquincena')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimir($id)
    {
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $quincena=Quincena::Quincena($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfDiario($quincena->diario);
            return redirect('lquincena')->with('diario',$url);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    
   
}
