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
use App\Models\Sucursal;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Response;

class documentosElectronicosController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $sucursal=Sucursal::SucursalesDistinc()->select('sucursal_nombre')->distinct()->get();
            $firmaElectronica = Firma_Electronica::firma()->first();
            $pubKey =Crypt::decryptString($firmaElectronica->firma_pubKey);
            $data=openssl_x509_parse($pubKey,true);
            return view('admin.sri.documentosElectronicos.index',['caduca'=>$data['validTo_time_t'],'sucursal'=>$sucursal,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    
    public function consultarDoc($claveAcceso){
        $docElectronico = new facturacionElectronicaController();
        print_r ($docElectronico->consultarDOC($claveAcceso));
    }
    public function buscar(Request $request){
        if (isset($_POST['consultar'])){
            return $this->consultar($request);
        }
        if (isset($_POST['autorizar'])){
            return $this->autorizar($request);
        }
        if (isset($_POST['consultarxnuemero'])){
            return $this->consultarBynumero($request);
        }
    }
    public function consultar(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $facturas = null;
            $ncs = null;
            $nds = null;
            $rets = null;
            $lcs = null;
            $guias = null;
            $sucursal=Sucursal::SucursalesDistinc()->select('sucursal_nombre')->distinct()->get();
            if(($request->get('tipo_documento') == '1' || $request->get('tipo_documento') == "--TODOS--" )  && $request->get('sucursal') == "--TODOS--" ){
                $facturas = Factura_Venta::FacturasbyFecha($request->get('idDesde'),$request->get('idHasta'))->orderBy('factura_numero')->get();
            }
            if(($request->get('tipo_documento') == '1' || $request->get('tipo_documento') == "--TODOS--" ) && $request->get('sucursal') != "--TODOS--"){
                $facturas = Factura_Venta::FacturasbyFechaSucrusal($request->get('idDesde'),$request->get('idHasta'),$request->get('sucursal'))->orderBy('factura_numero')->get();
            }
            if(($request->get('tipo_documento') == '2' || $request->get('tipo_documento') == "--TODOS--" )  && $request->get('sucursal') == "--TODOS--"){
                $ncs = Nota_Credito::NCbyFecha($request->get('idDesde'),$request->get('idHasta'))->orderBy('nc_numero')->get();
            }
            if(($request->get('tipo_documento') == '2' || $request->get('tipo_documento') == "--TODOS--" )  && $request->get('sucursal') != "--TODOS--"){
                $ncs = Nota_Credito::NCbyFechaSucrusal($request->get('idDesde'),$request->get('idHasta'),$request->get('sucursal'))->orderBy('nc_numero')->get();
            }

            if(($request->get('tipo_documento') == '3' || $request->get('tipo_documento') == "--TODOS--" )  && $request->get('sucursal') == "--TODOS--"){
                $nds = Nota_Debito::NDbyFecha($request->get('idDesde'),$request->get('idHasta'))->orderBy('nd_numero')->get();
            }

            if(($request->get('tipo_documento') == '3' || $request->get('tipo_documento') == "--TODOS--" )  && $request->get('sucursal') != "--TODOS--"){
                $nds = Nota_Debito::NDbyFechaSucrusal($request->get('idDesde'),$request->get('idHasta'),$request->get('sucursal'))->orderBy('nd_numero')->get();
            }
            if(($request->get('tipo_documento') == '4' || $request->get('tipo_documento') == "--TODOS--" )  && $request->get('sucursal') == "--TODOS--"){
                $rets = Retencion_Compra::retbyFecha($request->get('idDesde'),$request->get('idHasta'))->orderBy('retencion_numero','asc')->get();
            }

            if(($request->get('tipo_documento') == '4' || $request->get('tipo_documento') == "--TODOS--" )  && $request->get('sucursal') != "--TODOS--"){
                $rets = Retencion_Compra::retbyFechaSucrusal($request->get('idDesde'),$request->get('idHasta'),$request->get('sucursal'))->orderBy('retencion_numero','asc')->get();
            }

            if(($request->get('tipo_documento') == '5' || $request->get('tipo_documento') == "--TODOS--" ) && $request->get('sucursal') == "--TODOS--"){
                $lcs = Liquidacion_Compra::LCbyFecha($request->get('idDesde'),$request->get('idHasta'))->orderBy('lc_numero','asc')->get();
            }

            if(($request->get('tipo_documento') == '5' || $request->get('tipo_documento') == "--TODOS--" ) && $request->get('sucursal') != "--TODOS--"){
                $lcs = Liquidacion_Compra::LCbyFechaSucrusal($request->get('idDesde'),$request->get('idHasta'),$request->get('sucursal'))->orderBy('lc_numero','asc')->get();
            }
            if(($request->get('tipo_documento') == '6' || $request->get('tipo_documento') == "--TODOS--" ) && $request->get('sucursal') == "--TODOS--"){
                $guias = Guia_Remision::GuiasbyFecha($request->get('idDesde'),$request->get('idHasta'))->orderBy('gr_numero','asc')->get();
            }

            if(($request->get('tipo_documento') == '6' || $request->get('tipo_documento') == "--TODOS--" ) && $request->get('sucursal') != "--TODOS--"){
                $guias = Guia_Remision::GuiasbyFechaSucrusal($request->get('idDesde'),$request->get('idHasta'),$request->get('sucursal'))->orderBy('gr_numero','asc')->get();
            }
            $firmaElectronica = Firma_Electronica::firma()->first();
            $pubKey =Crypt::decryptString($firmaElectronica->firma_pubKey);
            $data=openssl_x509_parse($pubKey,true);
            return view('admin.sri.documentosElectronicos.index',['caduca'=>$data['validTo_time_t'],'idNumeroDoc'=>$request->get('idNumeroDoc'),'idsucursal'=>$request->get('sucursal'),'sucursal'=>$sucursal,'fecI'=>$request->get('idDesde'),'fecF'=>$request->get('idHasta'),'docC'=>$request->get('tipo_documento'),'facturas'=>$facturas, 'ncs'=>$ncs, 'nds'=>$nds, 'rets'=>$rets, 'lcs'=>$lcs, 'guias'=>$guias, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function consultarBynumero(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $facturas = null;
            $ncs = null;
            $nds = null;
            $rets = null;
            $lcs = null;
            $guias = null;
            $sucursal=Sucursal::SucursalesDistinc()->select('sucursal_nombre')->distinct()->get();
            $facturas = Factura_Venta::FacturasbyNumero($request->get('idNumeroDoc'))->get();                          
            $ncs = Nota_Credito::NCbyNumero($request->get('idNumeroDoc'))->get();                
            $nds = Nota_Debito::NDbyNumero($request->get('idNumeroDoc'))->get();
            $rets = Retencion_Compra::retbyNumero($request->get('idNumeroDoc'))->get();
            $lcs = Liquidacion_Compra::LCbyNumero($request->get('idNumeroDoc'))->get();
            $guias = Guia_Remision::GuiasbyNumero($request->get('idNumeroDoc'))->get();
            $firmaElectronica = Firma_Electronica::firma()->first();
            $pubKey =Crypt::decryptString($firmaElectronica->firma_pubKey);
            $data=openssl_x509_parse($pubKey,true);
            return view('admin.sri.documentosElectronicos.index',['caduca'=>$data['validTo_time_t'],'idNumeroDoc'=>$request->get('idNumeroDoc'),'sucursal'=>$sucursal,'fecI'=>$request->get('idDesde'),'fecF'=>$request->get('idHasta'),'docC'=>$request->get('tipo_documento'),'facturas'=>$facturas, 'ncs'=>$ncs, 'nds'=>$nds, 'rets'=>$rets, 'lcs'=>$lcs, 'guias'=>$guias, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function autorizar(Request $request){
        try{            
            $docElectronico = new facturacionElectronicaController();
            if($request->get('checkbox1')){
                $facturas = $request->get('checkbox1');
                for ($i = 0; $i < count($facturas); ++$i) {
                    $factura = Factura_Venta::findOrFail($facturas[$i]);
                    if($factura->factura_emision == 'ELECTRONICA'){
                        $factura->factura_autorizacion = $docElectronico->generarClaveAcceso($factura->factura_numero,$factura->factura_fecha,"01");
                        $facturaAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlFactura($factura),'FACTURA');
                        $factura->factura_xml_estado = $facturaAux->factura_xml_estado;
                        $factura->factura_xml_mensaje = $facturaAux->factura_xml_mensaje;
                        $factura->factura_xml_respuestaSRI = $facturaAux->factura_xml_respuestaSRI;
                        if($facturaAux->factura_xml_estado == 'AUTORIZADO'){
                            $factura->factura_xml_nombre = $facturaAux->factura_xml_nombre;
                            $factura->factura_xml_fecha = $facturaAux->factura_xml_fecha;
                            $factura->factura_xml_hora = $facturaAux->factura_xml_hora;
                        }
                        $factura->update();
                    }
                }
            }
            if($request->get('checkbox2')){
                $notasC = $request->get('checkbox2');
                for ($i = 0; $i < count($notasC); ++$i) {
                    $nc = Nota_Credito::findOrFail($notasC[$i]);
                    if($nc->nc_emision == 'ELECTRONICA'){
                        $ncAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlNotaCredito($nc),'NC');
                        $nc->nc_xml_estado = $ncAux->nc_xml_estado;
                        $nc->nc_xml_mensaje = $ncAux->nc_xml_mensaje;
                        $nc->nc_xml_respuestaSRI = $ncAux->nc_xml_respuestaSRI;
                        if($ncAux->nc_xml_estado == 'AUTORIZADO'){
                            $nc->nc_xml_nombre = $ncAux->nc_xml_nombre;
                            $nc->nc_xml_fecha = $ncAux->nc_xml_fecha;
                            $nc->nc_xml_hora = $ncAux->nc_xml_hora;
                        }
                        $nc->update();
                    }
                }
            }
            if($request->get('checkbox3')){
                $notasD = $request->get('checkbox3');
                for ($i = 0; $i < count($notasD); ++$i) {
                    $nd = Nota_Debito::findOrFail($notasD[$i]);
                    if($nd->nd_emision == 'ELECTRONICA'){
                        $ndAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlNotaDebito($nd),'ND');
                        $nd->nd_xml_estado = $ndAux->nd_xml_estado;
                        $nd->nd_xml_mensaje = $ndAux->nd_xml_mensaje;
                        $nd->nd_xml_respuestaSRI = $ndAux->nd_xml_respuestaSRI;
                        if($ndAux->nd_xml_estado == 'AUTORIZADO'){
                            $nd->nd_xml_nombre = $ndAux->nd_xml_nombre;
                            $nd->nd_xml_fecha = $ndAux->nd_xml_fecha;
                            $nd->nd_xml_hora = $ndAux->nd_xml_hora;
                        }
                        $nd->update();
                    }
                }
            }
            if($request->get('checkbox4')){
                $rets = $request->get('checkbox4');
                for ($i = 0; $i < count($rets); ++$i) {
                    $ret = Retencion_Compra::findOrFail($rets[$i]);
                    if($ret->retencion_emision == 'ELECTRONICA'){
                        $retencionAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlRetencion($ret),'RETENCION');
                        $ret->retencion_xml_estado = $retencionAux->retencion_xml_estado;
                        $ret->retencion_xml_mensaje =$retencionAux->retencion_xml_mensaje;
                        $ret->retencion_xml_respuestaSRI = $retencionAux->retencion_xml_respuestaSRI;
                        if($retencionAux->retencion_xml_estado == 'AUTORIZADO'){
                            $ret->retencion_xml_nombre = $retencionAux->retencion_xml_nombre;
                            $ret->retencion_xml_fecha = $retencionAux->retencion_xml_fecha;
                            $ret->retencion_xml_hora = $retencionAux->retencion_xml_hora;
                        }
                        $ret->update();
                    }
                }
            }
            if($request->get('checkbox5')){
                $lcs = $request->get('checkbox5');
                for ($i = 0; $i < count($lcs); ++$i) {
                    $lc = Liquidacion_Compra::findOrFail($lcs[$i]);
                    if($lc->lc_emision == 'ELECTRONICA'){
                        $lcAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlLC($lc),'LC');
                        $lc->lc_xml_estado = $lcAux->lc_xml_estado;
                        $lc->lc_xml_mensaje =$lcAux->lc_xml_mensaje;
                        $lc->lc_xml_respuestaSRI = $lcAux->lc_xml_respuestaSRI;
                        if($lcAux->lc_xml_estado == 'AUTORIZADO'){
                            $lc->lc_xml_nombre = $lcAux->lc_xml_nombre;
                            $lc->lc_xml_fecha = $lcAux->lc_xml_fecha;
                            $lc->lc_xml_hora = $lcAux->lc_xml_hora;
                        }
                        $lc->update();
                    }
                }
            }
            if($request->get('checkbox6')){
                $guias = $request->get('checkbox6');
                for ($i = 0; $i < count($guias); ++$i) {
                    $guia = Guia_Remision::findOrFail($guias[$i]);
                    if($guia->gr_emision == 'ELECTRONICA'){
                        $guiaAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlGuia($guia),'GUIA');
                        $guia->gr_xml_estado = $guiaAux->gr_xml_estado;
                        $guia->gr_xml_mensaje = $guiaAux->gr_xml_mensaje;
                        $guia->gr_xml_respuestaSRI = $guiaAux->gr_xml_respuestaSRI;
                        if($guiaAux->gr_xml_estado == 'AUTORIZADO'){
                            $guia->gr_xml_nombre = $guiaAux->gr_xml_nombre;
                            $guia->gr_xml_fecha = $guiaAux->gr_xml_fecha;
                            $guia->gr_xml_hora = $guiaAux->gr_xml_hora;
                        }
                        $guia->update();
                    }
                }
            }
           return redirect('docsElectronicos')->with('success','Proceso finalizado.');
        }catch(\Exception $ex){
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function reenviarFactura($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $factura = Factura_Venta::factura($id)->first();
            if($factura->factura_emision == 'ELECTRONICA'){
                $factura->factura_autorizacion = $docElectronico->generarClaveAcceso($factura->factura_numero,$factura->factura_fecha,"01");
                $facturaAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlFactura($factura),'FACTURA');
                $factura->factura_xml_estado = $facturaAux->factura_xml_estado;
                $factura->factura_xml_mensaje = $facturaAux->factura_xml_mensaje;
                $factura->factura_xml_respuestaSRI = $facturaAux->factura_xml_respuestaSRI;
                if($facturaAux->factura_xml_estado == 'AUTORIZADO'){
                    $factura->factura_xml_nombre = $facturaAux->factura_xml_nombre;
                    $factura->factura_xml_fecha = $facturaAux->factura_xml_fecha;
                    $factura->factura_xml_hora = $facturaAux->factura_xml_hora;
                }
                $factura->update();
            }
            DB::commit();
            if($facturaAux->factura_xml_estado == 'AUTORIZADO'){
                return redirect('docsElectronicos')->with('success','Factura autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d-m-Y').'/'.$factura->factura_xml_nombre.'.pdf');
            }else{
                return redirect('docsElectronicos')->with('error2','ERROR SRI--> '.$facturaAux->factura_xml_estado.' : '.$facturaAux->factura_xml_mensaje);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function emailFactura($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $factura = Factura_Venta::factura($id)->first();
            $docElectronico->enviarCorreoCliente($factura->cliente->cliente_email,DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d/m/Y'),$factura->factura_xml_nombre, $factura->empresa);
            DB::commit();
            return redirect('docsElectronicos')->with('success','Factura electrónica enviada exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function reenviarNc($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $nc = Nota_Credito::NotaCredito($id)->first();
            if($nc->nc_emision == 'ELECTRONICA'){
                $nc->nc_autorizacion = $docElectronico->generarClaveAcceso($nc->nc_numero,$nc->nc_fecha,"04");
                $ncAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlNotaCredito($nc),'NC');
                $nc->nc_xml_estado = $ncAux->nc_xml_estado;
                $nc->nc_xml_mensaje = $ncAux->nc_xml_mensaje;
                $nc->nc_xml_respuestaSRI = $ncAux->nc_xml_respuestaSRI;
                if($ncAux->nc_xml_estado == 'AUTORIZADO'){
                    $nc->nc_xml_nombre = $ncAux->nc_xml_nombre;
                    $nc->nc_xml_fecha = $ncAux->nc_xml_fecha;
                    $nc->nc_xml_hora = $ncAux->nc_xml_hora;
                }
                $nc->update();
            }
            DB::commit();
            if($ncAux->nc_xml_estado == 'AUTORIZADO'){
                return redirect('docsElectronicos')->with('success','NOTA DE CRÉDITO autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $nc->nc_fecha)->format('d-m-Y').'/'.$nc->nc_xml_nombre.'.pdf');
            }else{
                return redirect('docsElectronicos')->with('error2','ERROR SRI--> '.$ncAux->nc_xml_estado.' : '.$ncAux->nc_xml_mensaje);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function emailNC($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $nc = Nota_Credito::NotaCredito($id)->first();
            $docElectronico->enviarCorreoCliente($nc->factura->cliente->cliente_email,DateTime::createFromFormat('Y-m-d', $nc->nc_fecha)->format('d/m/Y'),$nc->nc_xml_nombre, $nc->rangoDocumento->empresa);
            DB::commit();
            return redirect('docsElectronicos')->with('success','Nota de Crédito electrónica enviada exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function reenviarNd($id)
    {   
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $nd = Nota_Debito::NotaDebito($id)->first();
            if($nd->nd_emision == 'ELECTRONICA'){
                $nd->nd_autorizacion = $docElectronico->generarClaveAcceso($nd->nd_numero,$nd->nd_fecha,"05");
                $ndAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlNotaDebito($nd),'ND');
                $nd->nd_xml_estado = $ndAux->nd_xml_estado;
                $nd->nd_xml_mensaje = $ndAux->nd_xml_mensaje;
                $nd->nd_xml_respuestaSRI = $ndAux->nd_xml_respuestaSRI;
                if($ndAux->nd_xml_estado == 'AUTORIZADO'){
                    $nd->nd_xml_nombre = $ndAux->nd_xml_nombre;
                    $nd->nd_xml_fecha = $ndAux->nd_xml_fecha;
                    $nd->nd_xml_hora = $ndAux->nd_xml_hora;
                }
                $nd->update();
            }
            DB::commit();
            if($ndAux->nd_xml_estado == 'AUTORIZADO'){
                return redirect('docsElectronicos')->with('success','NOTA DE DÉBITO autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $nd->nd_fecha)->format('d-m-Y').'/'.$nd->nd_xml_nombre.'.pdf');
            }else{
                return redirect('docsElectronicos')->with('error2','ERROR SRI--> '.$ndAux->nd_xml_estado.' : '.$ndAux->nd_xml_mensaje);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function emailND($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $nd = Nota_Debito::NotaDebito($id)->first();
            $docElectronico->enviarCorreoCliente($nd->factura->cliente->cliente_email,DateTime::createFromFormat('Y-m-d', $nd->nd_fecha)->format('d/m/Y'),$nd->nd_xml_nombre, $nd->rangoDocumento->empresa);
            DB::commit();
            return redirect('docsElectronicos')->with('success','Nota de Débito electrónica enviada exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function reenviarRet($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $ret = Retencion_Compra::Retencion($id)->first();
            if($ret->retencion_emision == 'ELECTRONICA'){
                $ret->retencion_autorizacion = $docElectronico->generarClaveAcceso($ret->retencion_numero,$ret->retencion_fecha,"07");
                $retencionAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlRetencion($ret),'RETENCION');
                $ret->retencion_xml_estado = $retencionAux->retencion_xml_estado;
                $ret->retencion_xml_mensaje =$retencionAux->retencion_xml_mensaje;
                $ret->retencion_xml_respuestaSRI = $retencionAux->retencion_xml_respuestaSRI;
                if($retencionAux->retencion_xml_estado == 'AUTORIZADO'){
                    $ret->retencion_xml_nombre = $retencionAux->retencion_xml_nombre;
                    $ret->retencion_xml_fecha = $retencionAux->retencion_xml_fecha;
                    $ret->retencion_xml_hora = $retencionAux->retencion_xml_hora;
                }
                $ret->update();
            }
            DB::commit();
            if($retencionAux->retencion_xml_estado == 'AUTORIZADO'){
                return redirect('docsElectronicos')->with('success','Comprobante de Retención autorizado exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $ret->retencion_fecha)->format('d-m-Y').'/'.$ret->retencion_xml_nombre.'.pdf');
            }else{
                return redirect('docsElectronicos')->with('error2','ERROR SRI--> '.$retencionAux->retencion_xml_estado.' : '.$retencionAux->retencion_xml_mensaje);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function emailRet($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $ret = Retencion_Compra::Retencion($id)->first();
            $email ='';
            if($ret->transaccionCompra){
                $email = $ret->transaccionCompra->proveedor->proveedor_email;
            }
            if($ret->liquidacionCompra){
                $email = $ret->liquidacionCompra->proveedor->proveedor_email;
            }
            $docElectronico->enviarCorreoCliente($email,DateTime::createFromFormat('Y-m-d', $ret->retencion_fecha)->format('d/m/Y'),$ret->retencion_xml_nombre, $ret->rangoDocumento->empresa);
            DB::commit();
            return redirect('docsElectronicos')->with('success','Comprobante de Retención electrónico enviado exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function reenviarLC($id)
    {   
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $lc = Liquidacion_Compra::LiquidacionCompra($id)->first();
            if($lc->lc_emision == 'ELECTRONICA'){
                $lc->lc_autorizacion = $docElectronico->generarClaveAcceso($lc->lc_numero,$lc->fecha,"03");
                $lcAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlLC($lc),'LC');
                $lc->lc_xml_estado = $lcAux->lc_xml_estado;
                $lc->lc_xml_mensaje =$lcAux->lc_xml_mensaje;
                $lc->lc_xml_respuestaSRI = $lcAux->lc_xml_respuestaSRI;
                if($lcAux->lc_xml_estado == 'AUTORIZADO'){
                    $lc->lc_xml_nombre = $lcAux->lc_xml_nombre;
                    $lc->lc_xml_fecha = $lcAux->lc_xml_fecha;
                    $lc->lc_xml_hora = $lcAux->lc_xml_hora;
                }
                $lc->update();
            }
            DB::commit();
            if($lcAux->lc_xml_estado == 'AUTORIZADO'){
                return redirect('docsElectronicos')->with('success','Liquidación de Compra autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $lc->lc_fecha)->format('d-m-Y').'/'.$lc->lc_xml_nombre.'.pdf');
            }else{
                return redirect('docsElectronicos')->with('error2','ERROR SRI--> '.$lcAux->lc_xml_estado.' : '.$lcAux->lc_xml_mensaje);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function emailLC($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $lc = Liquidacion_Compra::LiquidacionCompra($id)->first();
            $docElectronico->enviarCorreoCliente($lc->proveedor->proveedor_email,DateTime::createFromFormat('Y-m-d', $lc->lc_fecha)->format('d/m/Y'),$lc->lc_xml_nombre, $lc->rangoDocumento->empresa);
            DB::commit();
            return redirect('docsElectronicos')->with('success','Liquidación de Compra electrónica enviada exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function reenviarGR($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $guia = Guia_Remision::Guia($id)->first();
            if($guia->gr_emision == 'ELECTRONICA'){
                $guia->gr_autorizacion = $docElectronico->generarClaveAcceso($guia->gr_numero,$guia->gr_fecha,"06");
                $guiaAux = $docElectronico->enviarDocumentoElectronico($docElectronico->xmlGuia($guia),'GUIA');
                $guia->gr_xml_estado = $guiaAux->gr_xml_estado;
                $guia->gr_xml_mensaje = $guiaAux->gr_xml_mensaje;
                $guia->gr_xml_respuestaSRI = $guiaAux->gr_xml_respuestaSRI;
                if($guiaAux->gr_xml_estado == 'AUTORIZADO'){
                    $guia->gr_xml_nombre = $guiaAux->gr_xml_nombre;
                    $guia->gr_xml_fecha = $guiaAux->gr_xml_fecha;
                    $guia->gr_xml_hora = $guiaAux->gr_xml_hora;
                }
                $guia->update();
            }
            DB::commit();
            if($guiaAux->gr_xml_estado == 'AUTORIZADO'){
                return redirect('docsElectronicos')->with('success','Guia de remision autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $guia->gr_fecha)->format('d-m-Y').'/'.$guia->gr_xml_nombre.'.pdf');
            }else{
                return redirect('docsElectronicos')->with('error2','ERROR SRI--> '.$guiaAux->gr_xml_estado.' : '.$guiaAux->gr_xml_mensaje);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function emailGR($id)
    {
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $guia = Guia_Remision::Guia($id)->first();
            $docElectronico->enviarCorreoCliente($guia->cliente->cliente_email,DateTime::createFromFormat('Y-m-d', $guia->gr_fecha)->format('d/m/Y'),$guia->gr_xml_nombre, $guia->rangoDocumento->empresa);
            DB::commit();
            return redirect('docsElectronicos')->with('success','Guia de remision electrónica enviada exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function facturaPDF($id){
        try{            
            DB::beginTransaction();
            $docElectronico = new facturacionElectronicaController();
            $empresa=Empresa::Empresa()->first();
            $factura = Factura_Venta::factura($id)->first();
            if($factura->factura_emision == 'ELECTRONICA'){
                $factura->factura_autorizacion = $docElectronico->generarClaveAcceso($factura->factura_numero,$factura->factura_fecha,"01");
                $consultaDoc = $docElectronico->consultarDOC($factura->factura_autorizacion);
                if(array_key_exists('RespuestaAutorizacionComprobante', (array)$consultaDoc)){
                    if(array_key_exists('autorizaciones', (array)$consultaDoc['RespuestaAutorizacionComprobante'])){
                        if($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']!=''){
                            if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                                $fechaAutorizacion = $consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['fechaAutorizacion'];
                                $xml = simplexml_load_string($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['comprobante']);
                                return $docElectronico->crearPDFdocElectronico($xml, DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d/m/Y'), $factura->factura_xml_nombre,$empresa, $fechaAutorizacion, $factura->factura_ambiente, 'FACTURA');
                            }
                        }
                    }
                }            
            }
            DB::commit();
            $file='documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d-m-Y').'/'.$factura->factura_xml_nombre.'.pdf';
            return Response::file($file);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function respuesSRIFac($id)
    {
        try{            
            $factura = Factura_Venta::factura($id)->first();
            return $factura->factura_xml_respuestaSRI;
        }catch(\Exception $ex){
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function respuesSRIGR($id)
    {
        try{            
            $guia = Guia_Remision::Guia($id)->first();
            return $guia->gr_xml_respuestaSRI;
        }catch(\Exception $ex){
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function respuesSRINC($id)
    {
        try{            
            $nc = Nota_Credito::NotaCredito($id)->first();
            return $nc->nc_xml_respuestaSRI;
        }catch(\Exception $ex){
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function respuesSRIND($id)
    {
        try{            
            $nd = Nota_Debito::NotaDebito($id)->first();
            return $nd->nd_xml_respuestaSRI;
        }catch(\Exception $ex){
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function respuesSRILQ($id)
    {
        try{            
            $lc = Liquidacion_Compra::LiquidacionCompra($id)->first();
            return $lc->lc_xml_respuestaSRI;
        }catch(\Exception $ex){
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function respuesSRIRet($id)
    {
        try{            
            $ret = Retencion_Compra::Retencion($id)->first();
            return $ret->retencion_xml_respuestaSRI;
        }catch(\Exception $ex){
            return redirect('docsElectronicos')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
