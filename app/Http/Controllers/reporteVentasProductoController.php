<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Factura_Venta;
use App\Models\Producto;
use App\Models\sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class reporteVentasProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $totalCantidad = 0;
        $totalIva = 0;
        $total12=0;
        $total0=0;
        $totales =0;
        $totalLibras =0;
        $totalKilos =0;
        $totalTm =0;
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $productos = Producto::productos()->where('producto_compra_venta','=','3')->orwhere('producto_compra_venta','=','2')->get();
        $bodegas = Bodega::Bodegas()->get();
        return view('admin.inventario.reporteVentaxProducto.index',
        ['productos'=>$productos,
        'clientes'=>Cliente::clientes()->get(),
        'bodegas'=>$bodegas,   
        'totalCantidad'=>$totalCantidad,
        'totalIva'=>$totalIva,
        'totales'=>$totales,
        'total12'=>$total12,
        'total0'=>$total0,
        'totalLibras'=>$totalLibras,
        'totalKilos'=>$totalKilos,
        'totalTm'=>$totalTm,     
        'gruposPermiso'=>$gruposPermiso,         
        'permisosAdmin'=>$permisosAdmin]);
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
        try{
            $libras = 0;
            $kilos = 0;
            $tm = 0;
            $totalCantidad = 0;
            $totalIva = 0;
            $total12=0;
            $total0=0;
            $totales =0;
            $totalLibras =0;
            $totalKilos =0;
            $totalTm =0;
            $ventaxProductoMatriz = [];                
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ventasProductos=Factura_Venta::FacturaVentaxProducto($request->get('productoID'),$request->get('bodega_id'),$request->get('clienteID'),$request->get('idDesde'),$request->get('idHasta'))->get();   
            /*
            libras = CDbl(res!DESCRIPCION_TAMANO) * CDbl(res!CANTIDAD_DETALLE)
            kilos = libras * (0.453592)
            tm = kilos / 1000
            */
            $count = 1;            
            foreach($ventasProductos as $ventasProducto){
                $libras = floatval($ventasProducto->tamano->tamano_nombre) * $ventasProducto->detalle_cantidad;
                $kilos = $libras * 0.453592;
                $tm = $kilos / 100;
                /*SUMATORIAS*/
                $totalCantidad = $totalCantidad + $ventasProducto->detalle_cantidad;
                $totalIva = $totalIva + floatval($ventasProducto->detalle_iva);
                $totales = $totales + floatval($ventasProducto->total); 
                $totalLibras = $totalLibras+ $libras;
                $totalKilos = $totalKilos + $kilos;
                $totalTm = $totalTm + $tm;

                $ventaxProductoMatriz[$count]['Documento'] = $ventasProducto->factura_numero;
                $ventaxProductoMatriz[$count]['Codigo'] = $ventasProducto->producto_codigo;
                $ventaxProductoMatriz[$count]['Producto'] = $ventasProducto->producto_nombre;
                $ventaxProductoMatriz[$count]['Fecha'] = $ventasProducto->factura_fecha;
                $ventaxProductoMatriz[$count]['Cantidad'] = $ventasProducto->detalle_cantidad;
                $ventaxProductoMatriz[$count]['Pvp'] = floatval($ventasProducto->detalle_precio_unitario);
                $ventaxProductoMatriz[$count]['Iva'] = floatval($ventasProducto->detalle_iva);
                if($ventasProducto->detalle_iva){
                    $total12= $total12 + floatval($ventasProducto->subtotal);
                    $ventaxProductoMatriz[$count]['Subtotal12'] = floatval($ventasProducto->subtotal);
                    $ventaxProductoMatriz[$count]['Subtotal0'] = 0;
                }else{
                    $total0 = $total0 + floatval($ventasProducto->subtotal);
                    $ventaxProductoMatriz[$count]['Subtotal12'] = 0;
                    $ventaxProductoMatriz[$count]['Subtotal0'] = floatval($ventasProducto->subtotal);
                }
                $ventaxProductoMatriz[$count]['Total'] = floatval($ventasProducto->total);
                $ventaxProductoMatriz[$count]['libras'] = $libras;
                $ventaxProductoMatriz[$count]['kilos'] = $kilos;
                $ventaxProductoMatriz[$count]['tm'] = $tm;
                $ventaxProductoMatriz[$count]['Cliente'] = $ventasProducto->cliente_nombre;
                if(isset($ventasProducto->ordenDespacho->orden_numero)){
                    $ventaxProductoMatriz[$count]['Orden'] = $ventasProducto->ordenDespacho->orden_numero;
                }else{
                    $ventaxProductoMatriz[$count]['Orden'] ='';
                }                
                $ventaxProductoMatriz[$count]['Observacion'] = $ventasProducto->factura_comentario;
                $count = $count + 1;                
            }
            $fechaselect =  $request->get('idHasta');
            $fechaselect2 =  $request->get('idDesde');
            $productoC =  $request->get('productoID');
            $clienteC =  $request->get('clienteID');
            $bodegac =  $request->get('bodega_id');
            $fechaselect2 =  $request->get('idDesde');
            $productos = Producto::productos()->where('producto_compra_venta','=','3')->orwhere('producto_compra_venta','=','2')->get();
            $bodegas = Bodega::Bodegas()->get();
            return view('admin.inventario.reporteVentaxProducto.index',
            ['ventaxProductoMatriz'=>$ventaxProductoMatriz,            
            'fechaselect'=>$fechaselect,
            'clienteC'=>$clienteC,
            'productoC'=>$productoC,
            'bodegac'=>$bodegac,
            'productos'=>$productos,
            'bodegas'=>$bodegas,
            'clientes'=>Cliente::clientes()->get(),
            'fechaselect2'=>$fechaselect2,
            'totalCantidad'=>$totalCantidad,
            'totalIva'=>$totalIva,
            'totales'=>$totales,
            'total12'=>$total12,
            'total0'=>$total0,
            'totalLibras'=>$totalLibras,
            'totalKilos'=>$totalKilos,
            'totalTm'=>$totalTm,
            'gruposPermiso'=>$gruposPermiso,         
            'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('reporteVentaProductoC')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
}
