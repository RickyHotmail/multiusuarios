<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anticipo_Cliente;
use App\Models\Arqueo_Caja;
use App\Models\Banco;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Cuenta_Bancaria;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Movimiento_Caja;
use App\Models\Parametrizacion_Contable;
use App\Models\sucursal;
use App\Models\Tipo_Movimiento_Banco;
use App\Models\Tipo_Movimiento_Caja;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class descuentoManualAnticipoClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $clientes = Cliente::clientes()->get();
        $sucursales = Sucursal::Sucursales()->get();
        $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();                 
        $cajas = Caja::cajas()->get();
        return view('admin.cuentasCobrar.descuentoManual.index',
        ['clientes'=>$clientes,
        'cajasxusuario'=>$cajasxusuario,
        'cajas'=>$cajas,
        'bancos'=>Banco::bancos()->get(),
        'sucursales'=>$sucursales, 
        'movimientos'=>Tipo_Movimiento_Caja::TipoMovimientos()->get(),
        'movimientosBanco'=>Tipo_Movimiento_Banco::TipoMovimientos()->get(), 
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
        if (isset($_POST['buscarReporte'])){
            return $this->buscar($request);
        }
        if (isset($_POST['cruzarAnticipos'])){
            return $this->cruzar($request);
        }
        
    }
    private function buscar(Request $request){
        try{
           
            $anticiposClientesMatriz = [];                
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $anticipoClientes=Anticipo_Cliente::AnticipoClienteByFechaSucursal($request->get('clienteID'),$request->get('sucursalID'),$request->get('idDesde'),$request->get('idHasta'))->get();   
            $count = 1;            
            foreach($anticipoClientes as $anticipoCliente){  
                $anticiposClientesMatriz[$count]['ID'] = $anticipoCliente->anticipo_id;
                $anticiposClientesMatriz[$count]['Fecha'] = $anticipoCliente->anticipo_fecha;
                $anticiposClientesMatriz[$count]['Valor'] = $anticipoCliente->anticipo_valor;
                $anticiposClientesMatriz[$count]['Saldo'] = $anticipoCliente->anticipo_saldo;
                $anticiposClientesMatriz[$count]['Tipo'] = $anticipoCliente->anticipo_motivo;
                $anticiposClientesMatriz[$count]['Motivo'] = $anticipoCliente->anticipo_motivo;
                $anticiposClientesMatriz[$count]['Cliente'] = $anticipoCliente->cliente->cliente_nombre;
                if(isset($anticipoCliente->diario->diario_codigo)){
                    $anticiposClientesMatriz[$count]['Diario'] = $anticipoCliente->diario->diario_codigo;
                }else{
                    $anticiposClientesMatriz[$count]['Diario'] = '';
                }
                
                $count = $count + 1;
            }
            $fechaselect =  $request->get('idHasta');
            $fechaselect2 =  $request->get('idDesde');
            $clienteS =  $request->get('clienteID'); 
            $sucursalS =  $request->get('sucursalID');
            $clientes = Cliente::clientes()->get();
            $sucursales = Sucursal::Sucursales()->get();
            $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();                 
            $cajas = Caja::cajas()->get();
            return view('admin.cuentasCobrar.descuentoManual.index',
            ['anticiposClientesMatriz'=>$anticiposClientesMatriz,            
            'fechaselect'=>$fechaselect, 
            'fechaselect2'=>$fechaselect2, 
            'cajasxusuario'=>$cajasxusuario,
            'cajas'=>$cajas,
            'bancos'=>Banco::bancos()->get(),         
            'sucursalS'=>$sucursalS,
            'clienteS'=>$clienteS,           
            'sucursales'=>$sucursales,
            'movimientos'=>Tipo_Movimiento_Caja::tipoMovimientos()->where('sucursal_id','=',$request->get('sucursalID'))->get(),
            'movimientosBanco'=>Tipo_Movimiento_Banco::TipoMovimientos()->where('sucursal_id','=',$request->get('sucursalID'))->get(),
            'clientes'=>$clientes,
            'gruposPermiso'=>$gruposPermiso,         
            'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('descuentoManualClientes')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    private function cruzar(Request $request){
        try{
            DB::beginTransaction();
            $general = new generalController();
           
            if($request->get('sucursalID') == '0'){
                $sucursales = Sucursal::Sucursales()->get();
            }else{
                $sucursales = Sucursal::Sucursal($request->get('sucursalID'))->get();
            }            
            $cliente = Cliente::cliente($request->get('clienteID'))->first();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $cuentaBancaria = Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_id'))->first();
            $diarios = [];
            $valPago = $request->get('Ddescontar');
            $datosSuc = null;
            $filS=0;
            $numerosAnticipos = '';
            $contDiarios = 0;
            foreach($sucursales as $sucursal){
                $cierre = $general->cierre($request->get('idFechaCruze'),$sucursal->sucursal_id);         
                if($cierre){
                    return redirect('descuentoManualClientes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
                $datosSuc[$filS]['sucursal_id'] = $sucursal->sucursal_id;
                $datosAnticipos = null;
                $fil = 0;
                $sumaSeleciion = 0;
                for ($i = 0; $i < count($valPago); ++$i){
                    if($request->get('check'.$i)){
                        if($valPago[$i] > 0){
                            $anticipo = Anticipo_Cliente::Anticipo($request->get('check'.$i))->first();
                            $numerosAnticipos = $numerosAnticipos.' - '.$anticipo->anticipo_numero;
                            if($anticipo->rangoDocumento->puntoEmision->sucursal_id == $sucursal->sucursal_id){
                                $datosAnticipos[$fil]['anticipo_id'] = $anticipo->anticipo_id;
                                $datosAnticipos[$fil]['descontar'] = $valPago[$i];
                                $sumaSeleciion = $sumaSeleciion + $valPago[$i];
                                $fil ++ ;
                            }
                        }
                    }
                }
                $datosSuc[$filS]['anticipos'] = $datosAnticipos;
                $datosSuc[$filS]['valorSeleccion'] = $sumaSeleciion;
                $filS ++;
            }
            if($request->get('flexRadioDefault') == 'BANCO'){
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion = 'CRUCE DE ANTICIPOS DE CLIENTES CON BANCO';
                $transferencia->transferencia_beneficiario = $cliente->cliente_nombre;
                $transferencia->transferencia_fecha = $request->get('idFechaCruze');
                $transferencia->transferencia_valor = $request->get('idValorSeleccionado');
                $transferencia->cuenta_bancaria_id = $cuentaBancaria->cuenta_bancaria_id;
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de transferencia por cruce de anticipos de clientes con banco','0','Registro de transferencia por cruce de anticipos de clientes con banco : '.$numerosAnticipos);
            }
            if(is_null($datosSuc) == false){
                for($i=0;$i < count($datosSuc);$i++){
                    if(is_null($datosSuc[$i]['anticipos']) == false){
                        if(count($datosSuc[$i]['anticipos'])>0){
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $diario = new Diario();
                                $diario->diario_fecha = $request->get('idFechaCruze');
                                $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFechaCruze'),'CDAC');
                                $diario->diario_referencia = 'COMPROBANTE DE DESCUENTO ANTICIPO DE CLIENTE';
                                $diario->diario_tipo_documento = 'DESCUENTO DE ANTICIPO';
                                $diario->diario_tipo = 'CDAC';
                                $diario->diario_comentario = 'COMPROBANTE DE DESCUENTO ANTICIPO DE CLIENTE : CRUCE DE ANTICIPOS DE CLIENTES CON '.$request->get('flexRadioDefault');
                                $diario->diario_numero_documento = '0';
                                $diario->diario_beneficiario = $cliente->cliente_nombre;
                                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFechaCruze'))->format('m');
                                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFechaCruze'))->format('Y');
                                $diario->diario_cierre = '0';
                                $diario->diario_estado = '1';
                                $diario->empresa_id = Auth::user()->empresa_id;
                                $diario->sucursal_id = $datosSuc[$i]['sucursal_id'];
                                $diario->save();
                                $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
                                $diarios[$contDiarios] = $diario;
                                $contDiarios ++;
                            }
                            if($request->get('flexRadioDefault') == 'CAJA'){
                                /**********************movimiento caja****************************/
                                $movimientoCaja = new Movimiento_Caja();          
                                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                $movimientoCaja->movimiento_hora=date("H:i:s");
                                $movimientoCaja->movimiento_tipo="SALIDA";
                                $movimientoCaja->movimiento_descripcion= 'P/R CRUCE DE ANTICIPOS DE CLIENTES CON CAJA';
                                $movimientoCaja->movimiento_valor= $datosSuc[$i]['valorSeleccion'];
                                $movimientoCaja->movimiento_documento="CRUCE DE ANTICIPOS DE CLIENTES CON CAJA";
                                $movimientoCaja->movimiento_numero_documento= 0;
                                $movimientoCaja->movimiento_estado = 1;
                                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                    $movimientoCaja->diario()->associate($diario);
                                }
                                $movimientoCaja->save();
                                $general->registrarAuditoria('Registro de movimiento de caja por cruce de anticipos de clientes con caja','0','Registro de movimiento de caja por cruce de anticipos de clientes con caja');
                                /*********************************************************************/
                            }
                            for ($c = 0; $c < count($datosSuc[$i]['anticipos']); ++$c){
                                $anticipo = Anticipo_Cliente::Anticipo($datosSuc[$i]['anticipos'][$c]['anticipo_id'])->first();
                                /**********************descuento de anticipo****************************/
                                $descuento =  new Descuento_Anticipo_Cliente();
                                $descuento->descuento_fecha = $request->get('idFechaCruze');
                                $descuento->descuento_valor = $datosSuc[$i]['anticipos'][$c]['descontar'];
                                $descuento->descuento_estado = "1";
                                $descuento->anticipo_id = $anticipo->anticipo_id;
                                $descuento->descuento_descripcion = 'CRUCE DE ANTICIPO CON '.$request->get('flexRadioDefault');
                                $descuento->diario()->associate($diario);
                                $descuento->save();
                                $general->registrarAuditoria('Registro de descuentos de anticipo de cliente No. '.$anticipo->anticipo_numero,'0','Registro de descuentos de anticipo de cliente No. '.$anticipo->anticipo_numero.' con '.$request->get('flexRadioDefault').' por un valor de -> '.$datosSuc[$i]['anticipos'][$c]['descontar']);
                                /****************************************************************/
                                if(is_null($anticipo->anticipo_documento)){
                                    $anticipo->anticipo_saldo = $anticipo->anticipo_saldo - round(floatval($datosSuc[$i]['anticipos'][$c]['descontar']),2);
                                }else{
                                    $anticipo->anticipo_saldo = $anticipo->anticipo_valor-round(Anticipo_Cliente::AnticipoClienteDescuentos($anticipo->anticipo_id)->sum('descuento_valor'),2);
                                }
                                if(round($anticipo->anticipo_saldo, 2) == 0){
                                    $anticipo->anticipo_estado = '2';
                                }else{
                                    $anticipo->anticipo_estado = '1';
                                }
                                $anticipo->update();
                                $general->registrarAuditoria('Actualización de anticipo de cliente No. '.$anticipo->anticipo_numero,'0','Actualización de anticipo de cliente No. '.$anticipo->anticipo_numero.' con '.$request->get('flexRadioDefault'));
                                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                    /********************detalle de diario de pago a cliente********************/
                                    $detalleDiario = new Detalle_Diario();
                                    $detalleDiario->detalle_debe = $datosSuc[$i]['anticipos'][$c]['descontar'];
                                    $detalleDiario->detalle_haber =  0.00;
                                    $detalleDiario->detalle_comentario = 'P/R CRUCE DE ANTICIPO No. '.$anticipo->anticipo_numero.' CON '.$request->get('flexRadioDefault');
                                    $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE CLIENTE';
                                    $detalleDiario->detalle_numero_documento = '0';
                                    $detalleDiario->detalle_conciliacion = '0';
                                    $detalleDiario->detalle_estado = '1';
                                    $detalleDiario->cliente_id = $cliente->cliente_id;
                                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                                    }else{
                                        $detalleDiario->cuenta_id = $cliente->cliente_cuenta_anticipo;
                                    }
                                    $diario->detalles()->save($detalleDiario);
                                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,'0','Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$datosSuc[$i]['anticipos'][$c]['descontar']);
                                    /***************************************************************************/
                                }
                            }
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                /********************detalle de diario de pago a cliente********************/                                
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = $datosSuc[$i]['valorSeleccion'];
                                if($request->get('flexRadioDefault') == 'BANCO'){
                                    $detalleDiario->detalle_comentario = 'P/R CRUCE ANTICIPOS DE CLIENTES CON '.$cuentaBancaria->banco->bancoLista->banco_lista_nombre. ' cuenta No. '.$cuentaBancaria->cuenta_bancaria_numero;
                                }
                                if($request->get('flexRadioDefault') == 'CAJA'){
                                    $detalleDiario->detalle_comentario = 'P/R CRUCE ANTICIPOS DE CLIENTES CON CAJA';
                                }
                                if($request->get('flexRadioDefault') == 'OTRO'){
                                    $detalleDiario->detalle_comentario = 'P/R CRUCE ANTICIPOS DE CLIENTES';
                                }
                                
                                $detalleDiario->detalle_tipo_documento = 'DESCUENTO ANTICIPO DE CLIENTE';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1'; 
                                if($request->get('flexRadioDefault') == 'OTRO'){                                    
                                    $tipo=substr($request->get('movimiento_id'), -1);
                                    $Idmovimiento=substr($request->get('movimiento_id'), 0, -1);
                                    if ($tipo=='C') {
                                        $TipoMovimientoCaja=Tipo_Movimiento_Caja::tipoMovimiento($Idmovimiento)->first();
                                        $detalleDiario->cuenta_id = $TipoMovimientoCaja->cuenta_id;
                                    }
                                    if ($tipo=='B') {
                                        $TipoMovimientoCaja=Tipo_Movimiento_Banco::TipoMovimiento($Idmovimiento)->first();
                                        $detalleDiario->cuenta_id = $TipoMovimientoCaja->cuenta_id;
                                    }                                    
                                }                               
                                if($request->get('flexRadioDefault') == 'BANCO'){
                                    $detalleDiario->cuenta_id = $cuentaBancaria->cuenta_id;
                                    $detalleDiario->transferencia()->associate($transferencia);
                                }
                                if($request->get('flexRadioDefault') == 'CAJA'){
                                    $detalleDiario->cuenta_id = $arqueoCaja->caja->cuenta_id;
                                }
                                $diario->detalles()->save($detalleDiario);
                                if($request->get('flexRadioDefault') == 'BANCO'){
                                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,'0','Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '. $cuentaBancaria->cuenta->cuenta_numero.' en el debe por un valor de -> '.$datosSuc[$i]['valorSeleccion']);

                                }
                                if($request->get('flexRadioDefault') == 'CAJA'){
                                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,'0','Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '. $arqueoCaja->caja->cuenta->cuenta_numero.' en el debe por un valor de -> '.$datosSuc[$i]['valorSeleccion']);

                                }
                                /***************************************************************************/
                            }
                        }
                    }
                }
            }
            $url = $general->pdfVariosDiario($diarios, $request->get('idFechaCruze'));
            DB::commit();
            return redirect('descuentoManualClientes')->with('success','Cruce realizado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('descuentoManualClientes')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
}
