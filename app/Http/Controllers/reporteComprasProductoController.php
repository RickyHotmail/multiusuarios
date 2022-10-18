<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Centro_Consumo;
use App\Models\Detalle_TC;
use App\Models\Movimiento_Producto;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\Sustento_Tributario;
use App\Models\Transaccion_Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class reporteComprasProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.compras.modificarSustentos.index',['sustentos'=>Sustento_Tributario::Sustentos()->get(),'productos'=>Producto::ProductosCompraVenta()->get(),'CentroConsumos'=>Centro_Consumo::CentroConsumos()->get(),'gruposPermiso'=>$gruposPermiso, 'PE'=>Punto_Emision::puntos()->get(),'permisosAdmin'=>$permisosAdmin]);
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
        if (isset($_POST['buscarID'])){
            return $this->buscar($request);
        }
        if (isset($_POST['guardarID'])){
            return $this->guardar($request);
        }
    }
    public function buscar(Request $request)
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $detallesTC = Detalle_TC::DetalleFacturaxProducto($request->get('idProducto'), $request->get('idDesde'), $request->get('idHasta'))->get();
            $productos=Transaccion_Compra::MovimientoCConsumo($request->get('idCentroc'), $request->get('idDesde'), $request->get('idHasta'))->orderBy('transaccion_fecha', 'asc')->orderBy('transaccion_numero', 'asc')->get();
            return view('admin.compras.modificarSustentos.index',['centrosConsumo'=>Centro_Consumo::CentroConsumoxSustento($request->get('idSustento'))->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'productos'=>Producto::ProductosCompraVenta()->get(),'fecI'=>$request->get('idDesde'),'fecF'=>$request->get('idHasta'),'cc'=>$request->get('idProducto'),'st'=>$request->get('idSustento'),'detallesTC'=>$detallesTC,'gruposPermiso'=>$gruposPermiso, 'PE'=>Punto_Emision::puntos()->get(),'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request)
    {
        try{ 
            DB::beginTransaction();
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $idcc=$request->get('idcc');
            $idCentroc= $request->get('idCentroc');
            $contador=$request->get('contador');
            $general = new generalController();
            for ($i = 0; $i < count($contador); ++$i) {
                $transaccion=Transaccion_Compra::findOrFail($idcc[$contador[$i]]);
                $centro=Centro_Consumo::findOrFail($idCentroc[$contador[$i]]);
                foreach($transaccion->detalles as $detalle){
                    $aux=Detalle_TC::findOrFail($detalle->detalle_id);
                    $aux->centro_consumo_id=$idCentroc[$contador[$i]];
                    $aux->save();
                    $general->registrarAuditoria('Actualizacion de la transaccion compra '.$transaccion->transaccion_numero.' del detalle del centro de consumo con producto  -> '.$aux->producto->producto_nombre.' con centro de consumo '.$centro->centro_consumo_nombre,$transaccion->transaccion_numero,'');
                    $auxmov=Movimiento_Producto::findOrFail($aux->movimiento_id);
                    $auxmov->centro_consumo_id=$idCentroc[$contador[$i]];
                    $auxmov->save();
                    $general->registrarAuditoria('Actualizacion de la transaccion compra de Movimiento Producto '.$transaccion->transaccion_numero.' Con movimientos de Productos de id '.$auxmov->movimiento_id.' del detalle del centro de consumo con producto  -> '.$aux->producto->producto_nombre.' con centro de consumo '.$centro->centro_consumo_nombre,$transaccion->transaccion_numero,'');
                   
                }
                $transaccion->sustento_id= $centro->sustento->sustento_id;
                $transaccion->save();
                $general->registrarAuditoria('Actualizacion de la transaccion compra '.$transaccion->transaccion_numero.' con sustento -> '.$centro->sustento->sustento_nombre.' con centro de consumo '.$centro->centro_consumo_nombre,$transaccion->transaccion_numero,' cambio a centro de consumo con sustento '.$centro->sustento->sustento_id);        
            }
            DB::commit();
            return view('admin.compras.modificarSustentos.index',['cc'=>$request->get('idProducto'),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'productos'=>Producto::ProductosCompraVenta()->get(),'fecI'=>$request->get('idDesde'),'fecF'=>$request->get('idHasta'),'st'=>$request->get('idSustento'),'CentroConsumos'=>Centro_Consumo::CentroConsumos()->get(),'gruposPermiso'=>$gruposPermiso, 'PE'=>Punto_Emision::puntos()->get(),'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            DB::rollBack();
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function buscarBySustento($buscar){
        return Centro_Consumo::CentroConsumoxSustento($buscar)->get();
    }
}
