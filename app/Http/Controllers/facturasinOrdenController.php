<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Categoria_Producto;
use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_Diario;
use App\Models\Detalle_FV;
use App\Models\Detalle_Pago_CXC;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Forma_Pago;
use App\Models\Grupo_Producto;
use App\Models\Marca_Producto;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Producto;
use App\Models\Pago_CXC;
use App\Models\Parametrizacion_Contable;
use App\Models\Parametrizacion_Impresion;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\sucursal;
use App\Models\Tamano_Producto;
use App\Models\Tarifa_Iva;
use App\Models\Unidad_Medida_Producto;
use App\Models\Vendedor;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class facturasinOrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function nuevo($id){
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Factura')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $secuencial=1;    

            $productosGastos=Producto::productos()->where('producto_compra_venta','=','1')->get();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $categorias=Categoria_Producto::categorias()->get();
            $marcas=Marca_Producto::marcas()->get();
            $unidadMedidas=Unidad_Medida_Producto::unidadMedidas()->get();
            $tamanos=Tamano_Producto::tamanos()->get();
            $grupos=Grupo_Producto::grupos()->get();


            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Factura_Venta::secuencial($rangoDocumento->rango_id)->max('factura_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $general = new generalController();
                if($general->documentos()){
                    return view('admin.ventas.facturaSinOrden.nuevo',[
                        'vendedores'=>Vendedor::Vendedores()->get(),
                        'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                        'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                        'bodegas'=>Bodega::bodegasSucursal($id)->get(),
                        'formasPago'=>Forma_Pago::formaPagos()->get(), 
                        'cajaAbierta'=>$cajaAbierta, 
                        'rangoDocumento'=>$rangoDocumento,
                        'PE'=>Punto_Emision::puntos()->get(),
                        'tipoPermiso'=>$tipoPermiso,
                        'gruposPermiso'=>$gruposPermiso, 
                        'permisosAdmin'=>$permisosAdmin,
                        'categorias'=>$categorias,
                        'marcas'=>$marcas,
                        'unidadMedidas'=>$unidadMedidas,
                        'tamanos'=>$tamanos,
                        'grupos'=>$grupos,
                        'sucursales'=>Sucursal::sucursales()->get(),
                        'cuentas'=>$cuentas])->with('error2Msg','No puede generar documentos electronicos contrate un plan');
                }
                return view('admin.ventas.facturaSinOrden.nuevo',[
                    'vendedores'=>Vendedor::Vendedores()->get(),
                    'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),
                    'bodegas'=>Bodega::bodegasSucursal($id)->get(),
                    'formasPago'=>Forma_Pago::formaPagos()->get(),
                    'cajaAbierta'=>$cajaAbierta,
                    'rangoDocumento'=>$rangoDocumento,
                    'PE'=>Punto_Emision::puntos()->get(),
                    'tipoPermiso'=>$tipoPermiso,
                    'gruposPermiso'=>$gruposPermiso,
                    'permisosAdmin'=>$permisosAdmin,
                    'categorias'=>$categorias,
                    'marcas'=>$marcas,
                    'unidadMedidas'=>$unidadMedidas,
                    'tamanos'=>$tamanos,
                    'grupos'=>$grupos,
                    'sucursales'=>Sucursal::sucursales()->get(),
                    'cuentas'=>$cuentas
                ]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
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
    public function guardarfactura(Request $request)
    {
        try{            
            DB::beginTransaction();
            $rangoDocumentoorden = null;
            $secuencial=0;
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            /***************SABER SI SE GENERAR UN ASIENTO DE COSTO****************/
            $banderaP = false;
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                    $banderaP = true;
                }
            }
            /**********************************************************************/
            /********************cabecera de factura de venta ********************/
            $general = new generalController();
            $cierre = $general->cierre($request->get('factura_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/facturacionsinOrden/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            if($general->documentos()){
                return redirect('/facturacionsinOrden/new/'.$request->get('punto_id'))->with('error2','No puede generar documentos electronicos contrate un plan');
            }
            $docElectronico = new facturacionElectronicaController();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $factura = new Factura_Venta();
            $factura->factura_numero = $request->get('factura_serie').substr(str_repeat(0, 9).$request->get('factura_numero'), - 9);
            $factura->factura_serie = $request->get('factura_serie');
            $factura->factura_secuencial = $request->get('factura_numero');
            $factura->factura_fecha = $request->get('factura_fecha');
            $factura->factura_lugar = $request->get('factura_lugar');
            $factura->factura_tipo_pago = $request->get('factura_tipo_pago');
            $factura->factura_dias_plazo = $request->get('factura_dias_plazo');
            $factura->factura_fecha_pago = $request->get('factura_fecha_termino');
            $factura->factura_subtotal = $request->get('idSubtotal');
            $factura->factura_descuento = $request->get('idDescuento');
            $factura->factura_tarifa0 = $request->get('idTarifa0');
            $factura->factura_tarifa12 = $request->get('idTarifa12');
            $factura->factura_iva = $request->get('idIva');
            $factura->factura_total = $request->get('idTotal');
            if($request->get('factura_comentario')){
                $factura->factura_comentario = $request->get('factura_comentario');
            }else{
                $factura->factura_comentario = '';
            }
            $factura->factura_porcentaje_iva = $request->get('factura_porcentaje_iva');
            $factura->factura_emision = $request->get('tipoDoc');
            $factura->factura_ambiente = 'PRODUCCIÓN';
            $factura->factura_autorizacion = $docElectronico->generarClaveAcceso($factura->factura_numero,$request->get('factura_fecha'),"01");
            $factura->factura_estado = '1';
            $factura->bodega_id = $request->get('bodega_id');
            $factura->cliente_id = $request->get('clienteID');
            $factura->forma_pago_id = $request->get('forma_pago_id');
            $factura->rango_id = $request->get('rango_id');
            $factura->vendedor_id = $request->get('vendedor_id');
                /********************cuenta por cobrar***************************/
                $cxc = new Cuenta_Cobrar();
                $cxc->cuenta_descripcion = 'VENTA CON FACTURA No. '.$factura->factura_numero;
                if($request->get('factura_tipo_pago') == 'CREDITO' or $request->get('factura_tipo_pago') == 'CONTADO'){
                    $cxc->cuenta_tipo =$request->get('factura_tipo_pago');
                    $cxc->cuenta_saldo = $request->get('idTotal');
                    $cxc->cuenta_estado = '1';
                }else{
                    $cxc->cuenta_tipo = $request->get('factura_tipo_pago');
                    $cxc->cuenta_saldo = 0.00;
                    $cxc->cuenta_estado = '2';
                }
                $cxc->cuenta_fecha = $request->get('factura_fecha');
                $cxc->cuenta_fecha_inicio = $request->get('factura_fecha');
                $cxc->cuenta_fecha_fin = $request->get('factura_fecha_termino');
                $cxc->cuenta_monto = $request->get('idTotal');
                $cxc->cuenta_valor_factura = $request->get('idTotal');
                $cxc->cliente_id = $request->get('clienteID');
                $cxc->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $cxc->save();
                $general->registrarAuditoria('Registro de cuenta por cobrar de factura -> '.$factura->factura_numero,$factura->factura_numero,'Registro de cuenta por cobrar de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$factura->factura_autorizacion);
                /****************************************************************/
            $factura->cuentaCobrar()->associate($cxc);
           
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('factura_fecha'),'CFVE');
                $diario->diario_fecha = $request->get('factura_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE FACTURA DE VENTA';
                $diario->diario_tipo_documento = 'FACTURA';
                $diario->diario_numero_documento = $factura->factura_numero;
                $diario->diario_beneficiario = $request->get('buscarCliente');
                $diario->diario_tipo = 'CFVE';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DIARIO DE FACTURA: '.$factura->factura_numero;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de venta de factura -> '.$factura->factura_numero,$factura->factura_numero,'Registro de diario de venta de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                /****************************************************************/
                if($banderaP){
                    /**********************asiento diario de costo ****************************/
                    $diarioC = new Diario();
                    $diarioC->diario_codigo = $general->generarCodigoDiario($request->get('factura_fecha'),'CCVP');
                    $diarioC->diario_fecha = $request->get('factura_fecha');
                    $diarioC->diario_referencia = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO';
                    $diarioC->diario_tipo_documento = 'FACTURA';
                    $diarioC->diario_numero_documento = $factura->factura_numero;
                    $diarioC->diario_beneficiario = $request->get('buscarCliente');
                    $diarioC->diario_tipo = 'CCVP';
                    $diarioC->diario_secuencial = substr($diarioC->diario_codigo, 8);
                    $diarioC->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('m');
                    $diarioC->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('Y');
                    $diarioC->diario_comentario = 'COMPROBANTE DE COSTO DE VENTA DE PRODUCTO CON FACTURA: '.$factura->factura_numero;
                    $diarioC->diario_cierre = '0';
                    $diarioC->diario_estado = '1';
                    $diarioC->empresa_id = Auth::user()->empresa_id;
                    $diarioC->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                    $diarioC->save();
                    $general->registrarAuditoria('Registro de diario de costo de venta de factura -> '.$factura->factura_numero,$factura->factura_numero,'Registro de diario de costo de venta de factura -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diarioC->diario_codigo);
                    /************************************************************************/
                    $factura->diarioCosto()->associate($diarioC);
                }
            
                if($cxc->cuenta_estado == '2'){
                    /********************Pago por Venta en efectivo***************************/
                    $pago = new Pago_CXC();
                    $pago->pago_descripcion = 'PAGO EN EFECTIVO';
                    $pago->pago_fecha = $cxc->cuenta_fecha;
                    $pago->pago_tipo = 'PAGO EN EFECTIVO';
                    $pago->pago_valor = $cxc->cuenta_monto;
                    $pago->pago_estado = '1';
                    $pago->arqueo_id = $arqueoCaja->arqueo_id;
                    
                        $pago->diario()->associate($diario);
                    
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXC();
                    $detallePago->detalle_pago_descripcion = 'PAGO EN EFECTIVO';
                    $detallePago->detalle_pago_valor = $cxc->cuenta_monto; 
                    $detallePago->detalle_pago_cuota = 1;
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_id = $cxc->cuenta_id; 
                    $detallePago->pagoCXC()->associate($pago);
                    $detallePago->save();
                    /****************************************************************/
                }
                
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal');
                    $detalleDiario->detalle_haber = 0.00 ;
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    if($request->get('factura_tipo_pago') == 'CREDITO' OR $request->get('factura_tipo_pago') == 'CONTADO'){
                        $detalleDiario->cliente_id = $request->get('clienteID');
                        $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                        if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        }else{
                            $parametrizacionContable = Cliente::findOrFail($request->get('clienteID'));
                            $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                        }
                    }else{
                        $detalleDiario->detalle_comentario = 'P/R VENTA EN EFECTIVO';
                        $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                        $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                
                if ($request->get('idIva') > 0){
                    
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $request->get('idIva') ;
                        $detalleDiario->detalle_comentario = 'P/R IVA COBRADO EN VENTA';
                        $detalleDiario->detalle_tipo_documento = 'FACTURA';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA VENTAS')->first();
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$request->get('idIva'));
                    
                }
                /****************************************************************/
                /****************************************************************/
                
                    $factura->diario()->associate($diario);
                
            if($arqueoCaja){
                $factura->arqueo_id = $arqueoCaja->arqueo_id;
            }
            $factura->save();
            
                $general->registrarAuditoria('Registro de factura de venta numero -> '.$factura->factura_numero, $factura->factura_numero, 'Registro de factura de venta numero -> '.$factura->factura_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal').' con clave de acceso -> '.$factura->factura_autorizacion.' y con codigo de diario -> '.$diario->diario_codigo);
           
            /*******************************************************************/
            /********************detalle de factura de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $producto = Producto::findOrFail($isProducto[$i]);
                if($producto->producto_tipo == '1' and $producto->producto_compra_venta == '3'){
                    if($producto->producto_stock < $cantidad[$i] && Auth::user()->empresa->empresa_control_inventario){
                        throw new Exception('Stock insuficiente de productos');
                    }
                }
                
                $detalleFV = new Detalle_FV();
                $detalleFV->detalle_cantidad = $cantidad[$i];
                $detalleFV->detalle_precio_unitario = $pu[$i];
                $detalleFV->detalle_descuento = $descuento[$i];
                $detalleFV->detalle_iva = $iva[$i];
                $detalleFV->detalle_total = $total[$i];
                $detalleFV->detalle_descripcion = $nombre[$i];;
                $detalleFV->detalle_estado = '1';
                $detalleFV->producto_id = $isProducto[$i];
                    /******************registro de movimiento de producto******************/
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('factura_fecha');
                    $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    $movimientoProducto->movimiento_precio=$pu[$i];
                    $movimientoProducto->movimiento_iva=$iva[$i];
                    $movimientoProducto->movimiento_total=$total[$i];
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='FACTURA DE VENTA';
                    $movimientoProducto->movimiento_motivo='VENTA';
                    $movimientoProducto->movimiento_tipo='SALIDA';
                    $movimientoProducto->movimiento_descripcion='FACTURA DE VENTA No. '.$factura->factura_numero;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$factura->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de movimiento de producto por factura de venta numero -> '.$factura->factura_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                $detalleFV->movimiento()->associate($movimientoProducto);
                $factura->detalles()->save($detalleFV);
                $general->registrarAuditoria('Registro de detalle de factura de venta numero -> '.$factura->factura_numero,$factura->factura_numero,'Registro de detalle de factura de venta numero -> '.$factura->factura_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
                
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $total[$i];
                    $detalleDiario->detalle_comentario = 'P/R VENTA DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                    $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'VENTAS')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general=='1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_venta;
                    }
                   
                    $diario->detalles()->save($detalleDiario);
      
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$total[$i]);
                    
                  
                    
                if($banderaP){
                    if($producto->producto_tipo == '1'){
                       
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = 0.00;
                            $detalleDiario->detalle_haber = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'INVENTARIO')->first();
                            if($parametrizacionContable->parametrizacion_cuenta_general=='1'){
                                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            }else{
                                
                                $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                            }
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$detalleDiario->detalle_haber);
                            
                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe = $movimientoProducto->movimiento_costo_promedio;
                            $detalleDiario->detalle_haber = 0.00;
                            $detalleDiario->detalle_comentario = 'P/R COSTO DE INVENTARIO POR VENTA DE PRODUCTO '.$producto->producto_codigo;
                            $detalleDiario->detalle_tipo_documento = 'FACTURA';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';
                            $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            $parametrizacionContable  = Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'COSTOS DE MERCADERIA')->first();
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            $diarioC->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo,$factura->factura_numero,'Registro de detalle de diario con codigo -> '.$diarioC->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$detalleDiario->detalle_debe);
                        
                    }
                }
            }
            if($request->get('factura_tipo_pago') == 'EN EFECTIVO'){
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="ENTRADA";
                $movimientoCaja->movimiento_descripcion= 'P/R FACTURA DE VENTA :'.$request->get('buscarCliente');
                $movimientoCaja->movimiento_valor= $request->get('idTotal');
                $movimientoCaja->movimiento_documento="FACTURA DE VENTA";
                $movimientoCaja->movimiento_numero_documento= $factura->factura_numero;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                
                    $movimientoCaja->diario()->associate($diario);
                
                $movimientoCaja->save();
                /*********************************************************************/
            }
            /*******************************************************************/
            $urlRecibo = '';
            $url = '';
            $urlRecibo = $general->FacturaRecibo($factura,1);
            if(Auth::user()->empresa->empresa_contabilidad== '1'){
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            if($factura->factura_emision == 'ELECTRONICA'){
                $facturaAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlFacturaV2($factura),'FACTURA');
                $factura->factura_xml_estado = $facturaAux->factura_xml_estado;
                $factura->factura_xml_mensaje = $facturaAux->factura_xml_mensaje;
                $factura->factura_xml_respuestaSRI = $facturaAux->factura_xml_respuestaSRI;
                if($facturaAux->factura_xml_estado == 'AUTORIZADO'){
                    $factura->factura_xml_nombre = $facturaAux->factura_xml_nombre;
                    $factura->factura_xml_fecha = $facturaAux->factura_xml_fecha;
                    $factura->factura_xml_hora = $facturaAux->factura_xml_hora;
                }
                $factura->update();
            }
            if($facturaAux->factura_xml_estado == 'AUTORIZADO'){
                $urlPdf='documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('factura_fecha'))->format('d-m-Y').'/'.$factura->factura_xml_nombre.'.pdf';

                if (Schema::hasTable('parametrizacion_impresion_factura')) {
                    $parametrizacionImpresion=Parametrizacion_Impresion::ParametrizacionImpresion()->first();

                    if($parametrizacionImpresion){
                        if($parametrizacionImpresion->parametrizacioni_tipo==1) 
                            return redirect('/facturacionsinOrden/new/'.$request->get('punto_id'))->with('success','Factura registrada y autorizada exitosamente')->with('pdf',$urlPdf)->with('diario',$url);

                        if($parametrizacionImpresion->parametrizacioni_tipo==2) 
                            return redirect('/facturacionsinOrden/new/'.$request->get('punto_id'))->with('success','Factura registrada y autorizada exitosamente')->with('pdf2',$urlRecibo)->with('diario',$url);
                    }
                }
                
                return redirect('/facturacionsinOrden/new/'.$request->get('punto_id'))->with('success','Factura registrada y autorizada exitosamente')->with('pdf',$urlPdf)->with('pdf2',$urlRecibo)->with('diario',$url);

            }elseif($factura->factura_emision != 'ELECTRONICA'){
                return redirect('/facturacionsinOrden/new/'.$request->get('punto_id'))->with('success','Factura registrada exitosamente')->with('pdf2',$urlRecibo)->with('diario',$url);
            }else{
                return redirect('/facturacionsinOrden/new/'.$request->get('punto_id'))->with('success','Factura registrada exitosamente')->with('error2','ERROR SRI--> '.$facturaAux->factura_xml_estado.' : '.$facturaAux->factura_xml_mensaje)->with('pdf2',$urlRecibo)->with('diario',$url);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/facturacionsinOrden/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

}
