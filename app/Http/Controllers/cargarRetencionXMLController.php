<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anticipo_Cliente;
use App\Models\Concepto_Retencion;
use App\Models\Cuenta_Cobrar;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Pago_CXC;
use App\Models\Detalle_RV;
use App\Models\Diario;
use App\Models\Empresa;
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

class cargarRetencionXMLController extends Controller
{
    public function nuevo(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.ventas.retencionRecibidaXML.index',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $datos = [];
            $count = 1;
            if($request->file('file_sri')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '-SRI-retencion.' .$request->file('file_sri')->getClientOriginalExtension();
                $path = $request->file('file_sri')->move(public_path().'\temp', $name); 
                $contenido = file_get_contents ($path);
                $contenido = utf8_encode($contenido);
                $registros = explode ( "\n", $contenido);
                for ($i = 2; $i < sizeof($registros); $i ++) {
                    $data[$i] = explode("\t", $registros[$i]);
                    if(count($data[$i])>1){
                        $numero = explode('-',$data[$i][1]);
                        $serie = $numero[0].$numero[1];
                        $secuencial = floatval($numero[2]);
                        if($data[$i][0] == 'Comprobante de Retención'){
                            $datos[$count]['cliente'] = $data[$i][3];
                            $datos[$count]['fecha'] = $data[$i][4];
                            $datos[$count]['numero'] = $data[$i][1];
                            $datos[$count]['clave'] = $data[$i][9]; 
                            $datos[$count]['estado'] = 'nuevo';
                            $datos[$count]['mensaje'] = '';
                            $retencion = Retencion_Venta::RetencionBySerieSecuancial($serie, $secuencial,$data[$i][2])->first();
                            if(isset($retencion->retencion_id)){
                                $datos[$count]['estado'] = 'cargada';
                                $datos[$count]['mensaje'] = 'Retención registrada previamente';
                            } 
                            $retencion = Retencion_Venta::RetencionBySerieSecuancialND($serie, $secuencial,$data[$i][2])->first();
                            if(isset($retencion->retencion_id)){
                                $datos[$count]['estado'] = 'cargada';
                                $datos[$count]['mensaje'] = 'Retención registrada previamente';
                            }           
                            $count ++;
                        }
                    }                    
                }            
            }
            $datos = $this->procesar($datos);
            return view('admin.ventas.retencionRecibidaXML.index',['datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('retencionRecibidaXML')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    private function procesar($datos){
        $metodo =  new facturacionElectronicaController();
        for ($i = 1; $i <= count($datos); ++$i){
            if($datos[$i]['estado'] == 'nuevo'){
                $retencionXML = $metodo->consultarDOC($datos[$i]['clave']);
                if(isset($retencionXML['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])){
                    if($retencionXML['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO'){
                        $xmlRet = simplexml_load_string($retencionXML['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['comprobante']);
                        $factura = null;
                        $nd = null;
                        $doc = '0';
                        $valorRetenido = 0;
                        $baseRenta = 0;
                        $baseIva = 0;
                        $danderaError =  true;
                        try{ 
                            foreach($xmlRet->impuestos->impuesto as $impuesto){
                                if($impuesto->codigo == '1'){
                                    $baseRenta = $baseRenta + $impuesto->baseImponible;
                                }else if($impuesto->codigo == '2'){
                                    $baseIva = $baseIva + $impuesto->baseImponible;
                                }
                                $valorRetenido = $valorRetenido + floatval($impuesto->valorRetenido);
                                $doc = $impuesto->codDocSustento;
                                if($impuesto->codDocSustento == '01' || $impuesto->codDocSustento=='17'){
                                    $factura = Factura_Venta::FacturasbyNumero($impuesto->numDocSustento)->first();
                                }else{
                                    $nd = Nota_Debito::NDbyNumero($impuesto->numDocSustento)->first();
                                }
                            }
                        }catch(\Exception $ex){     
                            $danderaError =  false; 
                            $datos[$i]['mensaje'] = 'Error con estructura XML de la retencion.';
                            $datos[$i]['estado'] = 'no';
                        }
                        if($danderaError){
                            if($valorRetenido == 0){
                                $datos[$i]['mensaje'] = 'Retencion en cero';
                                $datos[$i]['estado'] = 'no';
                            }else{
                                if($doc == '01'  || $doc=='17'){
                                    if(isset($factura->factura_id)){
                                        if($factura->factura_estado == '1'){
                                            if(round($baseRenta,2) ==  round(floatval($factura->factura_subtotal)-floatval($factura->factura_descuento),2)){
                                                $response = $this->guardar($xmlRet);
                                                if($response == 'ok'){
                                                    $datos[$i]['mensaje'] = 'Retención registrada exitosamente.';
                                                    $datos[$i]['estado'] = 'si';
                                                }elseif($response == '1'){
                                                    $datos[$i]['mensaje'] = 'La retención no se registro porque pertence a un periodo cerrado.';
                                                    $datos[$i]['estado'] = 'no';
                                                }elseif($response == '2'){
                                                    $datos[$i]['mensaje'] = 'La factura ya tiene registrada una retencion, verifique la información y vuelva a intentar.';
                                                    $datos[$i]['estado'] = 'no';
                                                }elseif($response == '3'){
                                                    $datos[$i]['mensaje'] = 'La nota de debito ya tiene registrada una retencion, verifique la información y vuelva a intentar.';
                                                    $datos[$i]['estado'] = 'no';
                                                }elseif($response[0] == '4'){
                                                    $datos[$i]['mensaje'] = $response[1];
                                                    $datos[$i]['estado'] = 'no';
                                                }
                                            }else{
                                                $datos[$i]['mensaje'] = 'La base de impuesto a la renta de la retencion es diferente al subtotal de la factura'
                                                                        .', renta: '.$baseRenta
                                                                        .', subtotal: '.$factura->factura_subtotal
                                                                        .',     diferencia: '.$baseRenta-$factura->factura_subtotal-$factura->factura_descuento;
                                                $datos[$i]['estado'] = 'no';
                                            }
                                        }else{
                                            $datos[$i]['mensaje'] = 'La factura a la que pertenece esta retención se encuentra anulada.';
                                            $datos[$i]['estado'] = 'no';
                                        }
                                    }else{
                                        $datos[$i]['mensaje'] = 'La factura a la que pertenece esta retención no existe.';
                                        $datos[$i]['estado'] = 'no';
                                    }
                                }else{
                                    if(isset($nd->nd_id)){
                                        if($nd->nd_estado == '1'){
                                            if($baseRenta == $nd->nd_subtotal){
                                                $response = $this->guardar($xmlRet);
                                                if($response == 'ok'){
                                                    $datos[$i]['mensaje'] = 'Retención registrada exitosamente.';
                                                    $datos[$i]['estado'] = 'si';
                                                }elseif($response == '1'){
                                                    $datos[$i]['mensaje'] = 'La retención no se registro porque pertence a un periodo cerro.';
                                                    $datos[$i]['estado'] = 'no';
                                                }elseif($response == '2'){
                                                    $datos[$i]['mensaje'] = 'La factura ya tiene registrada una retencion, verifique la información y vuelva a intentar.';
                                                    $datos[$i]['estado'] = 'no';
                                                }elseif($response == '3'){
                                                    $datos[$i]['mensaje'] = 'La nota de debito ya tiene registrada una retencion, verifique la información y vuelva a intentar.';
                                                    $datos[$i]['estado'] = 'no';
                                                }elseif($response == '4'){
                                                    $datos[$i]['mensaje'] = 'Ocurrio un Error al guardar la retención. Verifique la factura y vuelva a intentar';
                                                    $datos[$i]['estado'] = 'no';
                                                }
                                            }else{
                                                $datos[$i]['mensaje'] = 'La base de impuesto a la renta de la retencion es diferente al subtotal de la nota de debito.';
                                                $datos[$i]['estado'] = 'no';
                                            }
                                        }else{
                                            $datos[$i]['mensaje'] = 'La nota de debito a la que pertenece esta retención se encuentra anulada.';
                                            $datos[$i]['estado'] = 'no';
                                        }
                                    }else{
                                        $datos[$i]['mensaje'] = 'La nota de debito a la que pertenece esta retención no existe. doc: '.$doc;
                                        $datos[$i]['estado'] = 'no';
                                    }
                                }
                            }
                        }
                    }else{
                        $datos[$i]['mensaje'] = 'La retencion no se encuentra autorizada.';
                        $datos[$i]['estado'] = 'no';
                    }
                }else{
                    $datos[$i]['mensaje'] = 'La retencion se encuentra anulada.';
                    $datos[$i]['estado'] = 'no';
                }
            }
        }
        return $datos;
    }

    public function guardar($xml){
        try{            
            DB::beginTransaction();
            $general = new generalController();
           
            $valorRetencion = 0;
            foreach($xml->impuestos->impuesto as $impuesto){
                $retenido=round(floatval($impuesto->valorRetenido),2);
                /*VALIDACION PORCENTAJE RETENIDO DIFERENTE*/
                //if(round(floatval($impuesto->baseImponible) * (floatval($impuesto->porcentajeRetener)/100),2) != round(floatval($impuesto->valorRetenido),2)){
                    
                $redondear=generalController::redondear2Dec(floatval($impuesto->baseImponible) * (floatval($impuesto->porcentajeRetener)/100));
                
                if(round($redondear,2) != round($retenido,2)  && abs(round($redondear-$retenido,2))>0.011 ){
                    throw new Exception('Error la retencion no pudo ser registrada, el valor retenido es difrente al valor calculado con el codigo de retencion '
                                        .$impuesto->codigoRetencion.'  diferencia '.abs(round($redondear-$retenido,2)));
                }


                /*FIN*/
                $valorRetencion = round($valorRetencion,2) + round(floatval($impuesto->valorRetenido),2);
                $factura = null;
                $nd = null;
                if($impuesto->codDocSustento == '01'){
                    $factura = Factura_Venta::FacturasbyNumero($impuesto->numDocSustento)->first();
                    $cierre = $general->cierre($factura->factura_fecha,$factura->sucursal_id);          
                    if($cierre){
                        DB::commit();
                        return '1';
                    }
                    if(isset($factura->retencion->retencion_id)){
                        DB::commit();
                        return '2';
                    }
                    $cxcAux = $factura->cuentaCobrar;
                }else{
                    $nd = Nota_Debito::NDbyNumero($impuesto->numDocSustento)->first();
                    $ndaux=Nota_Debito::findOrFail($nd->nd_id);
                    $cierre = $general->cierre($ndaux->nd_fecha,$ndaux->rangoDocumento->puntoEmision->sucursal_id);          
                    if($cierre){
                        DB::commit();
                        return '1';
                    }
                    if(isset($nd->retencion->retencion_id)){
                        DB::commit();
                        return '3';
                    }
                    $cxcAux = $nd->cuentaCobrar;
                }
            }
               
            
            $retencion = new Retencion_Venta();
            $retencion->retencion_fecha =DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('Y-m-d');
            $retencion->retencion_emision = "ELECTRONICA";
            $retencion->retencion_numero = $xml->infoTributaria->estab.$xml->infoTributaria->ptoEmi.substr(str_repeat(0, 9).$xml->infoTributaria->secuencial, - 9);
            $retencion->retencion_serie = $xml->infoTributaria->estab.$xml->infoTributaria->ptoEmi;
            $retencion->retencion_secuencial = $xml->infoTributaria->secuencial;
            $retencion->retencion_estado = '1';
            if(isset($factura->factura_id)){
                $retencion->factura_id = $factura->factura_id;
                $cierre = $general->cierre($factura->factura_fecha,Rango_Documento::rango($factura->rango_id)->first()->puntoEmision->sucursal_id);      
            }else{
                $retencion->nd_id = $nd->nd_id;
                $cierre = $general->cierre($nd->nd_fecha,Rango_Documento::rango($nd->rango_id)->first()->puntoEmision->sucursal_id);      
            }
            if($cierre){
                return redirect('listabeneficios')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario(DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('Y-m-d'),'CRER');
                $diario->diario_tipo = 'CRER';
                $diario->diario_fecha = DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('Y-m-d');
                $diario->diario_referencia = 'COMPROBANTE DIARIO DE RETENCIÓN DE VENTA';
                $diario->diario_tipo_documento = 'COMPROBANTE DE RETENCION';
                $diario->diario_numero_documento = $retencion->retencion_numero;
                if(isset($factura->factura_id)){
                    $diario->diario_beneficiario = $factura->cliente->cliente_nombre;
                }else{
                    $diario->diario_beneficiario = $nd->factura->cliente->cliente_nombre;
                }
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('m');
                $diario->diario_ano = DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('Y');
                if(isset($factura->factura_id)){
                    $diario->diario_comentario = 'COMPROBANTE DIARIO DE RETENCIÓN DE VENTA : '.$retencion->retencion_numero.' CON NUMERO DE FACTURA : '.$factura->factura_numero;
                }else{
                    $diario->diario_comentario = 'COMPROBANTE DIARIO DE RETENCIÓN DE VENTA : '.$retencion->retencion_numero.' CON NUMERO DE NOTA DE DEBITO : '.$nd->nd_numero;
                }
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                if(isset($factura->factura_id)){
                    $diario->sucursal_id = $factura->rangoDocumento->puntoEmision->sucursal_id;
                }else{
                    $diario->sucursal_id = $nd->rangoDocumento->puntoEmision->sucursal_id;
                }
                $diario->save();
                if(isset($factura->factura_id)){
                    $general->registrarAuditoria('Registro de diario de comprobante de retención de venta -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de diario de comprobante de retención de venta -> '.$retencion->retencion_numero.' con numero de factura -> '.$factura->factura_numero.' y con codigo de diario -> '.$diario->diario_codigo);
                }else{
                    $general->registrarAuditoria('Registro de diario de comprobante de retención de venta -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de diario de comprobante de retención de venta -> '.$retencion->retencion_numero.' con numero de nota de debito -> '.$nd->nd_numero.' y con codigo de diario -> '.$diario->diario_codigo);
                }
                /****************************************************************/
            $retencion->diario()->associate($diario);
            $retencion->save();
            $general->registrarAuditoria('Registro de retencion de venta numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de retencion de venta numero -> '.$retencion->retencion_numero.' y con codigo de diario -> '.$diario->diario_codigo);
            /******************************************************************/
            foreach($xml->impuestos->impuesto as $impuesto){
                if(floatval($impuesto->valorRetenido) > 0){
                    if($impuesto->codigo == '1'){
                        if(round(floatval($impuesto->valorRetenido),2) > 0){
                            $detalleRV = new Detalle_RV();
                            $detalleRV->detalle_tipo = 'FUENTE';
                            $detalleRV->detalle_base = $impuesto->baseImponible;
                            $detalleRV->detalle_porcentaje = $impuesto->porcentajeRetener;
                            $detalleRV->detalle_valor = $impuesto->valorRetenido;
                            $detalleRV->detalle_asumida = '0';
                            $detalleRV->detalle_estado = '1';
                            $detalleRV->concepto_id = Concepto_Retencion::ConceptoRetencionByCodigo($impuesto->codigoRetencion)->first()->concepto_id;
                            $retencion->detalles()->save($detalleRV);
                            $general->registrarAuditoria('Registro de detalle de retencion de venta numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de detalle de retencion de venta, con base imponible -> '.$detalleRV->detalle_base.' porcentaje -> '.$detalleRV->detalle_porcentaje.' valor de retencion -> '.$detalleRV->detalle_valor);
                            /********************detalle de diario de retencion de venta*******************/
                                $detalleDiario = new Detalle_Diario();
                                $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($detalleRV->concepto_id)->first();
                                $detalleDiario->detalle_debe = $detalleRV->detalle_valor;
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario = 'P/R RETENCION EN LA FUENTE '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE VENTA';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_recibida_cuenta;
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$retencion->retencion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$detalleRV->detalle_valor);
                            /******************************************************************/
                        }
                    }
                    if($impuesto->codigo == '2'){
                        if(round(floatval($impuesto->valorRetenido),2) > 0){
                            $detalleRV = new Detalle_RV();
                            $detalleRV->detalle_tipo = 'IVA';
                            $detalleRV->detalle_base = $impuesto->baseImponible;
                            $detalleRV->detalle_porcentaje = $impuesto->porcentajeRetener;
                            $detalleRV->detalle_valor = $impuesto->valorRetenido;
                            $detalleRV->detalle_asumida = '0';
                            $detalleRV->detalle_estado = '1';
                            $detalleRV->concepto_id = Concepto_Retencion::ConceptoRetencionByCodigo($impuesto->codigoRetencion)->first()->concepto_id;
                            $retencion->detalles()->save($detalleRV);
                            $general->registrarAuditoria('Registro de detalle de retencion de venta numero -> '.$retencion->retencion_numero,$retencion->retencion_numero,'Registro de detalle de retencion de venta, con base imponible -> '.$detalleRV->detalle_base.' porcentaje -> '.$detalleRV->detalle_porcentaje.' valor de retencion -> '.$detalleRV->detalle_valor);
                            /********************detalle de diario de retencion de venta*******************/
                                $detalleDiario = new Detalle_Diario();
                                $cuentaContableRetencion=Concepto_Retencion::ConceptoRetencion($detalleRV->concepto_id)->first();
                                $detalleDiario->detalle_debe = $detalleRV->detalle_valor;
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario = 'P/R RETENCION DE IVA '.$cuentaContableRetencion->concepto_codigo.' CON PORCENTAJE '.$cuentaContableRetencion->concepto_porcentaje.' %';
                                $detalleDiario->detalle_tipo_documento = 'COMPROBANTE DE RETENCION DE VENTA';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $detalleDiario->cuenta_id = $cuentaContableRetencion->concepto_recibida_cuenta;
                                $diario->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$retencion->retencion_numero,'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' con cuenta contable -> '.$cuentaContableRetencion->cuentaEmitida->cuenta_numero.' en el haber por un valor de -> '.$detalleRV->detalle_valor);
                            /******************************************************************/
                        }
                    }
                }
            }
            /******************************************************************/
            if(round($cxcAux->cuenta_saldo,2) == 0){
                if(isset($factura->factura_id)){
                    $rangoDocumentoRetencion=Rango_Documento::PuntoRango($factura->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                }else{
                    $rangoDocumentoRetencion=Rango_Documento::PuntoRango($nd->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                }
                if($rangoDocumentoRetencion){
                    $secuencial=$rangoDocumentoRetencion->rango_inicio;
                    $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    if(isset($factura->factura_id)){
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($factura->rangoDocumento->punto_id)->sucursal_id)->get();
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($nd->rangoDocumento->punto_id)->sucursal_id)->get();
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
                $anticipoCliente->anticipo_fecha = DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('Y-m-d');
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
                $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$cxcAux->cliente->cliente_nombre,$anticipoCliente->anticipo_numero,'Con motivo: Retencion de venta recibida');
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
                
            }else if(round($cxcAux->cuenta_saldo,2) >= round($valorRetencion,2)){        
                /********************Pago por Retencion de Venta***************************/
                $pago = new Pago_CXC();
                $pago->pago_descripcion = 'Retencion de venta No. '.$retencion->retencion_numero;
                $pago->pago_fecha = DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('Y-m-d');
                $pago->pago_tipo = 'COMPROBANTE DE RETENCION DE VENTA';
                $pago->pago_valor = $valorRetencion;
                $pago->pago_estado = '1';
                $pago->diario()->associate($diario);
                $pago->save();
                if(isset($factura->factura_id)){
                    $general->registrarAuditoria('Registro de pago a Cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Pago de factura No. '.$factura->factura_numero.' con motivo: Retencion recibida'.' No. '.$retencion->retencion_numero); 
                }else{
                    $general->registrarAuditoria('Registro de pago a Cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Pago de Nota de Debito No. '.$nd->factura_numero.' con motivo: Retencion recibida'.' No. '.$retencion->retencion_numero); 
                }
                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = 'Retencion de venta No. '.$retencion->retencion_numero;
                $detallePago->detalle_pago_valor = $valorRetencion; 
                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1;
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();
                if(isset($factura->factura_id)){
                    $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Detalle de pago de factura No. '.$factura->factura_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($factura->factura_id)->sum('descuento_valor'),2);
                }else{
                    $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Detalle de pago de factura No. '.$nd->nd_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2);
                }
                if(round($cxcAux->cuenta_saldo,2) == 0){
                    $cxcAux->cuenta_estado = '2';
                }else{
                    $cxcAux->cuenta_estado = '1';
                }
                $cxcAux->update();
                if(isset($factura->factura_id)){
                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Actualizacion de cuenta por cobrar de cliente -> '.$cxcAux->cliente->cliente_nombre.' con factura -> '.$factura->factura_numero);
                }else{
                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Actualizacion de cuenta por cobrar de cliente -> '.$cxcAux->cliente->cliente_nombre.' con nota de debito -> '.$nd->nd_numero);
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
                if(isset($factura->factura_id)){
                    $rangoDocumentoRetencion=Rango_Documento::PuntoRango($factura->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                }else{
                    $rangoDocumentoRetencion=Rango_Documento::PuntoRango($nd->rangoDocumento->punto_id, 'Anticipo de Cliente')->first();
                }
                if($rangoDocumentoRetencion){
                    $secuencial=$rangoDocumentoRetencion->rango_inicio;
                    $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumentoRetencion->rango_id)->max('anticipo_secuencial');
                    if($secuencialAux){$secuencial=$secuencialAux+1;}
                
                }else{
                    if(isset($factura->factura_id)){
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($factura->rangoDocumento->punto_id)->sucursal_id)->get();
                    }else{
                        $puntosEmision = Punto_Emision::PuntoxSucursal(Punto_Emision::findOrFail($nd->rangoDocumento->punto_id)->sucursal_id)->get();
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
                $anticipoCliente->anticipo_fecha = DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('Y-m-d');
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
                $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$cxcAux->cliente->cliente_nombre,$anticipoCliente->anticipo_numero,'Con motivo: Retencion de venta recibida');
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
                $pago->pago_fecha = DateTime::createFromFormat('d/m/Y', $xml->infoCompRetencion->fechaEmision)->format('Y-m-d');
                $pago->pago_tipo = 'COMPROBANTE DE RETENCION DE VENTA';
                $pago->pago_valor = $cxcAux->cuenta_saldo;
                $pago->pago_estado = '1';
                $pago->diario()->associate($diario);
                $pago->save();

                if(isset($factura->factura_id)){
                    $general->registrarAuditoria('Registro de pago a Cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Pago de factura No. '.$factura->factura_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                }else{
                    $general->registrarAuditoria('Registro de pago a Cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Pago de nota de debito No. '.$nd->nd_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                }

                $detallePago = new Detalle_Pago_CXC();
                $detallePago->detalle_pago_descripcion = 'Retencion de venta No. '.$retencion->retencion_numero;
                $detallePago->detalle_pago_valor = $cxcAux->cuenta_saldo; 
                $detallePago->detalle_pago_cuota = Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->count()+1;
                $detallePago->detalle_pago_estado = '1'; 
                $detallePago->cuenta_id = $cxcAux->cuenta_id; 
                $detallePago->pagoCXC()->associate($pago);
                $detallePago->save();

                if(isset($factura->factura_id)){
                    $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Detalle de pago de factura No. '.$factura->factura_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($factura->factura_id)->sum('descuento_valor'),2);
                }else{
                    $general->registrarAuditoria('Registro de detalle a pago de Cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Detalle de pago de nota de debito No. '.$nd->nd_numero.' con motivo: Retencion recibida').' No. '.$retencion->retencion_numero; 
                    $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2);
                }
                if(round($cxcAux->cuenta_saldo,2) == 0){
                    $cxcAux->cuenta_estado = '2';
                }else{
                    $cxcAux->cuenta_estado = '1';
                }
                $cxcAux->update();
                if(isset($factura->factura_id)){
                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Actualizacion de cuenta por cobrar de cliente -> '.$cxcAux->cliente->cliente_nombre.' con factura -> '.$factura->factura_numero);
                }else{
                    $general->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente -> '.$cxcAux->cliente->cliente_nombre,'0','Actualizacion de cuenta por cobrar de cliente -> '.$cxcAux->cliente->cliente_nombre.' con nota de debito -> '.$nd->factura_numero);
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
            $general->pdfDiario($diario);
            if($general->validateUnbalancedJournal($diario) ==  false){
                throw new Exception('Error la retencion no pudo ser registrada error al crear asiento diario.');
            }
            /****************************************************************/
            DB::commit();
            return 'ok';
        }catch(\Exception $ex){
            DB::rollBack();
            return ['4', $ex->getMessage()];
        }
    }
}
