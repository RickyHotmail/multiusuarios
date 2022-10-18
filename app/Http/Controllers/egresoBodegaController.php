<?php

namespace App\Http\Controllers;
use App\Models\Egreso_Bodega;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;

use App\Models\Bodega;
use App\Models\Centro_Consumo;
use App\Models\Cuenta;
use App\Models\Detalle_EB;
use App\Models\Movimiento_Producto;
use App\Models\Diario;
use App\Models\Detalle_Diario;
use App\Models\Producto;
use App\Models\Tipo_MI;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class egresoBodegaController extends Controller
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
            $puntoEmisiones = Punto_Emision::puntos()->get();        
            $bodega=Egreso_Bodega::BodegaDistinsc()->select('bodega.bodega_id','bodega.bodega_nombre')->distinct()->get();
            return view('admin.inventario.egresoBodega.view',['bodega'=>$bodega, 'puntoEmisiones'=>$puntoEmisiones,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            return redirect('/denegado');
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect('/denegado');
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
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Ddescripcion');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $consumo = $request->get('Didconsumo');    
            $productonombre='';   
            /********************cabecera de egreso de venta ********************/
            for ($i = 1; $i < count($cantidad); ++$i) {
                $prod=Producto::findOrFail($isProducto[$i]);
                $productonombre=$productonombre.$prod->producto_nombre.', ';
            }
            $general = new generalController();           
            $egreso = new Egreso_Bodega();
            $cierre = $general->cierre($request->get('egreso_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/egresoBodega/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $egreso->cabecera_egreso_numero = $request->get('egreso_serie').substr(str_repeat(0, 9).$request->get('egreso_numero'), - 9);
            $egreso->cabecera_egreso_serie = $request->get('egreso_serie');
            $egreso->cabecera_egreso_secuencial = $request->get('egreso_numero');
            $egreso->cabecera_egreso_fecha = $request->get('egreso_fecha');

          
            $egreso->cabecera_egreso_destino = $request->get('egreso_destino');
            $egreso->cabecera_egreso_destinatario = $request->get('egreso_destinatario');
            $egreso->cabecera_egreso_motivo = $request->get('egresomotivo');
            
            $egreso->cabecera_egreso_total = $request->get('idTotal');
            if($request->get('egreso_comentario')){
                $egreso->cabecera_egreso_comentario = $request->get('egreso_comentario');
            }else{
                $egreso->cabecera_egreso_comentario = '';
            }
            $egreso->cabecera_egreso_estado = '1';
            $egreso->bodega_id = $request->get('bodega_id');
            $egreso->tipo_id = $request->get('tipo');
            $egreso->user_id = Auth::user()->user_id;
            $egreso->rango_id = $request->get('rango_id');
            /**********************asiento diario****************************/
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('egreso_fecha'),'CEBP');
                $diario->diario_fecha = $request->get('egreso_fecha');
                $diario->diario_referencia = 'COMPROBANTE DE EGRESO DE BODEGA DE PRODUCTO';
                $diario->diario_tipo_documento = 'EGRESO DE BODEGA';
                $diario->diario_numero_documento = $egreso->cabecera_egreso_numero;
                $diario->diario_beneficiario = $request->get('egreso_destinatario');
                $diario->diario_tipo = 'CEBP';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('egreso_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('egreso_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE EGRESO DE BODEGA DE PRODUCTOS: '.$productonombre;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de egreso de bodega de producto -> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Registro de de egreso de bodega de producto  -> '.$egreso->cabecera_egreso_numero.' con bodega -> '.$request->get('bodega_nombre').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                $egreso->diario()->associate($diario);
            }
            $egreso->save();
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $general->registrarAuditoria('Registro de egreso de bodega de producto numero -> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Registro de egreso de bodega de producto numero -> '.$egreso->cabecera_egreso_numero.' con bodega -> '.$request->get('bodega_nombre').' con un total de -> '.$request->get('idTotal').'  y con codigo de diario -> '.$diario->diario_codigo);
            }else{
                $general->registrarAuditoria('Registro de egreso de bodega de producto numero -> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Registro de egreso de bodega de producto numero -> '.$egreso->cabecera_egreso_numero.' con bodega -> '.$request->get('bodega_nombre').' con un total de -> '.$request->get('idTotal'));
            }
            /********************detalle de factura de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleEB = new Detalle_EB();
                $detalleEB->detalle_egreso_cantidad = $cantidad[$i];
                $detalleEB->detalle_egreso_precio_unitario = $pu[$i];
                $detalleEB->detalle_egreso_total = $total[$i];  
                $detalleEB->detalle_egreso_descripcion = $nombre[$i]; 
                $detalleEB->detalle_egreso_estado = '1';
                $detalleEB->producto_id = $isProducto[$i]; 
                $detalleEB->centro_consumo_id = $consumo[$i]; 
                
                /******************registro de movimiento de producto******************/
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('egreso_fecha');
                    $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    $movimientoProducto->movimiento_precio=$pu[$i];
                    $movimientoProducto->movimiento_iva=0;
                    $movimientoProducto->movimiento_total=$total[$i];
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='EGRESO DE BODEGA';
                    $movimientoProducto->movimiento_motivo='MOVIMIENTO';
                    $movimientoProducto->movimiento_tipo='SALIDA';
                    $movimientoProducto->movimiento_descripcion='EGRESO DE BODEGA No. '.$egreso->cabecera_egreso_numero;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$egreso->bodega_id;
                    $movimientoProducto->centro_consumo_id=$detalleEB->centro_consumo_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por egreso de bodega numero -> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Registro de movimiento de producto por factura de venta numero -> '.$egreso->cabecera_egreso_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                    $detalleEB->movimiento()->associate($movimientoProducto);
                    if(Auth::user()->empresa->empresa_contabilidad == '1'){
                        /********************detalle de diario de venta********************/
                        $producto = Producto::findOrFail($isProducto[$i]);
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $total[$i];
                        $detalleDiario->detalle_comentario = 'P/R SALIDA DE INVENTARIO DEL PRODUCTO '.$producto->producto_nombre.' CON LA CANTIDAD DE '.$cantidad[$i].' Y CODIGO '.$producto->producto_codigo;
                        $detalleDiario->detalle_tipo_documento = 'EGRESO DE BODEGA';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1'; 
                        $detalleDiario->movimientoProducto()->associate($movimientoProducto);    
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$egreso->cabecera_egreso_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$total[$i]);
                    }
                    $egreso->detalles()->save($detalleEB);
                    $general->registrarAuditoria('Registro de detalle de egreso de venta numero -> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Registro de detalle de egreso de venta numero -> '.$egreso->cabecera_egreso_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);            
            }     
            $url = '';
            if(Auth::user()->empresa->empresa_contabilidad == '1'){               
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('idTotal');
                $detalleDiario->detalle_haber =  0.00;
                $detalleDiario->detalle_tipo_documento = 'EGRESO DE BODEGA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo = Tipo_MI::TipoMovimiento($request->get('tipo'))->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_id;
                $detalleDiario->detalle_comentario = 'P/R EGRESO DE PRODUCTOS CON DESTINO '.$request->get('egreso_destino');  
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$egreso->cabecera_egreso_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$tipo->cuenta->cuenta_numero.' en el haber por un valor de -> '.$request->get('idTotal'));                
                $url = $general->pdfDiario($diario);
                $url2 = $general->pdfComprobanteEgresoBodega($egreso);
            }
            DB::commit();
            return redirect('/egresoBodega/new/'.$request->get('punto_id'))->with('success','Egreso de bodega se registrada exitosamente')->with('diario',$url)->with('pdf2', $url2);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/egresoBodega/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect('/denegado');
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
        return redirect('/denegado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $egreso = Egreso_Bodega::findOrFail($id);
            $auditoria = new generalController();
            $cierre = $auditoria->cierre($egreso->cabecera_egreso_fecha,$egreso->diario->sucursal_id);          
            if($cierre){
                return redirect('egresoBodega')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $diario=$egreso->diario;
            $egreso->diario_id=null;
            $egreso->save();
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $auditoria->registrarAuditoria('Actualizacion del Id a null para la eliminacion del Diario  N°'.$diario->diario_codigo.'  relacionado al Egreso de Bodega N°-> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Permiso con id -> '.$id);
                foreach ($diario->detalles as $diariodetalle) {
                    
                    $diariodetalle->delete();
                    $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario->diario_codigo .'relacionado al ingreso de Bodega N°-> '.$egreso->cabecera_egreso_numero.' Con Documento detalle diario '.$diariodetalle->detalle_tipo_documento,$egreso->cabecera_egreso_numero,'Eliminacion del detalle diario con Permiso con id -> '.$id);  
                }
                $diario->delete();   
                $auditoria->registrarAuditoria('Eliminacion del  diario  N°'.$diario->diario_codigo .'relacionado al Egreso de Bodega N°-> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Permiso con id -> '.$id);   
            }

            foreach($egreso->detalles as $detalle){
                $movimiento=$detalle->movimiento;
                $detalle->movimiento_id=null;
                $detalle->update();
                $auditoria->registrarAuditoria('Actualizacion del Id a null para la eliminacion del Movimiento  motivo'.$movimiento->movimiento_motivo.' y tipo '.$movimiento->movimiento_tipo.'  relacionado al Egreso de Bodega N°-> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Permiso con id -> '.$id);         
                $movimiento->delete();
                $auditoria->registrarAuditoria('Eliminacion del Movimiento  motivo'.$movimiento->movimiento_motivo.' y tipo '.$movimiento->movimiento_tipo.'  relacionado al Egreso de Bodega N°-> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Permiso con id -> '.$id);   
                $detalle->delete();
                $auditoria->registrarAuditoria('Eliminacion del Detalle Egreso de Bodega N°-> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Permiso con id -> '.$id);   
            } 
            
                $egreso->delete(); 
            $auditoria->registrarAuditoria('Eliminacion del Egreso de Bodega N°-> '.$egreso->cabecera_egreso_numero,$egreso->cabecera_egreso_numero,'Permiso con id -> '.$id);
            DB::commit();
            return redirect('egresoBodega')->with('success','Datos eliminados exitosamente'); 
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('egresoBodega')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
    public function nuevo($id){

        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $rangoDocumento=Rango_Documento::PuntoRango($id, 'Egreso de Bodega')->first();
        $secuencial=1;
        $sucursalp=Punto_Emision::punto($id)->first();
        if($rangoDocumento){
            $secuencial=$rangoDocumento->rango_inicio;
            $secuencialAux=Egreso_Bodega::secuencial($rangoDocumento->rango_id)->max('cabecera_egreso_secuencial');
            if($secuencialAux){$secuencial=$secuencialAux+1;}
            return view('admin.inventario.egresoBodega.nuevo',['cuentas'=>Cuenta::CuentasMovimiento()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'tipo'=>Tipo_MI::TipoMovimientos()->where('sucursal_id','=',$sucursalp->sucursal_id)->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($id)->get(), 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }else{
            return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
        }

    }
    public function Presentardelete($id)
    {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $egreso=Egreso_Bodega::Egreso($id)->first();
        if($egreso){
            return view('admin.inventario.egresoBodega.eliminar',['centros'=>Centro_Consumo::CentroConsumos()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'egreso'=>$egreso,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }else{
            return redirect('/denegado');
        }
    }
    public function Presentarvisualizar($id)
    {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $egreso=Egreso_Bodega::Egreso($id)->first();
        if($egreso){
            return view('admin.inventario.egresoBodega.visualizar',['centros'=>Centro_Consumo::CentroConsumos()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'egreso'=>$egreso,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }else{
            return redirect('/denegado');
        }
    }
    public function consultar(Request $request)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $bodega=Egreso_Bodega::BodegaDistinsc()->select('bodega.bodega_id','bodega.bodega_nombre')->distinct()->get();
            $egreso=null;
            
           
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_bodega') == "--TODOS--") {
                $egreso=Egreso_Bodega::Egresos()->get();
            }
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_bodega') != "--TODOS--" ) {
                $egreso=Egreso_Bodega::EgresosDiferentes($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_bodega'))->get();
                        
            }             
            if ($request->get('fecha_todo') != "on" && $request->get('nombre_bodega') == "--TODOS--" ) {
                $egreso=Egreso_Bodega::EgresosFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->get();
            }
            if ($request->get('fecha_todo') == "on" && $request->get('nombre_bodega') != "--TODOS--") {
                $egreso=Egreso_Bodega::EgresosBodega($request->get('nombre_bodega'))->get();
            }   
            return view('admin.inventario.egresoBodega.view', ['nombre_bodega'=>$request->get('nombre_bodega'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'egreso'=>$egreso,'bodega'=>$bodega,  'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        
        }catch(\Exception $ex){
            return view('admin.inventario.egresoBodega.view',['egreso'=>$egreso,'bodega'=>$bodega, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin])->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
