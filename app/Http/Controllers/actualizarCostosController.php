<?php

namespace App\Http\Controllers;

use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Factura_Venta;
use App\Models\Movimiento_Producto;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class actualizarCostosController extends Controller
{
    public function nuevo()
    {
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            return view('admin.contabilidad.actualizarCostos.index',['sucursales'=>Sucursal::sucursales()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function actualizar(Request $request){
        try{
            ini_set('max_execution_time', 0);
            $this->verificarAsientosCostos($request->get('fecha_desde'), $request->get('fecha_hasta'));
            $this->actualizarPrecioCosto($request->get('fecha_desde'), $request->get('fecha_hasta'));
            return redirect('actualizarCostos')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            return redirect('actualizarCostos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function verificarAsientosCostos($fechaDesde, $fechaHasta){
        try{           
            DB::beginTransaction();
            $general = new generalController();
            foreach(Factura_Venta::Facturas()->where('factura_venta.factura_fecha','>=',$fechaDesde)->where('factura_venta.factura_fecha','<=',$fechaHasta)->get() as $factura){
                if(!isset($factura->diarioCosto->diario_id)){                
                    $banderaP = false;
                    foreach($factura->detalles as $detalle){
                        $producto = Producto::findOrFail($detalle->producto->producto_id);
                        if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                            $banderaP = true;
                        }
                    }
                    if($banderaP){
                        /**********************asiento diario de costo ****************************/
                        $diarioC = new Diario();
                        $diarioC->diario_codigo = $general->generarCodigoDiario($factura->factura_fecha,'CCVP');
                        $diarioC->diario_fecha = $factura->factura_fecha;
                        $diarioC->diario_referencia = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO';
                        $diarioC->diario_tipo_documento = 'FACTURA';
                        $diarioC->diario_numero_documento = $factura->factura_numero;
                        $diarioC->diario_beneficiario = $factura->cliente->cliente_nombre;
                        $diarioC->diario_tipo = 'CCVP';
                        $diarioC->diario_secuencial = substr($diarioC->diario_codigo, 8);
                        $diarioC->diario_mes = DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('m');
                        $diarioC->diario_ano = DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('Y');
                        $diarioC->diario_comentario = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO CON FACTURA: '.$factura->factura_numero;
                        $diarioC->diario_cierre = '0';
                        $diarioC->diario_estado = '1';
                        $diarioC->empresa_id = Auth::user()->empresa_id;
                        $diarioC->sucursal_id = Rango_Documento::rango($factura->rango_id)->first()->puntoEmision->sucursal_id;
                        $diarioC->save();
                        $general->registrarAuditoria('Registro de diario de costo de venta de factura -> '.$factura->factura_numero,$factura->factura_numero,'Registro de diario de costo de venta de factura -> '.$factura->factura_numero.' con cliente -> '.$factura->cliente->cliente_nombre.' con un total de -> '.$factura->factura_total.' y con codigo de diario -> '.$diarioC->diario_codigo);
                        /************************************************************************/
                        $factura->diarioCosto()->associate($diarioC);
                        $factura->update();
                        foreach($factura->detalles as $detalle){
                            $producto = Producto::findOrFail($detalle->producto->producto_id);
                            if($banderaP){
                                if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                                    $detalleDiario = new Detalle_Diario();
                                    $detalleDiario->detalle_debe = 0.00;
                                    $detalleDiario->detalle_haber = $detalle->movimiento->movimiento_costo_promedio;
                                    $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                                    $detalleDiario->detalle_numero_documento = $diarioC->diario_numero_documento;
                                    $detalleDiario->detalle_conciliacion = '0';
                                    $detalleDiario->detalle_estado = '1';
                                    $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                                    $detalleDiario->movimientoProducto()->associate($detalle->movimiento);
                                    $diarioC->detalles()->save($detalleDiario);
                                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$detalleDiario->detalle_haber);
                                    
                                    $detalleDiario = new Detalle_Diario();
                                    $detalleDiario->detalle_debe = $detalle->movimiento->movimiento_costo_promedio;
                                    $detalleDiario->detalle_haber = 0.00;
                                    $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                                    $detalleDiario->detalle_numero_documento = $diarioC->diario_numero_documento;
                                    $detalleDiario->detalle_conciliacion = '0';
                                    $detalleDiario->detalle_estado = '1';
                                    $detalleDiario->movimientoProducto()->associate($detalle->movimiento);
                                    $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diarioC->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                                    $diarioC->detalles()->save($detalleDiario);
                                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$detalleDiario->detalle_debe);
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('actualizarCostos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function actualizarPrecioCosto($fechaDesde, $fechaHasta){
        try{           
            DB::beginTransaction();
            $productos = Producto::Productos()->where('producto_compra_venta','=','3')->get();
            foreach($productos as $producto){
                $general = new generalController();
                
                $datos = null;
                $count = 1;
                /***********************SALDO ANTERIOR***********************/
                $datos[$count]['fec'] = '';
                $datos[$count]['can1'] = 0;
                $datos[$count]['pre1'] = 0;
                $datos[$count]['tot1'] = 0;
                $datos[$count]['can2'] = 0;
                $datos[$count]['pre2'] = 0;
                $datos[$count]['tot2'] = 0;
                $datos[$count]['can3'] = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($fechaDesde."- 1 days")) )->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_cantidad')-Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($fechaDesde."- 1 days")) )->where('movimiento_tipo','=','SALIDA')->sum('movimiento_cantidad');
                $costoPromedio = Movimiento_Producto::MovProductoByFechaCorte($producto->producto_id,date("Y-m-d",strtotime($fechaDesde."- 1 days")))->orderBy('movimiento_fecha','desc')->orderBy('movimiento_id','desc')->first();
                $datos[$count]['pre3'] = 0;
                if($costoPromedio){
                    $datos[$count]['pre3'] = $costoPromedio->movimiento_costo_promedio;
                }
                $datos[$count]['tot3'] = floatval($datos[$count]['can3'])*floatval($datos[$count]['pre3']);
                $datos[$count]['cos'] = '';
                $count ++;
                /***********************************************************/
                foreach(Movimiento_Producto::MovProductoByFecha($producto->producto_id,$fechaDesde,$fechaHasta)->orderBy('movimiento_fecha','asc')->orderBy('movimiento_id','asc')->get() as $movimiento){
                    $cierre = $general->cierre($fechaDesde,$movimiento->bodega->sucursal_id);          
                    if($cierre){
                        return redirect('actualizarCostos')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    $cierre = $general->cierre($fechaHasta,$movimiento->bodega->sucursal_id);          
                    if($cierre){
                        return redirect('actualizarCostos')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    $bandera2 = false;
                    if($movimiento->movimiento_motivo != "ANULACION"){
                        if($movimiento->movimiento_tipo == "SALIDA" and $movimiento->movimiento_motivo == "VENTA" and $movimiento->movimiento_documento == "FACTURA DE VENTA"){
                            if(isset($movimiento->detalle_fv->facturaVenta->diario->diario_id)){
                                $bandera2 = true;
                            }
                        }else{
                            $bandera2 = true;
                        }
                    }
                    $datos[$count]['fec'] = $movimiento->movimiento_fecha;
                    $datos[$count]['can1'] = 0;
                    $datos[$count]['pre1'] = 0;
                    $datos[$count]['tot1'] = 0;
                    $datos[$count]['can2'] = 0;
                    $datos[$count]['pre2'] = 0;
                    $datos[$count]['tot2'] = 0;
                    if($movimiento->movimiento_tipo == "ENTRADA"){
                        $datos[$count]['can1'] = $movimiento->movimiento_cantidad;
                        $datos[$count]['pre1'] = $movimiento->movimiento_precio;
                        $datos[$count]['tot1'] = $movimiento->movimiento_total;
                    }
                    if($movimiento->movimiento_tipo == "SALIDA"){
                        $datos[$count]['can2'] = $movimiento->movimiento_cantidad;
                    }
                    if($count-1 > 0){
                        $datos[$count]['can3'] = floatval($datos[$count-1]['can3'])+floatval($datos[$count]['can1'])-floatval($datos[$count]['can2']);
                    }else{
                        $datos[$count]['can3'] = floatval($datos[$count]['can1'])-floatval($datos[$count]['can2']);
                    }
                    if($movimiento->movimiento_motivo == "COMPRA" or $movimiento->movimiento_motivo == 'AJUSTE DE INVENTARIO'){
                        if(floatval($datos[$count]['can3']) <> 0){
                            if($count-1 > 0){
                                $datos[$count]['pre3'] = (floatval($datos[$count-1]['tot3'])+floatval($datos[$count]['tot1']))/floatval($datos[$count]['can3']);
                            }else{
                                $datos[$count]['pre3'] = floatval($datos[$count]['tot1'])/floatval($datos[$count]['can3']);
                            }
                            
                        }else{
                            $datos[$count]['pre3'] = 0;
                        }
                    }else{
                        if($count-1 > 0){
                            $datos[$count]['pre3'] = $datos[$count-1]['pre3'];
                        }else{
                            $datos[$count]['pre3'] = 0;
                        }
                    }                    
                    $datos[$count]['tot3'] = floatval($datos[$count]['can3'])*round(floatval($datos[$count]['pre3']),4);
                    if($movimiento->movimiento_tipo == "SALIDA"){
                        $datos[$count]['pre2'] = $datos[$count]['pre3'];
                        $datos[$count]['tot2'] = floatval($datos[$count]['can2'])*round(floatval($datos[$count]['pre2']),4);
                    }
                    if($movimiento->movimiento_tipo == "ENTRADA" and $movimiento->movimiento_motivo == "ANULACION" and $movimiento->movimiento_documento == "FACTURA DE VENTA"){
                        $datos[$count]['pre1'] = $datos[$count]['pre3'];
                        $datos[$count]['tot1'] = floatval($datos[$count]['can1'])*floatval($datos[$count]['pre1']);
                    }
                    if($movimiento->movimiento_tipo == "ENTRADA" and $movimiento->movimiento_motivo == "VENTA" and $movimiento->movimiento_documento == "NOTA DE CRÉDITO"){
                        $datos[$count]['pre1'] = $datos[$count]['pre3'];
                        $datos[$count]['tot1'] = floatval($datos[$count]['can1'])*floatval($datos[$count]['pre1']);
                    }
                    if($movimiento->movimiento_tipo == "SALIDA" and $movimiento->movimiento_motivo == "ANULACION" and $movimiento->movimiento_documento == "NOTA DE CRÉDITO"){
                        $datos[$count]['pre2'] = $datos[$count]['pre3'];
                        $datos[$count]['tot2'] = floatval($datos[$count]['can2'])*floatval($datos[$count]['pre2']);
                    }
                    $datos[$count]['dia'] = '';
                    $datos[$count]['cos'] = '';
                    if($movimiento->detalle_fv){
                        if($movimiento->detalle_fv->facturaVenta->diarioCosto){
                            $datos[$count]['cos'] = $movimiento->detalle_fv->facturaVenta->diarioCosto->diario_codigo;
                        }
                    }
                    if($movimiento->detalle_nc){
                        if($movimiento->detalle_nc->notaCredito->diarioCosto){
                            $datos[$count]['cos'] = $movimiento->detalle_nc->notaCredito->diarioCosto->diario_codigo;
                        }
                    }
                    if($movimiento->detalle_od){
                        if(isset($movimiento->detalle_od->ordenDespacho->Factura->diarioCosto)){
                            $datos[$count]['cos'] = $movimiento->detalle_od->ordenDespacho->Factura->diarioCosto->diario_codigo;
                        }
                    }
                    $datos[$count]['pre3'] = round($datos[$count]['pre3'],4);
                    $movimiento->movimiento_stock_actual = $datos[$count]['can3'];
                    $movimiento->movimiento_costo_promedio = $datos[$count]['pre3'];
                    $movimiento->update();
                    if($datos[$count]['cos'] <> ''){
                        $diario = Diario::DiarioCodigo($datos[$count]['cos'])->first();
                        foreach($movimiento->detallesDiario as $detalle){
                            if($diario->diario_id == $detalle->diario->diario_id){
                                if(isset($movimiento->detalle_fv->detalle_id)){
                                    if($detalle->cuenta_id == $movimiento->detalle_fv->producto->producto_cuenta_inventario){
                                        $detalle->detalle_haber = $datos[$count]['tot2'];
                                    }
                                    $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                                    if($detalle->cuenta_id == $parametrizacionContable->cuenta_id){
                                        $detalle->detalle_debe = $datos[$count]['tot2'];
                                    }
                                }
                                if(isset($movimiento->detalle_nc->detalle_id)){
                                    if($detalle->cuenta_id == $movimiento->detalle_nc->producto->producto_cuenta_inventario){
                                        $detalle->detalle_debe = $datos[$count]['tot1'];
                                    }
                                    $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                                    if($detalle->cuenta_id == $parametrizacionContable->cuenta_id){
                                        $detalle->detalle_haber = $datos[$count]['tot1'];
                                    }
                                }
                                if(isset($movimiento->detalle_od->detalle_id)){
                                    if($detalle->cuenta_id == $movimiento->detalle_od->producto->producto_cuenta_inventario){
                                        $detalle->detalle_haber = $datos[$count]['tot2'];
                                    }
                                    $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                                    if($detalle->cuenta_id == $parametrizacionContable->cuenta_id){
                                        $detalle->detalle_debe = $datos[$count]['tot2'];
                                    }
                                }
                                $detalle->update();
                            }
                        }
                    }
                    if($bandera2){
                        $count ++;
                    }
                }
            }
            DB::commit();
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('actualizarCostos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
