<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Bodega;
use App\Models\Centro_Consumo;
use App\Models\Codigo_Producto;
use App\Models\Concepto_Retencion;
use App\Models\Empresa;
use App\Models\Firma_Electronica;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Retencion_Compra;
use App\Models\Sustento_Tributario;
use App\Models\Tarifa_Iva;
use App\Models\Tipo_Comprobante;
use App\Models\Transaccion_Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\type;

class cargarXMLController extends Controller
{
    public function nuevo($punto)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.compras.xml.index',['punto'=>$punto,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function cargarproducto(Request $request)
    {
       
            DB::beginTransaction();
            $nombre = $request->get('DLdias');
            $provedor = $request->get('idproveedor');
            $producto = $request->get('productos');
            $totalxml=0;
            $activacion=false;
            if (isset($nombre)) {
                for ($i=0; $i < count($nombre); $i++) {
                    if ($producto[$i]!='0') {
                        $codigo = new Codigo_Producto();
                        $codigo->codigo_nombre = trim($nombre[$i]);
                        $codigo->proveedor_id = $provedor;
                        $codigo->codigo_estado = 1;
                        $codigo->producto_id = $producto[$i];
                        $codigo->save();
                        $product=Producto::findOrFail($producto[$i]);
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de Codigo a producto-> '.$product->producto_nombre, '0', '');
                    }
                }
            }
            $firmaElectronica = Firma_Electronica::firma()->first();
            $pubKey =Crypt::decryptString($firmaElectronica->firma_pubKey);
            $data=openssl_x509_parse($pubKey,true);
            $datos=null;
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $rangoDocumento=Rango_Documento::PuntoRango($request->get('punto'),'Comprobante de Retención')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $secuencial=1;
            $iva[1]['codigo']='02';
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Retencion_Compra::secuencial($rangoDocumento->rango_id)->max('retencion_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $electrocnico = new facturacionElectronicaController();
                $consultaDoc = $electrocnico->consultarDOC($request->get('clave'));       
                if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {               
                    $xmlEnvio = simplexml_load_string($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['comprobante']);
                    foreach ($xmlEnvio->detalles->detalle as $adicional) {
                        foreach($adicional->impuestos->impuesto as $adicion){
                            if ($adicion->codigoPorcentaje>0) {
                                $iva[1]['codigo']='0'.$adicion->codigoPorcentaje;
                            }
                        }    
                    }
                    $porcentaje=Tarifa_Iva::TarifaIvaCodigo($iva[1]['codigo'])->first();

                    $poveedorXML = Proveedor::ProveedoresByRuc($xmlEnvio->infoTributaria->ruc)->first();
                    if(!$poveedorXML){
                        return redirect('/transaccionCompra/new/'.$request->get('punto'))->with('error','No tiene resgistrado el proveedor, configure y vuelva a intentar');
                    }
                    $codigos = Codigo_Producto::buscarproductoproveedor($poveedorXML->proveedor_id)->get();
                    $coun=1;
                    
                            $totalxml=$request->get('total');
                            
                    foreach($xmlEnvio->detalles->detalle as $adicional){ 
                        
                        $activador=false;
                        $vari=trim(strval($adicional->codigoPrincipal));
                          
                        foreach ($codigos as $codigo) {
                            if( $vari==$codigo->codigo_nombre){
                                $activador=true;
                            }
                        }
                        if ($activador==true) {
                            if ($datos!=null) {
                                $product=Codigo_Producto::buscarproducto($vari, $poveedorXML->proveedor_id)->first();
                                for ($i = 1; $i <= count($datos); ++$i) {
                                    if ($product->producto_id==$datos[$i]['id']) {
                                        $datos[$i]['cantidad']=$datos[$i]['cantidad']+floatval($adicional->cantidad);
                                        $datos[$i]['subtotal2']=round($adicional->precioUnitario*$datos[$i]['cantidad'], 2);
                                        $datos[$i]['subtotal']=round(($adicional->precioUnitario*$datos[$i]['cantidad'])-($adicional->descuento), 2);
                                        $datos[$i]['t0']=round(($adicional->precioUnitario*$datos[$i]['cantidad'])-$datos[$i]['descuento'], 2);
                                        $datos[$i]['descuento']=$datos[$i]['descuento']+round(floatval($adicional->descuento), 2);
                                        if ($product->producto_tiene_iva==1) {
                                            $datos[$i]['diva']=round((($adicional->precioUnitario*$datos[$i]['cantidad'])-$datos[$i]['descuento'])*($porcentaje->tarifa_iva_porcentaje/100), 2);
                                            $datos[$i]['t12']=round(($adicional->precioUnitario*$datos[$i]['cantidad'])-$datos[$i]['descuento'], 2);
                                            $datos[$i]['t0']=0;
                                            if ($product->producto_tipo==1) {
                                                $datos[$i]['sb']=round((($adicional->precioUnitario*$datos[$i]['cantidad'])-$datos[$i]['descuento'])*($porcentaje->tarifa_iva_porcentaje/100), 2);
                                                $datos[$i]['ss']=0;
                                            }
                                            else{
                                                $datos[$i]['sb']=0.00;
                                                $datos[$i]['ss']=round((($adicional->precioUnitario*$datos[$i]['cantidad'])-$datos[$i]['descuento'])*($porcentaje->tarifa_iva_porcentaje/100), 2);
                                            }
                                        }
                                        $datos[$i]['total']=$datos[$i]['subtotal2']+$datos[$i]['diva']-$datos[$i]['descuento'];
                                        $activador=false;
                                    }
                                }
                            }
                        }
                        if($activador==true){
                            $product=Codigo_Producto::buscarproducto($vari,$poveedorXML->proveedor_id)->first();
                            
                            $datos[$coun]['id']=$product->producto_id;
                            $datos[$coun]['codigo']=$product->producto_codigo;
                            $datos[$coun]['descripcion']=$product->producto_nombre;
                            $datos[$coun]['t12']=0;
                            $datos[$coun]['iva']='NO';
                            $datos[$coun]['diva']=0.00;
                            $datos[$coun]['sb']=0.00;
                            $datos[$coun]['t0']=round(($adicional->precioUnitario*$adicional->cantidad)-$adicional->descuento,2); 
                            $datos[$coun]['ss']=0.00;    
                            if($product->producto_tiene_iva==1){
                                $datos[$coun]['iva']='SI';
                                $datos[$coun]['diva']=round((($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento))*($porcentaje->tarifa_iva_porcentaje/100),2);
                                $datos[$coun]['t12']=round(($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento),2);
                                $datos[$coun]['t0']=0;
                                if ($product->producto_tipo==1) {
                                    $datos[$coun]['sb']=round((($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento))*($porcentaje->tarifa_iva_porcentaje/100),2);
                                    $datos[$coun]['ss']=0;
                                }
                                else{
                                    $datos[$coun]['sb']=0;
                                    $datos[$coun]['ss']=round((($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento))*($porcentaje->tarifa_iva_porcentaje/100),2);
                                }
                                
                            }
                            $datos[$coun]['descuento']=round(floatval($adicional->descuento),2);
                            $datos[$coun]['valor']=floatval($adicional->precioUnitario);
                            $datos[$coun]['cantidad']=floatval($adicional->cantidad);
                            $datos[$coun]['subtotal2']=round($adicional->precioUnitario*$adicional->cantidad,2);
                            $datos[$coun]['subtotal']=round(($adicional->precioUnitario*$adicional->cantidad)-(floatval($adicional->descuento)),2);
                            if($product->producto_tipo==1){
                                $datos[$coun]['bien']='Bien';
                            }
                            else{
                                $datos[$coun]['bien']='Servicio';   
                            }
                            $datos[$coun]['total']=round((floatval($adicional->precioUnitario)*floatval($adicional->cantidad))+$datos[$coun]['diva']-floatval($adicional->descuento),2);
                            $coun++;
                        }
                       
                    }
                    DB::commit();
                    return view('admin.compras.xml.nuevo',['totalxml'=>$totalxml,'civa'=>$iva,'datos'=>$datos,'caduca'=>$data['validTo_time_t'],'poveedorXML'=>$poveedorXML,'xml'=>$xmlEnvio,'cajaAbierta'=>$cajaAbierta,'rangoDocumento'=>$rangoDocumento,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'bodegas'=>Bodega::bodegasSucursal($request->get('punto'))->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'comprobantes'=>Tipo_Comprobante::tipos()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                }
                DB::commit();
                return view('admin.compras.xml.nuevo',['totalxml'=>$totalxml,'civa'=>$iva,'datos'=>$datos,'caduca'=>$data['validTo_time_t'],'cajaAbierta'=>$cajaAbierta,'rangoDocumento'=>$rangoDocumento,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'bodegas'=>Bodega::bodegasSucursal($request->get('punto'))->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'comprobantes'=>Tipo_Comprobante::tipos()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                DB::commit();
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir retenciones, configueros y vuelva a intentar');
            }
       
    }
    public function cargar(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            if($request->file('file_sri')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '-SRI.' .$request->file('file_sri')->getClientOriginalExtension();
                $path = $request->file('file_sri')->move(public_path().'\temp', $name); 
                $contenido = file_get_contents ($path);
                $contenido = utf8_encode($contenido);
                $registros = explode ( "\n", $contenido);
                for ($i = 2; $i < sizeof($registros); $i ++) {
                    $data[$i] = explode("\t", $registros[$i]);
                    if(count($data[$i])>1){
                        $transaccion = Transaccion_Compra::TransaccionByAutorizacion($data[$i][9])->first();
                        if(isset($transaccion->transaccion_id) == false){
                            if($data[$i][0] == 'Factura' or $data[$i][0] == 'Notas de Crédito' or $data[$i][0] == 'Notas de Débito'){
                                $datos[$count]['proveedor'] = $data[$i][3];
                                $datos[$count]['fecha'] = $data[$i][4];
                                $datos[$count]['numero'] = $data[$i][1];
                                $datos[$count]['clave'] = $data[$i][9];
                                $datos[$count]['doc'] = $data[$i][0];       
                                $data[$i]=explode("\t", $registros[$i+1]);   
                                $datos[$count]['total'] = $data[$i][0];          
                                $count ++;
                            }
                        }   
                    }                    
                }       
               // return $datos;     
            }
            return view('admin.compras.xml.index',['datos'=>$datos,'punto'=>$request->get('puntoID'),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function procesarproducto($clave,$punto,$total)
    {
        try{
            $firmaElectronica = Firma_Electronica::firma()->first();
            $pubKey =Crypt::decryptString($firmaElectronica->firma_pubKey);
            $data=openssl_x509_parse($pubKey,true);
            $datos=null;
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $rangoDocumento=Rango_Documento::PuntoRango($punto,'Comprobante de Retención')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $secuencial=1;
            
            $productos=Producto::Productos()->get();
            $iva[1]['codigo']='02';
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Retencion_Compra::secuencial($rangoDocumento->rango_id)->max('retencion_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $electrocnico = new facturacionElectronicaController();
                $consultaDoc = $electrocnico->consultarDOC($clave);
                
                if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {
                    
                    $xmlEnvio = simplexml_load_string($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['comprobante']);
                   
                    foreach ($xmlEnvio->detalles->detalle as $adicional) {
                        if ($adicional->impuestos->impuesto->codigoPorcentaje>0) {
                            $iva[1]['codigo']='0'.$adicional->impuestos->impuesto->codigoPorcentaje;
                        }
                    }
                   
                    $poveedorXML = Proveedor::ProveedoresByRuc($xmlEnvio->infoTributaria->ruc)->first();
                    if(!$poveedorXML){
                        return redirect('/transaccionCompra/new/'.$punto)->with('error','No tiene resgistrado el proveedor, configure y vuelva a intentar');
                    }
                    $codigos = Codigo_Producto::buscarproductoproveedor($poveedorXML->proveedor_id)->get();
                    $coun=1;
                   
                    foreach($xmlEnvio->detalles->detalle as $adicional){ 
                        $activador=false;
                        $vari=trim(strval($adicional->codigoPrincipal));
                        foreach ($codigos as $codigo) {
                            if( $vari==$codigo->codigo_nombre){
                                $activador=true;
                            }
                            
                        }
                        if ($datos!=null) {
                            for ($i = 1; $i <= count($datos); ++$i) {
                                if ($datos[$i]['codigo']==$vari) {
                                    $activador=true;
                                }
                            }
                        }   
                        if($activador==false){
                            $datos[$coun]['codigo']=$vari;
                            $datos[$coun]['descripcion']=$adicional->descripcion;
                            $datos[$coun]['valor']=$adicional->precioUnitario;
                            $datos[$coun]['cantidad']=$adicional->cantidad;
                            $coun++;
                        }
                       
                    }
                   
                   
                        return view('admin.compras.xml.productos',['total'=>$total,'clave'=>$clave,'punto'=>$punto,'productos'=>$productos,'datos'=>$datos,'poveedorXML'=>$poveedorXML,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                    
                    
                    
                   
                }
               
                return view('admin.compras.xml.productos',['total'=>$total,'clave'=>$clave,'punto'=>$punto,'productos'=>$productos,'datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                    
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir retenciones, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function procesar($clave,$punto,$total)
    {
        try{
            $firmaElectronica = Firma_Electronica::firma()->first();
            $pubKey =Crypt::decryptString($firmaElectronica->firma_pubKey);
            $data=openssl_x509_parse($pubKey,true);
            $datos=null;
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $rangoDocumento=Rango_Documento::PuntoRango($punto,'Comprobante de Retención')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $secuencial=1;
            $totalxml=0;
            $activacion=false;
            $iva[1]['codigo']='02';
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Retencion_Compra::secuencial($rangoDocumento->rango_id)->max('retencion_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                $electrocnico = new facturacionElectronicaController();
                $consultaDoc = $electrocnico->consultarDOC($clave);
                
                if ($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] == 'AUTORIZADO') {          
                    $xmlEnvio = simplexml_load_string($consultaDoc['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['comprobante']);
                    foreach ($xmlEnvio->detalles->detalle as $adicional) {
                        foreach($adicional->impuestos->impuesto as $adicion){
                            if ($adicion->codigoPorcentaje>0) {
                                $iva[1]['codigo']='0'.$adicion->codigoPorcentaje;
                            }
                        }    
                    }
                    //return(json_encode($xmlEnvio));
                    $porcentaje=Tarifa_Iva::TarifaIvaCodigo($iva[1]['codigo'])->first();
                
                    $poveedorXML = Proveedor::ProveedoresByRuc($xmlEnvio->infoTributaria->ruc)->first();
                    if(!$poveedorXML){
                        return redirect('/transaccionCompra/new/'.$punto)->with('error','No tiene resgistrado el proveedor, configure y vuelva a intentar');
                    }
                    $codigos = Codigo_Producto::buscarproductoproveedor($poveedorXML->proveedor_id)->get();
                    $coun=1;
                   
                        
                    $totalxml=$total;
                           
                    
                    
                    foreach($xmlEnvio->detalles->detalle as $adicional){ 
                        
                        
                        $activador=false;
                        $vari=trim(strval($adicional->codigoPrincipal));
                        foreach ($codigos as $codigo) {
                            if( $vari==$codigo->codigo_nombre){
                                $activador=true;
                            }
                        }
                        if ($activador==true) {
                            if ($datos!=null) {
                                $product=Codigo_Producto::buscarproducto($vari, $poveedorXML->proveedor_id)->first();
                                for ($i = 1; $i <= count($datos); ++$i) {
                                    if ($product->producto_id==$datos[$i]['id']) {
                                        $datos[$i]['cantidad']=$datos[$i]['cantidad']+floatval($adicional->cantidad);
                                        $datos[$i]['subtotal2']=round($adicional->precioUnitario*$datos[$i]['cantidad'], 2);
                                        $datos[$i]['subtotal']=round(($adicional->precioUnitario*$datos[$i]['cantidad'])-floatval($adicional->descuento), 2);
                                        $datos[$i]['t0']=round(($adicional->precioUnitario*$datos[$i]['cantidad'])-floatval($adicional->descuento), 2);
                                        $datos[$i]['descuento']=$datos[$i]['descuento']+round(floatval($adicional->descuento), 2);
                                        if ($product->producto_tiene_iva==1) {
                                            $datos[$i]['diva']=round((($adicional->precioUnitario*$datos[$i]['cantidad'])-$datos[$i]['descuento'])*($porcentaje->tarifa_iva_porcentaje/100), 2);
                                            $datos[$i]['t12']=round($adicional->precioUnitario*$datos[$i]['cantidad'], 2)-round(floatval($adicional->descuento), 2);
                                            $datos[$i]['t0']=0;
                                            if ($product->producto_tipo==1) {
                                                $datos[$i]['sb']=round((($adicional->precioUnitario*$datos[$i]['cantidad'])-$datos[$i]['descuento'])*($porcentaje->tarifa_iva_porcentaje/100), 2);
                                                $datos[$i]['ss']=0;
                                            }
                                            else{
                                                $datos[$i]['sb']=0.00;
                                                $datos[$i]['ss']=round((($adicional->precioUnitario*$datos[$i]['cantidad'])-$datos[$i]['descuento'])*($porcentaje->tarifa_iva_porcentaje/100), 2);
                                            }
                                        }
                                        $datos[$i]['total']=$datos[$i]['subtotal2']+$datos[$i]['diva']-$datos[$i]['descuento'];

                                        $activador=false;
                                    }
                                }
                            }
                        }
                        if($activador==true){
                            $product=Codigo_Producto::buscarproducto($vari,$poveedorXML->proveedor_id)->first();
                            $datos[$coun]['id']=$product->producto_id;
                            $datos[$coun]['codigo']=$product->producto_codigo;
                            $datos[$coun]['descripcion']=$product->producto_nombre;
                            $datos[$coun]['t12']=0;
                            $datos[$coun]['iva']='NO';
                            $datos[$coun]['diva']=0.00;
                            $datos[$coun]['sb']=0.00;
                            $datos[$coun]['t0']=round(($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento),2); 
                            $datos[$coun]['ss']=0.00;
                            if($product->producto_tiene_iva==1){
                                $datos[$coun]['iva']='SI';
                                $datos[$coun]['diva']=round((($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento))*($porcentaje->tarifa_iva_porcentaje/100),2);
                                $datos[$coun]['t12']=round(($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento),2);
                                $datos[$coun]['t0']=0;
                                if ($product->producto_tipo==1) {
                                    $datos[$coun]['sb']=round((($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento))*($porcentaje->tarifa_iva_porcentaje/100),2);
                                    $datos[$coun]['ss']=0;
                                }
                                else{
                                    $datos[$coun]['sb']=0.00;
                                    $datos[$coun]['ss']=round((($adicional->precioUnitario*$adicional->cantidad)-($adicional->descuento))*($porcentaje->tarifa_iva_porcentaje/100),2);
                                }
                                
                            }
                            $datos[$coun]['descuento']=round(floatval($adicional->descuento),2);
                            $datos[$coun]['valor']=floatval($adicional->precioUnitario);
                            $datos[$coun]['cantidad']=floatval($adicional->cantidad);
                            $datos[$coun]['subtotal2']=round($adicional->precioUnitario*$adicional->cantidad,2);
                            $datos[$coun]['subtotal']=round(($adicional->precioUnitario*$adicional->cantidad)-floatval($adicional->descuento),2);
                            if($product->producto_tipo==1){
                                $datos[$coun]['bien']='Bien';
                            }
                            else{
                                $datos[$coun]['bien']='Servicio';   
                            }
                            $datos[$coun]['total']=round(floatval(($adicional->precioUnitario)*floatval($adicional->cantidad))+$datos[$coun]['diva']-$datos[$coun]['descuento'],2);
                            $coun++;
                        }
                        
                    }
                   
                    return view('admin.compras.xml.nuevo',['totalxml'=>$totalxml,'civa'=>$iva,'datos'=>$datos,'caduca'=>$data['validTo_time_t'],'poveedorXML'=>$poveedorXML,'xml'=>$xmlEnvio,'cajaAbierta'=>$cajaAbierta,'rangoDocumento'=>$rangoDocumento,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'bodegas'=>Bodega::bodegasSucursal($punto)->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'comprobantes'=>Tipo_Comprobante::tipos()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                }
                return view('admin.compras.xml.nuevo',['totalxml'=>$totalxml,'datos'=>$datos,'caduca'=>$data['validTo_time_t'],'cajaAbierta'=>$cajaAbierta,'rangoDocumento'=>$rangoDocumento,'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),'conceptosFuente'=>Concepto_Retencion::ConceptosFuente()->get(),'conceptosIva'=>Concepto_Retencion::ConceptosIva()->get(),'centros'=>Centro_Consumo::CentroConsumos()->get(),'bodegas'=>Bodega::bodegasSucursal($punto)->get(),'sustentos'=>Sustento_Tributario::Sustentos()->get(),'comprobantes'=>Tipo_Comprobante::tipos()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir retenciones, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
       
    }
}
