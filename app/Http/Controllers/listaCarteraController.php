<?php

namespace App\Http\Controllers;

use App\Models\Banco_Lista;
use App\Models\Cliente;
use App\Models\Cuenta_Cobrar;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Pago_CXC;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\NEOPAGUPA\ViewExcel;
use DateTime;
use PDF;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listaCarteraController extends Controller
{
    public function nuevo(){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasCobrar.listaCartera.index',['sucursales'=>Sucursal::sucursales()->get(),'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            return $this->excel($this->datos($request));
        }
        if (isset($_POST['guardarID'])){
            return $this->guardar($request);
        }
    }
    public function guardar(Request $request){
        try {
            DB::beginTransaction();   
            $general = new generalController();
            $idcuenta=$request->get('idfac');
            $nombre_doc=$request->get('idNom');
            $factura_cheque=$request->get('ncheque');
            $factura_banco=$request->get('bancoID');
            for ($i = 1; $i <= count($factura_cheque); ++$i) {

                if (!empty($nombre_doc[$i])) {
                    if ($nombre_doc[$i]=="FACTURA") {
                        if (!empty($factura_cheque[$i])) {
                            $cuenta=Cuenta_Cobrar::findOrFail($idcuenta[$i]);
                            $cuenta->cuenta_banco_anticipado=$factura_banco[$i];
                            $cuenta->cuenta_cheque_anticipado=$factura_cheque[$i];
                            $cuenta->save();
                            $general->registrarAuditoria('Actualizar cuenta corbar lista de cartera con banco   -> '.$factura_banco[$i]. ' y cheque '.$factura_cheque[$i], '', '');
                        } else {
                            $cuenta=Cuenta_Cobrar::findOrFail($idcuenta[$i]);
                            $cuenta->cuenta_banco_anticipado=null;
                            $cuenta->cuenta_cheque_anticipado=null;
                            $cuenta->save();
                        }
                    }
                }
            }
            DB::commit();
            return redirect('listaCartera')->with('success', 'Datos guardados exitosamente');
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

       
    }
    public function datos(Request $request){
        try{   
            $datos = null;
            switch ($request->get('tipoConsulta')) {
                case 0:
                    $datos = $this->general($request);
                    break;
                case 1:
                    $datos = $this->deudas($request);
                    break;
                case 2:
                    $datos = $this->pagos($request);
                    break;
                case 3:
                    $datos = $this->estadoCuenta($request);
                    break;
            }
            return $datos;
        }catch(\Exception $ex){
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscar(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $datos = null;
            $tipoCre = '';
            $tipoCon = '';
            $tipoEfe = '';
            $todo = 0; 
            if($request->get('formaCredito') == "on"){
                $tipoCre = $request->get('formaCredito');
            }
            if($request->get('formaContado') == "on"){
                $tipoCon = $request->get('formaContado');
            }
            if($request->get('formaEfectivo') == "on"){
                $tipoEfe = $request->get('formaEfectivo');
            }
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            $bancos=Banco_Lista::BancoListas()->get();
            if($request->get('formaCredito') != "on" and $request->get('formaContado') != "on" and $request->get('formaEfectivo') != "on"){
                return view('admin.cuentasCobrar.listaCartera.index',['bancos'=>$bancos,'tipo'=>$request->get('tipoConsulta'),'vencer'=>$facVencer,'vencidas'=>$facVencidas,'monto'=>$totMon,'saldo'=>$totSal,'datos'=>$datos,'sucurslaC'=>$request->get('sucursal_id'),'clienteC'=>$request->get('clienteID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            switch ($request->get('tipoConsulta')) {
                case 0:
                    $datos = $this->general($request);
                    break;
                case 1:
                    $datos = $this->deudas($request);
                    break;
                case 2:
                    $datos = $this->pagos($request);
                    break;
                case 3:
                    $datos = $this->estadoCuenta($request);
                    break;
            }
            return view('admin.cuentasCobrar.listaCartera.index',['bancos'=>$bancos,'tipoCre'=>$tipoCre,'tipoCon'=>$tipoCon,'tipoEfe'=>$tipoEfe,'tipo'=>$request->get('tipoConsulta'),'vencer'=>$datos[1],'vencidas'=>$datos[2],'monto'=>$datos[3],'saldo'=>$datos[4],'datos'=>$datos[0],'sucurslaC'=>$request->get('sucursal_id'),'clienteC'=>$request->get('clienteID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'sucursales'=>Sucursal::sucursales()->get(),'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function general(Request $request){
        try{   
            $resultado = null;
            $count = 1;
            $datos = null;
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $todo = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('clienteID') == "0"){
                $clientes = Cliente::clientes()->get();
            }else{
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }
            foreach($clientes as $cliente){
                $datos[$count]['ide'] = ''; 
                $datos[$count]['ruc'] = $cliente->cliente_cedula; 
                $datos[$count]['nom'] = $cliente->cliente_nombre; 
                $datos[$count]['doc'] = '';
                $datos[$count]['cheque'] = '';
                $datos[$count]['banco'] ='';
                $datos[$count]['pag'] = ''; 
                $datos[$count]['mon'] = Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->sum('cuenta_monto'); 
                $datos[$count]['sal'] = Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->sum('cuenta_saldo'); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['ter'] = ''; 
                $datos[$count]['pla'] = ''; 
                $datos[$count]['tra'] = ''; 
                $datos[$count]['ret'] = ''; 
                $datos[$count]['tot'] = '1';
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $count ++;
                foreach(Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->orderBy('cuenta_fecha')->orderBy('cuenta_id')->get() as $cxc){
                    $datos[$count]['ide'] = ''; 
                    $datos[$count]['ruc'] = ''; 
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = '';
                    if($cxc->facturaVenta){ 
                        $datos[$count]['ide'] = $cxc->cuenta_id;
                        $datos[$count]['doc'] = $cxc->facturaVenta->factura_numero;
                        $datos[$count]['nom'] = 'FACTURA'; 
                    }
                    if($cxc->notaEntrega){ 
                        $datos[$count]['doc'] = $cxc->notaEntrega->nt_numero;
                        $datos[$count]['nom'] = 'NOTA DE ENTREGA'; 
                    }
                    if($cxc->notaDebito){ 
                        $datos[$count]['doc'] = $cxc->notaDebito->nd_numero;
                        $datos[$count]['nom'] = 'NOTA DE DÉBITO'; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = substr($cxc->cuenta_descripcion, 38);
                        $datos[$count]['nom'] = 'FACTURA'; 
                        $datos[$count]['ide'] = $cxc->cuenta_id;
                    }
                    $datos[$count]['pag'] = ''; 
                    $datos[$count]['cheque'] = $cxc->cuenta_cheque_anticipado;
                    $datos[$count]['banco'] = $cxc->cuenta_banco_anticipado;
                    $datos[$count]['mon'] = $cxc->cuenta_monto; 
                    $datos[$count]['sal'] = $cxc->cuenta_saldo; 
                    $datos[$count]['fec'] = $cxc->cuenta_fecha; 
                    $datos[$count]['ter'] = $cxc->cuenta_fecha_fin; 
                    $datos[$count]['pla'] = '';
                    $fechaI = new DateTime($cxc->cuenta_fecha);
                    $fechaF = new DateTime($cxc->cuenta_fecha_fin);
                    $diff = $fechaI->diff($fechaF);
                    $datos[$count]['pla'] = $diff->days;
                    if($cxc->facturaVenta){ $datos[$count]['pla'] = $cxc->facturaVenta->factura_dias_plazo;}
                    if($cxc->notaDebito){ $datos[$count]['pla'] = $cxc->notaDebito->nd_dias_plazo;}
                    $datos[$count]['tra'] = '0'; 
                    if($datos[$count]['sal'] > 0){
                        $date1 = new DateTime($cxc->cuenta_fecha);
                        $date2 = date('d-m-Y');
                        $date2 = new DateTime($date2);
                        $diff = $date1->diff($date2);
                        $datos[$count]['tra'] = $diff->days; 
                    }
                    
                    $datos[$count]['ret'] = '';
                    if($cxc->facturaVenta){if($cxc->facturaVenta->retencion){ $datos[$count]['ret'] = 'R';}}
                    if($cxc->notaDebito){if($cxc->notaDebito->retencion){ $datos[$count]['ret'] = 'R';}}
                    $datos[$count]['tot'] = '0';
                    if($datos[$count]['tra'] > $datos[$count]['pla']){
                        $facVencidas = $facVencidas + floatval($datos[$count]['sal']);
                    }
                    if($datos[$count]['pla'] > $datos[$count]['tra']){
                        $facVencer = $facVencer + floatval($datos[$count]['sal']);
                    }
                    $count ++;
                    foreach(Detalle_Pago_CXC::CuentaCobrarPagos($cxc->cuenta_id)->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['ide'] = ''; 
                        $datos[$count]['ruc'] = ''; 
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = '';
                        $datos[$count]['cheque'] = '';
                        $datos[$count]['banco'] ='';
                        $datos[$count]['mon'] = ''; 
                        $datos[$count]['pag'] = $pago->detalle_pago_valor; 
                        $datos[$count]['sal'] = ''; 
                        $datos[$count]['fec'] = $pago->pagoCXC->pago_fecha; 
                        $datos[$count]['ter'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['pla'] = ''; 
                        $datos[$count]['tra'] = ''; 
                        $datos[$count]['ret'] = ''; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                    if($cxc->facturaVenta){
                        foreach(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxc->facturaVenta->factura_id)->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['ide'] = ''; 
                            $datos[$count]['ruc'] = ''; 
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = '';
                            $datos[$count]['cheque'] = '';
                            $datos[$count]['banco'] ='';
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['pag'] = $pago->descuento_valor; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['fec'] = $pago->descuento_fecha; 
                            $datos[$count]['ter'] = 'DESCUENTO DE ANTICIPO DE CLIENTE'; 
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
            $resultado[0] = $datos;
            $resultado[1] = $facVencer;
            $resultado[2] = $facVencidas;
            $resultado[3] = $totMon;
            $resultado[4] = $totSal;
            return $resultado;
        }catch(\Exception $ex){
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function deudas(Request $request){
        try{   
            $resultado = null;
            $count = 1;
            $datos = null;
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $todo = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('clienteID') == "0"){
                $clientes = Cliente::clientes()->get();
            }else{
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }
            foreach($clientes as $cliente){
                $datos[$count]['ide'] = ''; 
                $datos[$count]['ruc'] = $cliente->cliente_cedula; 
                $datos[$count]['nom'] = $cliente->cliente_nombre; 
                $datos[$count]['doc'] = '';
                $datos[$count]['cheque'] = '';
                $datos[$count]['banco'] ='';
                $datos[$count]['pag'] = ''; 
                $datos[$count]['mon'] = Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','1')->sum('cuenta_monto'); 
                $datos[$count]['sal'] = Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','1')->sum('cuenta_saldo'); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['ter'] = ''; 
                $datos[$count]['pla'] = ''; 
                $datos[$count]['tra'] = ''; 
                $datos[$count]['ret'] = ''; 
                $datos[$count]['tot'] = '1';
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $count ++;
                foreach(Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','1')->orderBy('cuenta_fecha')->orderBy('cuenta_id')->get() as $cxc){
                    $datos[$count]['ide'] = ''; 
                    $datos[$count]['ruc'] = ''; 
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = '';
                    if($cxc->facturaVenta){ 
                        $datos[$count]['ide'] = $cxc->cuenta_id;
                        $datos[$count]['doc'] = $cxc->facturaVenta->factura_numero;
                        $datos[$count]['nom'] = 'FACTURA'; 
                    }
                    if($cxc->notaEntrega){ 
                        $datos[$count]['doc'] = $cxc->notaEntrega->nt_numero;
                        $datos[$count]['nom'] = 'NOTA DE ENTREGA'; 
                    }
                    if($cxc->notaDebito){ 
                        $datos[$count]['doc'] = $cxc->notaDebito->nd_numero;
                        $datos[$count]['nom'] = 'NOTA DE DÉBITO'; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = substr($cxc->cuenta_descripcion, 38);
                        $datos[$count]['nom'] = 'FACTURA'; 
                        $datos[$count]['ide'] = $cxc->cuenta_id;
                    }
                    $datos[$count]['pag'] = ''; 
                    $datos[$count]['mon'] = $cxc->cuenta_monto; 
                    $datos[$count]['cheque'] = $cxc->cuenta_cheque_anticipado;
                    $datos[$count]['banco'] = $cxc->cuenta_banco_anticipado;
                    $datos[$count]['sal'] = $cxc->cuenta_saldo; 
                    $datos[$count]['fec'] = $cxc->cuenta_fecha; 
                    $datos[$count]['ter'] = $cxc->cuenta_fecha_fin; 
                    $datos[$count]['pla'] = '';
                    $fechaI = new DateTime($cxc->cuenta_fecha);
                    $fechaF = new DateTime($cxc->cuenta_fecha_fin);
                    $diff = $fechaI->diff($fechaF);
                    $datos[$count]['pla'] = $diff->days;
                    if($cxc->facturaVenta){ $datos[$count]['pla'] = $cxc->facturaVenta->factura_dias_plazo;}
                    if($cxc->notaDebito){ $datos[$count]['pla'] = $cxc->notaDebito->nd_dias_plazo;}
                    $datos[$count]['tra'] = '0'; 
                    if($datos[$count]['sal'] > 0){
                        $date1 = new DateTime($cxc->cuenta_fecha);
                        $date2 = date('d-m-Y');
                        $date2 = new DateTime($date2);
                        $diff = $date1->diff($date2);
                        $datos[$count]['tra'] = $diff->days; 
                    }
                    
                    $datos[$count]['ret'] = '';
                    if($cxc->facturaVenta){if($cxc->facturaVenta->retencion){ $datos[$count]['ret'] = 'R';}}
                    if($cxc->notaDebito){if($cxc->notaDebito->retencion){ $datos[$count]['ret'] = 'R';}}
                    $datos[$count]['tot'] = '0';
                    if($datos[$count]['tra'] > $datos[$count]['pla']){
                        $facVencidas = $facVencidas + floatval($datos[$count]['sal']);
                    }
                    if($datos[$count]['pla'] > $datos[$count]['tra']){
                        $facVencer = $facVencer + floatval($datos[$count]['sal']);
                    }
                    $count ++;
                    foreach(Detalle_Pago_CXC::CuentaCobrarPagos($cxc->cuenta_id)->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['ide'] = ''; 
                        $datos[$count]['ruc'] = ''; 
                        $datos[$count]['nom'] = ''; 
                        $datos[$count]['doc'] = '';
                        $datos[$count]['cheque'] = '';
                        $datos[$count]['banco'] ='';
                        $datos[$count]['mon'] = ''; 
                        $datos[$count]['pag'] = $pago->detalle_pago_valor; 
                        $datos[$count]['sal'] = ''; 
                        $datos[$count]['fec'] = $pago->pagoCXC->pago_fecha; 
                        $datos[$count]['ter'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['pla'] = ''; 
                        $datos[$count]['tra'] = ''; 
                        $datos[$count]['ret'] = ''; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                    if($cxc->facturaVenta){
                        foreach(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxc->facturaVenta->factura_id)->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['ide'] = ''; 
                            $datos[$count]['ruc'] = ''; 
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['doc'] = '';
                            $datos[$count]['cheque'] = '';
                            $datos[$count]['banco'] ='';
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['pag'] = $pago->descuento_valor; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['fec'] = $pago->descuento_fecha; 
                            $datos[$count]['ter'] = 'DESCUENTO DE ANTICIPO DE CLIENTE'; 
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
            $resultado[0] = $datos;
            $resultado[1] = $facVencer;
            $resultado[2] = $facVencidas;
            $resultado[3] = $totMon;
            $resultado[4] = $totSal;
            return $resultado;
        }catch(\Exception $ex){
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function pagos(Request $request){
        try{   
            $resultado = null;
            $count = 1;
            $datos = null;
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $todo = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('clienteID') == "0"){
                $clientes = Cliente::clientes()->get();
            }else{
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }
            foreach($clientes as $cliente){
                $datos[$count]['ide'] = ''; 
                $datos[$count]['ruc'] = $cliente->cliente_cedula; 
                $datos[$count]['nom'] = $cliente->cliente_nombre; 
                $datos[$count]['cheque'] = '';
                $datos[$count]['banco'] ='';
                $datos[$count]['doc'] = '';
                $datos[$count]['pag'] =''; 
                $datos[$count]['mon'] = Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','2')->sum('cuenta_monto'); 
                $datos[$count]['sal'] = Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','2')->sum('cuenta_saldo'); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['ter'] = ''; 
                $datos[$count]['pla'] = ''; 
                $datos[$count]['tra'] = ''; 
                $datos[$count]['ret'] = ''; 
                $datos[$count]['tot'] = '1';
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $count ++;
                foreach(Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','2')->orderBy('cuenta_fecha')->orderBy('cuenta_id')->get() as $cxc){
                    $datos[$count]['ide'] = ''; 
                    $datos[$count]['ruc'] = ''; 
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = '';
                    if($cxc->facturaVenta){ 
                        $datos[$count]['ide'] = $cxc->cuenta_id;
                        $datos[$count]['doc'] = $cxc->facturaVenta->factura_numero;
                        $datos[$count]['nom'] = 'FACTURA'; 
                    }
                    if($cxc->notaEntrega){ 
                        $datos[$count]['doc'] = $cxc->notaEntrega->nt_numero;
                        $datos[$count]['nom'] = 'NOTA DE ENTREGA'; 
                    }
                    if($cxc->notaDebito){ 
                        $datos[$count]['doc'] = $cxc->notaDebito->nd_numero;
                        $datos[$count]['nom'] = 'NOTA DE DÉBITO'; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = substr($cxc->cuenta_descripcion, 38);
                        $datos[$count]['nom'] = 'FACTURA'; 
                        $datos[$count]['ide'] = $cxc->cuenta_id;
                    }
                    $datos[$count]['mon'] = $cxc->cuenta_monto; 
                    $datos[$count]['pag'] =''; 
                    $datos[$count]['cheque'] = $cxc->cuenta_cheque_anticipado;
                    $datos[$count]['banco'] = $cxc->cuenta_banco_anticipado;
                    $datos[$count]['sal'] = $cxc->cuenta_saldo; 
                    $datos[$count]['fec'] = $cxc->cuenta_fecha; 
                    $datos[$count]['ter'] = $cxc->cuenta_fecha_fin; 
                    $datos[$count]['pla'] = '';
                    $fechaI = new DateTime($cxc->cuenta_fecha);
                    $fechaF = new DateTime($cxc->cuenta_fecha_fin);
                    $diff = $fechaI->diff($fechaF);
                    $datos[$count]['pla'] = $diff->days;
                    if($cxc->facturaVenta){ $datos[$count]['pla'] = $cxc->facturaVenta->factura_dias_plazo;}
                    if($cxc->notaDebito){ $datos[$count]['pla'] = $cxc->notaDebito->nd_dias_plazo;}
                    $datos[$count]['tra'] = '0'; 
                    if($datos[$count]['sal'] > 0){
                        $date1 = new DateTime($cxc->cuenta_fecha);
                        $date2 = date('d-m-Y');
                        $date2 = new DateTime($date2);
                        $diff = $date1->diff($date2);
                        $datos[$count]['tra'] = $diff->days; 
                    }
                    
                    $datos[$count]['ret'] = '';
                    if($cxc->facturaVenta){if($cxc->facturaVenta->retencion){ $datos[$count]['ret'] = 'R';}}
                    if($cxc->notaDebito){if($cxc->notaDebito->retencion){ $datos[$count]['ret'] = 'R';}}
                    $datos[$count]['tot'] = '0';
                    if($datos[$count]['tra'] > $datos[$count]['pla']){
                        $facVencidas = $facVencidas + floatval($datos[$count]['sal']);
                    }
                    if($datos[$count]['pla'] > $datos[$count]['tra']){
                        $facVencer = $facVencer + floatval($datos[$count]['sal']);
                    }
                    $count ++;
                    foreach(Detalle_Pago_CXC::CuentaCobrarPagos($cxc->cuenta_id)->orderBy('pago_fecha')->get() as $pago){
                        $datos[$count]['ide'] = ''; 
                        $datos[$count]['ruc'] = ''; 
                        $datos[$count]['nom'] = '';
                        $datos[$count]['cheque'] = ''; 
                        $datos[$count]['banco'] ='';
                        $datos[$count]['doc'] = '';
                        $datos[$count]['mon'] = ''; 
                        $datos[$count]['pag'] = $pago->detalle_pago_valor; 
                        $datos[$count]['sal'] = ''; 
                        $datos[$count]['fec'] = $pago->pagoCXC->pago_fecha; 
                        $datos[$count]['ter'] = $pago->detalle_pago_descripcion; 
                        $datos[$count]['pla'] = ''; 
                        $datos[$count]['tra'] = ''; 
                        $datos[$count]['ret'] = ''; 
                        $datos[$count]['tot'] = '2';
                        $count ++;
                    }
                    if($cxc->facturaVenta){
                        foreach(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxc->facturaVenta->factura_id)->orderBy('descuento_fecha')->get() as $pago){
                            $datos[$count]['ide'] = ''; 
                            $datos[$count]['ruc'] = ''; 
                            $datos[$count]['nom'] = ''; 
                            $datos[$count]['cheque'] = '';
                            $datos[$count]['banco'] ='';
                            $datos[$count]['doc'] = '';
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['pag'] = $pago->descuento_valor; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['fec'] = $pago->descuento_fecha; 
                            $datos[$count]['ter'] = 'DESCUENTO DE ANTICIPO DE CLIENTE'; 
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
            $resultado[0] = $datos;
            $resultado[1] = $facVencer;
            $resultado[2] = $facVencidas;
            $resultado[3] = $totMon;
            $resultado[4] = $totSal;
            return $resultado;
        }catch(\Exception $ex){
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function estadoCuenta(Request $request){
        try{   
            $resultado = null;
            $count = 1;
            $datos = null;
            $totMon= 0;
            $totSal = 0;
            $facVencidas = 0;
            $facVencer = 0;
            $todo = 0;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            if($request->get('clienteID') == "0"){
                $clientes = Cliente::clientes()->get();
            }else{
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }
            foreach($clientes as $cliente){
                $datos[$count]['ide'] = ''; 
                $datos[$count]['ruc'] = $cliente->cliente_cedula; 
                $datos[$count]['nom'] = $cliente->cliente_nombre; 
                $datos[$count]['doc'] = '';
                $datos[$count]['cheque'] = '';
                $datos[$count]['banco'] ='';
                $datos[$count]['pag'] =''; 
                $datos[$count]['mon'] = Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','1')->sum('cuenta_monto'); 
                $datos[$count]['sal'] = Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','1')->sum('cuenta_saldo'); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['ter'] = ''; 
                $datos[$count]['pla'] = ''; 
                $datos[$count]['tra'] = ''; 
                $datos[$count]['ret'] = ''; 
                $datos[$count]['tot'] = '1';
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $count ++;
                foreach(Cuenta_Cobrar::CuentasCartera($cliente->cliente_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$todo,$request->get('sucursal_id'),$request->get('formaCredito'),$request->get('formaContado'),$request->get('formaEfectivo'))->where('cuenta_estado','=','1')->orderBy('cuenta_fecha')->orderBy('cuenta_id')->get() as $cxc){
                    $datos[$count]['ide'] = ''; 
                    $datos[$count]['ruc'] = ''; 
                    $datos[$count]['nom'] = ''; 
                    $datos[$count]['doc'] = '';
                    if($cxc->facturaVenta){ 
                        $datos[$count]['ide'] = $cxc->cuenta_id;
                        $datos[$count]['doc'] = $cxc->facturaVenta->factura_numero;
                        $datos[$count]['nom'] = 'FACTURA'; 
                    }
                    if($cxc->notaEntrega){ 
                        $datos[$count]['doc'] = $cxc->notaEntrega->nt_numero;
                        $datos[$count]['nom'] = 'NOTA DE ENTREGA'; 
                    }
                    if($cxc->notaDebito){ 
                        $datos[$count]['doc'] = $cxc->notaDebito->nd_numero;
                        $datos[$count]['nom'] = 'NOTA DE DÉBITO'; 
                    }
                    if($datos[$count]['doc'] == ''){
                        $datos[$count]['doc'] = substr($cxc->cuenta_descripcion, 38);
                        $datos[$count]['nom'] = 'FACTURA'; 
                        $datos[$count]['ide'] = $cxc->cuenta_id;
                    }
                    $datos[$count]['pag'] =''; 
                    $datos[$count]['mon'] = $cxc->cuenta_monto; 
                    $datos[$count]['cheque'] = $cxc->cuenta_cheque_anticipado;
                    $datos[$count]['banco'] = $cxc->cuenta_banco_anticipado;
                    $datos[$count]['sal'] = $cxc->cuenta_saldo; 
                    $datos[$count]['fec'] = $cxc->cuenta_fecha; 
                    $datos[$count]['ter'] = $cxc->cuenta_fecha_fin; 
                    $fechaI = new DateTime($cxc->cuenta_fecha);
                    $fechaF = new DateTime($cxc->cuenta_fecha_fin);
                    $diff = $fechaI->diff($fechaF);
                    $datos[$count]['pla'] = $diff->days;
                    if($cxc->facturaVenta){ $datos[$count]['pla'] = $cxc->facturaVenta->factura_dias_plazo;}
                    if($cxc->notaDebito){ $datos[$count]['pla'] = $cxc->notaDebito->nd_dias_plazo;}
                    $datos[$count]['tra'] = '0'; 
                    if($datos[$count]['sal'] > 0){
                        $date1 = new DateTime($cxc->cuenta_fecha);
                        $date2 = date('d-m-Y');
                        $date2 = new DateTime($date2);
                        $diff = $date1->diff($date2);
                        $datos[$count]['tra'] = $diff->days; 
                    }
                    
                    $datos[$count]['ret'] = '';
                    if($cxc->facturaVenta){if($cxc->facturaVenta->retencion){ $datos[$count]['ret'] = 'R';}}
                    if($cxc->notaDebito){if($cxc->notaDebito->retencion){ $datos[$count]['ret'] = 'R';}}
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
            $resultado[0] = $datos;
            $resultado[1] = $facVencer;
            $resultado[2] = $facVencidas;
            $resultado[3] = $totMon;
            $resultado[4] = $totSal;
            return $resultado;
        }catch(\Exception $ex){
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $banco = $request->get('bancoID');
            $cheque = $request->get('ncheque');
            $doc = $request->get('idDoc');
            $mon = $request->get('idMon');
            $pag = $request->get('idPag');
            $sal = $request->get('idSal');
            $fec = $request->get('idFec');
            $ter = $request->get('idTer');
            $pla = $request->get('idPla');
            $tra = $request->get('idTra');
            $ret = $request->get('idRet');
            $tot = $request->get('idTot');
            $cheque = $request->get('ncheque');
            $banco = $request->get('bancoID');

            if($ruc){
                for ($i = 0; $i < count($ruc); ++$i){
                    $datos[$count]['ruc'] = $ruc[$i];
                    $datos[$count]['nom'] = $nom[$i]; 
                    $datos[$count]['doc'] = $doc[$i]; 
                    $datos[$count]['mon'] = $mon[$i]; 
                    if($request->get('tipoConsulta') != 3){
                        $datos[$count]['pag'] = $pag[$i]; 
                    }
                    $datos[$count]['sal'] = $sal[$i];
                    $datos[$count]['fec'] = $fec[$i]; 
                    $datos[$count]['ter'] = $ter[$i]; 
                    $datos[$count]['pla'] = $pla[$i]; 
                    $datos[$count]['tra'] = $tra[$i]; 
                    $datos[$count]['ret'] = $ret[$i];
                    $datos[$count]['tot'] = $tot[$i]; 
                    $datos[$count]['cheque'] = "";
                    $datos[$count]['banco'] = ""; 
                    if (!empty($nom[$i])) {
                        if ($nom[$i]=="FACTURA") {
                            if (!empty($cheque[$i])) {
                            }
                            $datos[$count]['cheque'] =  $cheque[$i];
                            $datos[$count]['banco'] =  $banco[$i];
                        }
                    }
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.listaCartera', ['tipo'=>$request->get('tipoConsulta'),'vencidas'=>$request->get('idVencidas'),'vencer'=>$request->get('idAVencer'),'monto'=>$request->get('idMonto'),'saldo'=>$request->get('idSaldo'),'todo'=>$todo,'datos'=>$datos,'desde'=>$request->get('fecha_desde'),'hasta'=>$request->get('fecha_hasta'),'actual'=>DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'),'empresa'=>$empresa]);
            if($todo == 1){
                $nombreArchivo = 'LISTA DE CARTERA AL '.DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-m-Y');
            }else{
                $nombreArchivo = 'LISTA DE CARTERA DEL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            }
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excel($datos){
        try{   
            return Excel::download(new ViewExcel('admin.formatosExcel.listaCartera',$datos[0]), 'NEOPAGUPA  Sistema Contable.xls');
        }catch(\Exception $ex){
            return redirect('listaCartera')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}


