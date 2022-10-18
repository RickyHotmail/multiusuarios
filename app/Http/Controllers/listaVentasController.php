<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Bodega;
use App\Models\Punto_Emision;
use App\Models\Factura_Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Sucursal;

class listaVentasController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $puntoEmisiones = Punto_Emision::puntos()->get();
            $clientes = Factura_Venta::ClienteDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $bodegas = Factura_Venta::BodegaDistinsc()->select('bodega.bodega_id','bodega.bodega_nombre')->distinct()->get();        
            $surcursal = Factura_Venta::SurcusalDistinsc()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            $reporteVentas=null;
            $total=0;
            return view('admin.ventas.listaVentas.index',['total'=>$total,'surcursal'=>$surcursal,'reporteVentas'=>$reporteVentas,'clientes'=>$clientes, 'bodegas'=>$bodegas,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
    public function consultar(Request $request)
    {
        try{
                $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
                $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
                $valor_cliente=$request->get('nombre_cliente');
                $valor_bodega=$request->get('nombre_bodega');
                $valor_emision=$request->get('nombre_emision');
                $clientes = Factura_Venta::ClienteDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
                $bodegas = Factura_Venta::BodegaDistinsc()->select('bodega.bodega_id','bodega.bodega_nombre')->distinct()->get();        
                $surcursal = Factura_Venta::SurcusalDistinsc()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
                $puntoEmisiones = Punto_Emision::puntos()->get();
                $suma=null;
                $fechatodo=0;
                if($request->get('fecha_todo')){
                    $fechatodo=$request->get('fecha_todo');
                }
               
                $reporteVentas=Factura_Venta::busqueda($request->get('fecha_todo'),$request->get('fecha_desde'),$request->get('fecha_hasta'), $request->get('nombre_cliente'), $request->get('nombre_bodega'), $request->get('sucursal'))->get();
                $reportedetalle=Factura_Venta::Busquedadetalle($request->get('fecha_todo'),$request->get('fecha_desde'),$request->get('fecha_hasta'), $request->get('nombre_cliente'), $request->get('nombre_bodega'), $request->get('sucursal'))->select('grupo_nombre',DB::raw("SUM(detalle_total) as total"))->groupBy('grupo_nombre')->get();
                $count=1;
                $validar=false;
                $datos=null;
                $total=0;
                foreach($reportedetalle as $report){        
                            $datos[$count]["Numero"]=$count;
                            $datos[$count]["Grupo"]=$report->grupo_nombre;
                            $datos[$count]["valor"]=$report->total;
                            $count++;
                            $total=$total+$report->total;     
                }            
                return view('admin.ventas.listaVentas.index', ['total'=>$total,'idsucursal'=>$request->get('sucursal'),'surcursal'=>$surcursal,'datos'=>$datos,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'valor_bodega'=>$valor_bodega,'nombre_emision'=>$request->get('nombre_emision'),'nombre_cliente'=>$request->get('nombre_cliente'),'fecha_todo'=>$request->get('fecha_todo'),'reporteVentas'=>$reporteVentas, 'puntoEmisiones'=>$puntoEmisiones, 'clientes'=>$clientes, 'bodegas'=>$bodegas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
     
    }
}
