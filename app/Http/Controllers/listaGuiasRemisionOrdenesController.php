<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Cuenta_Cobrar;
use App\Models\Factura_Venta;
use App\Models\Forma_Pago;
use App\Models\Guia_Remision;
use App\Models\Orden_Despacho;
use App\Models\Prefactura_Venta;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tarifa_Iva;
use App\Models\User;
use App\Models\Vendedor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listaGuiasRemisionOrdenesController extends Controller
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
            $clientes = Orden_Despacho::ClientesOrdeneGuiasDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $guias=null;
            $estados=Orden_Despacho::EstadoOrdeneGuiasDistinsc()->select('gr_estado')->distinct()->get();
            $sucursales=Orden_Despacho::SucursalOrdeneGuiasDistinsc()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            return view('admin.ventas.guiaremision.vieworden',['sucursales'=>$sucursales,'estados'=>$estados,'guias'=>$guias,'clientes'=>$clientes, 'puntoEmisiones'=>$puntoEmisiones,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            return redirect('/denegado');
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

    public function extraer(Request $request)
    {
        try{
            $gr_id = $request->get('checkbox');
            DB::beginTransaction();           
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $usuario=User::Usuario(Auth::user()->user_id)->get()->first();
            $puntoemeision=$usuario->puntosEmision()->get()->first();       
            $datos = null;
            $guias = null;
            $coun = 1;    
            $cont = 1; 
            $banderaStock = '1';
            $inventarioResevado = false; 
            for ($i = 0; $i < count($gr_id); ++$i) {
                $guiadatos=Guia_Remision::GuiaOrden($gr_id[$i])->get()->first();
                $puntoemeision = $guiadatos->rangoDocumento->puntoEmision;
                $guias[$cont]['gr_id']=$gr_id[$i];
                $guias[$cont]['gr_numero']=$guiadatos->gr_numero;;
                $cont++;
                $guia=Guia_Remision::GuiaDetalle($gr_id[$i])->get();  
                $orden=Orden_Despacho::OrdenGuia($gr_id[$i])->get();
                for ($j = 0; $j < count($orden); ++$j) {
                    $ordene= Orden_Despacho::findOrFail($orden[$j]["orden_id"]);
                    if($ordene->orden_reserva == '1' ){
                        $inventarioResevado = true;
                    }
                    if($ordene->orden_reserva == '0' and $inventarioResevado == true){
                        throw new Exception('Hay ordenes con reserva de inventario y hay ordenes sin reserva de inventario, verifique la informacion antes de facturar');
                    }
                   }

                for ($j = 0; $j < count($guia); ++$j) {
                    $productoGuia = Producto::findOrFail($guia[$j]['producto_id']);
                    if($inventarioResevado == false){
                        if($productoGuia->producto_tipo == '1' and $productoGuia->producto_compra_venta == '3'){
                            if($productoGuia->producto_stock < $guia[$j]['detalle_cantidad']){
                                $banderaStock = '0';
                            }
                        }
                    }
                        $datos[$coun]['producto_id'] = $guia[$j]['producto_id'];
                        $datos[$coun]['detalle_cantidad'] = $guia[$j]['detalle_cantidad'];
                        $datos[$coun]['detalle_descripcion'] = $guia[$j]['detalle_descripcion'];
                        $datos[$coun]['detalle_precio_unitario'] = $guia[$j]['detalle_precio_unitario'];
                        $datos[$coun]['detalle_descuento'] = $guia[$j]['detalle_descuento'];
                        $datos[$coun]['detalle_iva'] = $guia[$j]['detalle_iva'];
                        $datos[$coun]['detalle_total'] = $guia[$j]['detalle_total'];         
                        $datos[$coun]['producto_codigo'] = $guia[$j]['producto_codigo'];
                        $datos[$coun]['producto_stock'] = $guia[$j]['producto_stock'];
                        $coun++;
                    
                }
            }
            $rangoDocumento=Rango_Documento::PuntoRango($puntoemeision->punto_id, 'Factura')->first();
            $secuencial=1;      
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Factura_Venta::secuencial($rangoDocumento->rango_id)->max('factura_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;

                }
                $general = new generalController();
                if($general->documentos()){
                    return view('admin.ventas.guiaremision.guiafactura',['guias'=>$guias,
                    'banderaStock'=>$banderaStock,
                    'datos'=>$datos,
                    'guiadatos'=>$guiadatos,
                    'vendedores'=>Vendedor::Vendedores()->get(),
                    'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                    'formasPago'=>Forma_Pago::formaPagos()->get(), 
                    'bodegas'=>Bodega::bodegasSucursal($puntoemeision->punto_id)->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'gruposPermiso'=>$gruposPermiso, 
                    'tipoPermiso'=>$tipoPermiso,
                    'permisosAdmin'=>$permisosAdmin])->with('error2Msg','No puede generar documentos electronicos contrate un plan');
                }
                return view('admin.ventas.guiaremision.guiafactura',
                ['guias'=>$guias,
                'banderaStock'=>$banderaStock,
                'datos'=>$datos,
                'guiadatos'=>$guiadatos,
                'vendedores'=>Vendedor::Vendedores()->get(),
                'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                'formasPago'=>Forma_Pago::formaPagos()->get(), 
                'bodegas'=>Bodega::bodegasSucursal($puntoemeision->punto_id)->get(),
                'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                'PE'=>Punto_Emision::puntos()->get(),
                'rangoDocumento'=>$rangoDocumento,
                'gruposPermiso'=>$gruposPermiso, 
                'tipoPermiso'=>$tipoPermiso,
                'permisosAdmin'=>$permisosAdmin]
                    );
            }else{
                $puntosEmision = Punto_Emision::PuntoxSucursal($puntoemeision->sucursal_id)->get();
                foreach($puntosEmision as $punto){
                    $rangoDocumento=Rango_Documento::PuntoRango($punto->punto_id, 'Factura')->first();
                    if($rangoDocumento){
                        $puntoemeision = $punto;
                        break;
                    }
                }
                if($rangoDocumento){
                    $secuencial=$rangoDocumento->rango_inicio;
                    $secuencialAux=Factura_Venta::secuencial($rangoDocumento->rango_id)->max('factura_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                    $general = new generalController();
                        if($general->documentos()){
                            return view('admin.ventas.guiaremision.guiafactura',['guias'=>$guias,
                            'banderaStock'=>$banderaStock,
                            'datos'=>$datos,
                            'guiadatos'=>$guiadatos,
                            'vendedores'=>Vendedor::Vendedores()->get(),
                            'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                            'formasPago'=>Forma_Pago::formaPagos()->get(), 
                            'bodegas'=>Bodega::bodegasSucursal($puntoemeision->punto_id)->get(),
                            'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                            'PE'=>Punto_Emision::puntos()->get(),
                            'rangoDocumento'=>$rangoDocumento,
                            'tipoPermiso'=>$tipoPermiso,
                            'gruposPermiso'=>$gruposPermiso, 
                            'permisosAdmin'=>$permisosAdmin])->with('error2Msg','No puede generar documentos electronicos contrate un plan');
                        }
                    return view('admin.ventas.guiaremision.guiafactura',
                    ['guias'=>$guias,
                    'banderaStock'=>$banderaStock,
                    'datos'=>$datos,
                    'guiadatos'=>$guiadatos,
                    'vendedores'=>Vendedor::Vendedores()->get(),
                    'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                    'formasPago'=>Forma_Pago::formaPagos()->get(), 
                    'bodegas'=>Bodega::bodegasSucursal($puntoemeision->punto_id)->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'tipoPermiso'=>$tipoPermiso,
                    'gruposPermiso'=>$gruposPermiso, 
                    'permisosAdmin'=>$permisosAdmin]
                        );
                }else{
                    return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
                }
            }
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
            $puntoEmisiones = Punto_Emision::puntos()->get();   
            $clientes = Orden_Despacho::ClientesOrdeneGuiasDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $guias=null;
            $estados=Orden_Despacho::EstadoOrdeneGuiasDistinsc()->select('gr_estado')->distinct()->get();
            $sucursales=Orden_Despacho::SucursalOrdeneGuiasDistinsc()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            $fechatodo=0;

          
            if($request->get('fecha_todo')){
                $fechatodo=$request->get('fecha_todo');
            }
            $guias=Orden_Despacho::GuiasTodosDiferentes($fechatodo,$request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('estados'),$request->get('nombre_cliente'),$request->get('sucursal'))->select('guia_remision.gr_id','guia_remision.gr_numero','guia_remision.gr_fecha','cliente_nombre','guia_remision.gr_punto_partida','guia_remision.gr_punto_destino','transportista_nombre','guia_remision.factura_id','guia_remision.gr_estado','guia_remision.gr_autorizacion')->distinct()->get();
            return view('admin.ventas.guiaremision.vieworden', ['sucursales'=>$sucursales,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_cliente'=>$request->get('nombre_cliente'),'valorestados'=>$request->get('estados'),'estados'=>$estados,'guias'=>$guias, 'puntoEmisiones'=>$puntoEmisiones, 'clientes'=>$clientes,  'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultarpre(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();  
            $puntoEmisiones = Punto_Emision::puntos()->get();   
            $clientes = Orden_Despacho::ClientesOrdeneGuiasDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $guias=null;
            $estados=Orden_Despacho::EstadoOrdeneGuiasDistinsc()->select('gr_estado')->distinct()->get();
            $sucursales=Orden_Despacho::SucursalOrdeneGuiasDistinsc()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            $fechatodo=0;

          
            if($request->get('fecha_todo')){
                $fechatodo=$request->get('fecha_todo');
            }
            $guias=Orden_Despacho::GuiasTodosDiferentes($fechatodo,$request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('estados'),$request->get('nombre_cliente'),$request->get('sucursal'))->select('guia_remision.gr_id','guia_remision.gr_numero','guia_remision.gr_fecha','cliente_nombre','guia_remision.gr_punto_partida','guia_remision.gr_punto_destino','transportista_nombre','guia_remision.factura_id','guia_remision.gr_estado','guia_remision.gr_autorizacion')->distinct()->get();
            return view('admin.ventas.guiaremision.viewordenprefactura', ['sucursales'=>$sucursales,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_cliente'=>$request->get('nombre_cliente'),'valorestados'=>$request->get('estados'),'estados'=>$estados,'guias'=>$guias, 'puntoEmisiones'=>$puntoEmisiones, 'clientes'=>$clientes,  'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function prefacturas()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();  
            $puntoEmisiones = Punto_Emision::puntos()->get();   
            $clientes = Orden_Despacho::ClientesOrdeneGuiasDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();     
            $estados=Orden_Despacho::EstadoOrdeneGuiasDistinsc()->select('gr_estado')->distinct()->get();
            $sucursales=Orden_Despacho::SucursalOrdeneGuiasDistinsc()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            
            return view('admin.ventas.guiaremision.viewordenprefactura', ['sucursales'=>$sucursales,'estados'=>$estados, 'puntoEmisiones'=>$puntoEmisiones, 'clientes'=>$clientes,  'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function verificar(Request $request)
    {   
        if (isset($_POST['buscar'])){
            return $this->consultar($request);
        }
        if (isset($_POST['buscarpre'])){
            return $this->consultarpre($request);
        }
        if (isset($_POST['extraer'])){
            return $this->extraer($request);
        }
        if (isset($_POST['generar'])){
            return $this->generar($request);
        }
        else{
            return redirect('listaGuiasOrdenes')->with('error','Seleccion las Guias para generar una Factura. Vuelva a intentar. ');
        }
    }
    public function generar(Request $request)
    {
        try{
            $gr_id = $request->get('checkbox');
            DB::beginTransaction();           
            
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $usuario=User::Usuario(Auth::user()->user_id)->get()->first();
            $puntoemeision=$usuario->puntosEmision()->get()->first();       
            $datos = null;
            $guias = null;
            $coun = 1;    
            $cont = 1; 
            $banderaStock = '1';
            $inventarioResevado = false; 
            for ($i = 0; $i < count($gr_id); ++$i) {
                $guiadatos=Guia_Remision::GuiaOrden($gr_id[$i])->get()->first();
                $puntoemeision = $guiadatos->rangoDocumento->puntoEmision;
                $guias[$cont]['gr_id']=$gr_id[$i];
                $guias[$cont]['gr_numero']=$guiadatos->gr_numero;;
                $cont++;
                $guia=Guia_Remision::GuiaDetalle($gr_id[$i])->get();  
                $orden=Orden_Despacho::OrdenGuia($gr_id[$i])->get();
                for ($j = 0; $j < count($orden); ++$j) {
                    $ordene= Orden_Despacho::findOrFail($orden[$j]["orden_id"]);
                    if($ordene->orden_reserva == '1' ){
                        $inventarioResevado = true;
                    }
                    if($ordene->orden_reserva == '0' and $inventarioResevado == true){
                        throw new Exception('Hay ordenes con reserva de inventario y hay ordenes sin reserva de inventario, verifique la informacion antes de facturar');
                    }
                   }

                for ($j = 0; $j < count($guia); ++$j) {
                    $productoGuia = Producto::findOrFail($guia[$j]['producto_id']);
                    if($inventarioResevado == false){
                        if($productoGuia->producto_tipo == '1' and $productoGuia->producto_compra_venta == '3'){
                            if($productoGuia->producto_stock < $guia[$j]['detalle_cantidad']){
                                $banderaStock = '0';
                            }
                        }
                    }
                        $datos[$coun]['guia_id'] = $guia[$j]['gr_id'];
                        $datos[$coun]['guia_fecha'] = $guia[$j]['gr_fecha'];
                        $datos[$coun]['guia_numero'] = $guia[$j]['gr_numero'];
                        $datos[$coun]['transportista'] = $guia[$j]['transportista_nombre'];
                        $datos[$coun]['placa'] = $guia[$j]['gr_placa'];
                        $datos[$coun]['producto_id'] = $guia[$j]['producto_id'];
                        $datos[$coun]['detalle_cantidad'] = $guia[$j]['detalle_cantidad'];
                        $datos[$coun]['detalle_descripcion'] = $guia[$j]['detalle_descripcion'];
                        $datos[$coun]['detalle_precio_unitario'] = $guia[$j]['detalle_precio_unitario'];
                        $datos[$coun]['detalle_descuento'] = $guia[$j]['detalle_descuento'];
                        $datos[$coun]['detalle_iva'] = $guia[$j]['detalle_iva'];
                        $datos[$coun]['detalle_total'] = $guia[$j]['detalle_total'];         
                        $datos[$coun]['producto_codigo'] = $guia[$j]['producto_codigo'];
                        $datos[$coun]['producto_stock'] = $guia[$j]['producto_stock'];
                        $coun++;
                    
                }
            }
            $rangoDocumento=Rango_Documento::PuntoRango($puntoemeision->punto_id, 'PreFactura')->first();
            $secuencial=1;      
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Prefactura_Venta::secuencial($rangoDocumento->rango_id)->max('prefactura_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;
                }
                return view('admin.ventas.guiaremision.guiaprefactura',
                ['guias'=>$guias,
                'banderaStock'=>$banderaStock,
                'datos'=>$datos,
                'guiadatos'=>$guiadatos,
                'vendedores'=>Vendedor::Vendedores()->get(),
                'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                'formasPago'=>Forma_Pago::formaPagos()->get(), 
                'bodegas'=>Bodega::bodegasSucursal($puntoemeision->punto_id)->get(),
                'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                'PE'=>Punto_Emision::puntos()->get(),
                
                'rangoDocumento'=>$rangoDocumento,
                'gruposPermiso'=>$gruposPermiso, 
                'tipoPermiso'=>$tipoPermiso,
                'permisosAdmin'=>$permisosAdmin]
                    );
            }else{
                $puntosEmision = Punto_Emision::PuntoxSucursal($puntoemeision->sucursal_id)->get();
                foreach($puntosEmision as $punto){
                    $rangoDocumento=Rango_Documento::PuntoRango($punto->punto_id, 'PreFactura')->first();
                    if($rangoDocumento){
                        $puntoemeision = $punto;
                        break;
                    }
                }
                if($rangoDocumento){
                    $secuencial=$rangoDocumento->rango_inicio;
                    $secuencialAux=Prefactura_Venta::secuencial($rangoDocumento->rango_id)->max('prefactura_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                    return view('admin.ventas.guiaremision.guiaprefactura',
                    ['guias'=>$guias,
                    'banderaStock'=>$banderaStock,
                    'datos'=>$datos,
                    'guiadatos'=>$guiadatos,
                    'vendedores'=>Vendedor::Vendedores()->get(),
                    'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
                    'formasPago'=>Forma_Pago::formaPagos()->get(), 
                    'bodegas'=>Bodega::bodegasSucursal($puntoemeision->punto_id)->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'tipoPermiso'=>$tipoPermiso,
                    'gruposPermiso'=>$gruposPermiso, 
                    'permisosAdmin'=>$permisosAdmin]
                    );
                }else{
                    return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
                }
            }
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
    public function store(Request $request)
    {
        //
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $guia=Guia_Remision::Guia($id)->first();
        
            if($guia){
                return view('admin.ventas.guiaremision.visualizarorden',['guias'=>$guia,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try{
            DB::beginTransaction();
            $ordenes=Orden_Despacho::prueba($id)->get();
            $general = new generalController();
            $cierre = $general->cierre($ordenes->orden_fecha,Rango_Documento::rango($ordenes->rango_id)->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('listaGuiasOrdenes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach($ordenes as $orden){
                $ordene=Orden_Despacho::findOrFail($orden->orden_id);
                $ordene->gr_id=null;
                $ordene->orden_estado='1';
                $ordene->save();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Actualizacion del Id a null para la eliminacion de la Guia de remision N° '.$orden->guia->gr_numero.'relacionado a la orden de despacho'.$orden->orden_numero,$orden->guia->gr_numero,'Permiso con id -> '.$id);            
            }
            
            $guias = Guia_Remision::findOrFail($id);
            foreach ($guias->detalles as $guia) {
                $guia->delete();
                $auditoria->registrarAuditoria('Eliminacion del detalle de la Guia Remision N°-> '.$guias->gr_numero,$guias->gr_numero,'Eliminacion del detalle de la Guia Remision N°-> '.$guias->gr_numero. ' con producto nombre -> '.$guia->producto->producto_nombre.' cantidad -> '.$guia->detalle_cantidad);
            } 
            $guias->delete();
        
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de Guia Remision N°-> '.$guias->gr_numero,$guias->gr_numero,'Permiso con id -> '.$id);
            
            DB::commit();
            return redirect('listaGuiasOrdenes')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaGuiasOrdenes')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }    
    }
    
    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $guia=Guia_Remision::Guia($id)->first();
        
            if($guia){
                return view('admin.ventas.guiaremision.eliminarorden',['guias'=>$guia,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

}
