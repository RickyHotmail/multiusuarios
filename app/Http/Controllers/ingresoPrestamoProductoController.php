<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Ingreso_Prestamo_Producto;
use App\Models\Movimiento_Prestamo_Producto;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Transportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class ingresoPrestamoProductoController extends Controller
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
           
            return view('admin.ventas.ingresoPrestamo.index',['clientes'=>Cliente::Clientes()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function nuevo($id){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Ingreso Préstamo Producto')->first();
            $secuencial=1;
            if($rangoDocumento){
                $secuencialAux=Ingreso_Prestamo_Producto::secuencial($rangoDocumento->rango_id)->max('ingreso_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                return view('admin.ventas.ingresoPrestamo.nuevo',['transportistas'=>Transportista::Transportistas()->get(),'productos'=>Producto::productos()->get(),'clientes'=>Cliente::Clientes()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function cuadre(){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.ventas.ingresoPrestamo.prestamos',['clientes'=>Cliente::Clientes()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }
    public function cuadrebuscar(Request $request){
        if (isset($_POST['buscar'])){
            return $this->buscarcuadre($request);
        }
        if (isset($_POST['pdf'])){
            return $this->pdf($request);
            //return $this->excel($this->datos($request,$request->get('radioKardex')),$request->get('radioKardex'));
        }
      

    }
    public function buscarcuadre(Request $request)
    {
          try{   
            
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $movimientos=Movimiento_Prestamo_Producto::buscar($request->get('idcliente'),$request->get('fecha'))->orderBy('movimiento_fecha','asc')->get();
            $movimientostotalI=Movimiento_Prestamo_Producto::Total($request->get('idcliente'),$request->get('fecha'))->where('movimiento_tipo','=','ENTRADA')->groupBy('producto.producto_id')->selectRaw('sum(movimiento_valor) as sum, producto_nombre, producto.producto_id')->get();
            $movimientostotalE=Movimiento_Prestamo_Producto::Total($request->get('idcliente'),$request->get('fecha'))->where('movimiento_tipo','=','SALIDA')->groupBy('producto.producto_id')->selectRaw('sum(movimiento_valor) as sum, producto_nombre, producto.producto_id')->get();
            $i=1;
            $movimientostotal=null;
            $activador=false;
            foreach($movimientostotalE as $movimiento){
                $movimientostotal[$i]['producto_id']=$movimiento->producto_id;
                $movimientostotal[$i]['producto']=$movimiento->producto_nombre;
                $movimientostotal[$i]['valor']=$movimiento->sum*(-1);
                $i++;
            }
            foreach($movimientostotalI as $movimiento){
                if($movimientostotal!=null){
                    for ($j=1;$j <= count($movimientostotal);$j++) {
                        if($movimientostotal[$j]['producto_id']==$movimiento->producto_id){
                            $movimientostotal[$j]['valor']=$movimientostotal[$j]['valor']+$movimiento->sum;
                            $activador=true;
                        }
                        
                    }
                }
                
                if($activador==false){
                    $movimientostotal[$i]['producto_id']=$movimiento->producto_id;
                    $movimientostotal[$i]['producto']=$movimiento->producto_nombre;
                    $movimientostotal[$i]['valor']=$movimiento->sum;
                    $i++;
                }
            }
           
            return view('admin.ventas.ingresoPrestamo.prestamos',['fecha'=>$request->get('fecha'),'clienteselect'=>$request->get('idcliente'),'movimientostotal'=>$movimientostotal,'movimientostotalE'=>$movimientostotalE,'movimientostotalI'=>$movimientostotalI,'movimientos'=>$movimientos,'clientes'=>Cliente::Clientes()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
           
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request)
    {
            $cliente=Cliente::findOrFail($request->get('idcliente'));
            $movimientos=Movimiento_Prestamo_Producto::buscar($request->get('idcliente'),$request->get('fecha'))->orderBy('movimiento_fecha','asc')->get();
            $movimientostotalI=Movimiento_Prestamo_Producto::Total($request->get('idcliente'),$request->get('fecha'))->where('movimiento_tipo','=','ENTRADA')->groupBy('producto.producto_id')->selectRaw('sum(movimiento_valor) as sum, producto_nombre, producto.producto_id')->get();
            $movimientostotalE=Movimiento_Prestamo_Producto::Total($request->get('idcliente'),$request->get('fecha'))->where('movimiento_tipo','=','SALIDA')->groupBy('producto.producto_id')->selectRaw('sum(movimiento_valor) as sum, producto_nombre, producto.producto_id')->get();
            $i=1;
            $movimientostotal=null;
            $activador=false;
            foreach($movimientostotalE as $movimiento){
                $movimientostotal[$i]['producto_id']=$movimiento->producto_id;
                $movimientostotal[$i]['producto']=$movimiento->producto_nombre;
                $movimientostotal[$i]['valor']=$movimiento->sum*(-1);
                $i++;
            }
            foreach($movimientostotalI as $movimiento){
                if ($movimientostotal!=null) {
                    for ($j=1;$j <= count($movimientostotal);$j++) {
                        if ($movimientostotal[$j]['producto_id']==$movimiento->producto_id) {
                            $movimientostotal[$j]['valor']=$movimientostotal[$j]['valor']+$movimiento->sum;
                            $activador=true;
                        }
                    }
                }
                if($activador==false){
                    $movimientostotal[$i]['producto_id']=$movimiento->producto_id;
                    $movimientostotal[$i]['producto']=$movimiento->producto_nombre;
                    $movimientostotal[$i]['valor']=$movimiento->sum;
                    $i++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/roles/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.prestamoscuadre', ['fecha'=>$request->get('fecha'),'cliente'=>$cliente,'movimientostotalI'=>$movimientostotalI,'movimientostotalE'=>$movimientostotalE,'movimientos'=>$movimientos,'movimientostotal'=>$movimientostotal,'empresa'=>$empresa]);
            $nombreArchivo = 'Cuadreprestamos';
            return PDF::loadHTML($view)->save('roles/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        
            
       
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
    public function guardar(Request $request)
    {
  
        try{           
            DB::beginTransaction();
            $general = new generalController();
            $ingreso= new Ingreso_Prestamo_Producto();
            $cierre = $general->cierre($request->get('idFecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/ingresoPrestamo/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $producto=Producto::findOrFail($request->get('producto_id'));
            $ingreso->ingreso_numero = $request->get('ingreso_serie').substr(str_repeat(0, 9).$request->get('ingreso_numero'), - 9);
            $ingreso->ingreso_serie = $request->get('ingreso_serie');
            $ingreso->ingreso_secuencial = $request->get('ingreso_numero');
            $ingreso->ingreso_fecha = $request->get('idFecha');
            $ingreso->ingreso_valor = $request->get('idValor');
            $ingreso->ingreso_descripcion = $request->get('idMensaje');  
            $ingreso->cliente_id = $request->get('cliente_id');
            $ingreso->producto_id = $request->get('producto_id');
            $ingreso->transportista_id = $request->get('Transportista_id');
            $ingreso->ingreso_placa = $request->get('idPlaca');
            $ingreso->rango_id = $request->get('rango_id');
            $ingreso->ingreso_estado = 1;  
            $ingreso->empresa_id = Auth::user()->empresa_id;

            /**********************movimiento caja****************************/
            $movimiento = new Movimiento_Prestamo_Producto();          
            $movimiento->movimiento_fecha= $request->get('idFecha');
            $movimiento->movimiento_tipo="ENTRADA";
            $movimiento->movimiento_descripcion= $request->get('idMensaje');
            $movimiento->movimiento_valor= $request->get('idValor');
            $movimiento->movimiento_documento="INGRESO DE PRODUCTO";
            $movimiento->movimiento_numero_documento=  $ingreso->ingreso_numero;
            $movimiento->movimiento_estado = 1;    
            $movimiento->cliente_id = $request->get('cliente_id');
            $movimiento->producto_id = $request->get('producto_id');
            $movimiento->empresa_id = Auth::user()->empresa_id;
            $movimiento->save();
            $general->registrarAuditoria('Registro de Movimiento de Prestamo de Producto -> '.$producto->producto_nombre.' Cantidad: '.$request->get('idValor'),'0','Motivo: Entrada');
            



            $ingreso->movimiento()->associate($movimiento);
            $ingreso->save();
            $general->registrarAuditoria('Registro de Ingreso de Prestamo de Producto -> '.$producto->producto_nombre.' Cantidad: '.$request->get('idValor'),'0','Con motivo:'.$request->get('idMensaje'));
            
            DB::commit();            
            return redirect('/ingresoPrestamo/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente');
       }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/ingresoPrestamo/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
       }
    }

    public function store(Request $request)
    {
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['guardar'])){
            return $this->guardar($request);
        }
       
    }
    public function buscar(Request $request)
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ingresos=Ingreso_Prestamo_Producto::buscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('nombre_cliente'))->get();  
            return view('admin.ventas.ingresoPrestamo.index',['nombre_cliente'=>$request->get('nombre_cliente'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'ingresos'=>$ingresos,'clientes'=>Cliente::Clientes()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
    public function eliminar($id)
    {
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ingreso=Ingreso_Prestamo_Producto::findOrFail($id);
            if($ingreso){
                return view('admin.ventas.ingresoPrestamo.eliminar',['ingreso'=>$ingreso,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function ver($id)
    {
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $ingreso=Ingreso_Prestamo_Producto::findOrFail($id);
            if($ingreso){
                return view('admin.ventas.ingresoPrestamo.ver',['ingreso'=>$ingreso,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
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
        try {
            DB::beginTransaction();
            $ingreso = Ingreso_Prestamo_Producto::findOrFail($id);
            $auditoria = new generalController();
            $cierre = $auditoria->cierre($ingreso->cabecera_ingreso_fecha,$ingreso->rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('ingresoPrestamo')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $movimiento=Movimiento_Prestamo_Producto::findOrFail($ingreso->movimiento_id);
            $ingreso->delete();
            $auditoria->registrarAuditoria('Eliminacion del ingreso de ingreso de prestamo N°-> '.$ingreso->ingreso_numero,$ingreso->ingreso_numero,'Permiso con id -> '.$id);
            
            $movimiento->delete();
            $auditoria->registrarAuditoria('Eliminacion del Movimiento  motivo'.$movimiento->movimiento_tipo.' y tipo '.$movimiento->movimiento_descripcion,$ingreso->ingreso_numero,'Permiso con id -> '.$id);   

            DB::commit();
            return redirect('ingresoPrestamo')->with('success','Datos eliminados exitosamente'); 
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('ingresoPrestamo')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
}
