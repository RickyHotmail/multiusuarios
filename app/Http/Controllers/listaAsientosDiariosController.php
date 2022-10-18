<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Diario;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listaAsientosDiariosController extends Controller
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
            $tipos=Diario::DiarioTipo()->select('diario_tipo_documento')->distinct()->get();
            $sucursal=Diario::DiarioSucursal()->select('sucursal_nombre')->distinct()->get();
            return view('admin.contabilidad.listaAsiento.index',['tipos'=>$tipos,'sucursal'=>$sucursal,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
             $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
             $tipos=Diario::DiarioTipo()->select('diario_tipo_documento')->distinct()->get();
             $sucursal=Diario::DiarioSucursal()->select('sucursal_nombre')->distinct()->get();
             $diarios=null;
             $buscar = $request->get('BuscarLike');
 
             if ($request->get('fecha_todo') != "on" && $request->get('diario_tipo') != "--TODOS--" && $request->get('sucursal') != "--TODOS--"){                 
                 $diarios=Diario::reporteDiario()
                 ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                 ->where('diario_fecha','>=',$request->get('idDesde'))
                 ->where('diario_fecha','<=',$request->get('idHasta'))
                 ->where('diario_tipo_documento','=',$request->get('diario_tipo'))
                 ->where('sucursal_nombre','=',$request->get('sucursal'))
                 ->where(function($query) use($buscar){
                    $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
                })->get();
             } 
             if ($request->get('fecha_todo') == "on" && $request->get('diario_tipo') == "--TODOS--" && $request->get('sucursal') == "--TODOS--"){  
                $diarios=Diario::reporteDiario()
                ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                ->where(function($query) use($buscar){
                    $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
                })->get();
             }
             if ($request->get('fecha_todo') != "on" && $request->get('diario_tipo') == "--TODOS--" && $request->get('sucursal') == "--TODOS--"){  
                $diarios=Diario::reporteDiario()
                ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                ->where('diario_fecha','>=',$request->get('idDesde'))
                ->where('diario_fecha','<=',$request->get('idHasta'))
                ->where(function($query) use($buscar){
                    $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
                })->get();
             }
             if ($request->get('fecha_todo') == "on" && $request->get('diario_tipo') != "--TODOS--" && $request->get('sucursal') == "--TODOS--"){  
                $diarios=Diario::reporteDiario()
                ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                ->where('diario_tipo_documento','=',$request->get('diario_tipo'))
                ->where(function($query) use($buscar){
                    $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
                })->get();
             }
             if ($request->get('fecha_todo') == "on" && $request->get('diario_tipo') == "--TODOS--" && $request->get('sucursal') != "--TODOS--"){  
                $diarios=Diario::reporteDiario()
                ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                 ->where('sucursal_nombre','=',$request->get('sucursal'))
                 ->where(function($query) use($buscar){
                    $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
                })->get();
             }
             if ($request->get('fecha_todo') == "on" && $request->get('diario_tipo') != "--TODOS--" && $request->get('sucursal') != "--TODOS--"){  
                $diarios=Diario::reporteDiario()
                ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                ->where('diario_tipo_documento','=',$request->get('diario_tipo'))
                 ->where('sucursal_nombre','=',$request->get('sucursal'))
                 ->where(function($query) use($buscar){
                    $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
                })->get();
             }
             if ($request->get('fecha_todo') != "on" && $request->get('diario_tipo') == "--TODOS--" && $request->get('sucursal') != "--TODOS--"){  
                $diarios=Diario::reporteDiario()
                ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                ->where('diario_fecha','>=',$request->get('idDesde'))
                ->where('diario_fecha','<=',$request->get('idHasta'))
                 ->where('sucursal_nombre','=',$request->get('sucursal'))
                 ->where(function($query) use($buscar){
                    $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
                })->get();
             }
             if ($request->get('fecha_todo') != "on" && $request->get('diario_tipo') != "--TODOS--" && $request->get('sucursal') == "--TODOS--"){  
                $diarios=Diario::reporteDiario()
                ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                ->where('diario_fecha','>=',$request->get('idDesde'))
                ->where('diario_fecha','<=',$request->get('idHasta'))                 
                ->where('diario_tipo_documento','=',$request->get('diario_tipo'))
                ->where(function($query) use($buscar){
                    $query->where(DB::raw('lower(diario_codigo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_referencia)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_numero_documento)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_beneficiario)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_tipo)'), 'like', '%'.strtolower($buscar).'%')
                          ->orwhere(DB::raw('lower(diario_comentario)'), 'like', '%'.strtolower($buscar).'%');
                })->get();
               
             }  
                                
             return view('admin.contabilidad.listaAsiento.index',['diarios'=>$diarios,'tipos'=>$tipos,'sucursal'=>$sucursal,'buscaLike'=>$request->get('BuscarLike'),'idsucursal'=>$request->get('sucursal'),'fecha_todo'=>$request->get('fecha_todo'),'fecha_desde'=>$request->get('idDesde'),'fecha_hasta'=>$request->get('idHasta'),'idtipo'=>$request->get('diario_tipo'),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]); 
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
