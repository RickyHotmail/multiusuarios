<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activo_Fijo;
use App\Models\Cuenta;
use App\Models\Diario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\Models\Venta_Activo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ventaActivoController extends Controller
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
            
            $sucursales=Sucursal::sucursales()->get();
            return view('admin.activosFijos.ventaActivo.index',
            ['sucursales'=>$sucursales,  
            'gruposPermiso'=>$gruposPermiso,         
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $diarios=Diario::diarios()->get();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $productos=Producto::productos()->get();
            $proveedores=Proveedor::proveedores()->get();
            $sucursales=Sucursal::sucursales()->get();
            return view('admin.activosFijos.ventaActivo.nuevo',
            ['cuentas'=>$cuentas,        
            'sucursales'=>$sucursales,
            'productos'=>$productos,
            'diarios'=>$diarios,
            'proveedores'=>$proveedores,
            'gruposPermiso'=>$gruposPermiso,         
            'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
            $ventaActivo = new Venta_Activo();
            $general = new generalController();
            $sucursales=Sucursal::Sucursales()->get();
            foreach($sucursales as $sucursal){
                $cierre = $general->cierre($request->get('idFecha'),$sucursal->sucursal_id);          
                if($cierre){
                    return redirect('ventaActivo')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
            }
            
            $ventaActivo->venta_fecha = $request->get('idFecha');            
            $ventaActivo->venta_descripcion = $request->get('idDescripcion');
            $ventaActivo->venta_monto = $request->get('idMonto');
            $ventaActivo->venta_estado = 1;
            $ventaActivo->activo_id = $request->get('idActivoFijo');           
            $ventaActivo->save();

            $activoFijo=Activo_Fijo::findOrFail($request->get('idActivoFijo'));
            $activoFijo->activo_valor = str_replace(",","",$request->get('idValor'));
            $activoFijo->activo_base_depreciar = str_replace(",","",$request->get('idBaseDepreciar'));
            $activoFijo->activo_vida_util = $request->get('idVidaUtil');
            $activoFijo->activo_valor_util = str_replace(",","",$request->get('idValorUtil'));
            $activoFijo->activo_depreciacion = $request->get('porcentaje_depreciacion');
            $activoFijo->activo_depreciacion_mensual = str_replace(",","",$request->get('idDepreciacionMensual'));
            $activoFijo->activo_depreciacion_anual = str_replace(",","",$request->get('idDepreciacionAnual'));
            $activoFijo->activo_depreciacion_acumulada = str_replace(",","",$request->get('idDepreciacionAcumulada'));
            $activoFijo->save();

            $auditoria1 = new generalController();
            $auditoria1->registrarAuditoria('Se Registro una venta de Activo fijo -> '.$request->get('idValor'),'0', 'Descripcion: '. $request->get('idDescripcion'));
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Venta de activo Fijo con nombre: -> '.$request->get('idDescripcion'),'0','con el valor de'.$request->get('idMonto'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('ventaActivo')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('ventaActivo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarVentaActivo(Request $request){        
        try{            
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $sucursalselect =  $request->get('idsucursal');
            $activoselect =  $request->get('idActivo');
            $sumatoriaActivo =  Venta_Activo::Sumactivo($request->get('idActivo'))->first();
            $ventasActivo=Venta_Activo::VentaActivoxSucursalxActivo($request->get('idsucursal'), $request->get('idActivo'))->get();
            $activosFijo = Activo_Fijo::ActivoFijoxSucursalprodu($request->get('idsucursal'))->get();
            $sucursales=Sucursal::sucursales()->get();
            return view('admin.activosFijos.ventaActivo.index',
            ['sucursales'=>$sucursales,
            'ventasActivo'=>$ventasActivo,
            'sucursalselect'=>$sucursalselect, 
            'activoselect'=>$activoselect,
            'sumatoriaActivo'=>$sumatoriaActivo,
            'activosFijo'=>$activosFijo,
            'gruposPermiso'=>$gruposPermiso,                
            'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('activoFijo')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $ventaActivo=Venta_Activo::VentaActivo($id)->first();            
            if($ventaActivo){
                return view('admin.activosFijos.ventaActivo.ver',['ventaActivo'=>$ventaActivo, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try{
            DB::beginTransaction();

            $ventaActivo = Venta_Activo::findOrFail($id);
            $ventaActivoaux = Venta_Activo::findOrFail($id);
            $activoFijo=Activo_Fijo::findOrFail($ventaActivoaux->activo_id);
            $general = new generalController();
            $sucursales=Sucursal::Sucursales()->get();
            foreach($sucursales as $sucursal){
                $cierre = $general->cierre($ventaActivo->venta_fecha,$sucursal->sucursal_id);          
                if($cierre){
                    return redirect('ventaActivo')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
            }
            $valoractual= floatval($activoFijo->activo_valor) + floatval($ventaActivoaux->venta_monto);
            $valorUtil =  (floatval($valoractual) * floatval($activoFijo->activo_vida_util)) / 100;
            $porcentajeUil= $activoFijo->activo_depreciacion;
            $basedepreciar = $valoractual - $valorUtil;
            $depreAnual =  floatval($basedepreciar) * floatval($porcentajeUil) /100;
            
            $activoFijo->activo_valor = $valoractual;
            $activoFijo->activo_base_depreciar = $valoractual - $valorUtil;
            $activoFijo->activo_valor_util = $valorUtil;
            $activoFijo->activo_depreciacion_anual = $basedepreciar * $porcentajeUil /100;
            $activoFijo->activo_depreciacion_mensual = $depreAnual / 12;
            $ventaActivo->delete();
            $activoFijo->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de venta de activo -> '.$ventaActivoaux->venta_descripcion,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('ventaActivo')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('ventaActivo')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }  
    }
    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ventaActivo=Venta_Activo::VentaActivo($id)->first();
            if($ventaActivo){
                return view('admin.activosFijos.ventaActivo.eliminar',['ventaActivo'=>$ventaActivo,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    //buscarBySucursal
    public function buscarBySucursal($buscar){        
        return Activo_Fijo::ActivoFijoxSucursalprodu($buscar)->get();
    }
    //buscarByActivo
    public function buscarByActivo($buscar){        
        return Activo_Fijo::ActivoFijo($buscar)->first();
    }
    //sumatoriaVentas
    public function sumatoriaVentas($buscar){
        return Venta_Activo::Sumactivo($buscar)->first();
    }
}
