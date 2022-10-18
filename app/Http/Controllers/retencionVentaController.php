<?php

namespace App\Http\Controllers;

use App\Models\Anticipo_Cliente;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Concepto_Retencion;
use App\Models\Cuenta_Cobrar;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Pago_CXC;
use App\Models\Detalle_RV;
use App\Models\Diario;
use App\Models\Factura_Venta;
use App\Models\Nota_Debito;
use App\Models\Pago_CXC;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Retencion_Venta;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class retencionVentaController extends Controller
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
            return view('admin.ventas.retencionRecibida.nuevo',['bodegas'=>Bodega::Bodegas()->get(),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        if (isset($_POST['guardarID'])){
            return $this->guardar($request);
        }
        if (isset($_POST['eliminarID'])){
            return $this->eliminar($request);
        }
    }
    public function eliminar(Request $request){
        try{            
            DB::beginTransaction();
            $general = new generalController();
            $facturaAux = Factura_Venta::Factura($request->get('factura_id'))->first(); 
            $cierre = $general->cierre($facturaAux->retencion->retencion_fecha,$facturaAux->retencion->diario->sucursal_id);          
            if($cierre){
                return redirect('retencionVenta')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cxcAux = $facturaAux->cuentaCobrar;
            $retencion  = $facturaAux->retencion;
            $diario = $retencion->diario;
            $pagoCXC = Pago_CXC::PagoDiario($retencion->diario->diario_id)->first();
            if(isset($pagoCXC->pago_id)){
                foreach($pagoCXC->detalles as $detalle){
                    $detalle->delete();
                    $general->registrarAuditoria('Eliminacion de detalle de pago por eliminacion de retencion de Cliente -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Con motivo: eliminacion de retencion de venta recibida');
                }
                $pagoCXC->delete();
                $general->registrarAuditoria('Eliminacion de pago por eliminacion de retencion de Cliente -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Con motivo: eliminacion de retencion de venta recibida');
            }
            $anticipo = Anticipo_Cliente::AnticipoDiario($retencion->diario->diario_id)->first();
            if(isset($anticipo->anticipo_id)){
                if($anticipo->anticipo_valor == $anticipo->anticipo_saldo){
                    $anticipo->delete();
                    $general->registrarAuditoria('Eliminacion de anticipo por eliminacion de retencion de Cliente -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Con motivo: eliminacion de retencion de venta recibida');
                }else{
                    throw new Exception('Esta retencion genero un anticipo, y el anticipo ya ha sido cruzado, elimine el cruce del anticipo para luego eliminar la retencion de venta recibida.');
                }
            }
            $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($facturaAux->factura_id)->sum('descuento_valor'),2);
            if($cxcAux->cuenta_saldo == 0){
                $cxcAux->cuenta_estado = '2';
            }else{
                $cxcAux->cuenta_estado = '1';
            }
            $cxcAux->update();
            $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),$retencion->retencion_numero,'Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$facturaAux->factura_numero.' por motivo de eliminacion de retencion de venta.');
            foreach($retencion->detalles as $detalle){
                $detalle->delete();
                $general->registrarAuditoria('Eliminacion de detalle de retencion de Cliente -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Con motivo: eliminacion de retencion de venta recibida');
            }
            $retencion->delete();
            $general->registrarAuditoria('Eliminacion de retencion de Cliente -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Con motivo: eliminacion de retencion de venta recibida');
            foreach($diario->detalles as $detalle){
                $detalle->delete();
                $general->registrarAuditoria('Eliminacion de detalle de diario de retencion de Cliente -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Con motivo: eliminacion de retencion de venta recibida');
            }
            $diario->delete();
            $general->registrarAuditoria('Eliminacion de diario de retencion de Cliente -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Con motivo: eliminacion de retencion de venta recibida');
            DB::commit(); 
            return redirect('/retencionVenta')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/retencionVenta')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request){
        try{            
            DB::beginTransaction();
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
            $general = new generalController();
            if($request->get('tipo_doc') == '0'){
                $facturaAux = Factura_Venta::Factura($request->get('factura_id'))->first(); 
                $cxcAux = $facturaAux->cuentaCobrar;
            }else{
                $ndAux = Nota_Debito::NotaDebito($request->get('factura_id'))->first(); 
                $cxcAux = $ndAux->cuentaCobrar;
            }            
            $cierre = $general->cierre($request->get('retencion_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('retencionVenta')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            /***********************Retencion de Venta********************/
            $retencion = new Retencion_Venta();
            $retencion->retencion_fecha = $request->get('retencion_fecha');
            $retencion->retencion_emision = $request->get('tipoDoc');
            $retencion->retencion_numero = $request->get('retencion_serie').substr(str_repeat(0, 9).$request->get('retencion_secuencial'), - 9);
            $retencion->retencion_serie = $request->get('retencion_serie');
            $retencion->retencion_secuencial = $request->get('retencion_secuencial');
            $retencion->retencion_estado = '1';
            if($request->get('tipo_doc') == '0'){
                $retencion->factura_id = $request->get('factura_id');
            }else{
                $retencion->nd_id = $request->get('factura_id');
            }
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('retencion_fecha'),'CRER');
                $diario->diario_tipo = 'CRER';
                $diario->diario_fecha = $request->get('retencion_fecha');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE RETENCIÓN DE VENTA';
                $diario->diario_tipo_documento = 'COMPROBANTE DE RETENCION';
                $diario->diario_numero_documento = $retencion->retencion_numero;
                if($request->get('tipo_doc') == '0'){
                    $diario->diario_beneficiario = $facturaAux->cliente->cliente_nombre;
                }else{
                    $diario->diario_beneficiario = $ndAux->factura->cliente->cliente_nombre;
                }
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('retencion_fecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('retencion_fecha'))->format('Y');
                if($request->get('tipo_doc') == '0'){
                    $diario->diario_comentario = 'COMPROBANTE DIARIO DE RETENCIÓN DE VENTA : '.$retencion->retencion_numero.' CON NUMERO DE FACTURA : '.$facturaAux->factura_numero;
                }else{
                    $diario->diario_comentario = 'COMPROBANTE DIARIO DE RETENCIÓN DE VENTA : '.$retencion->retencion_numero.' CON NUMERO DE NOTA DE DEBITO : '.$ndAux->nd_numero;
                }
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de comprobante de retención de venta -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de diario de comprobante de retención de venta -> '.$retencion->retencion_numero.' con numero de factura -> '.$request->get('buscarFactura').' y con codigo de diario -> '.$diario->diario_codigo);
                /****************************************************************/
            $retencion->diario()->associate($diario);
            $retencion->save();
            $general->registrarAuditoria('Registro de retencion de venta numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de retencion de venta numero -> '.$retencion->retencion_numero.' y con codigo de diario -> '.$diario->diario_codigo);
            /******************************************************************/
            /********************Detalle retencion de venta*******************/
            for ($i = 1; $i < count($baseF); ++$i){
                $detalleRV = new Detalle_RV();
                $detalleRV->detalle_tipo = 'FUENTE';
                $detalleRV->detalle_base = $baseF[$i];
                $detalleRV->detalle_porcentaje = $porcentajeF[$i];
                $detalleRV->detalle_valor = $valorF[$i];
                $detalleRV->detalle_asumida = '0';
                $detalleRV->detalle_estado = '1';
                $detalleRV->concepto_id = $idRetF[$i];
                $retencion->detalles()->save($detalleRV);
                $general->registrarAuditoria('Registro de detalle de retencion de venta numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de detalle de retencion de venta, con base imponible -> '.$baseF[$i].' porcentaje -> '.$porcentajeF[$i].' valor de retencion -> '.$valorF[$i]);
                    /********************detalle de diario de retencion de venta*******************/
                    $detalleDiario = new Detalle_Diario();
                    $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetF[$i])->first();
                    $detalleDiario->detalle_debe = $valorF[$i];
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R RETENCION EN LA FUENTE '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                    $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE VENTA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_recibida_cuenta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$retencion->retencion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorF[$i]);
                    /******************************************************************/
            }
            for ($i = 1; $i < count($baseI); ++$i){
                $detalleRV = new Detalle_RV();
                $detalleRV->detalle_tipo = 'IVA';
                $detalleRV->detalle_base = $baseI[$i];
                $detalleRV->detalle_porcentaje = $porcentajeI[$i];
                $detalleRV->detalle_valor = $valorI[$i];
                $detalleRV->detalle_asumida = '0';
                $detalleRV->detalle_estado = '1';
                $detalleRV->concepto_id = $idRetI[$i];
                $retencion->detalles()->save($detalleRV);
                $general->registrarAuditoria('Registro de detalle de retencion de venta numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de detalle de retencion de venta, con base imponible -> '.$baseI[$i].' porcentaje -> '.$porcentajeI[$i].' valor de retencion -> '.$valorI[$i]);
                    /********************detalle de diario de retencion de venta*******************/
                    $detalleDiario = new Detalle_Diario();
                    $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($idRetI[$i])->first();
                    $detalleDiario->detalle_debe = $valorI[$i];
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario = 'P/R RETENCION DE IVA '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                    $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE VENTA';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_recibida_cuenta;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$retencion->retencion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$valorI[$i]);
                    /******************************************************************/
            }
            /******************************************************************/
            $valorRetencion= round($request->get('idTotalRetenido'),2);
            if(round($cxcAux->cuenta_saldo,2) == 0){
                if($request->get('tipo_doc') == '0'){
                    $rangoDocumentoRetencion=Rango_Documento::PuntoRango($facturaAux->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                }else{
                    $rangoDocumentoRetencion=Rango_Documento::PuntoRango($ndAux->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                }
                if($rangoDocumentoRetencion){
                    $secuencial=$rangoDocumentoRetencion->rango_inicio;
                    $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    if($request->get('tipo_doc') == '0'){
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($facturaAux->rangoDocumento->punto_id)->sucursal_id)->get();
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($ndAux->rangoDocumento->punto_id)->sucursal_id)->get();
                    }
                    foreach($puntosEmision as $punto){
                        $rangoDocumentoRetencion=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Cliente')->first();
                        if($rangoDocumentoRetencion){
                            break;
                        }
                    }
                    if($rangoDocumentoRetencion){
                        $secuencial=$rangoDocumentoRetencion->rango_inicio;
                        $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    }else{
                        return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir anticipos de clientes, configueros y vuelva a intentar');
                    }
                }
                /********************Anticipo por Retencion de Venta***************************/
                $anticipoCliente = new Anticipo_Cliente();
                $anticipoCliente->anticipo_numero = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $anticipoCliente->anticipo_serie = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie;
                $anticipoCliente->anticipo_secuencial = $secuencial;
                $anticipoCliente->anticipo_fecha = $request->get('retencion_fecha');
                $anticipoCliente->anticipo_tipo = 'COMPROBANTE DE RETENCION';   
                $anticipoCliente->anticipo_documento = $retencion->retencion_numero;          
                $anticipoCliente->anticipo_motivo = 'RETENCION DE VENTA';
                $anticipoCliente->anticipo_valor = $valorRetencion;  
                $anticipoCliente->anticipo_saldo = $valorRetencion;   
                $anticipoCliente->cliente_id = $cxcAux->cliente_id;
                $anticipoCliente->rango_id = $rangoDocumentoRetencion->rango_id;
                $anticipoCliente->anticipo_estado = 1; 
                $anticipoCliente->diario()->associate($diario);
                $anticipoCliente->save();
                $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$request->get('idCliente'),'0','Con motivo: Retencion de venta recibida');
                /******************************************************************/
                /********************detalle de diario de retencion de venta anticipo*******************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00 ;
                $detalleDiario->detalle_haber = $valorRetencion;
                $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE CLIENTE';
                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE VENTA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cliente_id = $cxcAux->cliente_id;
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = $cxcAux->cliente;
                    $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$retencion->retencion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$valorRetencion);
                /******************************************************************/
                
            }else if(round($cxcAux->cuenta_saldo) >= round($valorRetencion)){        
                /********************Pago por Retencion de Venta***************************/
                $pago = new Pago_CXC();
                $pago->pago_descripcion = 'Retencion de venta No. '.$retencion->retencion_numero;
                $pago->pago_fecha = $request->get('retencion_fecha');
                $pago->pago_tipo = 'COMPROBANTE DE RETENCION DE VENTA';
                $pago->pago_valor = $valorRetencion;
                $pago->pago_estado = '1';
                $pago->diario()->associate($diario);
                $pago->save();
                if($request->get('tipo_doc') == '0'){
                    $general->registrarAuditoria('Registro de pago a Cliente -> '.$request->get('nombreCliente'),'0','Pago de factura No. '.$facturaAux->factura_numero.' con motivo: Retencion recibida'.' No. '.$retencion->retencion_numero); 
                }else{
                    $general->registrarAuditoria('Registro de pago a Cliente -> '.$request->get('nombreCliente'),'0','Pago de Nota de Debito No. '.$ndAux->factura_numero.' con motivo: Retencion recibida'.' No. '.$retencion->retencion_numero); 
                }
                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = 'Retencion de venta No. '.$retencion->retencion_numero;
                $detallePago->detalle_pago_valor = $valorRetencion; 
                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1;
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();

                if($request->get('tipo_doc') == '0'){
                    $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$request->get('nombreCliente'),'0','Detalle de pago de factura No. '.$facturaAux->factura_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($facturaAux->factura_id)->sum('descuento_valor'),2);
                }else{
                    $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$request->get('nombreCliente'),'0','Detalle de pago de factura No. '.$ndAux->nd_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2);
                }
                if(round($cxcAux->cuenta_saldo,2) == 0){
                    $cxcAux->cuenta_estado = '2';
                }else{
                    $cxcAux->cuenta_estado = '1';
                }
                $cxcAux->update();
                if($request->get('tipo_doc') == '0'){
                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$facturaAux->factura_numero);
                }else{
                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con nota de debito -> '.$ndAux->nd_numero);
                }
                /****************************************************************/
                /********************detalle de diario de retencion de venta pagoCXC*******************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00 ;
                $detalleDiario->detalle_haber = $valorRetencion;
                $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE VENTA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cliente_id = $cxcAux->cliente_id;
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = $cxcAux->cliente;
                    $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$retencion->retencion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$valorRetencion);
                /******************************************************************/
            }else{
                if($request->get('tipo_doc') == '0'){
                    $rangoDocumentoRetencion=Rango_Documento::PuntoRango($facturaAux->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                }else{
                    $rangoDocumentoRetencion=Rango_Documento::PuntoRango($ndAux->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                }
                if($rangoDocumentoRetencion){
                    $secuencial=$rangoDocumentoRetencion->rango_inicio;
                    $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    if($request->get('tipo_doc') == '0'){
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($facturaAux->rangoDocumento->punto_id)->sucursal_id)->get();
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($ndAux->rangoDocumento->punto_id)->sucursal_id)->get();
                    }
                    foreach($puntosEmision as $punto){
                        $rangoDocumentoRetencion=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Cliente')->first();
                        if($rangoDocumentoRetencion){
                            break;
                        }
                    }
                    if($rangoDocumentoRetencion){
                        $secuencial=$rangoDocumentoRetencion->rango_inicio;
                        $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                        if($secuencialAux){$secuencial=$secuencialAux+1;}
                    }else{
                        return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir anticipos de clientes, configueros y vuelva a intentar');
                    }
                }
                /********************Anticipo por Retencion de Venta***************************/
                $anticipoCliente = new Anticipo_Cliente();
                $anticipoCliente->anticipo_numero = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                $anticipoCliente->anticipo_serie = $rangoDocumentoRetencion->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoRetencion->puntoEmision->punto_serie;
                $anticipoCliente->anticipo_secuencial = $secuencial;
                $anticipoCliente->anticipo_fecha = $request->get('retencion_fecha');
                $anticipoCliente->anticipo_tipo = 'COMPROBANTE DE RETENCION';   
                $anticipoCliente->anticipo_documento = $retencion->retencion_numero;          
                $anticipoCliente->anticipo_motivo = 'RETENCION DE VENTA';
                $anticipoCliente->anticipo_valor = $valorRetencion - $cxcAux->cuenta_saldo;  
                $anticipoCliente->anticipo_saldo = $valorRetencion - $cxcAux->cuenta_saldo;   
                $anticipoCliente->cliente_id = $cxcAux->cliente_id;
                $anticipoCliente->rango_id = $rangoDocumentoRetencion->rango_id;
                $anticipoCliente->anticipo_estado = 1; 
                $anticipoCliente->diario()->associate($diario);
                $anticipoCliente->save();
                $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$request->get('nombreCliente'),'0','Con motivo: Retencion de venta recibida');
                /******************************************************************/
                /********************detalle de diario de retencion de venta anticipo*******************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00 ;
                $detalleDiario->detalle_haber = $valorRetencion - $cxcAux->cuenta_saldo;
                $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE CLIENTE';
                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE VENTA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cliente_id = $cxcAux->cliente_id;
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = $cxcAux->cliente;
                    $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$retencion->retencion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$detalleDiario->detalle_haber);
                /******************************************************************/
                /********************Pago por Retencion de Venta***************************/
                $pago = new Pago_CXC();
                $pago->pago_descripcion = 'Retencion de venta No. '.$retencion->retencion_numero;
                $pago->pago_fecha = $request->get('retencion_fecha');
                $pago->pago_tipo = 'COMPROBANTE DE RETENCION DE VENTA';
                $pago->pago_valor = $cxcAux->cuenta_saldo;
                $pago->pago_estado = '1';
                $pago->diario()->associate($diario);
                $pago->save();

                if($request->get('tipo_doc') == '0'){
                    $general->registrarAuditoria('Registro de pago a Cliente -> '.$request->get('nombreCliente'),'0','Pago de factura No. '.$facturaAux->factura_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                }else{
                    $general->registrarAuditoria('Registro de pago a Cliente -> '.$request->get('nombreCliente'),'0','Pago de nota de debito No. '.$ndAux->nd_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                }

                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = 'Retencion de venta No. '.$retencion->retencion_numero;
                $detallePago->detalle_pago_valor = $cxcAux->cuenta_saldo; 
                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1;
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();

                if($request->get('tipo_doc') == '0'){
                    $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$request->get('nombreCliente'),'0','Detalle de pago de factura No. '.$facturaAux->factura_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($facturaAux->factura_id)->sum('descuento_valor'),2);
                }else{
                    $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$request->get('nombreCliente'),'0','Detalle de pago de nota de debito No. '.$ndAux->nd_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2);
                }
                if(round($cxcAux->cuenta_saldo,2) == 0){
                    $cxcAux->cuenta_estado = '2';
                }else{
                    $cxcAux->cuenta_estado = '1';
                }
                $cxcAux->update();
                if($request->get('tipo_doc') == '0'){
                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con factura -> '.$facturaAux->factura_numero);
                }else{
                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente'),'0','Actualizacion de cuenta por cobrar de cliente -> '.$request->get('nombreCliente').' con nota de debito -> '.$ndAux->factura_numero);
                }
                /****************************************************************/
                /********************detalle de diario de retencion de venta pagoCXC*******************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00 ;
                $detalleDiario->detalle_haber = $cxcAux->cuenta_saldo;
                $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE VENTA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cliente_id = $cxcAux->cliente_id;
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = $cxcAux->cliente;
                    $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$retencion->retencion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' por un valor de -> '.$valorRetencion);
                /******************************************************************/
            }
            $url = $general->pdfDiario($diario);
            if($general->validateUnbalancedJournal($diario) ==  false){
                throw new Exception('Error la retencion no pudo ser registrada error al crear asiento diario.');
            }
            /****************************************************************/
            DB::commit();
            return redirect('/retencionVenta')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/retencionVenta')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
