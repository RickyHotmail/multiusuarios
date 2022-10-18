<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Caja;
use App\Models\Caja_Usuario;
use App\Models\Detalle_Diario;
use App\Models\Detalle_FV;
use App\Models\Diario;
use App\Models\Egreso_Caja;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Faltante_Caja;
use App\Models\Ingreso_Caja;
use App\Models\Movimiento_Caja;
use App\Models\Pago_CXC;
use App\Models\Pago_CXP;
use App\Models\Punto_Emision;
use App\Models\Sobrante_Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

use function PHPUnit\Framework\isEmpty;

class cierreCajaController extends Controller
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
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $cajas = Caja::cajas()->get();
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
                    $saldoActualmovimiento = $cajaAbierta->arqueo_saldo_inicial;
                    if(Auth::user()->empresa->empresa_contabilidad == '1'){
                        $saldoActualdiario = $cajaAbierta->arqueo_saldo_inicial;
                    }
                }
                return view('admin.caja.cierreCaja.index',['saldoActualmovimiento'=>$saldoActualmovimiento, 'saldoActualdiario'=>$saldoActualdiario,'datosDiarios'=>$datosDiarios, 'datos'=>$datos,'cajas'=>$cajas, 'cajaAbierta'=>$cajaAbierta, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            
            }else{
                return redirect('inicio')->with('error','No tiene una caja Aperturada, Abra una caja y  vuelva a intentar');            
            } 
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
            $cajaAbierta=Arqueo_Caja::ArqueoCajaxuser(Auth::user()->user_id)->first();
            $general = new generalController();
            DB::beginTransaction();
            $arqueoCaja = new Arqueo_Caja();
            
            $arqueoCaja->arqueo_fecha=date("Y")."-".date("m")."-".date("d");
            $cierre = $general->cierre($arqueoCaja->arqueo_fecha,$cajaAbierta->caja->sucursal_id);          
            if($cierre){
                return redirect('arqueoCaja')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }  
            $arqueoCaja->arqueo_hora=date("H:i:s");
            $arqueoCaja->arqueo_observacion= $request->get('idMensaje');
            $arqueoCaja->arqueo_tipo="CIERRE";
            $arqueoCaja->arqueo_saldo_inicial= $cajaAbierta->arqueo_saldo_inicial;
            $arqueoCaja->arqueo_monto= $request->get('idMonto');                        
            $arqueoCaja->arqueo_billete1= $request->get('billete1');
            $arqueoCaja->arqueo_billete5= $request->get('billete5');
            $arqueoCaja->arqueo_billete10= $request->get('billete10');
            $arqueoCaja->arqueo_billete20= $request->get('billete20');
            $arqueoCaja->arqueo_billete50= $request->get('billete50');
            $arqueoCaja->arqueo_billete100= $request->get('billete100');
            $arqueoCaja->arqueo_moneda01= $request->get('moneda01');
            $arqueoCaja->arqueo_moneda05= $request->get('moneda05');
            $arqueoCaja->arqueo_moneda10= $request->get('moneda10');
            $arqueoCaja->arqueo_moneda25= $request->get('moneda25');
            $arqueoCaja->arqueo_moneda50= $request->get('moneda50');
            $arqueoCaja->arqueo_moneda1= $request->get('moneda1');
            $arqueoCaja->arqueo_estado='1';
            $arqueoCaja->empresa_id = Auth::user()->empresa_id;
            $arqueoCaja->caja_id= $cajaAbierta->caja_id;
            $arqueoCaja->user_id=Auth::user()->user_id;                    
            $arqueoCaja->save();
            $cajaAbierta->cierre()->associate($arqueoCaja);
            $cajaAbierta->update();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Cierre de Caja -> '.$arqueoCaja->caja->caja_nombre.' Con el Valor de: '.$request->get('idMonto'),'0', 'Por el usuario: '. $arqueoCaja->usuario->user_nombre);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('arqueoCaja')->with('success','Datos guardados exitosamente');                
                       
        }catch(\Exception $ex){
            DB::rollBack();
           return redirect('arqueoCaja')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function cierreCajaImprime($id){
        $MatrizVentasEfectivo = null;
        $MatrizVentasCredito = null;
        $MatrizVentasContado = null;
        $MatrizEgresoCaja = null;
        $MatrizIngresoCaja = null;
        $MatrizFaltanteCaja = null;
        $MatrizSobranteCaja = null;
        $MatrizResumenArqueo = null;
        $MatrizCuentaCobrar = null;
        $MatrizCuentaPagar = null;


        $arqueoCaja = Arqueo_Caja::cierrecaja($id)->first();
        $arqueocierre = Arqueo_Caja::ArqueoCierre($arqueoCaja->arqueo_id)->first();         
        $empresa = Empresa::empresa()->first();
        //reportes 
        $facturasVentaEfectivo =Factura_Venta::FacturaIdArqueo($arqueocierre->arqueo_id)->get();
        $facturasVentaContado =Factura_Venta::FacturaContadoIdArqueo($arqueocierre->arqueo_id)->get();
        $facturasVentaCredito =Factura_Venta::FacturaCreditoIdArqueo($arqueocierre->arqueo_id)->get();
        $pagosCXC = Pago_CXC::PagoArqueoID($arqueocierre->arqueo_id)->where('pago_tipo','=','EFECTIVO')->get();
        $pagosCXP = Pago_CXP::PagoArqueoID($arqueocierre->arqueo_id)->get();

        $sumatoriaCxC = Pago_CXC::PagoArqueoID($arqueocierre->arqueo_id)->where('pago_tipo','=','EFECTIVO')->sum('pago_valor');
        $sumatoriaCxP = Pago_CXP::PagoArqueoID($arqueocierre->arqueo_id)->sum('pago_valor');


        //sumatorias de totales
        $egresosCaja = Egreso_Caja::EgresoCajaIdArqueo($arqueocierre->arqueo_id)->get();
        $ingresosCaja = Ingreso_Caja::IngresoCajaxArqueo($arqueocierre->arqueo_id)->get();
        $faltantesCaja = Faltante_Caja::FaltantexArqueo($arqueocierre->arqueo_id)->get();
        $sobrantesCaja = Sobrante_Caja::SobrantexArqueo($arqueocierre->arqueo_id)->get();        
        $sumatoriaVentaEfectivo = Factura_Venta::FacturaSumaEfectivo($arqueocierre->arqueo_id)->first();
        $sumatoriaVentaCredito = Factura_Venta::FacturaSumaCredito($arqueocierre->arqueo_id)->first();    
        $sumatoriaVentaContado = Factura_Venta::FacturaSumaContado($arqueocierre->arqueo_id)->first();

        
        if(isset($sumatoriaVentaEfectivo->sumaefectivo)){
            $sumarEfectivo = $sumatoriaVentaEfectivo->sumaefectivo; 
        }else{                          
            $sumarEfectivo = 0;
        }
        if(isset($sumatoriaVentaCredito->sumacredito)){
            $sumarCredito = $sumatoriaVentaCredito->sumacredito; 
        }else{                          
            $sumarCredito = 0;
        }
        if(isset($sumatoriaVentaContado->sumacontado)){
            $sumarContado = $sumatoriaVentaContado->sumacontado; 
        }else{                          
            $sumarContado = 0;
        }
        $sumatoriaEgresoCaja = Egreso_Caja::EgresoCajaIdArqueoSuma($arqueocierre->arqueo_id)->first();        
        if(isset($sumatoriaEgresoCaja->sumaegreso)){
            $sumarEgreso = $sumatoriaEgresoCaja->sumaegreso; 
        }else{                          
            $sumarEgreso = 0;
        }
        $sumatoriaIngresoCaja = Ingreso_Caja::IngresoCajaxArqueoSuma($arqueocierre->arqueo_id)->first();
        if(isset($sumatoriaIngresoCaja->sumaingreso)){ 
            $sumarIngreso = $sumatoriaIngresoCaja->sumaingreso; 
        }else{
            $sumarIngreso = 0;                          
        }
        $sumatoriaFalatanteCaja = Faltante_Caja::FaltantexArqueoSuma($arqueocierre->arqueo_id)->first();
        if(isset($sumatoriaFalatanteCaja->sumafaltante)){           
            $sumarFaltante = $sumatoriaFalatanteCaja->sumafaltante; 
        }else{
            $sumarFaltante = 0;                        
        }
        $sumatoriaSobranteCaja = Sobrante_Caja::SobrantexArqueoSuma($arqueocierre->arqueo_id)->first();        
        if(isset($sumatoriaSobranteCaja->sumasobrante)){
            $sumarSobrante = $sumatoriaSobranteCaja->sumasobrante; 
        }else{
           $sumarSobrante = 0;                                                      
        }
        if(isset($sumatoriaCxC)){
            $sumarCXC = $sumatoriaCxC;
        }else{                          
            $sumarCXC = 0;
        }
        if(isset($sumatoriaCxP)){
            $sumarCXP = $sumatoriaCxP;
        }else{                          
            $sumarCXP = 0;
        }

        $countaux = 1;           
        foreach($facturasVentaEfectivo as $facturaVentaEfectivo){ 
            $MatrizVentasEfectivo[$countaux]['fecha'] = $facturaVentaEfectivo->factura_fecha;
            $MatrizVentasEfectivo[$countaux]['numero'] = $facturaVentaEfectivo->factura_numero;
            $MatrizVentasEfectivo[$countaux]['nombre'] = $facturaVentaEfectivo->cliente->cliente_nombre;
            $MatrizVentasEfectivo[$countaux]['cantidad'] = Detalle_FV::DetalleFacturaSuma($facturaVentaEfectivo->factura_id)->sum('detalle_cantidad');
            $MatrizVentasEfectivo[$countaux]['valor'] = number_format($facturaVentaEfectivo->factura_total, 2);
            $countaux = $countaux +1;
        }
        $countaux1 = 1;           
        foreach($facturasVentaContado as $facturaVentaContado){ 
            $MatrizVentasContado[$countaux1]['fecha'] = $facturaVentaContado->factura_fecha;
            $MatrizVentasContado[$countaux1]['numero'] = $facturaVentaContado->factura_numero;
            $MatrizVentasContado[$countaux1]['nombre'] = $facturaVentaContado->cliente->cliente_nombre;
            $MatrizVentasContado[$countaux1]['cantidad'] = Detalle_FV::DetalleFacturaSuma($facturaVentaContado->factura_id)->sum('detalle_cantidad');
            $MatrizVentasContado[$countaux1]['valor'] = number_format($facturaVentaContado->factura_total, 2);
            $countaux1 = $countaux1 +1;
        }
        $countaux2 = 1;           
        foreach($facturasVentaCredito as $facturaVentaCredito){ 
            $MatrizVentasCredito[$countaux2]['fecha'] = $facturaVentaCredito->factura_fecha;
            $MatrizVentasCredito[$countaux2]['numero'] = $facturaVentaCredito->factura_numero;
            $MatrizVentasCredito[$countaux2]['nombre'] = $facturaVentaCredito->cliente->cliente_nombre;
            $MatrizVentasCredito[$countaux2]['cantidad'] = Detalle_FV::DetalleFacturaSuma($facturaVentaCredito->factura_id)->sum('detalle_cantidad');; 
            $MatrizVentasCredito[$countaux2]['valor'] = number_format($facturaVentaCredito->factura_total, 2);
            $countaux2 = $countaux2 +1;
        }

        $count = 1;           
        foreach($egresosCaja as $egresoCaja){ 
            $MatrizEgresoCaja[$count]['fecha'] = $egresoCaja->egreso_fecha;
            $MatrizEgresoCaja[$count]['descripcion'] = $egresoCaja->egreso_descripcion;
            $MatrizEgresoCaja[$count]['diario'] = $egresoCaja->diario->diario_codigo;
            $MatrizEgresoCaja[$count]['valor'] = number_format($egresoCaja->egreso_valor, 2);
            $count = $count +1;
        }
        $count2 = 1;           
        foreach($ingresosCaja as $ingresoCaja){ 
            $MatrizIngresoCaja[$count2]['fecha'] = $ingresoCaja->ingreso_fecha;
            $MatrizIngresoCaja[$count2]['descripcion'] = $ingresoCaja->ingreso_descripcion;
            $MatrizIngresoCaja[$count2]['diario'] = $ingresoCaja->diario->diario_codigo;
            $MatrizIngresoCaja[$count2]['valor'] = number_format($ingresoCaja->ingreso_valor, 2);
            $count2 = $count2 +1;
        }
        $count3 = 1;           
        foreach($faltantesCaja as $faltanteCaja){ 
            $MatrizFaltanteCaja[$count3]['fecha'] = $faltanteCaja->faltante_fecha;
            $MatrizFaltanteCaja[$count3]['descripcion'] = $faltanteCaja->faltante_observacion;
            $MatrizFaltanteCaja[$count3]['diario'] = $faltanteCaja->diario->diario_codigo;
            $MatrizFaltanteCaja[$count3]['valor'] = number_format($faltanteCaja->faltante_monto, 2);
            $count3 = $count3 +1;
        }
        $count4 = 1;           
        foreach($sobrantesCaja as $sobranteCaja){ 
            $MatrizSobranteCaja[$count4]['fecha'] = $sobranteCaja->sobrante_fecha;
            $MatrizSobranteCaja[$count4]['descripcion'] = $sobranteCaja->sobrante_observacion;
            $MatrizSobranteCaja[$count4]['diario'] = $sobranteCaja->diario->diario_codigo;
            $MatrizSobranteCaja[$count4]['valor'] = number_format($sobranteCaja->sobrante_monto, 2);
            $count4 = $count4 +1;
        }
        $countaux5 = 1;           
        foreach($pagosCXC as $pagoCXC){ 
            $MatrizCuentaCobrar[$countaux5]['fecha'] = $pagoCXC->pago_fecha;
            $MatrizCuentaCobrar[$countaux5]['descripcion'] = $pagoCXC->pago_descripcion;
            $MatrizCuentaCobrar[$countaux5]['diario'] = $pagoCXC->diario->diario_codigo;
            $MatrizCuentaCobrar[$countaux5]['valor'] = number_format($pagoCXC->pago_valor, 2);           
            $countaux5 = $countaux5 +1;
        }
        $countaux6 = 1;           
        foreach($pagosCXP as $pagoCXP){ 
            $MatrizCuentaPagar[$countaux6]['fecha'] = $pagoCXP->pago_fecha;
            $MatrizCuentaPagar[$countaux6]['descripcion'] = $pagoCXP->pago_descripcion;
            $MatrizCuentaPagar[$countaux6]['diario'] = $pagoCXP->diario->diario_codigo;
            $MatrizCuentaPagar[$countaux6]['valor'] = number_format($pagoCXP->pago_valor, 2);           
            $countaux6 = $countaux6 +1;
        }

        $saldoConciliacion = floatval($arqueoCaja->arqueo_saldo_inicial) + floatval($sumarEfectivo) + floatval($sumarIngreso) + floatval($sumarSobrante) + floatval($sumarCXC) - floatval($sumarEgreso) - floatval($sumarFaltante) - floatval($sumarCXP);

        $MatrizResumenArqueo[1]['saldoInicial'] = number_format($arqueoCaja->arqueo_saldo_inicial, 2);
        $MatrizResumenArqueo[1]['VentasEfectivosum'] = number_format($sumarEfectivo, 2);
        $MatrizResumenArqueo[1]['CobrosCliente'] = number_format($sumarCXC, 2);
        $MatrizResumenArqueo[1]['PagosProveedor'] = number_format($sumarCXP, 2);
        $MatrizResumenArqueo[1]['Egresos'] = number_format($sumarEgreso, 2);
        $MatrizResumenArqueo[1]['Ingresos'] = number_format($sumarIngreso, 2);
        $MatrizResumenArqueo[1]['Faltantes'] = number_format($sumarFaltante, 2);
        $MatrizResumenArqueo[1]['Sobrantes'] = number_format($sumarSobrante, 2);
        $MatrizResumenArqueo[1]['saldoConciliar'] = number_format($saldoConciliacion, 2);
        $MatrizResumenArqueo[1]['conteoEfectivo'] = number_format($arqueoCaja->arqueo_monto, 2);
        
        $ruta = public_path().'/cierresCajasPDF/'.$empresa->empresa_ruc;
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = 'CC-'.$arqueoCaja->arqueo_fecha.'-'.$arqueoCaja->arqueo_id.'.pdf';
        $view =  \View::make('admin.formatosPDF.cierreCajaPdf', ['sumarCXC'=>$sumarCXC,'sumarCXP'=>$sumarCXP, 'arqueoCaja'=>$arqueoCaja,'empresa'=>$empresa,'MatrizCuentaPagar'=>$MatrizCuentaPagar, 'MatrizCuentaCobrar'=>$MatrizCuentaCobrar,'MatrizVentasContado'=>$MatrizVentasContado,'MatrizVentasCredito'=>$MatrizVentasCredito,'MatrizVentasEfectivo'=>$MatrizVentasEfectivo,'MatrizEgresoCaja'=>$MatrizEgresoCaja, 'MatrizIngresoCaja'=>$MatrizIngresoCaja,'MatrizFaltanteCaja'=>$MatrizFaltanteCaja, 'MatrizSobranteCaja'=>$MatrizSobranteCaja, 'MatrizResumenArqueo'=>$MatrizResumenArqueo, 'sumarCredito'=>$sumarCredito, 'sumarContado'=>$sumarContado, 'sumarEfectivo'=>$sumarEfectivo,'sumarEgreso'=>$sumarEgreso, 'sumarIngreso'=>$sumarIngreso, 'sumarFaltante'=>$sumarFaltante, 'sumarSobrante'=>$sumarSobrante]);
        //PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('cierreCaja.pdf');
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
}
