<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Caja;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Movimiento_Caja;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class cuadreCajaAbiertaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{ 
            $datos = null;
            $datosDiarios = null;
            $saldoActualmovimiento = 0;
            $saldoActualdiario = 0;
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $sucursales=Sucursal::sucursales()->get();
            $count = 1;
            $count2 = 1;
            //Tabla de movimientos de caja        
            $datos[$count]['Fecha'] = '';
            $datos[$count]['Descripcion'] = 'SALDO ANTERIOR';
            $datos[$count]['Valor'] = '';
            $datos[$count]['Saldo'] = 0;
            $datos[$count]['Diario'] = '';  
            $count = $count + 1;
            $datos[$count]['Fecha'] = '';
            $datos[$count]['Descripcion'] = 'NINGUNA CAJA SELECCIONADA!!';
            $datos[$count]['Valor'] = '';
            $datos[$count]['Saldo'] = '';
            $datos[$count]['Diario'] = '';  
            // fin
            //Tabla de movimientos contable de caja       
            $datosDiarios[$count2]['Fecha'] = '';
            $datosDiarios[$count2]['Descripcion'] = 'SALDO ANTERIOR';
            $datosDiarios[$count2]['Debe'] = '';
            $datosDiarios[$count2]['Haber'] = '';       
            $datosDiarios[$count2]['Saldo'] = 0;
            $datosDiarios[$count2]['Diario'] = '';  
            $count2 = $count2 + 1;
            $datosDiarios[$count2]['Fecha'] = '';
            $datosDiarios[$count2]['Descripcion'] = 'NINGUNA CAJA SELECCIONADA!!';
            $datosDiarios[$count2]['Debe'] = '';
            $datosDiarios[$count2]['Haber'] = '';       
            $datosDiarios[$count2]['Saldo'] = '';
            $datosDiarios[$count2]['Diario'] = '';  
            // fin
        
            return view('admin.caja.cuadreCajaAbierta.index',['sucursales'=>$sucursales,'saldoActualmovimiento'=>$saldoActualmovimiento, 'saldoActualdiario'=>$saldoActualdiario,'datosDiarios'=>$datosDiarios, 'datos'=>$datos, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
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
            $datos = null;
            $datosDiarios = null;
            $saldoActualmovimiento = 0;
            $saldoActualdiario = 0;
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $sucursales=Sucursal::sucursales()->get();        
            $cajaxsucursal = Caja::CajaSucursal($request->get('idsucursal'))->get();
            $cajaselect =  $request->get('idcaja');
            $sucursalselect =  $request->get('idsucursal');
                $cajaAbierta=Arqueo_Caja::cajasAbiertasxSucursal()     
                ->where('arqueo_caja.caja_id','=',$request->get('idcaja'))
                ->where('arqueo_estado','=','1')->first();            
                if($cajaAbierta){            
                    $cuentaCaja  = Caja::caja($cajaAbierta->caja_id)->first();      
                    $movimientosCaja = Movimiento_Caja::movimientoxCajaAbierta($cajaAbierta->arqueo_id)->get();            
                    if(count($movimientosCaja)>0){
                        $count = 1;
                        $count2 = 1;
                        //Tabla de movimientos de caja        
                        $datos[$count]['Fecha'] = '';
                        $datos[$count]['Descripcion'] = 'SALDO ANTERIOR';
                        $datos[$count]['Valor'] = '';
                        $datos[$count]['Saldo'] = $cajaAbierta->arqueo_saldo_inicial;
                        $datos[$count]['Diario'] = '';  
                        $count = $count + 1;
                        // fin
                        //Tabla de movimientos contable de caja       
                        $datosDiarios[$count2]['Fecha'] = '';
                        $datosDiarios[$count2]['Descripcion'] = 'SALDO ANTERIOR';
                        $datosDiarios[$count2]['Debe'] = '';
                        $datosDiarios[$count2]['Haber'] = '';       
                        $datosDiarios[$count2]['Saldo'] = $cajaAbierta->arqueo_saldo_inicial;
                        $datosDiarios[$count2]['Diario'] = '';  
                        $count2 = $count2 + 1;
                        // fin
                        foreach($movimientosCaja as $movimientoCaja){
                            $n = 1;
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                if(isset($movimientoCaja->diario->diario_codigo)){
                                    $diario = Diario::diarioCodigo($movimientoCaja->diario->diario_codigo)->first();            
                                    $DetalleDiarios = Detalle_Diario::detalleDiarioXdiarioYcuenta($movimientoCaja->diario_id,$cuentaCaja->cuenta_id)->get();
                                }else{
                                    $diario = null;            
                                    $DetalleDiarios = [];
                                }
                            }else{
                                $diario = null;
                                $DetalleDiarios = [];
                            }
                            //tabla movientos de caja           
                            $datos[$count]['Fecha'] = $movimientoCaja->movimiento_fecha;
                            $datos[$count]['Descripcion'] = $movimientoCaja->movimiento_descripcion;
                            if ($movimientoCaja->movimiento_tipo == 'SALIDA'){
                                $datos[$count]['Valor'] = '-'.''.$movimientoCaja->movimiento_valor;
                            }else{
                                $datos[$count]['Valor'] = $movimientoCaja->movimiento_valor;
                            } 
                            if ($movimientoCaja->movimiento_tipo == 'ENTRADA'){                    
                                $n = 1;                
                                $total = $datos[$count - 1]['Saldo'] + ($movimientoCaja->movimiento_valor * $n);
                                $datos[$count]['Saldo'] = $total;
                            }else{
                                $n = $n * -1;
                                $total = $datos[$count - 1]['Saldo'] + ($movimientoCaja->movimiento_valor * $n);
                                $datos[$count]['Saldo'] = $total;                  
                                
                            }
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                if(isset($movimientoCaja->diario->diario_codigo)){
                                    $datos[$count]['Diario'] = $movimientoCaja->diario->diario_codigo;
                                }else{
                                    $datos[$count]['Diario'] ='SIN DIARIO';
                                }
                            }
                            //fin de movimientos de caja
                            if(isset($movimientoCaja->diario->diario_codigo)){
                                foreach($DetalleDiarios as $DetalleDiario){                
                                    $datosDiarios[$count2]['Fecha'] = $diario->diario_fecha;
                                    $datosDiarios[$count2]['Descripcion'] = $diario->diario_comentario;
                                    $total2 = $datosDiarios[$count2 - 1]['Saldo'] + $DetalleDiario->detalle_debe - $DetalleDiario->detalle_haber;
                                    $datosDiarios[$count2]['Debe'] = $DetalleDiario->detalle_debe;
                                    $datosDiarios[$count2]['Haber'] = $DetalleDiario->detalle_haber;
                                    $datosDiarios[$count2]['Saldo'] = $total2;
                                    if(isset($movimientoCaja->diario->diario_codigo)){
                                        $datosDiarios[$count2]['Diario'] = $movimientoCaja->diario->diario_codigo;
                                    }else{
                                        $datosDiarios[$count2]['Diario'] = 'SIN DIARIO';
                                    }
                                    $count2 ++;               
                                } 
                            }else{
                                $datosDiarios[$count2]['Fecha'] = $movimientoCaja->movimiento_fecha;
                                $datosDiarios[$count2]['Descripcion'] = 'ESTE REGISTRO FUE ELIMINADO DE UNA CAJA CERRADA';
                                if ($movimientoCaja->movimiento_tipo == 'SALIDA'){
                                    $total2 = $datosDiarios[$count2 - 1]['Saldo'] + 0 - $movimientoCaja->movimiento_valor;
                                    $datosDiarios[$count2]['Haber'] = $movimientoCaja->movimiento_valor;
                                    $datosDiarios[$count2]['Debe'] = 0;
                                }else{
                                    $total2 = $datosDiarios[$count2 - 1]['Saldo'] + $movimientoCaja->movimiento_valor - 0;
                                    $datosDiarios[$count2]['Debe'] = $movimientoCaja->movimiento_valor;
                                    $datosDiarios[$count2]['Haber'] = 0;
                                }                                
                                
                                $datosDiarios[$count2]['Saldo'] = $total2;
                                $datosDiarios[$count2]['Diario'] = 'SIN DIARIO';
                                $count2 ++;
                            }                                        
                            $count ++;
                        }
                        $saldoActualmovimiento = $total;
                        if(Auth::user()->empresa->empresa_contabilidad == '1'){
                            $saldoActualdiario = $total2;
                        }
                    }else{
                        $count = 1;
                        $count2 = 1;
                        //Tabla de movimientos de caja        
                        $datos[$count]['Fecha'] = '';
                        $datos[$count]['Descripcion'] = 'SALDO ANTERIOR';
                        $datos[$count]['Valor'] = '';
                        $datos[$count]['Saldo'] = $cajaAbierta->arqueo_saldo_inicial;
                        $datos[$count]['Diario'] = '';  
                        $count = $count + 1;
                        $datos[$count]['Fecha'] = '';
                        $datos[$count]['Descripcion'] = 'CAJA NO TIENE MOVIMIENTOS !!';
                        $datos[$count]['Valor'] = '';
                        $datos[$count]['Saldo'] = '';
                        $datos[$count]['Diario'] = '';  
                        // fin
                        //Tabla de movimientos contable de caja       
                        $datosDiarios[$count2]['Fecha'] = '';
                        $datosDiarios[$count2]['Descripcion'] = 'SALDO ANTERIOR';
                        $datosDiarios[$count2]['Debe'] = '';
                        $datosDiarios[$count2]['Haber'] = '';       
                        $datosDiarios[$count2]['Saldo'] = $cajaAbierta->arqueo_saldo_inicial;
                        $datosDiarios[$count2]['Diario'] = '';  
                        $count2 = $count2 + 1;
                        $datosDiarios[$count2]['Fecha'] = '';
                        $datosDiarios[$count2]['Descripcion'] = 'CAJA NO TIENE MOVIMIENTOS !!';
                        $datosDiarios[$count2]['Debe'] = '';
                        $datosDiarios[$count2]['Haber'] = '';       
                        $datosDiarios[$count2]['Saldo'] = '';
                        $datosDiarios[$count2]['Diario'] = '';  
                        // fin
                    }
                    return view('admin.caja.cuadreCajaAbierta.index',['cajaxsucursal'=>$cajaxsucursal,'sucursalselect'=>$sucursalselect,'cajaselect'=>$cajaselect,'sucursales'=>$sucursales,'saldoActualmovimiento'=>$saldoActualmovimiento, 'saldoActualdiario'=>$saldoActualdiario,'datosDiarios'=>$datosDiarios, 'datos'=>$datos,'cajaAbierta'=>$cajaAbierta, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                
                }else{
                    $count = 1;
                    $count2 = 1;
                    //Tabla de movimientos de caja        
                    $datos[$count]['Fecha'] = '';
                    $datos[$count]['Descripcion'] = 'SALDO ANTERIOR';
                    $datos[$count]['Valor'] = '';
                    $datos[$count]['Saldo'] = 0;
                    $datos[$count]['Diario'] = '';  
                    $count = $count + 1;
                    $datos[$count]['Fecha'] = '';
                    $datos[$count]['Descripcion'] = 'NO SE ENCONTRARON RESULTADOS !!';
                    $datos[$count]['Valor'] = '';
                    $datos[$count]['Saldo'] = '';
                    $datos[$count]['Diario'] = '';  
                    // fin
                    //Tabla de movimientos contable de caja       
                    $datosDiarios[$count2]['Fecha'] = '';
                    $datosDiarios[$count2]['Descripcion'] = 'SALDO ANTERIOR';
                    $datosDiarios[$count2]['Debe'] = '';
                    $datosDiarios[$count2]['Haber'] = '';       
                    $datosDiarios[$count2]['Saldo'] = 0;
                    $datosDiarios[$count2]['Diario'] = '';  
                    $count2 = $count2 + 1;
                    $datosDiarios[$count2]['Fecha'] = '';
                    $datosDiarios[$count2]['Descripcion'] = 'NO SE ENCONTRARON RESULTADOS !!';
                    $datosDiarios[$count2]['Debe'] = '';
                    $datosDiarios[$count2]['Haber'] = '';       
                    $datosDiarios[$count2]['Saldo'] = '';
                    $datosDiarios[$count2]['Diario'] = '';  
                    // fin
                    return view('admin.caja.cuadreCajaAbierta.index',['cajaxsucursal'=>$cajaxsucursal,'sucursalselect'=>$sucursalselect, 'cajaselect'=>$cajaselect,'sucursales'=>$sucursales,'saldoActualmovimiento'=>$saldoActualmovimiento, 'saldoActualdiario'=>$saldoActualdiario,'datosDiarios'=>$datosDiarios, 'datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                }
            }catch(\Exception $ex){
                return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
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
        return redirect('/denegado');
    }
    public function buscarrol($buscar){        
        return Caja::cajaSucursal($buscar)->get();
    }
    public function buscarCajaBySucursal($buscar){        
        return Caja::cajaSucursal($buscar)->get();
    }
}
