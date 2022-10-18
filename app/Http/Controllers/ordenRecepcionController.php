<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anticipo_Proveedor;
use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Centro_Consumo;
use App\Models\Concepto_Retencion;
use App\Models\Cuenta_Pagar;
use App\Models\Descuento_Anticipo_Proveedor;
use App\Models\Detalle_Diario;
use App\Models\Detalle_OR;
use App\Models\Detalle_Pago_CXP;
use App\Models\Detalle_RC;
use App\Models\Detalle_TC;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Producto;
use App\Models\Orden_Recepcion;
use App\Models\Pago_CXP;
use App\Models\Parametrizacion_Contable;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Retencion_Compra;
use App\Models\Sustento_Tributario;
use App\Models\Tarifa_Iva;
use App\Models\Tipo_Comprobante;
use App\Models\Transaccion_Compra;
use App\Models\User;
use DateTime;
use Exception;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ordenRecepcionController extends Controller
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
            $orden = null;  
            $proveedores = Orden_Recepcion::ProveedorDistinsc()->select('proveedor.proveedor_id','proveedor.proveedor_nombre')->distinct()->get();
            $estados = Orden_Recepcion::EstadoDistinsc()->select('ordenr_estado')->distinct()->get();
            $sucursales= Orden_Recepcion::SucursalDistinsc()->select('sucursal_nombre')->distinct()->get();
            return view('admin.compras.ordenrecepcion.index',['sucursales'=>$sucursales,'estados'=>$estados,'proveedores'=>$proveedores ,'orden'=>$orden,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function nuevo($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Orden de Recepción')->first();   
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Orden_Recepcion::secuencial($rangoDocumento->rango_id)->max('ordenr_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                return view('admin.compras.ordenrecepcion.nuevo',['secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($rangoDocumento->puntoEmision->sucursal_id)->get(), 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir comprobante de retencion, configueros y vuelva a intentar');
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
    public function buscar(Request $request)
    {
        if (isset($_POST['buscar'])){
            return $this->consultar($request);
        }
        if (isset($_POST['extraer'])){
            return $this->extraer($request);
        }
    }
    public function consultar(Request $request)
    {
        try {    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();  
            $proveedores = Orden_Recepcion::ProveedorDistinsc()->select('proveedor.proveedor_id','proveedor.proveedor_nombre')->distinct()->get();
            $estados = Orden_Recepcion::EstadoDistinsc()->select('ordenr_estado')->distinct()->get();
            $sucursales= Orden_Recepcion::SucursalDistinsc()->select('sucursal.sucursal_id','sucursal_nombre')->distinct()->get();
            $valor_proveedor=$request->get('nombre_proveedor');
            $valor_estado=$request->get('estados');
            $ordenes=null;
            
            $ordenes=Orden_Recepcion::Filtrarbusqueda($request->get('fecha_todo'),$request->get('fecha_desde'),$request->get('fecha_hasta'),$valor_estado,$valor_proveedor,$request->get('sucursal'))->get();              
                         
            
            
            return view('admin.compras.ordenrecepcion.index',['fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'idsucursal'=>$request->get('sucursal'),'sucursales'=>$sucursales,'fecha_todo'=>$request->get('fecha_todo'),'idproveedor'=>$request->get('nombre_proveedor'),'valorestados'=>$request->get('estados'),'orden'=>$ordenes,'estados'=>$estados,'proveedores'=>$proveedores, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('ordenRecepecion')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function extraer(Request $request)
    {
        try{      
            $orden_id = $request->get('ORDEN_ID');
            $cont = 0; 
            $ide=null;
            for ($i = 0; $i < count($orden_id); ++$i) {
                $aux=Orden_Recepcion::Orden($orden_id[$i])->get()->first();
                if ($cont==0) {                 
                    $ide=$aux->proveedor_id;            
                }
                else{
                    if($ide!=$aux->proveedor_id){
                        return redirect('ordenRecepecion')->with('error2','Las Ordenes debe ser del mismo proveedore intentelo de nuevo. ');
                    }
                }
                $cont++;
            }        
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $usuario=User::Usuario(Auth::user()->user_id)->get()->first();
            $puntoemeision=$usuario->puntosEmision()->get()->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $datos = null;
            $ordenes = null;
            $cont = 1; 
            $coun = 1;           
            for ($i = 0; $i < count($orden_id); ++$i) {  
                $aux=Orden_Recepcion::Orden($orden_id[$i])->get()->first();
                $ordenes[$cont]['orden_id']=$aux->ordenr_id;
                $ordenes[$cont]['orden_numero']=$aux->ordenr_numero;
                $cont++;
                
                foreach($aux->detalles as $orden) {
                    $datos[$coun]['producto_id'] = $orden->producto_id;
                    $datos[$coun]['detalle_cantidad'] = $orden->detalle_cantidad;
                    $datos[$coun]['detalle_descripcion'] = $orden->producto->producto_nombre;
                    $datos[$coun]['Codigo'] = $orden->producto->producto_codigo;
                    
                    $datos[$coun]['precio'] = $orden->producto->producto_precio1;
                    $datos[$coun]['descuento'] = 0.00;
                    $datos[$coun]['total'] = 0.00;
                    $datos[$coun]['bieniva'] = 0.00;
                    $datos[$coun]['servicioiva'] = 0.00;
                    $datos[$coun]['Sub'] = 0.00;
                    $datos[$coun]['Tarifa12'] = 0.00;
                    $datos[$coun]['Tarifa0'] = 0.00;
                    $datos[$coun]['Iva'] = 0.00;
                    $datos[$coun]['Diva'] = 0.00;
                    $datos[$coun]['Total'] = 0.00;
                    $datos[$coun]['bodega'] = $aux->bodega->bodega_nombre;
                    $datos[$coun]['bodegaid'] =  $aux->bodega->bodega_id;
                    if($orden->producto->producto_tipo == '1'){
                        $datos[$coun]['servicio'] =  'Bien';
                    }else{             
                        $datos[$coun]['servicio'] =  'Servicio';
                    }              
                    if ($orden->producto->producto_tiene_iva=='1') {
                        $datos[$coun]['TieneIva'] = 'SI';                 
                    }
                    else{               
                        $datos[$coun]['TieneIva'] = 'NO';       
                    }
                    
                    $coun++;
                }
            }
            $rangoDocumento=Rango_Documento::PuntoRango($puntoemeision->punto_id,'Comprobante de Retención')->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Retencion_Compra::secuencial($rangoDocumento->rango_id)->max('retencion_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
               
                return view('admin.compras.ordenrecepcion.nuevoFacturaCompra',['cajaAbierta'=>$cajaAbierta,'orden'=>$aux,'datos'=>$datos,'ordenes'=>$ordenes,'rangoDocumento'=>$rangoDocumento,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'bodegas'=>Bodega::BodegasSucursal(Punto_Emision::Punto($puntoemeision->punto_id)->first()->sucursal_id)->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'comprobantes'=>Tipo_Comprobante::tipos()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                    
                $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($puntoemeision->punto_id)->sucursal_id)->get();
                foreach($puntosEmision as $punto){
                    $rangoDocumento=Rango_Documento::PuntoRango($punto->punto_id, 'Comprobante de Retención')->first();
                    if($rangoDocumento){
                        $puntoemeision=$punto;
                        break;
                    }
                }
                if($rangoDocumento){
                    $secuencial=$rangoDocumento->rango_inicio;
                    $secuencialAux=Retencion_Compra::secuencial($rangoDocumento->rango_id)->max('retencion_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                    return view('admin.compras.ordenrecepcion.nuevoFacturaCompra',['cajaAbierta'=>$cajaAbierta,'orden'=>$aux,'datos'=>$datos,'ordenes'=>$ordenes,'rangoDocumento'=>$rangoDocumento,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'bodegas'=>Bodega::BodegasSucursal(Punto_Emision::Punto($puntoemeision->punto_id)->first()->sucursal_id)->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'comprobantes'=>Tipo_Comprobante::tipos()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                }else{
                    return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir retenciones, configueros y vuelva a intentar');
                }
            }            
           
        }
        catch(\Exception $ex){
            return redirect('ordenRecepecion')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            
            $cantidad = $request->get('Dcantidad');
            
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            /********************cabecera de orden de venta ********************/
            $general = new generalController();           
            $orden = new Orden_Recepcion();

            $cierre = $general->cierre($request->get('orden_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/ordenRecepcion/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $orden->ordenr_numero = $request->get('orden_serie').substr(str_repeat(0, 9).$request->get('orden_numero'), - 9);
            $orden->ordenr_serie = $request->get('orden_serie');
            $orden->ordenr_secuencial = $request->get('orden_numero');
            if ($request->get('idGuia')) {
                $orden->ordenr_guia = $request->get('idGuia');
            }
            $orden->ordenr_fecha = $request->get('orden_fecha');
            if($request->get('orden_comentario')){
                $orden->ordenr_observacion = $request->get('orden_comentario');
            }else{
                $orden->ordenr_observacion = '';
            }  
            $orden->ordenr_estado = '1';
            $orden->bodega_id = $request->get('bodega_id');
            $orden->proveedor_id = $request->get('proveedorID');
            $orden->rango_id = $request->get('rango_id'); 
            $orden->save();
            $general->registrarAuditoria('Registro de orden de Rececpion numero -> '.$orden->ordenr_numero,$orden->ordenr_numero,'Registro de orden de Rececpion numero -> '.$orden->ordenr_numero.' con proveedor -> '.$request->get('buscarProveedor'));
            /*******************************************************************/
            /********************detalle de factura de venta********************/
           
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleOR = new Detalle_OR();
                $detalleOR->detalle_cantidad = $cantidad[$i];            
                $detalleOR->detalle_estado = '1';
                $detalleOR->producto_id = $isProducto[$i];

               
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('orden_fecha');
                    $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    $movimientoProducto->movimiento_precio=0;
                    $movimientoProducto->movimiento_iva=0;   
                    $movimientoProducto->movimiento_total=0;
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='ORDEN DE RECEPCION';
                    $movimientoProducto->movimiento_motivo='COMPRA';
                    $movimientoProducto->movimiento_tipo='ENTRADA';
                    $movimientoProducto->movimiento_descripcion='ORDEN DE RECEPCION No. '.$orden->ordenr_numero;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$orden->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por orden de Recepcion numero -> '.$orden->ordenr_numero, $orden->ordenr_numero, 'Registro de movimiento de producto por orden de despacho numero -> '.$orden->ordenr_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                    $detalleOR->movimiento()->associate($movimientoProducto);
                

                $orden->detalles()->save($detalleOR);

                $general->registrarAuditoria('Registro de detalle de orden de Recepcion numero -> '.$orden->ordenr_numero,$orden->ordenr_numero,'Registro de detalle de orden de Despacho numero -> '.$orden->ordenr_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i]);
            
            } 
                
            $empresa =  Empresa::empresa()->first();
           
             DB::commit();
            $view =  \View::make('admin.formatosPDF.ordenRecepciones', ['orden'=>$orden,'empresa'=>$empresa]);
            $ruta = public_path().'/documentos/OrdenRecepcion/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('orden_fecha'))->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'OR-'.$orden->ordenr_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            return redirect('/ordenRecepcion/new/'.$request->get('punto_id'))->with('success','Orden registrada exitosamente')->with('pdf','/documentos/OrdenRecepcion/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('orden_fecha'))->format('d-m-Y').'/'.$nombreArchivo.'.pdf');

        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/ordenRecepcion/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    
    public function guardarorden(Request $request)
    {
        try{
            DB::beginTransaction();
            $compraAux = Transaccion_Compra::TransaccionDuplicada($request->get('transaccion_serie').substr(str_repeat(0, 9).$request->get('transaccion_secuencial'), - 9),$request->get('tipo_comprobante_id'),$request->get('proveedorID'))->first();
            if(isset($compraAux->transaccion_id)){
                throw new Exception('Ese documento ya se encuentra registrado en el sistema.');
            }
            $valorCXP= $request->get('idTotal')-$request->get('id_total_fuente')-$request->get('id_total_iva');
            /********************detalle de la compra ********************/
            $ordenes = $request->get('ordenes');
        
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            $bodega = $request->get('Dbodega');
            $cconsumo = $request->get('Dcconsumo');
            $descripcion = $request->get('Ddescripcion');
            /****************************************************************/
            /***********************detalle de la retencion **********************/
            $baseF = $request->get('DbaseRF');
            $idRetF = $request->get('DRFID');
            $porcentajeF = $request->get('DporcentajeRF');
            $valorF = $request->get('DvalorRF');

            $baseI = $request->get('DbaseRI');
            $idRetI = $request->get('DRIID');
            $porcentajeI = $request->get('DporcentajeRI');
            $valorI = $request->get('DvalorRI');
            /*********************************************************************/
            /********************cabecera de transaccion de compra ********************/
            $general = new generalController();
            $docElectronico = new facturacionElectronicaController();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $transaccion = new Transaccion_Compra();
            $general = new generalController();
          
            $cierre = $general->cierre($request->get('transaccion_fecha'),Punto_Emision::Punto($request->get('punto_id'))->first()->sucursal_id);          
            if($cierre){
                return redirect('ordenRecepecion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cierre = $general->cierre($request->get('transaccion_inventario'),Punto_Emision::Punto($request->get('punto_id'))->first()->sucursal_id);          
            if($cierre){
                return redirect('ordenRecepecion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            /*VERIFICAR TOTAL DE IVA DE DETALLE CON EL TOTAL DE IVA DE LA FACTURA*/
            $totalDetalleIva = 0;
            for ($i = 1; $i < count($cantidad); ++$i){
                $totalDetalleIva = floatval($totalDetalleIva) + floatval($iva[$i]);
            }
            $squareVat = round($totalDetalleIva,2) == round(floatval($request->get('idIva')),2) ? false : true;
            /*******/
            $transaccion->transaccion_fecha = $request->get('transaccion_fecha');
            $transaccion->transaccion_caducidad = $request->get('transaccion_caducidad');
            $transaccion->transaccion_impresion = $request->get('transaccion_impresion');
            $transaccion->transaccion_vencimiento = $request->get('transaccion_vencimiento');
            $transaccion->transaccion_inventario = $request->get('transaccion_inventario');
            $transaccion->transaccion_numero = $request->get('transaccion_serie').substr(str_repeat(0, 9).$request->get('transaccion_secuencial'), - 9);
            $transaccion->transaccion_serie = $request->get('transaccion_serie');
            $transaccion->transaccion_secuencial = $request->get('transaccion_secuencial');
            $transaccion->transaccion_subtotal = $request->get('idSubtotal');
            $transaccion->transaccion_descuento = $request->get('idDescuento');
            $transaccion->transaccion_tarifa0 = $request->get('idTarifa0');
            $transaccion->transaccion_tarifa12 = $request->get('idTarifa12');
            $transaccion->transaccion_iva = $request->get('idIva');
            $transaccion->transaccion_total = $request->get('idTotal');
            $transaccion->transaccion_ivaB = $request->get('IvaBienesID');
            $transaccion->transaccion_ivaS = $request->get('IvaServiciosID');
            $transaccion->transaccion_dias_plazo = $request->get('transaccion_dias_plazo');
            $transaccion->transaccion_descripcion = $request->get('transaccion_descripcion');
            $transaccion->transaccion_tipo_pago = $request->get('transaccion_tipo_pago');
            $transaccion->transaccion_porcentaje_iva = $request->get('transaccion_porcentaje_iva');
            $transaccion->transaccion_autorizacion = $request->get('transaccion_autorizacion');
            $transaccion->transaccion_estado = '1';
            $transaccion->proveedor_id = $request->get('proveedorID');
            $transaccion->tipo_comprobante_id = $request->get('tipo_comprobante_id');
            $transaccion->sustento_id = $request->get('sustento_id');
            $transaccion->sucursal_id = Punto_Emision::Punto($request->get('punto_id'))->first()->sucursal_id;
           
            $tipoComprobante = Tipo_Comprobante::tipo($request->get('tipo_comprobante_id'))->first();
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                /********************cuenta por pagar***************************/
                $cxp = new Cuenta_Pagar();
                $cxp->cuenta_descripcion = strtoupper($tipoComprobante->tipo_comprobante_nombre).' DE COMPRA A PROVEEDOR '.$request->get('buscarProveedor').' CON DOCUMENTO No. '.$transaccion->transaccion_numero;
                if($request->get('transaccion_tipo_pago') == 'CREDITO' or $request->get('transaccion_tipo_pago') == 'CONTADO'){
                    $cxp->cuenta_tipo =$request->get('transaccion_tipo_pago');
                    $cxp->cuenta_saldo = $valorCXP;
                    $cxp->cuenta_estado = '1';
                }else{
                    $cxp->cuenta_tipo = $request->get('transaccion_tipo_pago');
                    $cxp->cuenta_saldo = 0.00;
                    $cxp->cuenta_estado = '2';
                }
                $cxp->cuenta_fecha = $request->get('transaccion_fecha');
                $cxp->cuenta_fecha_inicio = $request->get('transaccion_fecha');
                $cxp->cuenta_fecha_fin = date("Y-m-d",strtotime($request->get('transaccion_fecha')."+ ".$request->get('transaccion_dias_plazo')." days"));
                $cxp->cuenta_monto = $valorCXP;
                $cxp->cuenta_valor_factura = $request->get('idTotal');
                $cxp->proveedor_id = $transaccion->proveedor_id;
                $cxp->sucursal_id = $transaccion->sucursal_id;
                $cxp->save();
                $general->registrarAuditoria('Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero,$transaccion->transaccion_numero,'Registro de cuenta por pagar de factura -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal'));
                /****************************************************************/
                $transaccion->cuentaPagar()->associate($cxp);
            }
                /**********************asiento diario****************************/
                $diario = new Diario();
                if($tipoComprobante->tipo_comprobante_codigo == '01'){
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'),'CFCR');
                    $diario->diario_tipo = 'CFCR';
                }else if($tipoComprobante->tipo_comprobante_codigo == '04'){
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'),'CNCR');
                    $diario->diario_tipo = 'CNCR';
                }else if($tipoComprobante->tipo_comprobante_codigo == '05'){
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'),'CNDR');
                    $diario->diario_tipo = 'CNDR';
                }else{
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('transaccion_fecha'),'CTCR');
                    $diario->diario_tipo = 'CTCR';
                }
                $diario->diario_fecha = $request->get('transaccion_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' DE COMPRA';
                $diario->diario_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $diario->diario_numero_documento = $transaccion->transaccion_numero;
                $diario->diario_beneficiario = $request->get('buscarProveedor');
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('transaccion_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('transaccion_fecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DIARIO DE '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' : '.$transaccion->transaccion_numero;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = $transaccion->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de compra con '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' -> '.$transaccion->transaccion_numero,$transaccion->transaccion_numero,'Registro de diario de compra de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
                /****************************************************************/
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                if($cxp->cuenta_estado == '2'){
                    /********************Pago por compra en efectivo***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = 'PAGO EN EFECTIVO';
                    $pago->pago_fecha = $cxp->cuenta_fecha;
                    $pago->pago_tipo = 'PAGO EN EFECTIVO';
                    $pago->pago_valor = $cxp->cuenta_monto;
                    $pago->pago_estado = '1';
                    $pago->diario()->associate($diario);
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = 'PAGO EN EFECTIVO';
                    $detallePago->detalle_pago_valor = $cxp->cuenta_monto; 
                    $detallePago->detalle_pago_cuota = 1;
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_pagar_id = $cxp->cuenta_id; 
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();
                /****************************************************************/
                }
            }
            if($tipoComprobante->tipo_comprobante_codigo == '04'){
                $facturaAux = Transaccion_Compra::Transaccion($request->get('factura_id'))->first(); 
                $cxpAux = $facturaAux->cuentaPagar;
                $transaccion->transaccion_id_f = $facturaAux->transaccion_id;
                if(round($cxpAux->cuenta_saldo,2) == 0){
                    $anticipoProveedor = new Anticipo_Proveedor();
                    $anticipoProveedor->anticipo_fecha = $transaccion->transaccion_fecha;
                    $anticipoProveedor->anticipo_tipo = 'NOTA DE CRÉDITO';      
                    $anticipoProveedor->anticipo_documento = $transaccion->transaccion_numero;     
                    $anticipoProveedor->anticipo_motivo = $transaccion->transaccion_descripcion;
                    $anticipoProveedor->anticipo_valor = $request->get('idTotal');  
                    $anticipoProveedor->anticipo_saldo = $request->get('idTotal');   
                    $anticipoProveedor->proveedor_id = $facturaAux->proveedor_id;
                    $anticipoProveedor->sucursal_id = $facturaAux->sucursal_id;
                    $anticipoProveedor->anticipo_estado = 1; 
                    $anticipoProveedor->diario()->associate($diario);
                    $anticipoProveedor->save();
                    $general->registrarAuditoria('Registro de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal');
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                }else if(round($cxpAux->cuenta_saldo,2) >= round($transaccion->transaccion_total,2)){
                    /********************Pago por Nota de Credito***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero;
                    $pago->pago_fecha = $request->get('transaccion_fecha');
                    $pago->pago_tipo = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $pago->pago_valor = $request->get('idTotal');
                    $pago->pago_estado = '1';
                    $pago->diario()->associate($diario);
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero; 
                    $detallePago->detalle_pago_valor = $request->get('idTotal'); 
                    $detallePago->detalle_pago_cuota = Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->count()+1; 
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_pagar_id = $cxpAux->cuenta_id; 
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();

                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto-round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2);
                    if(round($cxpAux->cuenta_saldo,2) == 0){
                        $cxpAux->cuenta_estado = '2';
                    }else{
                        $cxpAux->cuenta_estado = '1';
                    }
                    $cxpAux->update();
                    /****************************************************************/
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal');
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal'));
                    /*******************************************************************/
                }else{
                    /********************Anticipo a proveedor**************************/
                    $anticipoProveedor = new Anticipo_Proveedor();
                    $anticipoProveedor->anticipo_fecha = $transaccion->transaccion_fecha;
                    $anticipoProveedor->anticipo_tipo = 'NOTA DE CRÉDITO';      
                    $anticipoProveedor->anticipo_documento = $transaccion->transaccion_numero;     
                    $anticipoProveedor->anticipo_motivo = $transaccion->transaccion_descripcion;
                    $anticipoProveedor->anticipo_valor = $request->get('idTotal') - $cxpAux->cuenta_saldo; 
                    $anticipoProveedor->anticipo_saldo = $request->get('idTotal') - $cxpAux->cuenta_saldo;
                    $anticipoProveedor->proveedor_id = $facturaAux->proveedor_id;
                    $anticipoProveedor->sucursal_id = $facturaAux->sucursal_id;
                    $anticipoProveedor->anticipo_estado = 1; 
                    $anticipoProveedor->diario()->associate($diario);
                    $anticipoProveedor->save();
                    $general->registrarAuditoria('Registro de Anticipo de Proveedor -> '.$request->get('buscarProveedor'),'0','Con motivo: Nota de Crédito');
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $request->get('idTotal') - $cxpAux->cuenta_saldo;
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_anticipo;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$request->get('idTotal') - $cxpAux->cuenta_saldo);
                    /*******************************************************************/
                    /********************Pago por Nota de Credito***************************/
                    $pago = new Pago_CXP();
                    $pago->pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero;
                    $pago->pago_fecha = $request->get('transaccion_fecha');
                    $pago->pago_tipo = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                    $pago->pago_valor = $cxpAux->cuenta_saldo;
                    $pago->pago_estado = '1';
                    $pago->diario()->associate($diario);
                    $pago->save();

                    $detallePago = new Detalle_Pago_CXP();
                    $detallePago->detalle_pago_descripcion = $request->get('transaccion_descripcion'). ' POR '.strtoupper($tipoComprobante->tipo_comprobante_nombre).' No. '.$transaccion->transaccion_numero; 
                    $detallePago->detalle_pago_valor = $cxpAux->cuenta_saldo; 
                    $detallePago->detalle_pago_cuota = Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->count()+1; 
                    $detallePago->detalle_pago_estado = '1'; 
                    $detallePago->cuenta_pagar_id = $cxpAux->cuenta_id; 
                    $detallePago->pagoCXP()->associate($pago);
                    $detallePago->save();

                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux->transaccionCompra->transaccion_id)->sum('descuento_valor'),2);
                    if(round($cxpAux->cuenta_saldo,2) == 0){
                        $cxpAux->cuenta_estado = '2';
                    }else{
                        $cxpAux->cuenta_estado = '1';
                    }
                    $cxpAux->update();
                    /****************************************************************/
                    /********************detalle de diario de venta********************/
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $cxpAux->cuenta_saldo;
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                    $detalleDiario->detalle_tipo_documento = 'NOTA DE CRÉDITO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$cxpAux->cuenta_saldo);
                    /*******************************************************************/
                }
            }
            $transaccion->diario()->associate($diario);
            if($arqueoCaja){
                $transaccion->arqueo_id = $arqueoCaja->arqueo_id;
            }
            $transaccion->save();
            $general->registrarAuditoria('Registro por medio de orden de recepcion de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero.' con sustento tributario '.Sustento_Tributario::sustento($transaccion->sustento_id)->first()->sustento_codigo,$transaccion->transaccion_numero,'Registro de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$transaccion->transaccion_numero.' con proveedor -> '.$request->get('buscarProveedor').' con un total de -> '.$request->get('idTotal').' y con codigo de diario -> '.$diario->diario_codigo);
            /****************************************************************/
            /********************detalle de transaccion de compra********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleTC = new Detalle_TC();
                $detalleTC->detalle_cantidad = $cantidad[$i];
                $detalleTC->detalle_precio_unitario =$pu[$i];
                $detalleTC->detalle_descuento = $descuento[$i];
                $detalleTC->detalle_iva = $iva[$i];
                $detalleTC->detalle_total = $total[$i];
                $detalleTC->detalle_descripcion = $descripcion[$i];
                $detalleTC->detalle_estado = '1';
                $detalleTC->producto_id = $isProducto[$i];
                $detalleTC->bodega_id = $bodega[$i];
                $detalleTC->centro_consumo_id = $cconsumo[$i];
                
                /********************detalle de diario de compra********************/
                $producto = Producto::findOrFail($isProducto[$i]);
                $detalleDiario = new Detalle_Diario();
                if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                    $detalleDiario->detalle_debe = $total[$i];
                    $detalleDiario->detalle_haber = 0.00;
                }else{
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = $total[$i];
                }
                $detalleDiario->detalle_comentario = 'P/R '.$descripcion[$i];
                $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
               /* $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                if($producto->cuentaInventario){
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                }else{
                    $detalleDiario->cuenta_id = $producto->producto_cuenta_gasto;
                }*/
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'MERCADERIA POR RECEPTAR')->first();
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta_id.' por un valor de -> '.$total[$i]);
                /**********************************************************************/
                    
                //$detalleTC->movimiento()->associate($movimientoProducto);
                $transaccion->detalles()->save($detalleTC);
                $general->registrarAuditoria('Registro de detalle de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' de compra numero -> '.$request->get('factura_serie').$request->get('factura_numero'),$request->get('factura_serie').$request->get('factura_numero'),'Registro de detalle de '. strtoupper($tipoComprobante->tipo_comprobante_nombre) .' numero -> '.$request->get('factura_serie').$request->get('factura_numero').' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
            }
            /****************************************************************/
            $sustentoTributario = Sustento_Tributario::findOrFail($request->get('sustento_id'));
            /********************detalle de diario de compra********************/
            if ($request->get('IvaBienesID') > 0){
                $detalleDiario = new Detalle_Diario();
                if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                    if($squareVat){
                        $detalleDiario->detalle_debe = floatval($request->get('IvaBienesID')) + (floatval($request->get('idIva')) - floatval($totalDetalleIva));
                        $squareVat = false;
                    }else{
                        $detalleDiario->detalle_debe = $request->get('IvaBienesID');
                    }                    
                    $detalleDiario->detalle_haber = 0.00;
                }else{
                    $detalleDiario->detalle_debe = 0.00;
                    if($squareVat){
                        $detalleDiario->detalle_haber = floatval($request->get('IvaBienesID')) + (floatval($request->get('idIva')) - floatval($totalDetalleIva));
                        $squareVat = false;
                    }else{
                        $detalleDiario->detalle_haber = $request->get('IvaBienesID');
                    }
                }
                $detalleDiario->detalle_comentario = 'P/R IVA PAGADO POR BIENES EN COMPRA';
                $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($sustentoTributario->sustento_credito == '1'){
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS PRODUCCION')->first();
                }else{
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS GASTO')->first();
                }
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('IvaBienesID'));
            }
            if ($request->get('IvaServiciosID') > 0){
                $detalleDiario = new Detalle_Diario();
                if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                    if($squareVat){
                        $detalleDiario->detalle_debe = floatval($request->get('IvaServiciosID')) + (floatval($request->get('idIva')) - floatval($totalDetalleIva));
                        $squareVat = false;
                    }else{
                        $detalleDiario->detalle_debe = $request->get('IvaServiciosID');
                    }
                    $detalleDiario->detalle_haber = 0.00;
                }else{
                    $detalleDiario->detalle_debe = 0.00;
                    if($squareVat){
                        $detalleDiario->detalle_haber = floatval($request->get('IvaServiciosID')) + (floatval($request->get('idIva')) - floatval($totalDetalleIva));
                        $squareVat = false;
                    }else{
                        $detalleDiario->detalle_haber = $request->get('IvaServiciosID');
                    }
                }
                $detalleDiario->detalle_comentario = 'P/R IVA PAGADO POR SERVICIOS EN COMPRAS';
                $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($sustentoTributario->sustento_credito == '1'){
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS PRODUCCION')->first();
                }else{
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'IVA COMPRAS GASTO')->first();
                }
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$request->get('IvaServiciosID'));
            }
            /****************************************************************/
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                /************************Retencion de compra********************/
                $retencion = new Retencion_Compra();
                $retencion->retencion_fecha = $request->get('retencion_fecha');
                $retencion->retencion_numero = $request->get('retencion_serie').substr(str_repeat(0, 9).$request->get('retencion_secuencial'), - 9);
                $retencion->retencion_serie = $request->get('retencion_serie');
                $retencion->retencion_secuencial = $request->get('retencion_secuencial');
                $retencion->retencion_emision = $request->get('tipoDoc');
                $retencion->retencion_ambiente = 'PRODUCCIÓN';
                $retencion->retencion_autorizacion = $docElectronico->generarClaveAcceso($retencion->retencion_numero,$request->get('retencion_fecha'),"07");
                $retencion->retencion_estado = '1';
                $retencion->rango_id = $request->get('rango_id');
                $retencion->transaccionCompra()->associate($transaccion);
                $retencion->save();
                $general->registrarAuditoria('Registro de retencion de compra numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de retencion de compra numero -> '.$retencion->retencion_numero.' y con codigo de diario -> '.$diario->diario_codigo);
                /******************************************************************/
                $diario->diario_comentario = $diario->diario_comentario.' RET: '.$retencion->retencion_numero;
                $diario->update();
                /********************Detalle retencion de compra*******************/
                for ($i = 1; $i < count($baseF); ++$i){
                    $detalleRC = new Detalle_RC();
                    $detalleRC->detalle_tipo = 'FUENTE';
                    $detalleRC->detalle_base = $baseF[$i];
                    $detalleRC->detalle_porcentaje = $porcentajeF[$i];
                    $detalleRC->detalle_valor = $valorF[$i];
                    $detalleRC->detalle_asumida = '0';
                    $detalleRC->detalle_estado = '1';
                    $detalleRC->concepto_id = $idRetF[$i];
                    $retencion->detalles()->save($detalleRC);
                    $general->registrarAuditoria('Registro de detalle de retencion de compra numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de detalle de retencion de compra, con base imponible -> '.$baseF[$i].' porcentaje -> '.$porcentajeF[$i].' valor de retencion -> '.$valorF[$i]);
                    if($valorF[$i] > 0){    
                        /********************detalle de diario de compra*******************/
                        $detalleDiario = new Detalle_Diario();
                        $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetF[$i])->first();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $valorF[$i];
                        $detalleDiario->detalle_comentario = 'P/R RETENCION EN LA FUENTE '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                        $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE COMPRA';
                        $detalleDiario->detalle_numero_documento = $retencion->retencion_numero;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_emitida_cuenta;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorF[$i]);
                        /******************************************************************/
                    }
                }
                for ($i = 1; $i < count($baseI); ++$i){
                    $detalleRC = new Detalle_RC();
                    $detalleRC->detalle_tipo = 'IVA';
                    $detalleRC->detalle_base = $baseI[$i];
                    $detalleRC->detalle_porcentaje = $porcentajeI[$i];
                    $detalleRC->detalle_valor = $valorI[$i];
                    $detalleRC->detalle_asumida = '0';
                    $detalleRC->detalle_estado = '1';
                    $detalleRC->concepto_id = $idRetI[$i];
                    $retencion->detalles()->save($detalleRC);
                    $general->registrarAuditoria('Registro de detalle de retencion de compra numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de detalle de retencion de compra, con base imponible -> '.$baseI[$i].' porcentaje -> '.$porcentajeI[$i].' valor de retencion -> '.$valorI[$i]);
                        /********************detalle de diario de compra*******************/
                        $detalleDiario = new Detalle_Diario();
                        $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetI[$i])->first();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $valorI[$i];
                        $detalleDiario->detalle_comentario = 'P/R RETENCION DE IVA '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                        $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE COMPRA';
                        $detalleDiario->detalle_numero_documento = $retencion->retencion_numero;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_emitida_cuenta;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorI[$i]);
                        /******************************************************************/
                }
            }
            if($tipoComprobante->tipo_comprobante_codigo <> '04'){
                /********************detalle de diario de compra*******************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = $valorCXP;
                $detalleDiario->detalle_tipo_documento = strtoupper($tipoComprobante->tipo_comprobante_nombre);
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                if($request->get('transaccion_tipo_pago') == 'CREDITO' OR $request->get('transaccion_tipo_pago') == 'CONTADO'){
                    $detalleDiario->proveedor_id = $request->get('proveedorID');
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){                       
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Proveedor::findOrFail($request->get('proveedorID'));
                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                    }
                }else{
                    $detalleDiario->detalle_comentario = 'P/R COMPRA EN EFECTIVO';
                    $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                    $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$transaccion->transaccion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$valorCXP);
                if($request->get('transaccion_tipo_pago') == 'EN EFECTIVO'){
                    /**********************movimiento caja****************************/
                    $movimientoCaja = new Movimiento_Caja();          
                    $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                    $movimientoCaja->movimiento_hora=date("H:i:s");
                    $movimientoCaja->movimiento_tipo="SALIDA";
                    $movimientoCaja->movimiento_descripcion= 'P/R FACTURA DE COMPRA :'.$request->get('buscarProveedor');
                    $movimientoCaja->movimiento_valor= $valorCXP;
                    $movimientoCaja->movimiento_documento="FACTURA DE COMPRA";
                    $movimientoCaja->movimiento_numero_documento= $transaccion->transaccion_numero;
                    $movimientoCaja->movimiento_estado = 1;
                    $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                    if(Auth::user()->empresa->empresa_contabilidad == '1'){
                        $movimientoCaja->diario()->associate($diario);
                    }
                    $movimientoCaja->save();
                    /*********************************************************************/
                }
                /******************************************************************/
                /********************retencion electronica************************/
                if($retencion->retencion_emision == 'ELECTRONICA'){
                    $retencionAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlRetencion($retencion),'RETENCION');
                    $retencion->retencion_xml_estado = $retencionAux->retencion_xml_estado;
                    $retencion->retencion_xml_mensaje =$retencionAux->retencion_xml_mensaje;
                    $retencion->retencion_xml_respuestaSRI = $retencionAux->retencion_xml_respuestaSRI;
                    if($retencionAux->retencion_xml_estado == 'AUTORIZADO'){
                        $retencion->retencion_xml_nombre = $retencionAux->retencion_xml_nombre;
                        $retencion->retencion_xml_fecha = $retencionAux->retencion_xml_fecha;
                        $retencion->retencion_xml_hora = $retencionAux->retencion_xml_hora;
                    }
                    $retencion->update();
                }
                /******************************************************************/
            }
            for ($i = 0; $i < count($ordenes); ++$i) {
                $orden=Orden_Recepcion::findOrFail($ordenes[$i]);
                /**********************asiento diario****************************/
                $diarioOR = new Diario();
                $diarioOR->diario_codigo = $general->generarCodigoDiario($orden->ordenr_fecha,'CORC');
                $diarioOR->diario_tipo = 'CORC';
                $diarioOR->diario_fecha = $orden->ordenr_fecha;
                $diarioOR->diario_referencia = 'COMPROBANTE DE ORDEN DE RECEPCION DE COMPRA';
                $diarioOR->diario_tipo_documento = 'ORDEN DE RECEPCION';
                $diarioOR->diario_numero_documento = $orden->ordenr_numero;
                $diarioOR->diario_beneficiario = $orden->proveedor->proveedor_nombre;
                $diarioOR->diario_secuencial = substr($diarioOR->diario_codigo, 8);
                $diarioOR->diario_mes = DateTime::createFromFormat('Y-m-d', $orden->ordenr_fecha)->format('m');
                $diarioOR->diario_ano = DateTime::createFromFormat('Y-m-d', $orden->ordenr_fecha)->format('Y');
                $diarioOR->diario_comentario = 'COMPROBANTE DE ORDEN DE RECEPCION DE COMPRA : '.$orden->ordenr_numero;
                $diarioOR->diario_cierre = '0';
                $diarioOR->diario_estado = '1';
                $diarioOR->empresa_id = Auth::user()->empresa_id;
                $diarioOR->sucursal_id = $orden->rangoDocumento->puntoEmision->sucursal_id;
                $diarioOR->save();
                $general->registrarAuditoria('Registro de diario de orden de recepcion de compra No. '.$orden->ordenr_numero,$orden->ordenr_numero,'Registro de diario de orden de recepcion de compra No. '.$orden->ordenr_numero.' con proveedor -> '.$orden->proveedor->proveedor_nombre.' con codigo de diario -> '.$diarioOR->diario_codigo);
                /****************************************************************/
                $precioTotal = 0.00;
                foreach($orden->detalles as $detalle){
                    /********************detalle de diario de compra********************/
                    $producto = Producto::findOrFail($detalle->producto_id);
                    $precio = 0.00;
                    foreach($transaccion->detalles as $detalleCompra){
                        if($detalleCompra->producto_id == $producto->producto_id){
                            $precio = $detalleCompra->detalle_precio_unitario;
                            break;
                        }
                    }
                    $mov = $detalle->movimiento;
                    $mov->movimiento_precio = $precio;
                    if($producto->producto_tiene_iva == '1'){
                        $mov->movimiento_iva = $precio*$mov->movimiento_cantidad*0.12;
                    }
                    $mov->movimiento_total = $precio*$mov->movimiento_cantidad;
                    $mov->update();
                    $precioTotal = $precioTotal+$mov->movimiento_total;

                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = round($mov->movimiento_total,2);
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R ORDEN DE RECEPCION DE COMPRA DE PRODUCTO '.$producto->producto_codigo;
                    $detalleDiario->detalle_tipo_documento = 'ORDEN DE RECEPCION';
                    $detalleDiario->detalle_numero_documento = $diarioOR->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->movimientoProducto()->associate($detalle->movimiento);
                    if($producto->producto_compra_venta == '3'){
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_inventario;
                    }else{
                        $detalleDiario->cuenta_id = $producto->producto_cuenta_gasto;
                    }
                    $diarioOR->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioOR->diario_codigo,$orden->ordenr_numero,'Registro de detalle de diario con codigo -> '.$diarioOR->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta_id.' por un valor de -> '.'');
                    /**********************************************************************/
                }
                /********************detalle de diario de compra********************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber =round($precioTotal,2);
                $detalleDiario->detalle_comentario = 'P/R MERCADERIA POR RECEPTAR';
                $detalleDiario->detalle_tipo_documento = 'ORDEN DE RECEPCION';
                $detalleDiario->detalle_numero_documento = $diarioOR->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diarioOR->sucursal_id, 'MERCADERIA POR RECEPTAR')->first();
                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                $diarioOR->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diarioOR->diario_codigo,$orden->ordenr_numero,'Registro de detalle de diario con codigo -> '.$diarioOR->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta_id.' por un valor de -> '.'');
                /**********************************************************************/
                $orden->transaccion_id=$transaccion->transaccion_id;
                $orden->diario()->associate($diarioOR);
                $orden->ordenr_estado='2';
                $orden->save();
            }
            $url = $general->pdfDiario($diario);
            DB::commit();
            if($tipoComprobante->tipo_comprobante_codigo == '04'){
                return redirect('ordenRecepecion')->with('success','Transaccion registrada exitosamente')->with('diario',$url);
            }else if($retencion->retencion_xml_estado == 'AUTORIZADO' and $tipoComprobante->tipo_comprobante_codigo <> '04'){
                return redirect('ordenRecepecion')->with('success','Transaccion registrada y autorizada exitosamente')->with('diario',$url)->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $retencion->retencion_fecha)->format('d-m-Y').'/'.$retencion->retencion_xml_nombre.'.pdf');
            }else{
                return redirect('ordenRecepecion')->with('success','Transaccion registrada exitosamente')->with('diario',$url)->with('error2','ERROR --> '.$retencionAux->retencion_xml_estado.' : '.$retencionAux->retencion_xml_mensaje);
            }
      
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('ordenRecepecion')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $orden=Orden_Recepcion::Orden($id)->first();
            if($orden){
                return view('admin.compras.ordenrecepcion.ver',['orden'=>$orden, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminar($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $orden=Orden_Recepcion::Orden($id)->first();
            if($orden){
                return view('admin.compras.ordenrecepcion.eliminar',['orden'=>$orden, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $orden=Orden_Recepcion::Orden($id)->first();
            
            if($orden){
                return view('admin.compras.ordenrecepcion.editar',['bodegas'=>Bodega::bodegasSucursal($orden->bodega->sucursal_id)->get(),'orden'=>$orden, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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

            
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
           

            /********************cabecera de orden de venta ********************/
            $general = new generalController();           
            $orden = Orden_Recepcion::findOrFail($id); 
            
            $cierre = $general->cierre($orden->ordenr_fecha,$orden->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('ordenRecepecion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cierre = $general->cierre($request->get('orden_fecha'),$orden->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('ordenRecepecion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            
            $orden->ordenr_fecha = $request->get('orden_fecha');
            if ($request->get('idGuia')) {
                $orden->ordenr_guia = $request->get('idGuia');
            }


            if($request->get('orden_comentario')){
                $orden->ordenr_observacion = $request->get('orden_comentario');
            }else{
                $orden->ordenr_observacion = '';
            }
            
                    
            $orden->ordenr_estado = '1';
           
           
            $orden->bodega_id = $request->get('bodega_id');
            $orden->proveedor_id = $request->get('proveedorID');
           

            $orden->update();
            $general->registrarAuditoria('Actualizada la orden de Recepcion numero -> '.$orden->ordenr_numero,$orden->ordenr_numero,'Actualizada la orden de Recepcion numero -> '.$orden->ordenr_numero.' con Proveedor -> '.$request->get('buscarProveedor'));
            /*******************************************************************/
            /********************detalle de factura de venta********************/
            foreach($orden->detalles as $detalles){

                $detall=Detalle_OR::findOrFail($detalles->detalle_id);
                $detall->movimiento_id=null;
                $detall->save();
                if (isset($detalles->movimiento)) {
                    $detalles->movimiento->delete();         
                }            
                $detall->delete();
            }
           
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleOR = new Detalle_OR();
                $detalleOR->detalle_cantidad = $cantidad[$i];            
                $detalleOR->detalle_estado = '1';
                $detalleOR->producto_id = $isProducto[$i];

               
                    $movimientoProducto = new Movimiento_Producto();
                    $movimientoProducto->movimiento_fecha=$request->get('orden_fecha');
                    $movimientoProducto->movimiento_cantidad=$cantidad[$i];
                    $movimientoProducto->movimiento_precio=0;
                    $movimientoProducto->movimiento_iva=0;   
                    $movimientoProducto->movimiento_total=0;
                    $movimientoProducto->movimiento_stock_actual=0;
                    $movimientoProducto->movimiento_costo_promedio=0;
                    $movimientoProducto->movimiento_documento='ORDEN DE RECEPCION';
                    $movimientoProducto->movimiento_motivo='COMPRA';
                    $movimientoProducto->movimiento_tipo='ENTRADA';
                    $movimientoProducto->movimiento_descripcion='ORDEN DE RECEPCION No. '.$orden->ordenr_numero;
                    $movimientoProducto->movimiento_estado='1';
                    $movimientoProducto->producto_id=$isProducto[$i];
                    $movimientoProducto->bodega_id=$orden->bodega_id;
                    $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                    $movimientoProducto->save();
                    $general->registrarAuditoria('Registro de movimiento de producto por orden de Recepcion numero -> '.$orden->ordenr_numero, $orden->ordenr_numero, 'Registro de movimiento de producto por orden de recepcion numero -> '.$orden->ordenr_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                    /*********************************************************************/
                    $detalleOR->movimiento()->associate($movimientoProducto);
                

                $orden->detalles()->save($detalleOR);

                $general->registrarAuditoria('Registro de detalle de orden de Recepcion numero -> '.$orden->ordenr_numero,$orden->ordenr_numero,'Registro de detalle de orden de Despacho numero -> '.$orden->ordenr_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i]);
            
            }       
            DB::commit();
            return redirect('ordenRecepecion')->with('success','Orden Actualizada exitosamente');
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('ordenRecepecion')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $orden=Orden_Recepcion::findOrFail($id);

            $general = new generalController();
            $cierre = $general->cierre($orden->ordenr_fecha,$orden->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('ordenRecepecion')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach($orden->detalles as $detalles){

                $detall=Detalle_OR::findOrFail($detalles->detalle_id);
                $detall->movimiento_id=null;
                $detall->save();
                if (isset($detalles->movimiento)) {
                    $aux = $detalles->movimiento;
                    $detalles->movimiento->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion de Movimiento de producto por Orden de Recepcion numero: -> '.$orden->ordenr_numero, $id, 'Con Producto '.$detalles->producto->producto_nombre.' Con la cantidad de producto de '.$aux->movimiemovimiento_totalnto_cantidad);
                }
                
                $aux = $detalles; 
                $detalles->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion de detalles de producto por Orden de Recepcion numero: -> '.$orden->ordenr_numero, $id, 'Con Producto '.$aux->producto->producto_nombre.' Con la cantidad de producto de '.$aux->detalle_cantidad);


            }
            
            $aux=$orden;
            $orden->delete();                        
           
            $general->registrarAuditoria('Eliminacion de la Orden de Recepcion numer numero: -> '.$orden->ordenr_numero, $id,'');

            DB::commit();
            return redirect('ordenRecepecion')->with('success','Datos Eliminados exitosamente');
                        
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('ordenRecepecion')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }
    public function imprimir($id){ 
        try{
            $orden = Orden_Recepcion::findOrFail($id);
            $empresa =  Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.ordenRecepciones', ['orden'=>$orden,'empresa'=>$empresa]);
            $ruta = public_path().'/documentos/OrdenRecepcion/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $orden->ordenr_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'OR-'.$orden->ordenr_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->stream('ordenRecepcion.pdf');
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
