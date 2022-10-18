<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Cuenta_Cobrar;
use App\Models\Cuenta_Pagar;
use App\Models\Descuento_Anticipo_Proveedor;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Pago_CXP;
use App\Models\Empresa;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\NEOPAGUPA\ViewExcel;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;


class cuentaPagarrespaldooController extends Controller
{
    public function index()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasPagar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function CargarExcel(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasPagar.estadoCuenta.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelCuentaPagar(Request $request){
        try{
            DB::beginTransaction();
            if($request->file('excelCXP')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelCXP')->getClientOriginalExtension();
                $path = $request->file('excelCXP')->move(public_path().'\temp\CXP', $name); 
                $array = Excel::toArray(new Cuenta_Pagar(), $path); 
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][3]);
                    if ($validar) {
                        $validacion=Proveedor::Existe($validar)->get();
                        if (count($validacion)>0) {
                            $PRO=Proveedor::Existe(trim($array[0][$i][3]))->first();
                        foreach(Cuenta_Pagar::CuentaByFacturaMigrada($array[0][$i][0])->get() as $cuenta){
                            if (substr($cuenta->cuenta_descripcion, 39)==$array[0][$i][0] && $PRO->proveedor_id==$cuenta->proveedor_id) {
                                $cuentas=Cuenta_Pagar::findOrFail($cuenta->cuenta_id);
                                $cuentas->cuenta_saldom=($array[0][$i][1]);
                                $cuentas->save();
                            }

                        }
                        
                            
                        }
                    }
                }
            }
            DB::commit();
            return redirect('excelCXP')->with('success','Datos guardados exitosamente');
            }catch(\Exception $ex){
                DB::rollBack();
                return redirect('excelCXP')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            $countProveedor = 0;
            $countCuenta = 0;
            $datos = null;
            $todo = 0;
            $mon = 0;
            $sal = 0;
            $pag = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('proveedorID') == "0"){
                $proveedores = Proveedor::proveedores()->get();
            }else{
                $proveedores = Proveedor::proveedor($request->get('proveedorID'))->get();
            }
            foreach($proveedores as $proveedor){
                $datos[$count]['nom'] = $proveedor->proveedor_nombre; 
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
                $countProveedor = $count - 1;
                $banderaMigrada = false;
                foreach(Cuenta_Pagar::CuentasPagarByPagos($request->get('fecha_desde'),$request->get('fecha_hasta'),$proveedor->proveedor_id,$todo,$request->get('sucursal_id') )->select('cuenta_pagar.cuenta_id','cuenta_pagar.cuenta_fecha','cuenta_pagar.cuenta_monto','cuenta_pagar.cuenta_descripcion')->distinct('cuenta_pagar.cuenta_fecha','cuenta_pagar.cuenta_id')->get() as $cuenta){
                    $banderaMigrada = false;
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = ''; 
                    $datos[$count]['num'] = ''; 
                    $datos[$count]['dia'] = '';
                    if($cuenta->transaccionCompra){
                        $datos[$count]['doc'] = $cuenta->transaccionCompra->tipoComprobante->tipo_comprobante_nombre; 
                        //$datos[$count]['num'] = $cuenta->transaccionCompra->tc_numero;
                        $datos[$count]['num'] = $cuenta->transaccionCompra->transaccion_numero;
                        $datos[$count]['dia'] = $cuenta->transaccionCompra->diario->diario_codigo; 
                    }
                    if($cuenta->liquidacionCompra){
                        $datos[$count]['doc'] = 'Liquidación de Compra'; 
                        $datos[$count]['num'] = $cuenta->liquidacionCompra->lc_numero;
                        $datos[$count]['dia'] = $cuenta->liquidacionCompra->diario->diario_codigo; 
                    }
                    if($cuenta->ingresoBodega){
                        $datos[$count]['doc'] = 'Ingreso de Bodega'; 
                        $datos[$count]['num'] = $cuenta->ingresoBodega->cabecera_ingreso_numero;  
                        $datos[$count]['dia'] = $cuenta->ingresoBodega->diario->diario_codigo; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 39);
                        $datos[$count]['doc'] = 'FACTURA'; 
                        $datos[$count]['dia'] = '';
                        $banderaMigrada = true;
                    }
                    $datos[$count]['fec'] = $cuenta->cuenta_fecha;
                    $datos[$count]['mon'] = $cuenta->cuenta_monto; 
                    if($banderaMigrada){
                        $datos[$count]['sal'] = $cuenta->cuenta_saldom;  
                    }else{
                        $datos[$count]['sal'] = $cuenta->cuenta_monto; 
                    }  
                    $datos[$count]['pag'] = 0; 
                    $datos[$count]['fep'] = ''; 
                    $datos[$count]['tip'] = ''; 
                    $datos[$count]['tot'] = '2';
                    $count ++;
                    $countCuenta = $count - 1;
                    foreach(Detalle_Pago_CXP::CuentaPagarPagosFecha($cuenta->cuenta_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo)->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = ''; 
                        $datos[$count]['num'] = ''; 
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = ''; 
                        $datos[$count]['sal'] = '';  
                        $datos[$count]['pag'] = $pago->detalle_pago_valor; 
                        $datos[$count]['fep'] = $pago->pagoCXP->pago_fecha; 
                        $datos[$count]['dia'] = $pago->pagoCXP->diario->diario_codigo; 
                        $datos[$count]['tip'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['tot'] = '3';
                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                       /* if(!$banderaMigrada){
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        }else{
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }*/
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                    if($cuenta->transaccionCompra){
                        foreach(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cuenta->transaccionCompra->transaccion_id)->orderBy('descuento_fecha')->get() as $pago){
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
                            $datos[$count]['tip'] = 'CRUCE DE ANTICIPO DE PROVEEDOR';
                            $datos[$count]['tot'] = '3';
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                           /* if(!$banderaMigrada){
                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                            }else{
                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                            }*/
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    if($banderaMigrada){
                        foreach(Descuento_Anticipo_Proveedor::DescuentosAnticipoByCXPCorte(substr($cuenta->cuenta_descripcion, 39),$request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago){
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
                            $datos[$count]['tip'] = 'CRUCE DE ANTICIPO DE PROVEEDOR';
                            $datos[$count]['tot'] = '3';

                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    $datos[$countProveedor]['mon'] = floatval($datos[$countProveedor]['mon']) + floatval($datos[$countCuenta]['mon']);
                    $datos[$countProveedor]['sal'] = floatval($datos[$countProveedor]['sal']) + floatval($datos[$countCuenta]['sal']);
                    $datos[$countProveedor]['pag'] = floatval($datos[$countProveedor]['pag']) + floatval($datos[$countCuenta]['pag']);
                }
                $mon = $mon + floatval($datos[$countProveedor]['mon']);
                $sal = $sal + floatval($datos[$countProveedor]['sal']);
                $pag = $pag + floatval($datos[$countProveedor]['pag']);
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasPagar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'tab'=>'1','mon'=>$mon,'sal'=>$sal,'pag'=>$pag,'fecC'=>$request->get('fecha_corte'),'tipo'=>$request->get('tipoConsulta'),'sucurslaC'=>$request->get('sucursal_id'),'sucursales'=>Sucursal::sucursales()->get(),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'datos'=>$datos,'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('cxp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pendientesPago(Request $request){
       
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $countProveedor = 0;
            $countCuenta = 0;
            $datos = null;
            $todo = 0;
            $mon = 0;
            $sal = 0;
            $pag = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('proveedorID') == "0"){
                $proveedores = Proveedor::proveedores()->get();
            }else{
                $proveedores = Proveedor::proveedor($request->get('proveedorID'))->get();
            }
            foreach($proveedores as $proveedor){
                $datos[$count]['nom'] = $proveedor->proveedor_nombre; 
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
                $countProveedor = $count - 1;
                $banderaMigrada = false;
                foreach(Cuenta_Pagar::CuentasPagarPendientesCorte($request->get('fecha_corte'),$proveedor->proveedor_id,$request->get('sucursal_id'))->select('cuenta_pagar.cuenta_id','cuenta_pagar.cuenta_fecha','cuenta_pagar.cuenta_monto','cuenta_pagar.cuenta_descripcion', 'cuenta_pagar.cuenta_saldom')->having('cuenta_monto','>',DB::raw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxp inner join pago_cxp on pago_cxp.pago_id = detalle_pago_cxp.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxp.cuenta_pagar_id = cuenta_pagar.cuenta_id)"))->orhavingRaw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxp inner join pago_cxp on pago_cxp.pago_id = detalle_pago_cxp.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxp.cuenta_pagar_id = cuenta_pagar.cuenta_id) is null")->groupBy('cuenta_pagar.cuenta_id','cuenta_pagar.cuenta_fecha','cuenta_pagar.cuenta_monto')->get() as $cuenta){
                    $is_ib = false;
                    if(isset($cuenta->ingresoBodega->cabecera_ingreso_id)){
                        $is_ib = true;
                    }
                    if(!$is_ib){
                        $banderaMigrada = false;
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = ''; 
                        $datos[$count]['num'] = ''; 
                        $datos[$count]['dia'] = '';
                        if($cuenta->transaccionCompra){
                            $datos[$count]['doc'] = $cuenta->transaccionCompra->tipoComprobante->tipo_comprobante_nombre; 
                            //$datos[$count]['num'] = $cuenta->transaccionCompra->tc_numero;transaccion_numero
                            $datos[$count]['num'] = $cuenta->transaccionCompra->transaccion_numero;
                            $datos[$count]['dia'] = $cuenta->transaccionCompra->diario->diario_codigo; 
                        }
                        if($cuenta->liquidacionCompra){
                            $datos[$count]['doc'] = 'Liquidación de Compra'; 
                            $datos[$count]['num'] = $cuenta->liquidacionCompra->lc_numero;
                            $datos[$count]['dia'] = $cuenta->liquidacionCompra->diario->diario_codigo; 
                        }
                        if($cuenta->ingresoBodega){
                            $datos[$count]['doc'] = 'Ingreso de Bodega'; 
                            $datos[$count]['num'] = $cuenta->ingresoBodega->cabecera_ingreso_numero;  
                            $datos[$count]['dia'] = $cuenta->ingresoBodega->diario->diario_codigo; 
                        }
                        if($datos[$count]['doc'] == ''){
                            $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 39);
                            $datos[$count]['doc'] = 'FACTURA'; 
                            $datos[$count]['dia'] = '';
                            $banderaMigrada = true;
                        }
                        $datos[$count]['fec'] = $cuenta->cuenta_fecha;
                        $datos[$count]['mon'] = $cuenta->cuenta_monto; 
                        if($banderaMigrada){
                            $datos[$count]['sal'] = $cuenta->cuenta_saldom; 
                        /* $datos[$count]['sal'] = $cuenta->cuenta_saldo + Detalle_Pago_CXP::CuentaPagarPagosAfterCorte($cuenta->cuenta_id,$request->get('fecha_corte'))->sum('detalle_pago_valor')
                            + Descuento_Anticipo_Proveedor::DescuentosAnticipoByCXPCorte(substr($cuenta->cuenta_descripcion, 39),$request->get('fecha_corte'))->sum('descuento_valor');  */
                        }else{
                            $datos[$count]['sal'] = $cuenta->cuenta_monto; 
                        }   
                        $datos[$count]['pag'] = 0; 
                        $datos[$count]['fep'] = ''; 
                        $datos[$count]['tip'] = ''; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                        $countCuenta = $count - 1;
                        foreach(Detalle_Pago_CXP::CuentaPagarPagosCorte($cuenta->cuenta_id,$request->get('fecha_corte'))->orderBy('pago_fecha')->get() as $pago){
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = ''; 
                            $datos[$count]['num'] = ''; 
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['sal'] = '';  
                            $datos[$count]['pag'] = $pago->detalle_pago_valor; 
                            $datos[$count]['fep'] = $pago->pagoCXP->pago_fecha;

                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $datos[$count]['dia'] = $pago->pagoCXP->diario->diario_codigo; 
                            }else{
                                $datos[$count]['dia'] = ''; 
                            }
                            $datos[$count]['tip'] = $pago->detalle_pago_descripcion; 
                            $datos[$count]['tot'] = '3';
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        /* if(!$banderaMigrada){
                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                            }else{
                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                            }*/
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                        if(isset($cuenta->transaccionCompra->transaccion_id)){
                            foreach(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFacturaCorte($cuenta->transaccionCompra->transaccion_id,$request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago){
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
                                $datos[$count]['tip'] = 'CRUCE DE ANTICIPO DE PROVEEDOR';
                                $datos[$count]['tot'] = '3';

                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                                $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                                $count ++;
                            }
                        }
                        if($banderaMigrada){
                            foreach(Descuento_Anticipo_Proveedor::DescuentosAnticipoByCXPCorte(substr($cuenta->cuenta_descripcion, 39),$request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago){
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
                                $datos[$count]['tip'] = 'CRUCE DE ANTICIPO DE PROVEEDOR';
                                $datos[$count]['tot'] = '3';

                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                                $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                                $count ++;
                            }
                        }
                        $datos[$countProveedor]['mon'] = floatval($datos[$countProveedor]['mon']) + floatval($datos[$countCuenta]['mon']);
                        $datos[$countProveedor]['sal'] = floatval($datos[$countProveedor]['sal']) + floatval($datos[$countCuenta]['sal']);
                        $datos[$countProveedor]['pag'] = floatval($datos[$countProveedor]['pag']) + floatval($datos[$countCuenta]['pag']);
                    
                        if(round($datos[$countCuenta]['sal'],2) == 0){
                            $count = $count - 1;
                            while($countCuenta <= $count){
                                array_pop($datos);
                                $count = $count - 1;
                            }
                            $count = $count + 1;
                        }
                    }
                }
                $mon = $mon + floatval($datos[$countProveedor]['mon']);
                $sal = $sal + floatval($datos[$countProveedor]['sal']);
                $pag = $pag + floatval($datos[$countProveedor]['pag']);
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
               
            }
           
            return view('admin.cuentasPagar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'tab'=>'1','mon'=>$mon,'sal'=>$sal,'pag'=>$pag,'fecC'=>$request->get('fecha_corte'),'tipo'=>$request->get('tipoConsulta'),'sucurslaC'=>$request->get('sucursal_id'),'sucursales'=>Sucursal::sucursales()->get(),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'datos'=>$datos,'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('cxp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $view =  \View::make('admin.formatosPDF.estadoCuentaCXP', ['mon'=>$request->get('idMonto'),'pag'=>$request->get('idPago'),'sal'=>$request->get('idSaldo'),'fecC'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_corte'))->format('d/m/Y'),'tipo'=>$request->get('tipoConsulta'),'todo'=>$todo,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'actual'=>DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'),'empresa'=>$empresa]);
            if ($request->get('tipoConsulta') == "0"){
                if($todo == 1){
                    $nombreArchivo = 'ESTADO DE CUENTA DE PROVEEDORES AL '.DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-m-Y');
                }else{
                    $nombreArchivo = 'ESTADO DE CUENTA DE PROVEEDORES DEL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
                }
            }
            if ($request->get('tipoConsulta') == "1"){
                $nombreArchivo = 'ESTADO DE CUENTA DE PROVEEDORES AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_corte'))->format('d-m-Y');
            }
            
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('cxp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excel(Request $request){
        try{   
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
            return Excel::download(new ViewExcel('admin.formatosExcel.estadoCuentaCXP',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('cxp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultarSaldo(Request $request)
    {
        try{
            if (isset($_POST['buscar'])){
                return $this->buscarSaldo($request);
            }
            if (isset($_POST['pdf'])){
                return $this->pdfSaldo($request);
            }
            if (isset($_POST['excel'])){
                return $this->excelSaldo($request);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
           

            foreach(Proveedor::proveedores()->get() as $proveedor){
                $datos[$count]['ruc'] = $proveedor->proveedor_cedula; 
                $datos[$count]['nom'] = $proveedor->proveedor_nombre; 
                $datos[$count]['ant'] = '0.00';
                                
                    $datos[$count]['deb'] = Detalle_Diario::MayorProveedor2($proveedor->proveedor_id,$request->get('fecha_desde2'),$request->get('fecha_hasta2'),$request->get('cuenta_id'),$request->get('sucursal_id2'))->sum('detalle_debe'); 
                    $datos[$count]['hab'] = Detalle_Diario::MayorProveedor2($proveedor->proveedor_id,$request->get('fecha_desde2'),$request->get('fecha_hasta2'),$request->get('cuenta_id'),$request->get('sucursal_id2'))->sum('detalle_haber'); 
                
                $datos[$count]['sal'] = floatval($datos[$count]['ant']) + floatval($datos[$count]['deb']) - floatval($datos[$count]['hab']);
                $count ++;
                $sal = $sal + floatval($datos[$count-1]['sal']);
                if(floatval($datos[$count-1]['sal']) == 0){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasPagar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'ini'=>$request->get('cuenta_id'),'tab'=>'2','sal2'=>$sal,'sucurslaC2'=>$request->get('sucursal_id2'),'sucursales'=>Sucursal::sucursales()->get(),'fecI2'=>$request->get('fecha_desde2'),'fecF2'=>$request->get('fecha_hasta2'),'datosSaldo'=>$datos,'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('cxp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            
            $view =  \View::make('admin.formatosPDF.saldoProveedores', ['sal'=>$request->get('idSaldo2'),'todo'=>$todo,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'actual'=>DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'),'empresa'=>$empresa]);
            if($todo == 1){
                $nombreArchivo = 'SALDO DE PROVEEDORES AL '.DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-m-Y');
            }else{
                $nombreArchivo = 'SALDO DE PROVEEDORES DEL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            }         
            return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('cxp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            return Excel::download(new ViewExcel('admin.formatosExcel.saldoProveedores',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('cxp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByProveedor(Request $request){
        return Cuenta_Pagar::CuentasByProveedor($request->get('proveedor_id'),$request->get('sucursal_id'))->select('cuenta_id',DB::raw('(SELECT transaccion_numero FROM transaccion_compra WHERE transaccion_compra.cuenta_id = cuenta_pagar.cuenta_id) as transaccion_numero'),DB::raw('(SELECT tipo_comprobante_nombre FROM transaccion_compra inner join tipo_comprobante on tipo_comprobante.tipo_comprobante_id =transaccion_compra.tipo_comprobante_id WHERE transaccion_compra.cuenta_id = cuenta_pagar.cuenta_id) as tipo_comprobante_nombre'),DB::raw('(SELECT lc_numero FROM liquidacion_compra WHERE liquidacion_compra.cuenta_id = cuenta_pagar.cuenta_id) as lc_numero'),'cuenta_saldo','cuenta_fecha','cuenta_fecha_fin','proveedor.proveedor_ruc','proveedor.proveedor_nombre','proveedor.proveedor_id',DB::raw('(SELECT sum(anticipo_saldo) FROM anticipo_proveedor WHERE anticipo_proveedor.proveedor_id = proveedor.proveedor_id) as saldo_proveedor'),'cuenta_pagar.cuenta_descripcion')->get();
    }
}

