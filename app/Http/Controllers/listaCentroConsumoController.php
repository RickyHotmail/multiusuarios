<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Centro_Consumo;
use App\Models\Detalle_TC;
use App\Models\Movimiento_Producto;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class listaCentroConsumoController extends Controller
{
    public function vista()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.compras.reporteCentroConsumo.index',['CentroConsumos'=>Centro_Consumo::CentroConsumos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function listarCentrosconsumo(Request $request)
    {          
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            if($request->get('idtodo') == "on"){
                $todo=1;
                $centros = Centro_Consumo::CentroConsumos()->get();
            }else{
                $todo=0;
                $centros = Centro_Consumo::CentroConsumo($request->get('idCentroc'))->get();
            }
            foreach($centros as $centro){
                $datos[$count]['fec'] = $centro->centro_consumo_nombre; 
                $datos[$count]['doc'] = ''; 
                $datos[$count]['num'] = '';
                $datos[$count]['pro'] = ''; 
                $datos[$count]['des'] = ''; 
                $datos[$count]['cat'] = ''; 
                $datos[$count]['cod'] = ''; 
                $datos[$count]['can'] = ''; 
                $datos[$count]['cos'] = Movimiento_Producto::MovimientoByCC($centro->centro_consumo_id,$request->get('idDesde'),$request->get('idHasta'))->sum('movimiento_precio'); 
                $datos[$count]['iva'] = Movimiento_Producto::MovimientoByCC($centro->centro_consumo_id,$request->get('idDesde'),$request->get('idHasta'))->sum('movimiento_iva'); ; 
                $datos[$count]['tol'] = Movimiento_Producto::MovimientoByCC($centro->centro_consumo_id,$request->get('idDesde'),$request->get('idHasta'))->sum('movimiento_total');  
                $datos[$count]['tot'] = '1';
                $count ++;
                foreach(Movimiento_Producto::MovimientoByCC($centro->centro_consumo_id,$request->get('idDesde'),$request->get('idHasta'))->orderBy('movimiento_fecha','asc')->get() as $movimiento){
                    $datos[$count]['fec'] = $movimiento->movimiento_fecha; 
                    $datos[$count]['doc'] = ''; 
                    $datos[$count]['num'] = '';
                    $datos[$count]['pro'] = ''; 
                    $datos[$count]['des'] = ''; 
                    $datos[$count]['cat'] = $movimiento->producto->categoriaProducto->categoria_nombre; 
                    if($movimiento->detalle_tc){
                        $datos[$count]['doc'] = $movimiento->detalle_tc->transaccionCompra->tipoComprobante->tipo_comprobante_nombre; 
                        $datos[$count]['num'] = $movimiento->detalle_tc->transaccionCompra->transaccion_numero;
                        $datos[$count]['pro'] = $movimiento->detalle_tc->transaccionCompra->proveedor->proveedor_nombre; 
                        $datos[$count]['des'] = $movimiento->detalle_tc->detalle_descripcion; 
                    }
                    if($movimiento->detalle_eb){
                        $datos[$count]['doc'] = 'Egreso de Bodega'; 
                        $datos[$count]['num'] = $movimiento->detalle_eb->egresobodega->cabecera_egreso_numero;
                        $datos[$count]['pro'] = $movimiento->detalle_eb->egresobodega->cabecera_egreso_destinatario; 
                        $datos[$count]['des'] = $movimiento->detalle_eb->detalle_egreso_descripcion; 
                    }
                    if($movimiento->detalle_ib){
                        $datos[$count]['doc'] = 'Ingreso de Bodega'; 
                        $datos[$count]['num'] = $movimiento->detalle_ib->Ingresobodega->cabecera_ingreso_numero;
                        $datos[$count]['pro'] = $movimiento->detalle_ib->Ingresobodega->proveedor->proveedor_nombre; 
                        $datos[$count]['des'] = $movimiento->detalle_ib->detalle_ingreso_descripcion; 
                    }
                    if($movimiento->detalle_lc){
                        $datos[$count]['doc'] = 'Liquidación de Compra'; 
                        $datos[$count]['num'] = $movimiento->detalle_lc->liquidacionCompra->lc_numero;
                        $datos[$count]['pro'] = $movimiento->detalle_lc->liquidacionCompra->proveedor->proveedor_nombre; 
                        $datos[$count]['des'] = $movimiento->detalle_lc->detalle_descripcion; 
                    }
                    $datos[$count]['cod'] = $movimiento->producto->producto_nombre; 
                    $datos[$count]['can'] = $movimiento->movimiento_cantidad; 
                    $datos[$count]['cos'] = $movimiento->movimiento_precio; 
                    $datos[$count]['iva'] = $movimiento->movimiento_iva; 
                    $datos[$count]['tol'] = $movimiento->movimiento_total; 
                    $datos[$count]['tot'] = '2';
                    $count ++;
                }
            }
            return view('admin.compras.reporteCentroConsumo.index',['todo'=>$todo,'cc'=>$request->get('idCentroc'),'fecI'=>$request->get('idDesde'),'fecF'=>$request->get('idHasta'),'datos'=>$datos,'CentroConsumos'=>Centro_Consumo::CentroConsumos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listaCc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

}
