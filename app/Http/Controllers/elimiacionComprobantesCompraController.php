<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Factura_Venta;
use App\Models\Guia_Remision;
use App\Models\Liquidacion_Compra;
use App\Models\Nota_Credito;
use App\Models\Nota_Debito;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class elimiacionComprobantesCompraController extends Controller
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
            $sucursal=Sucursal::SucursalesDistinc()->select('sucursal_nombre')->distinct()->get();
            return view('admin.sri.eliminacionComprabantes.index',[ 'sucursal'=>$sucursal,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
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
        try{    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $sucursal=Sucursal::SucursalesDistinc()->select('sucursal_nombre')->distinct()->get();
            if ($request->get('documento')=='Facturas' && $request->get('sucursal') == "--TODOS--") {
                $facturas=Factura_Venta::FacturasFiltrar($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'))->get();
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'facturas'=>$facturas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            if ($request->get('documento')=='Facturas' && $request->get('sucursal') != "--TODOS--") {
                $facturas=Factura_Venta::FacturasFiltrarsucursal($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'),$request->get('sucursal'))->get();
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'facturas'=>$facturas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }

            if ($request->get('documento')=='Guia Remsion' && $request->get('sucursal') == "--TODOS--") {
                $guias=Guia_Remision::GuiaFiltrar($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'))->get();
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'guias'=>$guias, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            if ($request->get('documento')=='Guia Remsion' && $request->get('sucursal') != "--TODOS--") {
                $guias=Guia_Remision::GuiasucursalFiltrar($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'),$request->get('sucursal'))->get();
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'guias'=>$guias, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }

            if ($request->get('documento')=='Nota de Debito' && $request->get('sucursal') == "--TODOS--") {
                $debito=Nota_Debito::NotasDebitoFechaFiltrar($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'))->get();
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'debito'=>$debito, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            if ($request->get('documento')=='Nota de Debito' && $request->get('sucursal') != "--TODOS--") {
                $debito=Nota_Debito::NotasDebitobuscar($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'),$request->get('sucursal'))->get();
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'debito'=>$debito, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }

            if ($request->get('documento')=='Liquidacion Compra' && $request->get('sucursal') == "--TODOS--") {
                $liquidacion=Liquidacion_Compra::LiquidacionCompraFecha($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'))->get();
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'liquidacion'=>$liquidacion, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            if ($request->get('documento')=='Liquidacion Compra' && $request->get('sucursal') != "--TODOS--") {
                $liquidacion=Liquidacion_Compra::LiquidacionCompraBuscar($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'),$request->get('sucursal'))->get();
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'liquidacion'=>$liquidacion, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }

            if($request->get('documento')=='Nota de Credito' && $request->get('sucursal') == "--TODOS--"){
                $datos=null;
                $count=1;
                $credito=Nota_Credito::NotasCreditoFechaFiltrar($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'))->get();
                foreach($credito as $creditos){
                    $datos[$count]["eliminar"]=0;
                
                    if(!isset($creditos->documentoAnulado)){
                        if(isset($creditos->diario->anticipo)){
                            if ($creditos->diario->anticipo->anticipo_valor==$creditos->diario->anticipo->anticipo_saldo) {
                                $datos[$count]["eliminar"]=1;
                            
                            }
                        } 
                        else{
                            $datos[$count]["eliminar"]=1;
                        
                        }
                        
                    }
                    $datos[$count]["nc_id"]=$creditos->nc_id;
                    $datos[$count]["nc_numero"]=$creditos->nc_numero;
                    $datos[$count]["nc_fecha"]=$creditos->nc_fecha;
                    $datos[$count]["factura_numero"]=$creditos->factura->factura_numero;
                    $datos[$count]["cliente_nombre"]=$creditos->factura->cliente->cliente_nombre;
                    $datos[$count]["nc_subtotal"]=$creditos->nc_subtotal;
                    $datos[$count]["nc_tarifa0"]=$creditos->nc_tarifa0;

                    $datos[$count]["nc_tarifa12"]=$creditos->nc_tarifa12;
                    $datos[$count]["nc_descuento"]=$creditos->nc_descuento;
                    $datos[$count]["nc_porcentaje_iva"]=$creditos->nc_porcentaje_iva;
                    $datos[$count]["nc_total"]=$creditos->nc_total;
                    $count++;
                }
                
                $credito=$datos;
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'credito'=>$credito, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);

            }
            if($request->get('documento')=='Nota de Credito' && $request->get('sucursal') != "--TODOS--"){
                $datos=null;
                $count=1;
                $credito=Nota_Credito::NotasCreditobuscar($request->get('fecha_desde'), $request->get('fecha_hasta'), $request->get('descripcion'),$request->get('sucursal'))->get();
                foreach($credito as $creditos){
                    $datos[$count]["eliminar"]=0;
                
                    if(!isset($creditos->documentoAnulado)){
                        if(isset($creditos->diario->anticipo)){
                            if ($creditos->diario->anticipo->anticipo_valor==$creditos->diario->anticipo->anticipo_saldo) {
                                $datos[$count]["eliminar"]=1;
                            
                            }
                        } 
                        else{
                            $datos[$count]["eliminar"]=1;
                        
                        }
                        
                    }
                    $datos[$count]["nc_id"]=$creditos->nc_id;
                    $datos[$count]["nc_numero"]=$creditos->nc_numero;
                    $datos[$count]["nc_fecha"]=$creditos->nc_fecha;
                    $datos[$count]["factura_numero"]=$creditos->factura->factura_numero;
                    $datos[$count]["cliente_nombre"]=$creditos->factura->cliente->cliente_nombre;
                    $datos[$count]["nc_subtotal"]=$creditos->nc_subtotal;
                    $datos[$count]["nc_tarifa0"]=$creditos->nc_tarifa0;

                    $datos[$count]["nc_tarifa12"]=$creditos->nc_tarifa12;
                    $datos[$count]["nc_descuento"]=$creditos->nc_descuento;
                    $datos[$count]["nc_porcentaje_iva"]=$creditos->nc_porcentaje_iva;
                    $datos[$count]["nc_total"]=$creditos->nc_total;
                    $count++;
                }
                
                $credito=$datos;
                return view('admin.sri.eliminacionComprabantes.index', ['sucursal'=>$sucursal,'idsucursal'=>$request->get('sucursal'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=> $request->get('fecha_hasta'),'descripcion'=> $request->get('descripcion'),'documento'=> $request->get('documento'),'credito'=>$credito, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);

            }
            return redirect('eliminacionComprantes')->with('error','Los registros no se encontraron.');
        }catch(\Exception $ex){
            return redirect('eliminacionComprantes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
