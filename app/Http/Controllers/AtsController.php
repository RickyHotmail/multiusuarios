<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Documento_Anulado;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Liquidacion_Compra;
use App\Models\Nota_Credito;
use App\Models\Nota_Debito;
use App\Models\Punto_Emision;
use App\Models\Retencion_Compra;
use App\Models\Retencion_Venta;
use App\Models\Sucursal;
use App\Models\Transaccion_Compra;
use App\Models\Transaccion_Identificacion;
use DateTime;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

class AtsController extends Controller
{
    public function nuevo()
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.sri.anexoTransaccional.index', ['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        } catch (\Exception $ex) {
            return redirect('inicio')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {
        if (isset($_POST['ver'])) {
            return $this->ver($request);
        }
        if (isset($_POST['generar'])) {
            return $this->generar($request);
        }
        if (isset($_POST['pdf'])) {
            return $this->pdf($request);
        }
    }
    public function ver(Request $request)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $fechaFin = $request->get('idPeriodo');
            $fechaInicio = DateTime::createFromFormat('Y-m-d', $fechaFin)->format('Y').'-'.DateTime::createFromFormat('Y-m-d', $fechaFin)->format('m').'-01';
            /******************************************************/
            $tabla1 = null;
            $count = 1;
            $ta0 = 0;
            $ta12 = 0;
            $iv = 0;
            foreach (Transaccion_Compra::TransaccionByFecha($fechaInicio, $fechaFin)->select(
                'tipo_comprobante_codigo',
                'tipo_comprobante_nombre',
                DB::raw('COUNT(transaccion_id) as cantidad'),
                DB::raw('SUM(transaccion_tarifa0) as tarifa0'),
                DB::raw('SUM(transaccion_tarifa12) as tarifa12'),
                DB::raw('SUM(transaccion_iva) as iva')
            )->groupBy('tipo_comprobante_codigo', 'tipo_comprobante_nombre')->get() as $transaccion) {
                $tabla1[$count]['cod'] = $transaccion->tipo_comprobante_codigo;
                $tabla1[$count]['tra'] = $transaccion->tipo_comprobante_nombre;
                $tabla1[$count]['can'] = $transaccion->cantidad;
                $tabla1[$count]['0'] = $transaccion->tarifa0;
                $tabla1[$count]['12'] = $transaccion->tarifa12;
                $tabla1[$count]['iva'] = $transaccion->iva;
                if($transaccion->tipo_comprobante_codigo == '04'){
                    $ta0 = $ta0 - $tabla1[$count]['0'];
                    $ta12 = $ta12 - $tabla1[$count]['12'];
                    $iv = $iv - $tabla1[$count]['iva'];
                }else{
                    $ta0 = $ta0 + $tabla1[$count]['0'];
                    $ta12 = $ta12 + $tabla1[$count]['12'];
                    $iv = $iv + $tabla1[$count]['iva'];
                }                
                $count = $count +1;
            }
            $lc = Liquidacion_Compra::LCbyFecha($fechaInicio, $fechaFin)->select(DB::raw('COUNT(lc_id) as cantidad'), DB::raw('SUM(lc_tarifa0) as tarifa0'), DB::raw('SUM(lc_tarifa12) as tarifa12'), DB::raw('SUM(lc_iva) as iva'))->first();
            $tabla1[$count]['cod'] = "03";
            $tabla1[$count]['tra'] = 'Liquidación de compra de Bienes o Prestación de servicios';
            $tabla1[$count]['can'] = $lc->cantidad;
            $tabla1[$count]['0'] = $lc->tarifa0;
            $tabla1[$count]['12'] = $lc->tarifa12;
            $tabla1[$count]['iva'] = $lc->iva;
            $ta0 = $ta0 + $tabla1[$count]['0'];
            $ta12 = $ta12 + $tabla1[$count]['12'];
            $iv = $iv + $tabla1[$count]['iva'];
            $count = $count +1;

            $tabla1[$count]['tra'] = 'TOTAL';
            $tabla1[$count]['0'] = $ta0;
            $tabla1[$count]['12'] = $ta12;
            $tabla1[$count]['iva'] = $iv;
            /******************************************************/
            $tabla2 = null;
            $count = 1;
            $ta0 = 0;
            $ta12 = 0;
            $iv = 0;
            $countFactura = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','not like','%REEMBOLSO DE GASTO%')
            ->select(DB::raw('COUNT(distinct factura_venta.factura_id) as cantidad'))->first();
            $facturas = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','not like','%REEMBOLSO DE GASTO%')
            ->select('detalle_fv.detalle_total', 'detalle_fv.detalle_iva')->get();
            $tabla2[$count]['cod'] = "18";
            $tabla2[$count]['tra'] = "DOCUMENTOS AUTORIZADOS EN VENTAS EXCEPTO ND Y NC";
            $tabla2[$count]['can'] = $countFactura->cantidad;
            $tabla2[$count]['12'] = 0;
            $tabla2[$count]['0'] = 0;
            $tabla2[$count]['iva'] = 0;
            foreach($facturas as $factura){
                if(floatval($factura->detalle_iva) > 0){
                    $tabla2[$count]['12'] = $tabla2[$count]['12'] + floatval($factura->detalle_total);
                }else{
                    $tabla2[$count]['0'] = $tabla2[$count]['0'] + floatval($factura->detalle_total);
                }          
                $tabla2[$count]['iva'] = $tabla2[$count]['iva'] + floatval($factura->detalle_iva);
            }
            $ta0 = $ta0 + $tabla2[$count]['0'];
            $ta12 = $ta12 + $tabla2[$count]['12'];
            $iv = $iv + $tabla2[$count]['iva'];
            $count = $count +1;
            $countFacturaR = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
            ->select(DB::raw('COUNT(distinct factura_venta.factura_id) as cantidad'))->first();
            $facturasR = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
            ->select('detalle_fv.detalle_total', 'detalle_fv.detalle_iva')->get();
            $tabla2[$count]['cod'] = "41";
            $tabla2[$count]['tra'] = "COMPROBANTE DE VENTA EMITIDO POR REEMBOLSO";
            $tabla2[$count]['can'] = $countFacturaR->cantidad;
            $tabla2[$count]['12'] = 0;
            $tabla2[$count]['0'] = 0;
            $tabla2[$count]['iva'] = 0;
            foreach($facturasR as $factura){
                if(floatval($factura->detalle_iva) > 0){
                    $tabla2[$count]['12'] = $tabla2[$count]['12'] + floatval($factura->detalle_total);
                }else{
                    $tabla2[$count]['0'] = $tabla2[$count]['0'] + floatval($factura->detalle_total);
                }          
                $tabla2[$count]['iva'] = $tabla2[$count]['iva'] + floatval($factura->detalle_iva);
            }
            $ta0 = $ta0 + $tabla2[$count]['0'];
            $ta12 = $ta12 + $tabla2[$count]['12'];
            $iv = $iv + $tabla2[$count]['iva'];
            $count = $count +1;
            $nc = Nota_Credito::NCbyFecha($fechaInicio, $fechaFin)->select(DB::raw('COUNT(nc_id) as cantidad'), DB::raw('SUM(nc_tarifa0) as tarifa0'), DB::raw('SUM(nc_tarifa12) as tarifa12'), DB::raw('SUM(nc_iva) as iva'))->first();
            $tabla2[$count]['cod'] = "04";
            $tabla2[$count]['tra'] = "NOTA DE CRÉDITO";
            $tabla2[$count]['can'] = $nc->cantidad;
            $tabla2[$count]['0'] = $nc->tarifa0;
            $tabla2[$count]['12'] = $nc->tarifa12;
            $tabla2[$count]['iva'] = $nc->iva;
            $ta0 = $ta0 - $tabla2[$count]['0'];
            $ta12 = $ta12 - $tabla2[$count]['12'];
            $iv = $iv - $tabla2[$count]['iva'];
            $count = $count +1;
            $nd = Nota_Debito::NDbyFecha($fechaInicio, $fechaFin)->select(DB::raw('COUNT(nd_id) as cantidad'), DB::raw('SUM(nd_tarifa0) as tarifa0'), DB::raw('SUM(nd_tarifa12) as tarifa12'), DB::raw('SUM(nd_iva) as iva'))->first();
            $tabla2[$count]['cod'] = "05";
            $tabla2[$count]['tra'] = "NOTA DE DÉBITO";
            $tabla2[$count]['can'] = $nd->cantidad;
            $tabla2[$count]['0'] = $nd->tarifa0;
            $tabla2[$count]['12'] = $nd->tarifa12;
            $tabla2[$count]['iva'] = $nd->iva;
            $ta0 = $ta0 + $tabla2[$count]['0'];
            $ta12 = $ta12 + $tabla2[$count]['12'];
            $iv = $iv + $tabla2[$count]['iva'];
            $count = $count +1;

            $tabla2[$count]['tra'] = 'TOTAL';
            $tabla2[$count]['0'] = $ta0;
            $tabla2[$count]['12'] = $ta12;
            $tabla2[$count]['iva'] = $iv;
            /******************************************************/
            $tabla3 = null;
            $count = 1;
            $retB = 0;
            $retV = 0;
            foreach (Retencion_Compra::retbyFecha($fechaInicio, $fechaFin)->join('detalle_rc', 'detalle_rc.retencion_id', '=', 'retencion_compra.retencion_id')->join('concepto_retencion', 'concepto_retencion.concepto_id', '=', 'detalle_rc.concepto_id')
                    ->select('concepto_codigo', 'concepto_nombre', DB::raw('COUNT(detalle_id) as cantidad'), DB::raw('SUM(detalle_base) as base'), DB::raw('SUM(detalle_valor) as valor'))
                    ->where('detalle_tipo', '=', 'FUENTE')->where('retencion_compra.retencion_estado','=','1')->groupBy('concepto_codigo', 'concepto_nombre')->get() as $retenciones) {
                $tabla3[$count]['cod'] = $retenciones->concepto_codigo;
                $tabla3[$count]['tra'] = $retenciones->concepto_nombre;
                $tabla3[$count]['can'] = $retenciones->cantidad;
                $tabla3[$count]['base'] = $retenciones->base;
                $retB = $retB + $tabla3[$count]['base'];
                $tabla3[$count]['valor'] = $retenciones->valor;
                $retV = $retV + $tabla3[$count]['valor'];
                $count = $count +1;
            }
            $tabla3[$count]['tra'] = 'TOTAL';
            $tabla3[$count]['base'] = $retB;
            $tabla3[$count]['valor'] = $retV;
            /******************************************************/
            $tabla4 = null;
            $count = 1;
            $ret = 0;
            foreach (Retencion_Compra::retbyFecha($fechaInicio, $fechaFin)->join('detalle_rc', 'detalle_rc.retencion_id', '=', 'retencion_compra.retencion_id')->join('concepto_retencion', 'concepto_retencion.concepto_id', '=', 'detalle_rc.concepto_id')
                    ->select('concepto_nombre', DB::raw('SUM(detalle_valor) as valor'))->where('retencion_compra.retencion_estado','=','1')->where('detalle_tipo', '=', 'IVA')->groupBy('concepto_codigo', 'concepto_nombre')->get() as $retenciones) {
                $tabla4[$count]['tra'] = $retenciones->concepto_nombre;
                $tabla4[$count]['valor'] = $retenciones->valor;
                $ret = $ret + $tabla4[$count]['valor'] ;
                $count = $count +1;
            }
            $tabla4[$count]['tra'] = 'TOTAL';
            $tabla4[$count]['valor'] = $ret;
            /******************************************************/
            $tabla5 = null;
            $count = 1;
            $ret = 0;
            $tabla5[$count]['tra'] = 'Valor de IVA que le han retenido';
            $tabla5[$count]['valor'] = Retencion_Venta::RetByFecha($fechaInicio, $fechaFin)->join('detalle_rv', 'detalle_rv.retencion_id', '=', 'retencion_venta.retencion_id')->where('detalle_tipo', '=', 'IVA')->where('retencion_venta.retencion_estado','=','1')->sum('detalle_valor');
            $ret = $ret + $tabla5[$count]['valor'] ;
            $count = $count +1;
            $tabla5[$count]['tra'] = 'Valor de Renta que le han retenido';
            $tabla5[$count]['valor'] = Retencion_Venta::RetByFecha($fechaInicio, $fechaFin)->join('detalle_rv', 'detalle_rv.retencion_id', '=', 'retencion_venta.retencion_id')->where('detalle_tipo', '=', 'FUENTE')->where('retencion_venta.retencion_estado','=','1')->sum('detalle_valor');
            $ret = $ret + $tabla5[$count]['valor'] ;
            $count = $count +1;
            $tabla5[$count]['tra'] = 'TOTAL';
            $tabla5[$count]['valor'] = $ret;
            
            return view('admin.sri.anexoTransaccional.index', ['tabla1'=>$tabla1,'tabla2'=>$tabla2,'tabla3'=>$tabla3,'tabla4'=>$tabla4,'tabla5'=>$tabla5,'fecha'=>$request->get('idPeriodo'),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        } catch (\Exception $ex) {
            return redirect('atsSRI')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $fechaFin = $request->get('idPeriodo');
            $fechaInicio = DateTime::createFromFormat('Y-m-d', $fechaFin)->format('Y').'-'.DateTime::createFromFormat('Y-m-d', $fechaFin)->format('m').'-01';
            /******************************************************/
            $tabla1 = null;
            $count = 1;
            $ta0 = 0;
            $ta12 = 0;
            $iv = 0;
            foreach (Transaccion_Compra::TransaccionByFecha($fechaInicio, $fechaFin)->select(
                'tipo_comprobante_codigo',
                'tipo_comprobante_nombre',
                DB::raw('COUNT(transaccion_id) as cantidad'),
                DB::raw('SUM(transaccion_tarifa0) as tarifa0'),
                DB::raw('SUM(transaccion_tarifa12) as tarifa12'),
                DB::raw('SUM(transaccion_iva) as iva')
            )->groupBy('tipo_comprobante_codigo', 'tipo_comprobante_nombre')->get() as $transaccion) {
                $tabla1[$count]['cod'] = $transaccion->tipo_comprobante_codigo;
                $tabla1[$count]['tra'] = $transaccion->tipo_comprobante_nombre;
                $tabla1[$count]['can'] = $transaccion->cantidad;
                $tabla1[$count]['0'] = $transaccion->tarifa0;
                $tabla1[$count]['12'] = $transaccion->tarifa12;
                $tabla1[$count]['iva'] = $transaccion->iva;
                if($transaccion->tipo_comprobante_codigo == '04'){
                    $ta0 = $ta0 - $tabla1[$count]['0'];
                    $ta12 = $ta12 - $tabla1[$count]['12'];
                    $iv = $iv - $tabla1[$count]['iva'];
                }else{
                    $ta0 = $ta0 + $tabla1[$count]['0'];
                    $ta12 = $ta12 + $tabla1[$count]['12'];
                    $iv = $iv + $tabla1[$count]['iva'];
                }  
                $count = $count +1;
            }
            $lc = Liquidacion_Compra::LCbyFecha($fechaInicio, $fechaFin)->select(DB::raw('COUNT(lc_id) as cantidad'), DB::raw('SUM(lc_tarifa0) as tarifa0'), DB::raw('SUM(lc_tarifa12) as tarifa12'), DB::raw('SUM(lc_iva) as iva'))->first();
            $tabla1[$count]['cod'] = "03";
            $tabla1[$count]['tra'] = 'Liquidación de compra de Bienes o Prestación de servicios';
            $tabla1[$count]['can'] = $lc->cantidad;
            $tabla1[$count]['0'] = $lc->tarifa0;
            $tabla1[$count]['12'] = $lc->tarifa12;
            $tabla1[$count]['iva'] = $lc->iva;
            $ta0 = $ta0 + $tabla1[$count]['0'];
            $ta12 = $ta12 + $tabla1[$count]['12'];
            $iv = $iv + $tabla1[$count]['iva'];
            $count = $count +1;

            $tabla1[$count]['tra'] = 'TOTAL';
            $tabla1[$count]['0'] = $ta0;
            $tabla1[$count]['12'] = $ta12;
            $tabla1[$count]['iva'] = $iv;
            /******************************************************/
            $tabla2 = null;
            $count = 1;
            $ta0 = 0;
            $ta12 = 0;
            $iv = 0;
            $countFactura = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','not like','%REEMBOLSO DE GASTO%')
            ->select(DB::raw('COUNT(distinct factura_venta.factura_id) as cantidad'))->first();
            $facturas = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','not like','%REEMBOLSO DE GASTO%')
            ->select('detalle_fv.detalle_total', 'detalle_fv.detalle_iva')->get();
            $tabla2[$count]['cod'] = "18";
            $tabla2[$count]['tra'] = "DOCUMENTOS AUTORIZADOS EN VENTAS EXCEPTO ND Y NC";
            $tabla2[$count]['can'] = $countFactura->cantidad;
            $tabla2[$count]['12'] = 0;
            $tabla2[$count]['0'] = 0;
            $tabla2[$count]['iva'] = 0;
            foreach($facturas as $factura){
                if(floatval($factura->detalle_iva) > 0){
                    $tabla2[$count]['12'] = $tabla2[$count]['12'] + floatval($factura->detalle_total);
                }else{
                    $tabla2[$count]['0'] = $tabla2[$count]['0'] + floatval($factura->detalle_total);
                }          
                $tabla2[$count]['iva'] = $tabla2[$count]['iva'] + floatval($factura->detalle_iva);
            }
            $ta0 = $ta0 + $tabla2[$count]['0'];
            $ta12 = $ta12 + $tabla2[$count]['12'];
            $iv = $iv + $tabla2[$count]['iva'];
            $count = $count +1;
            $countFacturaR = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
            ->select(DB::raw('COUNT(distinct factura_venta.factura_id) as cantidad'))->first();
            $facturasR = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
            ->select('detalle_fv.detalle_total', 'detalle_fv.detalle_iva')->get();
            $tabla2[$count]['cod'] = "41";
            $tabla2[$count]['tra'] = "COMPROBANTE DE VENTA EMITIDO POR REEMBOLSO";
            $tabla2[$count]['can'] = $countFacturaR->cantidad;
            $tabla2[$count]['12'] = 0;
            $tabla2[$count]['0'] = 0;
            $tabla2[$count]['iva'] = 0;
            foreach($facturasR as $factura){
                if(floatval($factura->detalle_iva) > 0){
                    $tabla2[$count]['12'] = $tabla2[$count]['12'] + floatval($factura->detalle_total);
                }else{
                    $tabla2[$count]['0'] = $tabla2[$count]['0'] + floatval($factura->detalle_total);
                }          
                $tabla2[$count]['iva'] = $tabla2[$count]['iva'] + floatval($factura->detalle_iva);
            }
            $ta0 = $ta0 + $tabla2[$count]['0'];
            $ta12 = $ta12 + $tabla2[$count]['12'];
            $iv = $iv + $tabla2[$count]['iva'];
            $count = $count +1;
            $nc = Nota_Credito::NCbyFecha($fechaInicio, $fechaFin)->select(DB::raw('COUNT(nc_id) as cantidad'), DB::raw('SUM(nc_tarifa0) as tarifa0'), DB::raw('SUM(nc_tarifa12) as tarifa12'), DB::raw('SUM(nc_iva) as iva'))->first();
            $tabla2[$count]['cod'] = "04";
            $tabla2[$count]['tra'] = "NOTA DE CRÉDITO";
            $tabla2[$count]['can'] = $nc->cantidad;
            $tabla2[$count]['0'] = $nc->tarifa0;
            $tabla2[$count]['12'] = $nc->tarifa12;
            $tabla2[$count]['iva'] = $nc->iva;
            $ta0 = $ta0 - $tabla2[$count]['0'];
            $ta12 = $ta12 - $tabla2[$count]['12'];
            $iv = $iv - $tabla2[$count]['iva'];
            $count = $count +1;
            $nd = Nota_Debito::NDbyFecha($fechaInicio, $fechaFin)->select(DB::raw('COUNT(nd_id) as cantidad'), DB::raw('SUM(nd_tarifa0) as tarifa0'), DB::raw('SUM(nd_tarifa12) as tarifa12'), DB::raw('SUM(nd_iva) as iva'))->first();
            $tabla2[$count]['cod'] = "05";
            $tabla2[$count]['tra'] = "NOTA DE DÉBITO";
            $tabla2[$count]['can'] = $nd->cantidad;
            $tabla2[$count]['0'] = $nd->tarifa0;
            $tabla2[$count]['12'] = $nd->tarifa12;
            $tabla2[$count]['iva'] = $nd->iva;
            $ta0 = $ta0 + $tabla2[$count]['0'];
            $ta12 = $ta12 + $tabla2[$count]['12'];
            $iv = $iv + $tabla2[$count]['iva'];
            $count = $count +1;

            $tabla2[$count]['tra'] = 'TOTAL';
            $tabla2[$count]['0'] = $ta0;
            $tabla2[$count]['12'] = $ta12;
            $tabla2[$count]['iva'] = $iv;
            /******************************************************/
            $tabla3 = null;
            $count = 1;
            $retB = 0;
            $retV = 0;
            foreach (Retencion_Compra::retbyFecha($fechaInicio, $fechaFin)->join('detalle_rc', 'detalle_rc.retencion_id', '=', 'retencion_compra.retencion_id')->join('concepto_retencion', 'concepto_retencion.concepto_id', '=', 'detalle_rc.concepto_id')
                    ->select('concepto_codigo', 'concepto_nombre', DB::raw('COUNT(detalle_id) as cantidad'), DB::raw('SUM(detalle_base) as base'), DB::raw('SUM(detalle_valor) as valor'))
                    ->where('detalle_tipo', '=', 'FUENTE')->where('retencion_compra.retencion_estado','=','1')->groupBy('concepto_codigo', 'concepto_nombre')->get() as $retenciones) {
                $tabla3[$count]['cod'] = $retenciones->concepto_codigo;
                $tabla3[$count]['tra'] = $retenciones->concepto_nombre;
                $tabla3[$count]['can'] = $retenciones->cantidad;
                $tabla3[$count]['base'] = $retenciones->base;
                $retB = $retB + $tabla3[$count]['base'];
                $tabla3[$count]['valor'] = $retenciones->valor;
                $retV = $retV + $tabla3[$count]['valor'];
                $count = $count +1;
            }
            $tabla3[$count]['tra'] = 'TOTAL';
            $tabla3[$count]['base'] = $retB;
            $tabla3[$count]['valor'] = $retV;
            /******************************************************/
            $tabla4 = null;
            $count = 1;
            $ret = 0;
            foreach (Retencion_Compra::retbyFecha($fechaInicio, $fechaFin)->join('detalle_rc', 'detalle_rc.retencion_id', '=', 'retencion_compra.retencion_id')->join('concepto_retencion', 'concepto_retencion.concepto_id', '=', 'detalle_rc.concepto_id')
                    ->select('concepto_nombre', DB::raw('SUM(detalle_valor) as valor'))->where('detalle_tipo', '=', 'IVA')->where('retencion_compra.retencion_estado','=','1')->groupBy('concepto_codigo', 'concepto_nombre')->get() as $retenciones) {
                $tabla4[$count]['tra'] = $retenciones->concepto_nombre;
                $tabla4[$count]['valor'] = $retenciones->valor;
                $ret = $ret + $tabla4[$count]['valor'] ;
                $count = $count +1;
            }
            $tabla4[$count]['tra'] = 'TOTAL';
            $tabla4[$count]['valor'] = $ret;
            /******************************************************/
            $tabla5 = null;
            $count = 1;
            $ret = 0;
            $tabla5[$count]['tra'] = 'Valor de IVA que le han retenido';
            $tabla5[$count]['valor'] = Retencion_Venta::RetByFecha($fechaInicio, $fechaFin)->join('detalle_rv', 'detalle_rv.retencion_id', '=', 'retencion_venta.retencion_id')->where('detalle_tipo', '=', 'IVA')->where('retencion_venta.retencion_estado','=','1')->sum('detalle_valor');
            $ret = $ret + $tabla5[$count]['valor'] ;
            $count = $count +1;
            $tabla5[$count]['tra'] = 'Valor de Renta que le han retenido';
            $tabla5[$count]['valor'] = Retencion_Venta::RetByFecha($fechaInicio, $fechaFin)->join('detalle_rv', 'detalle_rv.retencion_id', '=', 'retencion_venta.retencion_id')->where('detalle_tipo', '=', 'FUENTE')->where('retencion_venta.retencion_estado','=','1')->sum('detalle_valor');
            $ret = $ret + $tabla5[$count]['valor'] ;
            $count = $count +1;
            $tabla5[$count]['tra'] = 'TOTAL';
            $tabla5[$count]['valor'] = $ret;
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.ATS', ['tabla1'=>$tabla1,'tabla2'=>$tabla2,'tabla3'=>$tabla3,'tabla4'=>$tabla4,'tabla5'=>$tabla5,'desde'=>DateTime::createFromFormat('Y-m-d', $fechaInicio)->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $fechaFin)->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'ANEXO TRANSACCIONAL SIMPLIFICADO '.DateTime::createFromFormat('Y-m-d', $fechaInicio)->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $fechaFin)->format('d-m-Y');
            return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        } catch (\Exception $ex) {
            return redirect('atsSRI')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function generar(Request $request)
    {
        try {
            $ruta = $this->ats($request->get('idPeriodo'));
            return response()->download($ruta);
        } catch (\Exception $ex) {
            return redirect('atsSRI')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function ats($fechaFin)
    {
        try {
            $empresa = Empresa::empresa()->first();
            $fechaInicio = DateTime::createFromFormat('Y-m-d', $fechaFin)->format('Y').'-'.DateTime::createFromFormat('Y-m-d', $fechaFin)->format('m').'-01';
            $xml = xmlwriter_open_memory();
            xmlwriter_set_indent($xml, 1);
            $res = xmlwriter_set_indent_string($xml, ' ');
            //inicio del documento
            xmlwriter_start_document($xml, '1.0', 'UTF-8');
            //iva
            xmlwriter_start_element($xml, 'iva');
            //TipoIDInformante
            xmlwriter_start_element($xml, 'TipoIDInformante');
            xmlwriter_text($xml, 'R');
            xmlwriter_end_element($xml);
            // final 'TipoIDInformante'
            //IdInformante
            xmlwriter_start_element($xml, 'IdInformante');
            xmlwriter_text($xml, $empresa->empresa_ruc);
            xmlwriter_end_element($xml);
            // final 'IdInformante'
            //razonSocial
            xmlwriter_start_element($xml, 'razonSocial');
            xmlwriter_text($xml, $empresa->empresa_razonSocial);
            xmlwriter_end_element($xml);
            // final 'razonSocial'
            //Anio
            xmlwriter_start_element($xml, 'Anio');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $fechaFin)->format('Y'));
            xmlwriter_end_element($xml);
            // final 'Anio'
            //Mes
            xmlwriter_start_element($xml, 'Mes');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $fechaFin)->format('m'));
            xmlwriter_end_element($xml);
            // final 'Mes'
            //numEstabRuc
            xmlwriter_start_element($xml, 'numEstabRuc');
            xmlwriter_text($xml, substr(str_repeat(0, 3).Sucursal::Sucursales()->count('sucursal_id'), - 3));
            xmlwriter_end_element($xml);
            // final 'numEstabRuc'
            //totalVentas
            xmlwriter_start_element($xml, 'totalVentas');
            xmlwriter_text($xml, number_format(Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->select(DB::raw('SUM(factura_subtotal) - SUM(factura_descuento) as ventas'))->where('factura_venta.factura_emision','<>','ELECTRONICA')->first()->ventas, 2, '.', ''));
            xmlwriter_end_element($xml);
            // final 'totalVentas'
            //codigoOperativo
            xmlwriter_start_element($xml, 'codigoOperativo');
            xmlwriter_text($xml, 'IVA');
            xmlwriter_end_element($xml);
            // final 'codigoOperativo'
            //compras
            xmlwriter_start_element($xml, 'compras');
            foreach (Transaccion_Compra::TransaccionByFecha($fechaInicio, $fechaFin)->orderBy('transaccion_fecha', 'desc')->get() as $transaccion) {
                //detalleCompras
                xmlwriter_start_element($xml, 'detalleCompras');
                //codSustento
                xmlwriter_start_element($xml, 'codSustento');
                xmlwriter_text($xml, $transaccion->sustentoTributario->sustento_codigo);
                xmlwriter_end_element($xml);
                // final 'codSustento'
                //tpIdProv
                xmlwriter_start_element($xml, 'tpIdProv');
                xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($transaccion->proveedor->tipo_identificacion_id, 'Compra')->first()->transaccion_codigo);
                xmlwriter_end_element($xml);
                // final 'tpIdProv'
                //idProv
                xmlwriter_start_element($xml, 'idProv');
                xmlwriter_text($xml, $transaccion->proveedor->proveedor_ruc);
                xmlwriter_end_element($xml);
                // final 'idProv'
                //tipoComprobante
                xmlwriter_start_element($xml, 'tipoComprobante');
                xmlwriter_text($xml, $transaccion->tipoComprobante->tipo_comprobante_codigo);
                xmlwriter_end_element($xml);
                // final 'tipoComprobante'
                //parteRel
                xmlwriter_start_element($xml, 'parteRel');
                xmlwriter_text($xml, 'NO');
                xmlwriter_end_element($xml);
                // final 'parteRel'
                if (Transaccion_Identificacion::Identificacion($transaccion->proveedor->tipo_identificacion_id, 'Compra')->first()->transaccion_codigo == '03') {
                    //tipoProv
                    xmlwriter_start_element($xml, 'tipoProv');
                    xmlwriter_text($xml, $transaccion->proveedor->tipoSujeto->tipo_sujeto_codigo);
                    xmlwriter_end_element($xml);
                    // final 'tipoProv'
                    //denopr
                    xmlwriter_start_element($xml, 'denopr');
                    xmlwriter_text($xml, $transaccion->proveedor->proveedor_nombre);
                    xmlwriter_end_element($xml);
                    // final 'denopr'
                }
                //fechaRegistro
                xmlwriter_start_element($xml, 'fechaRegistro');
                xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $transaccion->transaccion_fecha)->format('d/m/Y'));
                xmlwriter_end_element($xml);
                // final 'fechaRegistro'
                //establecimiento
                xmlwriter_start_element($xml, 'establecimiento');
                xmlwriter_text($xml, substr($transaccion->transaccion_serie, 0, 3));
                xmlwriter_end_element($xml);
                // final 'establecimiento'
                //puntoEmision
                xmlwriter_start_element($xml, 'puntoEmision');
                xmlwriter_text($xml, substr($transaccion->transaccion_serie, 3, 3));
                xmlwriter_end_element($xml);
                // final 'puntoEmision'
                //secuencial
                xmlwriter_start_element($xml, 'secuencial');
                xmlwriter_text($xml, $transaccion->transaccion_secuencial);
                xmlwriter_end_element($xml);
                // final 'secuencial'
                //fechaEmision
                xmlwriter_start_element($xml, 'fechaEmision');
                xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $transaccion->transaccion_fecha)->format('d/m/Y'));
                xmlwriter_end_element($xml);
                // final 'fechaEmision'
                //autorizacion
                xmlwriter_start_element($xml, 'autorizacion');
                xmlwriter_text($xml, $transaccion->transaccion_autorizacion);
                xmlwriter_end_element($xml);
                // final 'autorizacion'
                //baseNoGraIva
                xmlwriter_start_element($xml, 'baseNoGraIva');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'baseNoGraIva'
                //baseImponible
                xmlwriter_start_element($xml, 'baseImponible');
                xmlwriter_text($xml, number_format($transaccion->transaccion_tarifa0, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImponible'
                //baseImpGrav
                xmlwriter_start_element($xml, 'baseImpGrav');
                xmlwriter_text($xml, number_format($transaccion->transaccion_tarifa12, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImpGrav'
                //baseImpExe
                xmlwriter_start_element($xml, 'baseImpExe');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'baseImpExe'
                //montoIce
                xmlwriter_start_element($xml, 'montoIce');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'montoIce'
                //montoIva
                xmlwriter_start_element($xml, 'montoIva');
                xmlwriter_text($xml, number_format($transaccion->transaccion_iva, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'montoIva'
                //valRetBien10
                xmlwriter_start_element($xml, 'valRetBien10');
                $valor = 0;
                if ($transaccion->retencionCompra) {
                    foreach ($transaccion->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '9') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valRetBien10'
                //valRetServ20
                xmlwriter_start_element($xml, 'valRetServ20');
                $valor = 0;
                if ($transaccion->retencionCompra) {
                    foreach ($transaccion->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '10') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valRetServ20'
                //valorRetBienes
                xmlwriter_start_element($xml, 'valorRetBienes');
                $valor = 0;
                if ($transaccion->retencionCompra) {
                    foreach ($transaccion->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '1') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valorRetBienes'
                //valRetServ50
                xmlwriter_start_element($xml, 'valRetServ50');
                $valor = 0;
                if ($transaccion->retencionCompra) {
                    foreach ($transaccion->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '11') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valRetServ50'
                //valorRetServicios
                xmlwriter_start_element($xml, 'valorRetServicios');
                $valor = 0;
                if ($transaccion->retencionCompra) {
                    foreach ($transaccion->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '2') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valorRetServicios'
                //valRetServ100
                xmlwriter_start_element($xml, 'valRetServ100');
                $valor = 0;
                if ($transaccion->retencionCompra) {
                    foreach ($transaccion->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '3') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valRetServ100'
                //totbasesImpReemb
                xmlwriter_start_element($xml, 'totbasesImpReemb');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'totbasesImpReemb'
                //pagoExterior
                xmlwriter_start_element($xml, 'pagoExterior');
                    //pagoLocExt
                    xmlwriter_start_element($xml, 'pagoLocExt');
                    xmlwriter_text($xml, '01');//revisar
                    xmlwriter_end_element($xml);
                    // final 'pagoLocExt'
                    //paisEfecPago
                    xmlwriter_start_element($xml, 'paisEfecPago');
                    xmlwriter_text($xml, 'NA');//revisar
                    xmlwriter_end_element($xml);
                    // final 'paisEfecPago'
                    //aplicConvDobTrib
                    xmlwriter_start_element($xml, 'aplicConvDobTrib');
                    xmlwriter_text($xml, 'NA');//revisar
                    xmlwriter_end_element($xml);
                    // final 'aplicConvDobTrib'
                    //pagExtSujRetNorLeg
                    xmlwriter_start_element($xml, 'pagExtSujRetNorLeg');
                    xmlwriter_text($xml, 'NA');//revisar
                    xmlwriter_end_element($xml);
                    // final 'pagExtSujRetNorLeg'
                xmlwriter_end_element($xml);
                // final 'pagoExterior'
                if ($transaccion->tipoComprobante->tipo_comprobante_codigo <> '04' and $transaccion->transaccion_total > 1000) {
                    //formasDePago
                    xmlwriter_start_element($xml, 'formasDePago');
                    //formaPago
                    xmlwriter_start_element($xml, 'formaPago');
                    //xmlwriter_text($xml, $transaccion->formaPago->forma_pago_codigo);
                    xmlwriter_text($xml, '20');
                    xmlwriter_end_element($xml);
                    // final 'formaPago'
                    xmlwriter_end_element($xml);
                    // final 'formasDePago'
                }
                //air
                if ($transaccion->retencionCompra) {
                    xmlwriter_start_element($xml, 'air');
                    foreach ($transaccion->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'FUENTE') {
                            //detalleAir
                            xmlwriter_start_element($xml, 'detalleAir');
                            //codRetAir
                            xmlwriter_start_element($xml, 'codRetAir');
                            xmlwriter_text($xml, $detalle->conceptoRetencion->concepto_codigo);
                            xmlwriter_end_element($xml);
                            // final 'codRetAir'
                            //baseImpAir
                            xmlwriter_start_element($xml, 'baseImpAir');
                            xmlwriter_text($xml, number_format($detalle->detalle_base, 2, '.', ''));
                            xmlwriter_end_element($xml);
                            // final 'baseImpAir'
                            //porcentajeAir
                            xmlwriter_start_element($xml, 'porcentajeAir');
                            xmlwriter_text($xml, $detalle->detalle_porcentaje);
                            xmlwriter_end_element($xml);
                            // final 'porcentajeAir'
                            //valRetAir
                            xmlwriter_start_element($xml, 'valRetAir');
                            xmlwriter_text($xml, number_format($detalle->detalle_valor, 2, '.', ''));
                            xmlwriter_end_element($xml);
                            // final 'valRetAir'

                            if($detalle->conceptoRetencion->concepto_codigo == '327' or $detalle->conceptoRetencion->concepto_codigo == '330' or
                            $detalle->conceptoRetencion->concepto_codigo == '504A' or $detalle->conceptoRetencion->concepto_codigo == '504D'){
                            
                                //fechaPagoDiv
                                xmlwriter_start_element($xml, 'fechaPagoDiv');
                                xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $transaccion->transaccion_fecha)->format('d/m/Y'));
                                xmlwriter_end_element($xml);
                                // final 'fechaPagoDiv'
                                //imRentaSoc
                                xmlwriter_start_element($xml, 'imRentaSoc');
                                xmlwriter_text($xml, number_format(0.00, 2, '.', '')); //revisar
                                xmlwriter_end_element($xml);
                                // final 'imRentaSoc'
                                //anioUtDiv
                                xmlwriter_start_element($xml, 'anioUtDiv');
                                xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $transaccion->transaccion_fecha)->format('Y'));
                                xmlwriter_end_element($xml);
                                // final 'anioUtDiv'
                            }
                            xmlwriter_end_element($xml);
                            // final 'detalleAir'
                        }
                    }
                    xmlwriter_end_element($xml);
                    // final 'air'
                    //estabRetencion1
                    xmlwriter_start_element($xml, 'estabRetencion1');
                    xmlwriter_text($xml, substr($transaccion->retencionCompra->retencion_serie, 0, 3));
                    xmlwriter_end_element($xml);
                    // final 'estabRetencion1'
                    //ptoEmiRetencion1
                    xmlwriter_start_element($xml, 'ptoEmiRetencion1');
                    xmlwriter_text($xml, substr($transaccion->retencionCompra->retencion_serie, 3, 3));
                    xmlwriter_end_element($xml);
                    // final 'ptoEmiRetencion1'
                    //secRetencion1
                    xmlwriter_start_element($xml, 'secRetencion1');
                    xmlwriter_text($xml, $transaccion->retencionCompra->retencion_secuencial);
                    xmlwriter_end_element($xml);
                    // final 'secRetencion1'
                    //autRetencion1
                    xmlwriter_start_element($xml, 'autRetencion1');
                    xmlwriter_text($xml, $transaccion->retencionCompra->retencion_autorizacion);
                    xmlwriter_end_element($xml);
                    // final 'autRetencion1'
                    //fechaEmiRet1
                    xmlwriter_start_element($xml, 'fechaEmiRet1');
                    xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $transaccion->retencionCompra->retencion_fecha)->format('d/m/Y'));
                    xmlwriter_end_element($xml);
                    // final 'fechaEmiRet1'
                }
                if ($transaccion->tipoComprobante->tipo_comprobante_codigo == '04' or $transaccion->tipoComprobante->tipo_comprobante_codigo == '05'
                            or $transaccion->tipoComprobante->tipo_comprobante_codigo == '41' or $transaccion->tipoComprobante->tipo_comprobante_codigo == '47'
                            or $transaccion->tipoComprobante->tipo_comprobante_codigo == '48') {
                    //docModificado
                    xmlwriter_start_element($xml, 'docModificado');
                    xmlwriter_text($xml, '01');
                    xmlwriter_end_element($xml);
                    // final 'docModificado'
                    //estabModificado
                    xmlwriter_start_element($xml, 'estabModificado');
                    if(isset($transaccion->facturaModificar->transaccion_serie)){
                        xmlwriter_text($xml, substr($transaccion->facturaModificar->transaccion_serie, 0, 3));
                    }else{
                        xmlwriter_text($xml, substr($transaccion->transaccion_factura_manual, 0, 3));
                    }                    
                    xmlwriter_end_element($xml);
                    // final 'estabModificado'
                    //ptoEmiModificado
                    xmlwriter_start_element($xml, 'ptoEmiModificado');
                    if(isset($transaccion->facturaModificar->transaccion_serie)){
                        xmlwriter_text($xml, substr($transaccion->facturaModificar->transaccion_serie, 3, 3));
                    }else{
                        xmlwriter_text($xml, substr($transaccion->transaccion_factura_manual, 4, 3));
                    }
                    xmlwriter_end_element($xml);
                    // final 'ptoEmiModificado'
                    //secModificado
                    xmlwriter_start_element($xml, 'secModificado');
                    if(isset($transaccion->facturaModificar->transaccion_serie)){
                        xmlwriter_text($xml, $transaccion->facturaModificar->transaccion_secuencial);
                    }else{
                        xmlwriter_text($xml, substr($transaccion->transaccion_factura_manual, 8, 9));
                    }
                    xmlwriter_end_element($xml);
                    // final 'secModificado'
                    //autModificado
                    xmlwriter_start_element($xml, 'autModificado');
                    if(isset($transaccion->facturaModificar->transaccion_serie)){
                        xmlwriter_text($xml, $transaccion->facturaModificar->transaccion_autorizacion);
                    }else{
                        xmlwriter_text($xml, $transaccion->transaccion_autorizacion_manual);
                    }
                    xmlwriter_end_element($xml);
                    // final 'autModificado'
                }
                xmlwriter_end_element($xml);
                // final 'detalleCompras'
            }
            foreach (Liquidacion_Compra::LCbyFecha($fechaInicio, $fechaFin)->orderBy('lc_numero', 'asc')->get() as $lc) {
                //detalleCompras
                xmlwriter_start_element($xml, 'detalleCompras');
                //codSustento
                xmlwriter_start_element($xml, 'codSustento');
                xmlwriter_text($xml, $lc->sustentoTributario->sustento_codigo);
                xmlwriter_end_element($xml);
                // final 'codSustento'
                //tpIdProv
                xmlwriter_start_element($xml, 'tpIdProv');
                xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($lc->proveedor->tipo_identificacion_id, 'Compra')->first()->transaccion_codigo);
                xmlwriter_end_element($xml);
                // final 'tpIdProv'
                //idProv
                xmlwriter_start_element($xml, 'idProv');
                xmlwriter_text($xml, $lc->proveedor->proveedor_ruc);
                xmlwriter_end_element($xml);
                // final 'idProv'
                //tipoComprobante
                xmlwriter_start_element($xml, 'tipoComprobante');
                xmlwriter_text($xml, $lc->rangoDocumento->tipoComprobante->tipo_comprobante_codigo);
                xmlwriter_end_element($xml);
                // final 'tipoComprobante'
                //parteRel
                xmlwriter_start_element($xml, 'parteRel');
                xmlwriter_text($xml, 'NO');
                xmlwriter_end_element($xml);
                // final 'parteRel'
                if (Transaccion_Identificacion::Identificacion($lc->proveedor->tipo_identificacion_id, 'Compra')->first()->transaccion_codigo == '03') {
                    //tipoProv
                    xmlwriter_start_element($xml, 'tipoProv');
                    xmlwriter_text($xml, $lc->proveedor->tipoSujeto->tipo_sujeto_codigo);
                    xmlwriter_end_element($xml);
                    // final 'tipoProv'
                    //denopr
                    xmlwriter_start_element($xml, 'denopr');
                    xmlwriter_text($xml, $lc->proveedor->proveedor_nombre);
                    xmlwriter_end_element($xml);
                    // final 'denopr'
                }
                //fechaRegistro
                xmlwriter_start_element($xml, 'fechaRegistro');
                xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $lc->lc_fecha)->format('d/m/Y'));
                xmlwriter_end_element($xml);
                // final 'fechaRegistro'
                //establecimiento
                xmlwriter_start_element($xml, 'establecimiento');
                xmlwriter_text($xml, substr($lc->lc_serie, 0, 3));
                xmlwriter_end_element($xml);
                // final 'establecimiento'
                //puntoEmision
                xmlwriter_start_element($xml, 'puntoEmision');
                xmlwriter_text($xml, substr($lc->lc_serie, 3, 3));
                xmlwriter_end_element($xml);
                // final 'puntoEmision'
                //secuencial
                xmlwriter_start_element($xml, 'secuencial');
                xmlwriter_text($xml, $lc->lc_secuencial);
                xmlwriter_end_element($xml);
                // final 'secuencial'
                //fechaEmision
                xmlwriter_start_element($xml, 'fechaEmision');
                xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $lc->lc_fecha)->format('d/m/Y'));
                xmlwriter_end_element($xml);
                // final 'fechaEmision'
                //autorizacion
                xmlwriter_start_element($xml, 'autorizacion');
                xmlwriter_text($xml, $lc->lc_autorizacion);
                xmlwriter_end_element($xml);
                // final 'autorizacion'
                //baseNoGraIva
                xmlwriter_start_element($xml, 'baseNoGraIva');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'baseNoGraIva'
                //baseImponible
                xmlwriter_start_element($xml, 'baseImponible');
                xmlwriter_text($xml, number_format($lc->lc_tarifa0, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImponible'
                //baseImpGrav
                xmlwriter_start_element($xml, 'baseImpGrav');
                xmlwriter_text($xml, number_format($lc->lc_tarifa12, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImpGrav'
                //baseImpExe
                xmlwriter_start_element($xml, 'baseImpExe');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'baseImpExe'
                //montoIce
                xmlwriter_start_element($xml, 'montoIce');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'montoIce'
                //montoIva
                xmlwriter_start_element($xml, 'montoIva');
                xmlwriter_text($xml, number_format($lc->lc_iva, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'montoIva'
                //valRetBien10
                xmlwriter_start_element($xml, 'valRetBien10');
                $valor = 0;
                if ($lc->retencionCompra) {
                    foreach ($lc->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '9') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valRetBien10'
                //valRetServ20
                xmlwriter_start_element($xml, 'valRetServ20');
                $valor = 0;
                if ($lc->retencionCompra) {
                    foreach ($lc->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '10') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valRetServ20'
                //valorRetBienes
                xmlwriter_start_element($xml, 'valorRetBienes');
                $valor = 0;
                if ($lc->retencionCompra) {
                    foreach ($lc->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '1') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valorRetBienes'
                //valRetServ50
                xmlwriter_start_element($xml, 'valRetServ50');
                $valor = 0;
                if ($lc->retencionCompra) {
                    foreach ($lc->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '11') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valRetServ50'
                //valorRetServicios
                xmlwriter_start_element($xml, 'valorRetServicios');
                $valor = 0;
                if ($lc->retencionCompra) {
                    foreach ($lc->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '2') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valorRetServicios'
                //valRetServ100
                xmlwriter_start_element($xml, 'valRetServ100');
                $valor = 0;
                if ($lc->retencionCompra) {
                    foreach ($lc->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_codigo == '3') {
                            $valor =  $detalle->detalle_valor;
                        }
                    }
                }
                if ($valor == 0) {
                    xmlwriter_text($xml, '0.00');
                } else {
                    xmlwriter_text($xml, number_format($valor, 2, '.', ''));
                }
                            
                xmlwriter_end_element($xml);
                // final 'valRetServ100'
                //pagoExterior
                xmlwriter_start_element($xml, 'pagoExterior');
                //pagoLocExt
                xmlwriter_start_element($xml, 'pagoLocExt');
                xmlwriter_text($xml, '01');//revisar
                xmlwriter_end_element($xml);
                // final 'pagoLocExt'
                xmlwriter_end_element($xml);
                // final 'pagoExterior'
                if ($lc->rangoDocumento->tipoComprobante->tipo_comprobante_codigo <> '04' and $lc->lc_total > 1000) {
                    //formasDePago
                    xmlwriter_start_element($xml, 'formasDePago');
                    //formaPago
                    xmlwriter_start_element($xml, 'formaPago');
                    xmlwriter_text($xml, $lc->formaPago->forma_pago_codigo);
                    xmlwriter_end_element($xml);
                    // final 'formaPago'
                    xmlwriter_end_element($xml);
                    // final 'formasDePago'
                }
                //air
                if ($lc->retencionCompra) {
                    xmlwriter_start_element($xml, 'air');
                    foreach ($lc->retencionCompra->detalles as $detalle) {
                        if ($detalle->detalle_tipo == 'FUENTE') {
                            //detalleAir
                            xmlwriter_start_element($xml, 'detalleAir');
                            //codRetAir
                            xmlwriter_start_element($xml, 'codRetAir');
                            xmlwriter_text($xml, $detalle->conceptoRetencion->concepto_codigo);
                            xmlwriter_end_element($xml);
                            // final 'codRetAir'
                            //baseImpAir
                            xmlwriter_start_element($xml, 'baseImpAir');
                            xmlwriter_text($xml, number_format($detalle->detalle_base, 2, '.', ''));
                            xmlwriter_end_element($xml);
                            // final 'baseImpAir'
                            //porcentajeAir
                            xmlwriter_start_element($xml, 'porcentajeAir');
                            xmlwriter_text($xml, $detalle->detalle_porcentaje);
                            xmlwriter_end_element($xml);
                            // final 'porcentajeAir'
                            //valRetAir
                            xmlwriter_start_element($xml, 'valRetAir');
                            xmlwriter_text($xml, number_format($detalle->detalle_valor, 2, '.', ''));
                            xmlwriter_end_element($xml);
                            // final 'valRetAir'
                            xmlwriter_end_element($xml);
                            // final 'detalleAir'
                        }
                    }
                    xmlwriter_end_element($xml);
                    // final 'air'
                    //estabRetencion1
                    xmlwriter_start_element($xml, 'estabRetencion1');
                    xmlwriter_text($xml, substr($lc->retencionCompra->retencion_serie, 0, 3));
                    xmlwriter_end_element($xml);
                    // final 'estabRetencion1'
                    //ptoEmiRetencion1
                    xmlwriter_start_element($xml, 'ptoEmiRetencion1');
                    xmlwriter_text($xml, substr($lc->retencionCompra->retencion_serie, 3, 3));
                    xmlwriter_end_element($xml);
                    // final 'ptoEmiRetencion1'
                    //secRetencion1
                    xmlwriter_start_element($xml, 'secRetencion1');
                    xmlwriter_text($xml, $lc->retencionCompra->retencion_secuencial);
                    xmlwriter_end_element($xml);
                    // final 'secRetencion1'
                    //autRetencion1
                    xmlwriter_start_element($xml, 'autRetencion1');
                    xmlwriter_text($xml, $lc->retencionCompra->retencion_autorizacion);
                    xmlwriter_end_element($xml);
                    // final 'autRetencion1'
                    //fechaEmiRet1
                    xmlwriter_start_element($xml, 'fechaEmiRet1');
                    xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $lc->retencionCompra->retencion_fecha)->format('d/m/Y'));
                    xmlwriter_end_element($xml);
                    // final 'fechaEmiRet1'
                }
                xmlwriter_end_element($xml);
                // final 'detalleCompras'
            }
            xmlwriter_end_element($xml);
            // final 'compras'
            //ventas
            xmlwriter_start_element($xml, 'ventas');
            foreach (Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->select('cliente_id', DB::raw('COUNT(factura_id) as cantidad'), DB::raw('SUM(factura_tarifa0) as tarifa0'), DB::raw('SUM(factura_tarifa12) as tarifa12'), DB::raw('SUM(factura_iva) as iva'),'factura_emision')->groupBy('cliente_id')->groupBy('factura_emision')->get() as $factura) {
                /*Ecnontrar facturas de reembolso por cliente*/
                $totRem0 = 0;
                $totRem12 = 0;
                $totRemIva = 0;
                $countFacturaR = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
                ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
                ->where('factura_venta.cliente_id','=',$factura->cliente->cliente_id)->select(DB::raw('COUNT(distinct factura_venta.factura_id) as cantidad'))->first();
                if(isset($countFacturaR->cantidad)){
                    $countFacturaR = $countFacturaR->cantidad;
                }else{
                    $countFacturaR = 0;
                }

                $facturasR = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
                ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
                ->where('factura_venta.cliente_id','=',$factura->cliente->cliente_id)->select('detalle_fv.detalle_total', 'detalle_fv.detalle_iva')->get();

                foreach($facturasR as $facturaR){
                    if(floatval($facturaR->detalle_iva) > 0){
                        $totRem12 = $totRem12 + floatval($facturaR->detalle_total);
                    }else{
                        $totRem0 = $totRem0 + floatval($facturaR->detalle_total);
                    }          
                    $totRemIva = $totRemIva + floatval($facturaR->detalle_iva);
                }
                /*Fin ecnontrar facturas de reembolso por cliente*/
                //detalleVentas
                xmlwriter_start_element($xml, 'detalleVentas');
                //tpIdCliente
                xmlwriter_start_element($xml, 'tpIdCliente');
                xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($factura->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
                xmlwriter_end_element($xml);
                // final 'tpIdCliente'
                //idCliente
                xmlwriter_start_element($xml, 'idCliente');
                xmlwriter_text($xml, $factura->cliente->cliente_cedula);
                xmlwriter_end_element($xml);
                // final 'idCliente'
                if(Transaccion_Identificacion::Identificacion($factura->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo != '07'){
                    //parteRelVtas
                    xmlwriter_start_element($xml, 'parteRelVtas');
                    xmlwriter_text($xml, 'NO');//revisar
                    xmlwriter_end_element($xml);
                    // final 'parteRelVtas'
                }
                if (Transaccion_Identificacion::Identificacion($factura->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo == '06') {
                    //tipoCliente
                    xmlwriter_start_element($xml, 'tipoCliente');
                    xmlwriter_text($xml, 'REVISAR');
                    xmlwriter_end_element($xml);
                    // final 'tipoCliente'
                    //DenoCli
                    xmlwriter_start_element($xml, 'DenoCli');
                    xmlwriter_text($xml, 'REVISAR');
                    xmlwriter_end_element($xml);
                    // final 'DenoCli'
                }
                //tipoComprobante
                xmlwriter_start_element($xml, 'tipoComprobante');
                xmlwriter_text($xml, '18');
                xmlwriter_end_element($xml);
                // final 'tipoComprobante'
                //tipoEmision
                xmlwriter_start_element($xml, 'tipoEmision');
                if($factura->factura_emision == 'ELECTRONICA'){
                    xmlwriter_text($xml, 'E');
                }else{
                    xmlwriter_text($xml, 'F');
                }
                xmlwriter_end_element($xml);
                // final 'tipoEmision'
                //numeroComprobantes
                xmlwriter_start_element($xml, 'numeroComprobantes');
                xmlwriter_text($xml, ($factura->cantidad - $countFacturaR));
                xmlwriter_end_element($xml);
                // final 'numeroComprobantes'
                //baseNoGraIva
                xmlwriter_start_element($xml, 'baseNoGraIva');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'baseNoGraIva'
                //baseImponible
                xmlwriter_start_element($xml, 'baseImponible');
                xmlwriter_text($xml, number_format($factura->tarifa0 - $totRem0, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImponible'
                //baseImpGrav
                xmlwriter_start_element($xml, 'baseImpGrav');
                xmlwriter_text($xml, number_format($factura->tarifa12 - $totRem12, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImpGrav'
                //montoIva
                xmlwriter_start_element($xml, 'montoIva');
                xmlwriter_text($xml, number_format($factura->iva  - $totRemIva, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'montoIva'
                //montoIce
                xmlwriter_start_element($xml, 'montoIce');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'montoIce'
                //valorRetIva
                xmlwriter_start_element($xml, 'valorRetIva');
                xmlwriter_text($xml, number_format(Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('retencion_venta', 'retencion_venta.factura_id', '=', 'factura_venta.factura_id')->join('detalle_rv', 'detalle_rv.retencion_id', '=', 'retencion_venta.retencion_id')->where('factura_venta.cliente_id', '=', $factura->cliente->cliente_id)->where('detalle_rv.detalle_tipo', '=', 'IVA')->select(DB::raw('SUM(detalle_valor) as valor'))->first()->valor, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'valorRetIva'
                //valorRetRenta
                xmlwriter_start_element($xml, 'valorRetRenta');
                xmlwriter_text($xml, number_format(Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('retencion_venta', 'retencion_venta.factura_id', '=', 'factura_venta.factura_id')->join('detalle_rv', 'detalle_rv.retencion_id', '=', 'retencion_venta.retencion_id')->where('factura_venta.cliente_id', '=', $factura->cliente->cliente_id)->where('detalle_rv.detalle_tipo', '=', 'FUENTE')->select(DB::raw('SUM(detalle_valor) as valor'))->first()->valor, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'valorRetRenta'
                //formasDePago
                xmlwriter_start_element($xml, 'formasDePago');
                foreach (Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->where('factura_venta.cliente_id', '=', $factura->cliente->cliente_id)->select('forma_pago_id')->distinct()->get() as $formaPago) {
                    //formaPago
                    xmlwriter_start_element($xml, 'formaPago');
                    xmlwriter_text($xml, $formaPago->formaPago->forma_pago_codigo);
                    xmlwriter_end_element($xml);
                    // final 'formaPago'
                }
                                
                xmlwriter_end_element($xml);
                // final 'formasDePago'
                xmlwriter_end_element($xml);
                // final 'detalleVentas'
            }
            //ventas de reembolso
            $clientesReembolso = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
            ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
            ->select('factura_venta.cliente_id')->distinct()->get();
            foreach($clientesReembolso as $clienteReembolso){
                $totRem0 = 0;
                $totRem12 = 0;
                $totRemIva = 0;
                $countFacturaR = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
                ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
                ->where('factura_venta.cliente_id','=',$clienteReembolso->cliente->cliente_id)->select(DB::raw('COUNT(distinct factura_venta.factura_id) as cantidad'))->first();
                $countFacturaR = $countFacturaR->cantidad;
                $facturasR = Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
                ->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('producto_nombre','like','%REEMBOLSO DE GASTO%')
                ->where('factura_venta.cliente_id','=',$clienteReembolso->cliente->cliente_id)->select('detalle_fv.detalle_total', 'detalle_fv.detalle_iva')->get();

                foreach($facturasR as $facturaR){
                    if(floatval($facturaR->detalle_iva) > 0){
                        $totRem12 = $totRem12 + floatval($facturaR->detalle_total);
                    }else{
                        $totRem0 = $totRem0 + floatval($facturaR->detalle_total);
                    }          
                    $totRemIva = $totRemIva + floatval($facturaR->detalle_iva);
                }

                //detalleVentas
                xmlwriter_start_element($xml, 'detalleVentas');
                //tpIdCliente
                xmlwriter_start_element($xml, 'tpIdCliente');
                xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($clienteReembolso->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
                xmlwriter_end_element($xml);
                // final 'tpIdCliente'
                //idCliente
                xmlwriter_start_element($xml, 'idCliente');
                xmlwriter_text($xml, $clienteReembolso->cliente->cliente_cedula);
                xmlwriter_end_element($xml);
                // final 'idCliente'
                if(Transaccion_Identificacion::Identificacion($clienteReembolso->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo != '07'){
                    //parteRelVtas
                    xmlwriter_start_element($xml, 'parteRelVtas');
                    xmlwriter_text($xml, 'NO');//revisar
                    xmlwriter_end_element($xml);
                    // final 'parteRelVtas'
                }
                if (Transaccion_Identificacion::Identificacion($clienteReembolso->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo == '06') {
                    //tipoCliente
                    xmlwriter_start_element($xml, 'tipoCliente');
                    xmlwriter_text($xml, 'REVISAR');
                    xmlwriter_end_element($xml);
                    // final 'tipoCliente'
                    //DenoCli
                    xmlwriter_start_element($xml, 'DenoCli');
                    xmlwriter_text($xml, 'REVISAR');
                    xmlwriter_end_element($xml);
                    // final 'DenoCli'
                }
                //tipoComprobante
                xmlwriter_start_element($xml, 'tipoComprobante');
                xmlwriter_text($xml, '41');
                xmlwriter_end_element($xml);
                // final 'tipoComprobante'
                //tipoEmision
                xmlwriter_start_element($xml, 'tipoEmision');
                if($factura->factura_emision == 'ELECTRONICA'){
                    xmlwriter_text($xml, 'E');
                }else{
                    xmlwriter_text($xml, 'F');
                }
                xmlwriter_end_element($xml);
                // final 'tipoEmision'
                //numeroComprobantes
                xmlwriter_start_element($xml, 'numeroComprobantes');
                xmlwriter_text($xml, ($countFacturaR));
                xmlwriter_end_element($xml);
                // final 'numeroComprobantes'
                //baseNoGraIva
                xmlwriter_start_element($xml, 'baseNoGraIva');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'baseNoGraIva'
                //baseImponible
                xmlwriter_start_element($xml, 'baseImponible');
                xmlwriter_text($xml, number_format($totRem0, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImponible'
                //baseImpGrav
                xmlwriter_start_element($xml, 'baseImpGrav');
                xmlwriter_text($xml, number_format($totRem12, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImpGrav'
                //montoIva
                xmlwriter_start_element($xml, 'montoIva');
                xmlwriter_text($xml, number_format($totRemIva, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'montoIva'
                //montoIce
                xmlwriter_start_element($xml, 'montoIce');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'montoIce'
                //valorRetIva
                xmlwriter_start_element($xml, 'valorRetIva');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'valorRetIva'
                //valorRetRenta
                xmlwriter_start_element($xml, 'valorRetRenta');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'valorRetRenta'
                //formasDePago
                xmlwriter_start_element($xml, 'formasDePago');
                foreach (Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->where('factura_venta.cliente_id', '=', $clienteReembolso->cliente->cliente_id)->select('forma_pago_id')->distinct()->get() as $formaPago) {
                    //formaPago
                    xmlwriter_start_element($xml, 'formaPago');
                    xmlwriter_text($xml, $formaPago->formaPago->forma_pago_codigo);
                    xmlwriter_end_element($xml);
                    // final 'formaPago'
                }
                                
                xmlwriter_end_element($xml);
                // final 'formasDePago'
                xmlwriter_end_element($xml);
                // final 'detalleVentas'
            }
            foreach (Nota_Credito::NCbyFecha($fechaInicio, $fechaFin)->join('factura_venta', 'factura_venta.factura_id', '=', 'nota_credito.factura_id')->select('factura_venta.cliente_id', DB::raw('COUNT(nc_id) as cantidad'), DB::raw('SUM(nc_tarifa0) as tarifa0'), DB::raw('SUM(nc_tarifa12) as tarifa12'), DB::raw('SUM(nc_iva) as iva'),'nc_emision')->groupBy('factura_venta.cliente_id')->groupBy('nc_emision')->get() as $nc) {
                //detalleVentas
                xmlwriter_start_element($xml, 'detalleVentas');
                //tpIdCliente
                xmlwriter_start_element($xml, 'tpIdCliente');
                xmlwriter_text($xml, Transaccion_Identificacion::Identificacion(Cliente::Cliente($nc->cliente_id)->first()->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
                xmlwriter_end_element($xml);
                // final 'tpIdCliente'
                //idCliente
                xmlwriter_start_element($xml, 'idCliente');
                xmlwriter_text($xml, Cliente::Cliente($nc->cliente_id)->first()->cliente_cedula);
                xmlwriter_end_element($xml);
                // final 'idCliente'
                //parteRelVtas
                xmlwriter_start_element($xml, 'parteRelVtas');
                xmlwriter_text($xml, 'NO');//revisar
                xmlwriter_end_element($xml);
                // final 'parteRelVtas'
                if (Transaccion_Identificacion::Identificacion(Cliente::Cliente($nc->cliente_id)->first()->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo == '06') {
                    //tipoCliente
                    xmlwriter_start_element($xml, 'tipoCliente');
                    xmlwriter_text($xml, 'REVISAR');
                    xmlwriter_end_element($xml);
                    // final 'tipoCliente'
                    //DenoCli
                    xmlwriter_start_element($xml, 'DenoCli');
                    xmlwriter_text($xml, 'REVISAR');
                    xmlwriter_end_element($xml);
                    // final 'DenoCli'
                }
                //tipoComprobante
                xmlwriter_start_element($xml, 'tipoComprobante');
                xmlwriter_text($xml, '04');
                xmlwriter_end_element($xml);
                // final 'tipoComprobante'
                //tipoEmision
                xmlwriter_start_element($xml, 'tipoEmision');
                if($nc->nc_emision == 'ELECTRONICA'){
                    xmlwriter_text($xml, 'E');
                }else{
                    xmlwriter_text($xml, 'F');
                }
                xmlwriter_end_element($xml);
                // final 'tipoEmision'
                //numeroComprobantes
                xmlwriter_start_element($xml, 'numeroComprobantes');
                xmlwriter_text($xml, $nc->cantidad);
                xmlwriter_end_element($xml);
                // final 'numeroComprobantes'
                //baseNoGraIva
                xmlwriter_start_element($xml, 'baseNoGraIva');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'baseNoGraIva'
                //baseImponible
                xmlwriter_start_element($xml, 'baseImponible');
                xmlwriter_text($xml, number_format($nc->tarifa0, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImponible'
                //baseImpGrav
                xmlwriter_start_element($xml, 'baseImpGrav');
                xmlwriter_text($xml, number_format($nc->tarifa12, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImpGrav'
                //montoIva
                xmlwriter_start_element($xml, 'montoIva');
                xmlwriter_text($xml, number_format($nc->iva, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'montoIva'
                //montoIce
                xmlwriter_start_element($xml, 'montoIce');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'montoIce'
                //valorRetIva
                xmlwriter_start_element($xml, 'valorRetIva');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'valorRetIva'
                //valorRetRenta
                xmlwriter_start_element($xml, 'valorRetRenta');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'valorRetRenta'
                xmlwriter_end_element($xml);
                // final 'detalleVentas'
            }
            foreach (Nota_Debito::NDbyFecha($fechaInicio, $fechaFin)->join('factura_venta', 'factura_venta.factura_id', '=', 'nota_debito.factura_id')->select('factura_venta.cliente_id', DB::raw('COUNT(nd_id) as cantidad'), DB::raw('SUM(nd_tarifa0) as tarifa0'), DB::raw('SUM(nd_tarifa12) as tarifa12'), DB::raw('SUM(nd_iva) as iva'),'nd_emision')->groupBy('factura_venta.cliente_id')->groupBy('nd_emision')->get() as $nd) {
                //detalleVentas
                xmlwriter_start_element($xml, 'detalleVentas');
                //tpIdCliente
                xmlwriter_start_element($xml, 'tpIdCliente');
                xmlwriter_text($xml, Transaccion_Identificacion::Identificacion(Cliente::Cliente($nd->cliente_id)->first()->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
                xmlwriter_end_element($xml);
                // final 'tpIdCliente'
                //idCliente
                xmlwriter_start_element($xml, 'idCliente');
                xmlwriter_text($xml, Cliente::Cliente($nd->cliente_id)->first()->cliente_cedula);
                xmlwriter_end_element($xml);
                // final 'idCliente'
                //parteRelVtas
                xmlwriter_start_element($xml, 'parteRelVtas');
                xmlwriter_text($xml, 'NO');//revisar
                xmlwriter_end_element($xml);
                // final 'parteRelVtas'
                if (Transaccion_Identificacion::Identificacion(Cliente::Cliente($nd->cliente_id)->first()->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo == '06') {
                    //tipoCliente
                    xmlwriter_start_element($xml, 'tipoCliente');
                    xmlwriter_text($xml, 'REVISAR');
                    xmlwriter_end_element($xml);
                    // final 'tipoCliente'
                    //DenoCli
                    xmlwriter_start_element($xml, 'DenoCli');
                    xmlwriter_text($xml, 'REVISAR');
                    xmlwriter_end_element($xml);
                    // final 'DenoCli'
                }
                //tipoComprobante
                xmlwriter_start_element($xml, 'tipoComprobante');
                xmlwriter_text($xml, '05');
                xmlwriter_end_element($xml);
                // final 'tipoComprobante'
                //tipoEmision
                xmlwriter_start_element($xml, 'tipoEmision');
                if($nd->nd_emision == 'ELECTRONICA'){
                    xmlwriter_text($xml, 'E');
                }else{
                    xmlwriter_text($xml, 'F');
                }
                xmlwriter_end_element($xml);
                // final 'tipoEmision'
                //numeroComprobantes
                xmlwriter_start_element($xml, 'numeroComprobantes');
                xmlwriter_text($xml, $nd->cantidad);
                xmlwriter_end_element($xml);
                // final 'numeroComprobantes'
                //baseNoGraIva
                xmlwriter_start_element($xml, 'baseNoGraIva');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'baseNoGraIva'
                //baseImponible
                xmlwriter_start_element($xml, 'baseImponible');
                xmlwriter_text($xml, number_format($nd->tarifa0, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImponible'
                //baseImpGrav
                xmlwriter_start_element($xml, 'baseImpGrav');
                xmlwriter_text($xml, number_format($nd->tarifa12, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'baseImpGrav'
                //montoIva
                xmlwriter_start_element($xml, 'montoIva');
                xmlwriter_text($xml, number_format($nd->iva, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'montoIva'
                //montoIce
                xmlwriter_start_element($xml, 'montoIce');
                xmlwriter_text($xml, '0.00');
                xmlwriter_end_element($xml);
                // final 'montoIce'
                //valorRetIva
                xmlwriter_start_element($xml, 'valorRetIva');
                xmlwriter_text($xml, number_format(Nota_Debito::NDbyFecha($fechaInicio, $fechaFin)->join('factura_venta', 'factura_venta.factura_id', '=', 'nota_debito.factura_id')->join('retencion_venta', 'retencion_venta.nd_id', '=', 'nota_debito.nd_id')->join('detalle_rv', 'detalle_rv.retencion_id', '=', 'retencion_venta.retencion_id')->where('factura_venta.cliente_id', '=', $nd->cliente_id)->where('detalle_rv.detalle_tipo', '=', 'IVA')->select(DB::raw('SUM(detalle_valor) as valor'))->first()->valor, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'valorRetIva'
                //valorRetRenta
                xmlwriter_start_element($xml, 'valorRetRenta');
                xmlwriter_text($xml, number_format(Nota_Debito::NDbyFecha($fechaInicio, $fechaFin)->join('factura_venta', 'factura_venta.factura_id', '=', 'nota_debito.factura_id')->join('retencion_venta', 'retencion_venta.nd_id', '=', 'nota_debito.nd_id')->join('detalle_rv', 'detalle_rv.retencion_id', '=', 'retencion_venta.retencion_id')->where('factura_venta.cliente_id', '=', $nd->cliente_id)->where('detalle_rv.detalle_tipo', '=', 'FUENTE')->select(DB::raw('SUM(detalle_valor) as valor'))->first()->valor, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'valorRetRenta'
                //formasDePago
                xmlwriter_start_element($xml, 'formasDePago');
                foreach (Nota_Debito::NDbyFecha($fechaInicio, $fechaFin)->join('factura_venta', 'factura_venta.factura_id', '=', 'nota_debito.factura_id')->where('factura_venta.cliente_id', '=', $nd->cliente_id)->select('nota_debito.forma_pago_id')->distinct()->get() as $formaPago) {
                    //formaPago
                    xmlwriter_start_element($xml, 'formaPago');
                    xmlwriter_text($xml, $formaPago->formaPago->forma_pago_codigo);
                    xmlwriter_end_element($xml);
                    // final 'formaPago'
                }
                                
                xmlwriter_end_element($xml);
                // final 'formasDePago'
                xmlwriter_end_element($xml);
                // final 'detalleVentas'
            }
            xmlwriter_end_element($xml);
            // final 'ventas'
            //ventasEstablecimiento
            xmlwriter_start_element($xml, 'ventasEstablecimiento');
            //foreach () {
            foreach(Sucursal::Sucursales()->get() as $sucursal){
                //ventaEst
                xmlwriter_start_element($xml, 'ventaEst');
                //codEstab
                xmlwriter_start_element($xml, 'codEstab');
                xmlwriter_text($xml, $sucursal->sucursal_codigo);
                xmlwriter_end_element($xml);
                // final 'codEstab'
                //ventasEstab
                xmlwriter_start_element($xml, 'ventasEstab');
                $establecimiento =Factura_Venta::FacturasbyFecha($fechaInicio, $fechaFin)->join('rango_documento', 'factura_venta.rango_id', '=', 'rango_documento.rango_id')->join('punto_emision', 'rango_documento.punto_id', '=', 'punto_emision.punto_id')->select(DB::raw('SUM(factura_venta.factura_subtotal) - SUM(factura_venta.factura_descuento) as ventas'))->where('factura_venta.factura_emision','<>','ELECTRONICA')->where('sucursal.sucursal_id','=',$sucursal->sucursal_id)->first();
                xmlwriter_text($xml, number_format($establecimiento->ventas, 2, '.', ''));
                xmlwriter_end_element($xml);
                // final 'ventasEstab'
                xmlwriter_end_element($xml);
                // final 'ventaEst'
            }
            xmlwriter_end_element($xml);
            // final 'ventasEstablecimiento'
            //anulados
            xmlwriter_start_element($xml, 'anulados');
            foreach (Documento_Anulado::DocumentosByFecha($fechaInicio, $fechaFin, '0')->orderBy('documento_anulado_fecha', 'asc')->get() as $documento) {
                //detalleAnulados
                xmlwriter_start_element($xml, 'detalleAnulados');
                //tipoComprobante
                xmlwriter_start_element($xml, 'tipoComprobante');
                if ($documento->facturaVenta) {
                    xmlwriter_text($xml, $documento->facturaVenta->rangoDocumento->tipoComprobante->tipo_comprobante_codigo);
                }
                if ($documento->notaCredito) {
                    xmlwriter_text($xml, $documento->notaCredito->rangoDocumento->tipoComprobante->tipo_comprobante_codigo);
                }
                if ($documento->notaDebito) {
                    xmlwriter_text($xml, $documento->notaDebito->rangoDocumento->tipoComprobante->tipo_comprobante_codigo);
                }
                if ($documento->retencion) {
                    xmlwriter_text($xml, $documento->retencion->rangoDocumento->tipoComprobante->tipo_comprobante_codigo);
                }
                if ($documento->liquidacion) {
                    xmlwriter_text($xml, $documento->liquidacion->rangoDocumento->tipoComprobante->tipo_comprobante_codigo);
                }
                xmlwriter_end_element($xml);
                // final 'tipoComprobante'
                //establecimiento
                xmlwriter_start_element($xml, 'establecimiento');
                if ($documento->facturaVenta) {
                    xmlwriter_text($xml, $documento->facturaVenta->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
                }
                if ($documento->notaCredito) {
                    xmlwriter_text($xml, $documento->notaCredito->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
                }
                if ($documento->notaDebito) {
                    xmlwriter_text($xml, $documento->notaDebito->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
                }
                if ($documento->retencion) {
                    xmlwriter_text($xml, $documento->retencion->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
                }
                if ($documento->liquidacion) {
                    xmlwriter_text($xml, $documento->liquidacion->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
                }
                xmlwriter_end_element($xml);
                // final 'establecimiento'
                //puntoEmision
                xmlwriter_start_element($xml, 'puntoEmision');
                if ($documento->facturaVenta) {
                    xmlwriter_text($xml, $documento->facturaVenta->rangoDocumento->puntoEmision->punto_serie);
                }
                if ($documento->notaCredito) {
                    xmlwriter_text($xml, $documento->notaCredito->rangoDocumento->puntoEmision->punto_serie);
                }
                if ($documento->notaDebito) {
                    xmlwriter_text($xml, $documento->notaDebito->rangoDocumento->puntoEmision->punto_serie);
                }
                if ($documento->retencion) {
                    xmlwriter_text($xml, $documento->retencion->rangoDocumento->puntoEmision->punto_serie);
                }
                if ($documento->liquidacion) {
                    xmlwriter_text($xml, $documento->liquidacion->rangoDocumento->puntoEmision->punto_serie);
                }
                xmlwriter_end_element($xml);
                // final 'puntoEmision'
                //secuencialInicio
                xmlwriter_start_element($xml, 'secuencialInicio');
                if ($documento->facturaVenta) {
                    xmlwriter_text($xml, $documento->facturaVenta->factura_secuencial);
                }
                if ($documento->notaCredito) {
                    xmlwriter_text($xml, $documento->notaCredito->nc_secuencial);
                }
                if ($documento->notaDebito) {
                    xmlwriter_text($xml, $documento->notaDebito->nd_secuencial);
                }
                if ($documento->retencion) {
                    xmlwriter_text($xml, $documento->retencion->retencion_secuencial);
                }
                if ($documento->liquidacion) {
                    xmlwriter_text($xml, $documento->liquidacion->lc_secuencial);
                }
                xmlwriter_end_element($xml);
                // final 'secuencialInicio'
                //secuencialFin
                xmlwriter_start_element($xml, 'secuencialFin');
                if ($documento->facturaVenta) {
                    xmlwriter_text($xml, $documento->facturaVenta->factura_secuencial);
                }
                if ($documento->notaCredito) {
                    xmlwriter_text($xml, $documento->notaCredito->nc_secuencial);
                }
                if ($documento->notaDebito) {
                    xmlwriter_text($xml, $documento->notaDebito->nd_secuencial);
                }
                if ($documento->retencion) {
                    xmlwriter_text($xml, $documento->retencion->retencion_secuencial);
                }
                if ($documento->liquidacion) {
                    xmlwriter_text($xml, $documento->liquidacion->lc_secuencial);
                }
                xmlwriter_end_element($xml);
                // final 'secuencialFin'
                //autorizacion
                xmlwriter_start_element($xml, 'autorizacion');
                if ($documento->facturaVenta) {
                    xmlwriter_text($xml, $documento->facturaVenta->factura_autorizacion);
                }
                if ($documento->notaCredito) {
                    xmlwriter_text($xml, $documento->notaCredito->nc_autorizacion);
                }
                if ($documento->notaDebito) {
                    xmlwriter_text($xml, $documento->notaDebito->nd_autorizacion);
                }
                if ($documento->retencion) {
                    xmlwriter_text($xml, $documento->retencion->retencion_autorizacion);
                }
                if ($documento->liquidacion) {
                    xmlwriter_text($xml, $documento->liquidacion->lc_autorizacion);
                }
                xmlwriter_end_element($xml);
                // final 'autorizacion'
                xmlwriter_end_element($xml);
                // final 'detalleAnulados'
            }
            xmlwriter_end_element($xml);
            // final 'anulados'
            xmlwriter_end_element($xml);
            // final 'iva'
            xmlwriter_end_document($xml);

        
            $ruta = public_path().'/ATS/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $xmlFinal = xmlwriter_output_memory($xml);
            $nombreArchivo = "ATS-".DateTime::createFromFormat('Y-m-d', $fechaFin)->format('m').DateTime::createFromFormat('Y-m-d', $fechaFin)->format('Y').".xml";
            $archivo = fopen($ruta.'/'.$nombreArchivo, 'w');
            fwrite($archivo, $xmlFinal);
            fclose($archivo);
            return $ruta.'/'.$nombreArchivo;
        } catch (\Exception $ex) {
            return redirect('atsSRI')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}