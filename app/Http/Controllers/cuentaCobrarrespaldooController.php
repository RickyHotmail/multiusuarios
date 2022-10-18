<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Cuenta_Cobrar;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Pago_CXC;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;


use App\NEOPAGUPA\ViewExcel;
use Maatwebsite\Excel\Facades\Excel;

class cuentaCobrarrespaldooController extends Controller
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
            return view('admin.cuentasCobrar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'gruposPermiso'=>$gruposPermiso,'sucursales'=>Sucursal::sucursales()->get(),'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function CargarExcel(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasCobrar.estadoCuenta.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelCuentaCobrar(Request $request){
        try{
            DB::beginTransaction();
            if($request->file('excelCXC')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelCXC')->getClientOriginalExtension();
                $path = $request->file('excelCXC')->move(public_path().'\temp\CXC', $name); 
                $array = Excel::toArray(new Cuenta_Cobrar(), $path); 
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][3]);
                    if ($validar) {
                        $validacion=Cliente::Existe($validar)->get();
                        //return $validacion;
                        if (count($validacion)==1) {
                            $validacion=Cliente::Existe($validar)->first();
                        foreach(Cuenta_Cobrar::CuentaByFacturaMigrada($array[0][$i][0])->get() as $cuenta){
                            if (substr($cuenta->cuenta_descripcion, 38)==$array[0][$i][0] && $validacion->cliente_id==$cuenta->cliente_id) {
                                $cuentas=Cuenta_Cobrar::findOrFail($cuenta->cuenta_id);
                                $cuentas->cuenta_saldom=($array[0][$i][1]);
                                $cuentas->save();
                            }
                            else
                                return dd($array[0][$i][0]);
                        }
                        
                            
                        }
                    }
                }
            }
            DB::commit();
            return redirect('excelCXC')->with('success','Datos guardados exitosamente');
            }catch(\Exception $ex){
                DB::rollBack();
                return redirect('excelCXC')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
            }
    }
    public function consultar(Request $request)
    {
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['pdf'])){
            return $this->pdf($request);
        }
        if (isset($_POST['excel'])){
            return $this->excel($request);
        }
    }
    public function buscar(Request $request){
        if ($request->get('tipoConsulta') == "0"){
            return $this->pagos($request);
        }
        if ($request->get('tipoConsulta') == "1"){
            return $this->pendientesPago($request);
        }
    }
    public function pagos(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $countCliente = 0;
            $countCuenta = 0;
            $datos = null;
            $todo = 0;
            $mon = 0;
            $sal = 0;
            $pag = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('clienteID') == "0"){
                $clientes = Cliente::clientes()->get();
            }else{
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }
            foreach($clientes as $cliente){
                $datos[$count]['nom'] = $cliente->cliente_nombre; 
                $datos[$count]['doc'] = ''; 
                $datos[$count]['num'] = ''; 
                $datos[$count]['fec'] = '';
                $datos[$count]['mon'] = 0; 
                $datos[$count]['sal'] = 0;  
                $datos[$count]['pag'] = 0; 
                $datos[$count]['fep'] = ''; 
                $datos[$count]['dia'] = ''; 
                $datos[$count]['tip'] = ''; 
                $datos[$count]['tot'] = '1';
                $count ++;
                $countCliente = $count - 1;
                $banderaMigrada = false;
                foreach(Cuenta_Cobrar::CuentasCobrarByPagos($request->get('fecha_desde'),$request->get('fecha_hasta'),$cliente->cliente_id,$todo,$request->get('sucursal_id') )->select('cuenta_cobrar.cuenta_id','cuenta_cobrar.cuenta_fecha','cuenta_cobrar.cuenta_monto','cuenta_cobrar.cuenta_descripcion','cuenta_cobrar.cuenta_saldo')->distinct('cuenta_cobrar.cuenta_fecha','cuenta_cobrar.cuenta_id')->get() as $cuenta){
                    $banderaMigrada = false;
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = ''; 
                    $datos[$count]['num'] = ''; 
                    $datos[$count]['dia'] = '';
                    if($cuenta->facturaVenta){
                        $datos[$count]['doc'] = 'FACTURA'; 
                        $datos[$count]['num'] = $cuenta->facturaVenta->factura_numero;
                        $datos[$count]['dia'] = $cuenta->facturaVenta->diario->diario_codigo; 
                    }
                    if($cuenta->notaEntrega){
                        $datos[$count]['doc'] = 'NOTA DE ENTREGA'; 
                        $datos[$count]['num'] = $cuenta->notaEntrega->nt_numero;
                        if(Auth::user()->empresa->empresa_contabilidad == '1'){
                            $datos[$count]['dia'] = $cuenta->notaEntrega->diario->diario_codigo; 
                        }else{
                            $datos[$count]['dia'] = '';
                        }
                    }
                    if($cuenta->notaDebito){
                        $datos[$count]['doc'] = 'NOTA DE DÉBITO'; 
                        $datos[$count]['num'] = $cuenta->notaDebito->nd_numero;  
                        $datos[$count]['dia'] = $cuenta->notaDebito->diario->diario_codigo; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = 'FACTURA';
                        $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 38); 
                        $datos[$count]['dia'] = '';
                        $banderaMigrada = true;
                    }
                    $datos[$count]['fec'] = $cuenta->cuenta_fecha;
                    $datos[$count]['mon'] = $cuenta->cuenta_monto; 
                    if($banderaMigrada){
                        $datos[$count]['sal'] = $cuenta->cuenta_saldo;  
                    }else{
                        $datos[$count]['sal'] = $cuenta->cuenta_monto; 
                    }
                    $datos[$count]['pag'] = 0; 
                    $datos[$count]['fep'] = ''; 
                    $datos[$count]['tip'] = ''; 
                    $datos[$count]['tot'] = '2';
                    $count ++;
                    $countCuenta = $count - 1;
                    foreach(Detalle_Pago_CXC::CuentaCobrarPagosFecha($cuenta->cuenta_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo)->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = ''; 
                        $datos[$count]['num'] = ''; 
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = ''; 
                        $datos[$count]['sal'] = '';  
                        $datos[$count]['pag'] = $pago->detalle_pago_valor; 
                        $datos[$count]['fep'] = $pago->pagoCXC->pago_fecha; 
                        if(Auth::user()->empresa->empresa_contabilidad == '1'){
                            $datos[$count]['dia'] = $pago->pagoCXC->diario->diario_codigo; 
                        }else{
                            $datos[$count]['dia'] = ''; 
                        }
                        $datos[$count]['tip'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['tot'] = '3';
                        if(!$banderaMigrada){
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        }else{
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                    if($cuenta->facturaVenta){
                        foreach(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cuenta->facturaVenta->factura_id)->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = ''; 
                            $datos[$count]['num'] = ''; 
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['pag'] = $pago->descuento_valor;                             
                            $datos[$count]['fep'] = $pago->descuento_fecha; 
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $datos[$count]['dia'] = $pago->diario->diario_codigo; 
                            }else{
                                $datos[$count]['dia'] = ''; 
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';
                            if(!$banderaMigrada){
                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                            }else{
                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                            }
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    if($banderaMigrada){
                        foreach(Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCCorte(substr($cuenta->cuenta_descripcion, 38),$request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = ''; 
                            $datos[$count]['num'] = ''; 
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['pag'] = $pago->descuento_valor;                             
                            $datos[$count]['fep'] = $pago->descuento_fecha; 
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $datos[$count]['dia'] = $pago->diario->diario_codigo; 
                            }else{
                                $datos[$count]['dia'] = ''; 
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';
                                
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    $datos[$countCliente]['mon'] = floatval($datos[$countCliente]['mon']) + floatval($datos[$countCuenta]['mon']);
                    $datos[$countCliente]['sal'] = floatval($datos[$countCliente]['sal']) + floatval($datos[$countCuenta]['sal']);
                    $datos[$countCliente]['pag'] = floatval($datos[$countCliente]['pag']) + floatval($datos[$countCuenta]['pag']);
                }
                $mon = $mon + floatval($datos[$countCliente]['mon']);
                $sal = $sal + floatval($datos[$countCliente]['sal']);
                $pag = $pag + floatval($datos[$countCliente]['pag']);
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasCobrar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'tab'=>'1','mon'=>$mon,'sal'=>$sal,'pag'=>$pag,'fecC'=>$request->get('fecha_corte'),'tipo'=>$request->get('tipoConsulta'),'sucurslaC'=>$request->get('sucursal_id'),'sucursales'=>Sucursal::sucursales()->get(),'clienteC'=>$request->get('clienteID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'datos'=>$datos,'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('cxc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pendientesPago(Request $request){
        try{
            ini_set('max_execution_time', 1200);
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $countCliente = 0;
            $countCuenta = 0;
            $datos = null;
            $todo = 0;
            $mon = 0;
            $sal = 0;
            $pag = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('clienteID') == "0"){
                $clientes = Cliente::clientes()->get();
            }else{
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }
            foreach($clientes as $cliente){
                $datos[$count]['nom'] = $cliente->cliente_nombre; 
                $datos[$count]['doc'] = ''; 
                $datos[$count]['num'] = ''; 
                $datos[$count]['fec'] = '';
                $datos[$count]['mon'] = 0; 
                $datos[$count]['sal'] = 0;  
                $datos[$count]['pag'] = 0; 
                $datos[$count]['fep'] = ''; 
                $datos[$count]['dia'] = ''; 
                $datos[$count]['tip'] = ''; 
                $datos[$count]['tot'] = '1';
                $count ++;
                $countCliente = $count - 1;
                $banderaMigrada = false;
                foreach(Cuenta_Cobrar::CuentasCobrarPendientes($request->get('fecha_corte'),$cliente->cliente_id,$request->get('sucursal_id'))->select('cuenta_cobrar.cuenta_saldom','cuenta_cobrar.cuenta_id','cuenta_cobrar.cuenta_fecha','cuenta_cobrar.cuenta_monto','cuenta_cobrar.cuenta_descripcion')->having('cuenta_monto','>',DB::raw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxc inner join pago_cxc on pago_cxc.pago_id = detalle_pago_cxc.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxc.cuenta_id = cuenta_cobrar.cuenta_id)"))->orhavingRaw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxc inner join pago_cxc on pago_cxc.pago_id = detalle_pago_cxc.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxc.cuenta_id = cuenta_cobrar.cuenta_id) is null")->groupBy('cuenta_cobrar.cuenta_id','cuenta_cobrar.cuenta_fecha','cuenta_cobrar.cuenta_monto')->get() as $cuenta){
                    $banderaMigrada = false;
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = ''; 
                    $datos[$count]['num'] = ''; 
                    $datos[$count]['dia'] = '';
                    if($cuenta->facturaVenta){
                        $datos[$count]['doc'] = 'FACTURA'; 
                        $datos[$count]['num'] = $cuenta->facturaVenta->factura_numero;
                        $datos[$count]['dia'] = $cuenta->facturaVenta->diario->diario_codigo; 
                    }
                    if($cuenta->notaEntrega){
                        $datos[$count]['doc'] = 'NOTA DE ENTREGA'; 
                        $datos[$count]['num'] = $cuenta->notaEntrega->nt_numero;
                        if(Auth::user()->empresa->empresa_contabilidad == '1'){
                            $datos[$count]['dia'] = $cuenta->notaEntrega->diario->diario_codigo; 
                        }else{
                            $datos[$count]['dia'] = '';
                        }
                    }
                    if($cuenta->notaDebito){
                        $datos[$count]['doc'] = 'NOTA DE DÉBITO'; 
                        $datos[$count]['num'] = $cuenta->notaDebito->nd_numero;  
                        $datos[$count]['dia'] = $cuenta->notaDebito->diario->diario_codigo; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 38);
                        $datos[$count]['doc'] = 'FACTURA'; 
                        $datos[$count]['dia'] = '';
                        $banderaMigrada = true;
                    }
                    $datos[$count]['fec'] = $cuenta->cuenta_fecha;
                    $datos[$count]['mon'] = $cuenta->cuenta_monto; 
                    if($banderaMigrada){
                        $datos[$count]['sal'] = $cuenta->cuenta_saldom; 
                    /*    $datos[$count]['sal'] = $cuenta->cuenta_saldo +  Detalle_Pago_CXC::cuentaCobrarPagosAfterCorte($cuenta->cuenta_id,$request->get('fecha_corte'))->sum('detalle_pago_valor')
                        + Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCAfterCorte(substr($cuenta->cuenta_descripcion, 38),$request->get('fecha_corte'))->sum('descuento_valor');
                        */
                    }else{
                        $datos[$count]['sal'] = $cuenta->cuenta_monto; 
                    }
                    $datos[$count]['pag'] = 0; 
                    $datos[$count]['fep'] = ''; 
                    $datos[$count]['tip'] = ''; 
                    $datos[$count]['tot'] = '2';
                    $count ++;
                    $countCuenta = $count - 1;
                    foreach(Detalle_Pago_CXC::CuentaCobrarPagosCorte($cuenta->cuenta_id,$request->get('fecha_corte'))->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = ''; 
                        $datos[$count]['num'] = ''; 
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = ''; 
                        $datos[$count]['sal'] = '';  
                        $datos[$count]['pag'] = $pago->detalle_pago_valor; 
                        $datos[$count]['fep'] = $pago->pagoCXC->pago_fecha;

                        if(Auth::user()->empresa->empresa_contabilidad == '1'){
                            $datos[$count]['dia'] = $pago->pagoCXC->diario->diario_codigo; 
                        }else{
                            $datos[$count]['dia'] = ''; 
                        }
                        $datos[$count]['tip'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['tot'] = '3';
                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        /*
                        if(!$banderaMigrada){
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        }else{
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }
                        */
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                    if(isset($cuenta->facturaVenta->factura_id)){
                        foreach(Descuento_Anticipo_Cliente::DescuentosAnticipoByFacturaCorte($cuenta->facturaVenta->factura_id,$request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = ''; 
                            $datos[$count]['num'] = ''; 
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['pag'] = $pago->descuento_valor;                             
                            $datos[$count]['fep'] = $pago->descuento_fecha; 
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $datos[$count]['dia'] = $pago->diario->diario_codigo; 
                            }else{
                                $datos[$count]['dia'] = ''; 
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';

                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    if($banderaMigrada){
                        foreach(Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCCorte(substr($cuenta->cuenta_descripcion, 38),$request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = ''; 
                            $datos[$count]['num'] = ''; 
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['pag'] = $pago->descuento_valor;                             
                            $datos[$count]['fep'] = $pago->descuento_fecha; 
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $datos[$count]['dia'] = $pago->diario->diario_codigo; 
                            }else{
                                $datos[$count]['dia'] = ''; 
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';
                                
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    $datos[$countCliente]['mon'] = floatval($datos[$countCliente]['mon']) + floatval($datos[$countCuenta]['mon']);
                    $datos[$countCliente]['sal'] = floatval($datos[$countCliente]['sal']) + floatval($datos[$countCuenta]['sal']);
                    $datos[$countCliente]['pag'] = floatval($datos[$countCliente]['pag']) + floatval($datos[$countCuenta]['pag']);

                    if(round($datos[$countCuenta]['sal'],2) == 0){
                        $count = $count - 1;
                        while($countCuenta <= $count){
                            array_pop($datos);
                            $count = $count - 1;
                        }
                        $count = $count + 1;
                    }
                }
                $mon = $mon + floatval($datos[$countCliente]['mon']);
                $sal = $sal + floatval($datos[$countCliente]['sal']);
                $pag = $pag + floatval($datos[$countCliente]['pag']);
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasCobrar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'tab'=>'1','mon'=>$mon,'sal'=>$sal,'pag'=>$pag,'fecC'=>$request->get('fecha_corte'),'tipo'=>$request->get('tipoConsulta'),'sucurslaC'=>$request->get('sucursal_id'),'sucursales'=>Sucursal::sucursales()->get(),'clienteC'=>$request->get('clienteID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'datos'=>$datos,'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('cxc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdf(Request $request){
        try{            
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            $datos = null;
            $count = 1;
            $nom = $request->get('idNom');
            $doc = $request->get('idDoc');
            $num = $request->get('idNum');
            $fec = $request->get('idFec');
            $mon = $request->get('idMon');
            $sal = $request->get('idSal');
            $pag = $request->get('idPag');
            $fep = $request->get('idFep');
            $dia = $request->get('idDia');
            $tip = $request->get('idTip');
            $tot = $request->get('idTot');
            if($nom){
                for ($i = 0; $i < count($nom); ++$i){
                    $datos[$count]['nom'] = $nom[$i]; 
                    $datos[$count]['doc'] = $doc[$i];  
                    $datos[$count]['num'] = $num[$i];  
                    $datos[$count]['fec'] = $fec[$i]; 
                    $datos[$count]['mon'] = $mon[$i];  
                    $datos[$count]['sal'] = $sal[$i];   
                    $datos[$count]['pag'] = $pag[$i];  
                    $datos[$count]['fep'] = $fep[$i];  
                    $datos[$count]['dia'] = $dia[$i];  
                    $datos[$count]['tip'] = $tip[$i];  
                    $datos[$count]['tot'] = $tot[$i]; 
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.estadoCuentaCXC', ['mon'=>$request->get('idMonto'),'pag'=>$request->get('idPago'),'sal'=>$request->get('idSaldo'),'fecC'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_corte'))->format('d/m/Y'),'tipo'=>$request->get('tipoConsulta'),'todo'=>$todo,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'actual'=>DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'),'empresa'=>$empresa]);
            if ($request->get('tipoConsulta') == "0"){
                if($todo == 1){
                    $nombreArchivo = 'ESTADO DE CUENTA DE CLIENTES AL '.DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-m-Y');
                }else{
                    $nombreArchivo = 'ESTADO DE CUENTA DE CLIENTES DEL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
                }
            }
            if ($request->get('tipoConsulta') == "1"){
                $nombreArchivo = 'ESTADO DE CUENTA DE CLIENTES AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_corte'))->format('d-m-Y');
            }
            
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('cxc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excel(Request $request){
       
            $datos = null;
            $count = 1;
            $nom = $request->get('idNom');
            $doc = $request->get('idDoc');
            $num = $request->get('idNum');
            $fec = $request->get('idFec');
            $mon = $request->get('idMon');
            $sal = $request->get('idSal');
            $pag = $request->get('idPag');
            $fep = $request->get('idFep');
            $dia = $request->get('idDia');
            $tip = $request->get('idTip');
            $tot = $request->get('idTot');
            if($nom){
                for ($i = 0; $i < count($nom); ++$i){
                    $datos[$count]['nom'] = $nom[$i]; 
                    $datos[$count]['doc'] = $doc[$i];  
                    $datos[$count]['num'] = $num[$i];  
                    $datos[$count]['fec'] = $fec[$i]; 
                    $datos[$count]['mon'] = $mon[$i];  
                    $datos[$count]['sal'] = $sal[$i];   
                    $datos[$count]['pag'] = $pag[$i];  
                    $datos[$count]['fep'] = $fep[$i];  
                    $datos[$count]['dia'] = $dia[$i];  
                    $datos[$count]['tip'] = $tip[$i];  
                    $datos[$count]['tot'] = $tot[$i]; 
                    $count ++;
                }
            }
            return Excel::download(new ViewExcel('admin.formatosExcel.estadoCuentaCXC',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
       
    }
    public function consultarSaldo(Request $request)
    {
        if (isset($_POST['buscar'])){
            return $this->buscarSaldo($request);
        }
        if (isset($_POST['pdf'])){
            return $this->pdfSaldo($request);
        }
        if (isset($_POST['excel'])){
            return $this->excelSaldo($request);
        }
    }
    public function buscarSaldo(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $datos = null;
            $todo = 0;
            $sal = 0;
            if ($request->get('fecha_todo2') == "on"){
                $todo = 1; 
            }
            foreach(Cliente::clientes()->get() as $cliente){
                $datos[$count]['ruc'] = $cliente->cliente_cedula; 
                $datos[$count]['nom'] = $cliente->cliente_nombre; 
                $datos[$count]['ant'] = '0.00';
               
                $datos[$count]['deb'] = Detalle_Diario::MayorCliente2($cliente->cliente_id,$request->get('fecha_desde2'),$request->get('fecha_hasta2'),$request->get('cuenta_id'),$request->get('sucursal_id2'))->sum('detalle_debe'); 
                $datos[$count]['hab'] = Detalle_Diario::MayorCliente2($cliente->cliente_id,$request->get('fecha_desde2'),$request->get('fecha_hasta2'),$request->get('cuenta_id'),$request->get('sucursal_id2'))->sum('detalle_haber');
            
                $datos[$count]['sal'] = floatval($datos[$count]['ant']) + floatval($datos[$count]['deb']) - floatval($datos[$count]['hab']);
                $count ++;
                $sal = $sal + floatval($datos[$count-1]['sal']);
                if(floatval($datos[$count-1]['sal']) == 0){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasCobrar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'tab'=>'2','sal2'=>$sal,'sucurslaC2'=>$request->get('sucursal_id2'),'sucursales'=>Sucursal::sucursales()->get(),'fecI2'=>$request->get('fecha_desde2'),'fecF2'=>$request->get('fecha_hasta2'),'todo2'=>$todo,'datosSaldo'=>$datos,'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('cxc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdfSaldo(Request $request){
        try{        
            $todo = 0;    
            if ($request->get('fecha_todo2') == "on"){
                $todo = 1; 
            }
            $datos = null;
            $count = 1;
            $ruc = $request->get('idRuc');
            $nom = $request->get('idNom');
            $ant = $request->get('idAnt');
            $deb = $request->get('idDeb');
            $hab = $request->get('idHab');
            $sal = $request->get('idSal');
            if($ruc){
                for ($i = 0; $i < count($ruc); ++$i){
                    $datos[$count]['ruc'] = $ruc[$i]; 
                    $datos[$count]['nom'] = $nom[$i];  
                    $datos[$count]['ant'] = $ant[$i];  
                    $datos[$count]['deb'] = $deb[$i]; 
                    $datos[$count]['hab'] = $hab[$i];  
                    $datos[$count]['sal'] = $sal[$i];   
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            
            $view =  \View::make('admin.formatosPDF.saldoClientes', ['sal'=>$request->get('idSaldo2'),'todo'=>$todo,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'actual'=>DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'),'empresa'=>$empresa]);
            if($todo == 1){
                $nombreArchivo = 'SALDO DE CLIENTES AL '.DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-m-Y');
            }else{
                $nombreArchivo = 'SALDO DE CLIENTES DEL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            }         
            return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('cxc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excelSaldo(Request $request){
        try{   
            $datos = null;
            $count = 1;
            $ruc = $request->get('idRuc');
            $nom = $request->get('idNom');
            $ant = $request->get('idAnt');
            $deb = $request->get('idDeb');
            $hab = $request->get('idHab');
            $sal = $request->get('idSal');
            if($ruc){
                for ($i = 0; $i < count($ruc); ++$i){
                    $datos[$count]['ruc'] = $ruc[$i]; 
                    $datos[$count]['nom'] = $nom[$i];  
                    $datos[$count]['ant'] = $ant[$i];  
                    $datos[$count]['deb'] = $deb[$i]; 
                    $datos[$count]['hab'] = $hab[$i];  
                    $datos[$count]['sal'] = $sal[$i]; 
                    $count ++;
                }
            }
            return Excel::download(new ViewExcel('admin.formatosExcel.saldoClientes',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('cxc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByCliente(Request $request){
        return Cuenta_Cobrar::CuentasByCliente($request->get('cliente_id'),$request->get('sucursal_id'))->select('cuenta_id',DB::raw('(SELECT factura_numero FROM factura_venta WHERE factura_venta.cuenta_id = cuenta_cobrar.cuenta_id) as factura_numero'),DB::raw('(SELECT nt_numero FROM nota_entrega WHERE nota_entrega.cuenta_id = cuenta_cobrar.cuenta_id) as nt_numero'),DB::raw('(SELECT nd_numero FROM nota_debito WHERE nota_debito.cuenta_id = cuenta_cobrar.cuenta_id) as nd_numero'),'cuenta_saldo','cuenta_fecha','cuenta_fecha_fin','cliente.cliente_id','cliente.cliente_cedula','cliente.cliente_nombre',DB::raw('(SELECT sum(anticipo_saldo) FROM anticipo_cliente WHERE anticipo_cliente.cliente_id = cliente.cliente_id) as saldo_cliente'),'cuenta_cobrar.cuenta_descripcion')->get();
    }
}
