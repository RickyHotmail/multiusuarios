<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Banco;
use App\Models\Banco_Lista;
use App\Models\Caja;
use App\Models\Caja_Usuario;
use App\Models\Cheque_Cliente;
use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Cuenta_Bancaria;
use App\Models\Cuenta_Cobrar;
use App\Models\Deposito;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Pago_CXC;
use App\Models\Diario;
use App\Models\Movimiento_Caja;
use App\Models\Pago_CXC;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\Models\Tarjeta_Credito;
use App\Models\Tipo_Movimiento_Banco;
use App\Models\Tipo_Movimiento_Caja;
use App\Models\Voucher;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class cobrosClientesController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            return view('admin.cuentasCobrar.pagosCXC.index',['movimientos'=>[],'movimientosBanco'=>[],'cajaAbierta'=>$cajaAbierta,'tarjetas'=>Tarjeta_Credito::TarjetasCredito()->get(),'sucursales'=>Sucursal::sucursales()->get(),'cajas'=>Caja_Usuario::CajaXusuario(Auth::user()->user_id)->get(),'bancos'=>Banco::bancos()->get(),'bancosLista'=>Banco_Lista::BancoListas()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function clientesSucursalCXC(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            return view('admin.cuentasCobrar.pagosCXC.index',['movimientos'=>Tipo_Movimiento_Caja::tipoMovimientos()->where('sucursal_id','=',$request->get('sucursal_id'))->get(),'movimientosBanco'=>Tipo_Movimiento_Banco::TipoMovimientos()->where('sucursal_id','=',$request->get('sucursal_id'))->get(),'bancosLista'=>Banco_Lista::BancoListas()->get(),'cajaAbierta'=>$cajaAbierta,'tarjetas'=>Tarjeta_Credito::TarjetasCredito()->get(),'sucurslaC'=>$request->get('sucursal_id'),'clientes'=>Cuenta_Cobrar::ClientesCXCSucursal($request->get('sucursal_id'))->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get(),'sucursales'=>Sucursal::sucursales()->get(),'cajas'=>Caja_Usuario::CajaXusuario(Auth::user()->user_id)->get(),'bancos'=>Banco::bancos()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('pagosCXC')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request){
        try{
            DB::beginTransaction();
            $general = new generalController();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $valPagoaux = $request->get('Ddescontar');
            for ($i = 0; $i < count($valPagoaux); ++$i){
                if($request->get('checkCXC'.$i)){
                    if($valPagoaux[$i] > 0){
                        $cxcAux2 = Cuenta_Cobrar::cuenta($request->get('checkCXC'.$i))->first();
                        $cierre = $general->cierre($request->get('fechaPago'),$cxcAux2->sucursal_id);          
                        if($cierre){
                            return redirect('pagosCXC')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                        }
                    }
                }
            }
            
            $docPago = 0;
            $facturas = '';
            $cuentaPago = '';
            $tipoDoc = '';
            if($request->get('radioPago') == 'EFECTIVO'){
                $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                $cuentaPago = $cuentacaja->cuenta_id;
                $tipoDoc = 'EFECTIVO';
            }
            if($request->get('radioPago') == 'DEPOSITO DE CHEQUE'){
                $docPago = $request->get('numDeposito');
                $cuentaBancaria = Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_bancaria'))->first();
                $cuentaPago = $cuentaBancaria->cuenta_id;
                $tipoDoc = 'DEPOSITO';
            }
            if($request->get('radioPago') == 'TRANSFERENCIA'){
                $docPago = $request->get('numDcoumento');
                $cuentaBancaria = Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_trans'))->first();
                $cuentaPago = $cuentaBancaria->cuenta_id;
                $tipoDoc = 'DEPOSITO';
            }
            if($request->get('radioPago') == 'TARJETA DE CRÉDITO'){
                $docPago = $request->get('numVoucher');
                $tarjeta = Tarjeta_Credito::findOrFail($request->get('tarjeta_id'));
                $cuentaPago = $tarjeta->cuenta_id;
                $tipoDoc = 'TARJETA DE CRÉDITO';
            }
            if($request->get('radioPago') == 'OTROS'){
                $docPago = 0;
                $tipo=substr($request->get('movimiento_id'), -1);
                $Idmovimiento=substr($request->get('movimiento_id'), 0, -1);
                if ($tipo=='C') {
                    $TipoMovimientoCaja=Tipo_Movimiento_Caja::tipoMovimiento($Idmovimiento)->first();
                    $cuentaPago = $TipoMovimientoCaja->cuenta_id;
                }
                if ($tipo=='B') {
                    $TipoMovimientoCaja=Tipo_Movimiento_Banco::TipoMovimiento($Idmovimiento)->first();
                    $cuentaPago = $TipoMovimientoCaja->cuenta_id;
                }
                $tipoDoc = 'OTROS';
            }
            
            
            if($request->get('radioPago') == 'DEPOSITO DE CHEQUE' or $request->get('radioPago') == 'TRANSFERENCIA'){
                $deposito =  new Deposito();
                $deposito->deposito_fecha = $request->get('fechaPago');
                $deposito->deposito_tipo = $request->get('radioPago');
                $deposito->deposito_valor = $request->get('idValorSeleccionado');
                $deposito->deposito_descripcion = 'PAGO DE CUENTAS POR COBRAR CLIENTE : '.$request->get('idNombre');
                $deposito->deposito_estado = '1';
                $deposito->empresa_id = Auth::user()->empresa_id;
                if($request->get('radioPago') == 'DEPOSITO DE CHEQUE'){
                    $deposito->cuenta_bancaria_id = $request->get('cuenta_bancaria');
                    $deposito->deposito_numero = $request->get('numDeposito');
                }else if($request->get('radioPago') == 'TRANSFERENCIA'){
                    $deposito->cuenta_bancaria_id = $request->get('cuenta_trans');
                    $deposito->deposito_numero = $request->get('numDcoumento');
                }
                $deposito->save();
                $general->registrarAuditoria('Registro de deposito por pago de cliente',$docPago,'Registro de deposito por pago de cliente en '.$request->get('radioPago')); 
                if($request->get('radioPago') == 'DEPOSITO DE CHEQUE'){
                    $chequeCliente = new Cheque_Cliente();
                    $chequeCliente->cheque_numero = $request->get('numero_cheque');
                    $chequeCliente->cheque_cuenta = $request->get('cuentaChequecliente');
                    $chequeCliente->cheque_valor = $request->get('idValorSeleccionado');
                    $chequeCliente->cheque_dueno = $request->get('idDueñoCheque');
                    $chequeCliente->cheque_estado = '1';
                    $chequeCliente->banco_lista_id = $request->get('banco_cheque'); 
                    $chequeCliente->deposito()->associate($deposito); 
                    $chequeCliente->save();
                    $general->registrarAuditoria('Registro de cheque por pago de cliente',$request->get('cuentaChequecliente'),'Registro de cheque por pago de cliente  en '.$request->get('radioPago'));
                }
            }
            if($request->get('radioPago') == 'TARJETA DE CRÉDITO'){
                $tarjeta = Tarjeta_Credito::findOrFail($request->get('tarjeta_id'));
                $voucher = new Voucher();
                $voucher->voucher_nombre = $tarjeta->tarjeta_nombre;
                $voucher->voucher_numero = $docPago;
                $voucher->voucher_valor = $request->get('idValorSeleccionado');
                $voucher->voucher_estado = '1';
                $voucher->empresa_id = Auth::user()->empresa_id;
                $voucher->save();
            }
            $valPago = $request->get('Ddescontar');
            $sucursales = Cuenta_Cobrar::ScucursalesxCXC($request->get('idCliente'))->select('sucursal_id')->distinct('sucursal_id')->get();
            $datosSuc = null;
            $filS=0;
            $contDiarios = 0;
            foreach($sucursales as $sucursal){
                $datosSuc[$filS]['sucursal_id'] = $sucursal->sucursal_id;
                $datosCuentas = null;
                $fil = 0;
                $sumaSeleciion = 0;
                for ($i = 0; $i < count($valPago); ++$i){
                    if($request->get('checkCXC'.$i)){
                        if($valPago[$i] > 0){
                            $cxcAux = Cuenta_Cobrar::cuenta($request->get('checkCXC'.$i))->first();
                            if($cxcAux->sucursal_id == $sucursal->sucursal_id){
                                $datosCuentas[$fil]['cuenta_id'] = $cxcAux->cuenta_id;
                                $datosCuentas[$fil]['descontar'] = $valPago[$i];
                                $sumaSeleciion = $sumaSeleciion + $valPago[$i];
                                $fil ++ ;
                            }
                        }
                    }
                }
                $datosSuc[$filS]['cuentas'] = $datosCuentas;
                $datosSuc[$filS]['valorSeleccion'] = $sumaSeleciion;
                $filS ++;
            }
            if(is_null($datosSuc) == false){
                for($i=0;$i < count($datosSuc);$i++){
                    $facturas = '';
                    $numDocAuc = '';
                    $tipDocAuc = '';
                    if(is_null($datosSuc[$i]['cuentas']) == false){
                        if(count($datosSuc[$i]['cuentas'])>0){
                            if(Auth::user()->empresa->empresa_contabilidad== '1'){
                                $diario = new Diario();
                                $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaPago'),'CIPC');
                                $diario->diario_fecha = $request->get('fechaPago');
                                $diario->diario_referencia = 'COMPROBANTE DE INGRESO DE PAGO DE CLIENTE';
                                $diario->diario_tipo_documento = 'PAGO EN '.$request->get('radioPago');
                                $diario->diario_numero_documento = $docPago;
                                $diario->diario_beneficiario = $request->get('idNombre');
                                $diario->diario_tipo = 'CIPC';
                                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechaPago'))->format('m');
                                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechaPago'))->format('Y');
                                $diario->diario_comentario = 'COMPROBANTE DE INGRESO DE PAGO DE CLIENTE : '.$request->get('idNombre').' '.$request->get('idConcepto');
                                $diario->diario_cierre = '0';
                                $diario->diario_estado = '1';
                                $diario->empresa_id = Auth::user()->empresa_id;
                                $diario->sucursal_id = $datosSuc[$i]['sucursal_id'];
                                $diario->save();
                                $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
                                $diarios[$contDiarios] = $diario;
                                $contDiarios ++;
                            }
                            $pago = new Pago_CXC();
                            $pago->pago_descripcion = $request->get('idConcepto');
                            $pago->pago_fecha = $request->get('fechaPago');
                            $pago->pago_tipo = $request->get('radioPago');
                            $pago->pago_valor = $datosSuc[$i]['valorSeleccion'];
                            $pago->pago_estado = '1';
                            if(Auth::user()->empresa->empresa_contabilidad== '1'){
                                $pago->diario()->associate($diario);
                            }
                            if($request->get('radioPago') == 'EFECTIVO'){
                                if($arqueoCaja){
                                    $pago->arqueo_id = $arqueoCaja->arqueo_id;
                                }
                            }
                            $pago->save();
                            $general->registrarAuditoria('Registro de Pago -> '.$pago->pago_descripcion.' Con '.$request->get('radioPago'),$docPago,'Con Valor '.$datosSuc[$i]['valorSeleccion']);
                                /***************************************************************************/
                            for ($c = 0; $c < count($datosSuc[$i]['cuentas']); ++$c){
                                $cxcAux = Cuenta_Cobrar::cuenta($datosSuc[$i]['cuentas'][$c]['cuenta_id'])->first();
                                $detallePago = new Detalle_Pago_CXC();
                                if($cxcAux->facturaVenta){
                                    $facturas = $facturas.' - '.$cxcAux->facturaVenta->factura_numero;
                                    $numDocAuc = $cxcAux->facturaVenta->factura_numero;
                                    $tipDocAuc = 'FACTURA DE VENTA';
                                    $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' DE FACTURA '.$cxcAux->facturaVenta->factura_numero;
                                    if($request->get('radioPago') == 'DEPOSITO DE CHEQUE'){
                                        $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' N° '.$request->get('numero_cheque').' DE FACTURA '.$cxcAux->facturaVenta->factura_numero;
                                    } 
                                }
                                else if($cxcAux->notaEntrega){
                                    $facturas = $facturas.' - '.$cxcAux->notaEntrega->nt_numero;
                                    $numDocAuc = $cxcAux->notaEntrega->nt_numero;
                                    $tipDocAuc = 'NOTA DE ENTREGA';
                                    $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' DE NOTA DE ENTREGA '.$cxcAux->notaEntrega->nt_numero; 
                                    if($request->get('radioPago') == 'DEPOSITO DE CHEQUE'){
                                        $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' N° '.$request->get('numero_cheque').' DE NOTA DE ENTREGA '.$cxcAux->notaEntrega->nt_numero;  
                                    }
                                }
                                else if($cxcAux->notaDebito){
                                    $facturas = $facturas.' - '.$cxcAux->notaDebito->nd_numero; 
                                    $numDocAuc = $cxcAux->notaDebito->nd_numero;
                                    $tipDocAuc = 'NOTA DE DEBITO'; 
                                    $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' DE NOTA DE DÉBITO '.$cxcAux->notaDebito->nd_numero; 
                                    if($request->get('radioPago') == 'DEPOSITO DE CHEQUE'){
                                        $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' N° '.$request->get('numero_cheque').' DE NOTA DE DÉBITO '.$cxcAux->notaDebito->nd_numero; 
                                    }
                                }else{
                                    $facturas = $facturas.' - '.substr($cxcAux->cuenta_descripcion, 38); 
                                    $numDocAuc = substr($cxcAux->cuenta_descripcion, 38); 
                                    $tipDocAuc = 'CUENTA POR COBRAR';
                                    $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' DE FACTURA '.substr($cxcAux->cuenta_descripcion, 38); 
                                    if($request->get('radioPago') == 'DEPOSITO DE CHEQUE'){
                                        $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' N° '.$request->get('numero_cheque').' DE FACTURA '.substr($cxcAux->cuenta_descripcion, 38); 
                                    }
                                }
                                $detallePago->detalle_pago_valor = $datosSuc[$i]['cuentas'][$c]['descontar']; 
                                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1; 
                                $detallePago->detalle_pago_estado = '1'; 
                                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                                $detallePago->pagoCXC()->associate($pago);
                                $detallePago->save();

                                if($cxcAux->facturaVenta){
                                    $general->registrarAuditoria('Registro de detalle de pago de Cliente -> '.$request->get('idNombre'),'0','Detalle de pago de factura No. '.$cxcAux->facturaVenta->factura_numero.' pago en '.$request->get('radioPago')); 
                                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxcAux->facturaVenta->factura_id)->sum('descuento_valor'),2);
                                }elseif($cxcAux->notaEntrega){
                                    $general->registrarAuditoria('Registro de detalle de pago de Cliente -> '.$request->get('idNombre'),'0','Detalle de pago de nota de entrega No. '.$cxcAux->notaEntrega->nt_numero.' pago en '.$request->get('radioPago')); 
                                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2);
                                }elseif($cxcAux->notaDebito){
                                    $general->registrarAuditoria('Registro de detalle de pago de Cliente -> '.$request->get('idNombre'),'0','Detalle de pago de nota de débito No. '.$cxcAux->notaDebito->nd_numero.' pago en '.$request->get('radioPago')); 
                                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2);
                                }else{
                                    $general->registrarAuditoria('Registro de detalle de pago de Cliente -> '.$request->get('idNombre'),'0','Detalle de pago de factura No. '.substr($cxcAux->cuenta_descripcion, 38).' pago en '.$request->get('radioPago')); 
                                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_saldo - $detallePago->detalle_pago_valor;
                                }
                                if(round($cxcAux->cuenta_saldo,2) == 0){
                                    $cxcAux->cuenta_estado = '2';
                                    $cxcAux->cuenta_saldo = 0;
                                }else{
                                    $cxcAux->cuenta_estado = '1';
                                }
                                $cxcAux->update();
                                /*Inicio de registro de auditoria*/
                                if($cxcAux->facturaVenta){
                                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('idNombre'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('idNombre').' con factura -> '.$cxcAux->facturaVenta->factura_numero);
                                }elseif($cxcAux->notaEntrega){
                                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('idNombre'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('idNombre').' con nota de entrega -> '.$cxcAux->notaEntrega->nt_numero);
                                }elseif($cxcAux->notaDebito){
                                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('idNombre'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('idNombre').' con Nota de Débito -> '.$cxcAux->notaDebito->nd_numero);
                                }else{
                                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('idNombre'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('idNombre').' con factura -> '.substr($cxcAux->cuenta_descripcion, 38));
                                }
                                /*Fin de registro de auditoria*/ 
                                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                                    /********************detalle de diario de pago a cliente********************/
                                    $detalleDiario = new Detalle_Diario();
                                    $detalleDiario->detalle_debe = 0.00;
                                    $detalleDiario->detalle_haber = $datosSuc[$i]['cuentas'][$c]['descontar']; 
                                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                                    $detalleDiario->detalle_tipo_documento = $tipDocAuc;
                                    $detalleDiario->detalle_numero_documento = $numDocAuc;
                                    $detalleDiario->detalle_conciliacion = '0';
                                    $detalleDiario->detalle_estado = '1';
                                    $detalleDiario->cliente_id = $request->get('idCliente');
                                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                                    }else{
                                        $parametrizacionContable = Cliente::findOrFail($request->get('idCliente'));
                                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                                    }
                                    $diario->detalles()->save($detalleDiario);
                                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$docPago,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$datosSuc[$i]['cuentas'][$c]['descontar']);
                                    /***************************************************************************/
                                }
                            }
                            $general->registrarAuditoria('Registro de pago de Cliente -> '.$request->get('idNombre'),$docPago,'Pago de documentos No. '.$facturas.' en '.$request->get('radioPago')); 
                            if(Auth::user()->empresa->empresa_contabilidad== '1'){
                                $diario->diario_comentario = 'COMPROBANTE DE INGRESO DE PAGO DE CLIENTE : '.$request->get('idNombre').' - '.$facturas.' '.$request->get('idConcepto');
                                $diario->update();
                                /********************detalle de diario de pago a cliente********************/
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = $datosSuc[$i]['valorSeleccion'];
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario = 'P/R PAGO EN '.$request->get('radioPago').' DE CUENTA POR COBRAR';
                                $detalleDiario->detalle_tipo_documento = $tipoDoc;
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuentaPago;
                                if($request->get('radioPago') == 'DEPOSITO DE CHEQUE' or $request->get('radioPago') == 'TRANSFERENCIA'){
                                    $detalleDiario->deposito()->associate($deposito);
                                }
                                if($request->get('radioPago') == 'TARJETA DE CRÉDITO'){
                                    $detalleDiario->voucher()->associate($voucher);
                                }
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$docPago,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.Cuenta::cuenta($cuentaPago)->first()->cuenta_numero.' en el debe por un valor de -> '.$datosSuc[$i]['valorSeleccion']);
                                /***************************************************************************/
                            }
                        }
                    }
                }
            }
            if($request->get('radioPago') == 'DEPOSITO DE CHEQUE' or $request->get('radioPago') == 'TRANSFERENCIA'){
                $deposito->deposito_descripcion = 'PAGO DE CUENTAS POR COBRAR CLIENTE : '.$request->get('idNombre').$facturas;
                $deposito->update();
            }
            if($request->get('radioPago') == 'EFECTIVO'){
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="ENTRADA";
                $movimientoCaja->movimiento_descripcion= 'P/R COBRO A CLIENTE :'.$request->get('idNombre');
                $movimientoCaja->movimiento_valor= $request->get('idValorSeleccionado');
                $movimientoCaja->movimiento_documento="COBRO DE CLIENTE EN EFECTIVO";
                $movimientoCaja->movimiento_numero_documento= $facturas;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                    $movimientoCaja->diario()->associate($diario);
                }
                $movimientoCaja->save();
                /*********************************************************************/
            }
            $url='';
            if(Auth::user()->empresa->empresa_llevaContabilidad== '1'){
                $url = $general->pdfVariosDiario($diarios, $request->get('fechaPago'));
            }
            DB::commit();
            return redirect('pagosCXC')->with('success','Pago realizado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('pagosCXC')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function nuevoEliminarPago(){
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasCobrar.eliminarPagos.index',['clientes'=>Cliente::clientes()->get(),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarEliminar(Request $request){
        if (isset($_POST['buscar'])){
            return $this->buscarPagos($request);
        }
        if (isset($_POST['eliminar'])){
            return $this->eliminarPagos($request);
        }
    }
    public function buscarPagos(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $todo = 0;
            $count = 1;
            $datos = null;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            foreach(Cuenta_Cobrar::CuentasCobrarByPagos($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('clienteID'),$todo,$request->get('sucursal_id'))->select('cuenta_cobrar.cuenta_id','cuenta_cobrar.cuenta_fecha','cuenta_cobrar.cuenta_monto','cuenta_cobrar.cuenta_saldo','cuenta_cobrar.cuenta_descripcion')->distinct('cuenta_cobrar.cuenta_fecha','cuenta_cobrar.cuenta_id')->get() as $cuenta){
                $datos[$count]['cod'] = $cuenta->cuenta_id;
                $datos[$count]['doc'] = ''; 
                $datos[$count]['num'] = ''; 
                $datos[$count]['dia'] = ''; 
                if($cuenta->facturaVenta){
                    $datos[$count]['doc'] = 'FACTURA'; 
                    $datos[$count]['num'] = $cuenta->facturaVenta->factura_numero;
                    $datos[$count]['dia'] = $cuenta->facturaVenta->diario->diario_codigo; 
                }
                if($cuenta->notaEntrega){
                    $datos[$count]['doc'] = 'NOTA DE ENTREGA'; 
                    $datos[$count]['num'] = $cuenta->notaEntrega->nt_numero;
                    if(Auth::user()->empresa->empresa_contabilidad== '1'){
                        $datos[$count]['dia'] = $cuenta->notaEntrega->diario->diario_codigo; 
                    }else{
                        $datos[$count]['dia'] = '';
                    }
                }
                if($cuenta->notaDebito){
                    $datos[$count]['doc'] = 'NOTA DE DÉBITO'; 
                    $datos[$count]['num'] = $cuenta->notaDebito->nd_numero;  
                    $datos[$count]['dia'] = $cuenta->notaDebito->diario->diario_codigo; 
                }
                if($datos[$count]['doc'] == ''){
                    $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 38);
                    $datos[$count]['doc'] = 'FACTURA'; 
                }
                $datos[$count]['fec'] = DateTime::createFromFormat('Y-m-d', $cuenta->cuenta_fecha)->format('d/m/Y');
                $datos[$count]['mon'] = $cuenta->cuenta_monto; 
                $datos[$count]['sal'] = $cuenta->cuenta_saldo; 
                $datos[$count]['val'] = ''; 
                $datos[$count]['fep'] = ''; 
                $datos[$count]['dip'] = ''; 
                $datos[$count]['ref'] = ''; 
                $datos[$count]['tot'] = '1';
                $count ++;
                foreach(Detalle_Pago_CXC::CuentaCobrarPagosFecha($cuenta->cuenta_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo)->orderBy('pago_fecha')->get() as $pago){
                    $datos[$count]['cod'] = $pago->detalle_pago_id;
                    $datos[$count]['doc'] = ''; 
                    $datos[$count]['num'] = ''; 
                    $datos[$count]['dia'] = '';                
                    $datos[$count]['fec'] = '';
                    $datos[$count]['mon'] = ''; 
                    $datos[$count]['sal'] = ''; 
                    $datos[$count]['val'] = $pago->detalle_pago_valor; 
                    $datos[$count]['fep'] = DateTime::createFromFormat('Y-m-d', $pago->pagoCXC->pago_fecha)->format('d/m/Y');  
                    $datos[$count]['dip'] = $pago->pagoCXC->diario->diario_codigo; 
                    $datos[$count]['ref'] = $pago->detalle_pago_descripcion; 
                    $datos[$count]['chk'] = '1';
                    if($pago->pagoCXC->pago_tipo == 'NOTA DE CRÉDITO' or $pago->pagoCXC->pago_tipo == 'COMPROBANTE DE RETENCION DE VENTA'){
                        $datos[$count]['chk'] = '0';
                    } 
                    $datos[$count]['tot'] = '2';
                    $count ++;
                }
            }
            return view('admin.cuentasCobrar.eliminarPagos.index',['datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'clienteC'=>$request->get('clienteID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'clientes'=>Cliente::clientes()->get(),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]); 
        }catch(\Exception $ex){
            return redirect('eliminarPagoCXC')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminarPagos(Request $request){
        try {
            DB::beginTransaction();
            $jo=false;
            $general = new generalController();   
            $noTienecaja =null;
            $seleccion = $request->get('checkbox');
            for ($i = 0; $i < count($seleccion); ++$i) {
                $detalle_pago = Detalle_Pago_CXC::DetallePago($seleccion[$i])->first();
                $cierre = $general->cierre($detalle_pago->pagoCXC->pago_fecha,$detalle_pago->cuentaCobrar->sucursal_id);          
                if($cierre){
                    return redirect('eliminarPagoCXC')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
            }
            for ($i = 0; $i < count($seleccion); ++$i) {
                $detalle_pago = Detalle_Pago_CXC::DetallePago($seleccion[$i])->first();
                if(isset($detalle_pago->detalle_pago_id)){
                    $cxcAux = $detalle_pago->cuentaCobrar;
                    $valorPagoGeneral = $detalle_pago->detalle_pago_valor;
                    $pago = $detalle_pago->pagoCXC;
                    
                    $diario = null;
                    $jo=false;
                    if(isset($pago->diario)){
                        $diario = $pago->diario;
                        if($pago->pago_tipo == 'EFECTIVO' or $pago->pago_tipo == 'PAGO EN EFECTIVO'){
                            $cajaAbierta=Arqueo_Caja::ArqueoCajaxid($pago->arqueo_id)->first();
                            if(isset($cajaAbierta->arqueo_id)){
                                $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($pago->arqueo_id, $pago->diario_id)->first();
                                $movimientoCaja->delete();
                                $jo=true;
                            }else{                            
                                $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                                if ($cajaAbierta){
                                    /**********************movimiento caja****************************/
                                    $movimientoCaja = new Movimiento_Caja();          
                                    $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                    $movimientoCaja->movimiento_hora=date("H:i:s");
                                    $movimientoCaja->movimiento_tipo="SALIDA";
                                    $movimientoCaja->movimiento_descripcion= 'P/R ELIMINACION DE PAGO DE CLIENTE :'.$pago->pago_descripcion;
                                    $movimientoCaja->movimiento_valor= $pago->pago_valor;
                                    $movimientoCaja->movimiento_documento="P/R ELIMINACION DE PAGO EN EFECTIVO";
                                    $movimientoCaja->movimiento_numero_documento= 0;
                                    $movimientoCaja->movimiento_estado = 1;
                                    $movimientoCaja->arqueo_id = $cajaAbierta->arqueo_id;                                
                                    $movimientoCaja->save();
                                    
                                    $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($pago->arqueo_id,$pago->diario_id)->first();
                                   
                                    $movimientoAnterioraux =Movimiento_Caja::findOrFail($movimientoAnterior->movimiento_id);
                                   
                                    $movimientoAnterioraux->diario_id = null;
                                    $movimientoAnterioraux->save();

                                    $jo=true;
                                /*********************************************************************/                               
                                }else{
                                    $noTienecaja = 'Lo valores en Efectivo no pudieron ser eliminados, porque no dispone de CAJA ABIERTA';                               
                                }
                            }
                        }else{
                            $jo=true;
                        }
                        if($jo){      
                            foreach($diario->detalles as $detalle){
                                if(isset($detalle->deposito)){                
                                    foreach($detalle->deposito->detalleDiario as $detalleDepo){
                                        if(isset($detalleDepo->diario)){
                                            $pago2 = $detalleDepo->diario->pagocuentaCobrar;
                                            $diario2 = $detalleDepo->diario;
                                            $bandera2=false;
                                            if($pago2->pago_tipo == 'EFECTIVO' or $pago2->pago_tipo == 'PAGO EN EFECTIVO'){
                                                $cajaAbierta2=Arqueo_Caja::ArqueoCajaxid($pago2->arqueo_id)->first();
                                                if(isset($cajaAbierta2->arqueo_id)){
                                                    $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($pago2->arqueo_id, $pago2->diario_id)->first();
                                                    $movimientoCajaraux =Movimiento_Caja::findOrFail($movimientoCaja->movimiento_id);
                                                    $movimientoCajaraux->delete();
                                                    $bandera2=true;
                                                }else{                            
                                                    $cajaAbierta2=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                                                    if ($cajaAbierta2){
                                                        /**********************movimiento caja****************************/
                                                        $movimientoCaja = new Movimiento_Caja();          
                                                        $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                                        $movimientoCaja->movimiento_hora=date("H:i:s");
                                                        $movimientoCaja->movimiento_tipo="SALIDA";
                                                        $movimientoCaja->movimiento_descripcion= 'P/R ELIMINACION DE PAGO DE CLINETE :'.$pago2->pago_descripcion;
                                                        $movimientoCaja->movimiento_valor= $pago2->pago_valor;
                                                        $movimientoCaja->movimiento_documento="P/R ELIMINACION DE PAGO EN EFECTIVO";
                                                        $movimientoCaja->movimiento_numero_documento= 0;
                                                        $movimientoCaja->movimiento_estado = 1;
                                                        $movimientoCaja->arqueo_id = $cajaAbierta2->arqueo_id;                                
                                                        $movimientoCaja->save();
                                                        
                                                        $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($pago2->arqueo_id,$pago2->diario_id)->first();
                                                        $movimientoAnterior =Movimiento_Caja::findOrFail($movimientoAnterior->movimiento_id);
                                                        $movimientoAnterior->diario_id = null;
                                                        $movimientoAnterior->save();
                        
                                                        $bandera2=true;
                                                    /*********************************************************************/                               
                                                    }else{
                                                        $noTienecaja = 'Lo valores en Efectivo no pudieron ser eliminados, porque no dispone de CAJA ABIERTA';                               
                                                    }
                                                }
                                            }else{
                                                $bandera2=true;
                                            }
                                            if($bandera2){
                                                foreach ($pago2->detalles as $detallePago) {
                                                    if(isset($detallePago->cuentaCobrar->cuenta_id)){
                                                        if(isset(Detalle_Pago_CXC::DetallePago($detallePago->detalle_pago_id)->first()->detalle_pago_id)){
                                                            $cxcAux2 = $detallePago->cuentaCobrar;
                                                            $valorPago = $detallePago->detalle_pago_valor;
                                                            $detallePago->delete();
                                                            $general->registrarAuditoria('Eliminacion del detalle de pago cuentas por cobrar  '.$detallePago->cuentaCobrar->cuenta_descripcion,'','');  
                                                            if(isset($cxcAux2->facturaVenta->factura_id)){
                                                                if($pago2->pago_tipo == 'PAGO EN EFECTIVO'){
                                                                    $cxcAux2->cuenta_tipo = 'CREDITO';
                                                                    $factura = $cxcAux2->facturaVenta;
                                                                    $factura->factura_tipo_pago = 'CREDITO';
                                                                    $factura->update();
                                                                }
                                                                $cxcAux2->cuenta_saldo = $cxcAux2->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux2->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxcAux2->facturaVenta->factura_id)->sum('descuento_valor'),2);
                                                            }elseif(isset($cxcAux2->notaDebito->nd_id)){
                                                                $cxcAux2->cuenta_saldo = $cxcAux2->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux2->cuenta_id)->sum('detalle_pago_valor'),2);
                                                            }elseif(isset($cxcAux2->notaEntrega->nt_id)){
                                                                $cxcAux2->cuenta_saldo = $cxcAux2->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux2->cuenta_id)->sum('detalle_pago_valor'),2);
                                                            }else{
                                                                $cxcAux2->cuenta_saldo = $cxcAux2->cuenta_saldo + $valorPago;
                                                            }
                                                            if(round($cxcAux2->cuenta_saldo,2) == 0){
                                                                $cxcAux2->cuenta_estado = '2';
                                                                $cxcAux2->cuenta_saldo = 0;
                                                            }else{
                                                                $cxcAux2->cuenta_estado = '1';
                                                            }
                                                            $cxcAux2->update();
                                                            if(isset($cxcAux2->facturaVenta->factura_id)){
                                                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux2->facturaVenta->cliente->cliente_nombre.' con factura -> '.$cxcAux2->facturaVenta->factura_numero);
                                                            }elseif(isset($cxcAux2->notaEntrega->nt_id)){
                                                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux2->notaEntrega->cliente->cliente_nombre.' con nota de entrega -> '.$cxcAux2->notaEntrega->nt_numero);
                                                            }elseif(isset($cxcAux2->notaDebito->nd_id)){
                                                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux2->notaDebito->factura->cliente->cliente_nombre.' con Nota de Débito -> '.$cxcAux2->notaDebito->nd_numero);
                                                            }else{
                                                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux2->cliente->cliente_nombre.' '.$cxcAux2->cuenta_descripcion);
                                                            }
                                                        }
                                                    }
                                                }
                                                $pago2->delete();
                                                if($pago2->pago_tipo == 'PAGO EN EFECTIVO'){
                                                    foreach($diario2->detalles as $detalleDiario2){
                                                        if($detalleDiario2->detalle_debe > 0){
                                                            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario2->sucursal_id, 'CUENTA POR COBRAR')->first();
                                                            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                                                                $detalleDiario2->cuenta_id = $parametrizacionContable->cuenta_id;
                                                            }else{
                                                                $parametrizacionContable = Cliente::findOrFail($cxcAux2->cliente_id);
                                                                $detalleDiario2->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                                                            }
                                                            $detalleDiario2->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                                                            $detalleDiario2->update();
                                                        }
                                                    }
                                                }else{
                                                    foreach($diario2->detalles as $detalleDiario2){
                                                        $detalleDiario2->delete();
                                                        $general->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario2->diario_codigo,$diario2->diario_codigo,'Eliminacion de detalle de diario por eliminacion de pago de cuentas por cobrar');  
                                                    }
                                                    $diario2->delete();
                                                    $general->registrarAuditoria('Eliminacion de diario  N°'.$diario2->diario_codigo,$diario2->diario_codigo,'Eliminacion de diario por eliminacion de pago de cuentas por cobrar');  
                                                }
                                            }
                                        }
                                    }   
                                    if($bandera2){
                                        $depositoEliminar = $detalle->deposito;
                                        if(isset($detalle->deposito)){
                                            if(isset($depositoEliminar->chequeCliente)){
                                                if(is_null($detalle->deposito->chequeCliente) == false){
                                                    $general->registrarAuditoria('Eliminacion de cheque de cliente '.$detalle->deposito->chequeCliente->cheque_dueno,'','Eliminacion de cheque de cliente '.$detalle->deposito->chequeCliente->cheque_dueno.' numero '.$detalle->deposito->chequeCliente->cheque_numero.' por un valor de '.$detalle->deposito->chequeCliente->cheque_valor.' por eliminacion de pago de cuenta por cobrar');  
                                                    $depositoEliminar->chequeCliente->delete();
                                                }
                                            }
                                        }
                                        $depositoEliminar->delete();
                                    }
                                }
                                if(isset($detalle->voucher)){
                                    foreach($detalle->voucher->detalleDiario as $detallevoucher){
                                        if(isset($detallevoucher->diario)){
                                            $pago2 = $detallevoucher->diario->pagocuentaCobrar;
                                            $diario2 = $detallevoucher->diario;
                                            $bandera2=false;
                                            if($pago2->pago_tipo == 'EFECTIVO'){
                                                $cajaAbierta2=Arqueo_Caja::ArqueoCajaxid($pago2->arqueo_id)->first();
                                                if(isset($cajaAbierta2->arqueo_id)){
                                                    $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($pago2->arqueo_id, $pago2->diario_id)->first();
                                                    $movimientoCajaraux =Movimiento_Caja::findOrFail($movimientoCaja->movimiento_id);
                                                    $movimientoCajaraux->delete();
                                                    
                                                    $bandera2=true;
                                                }else{                            
                                                    $cajaAbierta2=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                                                    if ($cajaAbierta2){
                                                        /**********************movimiento caja****************************/
                                                        $movimientoCaja = new Movimiento_Caja();          
                                                        $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                                        $movimientoCaja->movimiento_hora=date("H:i:s");
                                                        $movimientoCaja->movimiento_tipo="SALIDA";
                                                        $movimientoCaja->movimiento_descripcion= 'P/R ELIMINACION DE PAGO DE CLIENTE :'.$pago2->pago_descripcion;
                                                        $movimientoCaja->movimiento_valor= $pago2->pago_valor;
                                                        $movimientoCaja->movimiento_documento="P/R ELIMINACION DE PAGO EN EFECTIVO";
                                                        $movimientoCaja->movimiento_numero_documento= 0;
                                                        $movimientoCaja->movimiento_estado = 1;
                                                        $movimientoCaja->arqueo_id = $cajaAbierta2->arqueo_id;                                
                                                        $movimientoCaja->save();
                                                        
                                                        $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($pago2->arqueo_id,$pago2->diario_id)->first();
                                                        $movimientoAnterior =Movimiento_Caja::findOrFail($movimientoAnterior->movimiento_id);
                                                        $movimientoAnterior->diario_id = null;
                                                        $movimientoAnterior->save();
                        
                                                        $bandera2=true;
                                                    /*********************************************************************/                               
                                                    }else{
                                                        $noTienecaja = 'Lo valores en Efectivo no pudieron ser eliminados, porque no dispone de CAJA ABIERTA';                               
                                                    }
                                                }
                                            }else{
                                                $bandera2=true;
                                            }
                                            if($bandera2){
                                                foreach ($pago2->detalles as $detallePago) {
                                                    if($detallePago->cuentaCobrar){
                                                        if(isset(Detalle_Pago_CXC::DetallePago($detallePago->detalle_pago_id)->first()->detalle_pago_id)){
                                                            $cxcAux2 = $detallePago->cuentaCobrar;
                                                            $valorPago = $detallePago->detalle_pago_valor;
                                                            $detallePago->delete();
                                                            $general->registrarAuditoria('Eliminacion del detalle de pago cuentas por cobrar  '.$detallePago->cuentaCobrar->cuenta_descripcion,'','');  
                                                            if(isset($cxcAux2->facturaVenta->fctura_id)){
                                                                if($pago2->pago_tipo == 'PAGO EN EFECTIVO'){
                                                                    $cxcAux2->cuenta_tipo = 'CREDITO';
                                                                    $factura = $cxcAux2->facturaVenta;
                                                                    $factura->factura_tipo_pago = 'CREDITO';
                                                                    $factura->update();
                                                                }
                                                                $cxcAux2->cuenta_saldo = $cxcAux2->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux2->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxcAux2->facturaVenta->factura_id)->sum('descuento_valor'),2);
                                                            }elseif(isset($cxcAux2->notaEntrega->nt_id)){
                                                                $cxcAux2->cuenta_saldo = $cxcAux2->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux2->cuenta_id)->sum('detalle_pago_valor'),2);
                                                            }elseif(isset($cxcAux2->notaDebito->nd_id)){
                                                                $cxcAux2->cuenta_saldo = $cxcAux2->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux2->cuenta_id)->sum('detalle_pago_valor'),2);
                                                            }else{
                                                                $cxcAux2->cuenta_saldo = $cxcAux2->cuenta_saldo + $valorPago;
                                                            }
                                                            if(round($cxcAux2->cuenta_saldo,2) == 0){
                                                                $cxcAux2->cuenta_estado = '2';
                                                                $cxcAux2->cuenta_saldo = 0;
                                                            }else{
                                                                $cxcAux2->cuenta_estado = '1';
                                                            }
                                                            $cxcAux2->update();
                                                            if(isset($cxcAux2->facturaVenta->fctura_id)){
                                                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux2->facturaVenta->cliente->cliente_nombre.' con factura -> '.$cxcAux2->facturaVenta->factura_numero);
                                                            }elseif(isset($cxcAux2->notaEntrega->nt_id)){
                                                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux2->notaEntrega->cliente->cliente_nombre.' con nota de entrega -> '.$cxcAux2->notaEntrega->nt_numero);
                                                            }elseif(isset($cxcAux2->notaDebito->nd_id)){
                                                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux2->notaDebito->factura->cliente->cliente_nombre.' con Nota de Débito -> '.$cxcAux2->notaDebito->nd_numero);
                                                            }else{
                                                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux2->cliente->cliente_nombre.' '.$cxcAux2->cuenta_descripcion);
                                                            }
                                                        }
                                                    }
                                                }
                                                $pago2->delete();
                                                if($pago2->pago_tipo == 'PAGO EN EFECTIVO'){
                                                    foreach($diario2->detalles as $detalleDiario2){
                                                        if($detalleDiario2->detalle_debe > 0){
                                                            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario2->sucursal_id, 'CUENTA POR COBRAR')->first();
                                                            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                                                                $detalleDiario2->cuenta_id = $parametrizacionContable->cuenta_id;
                                                            }else{
                                                                $parametrizacionContable = Cliente::findOrFail($cxcAux2->cliente_id);
                                                                $detalleDiario2->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                                                            }
                                                            $detalleDiario2->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                                                            $detalleDiario2->update();
                                                        }
                                                    }
                                                }else{
                                                    foreach($diario2->detalles as $detalleDiario2){
                                                        $detalleDiario2->delete();
                                                        $general->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario2->diario_codigo,$diario2->diario_codigo,'Eliminacion de detalle de diario por eliminacion de pago de cuentas por cobrar');  
                                                    }
                                                    $diario2->delete();
                                                    $general->registrarAuditoria('Eliminacion de diario  N°'.$diario2->diario_codigo,$diario2->diario_codigo,'Eliminacion de diario por eliminacion de pago de cuentas por cobrar');  
                                                }
                                            }
                                        }
                                    }   
                                    if($bandera2){
                                        $detalle->voucher->delete();
                                    }
                                }
                            }
                        }
                    }
                    if(isset(Detalle_Pago_CXC::DetallePago($detalle_pago->detalle_pago_id)->first()->detalle_pago_id)){
                        if($jo){
                            foreach ($pago->detalles as $detalle) {
                                $detalle->delete();
                                $general->registrarAuditoria('Eliminacion del detalle de pago cuentas por cobrar  '.$detalle->cuentaCobrar->cuenta_descripcion,'','');  
                            }
                            $pago->delete();
                            $general->registrarAuditoria('Eliminacion de pago de cuentas por cobrar  '.$pago->pago_descripcion,'','');  
                            if(!is_null($diario)){
                                if($pago->pago_tipo == 'PAGO EN EFECTIVO'){
                                    foreach($diario->detalles as $detalleDiario){
                                        if($detalleDiario->detalle_debe > 0){
                                            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                                            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                                                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                                            }else{
                                                $parametrizacionContable = Cliente::findOrFail($cxcAux->cliente_id);
                                                $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                                            }
                                            $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                                            $detalleDiario->update();
                                        }
                                    }
                                }else{
                                    foreach($diario->detalles as $detalle){
                                        if(isset($detalle->deposito->deposito_id)){
                                            if(isset($detalle->deposito->chequeCliente)){
                                                if(is_null($detalle->deposito->chequeCliente) == false){
                                                    $general->registrarAuditoria('Eliminacion de cheque de cliente '.$detalle->deposito->chequeCliente->cheque_dueno,'','Eliminacion de cheque de cliente '.$detalle->deposito->chequeCliente->cheque_dueno.' numero '.$detalle->deposito->chequeCliente->cheque_numero.' por un valor de '.$detalle->deposito->chequeCliente->cheque_valor.' por eliminacion de pago de cuenta por cobrar');  
                                                    $detalle->deposito->chequeCliente->delete();
                                                }
                                            }
                                            $deposito1 = $detalle->deposito;
                                            $general->registrarAuditoria('Eliminacion de deposito de cliente ','','Eliminacion de deposito de cliente numero '.$detalle->deposito->deposito_numero.' por un valor de '.$detalle->deposito->deposito_valor.' por eliminacion de pago de cuenta por cobrar');  
                                        }
                                        if(isset($detalle->voucher)){
                                            $voucher1 = $detalle->voucher;
                                            $general->registrarAuditoria('Eliminacion de voucher de cliente ','','Eliminacion de voucher de cliente numero '.$detalle->voucher->voucher_numero.' por un valor de '.$detalle->voucher->voucher_valor.' por eliminacion de pago de cuenta por cobrar');  
                                        }
                                        $detalle->delete();
                                        if(isset($deposito1)){
                                            $deposito1->delete();
                                        }
                                        if(isset($voucher1)){
                                            $voucher1->delete();
                                        }
                                        $general->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de detalle de diario por eliminacion de pago de cuentas por cobrar');  
                                    }
                                    $diario->delete();
                                    $general->registrarAuditoria('Eliminacion de diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de diario por eliminacion de pago de cuentas por cobrar');  
                                }
                            }
                            if(isset($cxcAux->facturaVenta->factura_id)){
                                if($pago->pago_tipo == 'PAGO EN EFECTIVO'){
                                    $cxcAux->cuenta_tipo = 'CREDITO';
                                    $factura = $cxcAux->facturaVenta;
                                    $factura->factura_tipo_pago = 'CREDITO';
                                    $factura->update();
                                }
                                $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxcAux->facturaVenta->factura_id)->sum('descuento_valor'),2);
                            }elseif(isset($cxcAux->notaEntrega->nt_id)){
                                $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2);
                            }elseif(isset($cxcAux->notaDebito->nd_id)){
                                $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2);
                            }else{
                                $cxcAux->cuenta_saldo = $cxcAux->cuenta_saldo + $valorPagoGeneral;
                            }
                            
                            if(round($cxcAux->cuenta_saldo,2) == 0){
                                $cxcAux->cuenta_estado = '2';
                                $cxcAux->cuenta_saldo = 0;
                            }else{
                                $cxcAux->cuenta_estado = '1';
                            }
                            $cxcAux->update();
                            if(isset($cxcAux->facturaVenta->factura_id)){
                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux->facturaVenta->cliente->cliente_nombre.' con factura -> '.$cxcAux->facturaVenta->factura_numero);
                            }elseif(isset($cxcAux->notaEntrega->nt_id)){
                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux->notaEntrega->cliente->cliente_nombre.' con nota de entrega -> '.$cxcAux->notaEntrega->nt_numero);
                            }elseif(isset($cxcAux->notaDebito->nd_id)){
                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux->notaDebito->factura->cliente->cliente_nombre.' con Nota de Débito -> '.$cxcAux->notaDebito->nd_numero);
                            }else{
                                $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente','0','Actualizacion de cuenta por cobrar por eliminacion de pagos de cliente -> '.$cxcAux->cliente->cliente_nombre.' '.$cxcAux->cuenta_descripcion);
                            }
                        }
                    }
                }
            }
            DB::commit();
            if(isset($noTienecaja)){
                return redirect('eliminarPagoCXC')->with('success','Datos eliminados exitosamente')->with('error2',$noTienecaja);
            }else{
                return redirect('eliminarPagoCXC')->with('success','Datos eliminados exitosamente');
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('eliminarPagoCXC')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
