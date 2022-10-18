<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Firma_Electronica;
use App\Models\Guia_Remision;
use App\Models\Liquidacion_Compra;
use App\Models\Nota_Credito;
use App\Models\Nota_Debito;
use App\Models\Punto_Emision;
use App\Models\Retencion_Compra;
use App\Models\Transaccion_Identificacion;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use nusoap_client;
use PDF;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class facturacionElectronicaController extends Controller
{
    public function nuevaConsultaSri(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.sri.consultarDocsElectronicos.index',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultarDocSri(Request $request){
        try{
            $resp = $this->consultarDOC($request->get('clave'));
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.sri.consultarDocsElectronicos.index',['resp'=>$resp,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function generarClaveAcceso($numeroDocumento,$fecha,$tipo){
		$claveAccesoValor = DateTime::createFromFormat('Y-m-d', $fecha)->format('dmY') . $tipo . Empresa::Empresa()->first()->empresa_ruc . Firma_Electronica::firma()->first()->firma_ambiente . $numeroDocumento . "00000001" . Firma_Electronica::firma()->first()->firma_disponibilidad;
        //DIGITO VERIFICADOR (MODULO 11)
        $digitoVerificador =  $this->obtenerSumaPorDigitos($this->invertirCadena($claveAccesoValor));
        if($digitoVerificador == 11) {
            $digitoVerificador = 0;
        }elseif($digitoVerificador == 10){
            $digitoVerificador = 1;
        }
        return $claveAccesoValor.$digitoVerificador;
    }
    private function invertirCadena($cadena) {
        $cadenaInvertida = "";
        for ($x = strlen($cadena) - 1; $x >= 0; $x--) {
            $cadenaInvertida = $cadenaInvertida.$cadena[$x];
        }
        return $cadenaInvertida;
    }
    private function obtenerSumaPorDigitos($cadena) {
        $pivote = 2;
        $longitudCadena = strlen($cadena);
        $cantidadTotal = 0;
        for ($i = 0; $i < $longitudCadena; $i++) {
            if ($pivote == 8) {
                $pivote = 2;
            }
            $temporal = intval("" . substr($cadena, $i, 1));
            $temporal *= $pivote;
            $pivote++;
            $cantidadTotal += $temporal;
        }
        $cantidadTotal = 11 - $cantidadTotal % 11;
        return $cantidadTotal;
    }
    public function  enviarDocumentoElectronico($xml, $tipoDocumento){
        $firmaElectronica = Firma_Electronica::firma()->first();
        if($firmaElectronica->firma_estado == 1){
            $respuesta=$this->enviarDOC($xml, $firmaElectronica, $tipoDocumento);
            return $respuesta;
        }else{
            return 'Acceso Denegado. SERVICIO SUSPENDIDO POR FALTA DE PAGO';
        }
    }
    function consultarDOC($claveAcceso){
        $firmaElectronica = Firma_Electronica::firma()->first();
        if($firmaElectronica->firma_ambiente == 0 || $firmaElectronica->firma_ambiente == 1){
            $clientConsult = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
        }else{
            $clientConsult = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
        }
        $clientConsult = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
        $clientConsult->soap_defencoding = 'UTF-8';
        $clientConsult->decode_utf8 = FALSE;
        $parametrosConsult=array('claveAccesoComprobante'=>$claveAcceso);
        $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
        return $consultaDoc;
    }
    function enviarDOC($xml, $firmaElectronica, $tipoDocumento){
        $archivoFirmado = $this->firmarDoc($xml, $firmaElectronica);   
        $doc = new \DOMDocument();
        $doc->loadXML($archivoFirmado);
        $xml=base64_encode($doc->C14N());
        $parametros=array('xml'=>$xml); 
        if($firmaElectronica->firma_ambiente == 0 || $firmaElectronica->firma_ambiente == 1){
            $client = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl', 'wsdl');
        }else{
            $client = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl', 'wsdl');
        }
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = FALSE;
        $respuesta = $client->call("validarComprobante", $parametros);
        if($firmaElectronica->firma_ambiente == 0 || $firmaElectronica->firma_ambiente == 1){
            $clientConsult = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
        }else{
            $clientConsult = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
        }
        $clientConsult->soap_defencoding = 'UTF-8';
        $clientConsult->decode_utf8 = FALSE;
        $archivoFirmado = simplexml_load_string($archivoFirmado);
        $parametrosConsult=array('claveAccesoComprobante'=>$archivoFirmado->infoTributaria->claveAcceso);
        switch ($tipoDocumento) {
            case 'FACTURA':
                return $this->respuestaFactura($parametrosConsult,$respuesta,$clientConsult,$tipoDocumento);
                break;
            case 'RETENCION':
                return $this->respuestaRetencion($parametrosConsult,$respuesta,$clientConsult,$tipoDocumento);
                break;
            case 'NC':
                return $this->respuestaNC($parametrosConsult,$respuesta,$clientConsult,$tipoDocumento);
                break;
            case 'ND':
                return $this->respuestaND($parametrosConsult,$respuesta,$clientConsult,$tipoDocumento);
                break;
            case 'LC':
                return $this->respuestaLC($parametrosConsult,$respuesta,$clientConsult,$tipoDocumento);
                break;
            case 'GUIA':
                return $this->respuestaGuia($parametrosConsult,$respuesta,$clientConsult,$tipoDocumento);
                break;
        }
    }
    function respuestaFactura($parametrosConsult, $respuesta, $clientConsult, $tipoDocumento){
        $facturaAux = new Factura_Venta();
        $facturaAux->factura_xml_estado = 'Espere un momento y vuelva a intentar';
        try{
            if ($respuesta) {
                if (array_key_exists('RespuestaRecepcionComprobante', $respuesta) == false) {
                    $facturaAux->factura_xml_respuestaSRI = $respuesta;
                    $facturaAux->factura_xml_estado = 'Espere un momento y vuelva a intentar';
                    return $facturaAux;
                }
                if ($respuesta['RespuestaRecepcionComprobante']['estado'] == 'RECIBIDA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $facturaAux->factura_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $facturaAux->factura_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $facturaAux->factura_xml_estado = 'AUTORIZADO';
                        $facturaAux->factura_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $facturaAux->factura_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $facturaAux->factura_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $facturaAux->factura_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $facturaAux->factura_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $facturaAux->factura_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $facturaAux->factura_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } elseif ($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'] == 'CLAVE ACCESO REGISTRADA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $facturaAux->factura_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $facturaAux->factura_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $facturaAux->factura_xml_estado = 'AUTORIZADO';
                        $facturaAux->factura_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $facturaAux->factura_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $facturaAux->factura_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $facturaAux->factura_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $facturaAux->factura_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $facturaAux->factura_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $facturaAux->factura_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } else {
                    $facturaAux->factura_xml_respuestaSRI = $respuesta;
                    if (isset($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'])) {
                        $facturaAux->factura_xml_estado = $respuesta['RespuestaRecepcionComprobante']['estado'].' : '.$respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'];
                    } else {
                        $facturaAux->factura_xml_estado = 'Espere un momento y vuelva a intentar';
                    }
                }
            }
            return $facturaAux;
        }catch(\Exception $ex){
            return $facturaAux;
        }
    }
    function respuestaRetencion($parametrosConsult, $respuesta, $clientConsult, $tipoDocumento){
        $retencionAux = new Retencion_Compra();
        $retencionAux->retencion_xml_estado = 'Espere un momento y vuelva a intentar';
        try{
            if ($respuesta) {
                if (array_key_exists('RespuestaRecepcionComprobante', $respuesta) == false) {
                    $retencionAux->retencion_xml_respuestaSRI = $respuesta;
                    $retencionAux->retencion_xml_estado = 'Espere un momento y vuelva a intentar';
                    return $retencionAux;
                }
                if ($respuesta['RespuestaRecepcionComprobante']['estado'] == 'RECIBIDA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $retencionAux->retencion_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $retencionAux->retencion_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $retencionAux->retencion_xml_estado = 'AUTORIZADO';
                        $retencionAux->retencion_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $retencionAux->retencion_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $retencionAux->retencion_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $retencionAux->retencion_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $retencionAux->retencion_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $retencionAux->retencion_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $retencionAux->retencion_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } elseif ($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'] == 'CLAVE ACCESO REGISTRADA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $retencionAux->retencion_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $retencionAux->retencion_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $retencionAux->retencion_xml_estado = 'AUTORIZADO';
                        $retencionAux->retencion_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $retencionAux->retencion_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $retencionAux->retencion_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $retencionAux->retencion_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $retencionAux->retencion_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $retencionAux->retencion_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $retencionAux->retencion_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } else {
                    $retencionAux->retencion_xml_respuestaSRI = $respuesta;
                    if (isset($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'])) {
                        $retencionAux->retencion_xml_estado = $respuesta['RespuestaRecepcionComprobante']['estado'].' : '.$respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'];
                    } else {
                        $retencionAux->retencion_xml_estado = 'Espere un momento y vuelva a intentar';
                    }
                }
            }
            return $retencionAux;
        }catch(\Exception $ex){
            return $retencionAux;
        }
    }
    function respuestaNC($parametrosConsult, $respuesta, $clientConsult, $tipoDocumento){
        $ncAux = new Nota_Credito();
        $ncAux->nc_xml_estado = 'Espere un momento y vuelva a intentar';
        try{
            if ($respuesta) {
                if (array_key_exists('RespuestaRecepcionComprobante', $respuesta) == false) {
                    $ncAux->nc_xml_respuestaSRI = $respuesta;
                    $ncAux->nc_xml_estado = 'Espere un momento y vuelva a intentar';
                    return $ncAux;
                }
                if ($respuesta['RespuestaRecepcionComprobante']['estado'] == 'RECIBIDA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $ncAux->nc_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $ncAux->nc_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $ncAux->nc_xml_estado = 'AUTORIZADO';
                        $ncAux->nc_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $ncAux->nc_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $ncAux->nc_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $ncAux->nc_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $ncAux->nc_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $ncAux->nc_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $ncAux->nc_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } elseif ($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'] == 'CLAVE ACCESO REGISTRADA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $ncAux->nc_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $ncAux->nc_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $ncAux->nc_xml_estado = 'AUTORIZADO';
                        $ncAux->nc_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $ncAux->nc_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $ncAux->nc_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $ncAux->nc_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $ncAux->nc_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $ncAux->nc_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $ncAux->nc_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } else {
                    $ncAux->nc_xml_respuestaSRI = $respuesta;
                    if (isset($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'])) {
                        $ncAux->nc_xml_estado = $respuesta['RespuestaRecepcionComprobante']['estado'].' : '.$respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'];
                    } else {
                        $ncAux->nc_xml_estado = 'Espere un momento y vuelva a intentar';
                    }
                }
            }
            return $ncAux;
        }catch(\Exception $ex){
            return $ncAux;
        }
    }
    function respuestaND($parametrosConsult, $respuesta, $clientConsult, $tipoDocumento){
        $ndAux = new Nota_Debito();
        $ndAux->nd_xml_estado = 'Espere un momento y vuelva a intentar';
        try{
            if ($respuesta) {
                if (array_key_exists('RespuestaRecepcionComprobante', $respuesta) == false) {
                    $ndAux->nd_xml_respuestaSRI = $respuesta;
                    $ndAux->nd_xml_estado = 'Espere un momento y vuelva a intentar';
                    return $ndAux;
                }
                if ($respuesta['RespuestaRecepcionComprobante']['estado'] == 'RECIBIDA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $ndAux->nd_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $ndAux->nd_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $ndAux->nd_xml_estado = 'AUTORIZADO';
                        $ndAux->nd_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $ndAux->nd_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $ndAux->nd_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $ndAux->nd_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $ndAux->nd_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $ndAux->nd_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $ndAux->nd_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } elseif ($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'] == 'CLAVE ACCESO REGISTRADA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $ndAux->nd_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $ndAux->nd_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $ndAux->nd_xml_estado = 'AUTORIZADO';
                        $ndAux->nd_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $ndAux->nd_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $ndAux->nd_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $ndAux->nd_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $ndAux->nd_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $ndAux->nd_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $ndAux->nd_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } else {
                    $ndAux->nd_xml_respuestaSRI = $respuesta;
                    if (isset($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'])) {
                        $ndAux->nd_xml_estado = $respuesta['RespuestaRecepcionComprobante']['estado'].' : '.$respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'];
                    } else {
                        $ndAux->nd_xml_estado = 'Espere un momento y vuelva a intentar';
                    }
                }
            }
            return $ndAux;
        }catch(\Exception $ex){
            return $ndAux;
        }
    }
    function respuestaLC($parametrosConsult, $respuesta, $clientConsult, $tipoDocumento){
        $lcAux = new Liquidacion_Compra();
        $lcAux->lc_xml_estado = 'Espere un momento y vuelva a intentar';
        try{
            if ($respuesta) {
                if (array_key_exists('RespuestaRecepcionComprobante', $respuesta) == false) {
                    $lcAux->lc_xml_respuestaSRI = $respuesta;
                    $lcAux->lc_xml_estado = 'Espere un momento y vuelva a intentar';
                    return $lcAux;
                }
                if ($respuesta['RespuestaRecepcionComprobante']['estado'] == 'RECIBIDA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $lcAux->lc_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $lcAux->lc_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $lcAux->lc_xml_estado = 'AUTORIZADO';
                        $lcAux->lc_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $lcAux->lc_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $lcAux->lc_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $lcAux->lc_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $lcAux->lc_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $lcAux->lc_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $lcAux->lc_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } elseif ($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'] == 'CLAVE ACCESO REGISTRADA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $lcAux->lc_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $lcAux->lc_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $lcAux->lc_xml_estado = 'AUTORIZADO';
                        $lcAux->lc_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $lcAux->lc_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $lcAux->lc_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $lcAux->lc_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $lcAux->lc_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $lcAux->lc_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $lcAux->lc_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } else {
                    $lcAux->lc_xml_respuestaSRI = $respuesta;
                    if (isset($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'])) {
                        $lcAux->lc_xml_estado = $respuesta['RespuestaRecepcionComprobante']['estado'].' : '.$respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'];
                    } else {
                        $lcAux->lc_xml_estado = 'Espere un momento y vuelva a intentar';
                    }
                }
            }
            return $lcAux;
        }catch(\Exception $ex){
            return $lcAux;
        }
    }
    function respuestaGuia($parametrosConsult, $respuesta, $clientConsult, $tipoDocumento){
        $guiaAux = new Guia_Remision();
        $guiaAux->gr_xml_estado = 'Espere un momento y vuelva a intentar';
        try{
            if ($respuesta) {
                if (array_key_exists('RespuestaRecepcionComprobante', $respuesta) == false) {
                    $guiaAux->gr_xml_respuestaSRI = $respuesta;
                    $guiaAux->gr_xml_estado = 'Espere un momento y vuelva a intentar';
                    return $guiaAux;
                }
                if ($respuesta['RespuestaRecepcionComprobante']['estado'] == 'RECIBIDA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $guiaAux->gr_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $guiaAux->gr_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $guiaAux->gr_xml_estado = 'AUTORIZADO';
                        $guiaAux->gr_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $guiaAux->gr_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $guiaAux->gr_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $guiaAux->gr_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $guiaAux->gr_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $guiaAux->gr_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $guiaAux->gr_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } elseif ($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'] == 'CLAVE ACCESO REGISTRADA') {
                    $consultaDoc = $clientConsult->call("autorizacionComprobante", $parametrosConsult);
                    $guiaAux->gr_xml_respuestaSRI = $consultaDoc;
                    if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                        $guiaAux->gr_xml_nombre = $this->docAutorizado($consultaDoc, $tipoDocumento);
                        $guiaAux->gr_xml_estado = 'AUTORIZADO';
                        $guiaAux->gr_xml_mensaje = 'DOCUMENTO AUTORIZADO EXITOSAMENTE';
                        $guiaAux->gr_xml_fecha = strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T', true);
                        $guiaAux->gr_xml_hora = str_replace('-05:00', '', str_replace('T', '', strstr($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'], 'T')));
                    } else {
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'])) {
                            $guiaAux->gr_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'];
                        } else {
                            $guiaAux->gr_xml_estado = 'Espere un momento y vuelva a intentar';
                        }
                        if (isset($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'])) {
                            $guiaAux->gr_xml_estado = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['mensajes']['mensaje']['mensaje'];
                        } else {
                            $guiaAux->gr_xml_mensaje = 'Espere un momento y vuelva a intentar';
                        }
                    }
                } else {
                    $guiaAux->gr_xml_respuestaSRI = $respuesta;
                    if (isset($respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'])) {
                        $guiaAux->gr_xml_estado = $respuesta['RespuestaRecepcionComprobante']['estado'].' : '.$respuesta['RespuestaRecepcionComprobante']['comprobantes']["comprobante"]['mensajes']['mensaje']['mensaje'];
                    } else {
                        $guiaAux->gr_xml_estado = 'Espere un momento y vuelva a intentar';
                    }
                }
            }
            return $guiaAux;
        }catch(\Exception $ex){
            return $guiaAux;
        }
    }
    function docAutorizado($xml, $tipoDocumento){
        $empresa=Empresa::Empresa()->first();
        $xmlEnvio = simplexml_load_string($xml['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['comprobante']);
        $fecha = '';
        switch ($tipoDocumento) {
            case 'FACTURA':
                $fecha = $xmlEnvio->infoFactura->fechaEmision;
                $nombreArchivo = "FAC-".$xmlEnvio->infoTributaria->estab. "-".$xmlEnvio->infoTributaria->ptoEmi."-".$xmlEnvio->infoTributaria->secuencial;
                break;
            case 'RETENCION':
                $fecha = $xmlEnvio->infoCompRetencion->fechaEmision;
                $nombreArchivo = "RET-".$xmlEnvio->infoTributaria->estab. "-".$xmlEnvio->infoTributaria->ptoEmi."-".$xmlEnvio->infoTributaria->secuencial;
                break;
            case 'NC':
                $fecha = $xmlEnvio->infoNotaCredito->fechaEmision;
                $nombreArchivo = "NC-".$xmlEnvio->infoTributaria->estab. "-".$xmlEnvio->infoTributaria->ptoEmi."-".$xmlEnvio->infoTributaria->secuencial;
                break;
            case 'ND':
                $fecha = $xmlEnvio->infoNotaDebito->fechaEmision;
                $nombreArchivo = "ND-".$xmlEnvio->infoTributaria->estab. "-".$xmlEnvio->infoTributaria->ptoEmi."-".$xmlEnvio->infoTributaria->secuencial;
                break;
            case 'LC':
                $fecha = $xmlEnvio->infoLiquidacionCompra->fechaEmision;
                $nombreArchivo = "LC-".$xmlEnvio->infoTributaria->estab. "-".$xmlEnvio->infoTributaria->ptoEmi."-".$xmlEnvio->infoTributaria->secuencial;
                break;
            case 'GUIA':
                $fecha = $xmlEnvio->infoGuiaRemision->fechaIniTransporte;
                $nombreArchivo = "GUIA-".$xmlEnvio->infoTributaria->estab. "-".$xmlEnvio->infoTributaria->ptoEmi."-".$xmlEnvio->infoTributaria->secuencial;
                break;
        }
        $fechaAutorizacion = $xml['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'];
        $ambiente = $xml['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['ambiente'];
        $this->crearPDFdocElectronico($xmlEnvio, $fecha, $nombreArchivo, $empresa, $fechaAutorizacion, $ambiente, $tipoDocumento);
        foreach ($xmlEnvio->infoAdicional->campoAdicional as $adicional){
            if($adicional['nombre']== 'Email'){
                if($adicional !='SIN CORREO'){
                     $this->enviarCorreoCliente($adicional, $fecha, $nombreArchivo,$empresa);
                }
            }
        }  
        return $nombreArchivo;
    }   
    
    function crearPDFdocElectronico($xml, $fecha, $nombreArchivo,$empresa, $fechaAutorizacion, $ambiente, $tipoDocumento) 
    {
        switch ($tipoDocumento) {
            case 'FACTURA':
                $view =  \View::make('admin.formatosPDF.facturacionElectronica.facturaElectronica', ['xml'=> $xml, 'logo'=> $empresa->empresa_logo, 'fechaAutorizacion'=>strstr($fechaAutorizacion, 'T', true), 'horaAutorizacion'=> str_replace('-05:00','',str_replace('T','',strstr($fechaAutorizacion, 'T'))), 'ambiente'=>$ambiente]);
                break;
            case 'RETENCION':
                $view =  \View::make('admin.formatosPDF.facturacionElectronica.retencionElectronica', ['xml'=> $xml, 'logo'=> $empresa->empresa_logo, 'fechaAutorizacion'=>strstr($fechaAutorizacion, 'T', true), 'horaAutorizacion'=> str_replace('-05:00','',str_replace('T','',strstr($fechaAutorizacion, 'T'))), 'ambiente'=>$ambiente]);
                break;
            case 'NC':
                $view =  \View::make('admin.formatosPDF.facturacionElectronica.notaCreditoElectronica', ['xml'=> $xml, 'logo'=> $empresa->empresa_logo, 'fechaAutorizacion'=>strstr($fechaAutorizacion, 'T', true), 'horaAutorizacion'=> str_replace('-05:00','',str_replace('T','',strstr($fechaAutorizacion, 'T'))), 'ambiente'=>$ambiente]);
                break;
            case 'ND':
                $view =  \View::make('admin.formatosPDF.facturacionElectronica.notaDebitoElectronica', ['xml'=> $xml, 'logo'=> $empresa->empresa_logo, 'fechaAutorizacion'=>strstr($fechaAutorizacion, 'T', true), 'horaAutorizacion'=> str_replace('-05:00','',str_replace('T','',strstr($fechaAutorizacion, 'T'))), 'ambiente'=>$ambiente]);
                break;
            case 'LC':
                $view =  \View::make('admin.formatosPDF.facturacionElectronica.liquidacionCompraElectronica', ['xml'=> $xml, 'logo'=> $empresa->empresa_logo, 'fechaAutorizacion'=>strstr($fechaAutorizacion, 'T', true), 'horaAutorizacion'=> str_replace('-05:00','',str_replace('T','',strstr($fechaAutorizacion, 'T'))), 'ambiente'=>$ambiente]);
                break;
            case 'GUIA':
                $view =  \View::make('admin.formatosPDF.facturacionElectronica.guiaRemisionElectronica', ['xml'=> $xml, 'logo'=> $empresa->empresa_logo, 'fechaAutorizacion'=>strstr($fechaAutorizacion, 'T', true), 'horaAutorizacion'=> str_replace('-05:00','',str_replace('T','',strstr($fechaAutorizacion, 'T'))), 'ambiente'=>$ambiente]);
                break;
        }
        return PDF::loadHTML($view)->save('documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('d/m/Y', $fecha)->format('d-m-Y').'/'.$nombreArchivo.'.pdf')->stream($nombreArchivo.'.pdf');
    }
    public function enviarCorreoCliente($mailCli, $fecha, $fileName,$empresa){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP(); 
            $mail->CharSet = 'utf-8'; 
            $mail->Host = trim($empresa->emailEmpresa->email_servidor);
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';//$mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
            $mail->Port = trim($empresa->emailEmpresa->email_puerto); 
            $mail->Username = trim($empresa->emailEmpresa->email_email);
            $mail->Password = trim(($empresa->emailEmpresa->email_pass));
            $mail->setFrom(trim($empresa->emailEmpresa->email_email), $empresa->empresa_nombreComercial);
            $mail->Subject = $empresa->empresa_nombreComercial.' - Documento electronico '.$fileName;
            //$mail->MsgHTML($empresa->emailEmpresa->email_mensaje.'<br><br><img src="'. $image .'" alt="BANNER"  width="650"><br>');
            $mail->MsgHTML($empresa->emailEmpresa->email_mensaje);
            $correos = explode(";",$mailCli);
            foreach ($correos as $correo) {
                $mail->addAddress(trim($correo),'');
            }
            if(count($correos) == 0 ){
                $mail->addAddress(trim($mailCli),'');
            }
            $mail->addAttachment('documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('d/m/Y', $fecha)->format('d-m-Y').'/'.$fileName.'.pdf', $fileName.'.pdf');
            $mail->addAttachment('documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('d/m/Y', $fecha)->format('d-m-Y').'/'.$fileName.'.xml', $fileName.'.xml');
            $mail->SMTPOptions= array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            );
            $mail->send();
        } catch (Exception $e) {
         //   dd($e);
        }
    }
    function firmarDoc($archivo, $firmaElectronica){
        if(strpos($archivo, "</factura>")){
            $tipoDoc="</factura>";
        }
        if(strpos($archivo, "</comprobanteRetencion>")){
            $tipoDoc="</comprobanteRetencion>";
        }
        if(strpos($archivo, "</notaCredito>")){
            $tipoDoc="</notaCredito>";
        }
        if(strpos($archivo, "</notaDebito>")){
            $tipoDoc="</notaDebito>";
        }
        if(strpos($archivo, "</guiaRemision>")){
            $tipoDoc="</guiaRemision>";
        }
        if(strpos($archivo, "</liquidacionCompra>")){
            $tipoDoc="</liquidacionCompra>";
        }
        $xmlns = 'xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:etsi="http://uri.etsi.org/01903/v1.3.2#"';
        $pubKey =Crypt::decryptString($firmaElectronica->firma_pubKey);
        $privKey=Crypt::decryptString($firmaElectronica->firma_privKey);
        
        $pub_key = openssl_pkey_get_public($pubKey);
        $pubKey_detalles = openssl_pkey_get_details($pub_key);
        $modulus = base64_encode($pubKey_detalles['rsa']['n']);
        $exponent = base64_encode($pubKey_detalles['rsa']['e']);
        
        $key = openssl_pkey_get_private($privKey);

        $data=openssl_x509_parse($pubKey,true);
        $emisor = "CN=".$data['issuer']['CN'].",OU=".$data['issuer']['OU'].",O=".$data['issuer']['O'].",C=".$data['issuer']['C'];
        $X509SerialNumber = $data['serialNumber'];
        $certlist = $this->obtener_Certificate($pubKey);
        $certificateX509 = $certlist['Certificate'];
        $certificateX509_der_hash = $this->_extractBER($certificateX509);
        $certificateX509_der_hash = base64_encode(sha1($certificateX509_der_hash,true));

        //numeros involucrados en los hash:
        //var Certificate_number = 1217155;//p_obtener_aleatorio(); //1562780 en el ejemplo del SRI
        $Certificate_number = $this->p_obtener_aleatorio(); //1562780 en el ejemplo del SRI
        //var Signature_number = 1021879;//p_obtener_aleatorio(); //620397 en el ejemplo del SRI
        $Signature_number = $this->p_obtener_aleatorio(); //620397 en el ejemplo del SRI
        //var SignedProperties_number = 1006287;//p_obtener_aleatorio(); //24123 en el ejemplo del SRI
        $SignedProperties_number = $this->p_obtener_aleatorio(); //24123 en el ejemplo del SRI
        //numeros fuera de los hash:
        //var SignedInfo_number = 696603;//p_obtener_aleatorio(); //814463 en el ejemplo del SRI
        $SignedInfo_number = $this->p_obtener_aleatorio(); //814463 en el ejemplo del SRI
        //var SignedPropertiesID_number = 77625;//p_obtener_aleatorio(); //157683 en el ejemplo del SRI
        $SignedPropertiesID_number = $this->p_obtener_aleatorio(); //157683 en el ejemplo del SRI
        //var Reference_ID_number = 235824;//p_obtener_aleatorio(); //363558 en el ejemplo del SRI
        $Reference_ID_number = $this->p_obtener_aleatorio(); //363558 en el ejemplo del SRI
        //var SignatureValue_number = 844709;//p_obtener_aleatorio(); //398963 en el ejemplo del SRI
        $SignatureValue_number = $this->p_obtener_aleatorio(); //398963 en el ejemplo del SRI
        //var Object_number = 621794;//p_obtener_aleatorio(); //231987 en el ejemplo del SRI
        $Object_number = $this->p_obtener_aleatorio(); //231987 en el ejemplo del SRI
        $doc = new \DOMDocument();
        $doc->loadXML(str_replace('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', "", $archivo));
        $sha1_comprobante= base64_encode(sha1($doc->C14N(),true)); 

        $SignedProperties = '';
        $SignedProperties .= '<etsi:SignedProperties Id="Signature' . $Signature_number . '-SignedProperties' . $SignedProperties_number . '">';  //SignedProperties
        $SignedProperties .= '<etsi:SignedSignatureProperties>';
            $SignedProperties .= '<etsi:SigningTime>';
                //SignedProperties .= '2016-12-24T13:46:43-05:00';
                $dt = new \DateTime();
                $dt->setTimeZone(new \DateTimeZone('America/Guayaquil'));
                $SignedProperties .= $dt->format('Y-m-d\TH:i:sP');
            $SignedProperties .= '</etsi:SigningTime>';
            $SignedProperties .= '<etsi:SigningCertificate>';
                $SignedProperties .= '<etsi:Cert>';
                    $SignedProperties .= '<etsi:CertDigest>';
                        $SignedProperties .= '<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1">';
                        $SignedProperties .= '</ds:DigestMethod>';
                        $SignedProperties .= '<ds:DigestValue>';
                            $SignedProperties .= $certificateX509_der_hash;
                        $SignedProperties .= '</ds:DigestValue>';
                    $SignedProperties .= '</etsi:CertDigest>';
                    $SignedProperties .= '<etsi:IssuerSerial>';
                        $SignedProperties .= '<ds:X509IssuerName>';
                           // $SignedProperties .= 'CN=AC BANCO CENTRAL DEL ECUADOR,L=QUITO,OU=ENTIDAD DE CERTIFICACION DE INFORMACION-ECIBCE,O=BANCO CENTRAL DEL ECUADOR,C=EC';
                            $SignedProperties .=$emisor;
                          //  $SignedProperties .= 'CN=AUTORIDAD DE CERTIFICACION SUB SECURITY DATA,OU=ENTIDAD DE CERTIFICACION DE INFORMACION,O=SECURITY DATA S.A.,C=EC';
                        $SignedProperties .= '</ds:X509IssuerName>';
                    $SignedProperties .= '<ds:X509SerialNumber>';
                        $SignedProperties .= $X509SerialNumber;
                    $SignedProperties .= '</ds:X509SerialNumber>';
                    $SignedProperties .= '</etsi:IssuerSerial>';
                $SignedProperties .= '</etsi:Cert>';
            $SignedProperties .= '</etsi:SigningCertificate>';
        $SignedProperties .= '</etsi:SignedSignatureProperties>';
        $SignedProperties .= '<etsi:SignedDataObjectProperties>';
            $SignedProperties .= '<etsi:DataObjectFormat ObjectReference="#Reference-ID-' . $Reference_ID_number . '">';
                $SignedProperties .= '<etsi:Description>';
                    $SignedProperties .= 'contenido comprobante';                        
                $SignedProperties .= '</etsi:Description>';
                $SignedProperties .= '<etsi:MimeType>';
                    $SignedProperties .= 'text/xml';
                $SignedProperties .= '</etsi:MimeType>';
            $SignedProperties .= '</etsi:DataObjectFormat>';
        $SignedProperties .= '</etsi:SignedDataObjectProperties>';
        $SignedProperties .= '</etsi:SignedProperties>'; //fin SignedProperties
        $SignedProperties_para_hash = str_replace('<etsi:SignedProperties', '<etsi:SignedProperties ' . $xmlns, $SignedProperties);
        $sha1_SignedProperties = base64_encode(sha1($SignedProperties_para_hash, true));        

        $KeyInfo = '';
        $KeyInfo .= '<ds:KeyInfo Id="Certificate' . $Certificate_number . '">';
        $KeyInfo .= "\n".'<ds:X509Data>';
            $KeyInfo .= "\n".'<ds:X509Certificate>'."\n";
                //CERTIFICADO X509 CODIFICADO EN Base64 
                $KeyInfo .= $certificateX509;
            $KeyInfo .= '</ds:X509Certificate>';
        $KeyInfo .= "\n".'</ds:X509Data>';
        $KeyInfo .= "\n".'<ds:KeyValue>';
            $KeyInfo .= "\n".'<ds:RSAKeyValue>';
                $KeyInfo .= "\n".'<ds:Modulus>'."\n";
                    //MODULO DEL CERTIFICADO X509
                    $KeyInfo .= $modulus;
                $KeyInfo .= "\n".'</ds:Modulus>';
                $KeyInfo .= "\n".'<ds:Exponent>';
                    //KeyInfo .= 'AQAB';
                    $KeyInfo .= $exponent;
                $KeyInfo .= '</ds:Exponent>';
            $KeyInfo .= "\n".'</ds:RSAKeyValue>';
        $KeyInfo .= "\n".'</ds:KeyValue>';
        $KeyInfo .= "\n".'</ds:KeyInfo>';
        $KeyInfo_para_hash = str_replace('<ds:KeyInfo', '<ds:KeyInfo ' . $xmlns, $KeyInfo);
        $doc = new \DOMDocument();
        $doc->loadXML($KeyInfo_para_hash);
        $sha1_certificado = base64_encode(sha1($doc->C14N(), true));

        $SignedInfo = '';
        $SignedInfo .= '<ds:SignedInfo Id="Signature-SignedInfo' . $SignedInfo_number . '">';
            $SignedInfo .= "\n".'<ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315">';
            $SignedInfo .= '</ds:CanonicalizationMethod>';
            $SignedInfo .= "\n".'<ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1">';
            $SignedInfo .= '</ds:SignatureMethod>';
            $SignedInfo .= "\n".'<ds:Reference Id="SignedPropertiesID' . $SignedPropertiesID_number . '" Type="http://uri.etsi.org/01903#SignedProperties" URI="#Signature' . $Signature_number . '-SignedProperties' . $SignedProperties_number . '">';
                $SignedInfo .= "\n".'<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1">';
                $SignedInfo .= '</ds:DigestMethod>';
                $SignedInfo .= "\n".'<ds:DigestValue>';
                    //HASH O DIGEST DEL ELEMENTO <etsi:SignedProperties>';
                    $SignedInfo .= $sha1_SignedProperties;
                $SignedInfo .= '</ds:DigestValue>';
            $SignedInfo .= "\n".'</ds:Reference>';
            $SignedInfo .= "\n".'<ds:Reference URI="#Certificate' . $Certificate_number . '">';
                $SignedInfo .= "\n".'<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1">';
                $SignedInfo .= '</ds:DigestMethod>';
                $SignedInfo .= "\n".'<ds:DigestValue>';
                    //HASH O DIGEST DEL CERTIFICADO X509
                    $SignedInfo .= $sha1_certificado;

                $SignedInfo .= '</ds:DigestValue>';
            $SignedInfo .= "\n".'</ds:Reference>';
            $SignedInfo .= "\n".'<ds:Reference Id="Reference-ID-' . $Reference_ID_number . '" URI="#comprobante">';
                $SignedInfo .= "\n".'<ds:Transforms>';
                    $SignedInfo .= "\n".'<ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature">';
                    $SignedInfo .= '</ds:Transform>';
                $SignedInfo .= "\n".'</ds:Transforms>';
                $SignedInfo .= "\n".'<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1">';
                $SignedInfo .= '</ds:DigestMethod>';
                $SignedInfo .= "\n".'<ds:DigestValue>';
                    //HASH O DIGEST DE TODO EL ARCHIVO XML IDENTIFICADO POR EL id="comprobante" ;
                    $SignedInfo .= $sha1_comprobante;
                $SignedInfo .= '</ds:DigestValue>';
            $SignedInfo .= "\n".'</ds:Reference>';
        $SignedInfo .= "\n".'</ds:SignedInfo>';
        $SignedInfo_para_firma = str_replace('<ds:SignedInfo', '<ds:SignedInfo ' . $xmlns, $SignedInfo);

        $doc = new \DOMDocument();
        $doc->loadXML($SignedInfo_para_firma);
        openssl_sign($doc->C14N(), $signature, $key, 'SHA1');
        $signature = base64_encode($signature);

        $xades_bes = '';
        //INICIO DE LA FIRMA DIGITAL 
        $xades_bes .= '<ds:Signature ' . $xmlns . ' Id="Signature' . $Signature_number . '">';
            $xades_bes .= "\n".$SignedInfo;
            $xades_bes .= "\n".'<ds:SignatureValue Id="SignatureValue' . $SignatureValue_number . '">'."\n";
                //VALOR DE LA FIRMA (ENCRIPTADO CON LA LLAVE PRIVADA DEL CERTIFICADO DIGITAL) 
                $xades_bes .= $signature;
            $xades_bes .= "\n".'</ds:SignatureValue>';
            $xades_bes .= "\n".$KeyInfo;
            $xades_bes .= "\n".'<ds:Object Id="Signature' . $Signature_number . '-Object' . $Object_number . '">';
                $xades_bes .= '<etsi:QualifyingProperties Target="#Signature' . $Signature_number . '">';
                    //ELEMENTO <etsi:SignedProperties>';
                    $xades_bes .= $SignedProperties;
                $xades_bes .= '</etsi:QualifyingProperties>';
            $xades_bes .= '</ds:Object>';
        $xades_bes .= '</ds:Signature>';

        $combprobanteFirmado = str_replace(' standalone="yes"', '', $archivo);
        $combprobanteFirmado = str_replace($tipoDoc, $xades_bes.$tipoDoc, $combprobanteFirmado);
        //FIN DE LA FIRMA DIGITAL 
        return $combprobanteFirmado;         
    }
    function p_obtener_aleatorio() {
        return floor($this->randomFloat() * 999000) + 990;    
    }
    function randomFloat($min = 0, $max = 1) {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
    function obtener_Certificate($pubKey){
        $beginCertificate = '-----BEGIN CERTIFICATE-----';
        $endCertificate = '-----END CERTIFICATE-----';
        $data = '';
        $certlist = array();
        $inData = FALSE;
        $i = 0;
        $arCert = explode("\n", $pubKey);
        foreach ($arCert as $curData) {
            if (!$inData) {
                if (strncmp($curData, $beginCertificate, 27) === 0) {
                    $inData = true;
                }
            } else {
                if (strncmp($curData, $endCertificate, 25) === 0) {
                    $inData = false;
                    $certlist[$i]['Certificate'] = $data;
                    $data = '';
                    $i++;
                    continue;
                }
                $data .= trim($curData) . PHP_EOL;
            }
        }
        foreach ($certlist as $key => $certificateData) {
            if (empty($certificateData['Certificate'])) {
                unset($certlist[$key]);
                continue;
            }
            $certicate = $beginCertificate . PHP_EOL .
                         $certificateData['Certificate'] .
                         $endCertificate;
        }
        return $certlist[0];
    }
    function _extractBER($str)
    {
        $temp = preg_replace('#.*?^-+[^-]+-+#ms', '', $str, 1);
        // remove the -----BEGIN CERTIFICATE----- and -----END CERTIFICATE----- stuff
        $temp = preg_replace('#-+[^-]+-+#', '', $temp);
        // remove new lines
        $temp = str_replace(array("\r", "\n", ' '), '', $temp);
        $temp = preg_match('#^[a-zA-Z\d/+]*={0,2}$#', $temp) ? base64_decode($temp) : false;
        return $temp != false ? $temp : $str;
    }
    function xmlFactura(Factura_Venta $factura){
        $empresa = Empresa::empresa()->first();
        $xml = xmlwriter_open_memory();
        xmlwriter_set_indent($xml, 1);
        $res = xmlwriter_set_indent_string($xml, ' ');
        //inicio del documento
        xmlwriter_start_document($xml, '1.0', 'UTF-8');

        //primer elemento
        xmlwriter_start_element($xml, 'factura');

        // atributo 'id' del elemento 'factura'
        xmlwriter_start_attribute($xml, 'id');
        xmlwriter_text($xml, 'comprobante');
        xmlwriter_end_attribute($xml);

        // atributo 'version' del elemento 'factura'
        xmlwriter_start_attribute($xml, 'version');
        xmlwriter_text($xml, '1.1.0');
        xmlwriter_end_attribute($xml);

        // Elemento hijo de 'factura'
        xmlwriter_start_element($xml, 'infoTributaria');
            /**********************/
            xmlwriter_start_element($xml, 'ambiente');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_ambiente);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoEmision');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_disponibilidad);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocial');
            xmlwriter_text($xml, $empresa->empresa_razonSocial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'nombreComercial');
            xmlwriter_text($xml, $empresa->empresa_nombreComercial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ruc');
            xmlwriter_text($xml, $empresa->empresa_ruc);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'claveAcceso');
            xmlwriter_text($xml, $factura->factura_autorizacion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'codDoc');
            xmlwriter_text($xml, '01');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'estab');
            xmlwriter_text($xml, $factura->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ptoEmi');
            xmlwriter_text($xml, $factura->rangoDocumento->puntoEmision->punto_serie);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'secuencial');//
            xmlwriter_text($xml, substr(str_repeat(0, 9).$factura->factura_secuencial, - 9));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirMatriz');
            xmlwriter_text($xml, $empresa->empresa_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Agente de Retención'){
                xmlwriter_start_element($xml, 'agenteRetencion');
                xmlwriter_text($xml, '1');
                xmlwriter_end_element($xml); 
            }elseif($empresa->empresa_tipo == 'Microempresas'){
                xmlwriter_start_element($xml, 'regimenMicroempresas');
                xmlwriter_text($xml, 'CONTRIBUYENTE RÉGIMEN MICROEMPRESAS');
                xmlwriter_end_element($xml); 
            }elseif($empresa->empresa_tipo == 'Contribuyente Régimen Rimpe'){
                xmlwriter_start_element($xml, 'contribuyenteRimpe');
                xmlwriter_text($xml, 'CONTRIBUYENTE RÉGIMEN RIMPE');
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoTributaria'

        // Elemento hijo de 'factura'
        xmlwriter_start_element($xml, 'infoFactura');
            /**********************/
            xmlwriter_start_element($xml, 'fechaEmision');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirEstablecimiento');
            xmlwriter_text($xml, $factura->bodega->sucursal->sucursal_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Contribuyente Especial'){
                xmlwriter_start_element($xml, 'contribuyenteEspecial');
                xmlwriter_text($xml, $empresa->empresa_contribuyenteEspecial);
                xmlwriter_end_element($xml); 
            }
            /**********************/
            xmlwriter_start_element($xml, 'obligadoContabilidad');
            if($empresa->empresa_llevaContabilidad == 1){
                xmlwriter_text($xml, 'SI');
            }else{
                xmlwriter_text($xml, 'NO');
            }
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoIdentificacionComprador');
            xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($factura->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocialComprador');
            xmlwriter_text($xml, $factura->cliente->cliente_nombre);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'identificacionComprador');
            xmlwriter_text($xml, $factura->cliente->cliente_cedula);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'direccionComprador');
            xmlwriter_text($xml, $factura->cliente->cliente_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalSinImpuestos');
            xmlwriter_text($xml, number_format(($factura->factura_tarifa12+$factura->factura_tarifa0),2, '.', ''));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalDescuento');
            xmlwriter_text($xml, number_format($factura->factura_descuento,2, '.', ''));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalConImpuestos');
                /**********************/
                if($factura->factura_tarifa0 > 0){
                    xmlwriter_start_element($xml, 'totalImpuesto');
                        /**********************/
                        xmlwriter_start_element($xml, 'codigo');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'codigoPorcentaje');
                        xmlwriter_text($xml, '0');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'baseImponible');
                        xmlwriter_text($xml, number_format($factura->factura_tarifa0,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'valor');
                        xmlwriter_text($xml, '0.00');
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                }
                /**********************/
                if($factura->factura_tarifa12 > 0){
                    xmlwriter_start_element($xml, 'totalImpuesto');
                        /**********************/
                        xmlwriter_start_element($xml, 'codigo');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'codigoPorcentaje');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'baseImponible');
                        xmlwriter_text($xml, number_format($factura->factura_tarifa12,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'valor');
                        xmlwriter_text($xml, number_format($factura->factura_iva,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                }
                /**********************/
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'propina');
            xmlwriter_text($xml, '0.00');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'importeTotal');
            xmlwriter_text($xml, number_format($factura->factura_total,2, '.', ''));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'moneda');
            xmlwriter_text($xml, 'DOLAR');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'pagos');
                /**********************/
                xmlwriter_start_element($xml, 'pago');
                    /**********************/
                    xmlwriter_start_element($xml, 'formaPago');
                    xmlwriter_text($xml, $factura->formaPago->forma_pago_codigo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'total');
                    xmlwriter_text($xml, number_format($factura->factura_total,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'plazo');
                    xmlwriter_text($xml, $factura->factura_dias_plazo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'unidadTiempo');
                    xmlwriter_text($xml, 'dias');
                    xmlwriter_end_element($xml); 
                    /**********************/
                xmlwriter_end_element($xml); 
                /**********************/
            xmlwriter_end_element($xml); 
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoFactura'

        // Elemento hijo de 'factura'
        xmlwriter_start_element($xml, 'detalles');
            /**********************/
            foreach($factura->detalles as $detalle){
                xmlwriter_start_element($xml, 'detalle');
                    /**********************/
                    xmlwriter_start_element($xml, 'codigoPrincipal');
                    xmlwriter_text($xml, $detalle->producto->producto_codigo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'descripcion');
                    xmlwriter_text($xml, $detalle->detalle_descripcion);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'cantidad');
                    xmlwriter_text($xml, $detalle->detalle_cantidad);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'precioUnitario');
                    xmlwriter_text($xml, number_format($detalle->detalle_precio_unitario,6, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'descuento');
                    xmlwriter_text($xml, number_format($detalle->detalle_descuento,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'precioTotalSinImpuesto');
                    xmlwriter_text($xml, number_format($detalle->detalle_total,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'impuestos');
                        /**********************/
                        xmlwriter_start_element($xml, 'impuesto');
                            /**********************/
                            xmlwriter_start_element($xml, 'codigo');
                            xmlwriter_text($xml, '2');
                            xmlwriter_end_element($xml); 
                            /**********************/
                            if($detalle->detalle_iva > 0){
                                xmlwriter_start_element($xml, 'codigoPorcentaje');
                                xmlwriter_text($xml, '2');//revisar
                                xmlwriter_end_element($xml); 
                                /**********************/
                                xmlwriter_start_element($xml, 'tarifa');
                                xmlwriter_text($xml, '12');
                                xmlwriter_end_element($xml);
                            }else{
                                xmlwriter_start_element($xml, 'codigoPorcentaje');
                                xmlwriter_text($xml, '0');
                                xmlwriter_end_element($xml); 
                                /**********************/
                                xmlwriter_start_element($xml, 'tarifa');
                                xmlwriter_text($xml, '0');
                                xmlwriter_end_element($xml);
                            }  
                            /**********************/
                            xmlwriter_start_element($xml, 'baseImponible');
                            xmlwriter_text($xml, number_format($detalle->detalle_total,2, '.', ''));
                            xmlwriter_end_element($xml); 
                            /**********************/
                            xmlwriter_start_element($xml, 'valor');
                            xmlwriter_text($xml, number_format($detalle->detalle_iva,2, '.', ''));
                            xmlwriter_end_element($xml); 
                            /**********************/
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                    /**********************/
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'detalles'

        // Elemento hijo de 'factura'
        xmlwriter_start_element($xml, 'infoAdicional');
            /**********************/
            xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Email');
                xmlwriter_end_attribute($xml);
                /**********************/
            if(empty($factura->cliente->cliente_email)){
                xmlwriter_text($xml, 'SIN CORREO');
            }else{
                xmlwriter_text($xml, $factura->cliente->cliente_email);
            }
            xmlwriter_end_element($xml); 
            /**********************/
            if(!empty($factura->factura_comentario)){
                xmlwriter_start_element($xml, 'campoAdicional');
                    /**********************/
                    xmlwriter_start_attribute($xml, 'nombre');
                    xmlwriter_text($xml, 'Observaciones');
                    xmlwriter_end_attribute($xml);
                    /**********************/
                xmlwriter_text($xml, $factura->factura_comentario);
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoAdicional'

        xmlwriter_end_element($xml); 
        // final 'factura'

        xmlwriter_end_document($xml); 
        // final del documento
        $ruta = public_path().'/documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $xmlFinal = xmlwriter_output_memory($xml);
        $nombreArchivo = "FAC-".$factura->rangoDocumento->puntoEmision->sucursal->sucursal_codigo. "-".$factura->rangoDocumento->puntoEmision->punto_serie."-" . substr(str_repeat(0, 9).$factura->factura_secuencial, - 9). ".xml";
        $archivo = fopen($ruta.'/'.$nombreArchivo, 'w');
        fwrite($archivo, $xmlFinal);
        fclose($archivo);
        return $xmlFinal;
    }

    public function xmlRetencion(Retencion_Compra $retencion){
        $empresa = Empresa::empresa()->first();
        $xml = xmlwriter_open_memory();
        xmlwriter_set_indent($xml, 1);
        $res = xmlwriter_set_indent_string($xml, ' ');
        //inicio del documento
        xmlwriter_start_document($xml, '1.0', 'UTF-8');

        //primer elemento
        xmlwriter_start_element($xml, 'comprobanteRetencion');

        // atributo 'id' del elemento 'retencion'
        xmlwriter_start_attribute($xml, 'id');
        xmlwriter_text($xml, 'comprobante');
        xmlwriter_end_attribute($xml);

        // atributo 'version' del elemento 'retencion'
        xmlwriter_start_attribute($xml, 'version');
        xmlwriter_text($xml, '1.0.0');
        xmlwriter_end_attribute($xml);

        // Elemento hijo de 'retencion'
        xmlwriter_start_element($xml, 'infoTributaria');
            /**********************/
            xmlwriter_start_element($xml, 'ambiente');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_ambiente);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoEmision');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_disponibilidad);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocial');
            xmlwriter_text($xml, $empresa->empresa_razonSocial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'nombreComercial');
            xmlwriter_text($xml, $empresa->empresa_nombreComercial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ruc');
            xmlwriter_text($xml, $empresa->empresa_ruc);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'claveAcceso');
            xmlwriter_text($xml, $retencion->retencion_autorizacion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'codDoc');
            xmlwriter_text($xml, '07');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'estab');
            xmlwriter_text($xml, $retencion->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ptoEmi');
            xmlwriter_text($xml, $retencion->rangoDocumento->puntoEmision->punto_serie);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'secuencial');//
            xmlwriter_text($xml, substr(str_repeat(0, 9).$retencion->retencion_secuencial, - 9));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirMatriz');
            xmlwriter_text($xml, $empresa->empresa_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Agente de Retención'){
                xmlwriter_start_element($xml, 'agenteRetencion');
                xmlwriter_text($xml, '1');
                xmlwriter_end_element($xml); 
            }elseif($empresa->empresa_tipo == 'Microempresas'){
                xmlwriter_start_element($xml, 'regimenMicroempresas');
                xmlwriter_text($xml, 'CONTRIBUYENTE RÉGIMEN MICROEMPRESAS');
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoTributaria'

        // Elemento hijo de 'retencion'
        xmlwriter_start_element($xml, 'infoCompRetencion');
            /**********************/
            xmlwriter_start_element($xml, 'fechaEmision');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $retencion->retencion_fecha)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirEstablecimiento');
            xmlwriter_text($xml, $retencion->rangoDocumento->puntoEmision->sucursal->sucursal_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Contribuyente Especial'){
                xmlwriter_start_element($xml, 'contribuyenteEspecial');
                xmlwriter_text($xml, $empresa->empresa_contribuyenteEspecial);
                xmlwriter_end_element($xml); 
            }
            /**********************/
            xmlwriter_start_element($xml, 'obligadoContabilidad');
            if($empresa->empresa_llevaContabilidad == 1){
                xmlwriter_text($xml, 'SI');
            }else{
                xmlwriter_text($xml, 'NO');
            }
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoIdentificacionSujetoRetenido');
            if($retencion->transaccionCompra){
                xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($retencion->transaccionCompra->proveedor->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
            }
            if($retencion->liquidacionCompra){
                xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($retencion->liquidacionCompra->proveedor->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
            }
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocialSujetoRetenido');
            if($retencion->transaccionCompra){
                xmlwriter_text($xml, $retencion->transaccionCompra->proveedor->proveedor_nombre);
            }
            if($retencion->liquidacionCompra){
                xmlwriter_text($xml, $retencion->liquidacionCompra->proveedor->proveedor_nombre);
            }
            
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'identificacionSujetoRetenido');
            if($retencion->transaccionCompra){
                xmlwriter_text($xml, $retencion->transaccionCompra->proveedor->proveedor_ruc);
            }
            if($retencion->liquidacionCompra){
                xmlwriter_text($xml, $retencion->liquidacionCompra->proveedor->proveedor_ruc);
            }
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'periodoFiscal');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $retencion->retencion_fecha)->format('m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoCompRetencion'

        // Elemento hijo de 'retencion'
        xmlwriter_start_element($xml, 'impuestos');
            /**********************/
            foreach($retencion->detalles as $detalle){
                xmlwriter_start_element($xml, 'impuesto');
                    /**********************/
                    xmlwriter_start_element($xml, 'codigo');
                    if($detalle->detalle_tipo == "IVA"){
                        xmlwriter_text($xml, "2");
                    }else{
                        xmlwriter_text($xml, "1");
                    }
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'codigoRetencion');
                    xmlwriter_text($xml, $detalle->conceptoRetencion->concepto_codigo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'baseImponible');
                    xmlwriter_text($xml, number_format($detalle->detalle_base, 2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'porcentajeRetener');
                    xmlwriter_text($xml, $detalle->detalle_porcentaje);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'valorRetenido');
                    xmlwriter_text($xml, number_format($detalle->detalle_valor, 2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'codDocSustento');
                    if($retencion->transaccionCompra){
                        xmlwriter_text($xml, $retencion->transaccionCompra->tipoComprobante->tipo_comprobante_codigo);
                    }else{
                        xmlwriter_text($xml, '03');
                    }
                    
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'numDocSustento');
                    if($retencion->transaccionCompra){
                        xmlwriter_text($xml, $retencion->transaccionCompra->transaccion_numero);
                    }
                    if($retencion->liquidacionCompra){
                        xmlwriter_text($xml, $retencion->liquidacionCompra->lc_numero);
                    }
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'fechaEmisionDocSustento');
                    if($retencion->transaccionCompra){
                        xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $retencion->transaccionCompra->transaccion_fecha)->format('d/m/Y'));
                    }
                    if($retencion->liquidacionCompra){
                        xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $retencion->liquidacionCompra->lc_fecha)->format('d/m/Y'));
                    }
                    xmlwriter_end_element($xml); 
                    /**********************/
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'detalles'

        // Elemento hijo de 'retencion'
        xmlwriter_start_element($xml, 'infoAdicional');
            /**********************/
            xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Dirección');
                xmlwriter_end_attribute($xml);
                if($retencion->transaccionCompra){
                    xmlwriter_text($xml, $retencion->transaccionCompra->proveedor->proveedor_direccion);
                }
                if($retencion->liquidacionCompra){
                    xmlwriter_text($xml, $retencion->liquidacionCompra->proveedor->proveedor_direccion);
                }
                /**********************/
            xmlwriter_end_element($xml);
            /**********************/
            xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Email');
                xmlwriter_end_attribute($xml);
                /**********************/
            if($retencion->transaccionCompra){
                if(empty($retencion->transaccionCompra->proveedor->proveedor_email)){
                    xmlwriter_text($xml, 'SIN CORREO');
                }else{
                    xmlwriter_text($xml, $retencion->transaccionCompra->proveedor->proveedor_email);
                }
            }
            if($retencion->liquidacionCompra){
                if(empty($retencion->liquidacionCompra->proveedor->proveedor_email)){
                    xmlwriter_text($xml, 'SIN CORREO');
                }else{
                    xmlwriter_text($xml, $retencion->liquidacionCompra->proveedor->proveedor_email);
                }
            }
            xmlwriter_end_element($xml); 
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoAdicional'

        xmlwriter_end_element($xml); 
        // final 'retencion'

        xmlwriter_end_document($xml); 
        // final del documento
        $ruta = public_path().'/documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $retencion->retencion_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $xmlFinal = xmlwriter_output_memory($xml);
        $nombreArchivo = "RET-".$retencion->rangoDocumento->puntoEmision->sucursal->sucursal_codigo. "-".$retencion->rangoDocumento->puntoEmision->punto_serie."-" . substr(str_repeat(0, 9).$retencion->retencion_secuencial, - 9). ".xml";
        $archivo = fopen($ruta.'/'.$nombreArchivo, 'w');
        fwrite($archivo, $xmlFinal);
        fclose($archivo);
        return $xmlFinal;
    }
    public function xmlNotaCredito(Nota_Credito $nc){
        $empresa = Empresa::empresa()->first();
        $xml = xmlwriter_open_memory();
        xmlwriter_set_indent($xml, 1);
        $res = xmlwriter_set_indent_string($xml, ' ');
        //inicio del documento
        xmlwriter_start_document($xml, '1.0', 'UTF-8');

        //primer elemento
        xmlwriter_start_element($xml, 'notaCredito');

        // atributo 'id' del elemento 'nc'
        xmlwriter_start_attribute($xml, 'id');
        xmlwriter_text($xml, 'comprobante');
        xmlwriter_end_attribute($xml);

        // atributo 'version' del elemento 'nc'
        xmlwriter_start_attribute($xml, 'version');
        xmlwriter_text($xml, '1.1.0');
        xmlwriter_end_attribute($xml);

        // Elemento hijo de 'nc'
        xmlwriter_start_element($xml, 'infoTributaria');
            /**********************/
            xmlwriter_start_element($xml, 'ambiente');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_ambiente);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoEmision');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_disponibilidad);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocial');
            xmlwriter_text($xml, $empresa->empresa_razonSocial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'nombreComercial');
            xmlwriter_text($xml, $empresa->empresa_nombreComercial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ruc');
            xmlwriter_text($xml, $empresa->empresa_ruc);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'claveAcceso');
            xmlwriter_text($xml, $nc->nc_autorizacion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'codDoc');
            xmlwriter_text($xml, '04');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'estab');
            xmlwriter_text($xml, $nc->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ptoEmi');
            xmlwriter_text($xml, $nc->rangoDocumento->puntoEmision->punto_serie);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'secuencial');//
            xmlwriter_text($xml, substr(str_repeat(0, 9).$nc->nc_secuencial, - 9));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirMatriz');
            xmlwriter_text($xml, $empresa->empresa_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Agente de Retención'){
                xmlwriter_start_element($xml, 'agenteRetencion');
                xmlwriter_text($xml, '1');
                xmlwriter_end_element($xml); 
            }elseif($empresa->empresa_tipo == 'Microempresas'){
                xmlwriter_start_element($xml, 'regimenMicroempresas');
                xmlwriter_text($xml, 'CONTRIBUYENTE RÉGIMEN MICROEMPRESAS');
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoTributaria'

        // Elemento hijo de 'nc'
        xmlwriter_start_element($xml, 'infoNotaCredito');
            /**********************/
            xmlwriter_start_element($xml, 'fechaEmision');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $nc->nc_fecha)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirEstablecimiento');
            xmlwriter_text($xml, $nc->rangoDocumento->puntoEmision->sucursal->sucursal_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoIdentificacionComprador');
            xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($nc->factura->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocialComprador');
            xmlwriter_text($xml, $nc->factura->cliente->cliente_nombre);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'identificacionComprador');
            xmlwriter_text($xml, $nc->factura->cliente->cliente_cedula);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Contribuyente Especial'){
                xmlwriter_start_element($xml, 'contribuyenteEspecial');
                xmlwriter_text($xml, $empresa->empresa_contribuyenteEspecial);
                xmlwriter_end_element($xml); 
            }
            /**********************/
            xmlwriter_start_element($xml, 'obligadoContabilidad');
            if($empresa->empresa_llevaContabilidad == 1){
                xmlwriter_text($xml, 'SI');
            }else{
                xmlwriter_text($xml, 'NO');
            }
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'codDocModificado');
            xmlwriter_text($xml, "01");//AUTOMATIZAR
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'numDocModificado');
            xmlwriter_text($xml, $nc->factura->rangoDocumento->puntoEmision->sucursal->sucursal_codigo.'-'.$nc->factura->rangoDocumento->puntoEmision->punto_serie.'-'.substr(str_repeat(0, 9).$nc->factura->factura_secuencial, - 9));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'fechaEmisionDocSustento');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $nc->factura->factura_fecha)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalSinImpuestos');
            xmlwriter_text($xml, number_format(($nc->nc_tarifa12+$nc->nc_tarifa0),2, '.', ''));
            xmlwriter_end_element($xml);  
            /**********************/
            xmlwriter_start_element($xml, 'valorModificacion');
            xmlwriter_text($xml, number_format($nc->nc_total,2, '.', ''));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'moneda');
            xmlwriter_text($xml, 'DOLAR');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalConImpuestos');
                /**********************/
                if($nc->nc_tarifa0 > 0){
                    xmlwriter_start_element($xml, 'totalImpuesto');
                        /**********************/
                        xmlwriter_start_element($xml, 'codigo');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'codigoPorcentaje');
                        xmlwriter_text($xml, '0');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'baseImponible');
                        xmlwriter_text($xml, number_format($nc->nc_tarifa0,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'valor');
                        xmlwriter_text($xml, '0.00');
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                }
                /**********************/
                if($nc->nc_tarifa12 > 0){
                    xmlwriter_start_element($xml, 'totalImpuesto');
                        /**********************/
                        xmlwriter_start_element($xml, 'codigo');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'codigoPorcentaje');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'baseImponible');
                        xmlwriter_text($xml, number_format($nc->nc_tarifa12,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'valor');
                        xmlwriter_text($xml, number_format($nc->nc_iva,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                }
                /**********************/
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'motivo');
            xmlwriter_text($xml, $nc->nc_comentario);
            xmlwriter_end_element($xml);  
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoNotaCredito'
        
        // Elemento hijo de 'nc'
        xmlwriter_start_element($xml, 'detalles');
            /**********************/
            foreach($nc->detalles as $detalle){
                xmlwriter_start_element($xml, 'detalle');
                    /**********************/
                    xmlwriter_start_element($xml, 'codigoInterno');
                    xmlwriter_text($xml, $detalle->producto->producto_codigo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'descripcion');
                    xmlwriter_text($xml, $detalle->producto->producto_nombre);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'cantidad');
                    xmlwriter_text($xml, $detalle->detalle_cantidad);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'precioUnitario');
                    xmlwriter_text($xml, number_format($detalle->detalle_precio_unitario,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'descuento');
                    xmlwriter_text($xml, number_format($detalle->detalle_descuento,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'precioTotalSinImpuesto');
                    xmlwriter_text($xml, number_format($detalle->detalle_total,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'impuestos');
                        /**********************/
                        xmlwriter_start_element($xml, 'impuesto');
                            /**********************/
                            xmlwriter_start_element($xml, 'codigo');
                            xmlwriter_text($xml, '2');
                            xmlwriter_end_element($xml); 
                            /**********************/
                            if($detalle->detalle_iva > 0){
                                xmlwriter_start_element($xml, 'codigoPorcentaje');
                                xmlwriter_text($xml, '2');//revisar
                                xmlwriter_end_element($xml); 
                                /**********************/
                                xmlwriter_start_element($xml, 'tarifa');
                                xmlwriter_text($xml, '12');
                                xmlwriter_end_element($xml);
                            }else{
                                xmlwriter_start_element($xml, 'codigoPorcentaje');
                                xmlwriter_text($xml, '0');
                                xmlwriter_end_element($xml); 
                                /**********************/
                                xmlwriter_start_element($xml, 'tarifa');
                                xmlwriter_text($xml, '0');
                                xmlwriter_end_element($xml);
                            }     
                            /**********************/
                            xmlwriter_start_element($xml, 'baseImponible');
                            xmlwriter_text($xml, number_format($detalle->detalle_total,2, '.', ''));
                            xmlwriter_end_element($xml); 
                            /**********************/
                            xmlwriter_start_element($xml, 'valor');
                            xmlwriter_text($xml, number_format($detalle->detalle_iva,2, '.', ''));
                            xmlwriter_end_element($xml); 
                            /**********************/
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                    /**********************/
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'detalles'

        // Elemento hijo de 'nc'
        xmlwriter_start_element($xml, 'infoAdicional');
            /**********************/
                xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Dirección');
                xmlwriter_end_attribute($xml);
                xmlwriter_text($xml, $nc->factura->cliente->cliente_direccion);
                /**********************/
            xmlwriter_end_element($xml);
            /**********************/
            xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Email');
                xmlwriter_end_attribute($xml);
                /**********************/
            if(empty($nc->factura->cliente->cliente_email)){
                xmlwriter_text($xml, 'SIN CORREO');
            }else{
                xmlwriter_text($xml, $nc->factura->cliente->cliente_email);
            }
            xmlwriter_end_element($xml); 
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoAdicional'

        xmlwriter_end_element($xml); 
        // final 'factura'

        xmlwriter_end_document($xml); 
        // final del documento
        $ruta = public_path().'/documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $nc->nc_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $xmlFinal = xmlwriter_output_memory($xml);
        $nombreArchivo = "NC-".$nc->rangoDocumento->puntoEmision->sucursal->sucursal_codigo. "-".$nc->rangoDocumento->puntoEmision->punto_serie."-" . substr(str_repeat(0, 9).$nc->nc_secuencial, - 9). ".xml";
        $archivo = fopen($ruta.'/'.$nombreArchivo, 'w');
        fwrite($archivo, $xmlFinal);
        fclose($archivo);
        return $xmlFinal;
    }
    public function xmlNotaDebito(Nota_Debito $nd){
        $empresa = Empresa::empresa()->first();
        $xml = xmlwriter_open_memory();
        xmlwriter_set_indent($xml, 1);
        $res = xmlwriter_set_indent_string($xml, ' ');
        //inicio del documento
        xmlwriter_start_document($xml, '1.0', 'UTF-8');

        //primer elemento
        xmlwriter_start_element($xml, 'notaDebito');

        // atributo 'version' del elemento 'nd'
        xmlwriter_start_attribute($xml, 'version');
        xmlwriter_text($xml, '1.0.0');
        xmlwriter_end_attribute($xml);

        // atributo 'id' del elemento 'nd'
        xmlwriter_start_attribute($xml, 'id');
        xmlwriter_text($xml, 'comprobante');
        xmlwriter_end_attribute($xml);

        // Elemento hijo de 'nd'
        xmlwriter_start_element($xml, 'infoTributaria');
            /**********************/
            xmlwriter_start_element($xml, 'ambiente');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_ambiente);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoEmision');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_disponibilidad);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocial');
            xmlwriter_text($xml, $empresa->empresa_razonSocial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'nombreComercial');
            xmlwriter_text($xml, $empresa->empresa_nombreComercial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ruc');
            xmlwriter_text($xml, $empresa->empresa_ruc);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'claveAcceso');
            xmlwriter_text($xml, $nd->nd_autorizacion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'codDoc');
            xmlwriter_text($xml, '05');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'estab');
            xmlwriter_text($xml, $nd->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ptoEmi');
            xmlwriter_text($xml, $nd->rangoDocumento->puntoEmision->punto_serie);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'secuencial');//
            xmlwriter_text($xml, substr(str_repeat(0, 9).$nd->nd_secuencial, - 9));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirMatriz');
            xmlwriter_text($xml, $empresa->empresa_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Agente de Retención'){
                xmlwriter_start_element($xml, 'agenteRetencion');
                xmlwriter_text($xml, '1');
                xmlwriter_end_element($xml); 
            }elseif($empresa->empresa_tipo == 'Microempresas'){
                xmlwriter_start_element($xml, 'regimenMicroempresas');
                xmlwriter_text($xml, 'CONTRIBUYENTE RÉGIMEN MICROEMPRESAS');
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoTributaria'

        // Elemento hijo de 'nd'
        xmlwriter_start_element($xml, 'infoNotaDebito');
            /**********************/
            xmlwriter_start_element($xml, 'fechaEmision');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $nd->nd_fecha)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirEstablecimiento');
            xmlwriter_text($xml, $nd->rangoDocumento->puntoEmision->sucursal->sucursal_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoIdentificacionComprador');
            xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($nd->factura->cliente->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocialComprador');
            xmlwriter_text($xml, $nd->factura->cliente->cliente_nombre);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'identificacionComprador');
            xmlwriter_text($xml, $nd->factura->cliente->cliente_cedula);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Contribuyente Especial'){
                xmlwriter_start_element($xml, 'contribuyenteEspecial');
                xmlwriter_text($xml, $empresa->empresa_contribuyenteEspecial);
                xmlwriter_end_element($xml); 
            }
            /**********************/
            xmlwriter_start_element($xml, 'obligadoContabilidad');
            if($empresa->empresa_llevaContabilidad == 1){
                xmlwriter_text($xml, 'SI');
            }else{
                xmlwriter_text($xml, 'NO');
            }
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'codDocModificado');
            xmlwriter_text($xml, "01");//AUTOMATIZAR
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'numDocModificado');
            xmlwriter_text($xml, $nd->factura->rangoDocumento->puntoEmision->sucursal->sucursal_codigo.'-'.$nd->factura->rangoDocumento->puntoEmision->punto_serie.'-'.substr(str_repeat(0, 9).$nd->factura->factura_secuencial, - 9));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'fechaEmisionDocSustento');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $nd->factura->factura_fecha)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalSinImpuestos');
            xmlwriter_text($xml, number_format($nd->nd_subtotal,2, '.', ''));
            xmlwriter_end_element($xml);              
            /**********************/
            xmlwriter_start_element($xml, 'impuestos');
                /**********************/
                if($nd->nd_tarifa0 > 0){
                    xmlwriter_start_element($xml, 'impuesto');
                        /**********************/
                        xmlwriter_start_element($xml, 'codigo');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'codigoPorcentaje');
                        xmlwriter_text($xml, '0');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'tarifa');
                        xmlwriter_text($xml, '0');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'baseImponible');
                        xmlwriter_text($xml, number_format($nd->nd_tarifa0,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'valor');
                        xmlwriter_text($xml, '0.00');
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                }
                /**********************/
                if($nd->nd_tarifa12 > 0){
                    xmlwriter_start_element($xml, 'impuesto');
                        /**********************/
                        xmlwriter_start_element($xml, 'codigo');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'codigoPorcentaje');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'tarifa');
                        xmlwriter_text($xml, '12');
                        xmlwriter_end_element($xml);
                        /**********************/
                        xmlwriter_start_element($xml, 'baseImponible');
                        xmlwriter_text($xml, number_format($nd->nd_tarifa12,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'valor');
                        xmlwriter_text($xml, number_format($nd->nd_iva,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                }
                /**********************/
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'valorTotal');
            xmlwriter_text($xml, number_format($nd->nd_total,2, '.', ''));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'pagos');
                /**********************/
                xmlwriter_start_element($xml, 'pago');
                    /**********************/
                    xmlwriter_start_element($xml, 'formaPago');
                    xmlwriter_text($xml, $nd->formaPago->forma_pago_codigo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'total');
                    xmlwriter_text($xml, number_format($nd->nd_total,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'plazo');
                    xmlwriter_text($xml, $nd->nd_dias_plazo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'unidadTiempo');
                    xmlwriter_text($xml, 'dias');
                    xmlwriter_end_element($xml); 
                    /**********************/
                xmlwriter_end_element($xml); 
                /**********************/
            xmlwriter_end_element($xml);  
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoNotaDebito'
        
        // Elemento hijo de 'nd'
        xmlwriter_start_element($xml, 'motivos');
            /**********************/
            xmlwriter_start_element($xml, 'motivo');
                /**********************/
                xmlwriter_start_element($xml, 'razon');
                xmlwriter_text($xml, $nd->nd_motivo);
                xmlwriter_end_element($xml); 
                /**********************/
                xmlwriter_start_element($xml, 'valor');
                xmlwriter_text($xml, number_format($nd->nd_subtotal,2, '.', ''));
                xmlwriter_end_element($xml); 
                /**********************/
            xmlwriter_end_element($xml); 
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'motivos'

        // Elemento hijo de 'nd'
        xmlwriter_start_element($xml, 'infoAdicional');
            /**********************/
            xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Dirección');
                xmlwriter_end_attribute($xml);
                xmlwriter_text($xml, $nd->factura->cliente->cliente_direccion);
                /**********************/
            xmlwriter_end_element($xml);
            /**********************/
            xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Email');
                xmlwriter_end_attribute($xml);
                /**********************/
                if(empty($nd->factura->cliente->cliente_email)){
                    xmlwriter_text($xml, 'SIN CORREO');
                }else{
                    xmlwriter_text($xml, $nd->factura->cliente->cliente_email);
                }
            xmlwriter_end_element($xml); 
            /**********************/
            if(!empty($nd->nd_comentario)){
                xmlwriter_start_element($xml, 'campoAdicional');
                    /**********************/
                    xmlwriter_start_attribute($xml, 'nombre');
                    xmlwriter_text($xml, 'Observaciones');
                    xmlwriter_end_attribute($xml);
                    /**********************/
                xmlwriter_text($xml, $nd->nd_comentario);
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoAdicional'

        xmlwriter_end_element($xml); 
        // final 'factura'

        xmlwriter_end_document($xml); 
        // final del documento
        $ruta = public_path().'/documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $nd->nd_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $xmlFinal = xmlwriter_output_memory($xml);
        $nombreArchivo = "ND-".$nd->rangoDocumento->puntoEmision->sucursal->sucursal_codigo. "-".$nd->rangoDocumento->puntoEmision->punto_serie."-" . substr(str_repeat(0, 9).$nd->nd_secuencial, - 9). ".xml";
        $archivo = fopen($ruta.'/'.$nombreArchivo, 'w');
        fwrite($archivo, $xmlFinal);
        fclose($archivo);
        return $xmlFinal;
    }
    function xmlLC(Liquidacion_Compra $lc){
        $empresa = Empresa::empresa()->first();
        $xml = xmlwriter_open_memory();
        xmlwriter_set_indent($xml, 1);
        $res = xmlwriter_set_indent_string($xml, ' ');
        //inicio del documento
        xmlwriter_start_document($xml, '1.0', 'UTF-8');

        //primer elemento
        xmlwriter_start_element($xml, 'liquidacionCompra');

        // atributo 'id' del elemento 'liquidacionCompra'
        xmlwriter_start_attribute($xml, 'id');
        xmlwriter_text($xml, 'comprobante');
        xmlwriter_end_attribute($xml);

        // atributo 'version' del elemento 'liquidacionCompra'
        xmlwriter_start_attribute($xml, 'version');
        xmlwriter_text($xml, '1.1.0');
        xmlwriter_end_attribute($xml);

        // Elemento hijo de 'liquidacionCompra'
        xmlwriter_start_element($xml, 'infoTributaria');
            /**********************/
            xmlwriter_start_element($xml, 'ambiente');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_ambiente);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoEmision');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_disponibilidad);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocial');
            xmlwriter_text($xml, $empresa->empresa_razonSocial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'nombreComercial');
            xmlwriter_text($xml, $empresa->empresa_nombreComercial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ruc');
            xmlwriter_text($xml, $empresa->empresa_ruc);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'claveAcceso');
            xmlwriter_text($xml, $lc->lc_autorizacion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'codDoc');
            xmlwriter_text($xml, '03');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'estab');
            xmlwriter_text($xml, $lc->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ptoEmi');
            xmlwriter_text($xml, $lc->rangoDocumento->puntoEmision->punto_serie);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'secuencial');//
            xmlwriter_text($xml, substr(str_repeat(0, 9).$lc->lc_secuencial, - 9));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirMatriz');
            xmlwriter_text($xml, $empresa->empresa_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Agente de Retención'){
                xmlwriter_start_element($xml, 'agenteRetencion');
                xmlwriter_text($xml, '1');
                xmlwriter_end_element($xml); 
            }elseif($empresa->empresa_tipo == 'Microempresas'){
                xmlwriter_start_element($xml, 'regimenMicroempresas');
                xmlwriter_text($xml, 'CONTRIBUYENTE RÉGIMEN MICROEMPRESAS');
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoTributaria'

        // Elemento hijo de 'liquidacionCompra'
        xmlwriter_start_element($xml, 'infoLiquidacionCompra');
            /**********************/
            xmlwriter_start_element($xml, 'fechaEmision');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $lc->lc_fecha)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirEstablecimiento');
            xmlwriter_text($xml, $lc->rangoDocumento->puntoEmision->sucursal->sucursal_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Contribuyente Especial'){
                xmlwriter_start_element($xml, 'contribuyenteEspecial');
                xmlwriter_text($xml, $empresa->empresa_contribuyenteEspecial);
                xmlwriter_end_element($xml); 
            }
            /**********************/
            xmlwriter_start_element($xml, 'obligadoContabilidad');
            if($empresa->empresa_llevaContabilidad == 1){
                xmlwriter_text($xml, 'SI');
            }else{
                xmlwriter_text($xml, 'NO');
            }
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoIdentificacionProveedor');
            xmlwriter_text($xml, Transaccion_Identificacion::Identificacion($lc->proveedor->tipo_identificacion_id, 'Venta')->first()->transaccion_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocialProveedor');
            xmlwriter_text($xml, $lc->proveedor->proveedor_nombre);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'identificacionProveedor');
            xmlwriter_text($xml, $lc->proveedor->proveedor_ruc);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'direccionProveedor');
            xmlwriter_text($xml, $lc->proveedor->proveedor_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalSinImpuestos');
            xmlwriter_text($xml, number_format($lc->lc_subtotal,2, '.', ''));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalDescuento');
            xmlwriter_text($xml, number_format($lc->lc_descuento,2, '.', ''));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'totalConImpuestos');
                /**********************/
                if($lc->lc_tarifa0 > 0){
                    xmlwriter_start_element($xml, 'totalImpuesto');
                        /**********************/
                        xmlwriter_start_element($xml, 'codigo');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'codigoPorcentaje');
                        xmlwriter_text($xml, '0');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'baseImponible');
                        xmlwriter_text($xml, number_format($lc->lc_tarifa0,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'valor');
                        xmlwriter_text($xml, '0.00');
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                }
                /**********************/
                if($lc->lc_tarifa12 > 0){
                    xmlwriter_start_element($xml, 'totalImpuesto');
                        /**********************/
                        xmlwriter_start_element($xml, 'codigo');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'codigoPorcentaje');
                        xmlwriter_text($xml, '2');
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'baseImponible');
                        xmlwriter_text($xml, number_format($lc->lc_tarifa12,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                        xmlwriter_start_element($xml, 'valor');
                        xmlwriter_text($xml, number_format($lc->lc_iva,2, '.', ''));
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                }
                /**********************/
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'importeTotal');
            xmlwriter_text($xml, number_format($lc->lc_total,2, '.', ''));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'moneda');
            xmlwriter_text($xml, 'DOLAR');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'pagos');
                /**********************/
                xmlwriter_start_element($xml, 'pago');
                    /**********************/
                    xmlwriter_start_element($xml, 'formaPago');
                    xmlwriter_text($xml, $lc->formaPago->forma_pago_codigo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'total');
                    xmlwriter_text($xml, number_format($lc->lc_total,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'plazo');
                    xmlwriter_text($xml, $lc->lc_dias_plazo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'unidadTiempo');
                    xmlwriter_text($xml, 'dias');
                    xmlwriter_end_element($xml); 
                    /**********************/
                xmlwriter_end_element($xml); 
                /**********************/
            xmlwriter_end_element($xml); 
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoLiquidacionCompra'

        // Elemento hijo de 'liquidacionCompra'
        xmlwriter_start_element($xml, 'detalles');
            /**********************/
            foreach($lc->detalles as $detalle){
                xmlwriter_start_element($xml, 'detalle');
                    /**********************/
                    xmlwriter_start_element($xml, 'codigoPrincipal');
                    xmlwriter_text($xml, $detalle->producto->producto_codigo);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'descripcion');
                    xmlwriter_text($xml, $detalle->producto->producto_nombre);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'cantidad');
                    xmlwriter_text($xml, $detalle->detalle_cantidad);
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'precioUnitario');
                    xmlwriter_text($xml, number_format($detalle->detalle_precio_unitario,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'descuento');
                    xmlwriter_text($xml, number_format($detalle->detalle_descuento,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'precioTotalSinImpuesto');
                    xmlwriter_text($xml, number_format($detalle->detalle_total,2, '.', ''));
                    xmlwriter_end_element($xml); 
                    /**********************/
                    xmlwriter_start_element($xml, 'impuestos');
                        /**********************/
                        xmlwriter_start_element($xml, 'impuesto');
                            /**********************/
                            xmlwriter_start_element($xml, 'codigo');
                            xmlwriter_text($xml, '2');
                            xmlwriter_end_element($xml); 
                            /**********************/
                            if($detalle->detalle_iva > 0){
                                xmlwriter_start_element($xml, 'codigoPorcentaje');
                                xmlwriter_text($xml, '2');//revisar
                                xmlwriter_end_element($xml); 
                                /**********************/
                                xmlwriter_start_element($xml, 'tarifa');
                                xmlwriter_text($xml, '12');
                                xmlwriter_end_element($xml);
                            }else{
                                xmlwriter_start_element($xml, 'codigoPorcentaje');
                                xmlwriter_text($xml, '0');
                                xmlwriter_end_element($xml); 
                                /**********************/
                                xmlwriter_start_element($xml, 'tarifa');
                                xmlwriter_text($xml, '0');
                                xmlwriter_end_element($xml);
                            }  
                            /**********************/
                            xmlwriter_start_element($xml, 'baseImponible');
                            xmlwriter_text($xml, number_format($detalle->detalle_total));
                            xmlwriter_end_element($xml); 
                            /**********************/
                            xmlwriter_start_element($xml, 'valor');
                            xmlwriter_text($xml, number_format($detalle->detalle_iva,2, '.', ''));
                            xmlwriter_end_element($xml); 
                            /**********************/
                        xmlwriter_end_element($xml); 
                        /**********************/
                    xmlwriter_end_element($xml); 
                    /**********************/
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'detalles'

        // Elemento hijo de 'liquidacionCompra'
        xmlwriter_start_element($xml, 'infoAdicional');
            /**********************/
            xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Email');
                xmlwriter_end_attribute($xml);
                /**********************/
            if(empty($lc->cliente->cliente_email)){
                xmlwriter_text($xml, 'SIN CORREO');
            }else{
                xmlwriter_text($xml, $lc->cliente->cliente_email);
            }
            xmlwriter_end_element($xml); 
            /**********************/
            if(!empty($lc->lc_comentario)){
                xmlwriter_start_element($xml, 'campoAdicional');
                    /**********************/
                    xmlwriter_start_attribute($xml, 'nombre');
                    xmlwriter_text($xml, 'Observaciones');
                    xmlwriter_end_attribute($xml);
                    /**********************/
                xmlwriter_text($xml, $lc->lc_comentario);
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoAdicional'

        xmlwriter_end_element($xml); 
        // final 'iquidacionCompra'

        xmlwriter_end_document($xml); 
        // final del documento
        $ruta = public_path().'/documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $lc->lc_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $xmlFinal = xmlwriter_output_memory($xml);
        $nombreArchivo = "LC-".$lc->rangoDocumento->puntoEmision->sucursal->sucursal_codigo. "-".$lc->rangoDocumento->puntoEmision->punto_serie."-" . substr(str_repeat(0, 9).$lc->lc_secuencial, - 9). ".xml";
        $archivo = fopen($ruta.'/'.$nombreArchivo, 'w');
        fwrite($archivo, $xmlFinal);
        fclose($archivo);
        return $xmlFinal;
    }
    function xmlGuia(Guia_Remision $guia){
        $empresa = Empresa::empresa()->first();
        $xml = xmlwriter_open_memory();
        xmlwriter_set_indent($xml, 1);
        $res = xmlwriter_set_indent_string($xml, ' ');
        //inicio del documento
        xmlwriter_start_document($xml, '1.0', 'UTF-8');

        //primer elemento
        xmlwriter_start_element($xml, 'guiaRemision');

        // atributo 'id' del elemento 'guiaRemision'
        xmlwriter_start_attribute($xml, 'id');
        xmlwriter_text($xml, 'comprobante');
        xmlwriter_end_attribute($xml);

        // atributo 'version' del elemento 'guiaRemision'
        xmlwriter_start_attribute($xml, 'version');
        xmlwriter_text($xml, '1.1.0');
        xmlwriter_end_attribute($xml);

        // Elemento hijo de 'guiaRemision'
        xmlwriter_start_element($xml, 'infoTributaria');
            /**********************/
            xmlwriter_start_element($xml, 'ambiente');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_ambiente);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoEmision');
            xmlwriter_text($xml, $empresa->firmaElectronica->firma_disponibilidad);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocial');
            xmlwriter_text($xml, $empresa->empresa_razonSocial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'nombreComercial');
            xmlwriter_text($xml, $empresa->empresa_nombreComercial);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ruc');
            xmlwriter_text($xml, $empresa->empresa_ruc);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'claveAcceso');
            xmlwriter_text($xml, $guia->gr_autorizacion);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'codDoc');
            xmlwriter_text($xml, '06');
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'estab');
            xmlwriter_text($xml, $guia->rangoDocumento->puntoEmision->sucursal->sucursal_codigo);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'ptoEmi');
            xmlwriter_text($xml, $guia->rangoDocumento->puntoEmision->punto_serie);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'secuencial');//
            xmlwriter_text($xml, substr(str_repeat(0, 9).$guia->gr_secuencial, - 9));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'dirMatriz');
            xmlwriter_text($xml, $empresa->empresa_direccion);
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Agente de Retención'){
                xmlwriter_start_element($xml, 'agenteRetencion');
                xmlwriter_text($xml, '1');
                xmlwriter_end_element($xml); 
            }elseif($empresa->empresa_tipo == 'Microempresas'){
                xmlwriter_start_element($xml, 'regimenMicroempresas');
                xmlwriter_text($xml, 'CONTRIBUYENTE RÉGIMEN MICROEMPRESAS');
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoTributaria'

        // Elemento hijo de 'guiaRemision'
        xmlwriter_start_element($xml, 'infoGuiaRemision');
            /**********************/
            xmlwriter_start_element($xml, 'dirEstablecimiento');
            xmlwriter_text($xml, $guia->bodega->sucursal->sucursal_direccion);
            xmlwriter_end_element($xml);
            /**********************/
            xmlwriter_start_element($xml, 'dirPartida');
            xmlwriter_text($xml, $guia->gr_punto_partida);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'razonSocialTransportista');
            xmlwriter_text($xml, $guia->Transportista->transportista_nombre);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'tipoIdentificacionTransportista');
            if(strlen($guia->Transportista->transportista_cedula) == 10){
                xmlwriter_text($xml, '05');
            }else{
                xmlwriter_text($xml, '04');
            }
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'rucTransportista');
            xmlwriter_text($xml, $guia->Transportista->transportista_cedula);
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'obligadoContabilidad');
            if($empresa->empresa_llevaContabilidad == 1){
                xmlwriter_text($xml, 'SI');
            }else{
                xmlwriter_text($xml, 'NO');
            }
            xmlwriter_end_element($xml); 
            /**********************/
            if($empresa->empresa_tipo == 'Contribuyente Especial'){
                xmlwriter_start_element($xml, 'contribuyenteEspecial');
                xmlwriter_text($xml, $empresa->empresa_contribuyenteEspecial);
                xmlwriter_end_element($xml); 
            }
            /**********************/
            xmlwriter_start_element($xml, 'fechaIniTransporte');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $guia->gr_fecha_inicio)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'fechaFinTransporte');
            xmlwriter_text($xml, DateTime::createFromFormat('Y-m-d', $guia->gr_fecha_fin)->format('d/m/Y'));
            xmlwriter_end_element($xml); 
            /**********************/
            xmlwriter_start_element($xml, 'placa');
            xmlwriter_text($xml, $guia->gr_placa);
            xmlwriter_end_element($xml); 
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoGuiaRemision'

        // Elemento hijo de 'guiaRemision'
        xmlwriter_start_element($xml, 'destinatarios');
            /**********************/
            xmlwriter_start_element($xml, 'destinatario');
                /**********************/
                xmlwriter_start_element($xml, 'identificacionDestinatario');
                xmlwriter_text($xml, $guia->cliente->cliente_cedula);
                xmlwriter_end_element($xml); 
                /**********************/
                xmlwriter_start_element($xml, 'razonSocialDestinatario');
                xmlwriter_text($xml, $guia->cliente->cliente_nombre);
                xmlwriter_end_element($xml); 
                /**********************/
                xmlwriter_start_element($xml, 'dirDestinatario');
                xmlwriter_text($xml, $guia->cliente->cliente_direccion);
                xmlwriter_end_element($xml); 
                /**********************/
                xmlwriter_start_element($xml, 'motivoTraslado');
                xmlwriter_text($xml, $guia->gr_motivo);
                xmlwriter_end_element($xml); 
                /**********************/
                if($guia->gr_doc_aduanero <> ''){
                    xmlwriter_start_element($xml, 'docAduaneroUnico');
                    xmlwriter_text($xml, $guia->cliente->cliente_cedula);
                    xmlwriter_end_element($xml); 
                }
                /**********************/
            /*    xmlwriter_start_element($xml, 'codEstabDestino');
                xmlwriter_text($xml, '');
                xmlwriter_end_element($xml); 
                
                xmlwriter_start_element($xml, 'ruta');
                xmlwriter_text($xml, '');
                xmlwriter_end_element($xml); 
                
                xmlwriter_start_element($xml, 'codDocSustento');
                xmlwriter_text($xml, '');
                xmlwriter_end_element($xml); 
                
                xmlwriter_start_element($xml, 'numDocSustento');
                xmlwriter_text($xml, '');
                xmlwriter_end_element($xml); 
                
                xmlwriter_start_element($xml, 'numAutDocSustento');
                xmlwriter_text($xml, '');
                xmlwriter_end_element($xml); 
                
                xmlwriter_start_element($xml, 'fechaEmisionDocSustento');
                xmlwriter_text($xml, '');
                xmlwriter_end_element($xml); */
                /**********************/
                xmlwriter_start_element($xml, 'detalles');
                    /**********************/
                    foreach($guia->detalles as $detalle){
                        xmlwriter_start_element($xml, 'detalle');
                            /**********************/
                            xmlwriter_start_element($xml, 'codigoInterno');
                            xmlwriter_text($xml, $detalle->producto->producto_codigo);
                            xmlwriter_end_element($xml); 
                            /**********************/
                            xmlwriter_start_element($xml, 'descripcion');
                            xmlwriter_text($xml, $detalle->producto->producto_nombre);
                            xmlwriter_end_element($xml); 
                            /**********************/
                            xmlwriter_start_element($xml, 'cantidad');
                            xmlwriter_text($xml, $detalle->detalle_cantidad);
                            xmlwriter_end_element($xml); 
                            /**********************/
                        xmlwriter_end_element($xml); 
                    }
                    /**********************/
                xmlwriter_end_element($xml); 
                /**********************/
            xmlwriter_end_element($xml); 
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'destinatarios'

        // Elemento hijo de 'guiaRemision'
        xmlwriter_start_element($xml, 'infoAdicional');
            /**********************/
            xmlwriter_start_element($xml, 'campoAdicional');
                /**********************/
                xmlwriter_start_attribute($xml, 'nombre');
                xmlwriter_text($xml, 'Email');
                xmlwriter_end_attribute($xml);
                /**********************/
            if(empty($guia->cliente->cliente_email)){
                xmlwriter_text($xml, 'SIN CORREO');
            }else{
                xmlwriter_text($xml, $guia->cliente->cliente_email);
            }
            xmlwriter_end_element($xml); 
            /**********************/
            /**********************/
            if(!empty($guia->gr_comentario)){
                xmlwriter_start_element($xml, 'campoAdicional');
                    /**********************/
                    xmlwriter_start_attribute($xml, 'nombre');
                    xmlwriter_text($xml, 'Observaciones');
                    xmlwriter_end_attribute($xml);
                    /**********************/
                xmlwriter_text($xml, $guia->gr_comentario);
                xmlwriter_end_element($xml); 
            }
            /**********************/
        xmlwriter_end_element($xml); 
        // final 'infoAdicional'

        xmlwriter_end_element($xml); 
        // final 'guiaRemision'

        xmlwriter_end_document($xml); 
        // final del documento
        $ruta = public_path().'/documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $guia->gr_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $xmlFinal = xmlwriter_output_memory($xml);
        $nombreArchivo = "GUIA-".$guia->rangoDocumento->puntoEmision->sucursal->sucursal_codigo. "-".$guia->rangoDocumento->puntoEmision->punto_serie."-" . substr(str_repeat(0, 9).$guia->gr_secuencial, - 9). ".xml";
        $archivo = fopen($ruta.'/'.$nombreArchivo, 'w');
        fwrite($archivo, $xmlFinal);
        fclose($archivo);
        return $xmlFinal;
    }
}
