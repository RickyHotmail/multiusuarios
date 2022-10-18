<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_FV;
use App\Models\Detalle_OD;
use App\Models\Detalle_Pago_CXC;
use App\Models\Detalle_Rol;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Movimiento_Producto;
use App\Models\Orden_Despacho;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use DateTime;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listaFacturaController extends Controller
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
            $facturas=null;  
            $sucursal = Factura_Venta::SurcusalDistinsc()->select('sucursal.sucursal_id','sucursal_nombre')->distinct()->get(); 
            $estados = Factura_Venta::EstadoDistinsc()->select('factura_estado')->distinct()->get(); 
              
            return view('admin.ventas.listaFactura.index',['estados'=>$estados,'sucursal'=>$sucursal,'facturas'=>$facturas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $sucursal = Factura_Venta::SurcusalDistinsc()->select('sucursal.sucursal_id','sucursal_nombre')->distinct()->get(); 
            
            $estados = Factura_Venta::EstadoDistinsc()->select('factura_estado')->distinct()->get();   
            $facturas=Factura_Venta::Filtrar($request->get('fecha_todo'),$request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('descripcion'),$request->get('sucursal'),$request->get('estado_id'))->orderBy('factura_numero')->get();           
            return view('admin.ventas.listaFactura.index',['estados'=>$estados,'idestado'=>$request->get('estado_id'),'todo'=>$request->get('fecha_todo'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'idsucursal'=>$request->get('sucursal'),'descripcion'=>$request->get('descripcion'),'sucursal'=>$sucursal,'facturas'=>$facturas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        //
    }
    public function ver($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $facturas=Factura_Venta::Factura($id)->get()->first();      
            return view('admin.ventas.listaFactura.view',['facturas'=>$facturas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminar($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $facturas=Factura_Venta::Factura($id)->get()->first();      
            return view('admin.sri.eliminacionComprabantes.eliminarfactura',['facturas'=>$facturas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();        
            $facturas=Factura_Venta::Factura($id)->get()->first();
            
            $general = new generalController();
            
            $cierre = $general->cierre($facturas->factura_fecha,Rango_Documento::rango($facturas->rango_id)->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('eliminacionComprantes')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $general3 = new generalController();

            foreach($facturas->detalles as $detalles){

                $detall=Detalle_FV::findOrFail($detalles->detalle_id);
                $detall->movimiento_id=null;
                $detall->save();

                $aux = $detalles->movimiento; 
                $detalles->movimiento->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion de Movimiento de producto por factura de venta numero: -> '.$facturas->factura_numero, $id, 'Con Producto '.$detalles->producto->producto_nombre.' Con la cantidad de producto de '.$aux->movimiemovimiento_totalnto_cantidad.' y total '.$aux->movimiento_total  );
                
                
                $aux = $detalles; 
                $detalles->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion de detalles de producto por factura de venta numero: -> '.$facturas->factura_numero, $id, 'Con Producto '.$aux->producto->producto_nombre.' Con la cantidad de producto de '.$aux->detalle_cantidad.' y total '.$aux->detalle_total);


            }
            $factur=Factura_Venta::findOrFail($facturas->factura_id);
            $factur->cuenta_id=null;
            $factur->save();
            
    
            foreach($facturas->cuentaCobrar->detallepago as $detalle){
                $cuenta=Detalle_Pago_CXC::findOrFail($detalle->detalle_pago_id);
                $cuenta->pago_id=null;
                $cuenta->cuenta_id=null;
                $cuenta->save();
                

                $aux=$detalle->pagoCXC;
                $detalle->pagoCXC->delete();
                $general3->registrarAuditoria('Eliminacion del detalle del pago de la cuenta por cobrar por factura de venta numero: -> '.$facturas->factura_numero, $id, ' Descripcion -> '.$aux->detalle_pago_descripcion.' Con el valor-> '.$aux->detalle_pago_valor);

                
                $aux=$detalle;
                $detalle->delete();
                $general3->registrarAuditoria('Eliminacion de la cuenta por cobrar  por factura de venta numero: -> '.$facturas->factura_numero, $id, 'Tipo '.$aux->cuenta_tipo.' con Descripcion -> '.$aux->cuenta_descripcion.' Con el valor-> '.$aux->cuenta_monto);   
            }   
            $aux=$facturas->cuentaCobrar;
            $facturas->cuentaCobrar->delete();                        
            $general3 = new generalController();
            $general3->registrarAuditoria('Eliminacion de la cuenta por cobrar por factura de venta numero: -> '.$facturas->factura_numero, $id, ' Descripcion -> '.$aux->cuenta_descripcion.' Con el monto-> '.$aux->cuenta_monto);


            foreach ($facturas->diario->detalles as $detalles) {
                $aux=$detalles;
                $detalles->delete();
                $general = new generalController();
                $general->registrarAuditoria('Eliminacion de detalle de diario por factura de venta numero: -> '.$facturas->factura_numero, $id,  'Con id de diario-> '.$aux->diario_id.'Comentario -> '.$aux->detalle_comentario  );
            }

            $factur=Factura_Venta::findOrFail($facturas->factura_id);
            $factur->diario_id=null;
            $factur->save();

            $facturas->diario->delete();                        
            $general3 = new generalController();
            $general3->registrarAuditoria('Eliminacion de diario por factura de venta numero: -> '.$facturas->factura_numero, $id, 'Con el diario-> '.$aux->diario_codigo.'Comentario -> '.$aux->diario_comentario);
            if(isset($facturas->diarioCosto)){
                foreach ($facturas->diarioCosto->detalles as $detalles) {
                    $aux=$detalles;
                    $detalles->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion de detalle de diario por factura de venta numero: -> '.$facturas->factura_numero, $id,  'Con id de diario-> '.$aux->diario_id.'Comentario -> '.$aux->detalle_comentario  );
                }
                $factur=Factura_Venta::findOrFail($facturas->factura_id);
                $factur->diario_costo_id=null;
                $factur->save();
            
                $aux = $facturas->diarioCosto;
                $facturas->diarioCosto->delete();                        
                $general3 = new generalController();
                $general3->registrarAuditoria('Eliminacion de diario por factura de venta numero: -> '.$facturas->factura_numero, $id, 'Con el diario-> '.$aux->diario_codigo.'Comentario -> '.$aux->diario_comentario);
            } 

            $aux=$facturas;
            $facturas->delete();                        
            $general3 = new generalController();
            $general3->registrarAuditoria('Eliminacion de la factura de venta numero: -> '.$facturas->factura_numero, $id, 'Con el valor de -> '.$aux->factura_total);

            DB::commit();
            return redirect('eliminacionComprantes')->with('success','Datos Eliminados exitosamente');
                        
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('eliminacionComprantes')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
        
    }
    public function ordenDespacho($id){
        try{
            DB::beginTransaction(); 
            $factura = Factura_Venta::findOrFail($id);
            $general = new generalController();
            $auditoria = new generalController();
            $cierre = $auditoria->cierre($factura->factura_fecha,Rango_Documento::rango($factura->rango_id)->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('listaFactura')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $rangoDocumentoorden=Rango_Documento::PuntoRango($factura->rangoDocumento->punto_id, 'Orden de Despacho')->first();
            if($rangoDocumentoorden){
                $secuencial=$rangoDocumentoorden->rango_inicio;
                $secuencialAux=Orden_Despacho::secuencial($rangoDocumentoorden->rango_id)->max('orden_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
            
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir ordenes de despacho, configueros y vuelva a intentar');
            }

            $orden = new Orden_Despacho();
            $orden->orden_numero = $rangoDocumentoorden->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoorden->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
            $orden->orden_serie = $rangoDocumentoorden->puntoEmision->sucursal->sucursal_codigo.$rangoDocumentoorden->puntoEmision->punto_serie;
            $orden->orden_secuencial = $secuencial;
            $orden->orden_fecha = $factura->factura_fecha;
            $orden->orden_tipo_pago = $factura->factura_tipo_pago;
            $orden->orden_dias_plazo = $factura->factura_dias_plazo;
            $orden->orden_fecha_pago = $factura->factura_fecha_pago;
            $orden->orden_subtotal = $factura->factura_subtotal;
            $orden->orden_descuento = $factura->factura_descuento;
            $orden->orden_tarifa0 = $factura->factura_tarifa0;
            $orden->orden_tarifa12 = $factura->factura_tarifa12;
            $orden->orden_iva = $factura->factura_iva;
            $orden->orden_total = $factura->factura_total;
            $orden->orden_reserva ='0';
            $orden->orden_comentario = 'Orden de despacho con Factura N° '.$factura->factura_numero;
            $orden->orden_porcentaje_iva = $factura->factura_porcentaje_iva;
            $orden->orden_estado = '3';
            $orden->bodega_id = $factura->bodega_id;
            $orden->cliente_id = $factura->cliente_id;
            $orden->rango_id = $rangoDocumentoorden->rango_id;
            $orden->vendedor_id = $factura->vendedor_id;
            $orden->factura_id =$factura->factura_id;
            $orden->save();
            $general->registrarAuditoria('Registro de orden de Despacho numero -> '.$orden->orden_numero, $orden->orden_numero, 'Registro de orden de Despacho numero -> '.$orden->orden_numero.' con cliente -> '.$factura->cliente->cliente_nombre.' con un total de -> '.$factura->factura_total);
            /*******************************************************************/
            /********************detalle de factura de venta********************/
        
            foreach($factura->detalles as $detalle){
                $detalleOD = new Detalle_OD();
                $detalleOD->detalle_descripcion = $detalle->detalle_descripcion;
                $detalleOD->detalle_cantidad = $detalle->detalle_cantidad;
                $detalleOD->detalle_precio_unitario = $detalle->detalle_precio_unitario;
                $detalleOD->detalle_descuento = $detalle->detalle_descuento;
                $detalleOD->detalle_iva = $detalle->detalle_iva;
                $detalleOD->detalle_total = $detalle->detalle_total;
                $detalleOD->detalle_estado = '1';
                $detalleOD->producto_id = $detalle->producto_id;
                $orden->detalles()->save($detalleOD);
                $general->registrarAuditoria('Registro de detalle de orden de Despacho numero -> '.$orden->orden_numero, $orden->orden_numero, 'Registro de detalle de orden de Despacho numero -> '.$orden->orden_numero.' producto de nombre -> '.$detalle->producto->producto_nombre.' con la cantidad de -> '.$detalle->detalle_cantidad.' a un precio unitario de -> '.$detalle->detalle_precio_unitario);
            }
            DB::commit();
            return redirect('listaFactura')->with('success','Datos generados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaFactura')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimir($id){ 
        try{
            $factura = Factura_Venta::findOrFail($id);
            $empresa =  Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.facturaVenta', ['factura'=>$factura,'empresa'=>$empresa]);
            $ruta = public_path().'/DocumentosOffline/FacturaVenta/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'FAC-'.$factura->factura_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->stream('factura.pdf');
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimirRecibo($id){
        try{
            $factura=Factura_Venta::findOrFail($id);
            $general = new generalController();
            $url = $general->FacturaRecibo($factura,0);
            return $url;
        }catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
