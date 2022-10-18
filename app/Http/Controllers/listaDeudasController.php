<?php

namespace App\Http\Controllers;

use App\Models\Cuenta_Pagar;
use App\Models\Descuento_Anticipo_Proveedor;
use App\Models\Detalle_Pago_CXP;
use App\Models\Empresa;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\NEOPAGUPA\ViewExcel;
use DateTime;
use PDF;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listaDeudasController extends Controller
{
    public function nuevo(){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasPagar.listaDeudas.index',['sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        switch ($request->get('tipoConsulta')) {
            case 0:
                return $this->general($request);
                break;
            case 1:
                return $this->deudas($request);
                break;
            case 2:
                return $this->pagos($request);
                break;
            case 3:
                return $this->estadoCuenta($request);
                break;
        }
    }
    public function general(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $datos = null;
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $todo = 0;
            if($request->get('formaCredito') != "on" and $request->get('formaContado') != "on" and $request->get('formaEfectivo') != "on" and $request->get('formaOtro') != "on"){
                return view('admin.cuentasPagar.listaDeudas.index',['tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            $tipoCre = '';
            $tipoCon = '';
            $tipoEfe = '';
            $tipoOtr = '';
            if($request->get('formaCredito') == "on"){
                $tipoCre = $request->get('formaCredito');
            }
            if($request->get('formaContado') == "on"){
                $tipoCon = $request->get('formaContado');
            }
            if($request->get('formaEfectivo') == "on"){
                $tipoEfe = $request->get('formaEfectivo');
            }
            if($request->get('formaOtro') == "on"){
                $tipoOtr = $request->get('formaOtro');
            }
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('proveedorID') == "0"){
                $proveedores = Proveedor::proveedores()->get();
            }else{
                $proveedores = Proveedor::proveedor($request->get('proveedorID'))->get();
            }
            foreach($proveedores as $proveedor){
                $datos[$count]['ide'] = ''; 
                $datos[$count]['ruc'] = $proveedor->proveedor_ruc; 
                $datos[$count]['nom'] = $proveedor->proveedor_nombre; 
                $datos[$count]['doc'] = '';
                $datos[$count]['mon'] = Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->sum('cuenta_monto'); 
                $datos[$count]['sal'] = Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->sum('cuenta_saldo'); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['ter'] = ''; 
                $datos[$count]['pla'] = ''; 
                $datos[$count]['tra'] = ''; 
                $datos[$count]['ret'] = ''; 
                $datos[$count]['tot'] = '1';
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $count ++;
                foreach(Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->orderBy('cuenta_fecha')->get() as $cxp){
                    $datos[$count]['ide'] = ''; 
                    $datos[$count]['ruc'] = $cxp->cuenta_tipo; 
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = '';
                    if($cxp->transaccionCompra){ 
                        $datos[$count]['doc'] = $cxp->transaccionCompra->transaccion_numero;
                        $datos[$count]['nom'] = 'FACTURA'; 
                    }
                    if($cxp->liquidacionCompra){ 
                        $datos[$count]['doc'] =$cxp->liquidacionCompra->lc_numero;
                        $datos[$count]['nom'] = 'LIQUIDACIÓN DE COMPRA'; 
                    }
                    if($cxp->ingresoBodega){ 
                        $datos[$count]['doc'] =$cxp->ingresoBodega->cabecera_ingreso_numero;
                        $datos[$count]['nom'] = 'INGRESO A BODEGA'; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = substr($cxp->cuenta_descripcion, 39);
                        $datos[$count]['nom'] = 'FACTURA'; 
                        $datos[$count]['ide'] = $cxp->cuenta_id;
                    }
                    $datos[$count]['mon'] = $cxp->cuenta_monto; 
                    $datos[$count]['sal'] = $cxp->cuenta_saldo; 
                    $datos[$count]['fec'] = $cxp->cuenta_fecha; 
                    $datos[$count]['ter'] = $cxp->cuenta_fecha_fin; 
                    $datos[$count]['pla'] = '';
                    if($cxp->transaccionCompra){ $datos[$count]['pla'] = $cxp->transaccionCompra->transaccion_dias_plazo;}
                    if($cxp->liquidacionCompra){ $datos[$count]['pla'] = $cxp->liquidacionCompra->lc_dias_plazo;}
                    if($cxp->ingresoBodega){ $datos[$count]['pla'] = $cxp->ingresoBodega->cabecera_ingreso_plazo;}
                    $datos[$count]['tra'] = '0'; 
                    if($datos[$count]['sal'] > 0){
                        $date1 = new DateTime($cxp->cuenta_fecha);
                        $date2 = date('d-m-Y');
                        $date2 = new DateTime($date2);
                        $diff = $date1->diff($date2);
                        $datos[$count]['tra'] = $diff->days; 
                    }
                    $datos[$count]['ret'] = '';
                    if($cxp->transaccionCompra){if($cxp->transaccionCompra->retencionCompra){ $datos[$count]['ret'] = 'R';}}
                    if($cxp->liquidacionCompra){if($cxp->liquidacionCompra->retencionCompra){ $datos[$count]['ret'] = 'R';}}
                    $datos[$count]['tot'] = '0';
                    if($datos[$count]['tra'] > $datos[$count]['pla']){
                        $facVencidas = $facVencidas + floatval($datos[$count]['sal']);
                    }
                    if($datos[$count]['pla'] > $datos[$count]['tra']){
                        $facVencer = $facVencer + floatval($datos[$count]['sal']);
                    }
                    $count ++;
                    foreach(Detalle_Pago_CXP::CuentaPagarPagos($cxp->cuenta_id)->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['ide'] = ''; 
                        $datos[$count]['ruc'] = ''; 
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = '';
                        $datos[$count]['mon'] = $pago->detalle_pago_valor; 
                        $datos[$count]['sal'] = ''; 
                        $datos[$count]['fec'] = $pago->pagoCXP->pago_fecha; 
                        $datos[$count]['ter'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['pla'] = ''; 
                        $datos[$count]['tra'] = ''; 
                        $datos[$count]['ret'] = ''; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                    if($cxp->transaccionCompra){
                        foreach(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxp->transaccionCompra->transaccion_id)->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['ide'] = ''; 
                            $datos[$count]['ruc'] = ''; 
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = '';
                            $datos[$count]['mon'] = $pago->descuento_valor; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['fec'] = $pago->descuento_fecha; 
                            $datos[$count]['ter'] = 'DESCUENTO DE ANTICIPO A PROVEEDOR'; 
                            $datos[$count]['pla'] = ''; 
                            $datos[$count]['tra'] = ''; 
                            $datos[$count]['ret'] = ''; 
                            $datos[$count]['tot'] = '2';
                            $count ++;
                        }
                    }
                }
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasPagar.listaDeudas.index',['tipoCre'=>$tipoCre,'tipoCon'=>$tipoCon,'tipoOtr'=>$tipoOtr,'tipoEfe'=>$tipoEfe,'tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listaDeudas')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function deudas(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $datos = null;
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $todo = 0;
            if($request->get('formaCredito') != "on" and $request->get('formaContado') != "on" and $request->get('formaEfectivo') != "on" and $request->get('formaOtro') != "on"){
                return view('admin.cuentasPagar.listaDeudas.index',['tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            $tipoCre = '';
            $tipoCon = '';
            $tipoEfe = '';
            $tipoOtr = '';
            if($request->get('formaCredito') == "on"){
                $tipoCre = $request->get('formaCredito');
            }
            if($request->get('formaContado') == "on"){
                $tipoCon = $request->get('formaContado');
            }
            if($request->get('formaEfectivo') == "on"){
                $tipoEfe = $request->get('formaEfectivo');
            }
            if($request->get('formaOtro') == "on"){
                $tipoOtr = $request->get('formaOtro');
            }
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('proveedorID') == "0"){
                $proveedores = Proveedor::proveedores()->get();
            }else{
                $proveedores = Proveedor::proveedor($request->get('proveedorID'))->get();
            }
            foreach($proveedores as $proveedor){
                $datos[$count]['ide'] = ''; 
                $datos[$count]['ruc'] = $proveedor->proveedor_ruc; 
                $datos[$count]['nom'] = $proveedor->proveedor_nombre; 
                $datos[$count]['doc'] = '';
                $datos[$count]['mon'] = Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','1')->sum('cuenta_monto'); 
                $datos[$count]['sal'] = Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','1')->sum('cuenta_saldo'); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['ter'] = ''; 
                $datos[$count]['pla'] = ''; 
                $datos[$count]['tra'] = ''; 
                $datos[$count]['ret'] = ''; 
                $datos[$count]['tot'] = '1';
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $count ++;
                foreach(Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','1')->orderBy('cuenta_fecha')->get() as $cxp){
                    $datos[$count]['ide'] = ''; 
                    $datos[$count]['ruc'] = $cxp->cuenta_tipo; 
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = '';
                    if($cxp->transaccionCompra){ 
                        $datos[$count]['doc'] = $cxp->transaccionCompra->transaccion_numero;
                        $datos[$count]['nom'] = 'FACTURA'; 
                    }
                    if($cxp->liquidacionCompra){ 
                        $datos[$count]['doc'] =$cxp->liquidacionCompra->lc_numero;
                        $datos[$count]['nom'] = 'LIQUIDACIÓN DE COMPRA'; 
                    }
                    if($cxp->ingresoBodega){ 
                        $datos[$count]['doc'] =$cxp->ingresoBodega->cabecera_ingreso_numero;
                        $datos[$count]['nom'] = 'INGRESO A BODEGA'; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = substr($cxp->cuenta_descripcion, 39);
                        $datos[$count]['nom'] = 'FACTURA'; 
                        $datos[$count]['ide'] = $cxp->cuenta_id;
                    }
                    $datos[$count]['mon'] = $cxp->cuenta_monto; 
                    $datos[$count]['sal'] = $cxp->cuenta_saldo; 
                    $datos[$count]['fec'] = $cxp->cuenta_fecha; 
                    $datos[$count]['ter'] = $cxp->cuenta_fecha_fin; 
                    $datos[$count]['pla'] = '';
                    if($cxp->transaccionCompra){ $datos[$count]['pla'] = $cxp->transaccionCompra->transaccion_dias_plazo;}
                    if($cxp->liquidacionCompra){ $datos[$count]['pla'] = $cxp->liquidacionCompra->lc_dias_plazo;}
                    if($cxp->ingresoBodega){ $datos[$count]['pla'] = $cxp->ingresoBodega->cabecera_ingreso_plazo;}
                    $datos[$count]['tra'] = '0'; 
                    if($datos[$count]['sal'] > 0){
                        $date1 = new DateTime($cxp->cuenta_fecha);
                        $date2 = date('d-m-Y');
                        $date2 = new DateTime($date2);
                        $diff = $date1->diff($date2);
                        $datos[$count]['tra'] = $diff->days; 
                    }
                    $datos[$count]['ret'] = '';
                    if($cxp->transaccionCompra){if($cxp->transaccionCompra->retencionCompra){ $datos[$count]['ret'] = 'R';}}
                    if($cxp->liquidacionCompra){if($cxp->liquidacionCompra->retencionCompra){ $datos[$count]['ret'] = 'R';}}
                    $datos[$count]['tot'] = '0';
                    if($datos[$count]['tra'] > $datos[$count]['pla']){
                        $facVencidas = $facVencidas + floatval($datos[$count]['sal']);
                    }
                    if($datos[$count]['pla'] > $datos[$count]['tra']){
                        $facVencer = $facVencer + floatval($datos[$count]['sal']);
                    }
                    $count ++;
                    foreach(Detalle_Pago_CXP::CuentaPagarPagos($cxp->cuenta_id)->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['ide'] = ''; 
                        $datos[$count]['ruc'] = ''; 
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = '';
                        $datos[$count]['mon'] = $pago->detalle_pago_valor; 
                        $datos[$count]['sal'] = ''; 
                        $datos[$count]['fec'] = $pago->pagoCXP->pago_fecha; 
                        $datos[$count]['ter'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['pla'] = ''; 
                        $datos[$count]['tra'] = ''; 
                        $datos[$count]['ret'] = ''; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                    if($cxp->transaccionCompra){
                        foreach(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxp->transaccionCompra->transaccion_id)->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['ide'] = ''; 
                            $datos[$count]['ruc'] = ''; 
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = '';
                            $datos[$count]['mon'] = $pago->descuento_valor; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['fec'] = $pago->descuento_fecha; 
                            $datos[$count]['ter'] = 'DESCUENTO DE ANTICIPO A PROVEEDOR'; 
                            $datos[$count]['pla'] = ''; 
                            $datos[$count]['tra'] = ''; 
                            $datos[$count]['ret'] = ''; 
                            $datos[$count]['tot'] = '2';
                            $count ++;
                        }
                    }
                }
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasPagar.listaDeudas.index',['tipoCre'=>$tipoCre,'tipoCon'=>$tipoCon,'tipoOtr'=>$tipoOtr,'tipoEfe'=>$tipoEfe,'tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listaDeudas')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function pagos(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $datos = null;
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $todo = 0;
            if($request->get('formaCredito') != "on" and $request->get('formaContado') != "on" and $request->get('formaEfectivo') != "on" and $request->get('formaOtro') != "on"){
                return view('admin.cuentasPagar.listaDeudas.index',['tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            $tipoCre = '';
            $tipoCon = '';
            $tipoEfe = '';
            $tipoOtr = '';
            if($request->get('formaCredito') == "on"){
                $tipoCre = $request->get('formaCredito');
            }
            if($request->get('formaContado') == "on"){
                $tipoCon = $request->get('formaContado');
            }
            if($request->get('formaEfectivo') == "on"){
                $tipoEfe = $request->get('formaEfectivo');
            }
            if($request->get('formaOtro') == "on"){
                $tipoOtr = $request->get('formaOtro');
            }
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('proveedorID') == "0"){
                $proveedores = Proveedor::proveedores()->get();
            }else{
                $proveedores = Proveedor::proveedor($request->get('proveedorID'))->get();
            }
            foreach($proveedores as $proveedor){
                $datos[$count]['ide'] = ''; 
                $datos[$count]['ruc'] = $proveedor->proveedor_ruc; 
                $datos[$count]['nom'] = $proveedor->proveedor_nombre; 
                $datos[$count]['doc'] = '';
                $datos[$count]['mon'] = Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','2')->sum('cuenta_monto'); 
                $datos[$count]['sal'] = Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','2')->sum('cuenta_saldo'); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['ter'] = ''; 
                $datos[$count]['pla'] = ''; 
                $datos[$count]['tra'] = ''; 
                $datos[$count]['ret'] = ''; 
                $datos[$count]['tot'] = '1';
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $count ++;
                foreach(Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','2')->orderBy('cuenta_fecha')->get() as $cxp){
                    $datos[$count]['ide'] = ''; 
                    $datos[$count]['ruc'] = $cxp->cuenta_tipo;  
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = '';
                    if($cxp->transaccionCompra){ 
                        $datos[$count]['doc'] = $cxp->transaccionCompra->transaccion_numero;
                        $datos[$count]['nom'] = 'FACTURA'; 
                    }
                    if($cxp->liquidacionCompra){ 
                        $datos[$count]['doc'] =$cxp->liquidacionCompra->lc_numero;
                        $datos[$count]['nom'] = 'LIQUIDACIÓN DE COMPRA'; 
                    }
                    if($cxp->ingresoBodega){ 
                        $datos[$count]['doc'] =$cxp->ingresoBodega->cabecera_ingreso_numero;
                        $datos[$count]['nom'] = 'INGRESO A BODEGA'; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = substr($cxp->cuenta_descripcion, 39);
                        $datos[$count]['nom'] = 'FACTURA'; 
                        $datos[$count]['ide'] = $cxp->cuenta_id;
                    }
                    $datos[$count]['mon'] = $cxp->cuenta_monto; 
                    $datos[$count]['sal'] = $cxp->cuenta_saldo; 
                    $datos[$count]['fec'] = $cxp->cuenta_fecha; 
                    $datos[$count]['ter'] = $cxp->cuenta_fecha_fin; 
                    $datos[$count]['pla'] = '';
                    if($cxp->transaccionCompra){ $datos[$count]['pla'] = $cxp->transaccionCompra->transaccion_dias_plazo;}
                    if($cxp->liquidacionCompra){ $datos[$count]['pla'] = $cxp->liquidacionCompra->lc_dias_plazo;}
                    if($cxp->ingresoBodega){ $datos[$count]['pla'] = $cxp->ingresoBodega->cabecera_ingreso_plazo;}
                    $datos[$count]['tra'] = '0'; 
                    if($datos[$count]['sal'] > 0){
                        $date1 = new DateTime($cxp->cuenta_fecha);
                        $date2 = date('d-m-Y');
                        $date2 = new DateTime($date2);
                        $diff = $date1->diff($date2);
                        $datos[$count]['tra'] = $diff->days; 
                    }
                    
                    $datos[$count]['ret'] = '';
                    if($cxp->transaccionCompra){if($cxp->transaccionCompra->retencionCompra){ $datos[$count]['ret'] = 'R';}}
                    if($cxp->liquidacionCompra){if($cxp->liquidacionCompra->retencionCompra){ $datos[$count]['ret'] = 'R';}}
                    $datos[$count]['tot'] = '0';
                    if($datos[$count]['tra'] > $datos[$count]['pla']){
                        $facVencidas = $facVencidas + floatval($datos[$count]['sal']);
                    }
                    if($datos[$count]['pla'] > $datos[$count]['tra']){
                        $facVencer = $facVencer + floatval($datos[$count]['sal']);
                    }
                    $count ++;
                    foreach(Detalle_Pago_CXP::CuentaPagarPagos($cxp->cuenta_id)->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['ide'] = ''; 
                        $datos[$count]['ruc'] = ''; 
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = '';
                        $datos[$count]['mon'] = $pago->detalle_pago_valor; 
                        $datos[$count]['sal'] = ''; 
                        $datos[$count]['fec'] = $pago->pagoCXP->pago_fecha; 
                        $datos[$count]['ter'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['pla'] = ''; 
                        $datos[$count]['tra'] = ''; 
                        $datos[$count]['ret'] = ''; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                    if($cxp->transaccionCompra){
                        foreach(Descuento_Anticipo_Proveedor::DescuentosAnticipoByFactura($cxp->transaccionCompra->transaccion_id)->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['ide'] = ''; 
                            $datos[$count]['ruc'] = ''; 
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = '';
                            $datos[$count]['mon'] = $pago->descuento_valor; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['fec'] = $pago->descuento_fecha; 
                            $datos[$count]['ter'] = 'DESCUENTO DE ANTICIPO A PROVEEDOR'; 
                            $datos[$count]['pla'] = ''; 
                            $datos[$count]['tra'] = ''; 
                            $datos[$count]['ret'] = ''; 
                            $datos[$count]['tot'] = '2';
                            $count ++;
                        }
                    }
                }
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasPagar.listaDeudas.index',['tipoCre'=>$tipoCre,'tipoCon'=>$tipoCon,'tipoOtr'=>$tipoOtr,'tipoEfe'=>$tipoEfe,'tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listaDeudas')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function estadoCuenta(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $datos = null;
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $todo = 0;
            if($request->get('formaCredito') != "on" and $request->get('formaContado') != "on" and $request->get('formaEfectivo') != "on" and $request->get('formaOtro') != "on"){
                return view('admin.cuentasPagar.listaDeudas.index',['tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            $tipoCre = '';
            $tipoCon = '';
            $tipoEfe = '';
            $tipoOtr = '';
            if($request->get('formaCredito') == "on"){
                $tipoCre = $request->get('formaCredito');
            }
            if($request->get('formaContado') == "on"){
                $tipoCon = $request->get('formaContado');
            }
            if($request->get('formaEfectivo') == "on"){
                $tipoEfe = $request->get('formaEfectivo');
            }
            if($request->get('formaOtro') == "on"){
                $tipoOtr = $request->get('formaOtro');
            }
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('proveedorID') == "0"){
                $proveedores = Proveedor::proveedores()->get();
            }else{
                $proveedores = Proveedor::proveedor($request->get('proveedorID'))->get();
            }
            foreach($proveedores as $proveedor){
                $datos[$count]['ide'] = ''; 
                $datos[$count]['ruc'] = $proveedor->proveedor_ruc; 
                $datos[$count]['nom'] = $proveedor->proveedor_nombre; 
                $datos[$count]['doc'] = '';
                $datos[$count]['mon'] = Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','1')->sum('cuenta_monto'); 
                $datos[$count]['sal'] = Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','1')->sum('cuenta_saldo'); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['ter'] = ''; 
                $datos[$count]['pla'] = ''; 
                $datos[$count]['tra'] = ''; 
                $datos[$count]['ret'] = ''; 
                $datos[$count]['tot'] = '1';
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $count ++;
                foreach(Cuenta_Pagar::CuentasDeudas($proveedor->proveedor_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'),$request->get('formaOtro'))->where('cuenta_estado','=','1')->orderBy('cuenta_fecha')->get() as $cxp){
                    $datos[$count]['ide'] = ''; 
                    $datos[$count]['ruc'] = $cxp->cuenta_tipo; 
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = '';
                    if($cxp->transaccionCompra){ 
                        $datos[$count]['doc'] = $cxp->transaccionCompra->transaccion_numero;
                        $datos[$count]['nom'] = 'FACTURA'; 
                    }
                    if($cxp->liquidacionCompra){ 
                        $datos[$count]['doc'] =$cxp->liquidacionCompra->lc_numero;
                        $datos[$count]['nom'] = 'LIQUIDACIÓN DE COMPRA'; 
                    }
                    if($cxp->ingresoBodega){ 
                        $datos[$count]['doc'] =$cxp->ingresoBodega->cabecera_ingreso_numero;
                        $datos[$count]['nom'] = 'INGRESO A BODEGA'; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = substr($cxp->cuenta_descripcion, 39);
                        $datos[$count]['nom'] = 'FACTURA'; 
                        $datos[$count]['ide'] = $cxp->cuenta_id;
                    }
                    $datos[$count]['mon'] = $cxp->cuenta_monto; 
                    $datos[$count]['sal'] = $cxp->cuenta_saldo; 
                    $datos[$count]['fec'] = $cxp->cuenta_fecha; 
                    $datos[$count]['ter'] = $cxp->cuenta_fecha_fin; 
                    $datos[$count]['pla'] = '';
                    if($cxp->transaccionCompra){ $datos[$count]['pla'] = $cxp->transaccionCompra->transaccion_dias_plazo;}
                    if($cxp->liquidacionCompra){ $datos[$count]['pla'] = $cxp->liquidacionCompra->lc_dias_plazo;}
                    if($cxp->ingresoBodega){ $datos[$count]['pla'] = $cxp->ingresoBodega->cabecera_ingreso_plazo;}
                    $datos[$count]['tra'] = '0'; 
                    if($datos[$count]['sal'] > 0){
                        $date1 = new DateTime($cxp->cuenta_fecha);
                        $date2 = date('d-m-Y');
                        $date2 = new DateTime($date2);
                        $diff = $date1->diff($date2);
                        $datos[$count]['tra'] = $diff->days; 
                    }
                    $datos[$count]['ret'] = '';
                    if($cxp->transaccionCompra){if($cxp->transaccionCompra->retencionCompra){ $datos[$count]['ret'] = 'R';}}
                    if($cxp->liquidacionCompra){if($cxp->liquidacionCompra->retencionCompra){ $datos[$count]['ret'] = 'R';}}
                    $datos[$count]['tot'] = '0';
                    if($datos[$count]['tra'] > $datos[$count]['pla']){
                        $facVencidas = $facVencidas + floatval($datos[$count]['sal']);
                    }
                    if($datos[$count]['pla'] > $datos[$count]['tra']){
                        $facVencer = $facVencer + floatval($datos[$count]['sal']);
                    }
                    $count ++;
                }
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasPagar.listaDeudas.index',['tipoCre'=>$tipoCre,'tipoCon'=>$tipoCon,'tipoOtr'=>$tipoOtr,'tipoEfe'=>$tipoEfe,'tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'proveedorC'=>$request->get('proveedorID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'proveedores'=>Proveedor::proveedores()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listaDeudas')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function pdf(Request $request){
        try{            
            $actual = date('Y-m-d');
            $todo = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            $datos = null;
            $count = 1;
            $ruc = $request->get('idRuc');
            $nom = $request->get('idNom');
            $doc = $request->get('idDoc');
            $mon = $request->get('idMon');
            $sal = $request->get('idSal');
            $fec = $request->get('idFec');
            $ter = $request->get('idTer');
            $pla = $request->get('idPla');
            $tra = $request->get('idTra');
            $ret = $request->get('idRet');
            $tot = $request->get('idTot');
            if($ruc){
                for ($i = 0; $i < count($ruc); ++$i){
                    $datos[$count]['ruc'] = $ruc[$i];
                    $datos[$count]['nom'] = $nom[$i]; 
                    $datos[$count]['doc'] = $doc[$i]; 
                    $datos[$count]['mon'] = $mon[$i]; 
                    $datos[$count]['sal'] = $sal[$i];
                    $datos[$count]['fec'] = $fec[$i]; 
                    $datos[$count]['ter'] = $ter[$i]; 
                    $datos[$count]['pla'] = $pla[$i]; 
                    $datos[$count]['tra'] = $tra[$i]; 
                    $datos[$count]['ret'] = $ret[$i];
                    $datos[$count]['tot'] = $tot[$i]; 
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.listaDeudas', ['vencidas'=>$request->get('idVencidas'),'vencer'=>$request->get('idAVencer'),'monto'=>$request->get('idMonto'),'saldo'=>$request->get('idSaldo'),'todo'=>$todo,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'actual'=>DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'),'empresa'=>$empresa]);
            if($todo == 1){
                $nombreArchivo = 'LISTA DE DEUDAS AL '.DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-m-Y');
            }else{
                $nombreArchivo = 'LISTA DE DEUDAS DEL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            }
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('listaDeudas')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excel(Request $request){
        try{   
            $datos = null;
            $count = 1;
            $ruc = $request->get('idRuc');
            $nom = $request->get('idNom');
            $doc = $request->get('idDoc');
            $mon = $request->get('idMon');
            $sal = $request->get('idSal');
            $fec = $request->get('idFec');
            $ter = $request->get('idTer');
            $pla = $request->get('idPla');
            $tra = $request->get('idTra');
            $ret = $request->get('idRet');
            $tot = $request->get('idTot');
            if($ruc){
                for ($i = 0; $i < count($ruc); ++$i){
                    $datos[$count]['ruc'] = $ruc[$i];
                    $datos[$count]['nom'] = $nom[$i]; 
                    $datos[$count]['doc'] = $doc[$i]; 
                    $datos[$count]['mon'] = $mon[$i]; 
                    $datos[$count]['sal'] = $sal[$i];
                    $datos[$count]['fec'] = $fec[$i]; 
                    $datos[$count]['ter'] = $ter[$i]; 
                    $datos[$count]['pla'] = $pla[$i]; 
                    $datos[$count]['tra'] = $tra[$i]; 
                    $datos[$count]['ret'] = $ret[$i];
                    $datos[$count]['tot'] = $tot[$i]; 
                    $count ++;
                }
            }
            return Excel::download(new ViewExcel('admin.formatosExcel.listaDeudas',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        }catch(\Exception $ex){
            return redirect('listaDeudas')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
