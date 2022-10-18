<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guia_Remision;
use App\Models\Punto_Emision;
use App\Models\Cliente;
use App\Models\Orden_Despacho;
use App\Models\Tarifa_Iva;
use App\Models\Bodega;
use App\Models\Vendedor;
use App\Models\Forma_Pago;
use App\Models\Detalle_GR;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Rango_Documento;
use App\Models\Transportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use PDF;
use DateTime;

class guiaremisionController extends Controller
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
            $puntoEmisiones = Punto_Emision::puntos()->get();   
            $clientes = Guia_Remision::ClientesDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();
            $estados=Guia_Remision::EstadoDistinsc()->select('gr_estado')->distinct()->get();
            $sucursales=Guia_Remision::SucursalDistinsc()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            return view('admin.ventas.guiaremision.view',['sucursales'=>$sucursales,'estados'=>$estados,'clientes'=>$clientes, 'puntoEmisiones'=>$puntoEmisiones,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            return redirect('/denegado');
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
        //
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
            DB::beginTransaction();
            
            $cantidad = $request->get('Dcantidad');
            
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $transportist=Transportista::Transportista($request->get('transportistas'))->first();
            /********************cabecera de proforma de venta ********************/
            $general = new generalController();   
            $docElectronico = new facturacionElectronicaController();        
            $guia = new Guia_Remision();
            $cierre = $general->cierre($request->get('guia_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/guiaRemision/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $guia->gr_numero = $request->get('guia_serie').substr(str_repeat(0, 9).$request->get('guia_numero'), - 9);
            $guia->gr_serie = $request->get('guia_serie');
            $guia->gr_secuencial = $request->get('guia_numero');
            $guia->gr_fecha = $request->get('guia_fecha');
            $guia->gr_fecha_inicio = $request->get('traslado_fecha');
            $guia->gr_fecha_fin = $request->get('traslado_fecha_fin');
            if($request->get('aduana')){
                $guia->gr_doc_aduanero = $request->get('aduana');
            }else{
                $guia->gr_doc_aduanero = '';
            }
            if($request->get('gabarra')){
                $guia->gr_gabarra = $request->get('gabarra');
            }
            $guia->gr_punto_partida = $request->get('partida'); 
            $guia->gr_punto_destino = $request->get('llegada'); 
            $guia->gr_ruta = $request->get('motivo'); 
            $guia->gr_placa=$transportist->transportista_placa;
            $guia->gr_motivo = $request->get('motivo');       
            $guia->gr_comentario = $request->get('gr_comentario');       
            $guia->gr_emision = $request->get('tipoDoc');
            $guia->gr_ambiente = 'PRODUCCIÓN';
            $guia->gr_autorizacion = $docElectronico->generarClaveAcceso($guia->gr_numero,$request->get('guia_fecha'),"06");              
            $guia->gr_estado = '1';
            $guia->bodega_id = $request->get('bodega_id');
            $guia->cliente_id = $request->get('clienteID');
             
            $guia->transportista_id = $request->get('transportistas');     
           
            $guia->rango_id = $request->get('rango_id');   
            $guia->save();
            $general->registrarAuditoria('Registro de Guia de Remision numero -> '.$guia->gr_numero,$guia->gr_numero,'Registro de Guia de Remision numero -> '.$guia->gr_numero.' con cliente -> '.$request->get('buscarCliente').' con transportistas -> '.$request->get('transportistas'));
            /*******************************************************************/
            /********************detalle de factura de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleGR = new Detalle_GR();
                $detalleGR->detalle_cantidad = $cantidad[$i];               
                $detalleGR->detalle_estado = '1';
                $detalleGR->producto_id = $isProducto[$i];              
                $guia->detalles()->save($detalleGR);
                $general->registrarAuditoria('Registro de detalle de Guia de Remision numero -> '.$guia->gr_numero,$guia->gr_numero,'Registro de detalle de Guia de Remision numero -> '.$guia->gr_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i] );
            }       
            /*Generar documento electronico*/
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
            DB::commit();
            if($guiaAux->gr_xml_estado == 'AUTORIZADO'){
                return redirect('/guiaRemision/new/'.$request->get('punto_id'))->with('success','Guia de Remision registrada y autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('guia_fecha'))->format('d-m-Y').'/'.$guiaAux->gr_xml_nombre.'.pdf');
            }elseif($guia->gr_emision != 'ELECTRONICA'){
                return redirect('/guiaRemision/new/'.$request->get('punto_id'))->with('success','Guia de Remision registrada exitosamente');
            }else{
                return redirect('/guiaRemision/new/'.$request->get('punto_id'))->with('success','Guia de Remision registrada exitosamente')->with('error2','ERROR SRI--> '.$guiaAux->gr_xml_estado.' : '.$guiaAux->gr_xml_mensaje);
            }           
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/guiaRemision/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function enviar(Request $request)
    {
        try {
            DB::beginTransaction();
            $cantidad = $request->get('Dcantidad');
            $orden = $request->get('Dorden');
           
            $isProducto = $request->get('Dcodigo');
            $nombre = $request->get('Dnombre');
            $transportist=Transportista::Transportista($request->get('transportistas'))->first();
            /********************cabecera de proforma de venta ********************/
            $general = new generalController();
            $cierre = $general->cierre($request->get('guia_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);                 
            if($cierre){
                return redirect('listaOrdenes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $docElectronico = new facturacionElectronicaController();
            $guia = new Guia_Remision();
            $guia->gr_numero = $request->get('guia_serie').substr(str_repeat(0, 9).$request->get('guia_numero'), - 9);
            $guia->gr_serie = $request->get('guia_serie');
            $guia->gr_secuencial = $request->get('guia_numero');
            $guia->gr_fecha = $request->get('guia_fecha');
            $guia->gr_fecha_inicio = $request->get('traslado_fecha');
            $guia->gr_fecha_fin = $request->get('traslado_fecha_fin');
            if ($request->get('aduana')) {
                $guia->gr_doc_aduanero = $request->get('aduana');
            } else {
                $guia->gr_doc_aduanero = '';
            }
           
            $guia->gr_punto_partida = $request->get('partida');
            $guia->gr_punto_destino = $request->get('llegada');
            $guia->gr_ruta = $request->get('motivo');
            $guia->gr_placa=$transportist->transportista_placa;
            $guia->gr_motivo = $request->get('motivo');
            $guia->gr_comentario = $request->get('gr_comentario');
            $guia->gr_emision = $request->get('tipoDoc');
            $guia->gr_ambiente = 'PRODUCCIÓN';
            $guia->gr_autorizacion = $docElectronico->generarClaveAcceso($guia->gr_numero, $request->get('guia_fecha'), "06");
            $guia->gr_estado = '1';
            $guia->bodega_id = $request->get('bodega_id');
            $guia->cliente_id = $request->get('clienteID');
             
            $guia->transportista_id = $request->get('transportistas');
           
            $guia->rango_id = $request->get('rango_id');
            $guia->save();
            $general->registrarAuditoria('Registro de Guia de Remision numero -> '.$guia->gr_numero, $guia->gr_numero, 'Registro de Guia de Remision numero -> '.$guia->gr_numero.' con cliente -> '.$request->get('buscarCliente').' con transportistas -> '.$request->get('transportistas'));
            /*******************************************************************/
            for ($k = 0; $k < count($orden); ++$k) {
                $ordene = Orden_Despacho::findOrFail($orden[$k]);
                
                $ordene->Guia()->associate($guia);
                $ordene->orden_estado='2';
                $ordene->update();
                $general->registrarAuditoria('Actualizacion de Orden de Despacho -> '.$ordene->orden_numero,$ordene->orden_numero,'Actualizacion de Orden de Despacho -> '.$ordene->orden_numero.' con Guia de remision -> '.$guia->gr_numero.' con transportistas -> '.$request->get('transportistas'));
            
            }
            /********************detalle de factura de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleGR = new Detalle_GR();
               
                $detalleGR->detalle_cantidad = $cantidad[$i]; 
                       
                $detalleGR->detalle_estado = '1';
                $detalleGR->producto_id = $isProducto[$i];              
                $guia->detalles()->save($detalleGR);
                $general->registrarAuditoria('Registro de detalle de Guia de Remision numero -> '.$guia->gr_numero,$guia->gr_numero,'Registro de detalle de Guia de Remision numero -> '.$guia->gr_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i] );
            }    
            /*Generar documento electronico*/
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
            DB::commit();
            if($guia->gr_emision != 'ELECTRONICA'){
                return redirect('listaOrdenes')->with('success','Guia de Remision registrada exitosamente');
            }elseif($guiaAux->gr_xml_estado == 'AUTORIZADO'){
                return redirect('/listaOrdenes')->with('success','Guia de Remision registrada y autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('guia_fecha'))->format('d-m-Y').'/'.$guiaAux->gr_xml_nombre.'.pdf');
            }else{
                return redirect('/listaOrdenes')->with('success','Guia de Remision registrada exitosamente')->with('error2','ERROR SRI--> '.$guiaAux->gr_xml_estado.' : '.$guiaAux->gr_xml_mensaje);
            }               
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaOrdenes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function enviarcm(Request $request)
    {
        try {
            DB::beginTransaction();
            $cantidad = $request->get('Dcantidad');
            $orden = $request->get('Dorden');
           
            $isProducto = $request->get('Dcodigo');
            $nombre = $request->get('Dnombre');
            $transportist=Transportista::Transportista($request->get('transportistas'))->first();
            /********************cabecera de proforma de venta ********************/
            $general = new generalController();
            $cierre = $general->cierre($request->get('guia_fecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            
            if($cierre){
                return redirect('listaOrdenes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $docElectronico = new facturacionElectronicaController();
            $guia = new Guia_Remision();
            $guia->gr_numero = $request->get('guia_serie').substr(str_repeat(0, 9).$request->get('guia_numero'), - 9);
            $guia->gr_serie = $request->get('guia_serie');
            $guia->gr_secuencial = $request->get('guia_numero');
            $guia->gr_fecha = $request->get('guia_fecha');
            $guia->gr_fecha_inicio = $request->get('traslado_fecha');
            $guia->gr_fecha_fin = $request->get('traslado_fecha_fin');
            if ($request->get('aduana')) {
                $guia->gr_doc_aduanero = $request->get('aduana');
            } else {
                $guia->gr_doc_aduanero = '';
            }
            if($request->get('gabarra')){
                $guia->gr_gabarra = $request->get('gabarra');
            }
            $guia->gr_punto_partida = $request->get('partida');
            $guia->gr_punto_destino = $request->get('llegada');
            $guia->gr_ruta = $request->get('motivo');
            $guia->gr_placa=$transportist->transportista_placa;
            $guia->gr_motivo = $request->get('motivo');
            $guia->gr_comentario = $request->get('gr_comentario');
            $guia->gr_emision = $request->get('tipoDoc');
            $guia->gr_ambiente = 'PRODUCCIÓN';
            $guia->gr_autorizacion = $docElectronico->generarClaveAcceso($guia->gr_numero, $request->get('guia_fecha'), "06");
            $guia->gr_estado = '1';
            $guia->bodega_id = $request->get('bodega_id');
            $guia->cliente_id = $request->get('clienteID');
             
            $guia->transportista_id = $request->get('transportistas');
           
            $guia->rango_id = $request->get('rango_id');
            $guia->save();
            $general->registrarAuditoria('Registro de Guia de Remision numero -> '.$guia->gr_numero, $guia->gr_numero, 'Registro de Guia de Remision numero -> '.$guia->gr_numero.' con cliente -> '.$request->get('buscarCliente').' con transportistas -> '.$request->get('transportistas'));
            /*******************************************************************/
            for ($k = 0; $k < count($orden); ++$k) {
                $ordene = Orden_Despacho::findOrFail($orden[$k]);
                
                $ordene->Guia()->associate($guia);
                $ordene->orden_estado='2';
                $ordene->update();
                $general->registrarAuditoria('Actualizacion de Orden de Despacho -> '.$ordene->orden_numero,$ordene->orden_numero,'Actualizacion de Orden de Despacho -> '.$ordene->orden_numero.' con Guia de remision -> '.$guia->gr_numero.' con transportistas -> '.$request->get('transportistas'));
            
            }
            /********************detalle de factura de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detalleGR = new Detalle_GR();
               
                $detalleGR->detalle_cantidad = $cantidad[$i]; 
                       
                $detalleGR->detalle_estado = '1';
                $detalleGR->producto_id = $isProducto[$i];              
                $guia->detalles()->save($detalleGR);
                $general->registrarAuditoria('Registro de detalle de Guia de Remision numero -> '.$guia->gr_numero,$guia->gr_numero,'Registro de detalle de Guia de Remision numero -> '.$guia->gr_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i] );
            }    
            /*Generar documento electronico*/
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
            DB::commit();
            if($guia->gr_emision != 'ELECTRONICA'){
                return redirect('listaOrdenes')->with('success','Guia de Remision registrada exitosamente');
            }elseif($guiaAux->gr_xml_estado == 'AUTORIZADO'){
                return redirect('/listaOrdenes')->with('success','Guia de Remision registrada y autorizada exitosamente')->with('pdf','documentosElectronicos/'.Empresa::Empresa()->first()->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $request->get('guia_fecha'))->format('d-m-Y').'/'.$guiaAux->gr_xml_nombre.'.pdf');
            }else{
                return redirect('/listaOrdenes')->with('success','Guia de Remision registrada exitosamente')->with('error2','ERROR SRI--> '.$guiaAux->gr_xml_estado.' : '.$guiaAux->gr_xml_mensaje);
            }               
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaOrdenes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try {    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $guia=Guia_Remision::Guia($id)->first();
            $Accion='Visualizar';
            if($guia){
                return view('admin.ventas.guiaremision.visualizarorden',['Accion'=>$Accion,'guias'=>$guia,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
        try {    
            DB::beginTransaction();

            $guias = Guia_Remision::findOrFail($id);
            $general = new generalController();
            $cierre = $general->cierre($guias->gr_fecha,$guias->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('eliminacionComprantes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            if (count($guias->ordenes)>0) {
                foreach ($guias->ordenes as $orden) {
                    $ordene=Orden_Despacho::findOrFail($orden->orden_id);
                    $ordene->gr_id=null;
                    $ordene->orden_estado='1';
                    $ordene->save();
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Actualizacion del Id a null para la eliminacion de la Guia de remision N° '.$guias->gr_numero.'relacionado a la orden de despacho'.$ordene->orden_numero, $guias->gr_numero, 'Permiso con id -> '.$id);
                }
            }
            
            foreach ($guias->detalles as $guia) {
                $guia->delete();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Eliminacion del detalle de la Guia Remision N°-> '.$guias->gr_numero,$guias->gr_numero,'Eliminacion del detalle de la Guia Remision N°-> '.$guias->gr_numero. ' con producto nombre -> '.$guia->producto->producto_nombre.' cantidad -> '.$guia->detalle_cantidad);
            } 
            $guias->delete();
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de Guia Remision N°-> '.$guias->gr_numero,$guias->gr_numero,'Permiso con id -> '.$id);
            
            DB::commit();
            return redirect('eliminacionComprantes')->with('success','Datos eliminados exitosamente');
        }
        catch(\Exception $ex){  
            DB::rollBack();    
            return redirect('eliminacionComprantes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }

    public function nuevo($id){
        try {    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Guías de Remisión')->first();
            $secuencial=1;
        
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Guia_Remision::secuencial($rangoDocumento->rango_id)->max('gr_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;
                }
                return view('admin.ventas.guiaremision.nuevo',
                    ['clientes'=>Cliente::Clientes()->get(),
                    'trasportistas'=>Transportista::Transportistas()->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                    'bodegas'=>Bodega::bodegasSucursal($id)->get(),
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'gruposPermiso'=>$gruposPermiso, 
                    'permisosAdmin'=>$permisosAdmin]
                );
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }  
    public function newCM($id){
        try {    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Guías de Remisión')->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Guia_Remision::secuencial($rangoDocumento->rango_id)->max('gr_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;
                }
                return view('admin.ventas.guiaremision.index',
                    ['clientes'=>Cliente::Clientes()->get(),
                    'trasportistas'=>Transportista::Transportistas()->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 
                    'bodegas'=>Bodega::bodegasSucursal($id)->get(),
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'gruposPermiso'=>$gruposPermiso, 
                    'permisosAdmin'=>$permisosAdmin]
                );
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    } 
    public function delete($id)
    {
        try {    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $guia=Guia_Remision::Guia($id)->first();
            return view('admin.sri.eliminacionComprabantes.eliminarguia',['guias'=>$guia,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByIdTransportista($buscar){
        return Transportista::Transportista($buscar)->get();
    }

   

    public function consultar(Request $request)
    {
        try {    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();  
            $puntoEmisiones = Punto_Emision::puntos()->get();   
            $clientes = Guia_Remision::ClientesDistinsc()->select('cliente.cliente_id','cliente.cliente_nombre')->distinct()->get();  
            $estados=DB::table('guia_remision')->select('gr_estado')->distinct()->get();
            $sucursales=Guia_Remision::SucursalDistinsc()->select('sucursal.sucursal_id','sucursal.sucursal_nombre')->distinct()->get();
            $guias=null;
            $fechatodo=0;
           
            if($request->get('fecha_todo')){
                $fechatodo=$request->get('fecha_todo');
            }
            $guias=Guia_Remision::GuiasTodosDiferentes($fechatodo,$request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_cliente'),$request->get('estados'),$request->get('sucursal'))->get();

            return view('admin.ventas.guiaremision.view', ['idsucursal'=>$request->get('sucursal'),'sucursales'=>$sucursales,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'fecha_todo'=>$request->get('fecha_todo'),'nombre_cliente'=>$request->get('nombre_cliente'),'valorestados'=>$request->get('estados'),'estados'=>$estados,'guias'=>$guias, 'puntoEmisiones'=>$puntoEmisiones, 'clientes'=>$clientes,  'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('listaGuias')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    
    public function imprimir($id){ 
        try{
            $guias = Guia_Remision::findOrFail($id);
            $empresa =  Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.guiaremision', ['guias'=>$guias,'empresa'=>$empresa]);
            $ruta = public_path().'/DocumentosOffline/GuiasRemision/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $prefactura->prefactura_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'GR-'.$guias->gr_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->stream('guia.pdf');
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimirprefactura($id){ 
        try{
            $guias = Guia_Remision::findOrFail($id);
            $empresa =  Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.guiaremision', ['guias'=>$guias,'empresa'=>$empresa]);
            $ruta = public_path().'/prefacturacion/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $prefactura->prefactura_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'GR-'.$guias->gr_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->stream('guia.pdf');
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function veranular($id){
        try {    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $guia=Guia_Remision::Guia($id)->first();
            return view('admin.ventas.guiaremision.anular',['guias'=>$guia,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('listaGuiasOrdenes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function anular(Request $request){
        try {    
            DB::beginTransaction();
            $general = new generalController();
            $auditoria = new generalController();
            $guia=Guia_Remision::findOrFail($request->get('gr_id'));
            $cierre = $auditoria->cierre($guia->gr_fecha,$guia->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('egresoBodega')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
           
            $guia->gr_estado = '0';
            $guia->update();
            $general->registrarAuditoria('Anulacion de guia de remision -> '.$guia->gr_numero,$guia->gr_numero,'Anulacion de guia de remision -> '.$guia->gr_numero);
            foreach($guia->ordenes as $orden){
                $orden->gr_id = null;
                $orden->orden_estado='1';
                $orden->update();
                $general->registrarAuditoria('Actualizacion de Orden de Despacho -> '.$orden->orden_numero,$orden->orden_numero,'Actualizacion de Orden de Despacho -> '.$orden->orden_numero.' por anulacion de Guia de remision -> '.$guia->gr_numero);
            }                
            DB::commit();
            return redirect('listaGuiasOrdenes')->with('success','Datos anulados exitosamente');
        }
        catch(\Exception $ex){   
            DB::rollBack();       
            return redirect('listaGuiasOrdenes')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

}
