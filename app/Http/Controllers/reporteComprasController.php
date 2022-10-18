<?php

namespace App\Http\Controllers;

use App\Models\Detalle_RC;
use App\Models\Liquidacion_Compra;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Transaccion_Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class reporteComprasController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.sri.reporteCompras.index',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $transaccionCompras=Transaccion_Compra::reporteTransacciones()
            ->where('transaccion_fecha','>=',$request->get('idDesde'))
            ->where('transaccion_fecha','<=',$request->get('idHasta'))->distinct()->where('tipo_comprobante.tipo_comprobante_codigo','<>','02')->where('tipo_comprobante.tipo_comprobante_codigo','<>','04')->where('tipo_comprobante.tipo_comprobante_codigo','<>','05')->orderBy('transaccion_fecha','asc')->get();
            $notasVenta=Transaccion_Compra::reporteTransacciones()
            ->where('transaccion_fecha','>=',$request->get('idDesde'))
            ->where('transaccion_fecha','<=',$request->get('idHasta'))->distinct()->where('tipo_comprobante.tipo_comprobante_codigo','=','02')->orderBy('transaccion_fecha','asc')->get();
            $notasCredito=Transaccion_Compra::reporteTransacciones()
            ->where('transaccion_fecha','>=',$request->get('idDesde'))
            ->where('transaccion_fecha','<=',$request->get('idHasta'))->distinct()->where('tipo_comprobante.tipo_comprobante_codigo','=','04')->orderBy('transaccion_fecha','asc')->get();
            $notasDebito=Transaccion_Compra::reporteTransacciones()
            ->where('transaccion_fecha','>=',$request->get('idDesde'))
            ->where('transaccion_fecha','<=',$request->get('idHasta'))->distinct()->where('tipo_comprobante.tipo_comprobante_codigo','=','05')->orderBy('transaccion_fecha','asc')->get();
            $liquidaciones=Liquidacion_Compra::ReporteLiquidaciones()
            ->where('lc_fecha','>=',$request->get('idDesde'))
            ->where('lc_fecha','<=',$request->get('idHasta'))->distinct()->orderBy('lc_fecha','asc')->get();
            $retencionesF = Detalle_RC::DetalleByFecha($request->get('idDesde'),$request->get('idHasta'))->select('concepto_codigo','concepto_nombre',DB::raw('SUM(detalle_rc.detalle_base) as base'),DB::raw('SUM(detalle_rc.detalle_valor) as valor'),DB::raw('COUNT(detalle_rc.detalle_id) as cantidad'))->where('retencion_compra.retencion_estado','=','1')->groupBy('concepto_codigo','concepto_nombre')->where('detalle_tipo','=','FUENTE')->get();
            $retencionesI = Detalle_RC::DetalleByFecha($request->get('idDesde'),$request->get('idHasta'))->select('concepto_codigo','concepto_nombre',DB::raw('SUM(detalle_rc.detalle_base) as base'),DB::raw('SUM(detalle_rc.detalle_valor) as valor'),DB::raw('COUNT(detalle_rc.detalle_id) as cantidad'))->where('retencion_compra.retencion_estado','=','1')->groupBy('concepto_codigo','concepto_nombre')->where('detalle_tipo','=','IVA')->get();
            $resumenTotales = Transaccion_Compra::reporteTransacciones()
            ->where('transaccion_fecha','>=',$request->get('idDesde'))
            ->where('transaccion_fecha','<=',$request->get('idHasta'))           
            ->select('transaccion_compra.tipo_comprobante_id','tipo_comprobante_nombre',DB::raw('COUNT(transaccion_compra.tipo_comprobante_id) as cantidad'),DB::raw('SUM(transaccion_subtotal) as subtotal'),DB::raw('SUM(transaccion_tarifa0) as tarifa0'),DB::raw('SUM(transaccion_tarifa12) as tarifa12'),DB::raw('SUM(transaccion_iva) as iva'),DB::raw('SUM(transaccion_total) as total'))
            ->groupBy('transaccion_compra.tipo_comprobante_id','tipo_comprobante_nombre')->get();
            $resumenTotalesLC = Liquidacion_Compra::ReporteLiquidaciones()
            ->where('lc_fecha','>=',$request->get('idDesde'))
            ->where('lc_fecha','<=',$request->get('idHasta'))           
            ->select(DB::raw('COUNT(lc_id) as cantidad'),DB::raw('SUM(lc_subtotal) as subtotal'),DB::raw('SUM(lc_tarifa0) as tarifa0'),DB::raw('SUM(lc_tarifa12) as tarifa12'),DB::raw('SUM(lc_iva) as iva'),DB::raw('SUM(lc_total) as total'))->get();

            return view('admin.sri.reporteCompras.index',['notasVenta'=>$notasVenta,'notasCredito'=>$notasCredito,'notasDebito'=>$notasDebito,'fecI'=>$request->get('idDesde'),'fecF'=>$request->get('idHasta'),'resumenTotales'=>$resumenTotales,'resumenTotalesLC'=>$resumenTotalesLC,'liquidaciones'=>$liquidaciones,'transaccionCompras'=>$transaccionCompras,'retencionesF'=>$retencionesF,'retencionesI'=>$retencionesI,'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
