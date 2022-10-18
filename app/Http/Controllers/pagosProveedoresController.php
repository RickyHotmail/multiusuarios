<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Banco;
use App\Models\Caja;
use App\Models\Caja_Usuario;
use App\Models\Cheque;
use App\Models\Cuenta;
use App\Models\Cuenta_Bancaria;
use App\Models\Cuenta_Pagar;
use App\Models\Descuento_Anticipo_Proveedor;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Pago_CXP;
use App\Models\Diario;
use App\Models\Movimiento_Caja;
use App\Models\Nota_Debito_banco;
use App\Models\Pago_CXP;
use App\Models\Parametrizacion_Contable;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use App\Models\Tipo_Movimiento_Banco;
use App\Models\Tipo_Movimiento_Caja;
use App\Models\Transferencia;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class pagosProveedoresController extends Controller
{
    public function nuevo()
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            return view('admin.cuentasPagar.pagosCXP.index',['movimientosBanco'=>[],'movimientos'=>[],'cajaAbierta'=>$cajaAbierta,'sucursales'=>Sucursal::sucursales()->get(),'cajas'=>Caja_Usuario::CajaXusuario(Auth::user()->user_id)->get(),'bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function proveedoresSucursalCXP(Request $request){
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            return view('admin.cuentasPagar.pagosCXP.index',['movimientosBanco'=>Tipo_Movimiento_Banco::tipoMovimientos()->where('sucursal_id','=',$request->get('sucursal_id'))->get(),'movimientos'=>Tipo_Movimiento_Caja::tipoMovimientos()->where('sucursal_id','=',$request->get('sucursal_id'))->get(),'cajaAbierta'=>$cajaAbierta,'sucurslaC'=>$request->get('sucursal_id'),'proveedores'=>Cuenta_Pagar::ProveedoresCXPSucursal($request->get('sucursal_id'))->select('proveedor.proveedor_id','proveedor.proveedor_nombre')->distinct()->get(),'sucursales'=>Sucursal::sucursales()->get(),'cajas'=>Caja_Usuario::CajaXusuario(Auth::user()->user_id)->get(),'bancos'=>Banco::bancos()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request){
        try{
            DB::beginTransaction();
            $urlcheque = '';
            $general = new generalController();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            $valPagoaux = $request->get('Ddescontar');
            for ($i = 0; $i < count($valPagoaux); ++$i){
                if($request->get('checkCXC'.$i)){
                    if($valPagoaux[$i] > 0){
                        $cxcAux2 = Cuenta_Pagar::cuenta($request->get('checkCXC'.$i))->first();
                        $cierre = $general->cierre($request->get('fechaPago'),$cxcAux2->sucursal_id);          
                        if($cierre){
                            return redirect('pagosCXP')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                        }
                    }
                }
            }
           
            $docPago = 0;
            $facturas = '';
            $cuentaPago = '';
            $tipoDoc = '';
            $nota = new Nota_Debito_banco();
            if($request->get('radioPago') == 'EFECTIVO'){
                $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                $cuentaPago = $cuentacaja->cuenta_id;
                $tipoDoc = 'EFECTIVO';
            }
            if($request->get('radioPago') == 'CHEQUE'){
                $docPago = $request->get('numCheque');
                $cuentaPago = Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_bancaria'))->first()->cuenta->cuenta_id;
                $tipoDoc = 'CHEQUE';
            }
            if($request->get('radioPago') == 'TRANSFERENCIA'){
                $docPago = $request->get('numDcoumento');
                $cuentaPago = Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_trans'))->first()->cuenta->cuenta_id;
                $tipoDoc = 'TRANSFERENCIA';
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
            if($request->get('radioPago') == 'NDB'){
                $docPago = 0;
                $cuentaPago = Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_nd'))->first()->cuenta->cuenta_id;
                $tipoDoc = 'NOTA DE DEBITO BANCARIA';
            }
            if($request->get('radioPago') == 'CHEQUE'){
                $formatter = new NumeroALetras();
                $cheque = new Cheque();
                $cheque->cheque_numero = $request->get('numCheque');
                $cheque->cheque_descripcion = 'PAGO DE CUENTAS POR PAGAR '.' - '.$facturas;
                $cheque->cheque_beneficiario = $request->get('idBeneficiario');
                $cheque->cheque_fecha_emision = $request->get('fechaPago');
                $cheque->cheque_fecha_pago = $request->get('fecha_cheque');
                $cheque->cheque_valor = $request->get('idValorSeleccionado');
                $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                $cheque->cuenta_bancaria_id = $request->get('cuenta_bancaria');
                $cheque->cheque_estado = '1';
                $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_bancaria'),$cheque);
                $cheque->save();
                $general->registrarAuditoria('Registro de cheque por pago a proveedor',$docPago,'Registro de cheque por pago a proveedor en '.$request->get('radioPago')); 
            }
            if($request->get('radioPago') == 'TRANSFERENCIA'){
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion = 'PAGO DE CUENTAS POR PAGAR '.' - '.$facturas;
                $transferencia->transferencia_beneficiario = $request->get('idBeneficiario');
                $transferencia->transferencia_fecha = $request->get('fechaPago');
                $transferencia->transferencia_valor = $request->get('idValorSeleccionado');
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_trans');
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de transferencia por pago a proveedor',$docPago,'Registro de transferencia por pago a proveedor en '.$request->get('radioPago')); 
            }
            $valPago = $request->get('Ddescontar');
            $sucursales = Cuenta_Pagar::ScucursalesxCXP($request->get('idProveedor'))->select('sucursal_id')->distinct('sucursal_id')->get();
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
                            $cxcAux = Cuenta_Pagar::cuenta($request->get('checkCXC'.$i))->first();
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
                    $numDocAux = '';
                    $tipDocAux = '';
                    if(is_null($datosSuc[$i]['cuentas']) == false){
                        if(count($datosSuc[$i]['cuentas'])>0){
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $diario = new Diario();
                               
                                $diario->diario_fecha = $request->get('fechaPago');
                                if($request->get('radioPago') == 'NDB'){
                                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaPago'),'CNDB');
                                    $diario->diario_referencia = 'COMPROBANTE DE NOTA DE DEBITO BANCARIA';
                                    $diario->diario_tipo_documento = 'NOTA DE DEBITO DE BANCO';
                                    $diario->diario_tipo = 'CNDB';
                                    $diario->diario_comentario = 'COMPROBANTE DE NOTA DE DEBITO BANCARIA: '.$request->get('idConcepto');
                                }else{
                                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechaPago'),'CEPP');
                                    $diario->diario_referencia = 'COMPROBANTE DE EGRESO DE PAGO A PROVEEDOR';
                                    $diario->diario_tipo_documento = 'PAGO EN '.$request->get('radioPago');
                                    $diario->diario_tipo = 'CEPP';
                                    $diario->diario_comentario = 'COMPROBANTE DE EGRESO DE PAGO A PROVEEDOR : '.$request->get('idNombre').' '.$request->get('idConcepto');
                                }
                                $diario->diario_numero_documento = $docPago;
                                $diario->diario_beneficiario = $request->get('idNombre');
                                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechaPago'))->format('m');
                                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechaPago'))->format('Y');
                                $diario->diario_cierre = '0';
                                $diario->diario_estado = '1';
                                $diario->empresa_id = Auth::user()->empresa_id;
                                $diario->sucursal_id = $datosSuc[$i]['sucursal_id'];
                                $diario->save();
                                $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
                                $diarios[$contDiarios] = $diario;
                                $contDiarios ++;
                            }
                            if($request->get('radioPago') == 'NDB'){
                                $puntosEmision = Punto_Emision::PuntoxSucursal($datosSuc[$i]['sucursal_id'])->get();
                                foreach($puntosEmision as $punto){
                                    $rangoDocumento=Rango_Documento::PuntoRango($punto->punto_id, 'Nota de Débito de Banco')->first();
                                    if($rangoDocumento){
                                        break;
                                    }
                                }
                                if($rangoDocumento){
                                    $secuencial=$rangoDocumento->rango_inicio;
                                    $secuencialAux=Nota_Debito_banco::secuencial($rangoDocumento->rango_id)->max('nota_secuencial');
                                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                                }else{
                                    throw new Exception('No tiene configurado, un punto de emisión o un rango de documentos para emitir nota de debito de banco, configueros y vuelva a intentar');
                                }
                                $nota->nota_numero = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                                $nota->nota_serie = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie;;
                                $nota->nota_secuencial = $secuencial;
                                $nota->nota_fecha = $request->get('fechaPago');
                                $nota->nota_valor = $datosSuc[$i]['valorSeleccion'];
                                $nota->nota_descripcion = $request->get('idConcepto');  
                                $nota->nota_beneficiario = $request->get('idNombre');
                                $nota->cuenta_bancaria_id= $request->get('cuenta_nd');
                                $nota->rango_id = $rangoDocumento->rango_id;
                                $nota->nota_estado = 1;        
                                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                    $nota->diario()->associate($diario);
                                }
                                $nota->save();
                                $general->registrarAuditoria('Registro de Nota de Debito de Banco -> '.$request->get('idNombre'),$diario->diario_codigo,'Con motivo: Pago de factura por modulo de pago cxp.');
                            }
                            $pago = new Pago_CXP();
                            $pago->pago_descripcion = $request->get('idConcepto');
                            $pago->pago_fecha = $request->get('fechaPago');
                            $pago->pago_tipo = $request->get('radioPago');
                            $pago->pago_valor = $datosSuc[$i]['valorSeleccion'];
                            $pago->pago_estado = '1';
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $pago->diario()->associate($diario);
                            }
                            if($request->get('radioPago') == 'EFECTIVO'){
                                if($arqueoCaja){
                                    $pago->arqueo_id = $arqueoCaja->arqueo_id;
                                }
                            }
                            $pago->save();
                            for ($c = 0; $c < count($datosSuc[$i]['cuentas']); ++$c){
                                $cxpAux = Cuenta_Pagar::cuenta($datosSuc[$i]['cuentas'][$c]['cuenta_id'])->first();
                                $detallePago = new Detalle_Pago_CXP();
                                if($cxpAux->transaccionCompra){
                                    $numDocAux = $cxpAux->transaccionCompra->transaccion_numero;
                                    $tipDocAux = $cxpAux->transaccionCompra->tipoComprobante->tipo_comprobante_nombre;
                                    $facturas = $facturas.' - '.$cxpAux->transaccionCompra->transaccion_numero;
                                    $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' DE '.$cxpAux->transaccionCompra->tipoComprobante->tipo_comprobante_nombre.' '.$cxpAux->transaccionCompra->transaccion_numero; 
                                    if ($request->get('radioPago') == 'CHEQUE') {
                                        $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' N° '.$request->get('numCheque').' DE '.$cxpAux->transaccionCompra->tipoComprobante->tipo_comprobante_nombre.' '.$cxpAux->transaccionCompra->transaccion_numero; 
                                    }
                                }
                                elseif($cxpAux->liquidacionCompra){
                                    $numDocAux = $cxpAux->liquidacionCompra->lc_numero;
                                    $tipDocAux = 'LIQUIDACIÓN DE COMPRA';
                                    $facturas = $facturas.' - '.$cxpAux->liquidacionCompra->lc_numero; 
                                    $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' DE LIQUIDACIÓN DE COMPRA '.$cxpAux->liquidacionCompra->lc_numero;
                                    if ($request->get('radioPago') == 'CHEQUE') {
                                        $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' N° '.$request->get('numCheque').' DE LIQUIDACIÓN DE COMPRA '.$cxpAux->liquidacionCompra->lc_numero; 
                                    } 
                                }else{
                                    $numDocAux = substr($cxpAux->cuenta_descripcion, 39); 
                                    $tipDocAux = 'CUENTA POR PAGAR';
                                    $facturas = $facturas.' - '.substr($cxpAux->cuenta_descripcion, 39); 
                                    $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' DE FACTURA '.substr($cxpAux->cuenta_descripcion, 39); 
                                    if ($request->get('radioPago') == 'CHEQUE') {
                                        $detallePago->detalle_pago_descripcion = 'PAGO EN '.$request->get('radioPago').' N° '.$request->get('numCheque').' DE FACTURA '.substr($cxpAux->cuenta_descripcion, 39); 
                                    }
                                   
                                   
                                }
                                $detallePago->detalle_pago_valor = $datosSuc[$i]['cuentas'][$c]['descontar']; 
                                $detallePago->detalle_pago_cuota = Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->count()+1; 
                                $detallePago->detalle_pago_estado = '1'; 
                                $detallePago->cuenta_pagar_id = $cxpAux->cuenta_id; 
                                $detallePago->pagoCXP()->associate($pago);
                                $detallePago->save();

                                if($cxpAux->transaccionCompra){
                                    $general->registrarAuditoria('Registro de detalle de pago de proveedor -> '.$request->get('idNombre'),'0','Detalle de pago de '.$cxpAux->transaccionCompra->tipoComprobante->tipo_comprobante_nombre.' No. '.$cxpAux->transaccionCompra->transaccion_numero.' pago en '.$request->get('radioPago')); 
                                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux->transaccionCompra->transaccion_id)->sum('descuento_valor'),2);
                                }elseif($cxpAux->liquidacionCompra){
                                    $general->registrarAuditoria('Registro de detalle de pago de proveedor -> '.$request->get('idNombre'),'0','Detalle de pago de Liquidacion de compra No. '.$cxpAux->liquidacionCompra->lc_numero.' pago en '.$request->get('radioPago')); 
                                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux->cuenta_id)->sum('detalle_pago_valor'),2);
                                }else{
                                    $general->registrarAuditoria('Registro de detalle de pago de proveedor -> '.$request->get('idNombre'),'0','Detalle de pago de Factura No. '.substr($cxpAux->cuenta_descripcion, 39).' pago en '.$request->get('radioPago')); 
                                    $cxpAux->cuenta_saldo = $cxpAux->cuenta_saldo - $detallePago->detalle_pago_valor;
                                }
                                if(round($cxpAux->cuenta_saldo,2) == 0){
                                    $cxpAux->cuenta_estado = '2';
                                    $cxpAux->cuenta_saldo = 0;
                                }else{
                                    $cxpAux->cuenta_estado = '1';
                                }
                                $cxpAux->update();
                                /*Inicio de registro de auditoria*/
                                if($cxpAux->transaccionCompra){
                                    $general->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor -> '.$request->get('idNombre'),'0','Actualizacion de cuenta por pagar de proveedor -> '.$request->get('idNombre').' con '.$cxpAux->transaccionCompra->tipoComprobante->tipo_comprobante_nombre.' -> '.$cxpAux->transaccionCompra->transaccion_numero);
                                }elseif($cxpAux->liquidacionCompra){
                                    $general->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor -> '.$request->get('idNombre'),'0','Actualizacion de cuenta por pagar de proveedor -> '.$request->get('idNombre').' con Liquidacion de compra -> '.$cxpAux->liquidacionCompra->lc_numero);
                                }else{
                                    $general->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor -> '.$request->get('idNombre'),'0','Actualizacion de cuenta por pagar de proveedor -> '.$request->get('idNombre').' con Factura -> '.substr($cxpAux->cuenta_descripcion, 39));
                                }
                                /*Fin de registro de auditoria*/ 
                                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                    /********************detalle de diario de pago a proveedor********************/
                                    $detalleDiario = new Detalle_Diario();
                                    $detalleDiario->detalle_debe = $datosSuc[$i]['cuentas'][$c]['descontar']; 
                                    $detalleDiario->detalle_haber = 0.00; 
                                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR A PROVEEDOR';
                                    $detalleDiario->detalle_tipo_documento = $tipDocAux;
                                    $detalleDiario->detalle_numero_documento = $numDocAux;
                                    $detalleDiario->detalle_conciliacion = '0';
                                    $detalleDiario->detalle_estado = '1';
                                    $detalleDiario->proveedor_id = $request->get('idProveedor');
                                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                                    }else{
                                        $parametrizacionContable = Proveedor::findOrFail($request->get('idProveedor'));
                                        $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                                    }
                                    $diario->detalles()->save($detalleDiario);
                                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$docPago,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el debe por un valor de -> '.$datosSuc[$i]['cuentas'][$c]['descontar']);
                                    /***************************************************************************/
                                }
                            }
                            $general->registrarAuditoria('Registro de pago de proveedor -> '.$request->get('idNombre'),$docPago,'Pago de documentos No. '.$facturas.' en '.$request->get('radioPago')); 
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                if($request->get('radioPago') == 'NDB'){
                                    $diario->diario_comentario = 'COMPROBANTE DE NOTA DE DEBITO BANCARIA A PROVEEDOR : '.$request->get('idNombre').' - '.$facturas.' '.$request->get('idConcepto');
                                }else{
                                    $diario->diario_comentario = 'COMPROBANTE DE EGRESO DE PAGO A PROVEEDOR : '.$request->get('idNombre').' - '.$facturas.' '.$request->get('idConcepto');
                                }
                                $diario->update();
                                /********************detalle de diario de pago a proveedor********************/
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = $datosSuc[$i]['valorSeleccion'];
                                if($request->get('radioPago') == 'NDB'){
                                    $detalleDiario->detalle_comentario = 'P/R PAGO DE CUENTA POR PAGAR CON NOTA DE DEBITO BANCARIA';
                                }else{
                                    $detalleDiario->detalle_comentario = 'P/R PAGO EN '.$request->get('radioPago').' DE CUENTA POR PAGAR';
                                }
                                $detalleDiario->detalle_tipo_documento = $tipoDoc;
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuentaPago;
                                if($request->get('radioPago') == 'CHEQUE'){
                                    $detalleDiario->cheque()->associate($cheque);
                                }
                                if($request->get('radioPago') == 'TRANSFERENCIA'){
                                    $detalleDiario->transferencia()->associate($transferencia);
                                }
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$docPago,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.Cuenta::cuenta($cuentaPago)->first()->cuenta_numero.' en el debe por un valor de -> '.$datosSuc[$i]['valorSeleccion']);
                                /***************************************************************************/
                            }
                        }
                    }
                }
            }
            if($request->get('radioPago') == 'CHEQUE'){
                $cheque->cheque_descripcion = 'PAGO DE CUENTAS POR PAGAR '.' - '.$facturas;
                $cheque->update();
            }
            if($request->get('radioPago') == 'TRANSFERENCIA'){
                $transferencia->transferencia_descripcion = 'PAGO DE CUENTAS POR PAGAR '.' - '.$facturas;
                $transferencia->update();
            }
            if($request->get('radioPago') == 'EFECTIVO'){
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="SALIDA";
                $movimientoCaja->movimiento_descripcion= 'P/R PAGO A PROVEEDOR :'.$request->get('idNombre');
                $movimientoCaja->movimiento_valor= $request->get('idValorSeleccionado');
                $movimientoCaja->movimiento_documento="PAGO DE PROVEEDOR EN EFECTIVO";
                $movimientoCaja->movimiento_numero_documento= 0;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                    $movimientoCaja->diario()->associate($diario);
                }
                $movimientoCaja->save();
                /*********************************************************************/
            }
            $url ='';
            if(Auth::user()->empresa->empresa_llevaContabilidad == '1'){
                $url = $general->pdfVariosDiario($diarios, $request->get('fechaPago'));
            }
            if ($request->get('radioPago') == 'CHEQUE') {
                DB::commit();
                return redirect('pagosCXP')->with('success','Pago realizado exitosamente')->with('diario',$url)->with('cheque',$urlcheque);;
            }
            DB::commit();
            return redirect('pagosCXP')->with('success','Pago realizado exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('pagosCXP')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function nuevoEliminarPago(){
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasPagar.eliminarPagos.index',['proveedores'=>Proveedor::proveedores()->get(),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
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
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $todo = 0;
            $count = 1;
            $datos = null;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            foreach(Cuenta_Pagar::CuentasPagarByPagos($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('proveedorID'),$todo,$request->get('sucursal_id'))->select('cuenta_pagar.cuenta_id','cuenta_pagar.cuenta_fecha','cuenta_pagar.cuenta_monto','cuenta_pagar.cuenta_saldo','cuenta_pagar.cuenta_descripcion')->distinct('cuenta_pagar.cuenta_fecha','cuenta_pagar.cuenta_id')->get() as $cuenta){
                $datos[$count]['cod'] = $cuenta->cuenta_id;
                $datos[$count]['doc'] = ''; 
                $datos[$count]['num'] = ''; 
                $datos[$count]['dia'] = ''; 
                if($cuenta->transaccionCompra){
                    $datos[$count]['doc'] = $cuenta->transaccionCompra->tipoComprobante->tipo_comprobante_nombre; 
                    $datos[$count]['num'] = $cuenta->transaccionCompra->transaccion_numero;
                    $datos[$count]['dia'] = $cuenta->transaccionCompra->diario->diario_codigo; 
                }
                if($cuenta->liquidacionCompra){
                    $datos[$count]['doc'] = 'Liquidacion de Compra'; 
                    $datos[$count]['num'] = $cuenta->liquidacionCompra->lc_numero;
                    $datos[$count]['dia'] = $cuenta->liquidacionCompra->diario->diario_codigo; 
                }
                if($cuenta->ingresoBodega){
                    $datos[$count]['doc'] = 'Ingreso de Bodega'; 
                    $datos[$count]['num'] = $cuenta->ingresoBodega->cabecera_ingreso_numero;  
                    $datos[$count]['dia'] = $cuenta->ingresoBodega->diario->diario_codigo; 
                }
                if($datos[$count]['doc'] == ''){
                    $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 39);
                    $datos[$count]['doc'] = 'FACTURA'; 
                }
                $datos[$count]['fec'] = DateTime::createFromFormat('Y-m-d', $cuenta->cuenta_fecha)->format('d/m/Y');
                $datos[$count]['mon'] = $cuenta->cuenta_monto; 
                $datos[$count]['sal'] = $cuenta->cuenta_saldo; 
                $datos[$count]['val'] = ''; 
                $datos[$count]['fep'] = ''; 
                $datos[$count]['dip'] = ''; 
                $datos[$count]['ref'] = ''; 
                $datos[$count]['che'] = '0';
                $datos[$count]['tot'] = '1';
                $count ++;
                foreach(Detalle_Pago_CXP::CuentaPagarPagosFecha($cuenta->cuenta_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo)->orderBy('pago_fecha')->get() as $pago){
                    $datos[$count]['cod'] = $pago->detalle_pago_id;
                    $datos[$count]['doc'] = ''; 
                    $datos[$count]['num'] = ''; 
                    $datos[$count]['dia'] = '';                
                    $datos[$count]['fec'] = '';
                    $datos[$count]['mon'] = ''; 
                    $datos[$count]['sal'] = ''; 
                    $datos[$count]['val'] = $pago->detalle_pago_valor; 
                    $datos[$count]['fep'] = DateTime::createFromFormat('Y-m-d', $pago->pagoCXP->pago_fecha)->format('d/m/Y');  
                    $datos[$count]['dip'] = $pago->pagoCXP->diario->diario_codigo; 
                    $datos[$count]['ref'] = $pago->detalle_pago_descripcion; 
                    $datos[$count]['che'] = '0';
                    foreach($pago->pagoCXP->diario->detalles as $detalle){
                        if(isset($detalle->cheque->cheque_id)){
                            $datos[$count]['che'] = 'Cheque '.$detalle->cheque->cheque_numero.' del banco '.$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre.' de la cuenta '.$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                        }
                    }
                    $datos[$count]['chk'] = '1';
                    if($pago->pagoCXP->pago_tipo == 'NOTA DE CRéDITO'){
                        $datos[$count]['chk'] = '0';
                    }
                    $datos[$count]['tot'] = '2';
                    $count ++;
                }
                if(isset($cuenta->transaccionCompra->transaccion_id)){
                    foreach(Descuento_Anticipo_Proveedor::AnticiposPagosFecha($cuenta->transaccionCompra->transaccion_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo)->orderBy('descuento_fecha')->get() as $pago){
                        $datos[$count]['cod'] = $pago->descuento_id;
                        $datos[$count]['doc'] = ''; 
                        $datos[$count]['num'] = ''; 
                        $datos[$count]['dia'] = '';                
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = ''; 
                        $datos[$count]['sal'] = ''; 
                        $datos[$count]['val'] = $pago->descuento_valor; 
                        $datos[$count]['fep'] = DateTime::createFromFormat('Y-m-d', $pago->descuento_fecha)->format('d/m/Y');  
                        $datos[$count]['dip'] = $pago->diario->diario_codigo; 
                        $datos[$count]['ref'] = 'CRUCE CON ANTICIPO DE PROVEEDOR No '.$pago->anticipo->anticipo_numero; 
                        $datos[$count]['che'] = '0';
                        $datos[$count]['chk'] = '0';
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                }
            }
            return view('admin.cuentasPagar.eliminarPagos.index',['datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'proveedores'=>Proveedor::proveedores()->get(),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]); 
        }catch(\Exception $ex){
            return redirect('eliminarPagoCXP')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminarPagos(Request $request){
        try {
            DB::beginTransaction();
            $jo=false;
            $auditoria = new generalController();
            $noTienecaja =null;
            $seleccion = $request->get('checkbox');
            for ($i = 0; $i < count($seleccion); ++$i) {
                $detalle_pago = Detalle_Pago_CXP::DetallePagoCXP($seleccion[$i])->first();
                $cierre = $auditoria->cierre($detalle_pago->pagoCXP->pago_fecha,$detalle_pago->cuentaPagar->sucursal_id);
                if ($cierre) {
                    return redirect('eliminarPagoCXP')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
            }            
            for ($i = 0; $i < count($seleccion); ++$i) {
                $detalle_pago = Detalle_Pago_CXP::DetallePagoCXP($seleccion[$i])->first();
                if(isset($detalle_pago->detalle_pago_id)){
                    $cxpAux = $detalle_pago->cuentaPagar;
                    $valorPagoGeneral = $detalle_pago->detalle_pago_valor;
                    $pago = $detalle_pago->pagoCXP;
                    $pagos =Pago_CXP::findOrFail($detalle_pago->pago_id);
                    $pagos =$pagos->detalles;
                  
                    $diario = null;
                    $jo=false;
                    if(isset($pago->diario)){
                        $diario = $pago->diario;
                        if($pago->pago_tipo == 'EFECTIVO'){
                            $cajaAbierta=Arqueo_Caja::ArqueoCajaxid($pago->arqueo_id)->first();
                            if(isset($cajaAbierta->arqueo_id)){
                                $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($pago->arqueo_id, $pago->diario_id)->first();
                                if($movimientoCaja){
                                    $movimientoCaja->delete(); 
                                }

                                $jo=true;
                            }else{                            
                                $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                                if ($cajaAbierta){
                                    /**********************movimiento caja****************************/
                                    $movimientoCaja = new Movimiento_Caja();          
                                    $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                    $movimientoCaja->movimiento_hora=date("H:i:s");
                                    $movimientoCaja->movimiento_tipo="ENTRADA";
                                    $movimientoCaja->movimiento_descripcion= 'P/R ELIMINACION DE PAGO DE PROVEEDOR :'.$pago->pago_descripcion;
                                    $movimientoCaja->movimiento_valor= $pago->pago_valor;
                                    $movimientoCaja->movimiento_documento="P/R ELIMINACION DE PAGO EN EFECTIVO";
                                    $movimientoCaja->movimiento_numero_documento= 0;
                                    $movimientoCaja->movimiento_estado = 1;
                                    $movimientoCaja->arqueo_id = $cajaAbierta->arqueo_id;                                
                                    $movimientoCaja->save();
                                    
                                    $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($pago->arqueo_id,$pago->diario_id)->first();
                                    $movimientoAnterior->diario_id = null;
                                    $movimientoAnterior->update();

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
                            if($pago->pago_tipo <> 'NDB'){
                                foreach($diario->detalles as $detalle){
                                    if(isset($detalle->cheque)){
                                        foreach($detalle->cheque->detalleDiario as $detalleCheque){
                                            if(isset($detalleCheque->diario)){
                                                $pago2 = $detalleCheque->diario->pagocuentapagar;
                                                $diario2 = $detalleCheque->diario;
                                                $bandera2=false;
                                                if($pago2->pago_tipo == 'EFECTIVO'){
                                                    $cajaAbierta2=Arqueo_Caja::ArqueoCajaxid($pago2->arqueo_id)->first();
                                                    if(isset($cajaAbierta2->arqueo_id)){
                                                        $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($pago2->arqueo_id, $pago2->diario_id)->first();
                                                        $movimientoCaja->delete();
                                                        $bandera2=true;
                                                    }else{                            
                                                        $cajaAbierta2=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                                                        if ($cajaAbierta2){
                                                            /**********************movimiento caja****************************/
                                                            $movimientoCaja = new Movimiento_Caja();          
                                                            $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                                            $movimientoCaja->movimiento_hora=date("H:i:s");
                                                            $movimientoCaja->movimiento_tipo="ENTRADA";
                                                            $movimientoCaja->movimiento_descripcion= 'P/R ELIMINACION DE PAGO DE PROVEEDOR :'.$pago2->pago_descripcion;
                                                            $movimientoCaja->movimiento_valor= $pago2->pago_valor;
                                                            $movimientoCaja->movimiento_documento="P/R ELIMINACION DE PAGO EN EFECTIVO";
                                                            $movimientoCaja->movimiento_numero_documento= 0;
                                                            $movimientoCaja->movimiento_estado = 1;
                                                            $movimientoCaja->arqueo_id = $cajaAbierta2->arqueo_id;                                
                                                            $movimientoCaja->save();
                                                            
                                                            $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($pago2->arqueo_id,$pago2->diario_id)->first();
                                                            $movimientoAnterior->diario_id = null;
                                                            $movimientoAnterior->update();
                            
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
                                                        if(isset($detallePago->cuentaPagar->cuenta_id)){
                                                            if(isset(Detalle_Pago_CXP::DetallePagoCXP($detallePago->detalle_pago_id)->first()->detalle_pago_id)){
                                                                $cxpAux2 = $detallePago->cuentaPagar;
                                                                $valorPago = $detallePago->detalle_pago_valor;
                                                                $detallePago->delete();
                                                                $auditoria->registrarAuditoria('Eliminacion del detalle de pago cuentas por pagar  '.$detallePago->cuentaPagar->cuenta_descripcion,'','');  
                                                                if(isset($cxpAux2->transaccionCompra->transaccion_id)){
                                                                    $cxpAux2->cuenta_saldo = $cxpAux2->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux2->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux2->transaccionCompra->transaccion_id)->sum('descuento_valor'),2);
                                                                }elseif(isset($cxpAux2->liquidacionCompra->lc_id)){
                                                                    $cxpAux2->cuenta_saldo = $cxpAux2->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux2->cuenta_id)->sum('detalle_pago_valor'),2);
                                                                }elseif(isset($cxpAux2->ingresoBodega->cabecera_ingreso_id)){
                                                                    $cxpAux2->cuenta_saldo = $cxpAux2->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux2->cuenta_id)->sum('detalle_pago_valor'),2);
                                                                }else{
                                                                    $cxpAux2->cuenta_saldo = $cxpAux2->cuenta_saldo + $valorPago;
                                                                }
                                                                if(round($cxpAux2->cuenta_saldo,2) == 0){
                                                                    $cxpAux2->cuenta_estado = '2';
                                                                    $cxpAux2->cuenta_saldo = 0;
                                                                }else{
                                                                    $cxpAux2->cuenta_estado = '1';
                                                                }
                                                                $cxpAux2->update();
                                                                if(isset($cxpAux2->transaccionCompra->transaccion_id)){
                                                                    $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux2->transaccionCompra->proveedor->proveedor_nombre.' con '.$cxpAux2->transaccionCompra->tipoComprobante->tipo_comprobante_nombre.' -> '.$cxpAux2->transaccionCompra->transaccion_numero);
                                                                }elseif(isset($cxpAux2->liquidacionCompra->lc_id)){
                                                                    $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux2->liquidacionCompra->proveedor->proveedor_nombre.' con Liquidacion de compra -> '.$cxpAux2->liquidacionCompra->lc_numero);
                                                                }elseif(isset($cxpAux2->ingresoBodega->cabecera_ingreso_id)){
                                                                    $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux2->ingresoBodega->proveedor->proveedor_nombre.' con Ingreso de Bodega -> '.$cxpAux2->ingresoBodega->cabecera_ingreso_numero);
                                                                }else{
                                                                    $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux2->proveedor->proveedor_nombre.' '.$cxpAux2->cuenta_descripcion);
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $pago2->delete();
                                                    foreach($diario2->detalles as $detalleDiario2){
                                                        $detalleDiario2->delete();
                                                        $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario2->diario_codigo,$diario2->diario_codigo,'Eliminacion de detalle de diario por eliminacion de pago de cuentas por pagar');  
                                                    }
                                                    $diario2->delete();
                                                    $auditoria->registrarAuditoria('Eliminacion de diario  N°'.$diario2->diario_codigo,$diario2->diario_codigo,'Eliminacion de diario por eliminacion de pago de cuentas por pagar');  
                                                }
                                            }
                                        }   
                                        if($bandera2){
                                            if($request->get('anularChequeID') == 'no'){
                                                $detalle->cheque->delete();
                                            }else{
                                                $che = $detalle->cheque;
                                                $che->cheque_estado = '2';
                                                $che->update();
                                            }
                                            
                                        }
                                    }
                                    if(isset($detalle->transferencia)){
                                        foreach($detalle->transferencia->detalleDiario as $detalleTransferencia){
                                            if(isset($detalleTransferencia->diario)){
                                                $pago2 = $detalleTransferencia->diario->pagocuentapagar;
                                                $diario2 = $detalleTransferencia->diario;
                                                $bandera2=false;
                                                if($pago2->pago_tipo == 'EFECTIVO'){
                                                    $cajaAbierta2=Arqueo_Caja::ArqueoCajaxid($pago2->arqueo_id)->first();
                                                    if(isset($cajaAbierta2->arqueo_id)){
                                                        $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($pago2->arqueo_id, $pago2->diario_id)->first();
                                                        $movimientoCaja->delete();
                                                        $bandera2=true;
                                                    }else{                            
                                                        $cajaAbierta2=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                                                        if ($cajaAbierta2){
                                                            /**********************movimiento caja****************************/
                                                            $movimientoCaja = new Movimiento_Caja();          
                                                            $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                                            $movimientoCaja->movimiento_hora=date("H:i:s");
                                                            $movimientoCaja->movimiento_tipo="ENTRADA";
                                                            $movimientoCaja->movimiento_descripcion= 'P/R ELIMINACION DE PAGO DE PROVEEDOR :'.$pago2->pago_descripcion;
                                                            $movimientoCaja->movimiento_valor= $pago2->pago_valor;
                                                            $movimientoCaja->movimiento_documento="P/R ELIMINACION DE PAGO EN EFECTIVO";
                                                            $movimientoCaja->movimiento_numero_documento= 0;
                                                            $movimientoCaja->movimiento_estado = 1;
                                                            $movimientoCaja->arqueo_id = $cajaAbierta2->arqueo_id;                                
                                                            $movimientoCaja->save();
                                                            
                                                            $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($pago2->arqueo_id,$pago2->diario_id)->first();
                                                            $movimientoAnterior->diario_id = null;
                                                            $movimientoAnterior->update();
                            
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
                                                        if(isset($detallePago->cuentaPagar->cuenta_id)){
                                                            if(isset(Detalle_Pago_CXP::DetallePagoCXP($detallePago->detalle_pago_id)->first()->detalle_pago_id)){
                                                                $cxpAux2 = $detallePago->cuentaPagar;
                                                                $valorPago = $detallePago->detalle_pago_valor;
                                                                $detallePago->delete();
                                                                $auditoria->registrarAuditoria('Eliminacion del detalle de pago cuentas por pagar  '.$detallePago->cuentaPagar->cuenta_descripcion,'','');  
                                                                if(isset($cxpAux2->transaccionCompra->transaccion_id)){
                                                                    $cxpAux2->cuenta_saldo = $cxpAux2->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux2->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxpAux2->transaccionCompra->transaccion_id)->sum('descuento_valor'),2) ;
                                                                }elseif(isset($cxpAux2->liquidacionCompra->lc_id)){
                                                                    $cxpAux2->cuenta_saldo = $cxpAux2->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux2->cuenta_id)->sum('detalle_pago_valor'),2) ;
                                                                }elseif(isset($cxpAux2->ingresoBodega->cabecera_ingreso_id)){
                                                                    $cxpAux2->cuenta_saldo = $cxpAux2->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cxpAux2->cuenta_id)->sum('detalle_pago_valor'),2) ;
                                                                }else{
                                                                    $cxpAux2->cuenta_saldo = $cxpAux2->cuenta_saldo + $valorPago;
                                                                }
                                                                if(round($cxpAux2->cuenta_saldo,2) == 0){
                                                                    $cxpAux2->cuenta_estado = '2';
                                                                    $cxpAux2->cuenta_saldo = 0;
                                                                }else{
                                                                    $cxpAux2->cuenta_estado = '1';
                                                                }
                                                                $cxpAux2->update();
                                                                if(isset($cxpAux2->transaccionCompra->transaccion_id)){
                                                                    $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux2->transaccionCompra->proveedor->proveedor_nombre.' con '.$cxpAux2->transaccionCompra->tipoComprobante->tipo_comprobante_nombre.' -> '.$cxpAux2->transaccionCompra->transaccion_numero);
                                                                }elseif(isset($cxpAux2->liquidacionCompra->lc_id)){
                                                                    $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux2->liquidacionCompra->proveedor->proveedor_nombre.' con Liquidacion de compra -> '.$cxpAux2->liquidacionCompra->lc_numero);
                                                                }elseif(isset($cxpAux2->ingresoBodega->cabecera_ingreso_id)){
                                                                    $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux2->ingresoBodega->proveedor->proveedor_nombre.' con Ingreso de Bodega -> '.$cxpAux2->ingresoBodega->cabecera_ingreso_numero);
                                                                }else{
                                                                    $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux2->proveedor->proveedor_nombre.' '.$cxpAux2->cuenta_descripcion);
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $pago2->delete();
                                                    foreach($diario2->detalles as $detalleDiario2){
                                                        $detalleDiario2->delete();
                                                        $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario2->diario_codigo,$diario2->diario_codigo,'Eliminacion de detalle de diario por eliminacion de pago de cuentas por pagar');  
                                                    }
                                                    $diario2->delete();
                                                    $auditoria->registrarAuditoria('Eliminacion de diario  N°'.$diario2->diario_codigo,$diario2->diario_codigo,'Eliminacion de diario por eliminacion de pago de cuentas por pagar');  
                                                }
                                            }
                                        }   
                                        if($bandera2){
                                            $detalle->transferencia->delete();
                                        }
                                    }
                                }
                            }
                        }
                    }
                
                    if(isset(Detalle_Pago_CXP::DetallePagoCXP($detalle_pago->detalle_pago_id)->first()->detalle_pago_id)){
                        if($jo){
                            if($pago->pago_tipo == 'NDB'){
                                $notaDebitoBanco = Nota_Debito_banco::NotaDebitoBancoByDiario($diario->diario_id)->first();
                                $notaDebitoBanco->delete();
                                $auditoria->registrarAuditoria('Eliminacion del nota de debito bancaria de pago cuentas por pagar con diario '.$diario->diario_codigo,'','');  
                            }
                            foreach ($pago->detalles as $detalle) {
                                $detalle->delete();
                                $auditoria->registrarAuditoria('Eliminacion del detalle de pago cuentas por pagar  '.$detalle->cuentaPagar->cuenta_descripcion,'','');  
                            }
                            $pago->delete();
                            $auditoria->registrarAuditoria('Eliminacion de pago de cuentas por pagar  '.$pago->pago_descripcion,'','');  
                            if(!is_null($diario)){
                                foreach($diario->detalles as $detalle){
                                    if(isset($detalle->cheque)){
                                        $cheque1 = $detalle->cheque;
                                        $auditoria->registrarAuditoria('Eliminacion de cheque','','Eliminacion de cheque numero '.$detalle->cheque->cheque_numero.' por un valor de '.$detalle->cheque->cheque_valor.' por eliminacion de pago de cuenta por pagar');  
                                    }
                                    if(isset($detalle->transferencia)){
                                        $transferencia1 = $detalle->transferencia;
                                        $auditoria->registrarAuditoria('Eliminacion de transferencia a proveedor '.$detalle->transferencia->transferencia_beneficiario,'','Eliminacion de transferencia a proveedor '.$detalle->transferencia->transferencia_beneficiario.' por un valor de '.$detalle->transferencia->transferencia_valor.' por eliminacion de pago de cuenta por pagar');  
                                    }
                                    $detalle->delete();
                                    $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de detalle de diario por eliminacion de pago de cuentas por pagar');  
                                }
                                $diario->delete();
                                if(isset($cheque1)){
                                    if($request->get('anularChequeID') == 'no'){
                                        $cheque1->delete();
                                    }else{
                                        $cheque1->cheque_estado = '2';
                                        $cheque1->update();
                                    }
                                    
                                }
                                if(isset($transferencia1)){
                                    $transferencia1->delete();
                                }
                                $auditoria->registrarAuditoria('Eliminacion de diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de diario por eliminacion de pago de cuentas por pagar');  
                            }
                           
                            foreach ($pagos as $detallea) {
                                $cuentaaux=Cuenta_Pagar::findOrFail($detallea->cuenta_pagar_id);
                                             
                                if (isset($cuentaaux->transaccionCompra->transaccion_id)) {
                                    $cuentaaux->cuenta_saldo = $cuentaaux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cuentaaux->cuenta_id)->sum('detalle_pago_valor'), 2) - round(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cuentaaux->transaccionCompra->transaccion_id)->sum('descuento_valor'), 2);
                                    
                                } elseif (isset($cuentaaux->liquidacionCompra->lc_id)) {
                                    $cuentaaux->cuenta_saldo = $cuentaaux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cuentaaux->cuenta_id)->sum('detalle_pago_valor'), 2);
                                } elseif (isset($cuentaaux->ingresoBodega->cabecera_ingreso_id)) {
                                    $cuentaaux->cuenta_saldo = $cuentaaux->cuenta_monto - round(Cuenta_Pagar::CuentaPagarPagos($cuentaaux->cuenta_id)->sum('detalle_pago_valor'), 2);
                                } else {
                                    $cuentaaux->cuenta_saldo = $cuentaaux->cuenta_saldo + $valorPagoGeneral;
                                }
                               
                                if ($cuentaaux->cuenta_saldo == 0) {
                                    $cuentaaux->cuenta_estado = '2';
                                    $cuentaaux->cuenta_saldo = 0;
                                } else {
                                    $cuentaaux->cuenta_estado = '1';
                                }
                                $cuentaaux->update();
                            }
                           
                            if(isset($cxpAux->transaccionCompra->transaccion_id)){
                                $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux->transaccionCompra->proveedor->proveedor_nombre.' con '.$cxpAux->transaccionCompra->tipoComprobante->tipo_comprobante_nombre.' -> '.$cxpAux->transaccionCompra->transaccion_numero);
                            }elseif(isset($cxpAux->liquidacionCompra->lc_id)){
                                $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux->liquidacionCompra->proveedor->proveedor_nombre.' con Liquidacion de compra -> '.$cxpAux->liquidacionCompra->lc_numero);
                            }elseif(isset($cxpAux->ingresoBodega->cabecera_ingreso_id)){
                                $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux->ingresoBodega->proveedor->proveedor_nombre.' con Ingreso de Bodega -> '.$cxpAux->ingresoBodega->cabecera_ingreso_numero);
                            }else{
                                $auditoria->registrarAuditoria('Actualizacion de cuenta por pagar de proveedor','0','Actualizacion de cuenta por pagar de proveedor -> '.$cxpAux->proveedor->proveedor_nombre.' '.$cxpAux->cuenta_descripcion);
                            }
                        }
                    }
                }
            }
            DB::commit();
            if(isset($noTienecaja)){
                return redirect('eliminarPagoCXP')->with('success','Datos eliminados exitosamente')->with('error2',$noTienecaja);
            }else{
                return redirect('eliminarPagoCXP')->with('success','Datos eliminados exitosamente');
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('eliminarPagoCXP')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }catch(\TypeError $ex){
            DB::rollBack();
            return redirect('eliminarPagoCXP')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
