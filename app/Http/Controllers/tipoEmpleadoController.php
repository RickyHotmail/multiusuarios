<?php

namespace App\Http\Controllers;

use App\Models\Categoria_Rol;
use App\Models\Cuenta;
use App\Models\Punto_Emision;
use App\Models\Rubro;
use App\Models\Sucursal;
use App\Models\Tipo_Empleado;
use App\Models\Tipo_Empleado_Parametrizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class tipoEmpleadoController extends Controller
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
            $tiposEmpleado=Tipo_Empleado::Tipos()->get();
            $categorias = Categoria_Rol::Categorias()->get();   
            return view('admin.recursosHumanos.tipoEmpleado.index',['categorias'=>$categorias,'sucursales'=>Sucursal::Sucursales()->get(),'rubros'=>Rubro::rubros()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'tiposEmpleado'=>$tiposEmpleado, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $tipoEmpleado = new Tipo_Empleado();
            $tipoEmpleado->tipo_descripcion = $request->get('tipo_descripcion');
            $tipoEmpleado->tipo_categoria = $request->get('tipo_categoria');
            $tipoEmpleado->tipo_estado = 1;
            $tipoEmpleado->empresa_id = Auth::user()->empresa_id;
            $tipoEmpleado->sucursal_id = $request->get('sucursal');
            $tipoEmpleado->save();
            foreach(Rubro::rubros()->get() as $rubro){
                $detalle = new Tipo_Empleado_Parametrizacion();
                $detalle->parametrizacion_estado = 1;
                $detalle->cuenta_debe = $request->get($rubro->rubro_id.'-debe');
                $detalle->cuenta_haber = $request->get($rubro->rubro_id.'-haber');
                $detalle->rubro_id = $rubro->rubro_id;
                $detalle->categoria_id = $request->get($rubro->rubro_id.'-categoria');
                $tipoEmpleado->detalles()->save($detalle);
            }
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Tipo de empleado -> '.$request->get('tipo_descripcion'),'0','Con categoría '.$request->get('tipo_categoria'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('tipoEmpleado')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('tipoEmpleado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $tipoEmpleado=Tipo_Empleado::tipoEmpleado($id)->first();
            $categorias = Categoria_Rol::Categorias()->get();   
            if($tipoEmpleado){
                return view('admin.recursosHumanos.tipoEmpleado.ver',['categorias'=>$categorias,'sucursales'=>Sucursal::Sucursales()->get(),'tipoEmpleado'=>$tipoEmpleado ,'PE'=>Punto_Emision::puntos()->get(), 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $tipoEmpleado=Tipo_Empleado::tipoEmpleado($id)->first();
            $categorias = Categoria_Rol::Categorias()->get();   
            if($tipoEmpleado){
                return view('admin.recursosHumanos.tipoEmpleado.editar',['categorias'=>$categorias,'sucursales'=>Sucursal::Sucursales()->get(),'tipoEmpleado'=>$tipoEmpleado ,'rubros'=>Rubro::rubros()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'PE'=>Punto_Emision::puntos()->get(), 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
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
            $tipoEmpleado = Tipo_Empleado::tipoEmpleado($id)->first();
            $tipoEmpleado->tipo_descripcion = $request->get('tipo_descripcion');
            $tipoEmpleado->tipo_categoria = $request->get('tipo_categoria');
            $tipoEmpleado->sucursal_id = $request->get('sucursal');
            if ($request->get('tipo_estado') == "on"){
                $tipoEmpleado->tipo_estado = 1;
            }else{
                $tipoEmpleado->tipo_estado = 0;
            }
            $tipoEmpleado->save();
            $tipoEmpleado->detalles()->delete();
            foreach(Rubro::rubros()->get() as $rubro){
                $detalle = new Tipo_Empleado_Parametrizacion();
                $detalle->parametrizacion_estado = 1;
                $detalle->cuenta_debe = $request->get($rubro->rubro_id.'-debe');
                $detalle->cuenta_haber = $request->get($rubro->rubro_id.'-haber');
                $detalle->rubro_id = $rubro->rubro_id;
                $detalle->categoria_id = $request->get($rubro->rubro_id.'-categoria');
                $tipoEmpleado->detalles()->save($detalle);
            }
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de Tipo de empleado -> '.$request->get('tipo_descripcion'),'0','Con categoría '.$request->get('tipo_categoria'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('tipoEmpleado')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('tipoEmpleado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $tipoEmpleado = Tipo_Empleado::findOrFail($id);
            $tipoEmpleado->detalles()->delete();
            $tipoEmpleado->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de tipo de empleado -> '.$tipoEmpleado->tipo_descripcion.' con id -> '.$tipoEmpleado->tipo_id,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('tipoEmpleado')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('tipoEmpleado')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $tipoEmpleado=Tipo_Empleado::tipoEmpleado($id)->first();
            $categorias = Categoria_Rol::Categorias()->get();   
            if($tipoEmpleado){
                return view('admin.recursosHumanos.tipoEmpleado.eliminar',['categorias'=>$categorias,'sucursales'=>Sucursal::Sucursales()->get(),'tipoEmpleado'=>$tipoEmpleado ,'PE'=>Punto_Emision::puntos()->get(), 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
