<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Proforma;


use App\Models\Bodega;

use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Cliente;
use App\Models\Detalle_Proforma;
use App\Models\Empresa;
use App\Models\Tarifa_Iva;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DateTime;
use PDF;
class proformaController extends Controller
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
            $clientes = Cliente::clientes()->get();
            $bodegas = Bodega::bodegas()->get();
            $puntoEmisiones = Punto_Emision::puntos()->get();
            $proforma=Proforma::Proformas()->get();
            return view('admin.ventas.proforma.view',['reporteproforma'=>$proforma,'clientes'=>$clientes, 'puntoEmisiones'=>$puntoEmisiones,'bodegas'=>$bodegas,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try{            
            DB::beginTransaction();
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');
            /********************cabecera de proforma de venta ********************/
            $general = new generalController();           
            $proforma = new Proforma();
            $proforma->proforma_numero = $request->get('proforma_serie').substr(str_repeat(0, 9).$request->get('proforma_numero'), - 9);
            $proforma->proforma_serie = $request->get('proforma_serie');
            $proforma->proforma_secuencial = $request->get('proforma_numero');
            $proforma->proforma_fecha = $request->get('proforma_fecha');
            $proforma->proforma_subtotal = $request->get('idSubtotal');
            $proforma->proforma_descuento = $request->get('idDescuento');
            $proforma->proforma_tarifa0 = $request->get('idTarifa0');
            $proforma->proforma_tarifa12 = $request->get('idTarifa12');
            $proforma->proforma_tarifa12 = $request->get('idTarifa12');
            $proforma->proforma_tarifa12 = $request->get('idTarifa12');
            $proforma->proforma_tipo_pago = $request->get('factura_tipo_pago');
            $proforma->proforma_dias_plazo = $request->get('factura_dias_plazo');

            $proforma->proforma_iva = $request->get('idIva');
            $proforma->proforma_total = $request->get('idTotal');
            if($request->get('factura_comentario')){
                $proforma->proforma_comentario = $request->get('factura_comentario');
            }else{
                $proforma->proforma_comentario = '';
            }
            $proforma->proforma_porcentaje_iva = $request->get('factura_porcentaje_iva');          
            $proforma->proforma_estado = '1';
            $proforma->bodega_id = $request->get('bodega_id');
            $proforma->cliente_id = $request->get('clienteID');
            $proforma->rango_id = $request->get('rango_id');             
            $proforma->save();
            $general->registrarAuditoria('Registro de proforma de venta numero -> '.$proforma->proforma_numero,$proforma->proforma_numero,'Registro de Proforma de venta numero -> '.$proforma->proforma_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal'));
            /*******************************************************************/
            /********************detalle de factura de venta********************/
            for ($i = 1; $i < count($cantidad); ++$i){
                $detallePF = new Detalle_Proforma();
                $detallePF->detalle_cantidad = $cantidad[$i];
                $detallePF->detalle_precio_unitario = $pu[$i];
                $detallePF->detalle_descuento = $descuento[$i];
                $detallePF->detalle_iva = $iva[$i];
                $detallePF->detalle_total = $total[$i];              
                $detallePF->detalle_estado = '1';
                $detallePF->producto_id = $isProducto[$i];               
                $proforma->detalles()->save($detallePF);
                $general->registrarAuditoria('Registro de detalle de proforma de venta numero -> '.$proforma->proforma_numero,$proforma->proforma_numero,'Registro de detalle de proforma de venta numero -> '.$proforma->proforma_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
            }       
            $proforma=Proforma::findOrFail($proforma->proforma_id);
            $url = $general->pdfProforma($proforma);    
            DB::commit();
            return redirect('/proforma/new/'.$request->get('punto_id'))->with('success','Proforma registrada exitosamente')->with('pdf',$url);
           
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/proforma/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function nuevo($id){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Proforma')->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Proforma::secuencial($rangoDocumento->rango_id)->max('proforma_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;
                }
                return view('admin.ventas.proforma.nuevo',
                    ['clientes'=>Cliente::Clientes()->get(),
                    'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),
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
    public function editar($id)
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $proforma=Proforma::Proforma($id)->first();
            
            $clientes = Cliente::clientes()->get(); 
        
            if($proforma){
                return view('admin.ventas.proforma.editar',['proforma'=>$proforma,'clientes'=>$clientes, 'bodegas'=>Bodega::bodegasSucursal($proforma->proforma_serie)->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(), 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
               
                return redirect('/denegado');
            }
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
    public function imprimir($id)
    {
        try{     
            $profor=Proforma::findOrFail($id);
            $empresa=Empresa::findOrFail(Auth::user()->empresa_id);
            $ruta = public_path().'/Profo/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $profor->proforma_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = $profor->proforma_numero. ".pdf";
            $view =  \View::make('admin.formatosPDF.pdfproforma', ['prof'=>$profor,'empresa'=>$empresa]);
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('rol.pdf');
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
        try{    
            DB::beginTransaction();
            $general = new generalController(); 
            $cantidad = $request->get('Dcantidad');

           
            $cantidad = $request->get('Dcantidad');
            $isProducto = $request->get('DprodcutoID');
            $nombre = $request->get('Dnombre');
            $iva = $request->get('DViva');
            $pu = $request->get('Dpu');
            $total = $request->get('Dtotal');
            $descuento = $request->get('Ddescuento');

            $proforma = Proforma::findOrFail($id);      
            $proforma->cliente_id = $request->get('clienteID');
            $proforma->proforma_subtotal = $request->get('idSubtotal');
            $proforma->proforma_tarifa0 = $request->get('idDescuento');
            $proforma->proforma_tarifa12 = $request->get('idTarifa12');
            $proforma->proforma_descuento = $request->get('idDescuento');  
            $proforma->proforma_iva = $request->get('idIva'); 
            $proforma->proforma_total = $request->get('idTotal');   
            $proforma->proforma_tipo_pago = $request->get('factura_tipo_pago');
            $proforma->proforma_dias_plazo = $request->get('factura_dias_plazo');
            
            $proforma->update();
            
            /*Inicio de registro de auditoria 
            */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de proforma de venta numero -> '.$proforma->proforma_numero,$proforma->proforma_numero,'Actualizacion de Proforma de venta numero -> '.$proforma->proforma_numero.' con cliente -> '.$request->get('buscarCliente').' con un total de -> '.$request->get('idTotal'));
          
              /*
            Fin de registro de auditoria */
            
            $proforma->detalles()->delete();
         
            for ($i = 1; $i < count($cantidad); ++$i){
                $detallePF = new Detalle_Proforma();
                $detallePF->detalle_cantidad = $cantidad[$i];
                $detallePF->detalle_precio_unitario = $pu[$i];
                $detallePF->detalle_descuento = $descuento[$i];
                $detallePF->detalle_iva = $iva[$i];
                $detallePF->detalle_total = $total[$i];              
                $detallePF->detalle_estado = '1';
                $detallePF->producto_id = $isProducto[$i];

                $proforma->detalles()->save($detallePF);
                $auditoria->registrarAuditoria('Actualizar el detalle de proforma de venta numero -> '.$proforma->proforma_numero,$proforma->proforma_numero,'Actualizar de detalle de proforma de venta numero -> '.$proforma->proforma_numero.' producto de nombre -> '.$nombre[$i].' con la cantidad de -> '.$cantidad[$i].' a un precio unitario de -> '.$pu[$i]);
            }
            $proforma=Proforma::findOrFail($proforma->proforma_id);
            $url = $general->pdfProforma($proforma);    
            DB::commit();
            return redirect('listaProforma')->with('success','Datos guardados exitosamente')->with('pdf',$url);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
