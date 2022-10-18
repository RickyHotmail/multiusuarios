<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Bodega;
use App\Models\Punto_Emision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Detalle_RV;
use App\Models\Factura_Venta;
use App\Models\Nota_Credito;
use App\Models\Nota_Debito;

class reporteVentasController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.sri.reporteVentas.index',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $facturas = Factura_Venta::FacturasbyFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->distinct()->get();
            $notasC = Nota_Credito::NCbyFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->distinct()->get();
            $notasD = Nota_Debito::NDbyFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->distinct()->get();
            $retencionesF = Detalle_RV::DetalleByFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('concepto_codigo','concepto_nombre',DB::raw('SUM(detalle_rv.detalle_base) as base'),DB::raw('SUM(detalle_rv.detalle_valor) as valor'),DB::raw('COUNT(detalle_rv.detalle_id) as cantidad'))->where('detalle_tipo','=','FUENTE')->where('retencion_venta.retencion_estado','=','1')->groupBy('concepto_codigo','concepto_nombre')->get();
            $retencionesI = Detalle_RV::DetalleByFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('concepto_codigo','concepto_nombre',DB::raw('SUM(detalle_rv.detalle_base) as base'),DB::raw('SUM(detalle_rv.detalle_valor) as valor'),DB::raw('COUNT(detalle_rv.detalle_id) as cantidad'))->where('detalle_tipo','=','IVA')->where('retencion_venta.retencion_estado','=','1')->groupBy('concepto_codigo','concepto_nombre')->get();
            $resumenTotales = Factura_Venta::FacturasbyFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))
            ->select(DB::raw('COUNT(factura_id) as cantidad'),DB::raw('SUM(factura_subtotal) as subtotal'),DB::raw('SUM(factura_tarifa0) as tarifa0'),DB::raw('SUM(factura_tarifa12) as tarifa12'),DB::raw('SUM(factura_iva) as iva'),DB::raw('SUM(factura_total) as total'))->get();
            $resumenTotalesNC = Nota_Credito::NCbyFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))
            ->select(DB::raw('COUNT(nc_id) as cantidad'),DB::raw('SUM(nc_subtotal) as subtotal'),DB::raw('SUM(nc_tarifa0) as tarifa0'),DB::raw('SUM(nc_tarifa12) as tarifa12'),DB::raw('SUM(nc_iva) as iva'),DB::raw('SUM(nc_total) as total'))->get();
            $resumenTotalesND = Nota_Debito::NDbyFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))
            ->select(DB::raw('COUNT(nd_id) as cantidad'),DB::raw('SUM(nd_subtotal) as subtotal'),DB::raw('SUM(nd_tarifa0) as tarifa0'),DB::raw('SUM(nd_tarifa12) as tarifa12'),DB::raw('SUM(nd_iva) as iva'),DB::raw('SUM(nd_total) as total'))->get();
            
            return view('admin.sri.reporteVentas.index', ['fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'resumenTotales'=>$resumenTotales,'resumenTotalesNC'=>$resumenTotalesNC,'resumenTotalesND'=>$resumenTotalesND,'retencionesF'=>$retencionesF,'retencionesI'=>$retencionesI,'facturas'=>$facturas,'notasC'=>$notasC,'notasD'=>$notasD, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
