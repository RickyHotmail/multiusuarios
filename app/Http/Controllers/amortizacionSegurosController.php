<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Amortizacion_Seguros;
use App\Models\Cuenta;
use App\Models\Detalle_Amortizacion;
use App\Models\Detalle_Seguro;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\Models\Transaccion_Compra;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class amortizacionSegurosController extends Controller
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
            $estados=Amortizacion_Seguros::segurosdistinc()->orderBy('amortizacion_estado')->select('amortizacion_estado')->distinct()->get();        
            return view('admin.bancos.seguros.index',['estados'=>$estados,'cuentas'=>Cuenta::CuentasMovimiento()->get(),'proveedores'=>Proveedor::proveedores()->get(),'sucursales'=>Sucursal::sucursales()->get(), 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscar(Request $request)
    {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $seguros=Amortizacion_Seguros::segurosucursal($request->get('idsucursal'),$request->get('idestado'))->get();
            $estados=Amortizacion_Seguros::segurosdistinc()->orderBy('amortizacion_estado')->select('amortizacion_estado')->distinct()->get();
            return view('admin.bancos.seguros.index',['estadoselect'=>$request->get('idestado'),'estados'=>$estados,'seguros'=>$seguros,'cuentas'=>Cuenta::CuentasMovimiento()->get(),'proveedores'=>Proveedor::proveedores()->get(),'sucursales'=>Sucursal::Sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);

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
    public function validarfecha($fecha){
        $valores = explode('/', $fecha);
        if(count($valores) == 3 && checkdate($valores[1], $valores[0], $valores[2])){
            return true;
        }
        return false;
    }
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $general = new generalController();
            $cierre = $general->cierre($request->get('idFecha'),$request->get('idSucursal'));          
            if($cierre){
                return redirect('amortizacion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $trasaccion=Transaccion_Compra::findOrFail($request->get('idFactura'));
            $seguro = new Amortizacion_Seguros();
            $seguro->amortizacion_fecha = $request->get('idFecha');
            $seguro->amortizacion_periodo = $request->get('periodo');
            $seguro->amortizacion_total = $request->get('idValor');
            $seguro->amortizacion_observacion = $request->get('idDescripcion');
            $seguro->amortizacion_pago_total = 0;
            $seguro->transaccion_id = $request->get('idFactura');
            $seguro->cuenta_debe = $request->get('idCuenta');
            $seguro->sucursal_id = $request->get('idSucursal');
            $seguro->amortizacion_estado = 1;
            $seguro->save();
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Amortizacion se seguro -> Con Monto '.$request->get('idValor').' Con Factura Compra '.$trasaccion->transaccion_numero,'0','Con Periodo Amortizacion -> '.$request->get('periodo'));
           
            $dia=DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('d');
            $mes=DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
            $anio=DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
            for ($i = 0; $i < $request->get('periodo'); ++$i) {
                    $mes++;
                    $detalle=new Detalle_Amortizacion();
                    if($mes==13){
                        $mes=1;
                        $anio=$anio+1;
                    }
                    $fecha=$dia.'/'.$mes.'/'.$anio;
                    
                if($this->validarfecha($fecha)==false){
                    $L = new DateTime( $anio.'-'.$mes.'-01' ); 
                    $day=DateTime::createFromFormat('Y-m-d',$L->format( 'Y-m-t' ))->format('d');
                    $fecha=$day.'/'.$mes.'/'.$anio;
                }
                

                $fecha=(DateTime::createFromFormat('d/m/Y', $fecha)->format('Y-m-d'));
                $detalle->detalle_fecha=$fecha;
                $detalle->detalle_mes=DateTime::createFromFormat('Y-m-d', $detalle->detalle_fecha)->format('m');
                $detalle->detalle_anio=DateTime::createFromFormat('Y-m-d',$detalle->detalle_fecha)->format('Y');
                $detalle->detalle_valor=round($request->get('idValor')/$request->get('periodo'),2);
                $detalle->detalle_estado=1;
               
                $detalle->seguro()->associate($seguro); 
                $detalle->save(); 
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Registro de Detalle de  Amortizacion se seguro -> Con Monto '.round($request->get('idValor')/$request->get('periodo'),2).' Con Factura Compra '.$trasaccion->transaccion_numero,'0','Con fecha -> '.$fecha);
                          
            }

           
          
           
            DB::commit();
            return redirect('amortizacion')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('amortizacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $seguro=Amortizacion_Seguros::findOrFail($id);  
            if($seguro){
                return view('admin.bancos.seguros.ver',['seguro'=>$seguro, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('amortizacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $seguro=Amortizacion_Seguros::findOrFail($id);  
            if($seguro){
                return view('admin.bancos.seguros.eliminar',['seguro'=>$seguro, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('amortizacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
       
    }
    public function editar($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $seguro=Amortizacion_Seguros::findOrFail($id);  
            if($seguro){
                return view('admin.bancos.seguros.editar',['cuentas'=>Cuenta::CuentasMovimiento()->get(),'seguro'=>$seguro, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('amortizacion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{
            DB::beginTransaction();
           
            $seguro = Amortizacion_Seguros::findOrFail($id);
            $general = new generalController();
            $cierre = $general->cierre($seguro->amortizacion_fecha,$seguro->sucursal_id);          
            if($cierre){
                return redirect('amortizacion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $seguro->amortizacion_observacion = $request->get('idDescripcion');
            $seguro->cuenta_debe = $request->get('idCuenta');
            $seguro->amortizacion_estado = 1;
            $seguro->save();
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de Amortizacion de seguro -> Con uuenta '.$request->get('idCuenta').' Con Factura Compra '.$seguro->transaccionCompra->transaccion_numero,'0','Con Observacion -> '.$request->get('idDescripcion'));
            DB::commit();
            return redirect('amortizacion')->with('success','Datos Actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('amortizacion')->with('error','El registro no pudo ser Actualizado.');
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
            $seguro = Amortizacion_Seguros::findOrFail($id);
            $general = new generalController();
            $cierre = $general->cierre($seguro->amortizacion_fecha,$seguro->sucursal_id);          
            if($cierre){
                return redirect('amortizacion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $seguro->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de Amortizacion de Seguro -> '.$seguro->amortizacion_total.' con Factura '.$seguro->transaccionCompra->transaccion_numero,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('amortizacion')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('amortizacion')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }
}
